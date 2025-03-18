<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\TypeProduct;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class ProductController extends AbstractController
{
    #[Route('/manage/products/{id}', name: 'app_produits')]
    public function produits(int $id, EntityManagerInterface $entityManager): Response
    {
        $products = $entityManager->createQueryBuilder()
            ->select('p', 'tp')
            ->from('App\Entity\Product', 'p')
            ->leftJoin('p.typeProduct', 'tp')
            ->where('p.user = :userId')
            ->setParameter('userId', $id)
            ->getQuery()
            ->getResult();

        $types = $entityManager->getRepository(TypeProduct::class)->findAll();

        $request = $this->container->get('request_stack')->getCurrentRequest();
        $request->attributes->set('types', $types);

        return $this->render('manage/produits.html.twig', [
            'products' => $products
        ]);
    }

    #[Route('/manage/product/add', name: 'app_product_add', methods: ['POST'])]
    public function addProduct(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        // Check CSRF token
        $submittedToken = $request->request->get('_csrf_token');
        if (!$this->isCsrfTokenValid('add_product', $submittedToken)) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirectToRoute('app_produits', ['id' => $this->getUser()->getId()]);
        }

        // Get current user
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour ajouter un produit');
            return $this->redirectToRoute('app_login');
        }

        // Create new product
        $product = new Product();
        $product->setName($request->request->get('name'));
        $product->setPrice($request->request->get('price'));
        $product->setQuantity($request->request->get('quantity')); // Added quantity field
        $product->setUser($user);

        // Set type product
        $typeProductId = $request->request->get('typeProduct');
        $typeProduct = $entityManager->getRepository(TypeProduct::class)->find($typeProductId);
        if (!$typeProduct) {
            $this->addFlash('error', 'Type de produit invalide');
            return $this->redirectToRoute('app_produits', ['id' => $user->getId()]);
        }
        $product->setTypeProduct($typeProduct);

        // Save product to get ID for image naming
        $entityManager->persist($product);
        $entityManager->flush();

        // Handle file upload
        $photoFile = $request->files->get('photo');
        if ($photoFile) {
            $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $product->getId() . '.jpg';

            try {
                $photoFile->move(
                    $this->getParameter('products_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                $this->addFlash('error', 'Erreur lors de l\'upload de l\'image');
                return $this->redirectToRoute('app_produits', ['id' => $user->getId()]);
            }
        }

        $this->addFlash('success', 'Produit ajouté avec succès');
        return $this->redirectToRoute('app_produits', ['id' => $user->getId()]);
    }

    #[Route('/manage/product/edit/{id}', name: 'app_product_edit', methods: ['POST'])]
    public function editProduct(int $id, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        // Check CSRF token
        $submittedToken = $request->request->get('_csrf_token');
        if (!$this->isCsrfTokenValid('edit_product_' . $id, $submittedToken)) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirectToRoute('app_produits', ['id' => $this->getUser()->getId()]);
        }

        // Get product
        $product = $entityManager->getRepository(Product::class)->find($id);
        if (!$product) {
            $this->addFlash('error', 'Produit non trouvé');
            return $this->redirectToRoute('app_produits', ['id' => $this->getUser()->getId()]);
        }

        // Security check: only owner can edit
        if ($product->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à modifier ce produit');
            return $this->redirectToRoute('app_produits', ['id' => $this->getUser()->getId()]);
        }

        // Update product data
        $product->setName($request->request->get('name'));
        $product->setPrice($request->request->get('price'));
        $product->setQuantity($request->request->get('quantity')); // Added quantity field

        // Update type product
        $typeProductId = $request->request->get('typeProduct');
        $typeProduct = $entityManager->getRepository(TypeProduct::class)->find($typeProductId);
        if ($typeProduct) {
            $product->setTypeProduct($typeProduct);
        }

        // Handle file upload if new photo provided
        $photoFile = $request->files->get('photo');
        if ($photoFile) {
            $newFilename = $product->getId() . '.jpg';

            try {
                $photoFile->move(
                    $this->getParameter('products_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                $this->addFlash('error', 'Erreur lors de l\'upload de l\'image');
            }
        }

        // Save changes
        $entityManager->flush();

        $this->addFlash('success', 'Produit modifié avec succès');
        return $this->redirectToRoute('app_produits', ['id' => $this->getUser()->getId()]);
    }

    #[Route('/manage/product/delete/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function deleteProduct(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Check CSRF token
        $submittedToken = $request->request->get('_csrf_token');
        if (!$this->isCsrfTokenValid('delete_product_' . $id, $submittedToken)) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirectToRoute('app_produits', ['id' => $this->getUser()->getId()]);
        }

        // Get product
        $product = $entityManager->getRepository(Product::class)->find($id);
        if (!$product) {
            $this->addFlash('error', 'Produit non trouvé');
            return $this->redirectToRoute('app_produits', ['id' => $this->getUser()->getId()]);
        }

        // Security check: only owner can delete
        if ($product->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à supprimer ce produit');
            return $this->redirectToRoute('app_produits', ['id' => $this->getUser()->getId()]);
        }

        // Delete product image if exists
        $imagePath = $this->getParameter('products_directory') . '/' . $product->getId() . '.jpg';
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        // Remove product from database
        $entityManager->remove($product);
        $entityManager->flush();

        $this->addFlash('success', 'Produit supprimé avec succès');
        return $this->redirectToRoute('app_produits', ['id' => $this->getUser()->getId()]);
    }
}