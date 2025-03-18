<?php

namespace App\Controller;

use App\Entity\TypeProduct;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}