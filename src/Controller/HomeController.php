<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\TypeProfession;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Get filter parameters from request
        $categorieId = $request->query->get('categorie', 'Tout');
        $ville = $request->query->get('rechercheVille');
        $rayon = $request->query->get('rayon', 100);
        $tri = $request->query->get('tri', 'nombreDeProduits');
        $latitude = $request->query->get('latitude');
        $longitude = $request->query->get('longitude');

        // Build query for producteurs
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.roles LIKE :role AND u.roles NOT LIKE :role2')
            ->setParameter('role', '%ROLE_PRODUCTEUR%')
            ->setParameter('role2', '%ROLE_ADMIN%');

        // Add city filter if provided
        if ($ville) {
            $queryBuilder->andWhere('u.address LIKE :ville')
                ->setParameter('ville', '%' . $ville . '%');
        }

        // Add profession filter if not "Tout"
        if ($categorieId && $categorieId !== 'Tout') {
            // If it's a number, use it as ID, otherwise look up by name
            if (is_numeric($categorieId)) {
                $queryBuilder->leftJoin('u.profession', 'tp')
                    ->andWhere('tp.id = :categorieId')
                    ->setParameter('categorieId', $categorieId);
            } else {
                $queryBuilder->leftJoin('u.profession', 'tp')
                    ->andWhere('tp.name = :categorieName')
                    ->setParameter('categorieName', $categorieId);
            }
        }

        // Add sorting based on selected option
        switch ($tri) {
            case 'nombreDeProduits':
                $queryBuilder->addSelect('COUNT(p.id) as HIDDEN productCount')
                    ->leftJoin('u.products', 'p')
                    ->groupBy('u.id')
                    ->orderBy('productCount', 'DESC');
                break;
            case 'ordreNomAlphabétique':
                $queryBuilder->orderBy('u.name', 'ASC');
                break;
            case 'ordreNomAntiAlphabétique':
                $queryBuilder->orderBy('u.name', 'DESC');
                break;
            case 'ordrePrenomAlphabétique':
                $queryBuilder->orderBy('u.firstName', 'ASC');
                break;
            case 'ordrePrenomAntiAlphabétique':
                $queryBuilder->orderBy('u.firstName', 'DESC');
                break;
            default:
                $queryBuilder->addSelect('COUNT(p.id) as HIDDEN productCount')
                    ->leftJoin('u.products', 'p')
                    ->groupBy('u.id')
                    ->orderBy('productCount', 'DESC');
        }

        $producteurs = $queryBuilder->getQuery()->getResult();

        // Get all professions for the dropdown
        $professions = $entityManager->getRepository(TypeProfession::class)->findAll();

        return $this->render('home/index.html.twig', [
            'producteurs' => $producteurs,
            'professions' => $professions,
            'filters' => [
                'categorie' => $categorieId,
                'ville' => $ville,
                'rayon' => $rayon,
                'tri' => $tri,
                'latitude' => $latitude,
                'longitude' => $longitude
            ]
        ]);
    }
}