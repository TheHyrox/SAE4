<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Entity\Outadated\PRODUCTEUR;
use Entity\Outadated\UTILISATEUR;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ProducteurController extends AbstractController
{
    #[Route('/producteur/{id}', name: 'app_producteur')]
    public function show(int $id, EntityManagerInterface $entityManager): Response
    {
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder
            ->select('u', 'p')
            ->from(UTILISATEUR::class, 'u')
            ->innerJoin(PRODUCTEUR::class, 'p', 'WITH', 'u.id = p.Id_Uti')
            ->where('p.id = :id')
            ->setParameter('id', $id);

        $producteur = $queryBuilder->getQuery()->getResult();

        return $this->render('producteur/show.html.twig', [
            dd($producteur),
            'producteur' => $producteur,
        ]);
    }
}
