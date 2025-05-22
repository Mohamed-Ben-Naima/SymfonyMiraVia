<?php

namespace App\Controller;

use App\Entity\Utilisateurs; // Adjusted to use Utilisateurs instead of User
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Psr\Log\LoggerInterface;

final class ProfileController extends AbstractController
{
    #[Route('/client/profile', name: 'app_profile', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        SluggerInterface $slugger,
        LoggerInterface $logger
    ): Response {
        /** @var Utilisateurs $user */
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Utilisateur non connecté.');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle password update
            if ($form->get('plainPassword')->getData()) {
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            }

            // Handle avatar upload
            $avatarFile = $form->get('avatar')->getData();
            if ($avatarFile) {
                $avatarsDirectory = $this->getParameter('avatars_directory');

                // Create directory if it doesn't exist
                if (!file_exists($avatarsDirectory)) {
                    mkdir($avatarsDirectory, 0755, true);
                }

                $originalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $avatarFile->guessExtension();

                try {
                    $avatarFile->move($avatarsDirectory, $newFilename);
                    // Store the relative path to the avatar
                    $user->setAvatar('avatars/' . $newFilename);
                    $logger->info('Avatar uploaded for user {user}: {filename}', [
                        'user' => $user->getIdUtilisateur(),
                        'filename' => 'avatars/' . $newFilename,
                    ]);
                } catch (FileException $e) {
                    $logger->error('Avatar upload failed: {error}', ['error' => $e->getMessage()]);
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'avatar : ' . $e->getMessage());
                }
            }

            try {
                $em->persist($user); // Explicitly persist the user entity
                $em->flush();
                $logger->info('User profile updated successfully for user {user}', [
                    'user' => $user->getIdUtilisateur(),
                ]);
                $this->addFlash('success', 'Profil mis à jour avec succès');
            } catch (\Exception $e) {
                $logger->error('Database error during profile update: {error}', ['error' => $e->getMessage()]);
                $this->addFlash('error', 'Erreur lors de la mise à jour du profil : ' . $e->getMessage());
            }

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/index.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}