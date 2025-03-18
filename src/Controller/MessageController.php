<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class MessageController extends AbstractController
{
    #[Route('/messagerie', name: 'app_messagerie')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        
        // Get received messages
        $receivedMessages = $entityManager->getRepository(Message::class)->findBy([
            'recipient' => $currentUser
        ], ['sendDate' => 'DESC']);
        
        // Get sent messages
        $sentMessages = $entityManager->getRepository(Message::class)->findBy([
            'sender' => $currentUser
        ], ['sendDate' => 'DESC']);
        
        // Combine all messages
        $allMessages = array_merge($receivedMessages, $sentMessages);
        
        // Sort by date (most recent first)
        usort($allMessages, function($a, $b) {
            return $b->getSendDate() <=> $a->getSendDate();
        });

        return $this->render('message/index.html.twig', [
            'messages' => $allMessages,
            'currentUser' => $currentUser
        ]);
    }

    #[Route('/messagerie/envoi', name: 'app_messagerie_send', methods: ['POST'])]
    public function send(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('send-message', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }

        $recipientId = $request->request->get('destinataire');
        $content = $request->request->get('content');
        
        if (empty($recipientId) || empty($content)) {
            $this->addFlash('error', 'Le destinataire et le contenu sont obligatoires.');
            return $this->redirectToRoute('app_messagerie');
        }
        
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $recipient = $entityManager->getRepository(User::class)->find($recipientId);
        
        if (!$recipient) {
            throw $this->createNotFoundException('Destinataire non trouvé');
        }
        
        $message = new Message();
        $message->setContent(htmlspecialchars($content));
        $message->setSendDate(new \DateTime());
        $message->setSender($currentUser);
        $message->setRecipient($recipient);

        $entityManager->persist($message);
        $entityManager->flush();

        $this->addFlash('success', 'Message envoyé avec succès.');
        
        // Redirect to the conversation with the recipient
        return $this->redirectToRoute('app_messagerie_conversation', [
            'userId' => $recipient->getId()
        ]);
    }

    #[Route('/api/users/search', name: 'api_users_search')]
    public function searchUsers(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $query = $request->query->get('q');
        
        if (empty($query) || strlen($query) < 2) {
            return $this->json([]);
        }
        
        $users = $entityManager->getRepository(User::class)
            ->createQueryBuilder('u')
            ->where('u.email LIKE :query')
            ->orWhere('u.firstName LIKE :query')
            ->orWhere('u.name LIKE :query')
            ->andWhere('u.id != :currentUserId')
            ->setParameter('query', '%' . $query . '%')
            ->setParameter('currentUserId', $this->getUser()->getId())
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
        
        $formattedUsers = array_map(function(User $user) {
            return [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getFirstName() . ' ' . $user->getName(),
                'isProducer' => $user->getProfession() !== null
            ];
        }, $users);
        
        return $this->json($formattedUsers);
    }

    #[Route('/messagerie/{userId}', name: 'app_messagerie_conversation')]
    public function conversation(
        int $userId, 
        EntityManagerInterface $entityManager
    ): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $otherUser = $entityManager->getRepository(User::class)->find($userId);
        
        if (!$otherUser) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }
        
        // Get conversation between current user and the other user
        $sentMessages = $entityManager->getRepository(Message::class)->findBy([
            'sender' => $currentUser,
            'recipient' => $otherUser
        ]);
        
        $receivedMessages = $entityManager->getRepository(Message::class)->findBy([
            'sender' => $otherUser,
            'recipient' => $currentUser
        ]);
        
        // Combine and sort by date
        $conversation = array_merge($sentMessages, $receivedMessages);
        usort($conversation, function($a, $b) {
            return $a->getSendDate() <=> $b->getSendDate();
        });
        
        $entityManager->flush();
        
        // Get all received messages for sidebar
        $allReceivedMessages = $entityManager->getRepository(Message::class)->findBy([
            'recipient' => $currentUser
        ], ['sendDate' => 'DESC']);
        
        // Get all sent messages for sidebar
        $allSentMessages = $entityManager->getRepository(Message::class)->findBy([
            'sender' => $currentUser
        ], ['sendDate' => 'DESC']);
        
        // Combine and sort by date
        $allMessages = array_merge($allReceivedMessages, $allSentMessages);
        usort($allMessages, function($a, $b) {
            return $b->getSendDate() <=> $a->getSendDate();
        });
        
        return $this->render('message/index.html.twig', [
            'messages' => $allMessages,
            'conversation' => $conversation,
            'selectedUser' => $otherUser,
            'currentUser' => $currentUser
        ]);
    }
}