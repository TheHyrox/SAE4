<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Entity\Outadated\PRODUCTEUR;
use Entity\Outadated\UTILISATEUR;
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
            ->select('u', 'p')
            ->from(UTILISATEUR::class, 'u')
            ->innerJoin(PRODUCTEUR::class, 'p', 'WITH', 'u.id = p.Id_Uti');

        $results = $queryBuilder->getQuery()->getResult();

        $producteurs = [];
        foreach ($results as $result) {
            if ($result instanceof UTILISATEUR) {
                $producteurs[] = [
                    'utilisateur' => $result,
                    'producteur' => $result->getProducteur(),
                ];
            }
        }

        return $this->render('home/index.html.twig', [
            'producteurs' => $producteurs,
        ]);
    }
}
