<?php

// src/Controller/RegistrationController.php

namespace App\Controller;

use App\Entity\Utilisateurs;
use App\Form\RegistrationFormType;
use App\Form\RegistrationFormType2Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController2Controller extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Utilisateurs();
        $form = $this->createForm(RegistrationFormType2Type::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encodage du mot de passe
            $user->setMotDePasse(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            
            // Valeurs par défaut
            $user->setDateInscription(new \DateTime());
            $user->setTypeUtilisateur('client');
            $user->setStatutVerification('en attente');
            $user->setStatus('actif');
            $user->setAvatar('https://example.com/default-avatar.png');

            $entityManager->persist($user);
            $entityManager->flush();

            // Redirection après inscription réussie
            $this->addFlash('success', 'Votre compte a été créé avec succès !');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration_controller2/index.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}