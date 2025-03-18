<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    #[Route('/api/users/search', name: 'api_users_search', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function searchUsers(Request $request, UserRepository $userRepository): JsonResponse
    {
        // Get search query
        $query = $request->query->get('q');
        
        // Validate input
        if (!$query || strlen($query) < 2) {
            return $this->json([]);
        }
        
        // Get current user
        $currentUser = $this->getUser();
        
        // Search users excluding current user
        $users = $userRepository->searchUsers($query, $currentUser->getId());
        
        // Format response
        $formattedUsers = [];
        foreach ($users as $user) {
            $formattedUsers[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'firstName' => $user->getFirstName(),
                'profession' => $user->getProfession() ? [
                    'id' => $user->getProfession()->getId(),
                    'name' => $user->getProfession()->getName()
                ] : null,
                // Add any other properties you need
            ];
        }
        
        return $this->json($formattedUsers);
    }
}