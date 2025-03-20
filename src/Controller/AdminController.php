<?php

namespace App\Controller;

use App\Entity\Command;
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
            try {
                $entityManager->beginTransaction();

                // Check for pending commands
                $queryBuilder = $entityManager->createQueryBuilder();
                $pendingCommands = $queryBuilder
                    ->select('c')
                    ->from(Command::class, 'c')
                    ->join('c.products', 'p')
                    ->join('c.status', 's')
                    ->where('p.user = :producteur')
                    ->andWhere('s.name IN (:statusList)')
                    ->setParameter('producteur', $user)
                    ->setParameter('statusList', ['En traitement', 'Prête'])
                    ->groupBy('c.id')
                    ->getQuery()
                    ->getResult();

                if (!empty($pendingCommands)) {
                    $this->addFlash('warning', 'Impossible de supprimer le compte. Il y a des commandes en cours.');
                    return $this->redirectToRoute('admin_panel');
                }

                // 1. Delete association records directly from the join table using native SQL
                $connection = $entityManager->getConnection();
                $connection->executeStatement(
                    'DELETE FROM command_product WHERE command_id IN (
                    SELECT id FROM command WHERE customer_id = :user_id
                ) OR product_id IN (
                    SELECT id FROM product WHERE user_id = :user_id
                )',
                    ['user_id' => $user->getId()]
                );

                // 2. Delete messages
                $queryBuilder = $entityManager->createQueryBuilder();
                $queryBuilder
                    ->delete('App\Entity\Message', 'm')
                    ->where('m.sender = :user')
                    ->orWhere('m.recipient = :user')
                    ->setParameter('user', $user);
                $queryBuilder->getQuery()->execute();

                // 3. Delete user's products
                $queryBuilder = $entityManager->createQueryBuilder();
                $queryBuilder
                    ->delete('App\Entity\Product', 'p')
                    ->where('p.user = :user')
                    ->setParameter('user', $user);
                $queryBuilder->getQuery()->execute();

                // 4. Delete commands where user is customer
                $queryBuilder = $entityManager->createQueryBuilder();
                $queryBuilder
                    ->delete(Command::class, 'c')
                    ->where('c.customer = :user')
                    ->setParameter('user', $user);
                $queryBuilder->getQuery()->execute();

                // 5. Finally delete the user
                $entityManager->remove($user);
                $entityManager->flush();
                $entityManager->commit();

                $this->addFlash('danger', 'Le compte a bien été supprimé.');
            } catch (\Exception $e) {
                $entityManager->rollback();
                $this->addFlash('error', 'Erreur lors de la suppression: ' . $e->getMessage());
            }
        } else {
            $this->addFlash('warning', 'Utilisateur non trouvé.');
        }

        return $this->redirectToRoute('admin_panel');
    }
}
