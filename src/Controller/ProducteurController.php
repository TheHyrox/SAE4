<?php

namespace App\Controller;

use App\Entity\TypeProduct;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ProducteurController extends AbstractController
{
    #[Route('/producteur/{id}', name: 'app_producteur')]
    public function show(int $id, EntityManagerInterface $entityManager): Response
    {
        $producteur = $entityManager->getRepository(User::class)->find($id);

        if (!$producteur || !in_array('ROLE_PRODUCTEUR', $producteur->getRoles())) {
            throw $this->createNotFoundException('Producteur non trouvé');
        }

        $products = $entityManager->createQueryBuilder()
            ->select('p', 'tp')
            ->from('App\Entity\Product', 'p')
            ->leftJoin('p.typeProduct', 'tp')
            ->where('p.user = :userId')
            ->setParameter('userId', $id)
            ->getQuery()
            ->getResult();

        return $this->render('producteur/producteur.html.twig', [
            'producteur' => $producteur,
            'products' => $products
        ]);
    }
    
    #[Route('/producteur/cart/add', name: 'app_cart_add', methods: ['POST'])]
    public function addToCart(Request $request): Response
    {
        $productId = $request->request->get('product_id');
        $quantity = (int)$request->request->get('quantity');
        $name = $request->request->get('name');
        $price = (float)$request->request->get('price');
        $producerId = $request->request->get('producer_id');
        
        $session = $request->getSession();
        $cart = $session->get('cart', []);
        
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'name' => $name,
                'quantity' => $quantity,
                'price' => $price,
                'total' => $price * $quantity
            ];
        }
        
        // Update total for this item
        $cart[$productId]['total'] = $cart[$productId]['price'] * $cart[$productId]['quantity'];
        
        $session->set('cart', $cart);
        
        return $this->redirectToRoute('app_producteur', ['id' => $producerId]);
    }
    
    #[Route('/producteur/cart/update', name: 'app_cart_update', methods: ['POST'])]
    public function updateCart(Request $request): Response
    {
        $productId = $request->request->get('product_id');
        $action = $request->request->get('action');
        $producerId = $request->request->get('producer_id');
        
        $session = $request->getSession();
        $cart = $session->get('cart', []);
        
        if (isset($cart[$productId])) {
            if ($action === 'increase') {
                $cart[$productId]['quantity']++;
            } elseif ($action === 'decrease') {
                $cart[$productId]['quantity']--;
                if ($cart[$productId]['quantity'] <= 0) {
                    unset($cart[$productId]);
                }
            } elseif ($action === 'remove') {
                unset($cart[$productId]);
            }
            
            if (isset($cart[$productId])) {
                $cart[$productId]['total'] = $cart[$productId]['price'] * $cart[$productId]['quantity'];
            }
        }
        
        $session->set('cart', $cart);
        
        return $this->redirectToRoute('app_producteur', ['id' => $producerId]);
    }
    
    #[Route('/producteur/cart/clear', name: 'app_cart_clear', methods: ['POST'])]
    public function clearCart(Request $request): Response
    {
        $producerId = $request->request->get('producer_id');
        $request->getSession()->remove('cart');
        
        return $this->redirectToRoute('app_producteur', ['id' => $producerId]);
    }
}