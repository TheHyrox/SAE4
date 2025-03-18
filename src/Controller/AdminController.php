<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    #[Route('/admin/panel', name: 'admin_panel')]
    public function adminPanel(EntityManagerInterface $entityManager): Response
    {
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.roles LIKE :role AND u.roles NOT LIKE :role2')
            ->setParameter('role', '%ROLE_PRODUCTEUR%')
            ->setParameter('role2', '%ROLE_ADMIN%');

        $producteurs = $queryBuilder->getQuery()->getResult();

        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.roles LIKE :role AND u.roles NOT LIKE :role2 AND u.roles NOT LIKE :role3')
            ->setParameter('role', '%ROLE_USER%')
            ->setParameter('role2', '%ROLE_ADMIN%')
            ->setParameter('role3', '%ROLE_PRODUCTEUR%');

        $users = $queryBuilder->getQuery()->getResult();

        return $this->render('admin/index.html.twig', [
            'producteurs' => $producteurs,
            'users' => $users,
        ]);
    }

    #[Route('/admin/panel/{id}', name: 'delete_account')]
    public function deleteAccount(int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if ($user) {
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('danger', 'Le compte a bien été supprimé.');
        } else {
            $this->addFlash('warning', 'Utilisateur non trouvé.');
        }

        return $this->redirectToRoute('admin_panel');
    }
}
