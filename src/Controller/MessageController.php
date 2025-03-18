<?php

namespace App\Controller;

use App\Entity\MESSAGE;
use App\Entity\UTILISATEUR;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

//#[IsGranted('ROLE_USER')]
class MessageController extends AbstractController
{
    #[Route('/messagerie', name: 'app_messagerie')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        /** @var UTILISATEUR $user */
        $user = $this->getUser();
        
        $messages = $entityManager->getRepository(MESSAGE::class)->findBy([
            'Destinataire' => $user
        ], ['Date_Msg' => 'DESC']);

        // Get all users except current user
        $users = $entityManager->getRepository(UTILISATEUR::class)
            ->createQueryBuilder('u')
            //->where('u.id != :currentUserId')
            //->setParameter('currentUserId', $user->getId())
            ->getQuery();
            //->getResult();

        return $this->render('message/index.html.twig', [
            'messages' => $messages,
            'users' => $users
        ]);
    }

    #[Route('/messagerie/envoi', name: 'app_messagerie_send', methods: ['POST'])]
    public function send(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('send-message', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }

        $message = new MESSAGE();
        $message->setContenuMsg(htmlspecialchars($request->request->get('content')));
        $message->setDateMsg(new \DateTime());
        $message->setDateExpiMsg(new \DateTime('+30 days'));
        $message->setEmetteur($this->getUser());
        
        $destinataire = $entityManager->getRepository(UTILISATEUR::class)->find($request->request->get('destinataire'));
        if (!$destinataire) {
            throw $this->createNotFoundException('Destinataire non trouvé');
        }
        
        $message->setDestinataire($destinataire);

        $entityManager->persist($message);
        $entityManager->flush();

        $this->addFlash('success', 'Message envoyé');
        return $this->redirectToRoute('app_messagerie');
    }

    #[Route('/api/users/search', name: 'api_users_search')]
    public function searchUsers(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $query = $request->query->get('q');
        
        $users = $entityManager->getRepository(UTILISATEUR::class)
            ->createQueryBuilder('u')
            ->where('u.email LIKE :query')
            ->andWhere('u.id != :currentUserId')
            ->setParameter('query', '%' . $query . '%')
            ->setParameter('currentUserId', $this->getUser()->getId())
            ->getQuery()
            ->getResult();
        
        return $this->json($users, 200, [], ['groups' => ['search']]);
    }

    #[Route('/messagerie/{userId}', name: 'app_messagerie_conversation')]
    public function conversation(
        int $userId, 
        EntityManagerInterface $entityManager
    ): Response
    {
        $conversation = $entityManager->getRepository(MESSAGE::class)
            ->createQueryBuilder('m')
            ->where('m.Emetteur = :user1 AND m.Destinataire = :user2')
            ->orWhere('m.Emetteur = :user2 AND m.Destinataire = :user1')
            ->setParameter('user1', $this->getUser())
            ->setParameter('user2', $userId)
            ->orderBy('m.Date_Msg', 'ASC')
            ->getQuery()
            ->getResult();
        
        return $this->render('message/index.html.twig', [
            'conversation' => $conversation,
            'selectedUser' => $entityManager->getRepository(UTILISATEUR::class)->find($userId)
        ]);
    }
}