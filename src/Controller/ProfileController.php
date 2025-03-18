<?php

namespace App\Controller;

use App\Entity\TypeProfession;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function edit(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        SluggerInterface $slugger
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour modifier votre profil.');
            return $this->redirectToRoute('app_login');
        }

        if (!$this->isCsrfTokenValid('profile_edit', $request->request->get('_csrf_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_home');
        }

        $currentPassword = $request->request->get('currentPassword');
        if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
            $this->addFlash('error', 'Le mot de passe actuel est incorrect.');
            return $this->redirectToRoute('app_home');
        }

        $user->setFirstName($request->request->get('firstName'));
        $user->setName($request->request->get('name'));
        $user->setEmail($request->request->get('email'));
        $user->setAddress($request->request->get('address'));

        if (in_array('ROLE_PRODUCTEUR', $user->getRoles()) && $request->request->get('profession')) {
            $profession = $entityManager->getRepository(TypeProfession::class)->find($request->request->get('profession'));
            if ($profession && method_exists($user, 'setTypeProfession')) {
                $user->setTypeProfession($profession);
            }
        }

        $newPassword = $request->request->get('newPassword');
        $confirmPassword = $request->request->get('confirmPassword');
        if (!empty($newPassword)) {
            if ($newPassword === $confirmPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            } else {
                $this->addFlash('error', 'Les nouveaux mots de passe ne correspondent pas.');
                return $this->redirectToRoute('app_home');
            }
        }

        $profilePhotoFile = $request->files->get('profilePhoto');
        if ($profilePhotoFile) {
            $originalFilename = pathinfo($profilePhotoFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = 'producteur_' . $user->getId() . '.jpg';

            try {
                $uploadsDirectory = $this->getParameter('kernel.project_dir') . '/public/images';
                $profilePhotoFile->move(
                    $uploadsDirectory,
                    $newFilename
                );
            } catch (FileException $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de l\'upload de votre photo de profil.');
                return $this->redirectToRoute('app_home');
            }
        }

        $entityManager->flush();

        $this->addFlash('success', 'Votre profil a été mis à jour avec succès.');
        return $this->redirectToRoute('app_home');
    }
}