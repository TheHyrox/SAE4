<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_PRODUCTEUR%');

        $producteurs = $queryBuilder->getQuery()->getResult();

        return $this->render('home/index.html.twig', [
            'producteurs' => $producteurs,
        ]);
    }

    #[Route('/', name: 'app_achats')]
    public function achats(EntityManagerInterface $entityManager): Response
    {
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_PRODUCTEUR%');

        $producteurs = $queryBuilder->getQuery()->getResult();

        return $this->render('home/index.html.twig', [
            'producteurs' => $producteurs,
        ]);
    }

    #[Route('/', name: 'app_produits')]
    public function produits(EntityManagerInterface $entityManager): Response
    {
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.roles LIKE :role AND u.roles NOT LIKE :role2')
            ->setParameter('role', '%ROLE_PRODUCTEUR%')
            ->setParameter('role2', '%ROLE_ADMIN%');

        $producteurs = $queryBuilder->getQuery()->getResult();

        return $this->render('home/index.html.twig', [
            'producteurs' => $producteurs,
        ]);
    }

    #[Route('/', name: 'app_commandes')]
    public function commands(EntityManagerInterface $entityManager): Response
    {
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_PRODUCTEUR%');

        $producteurs = $queryBuilder->getQuery()->getResult();

        return $this->render('home/index.html.twig', [
            'producteurs' => $producteurs,
        ]);
    }
}