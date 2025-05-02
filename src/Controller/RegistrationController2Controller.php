<?php

// src/Controller/RegistrationController.php

namespace App\Controller;

use App\Entity\Utilisateurs;
use App\Form\RegistrationFormType;
use App\Form\RegistrationFormType2Type;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
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
            try {
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
                $user->setAvatar('https://static.vecteezy.com/system/resources/previews/009/734/564/original/default-avatar-profile-icon-of-social-media-user-vector.jpg');

                $entityManager->persist($user);
                $entityManager->flush();

                // Message flash personnalisé
                $this->addFlash('success', sprintf('Félicitations %s ! Votre compte a été créé avec succès. Veuillez vous connecter.', $user->getPrenom()));
                return $this->redirectToRoute('app_login_controller2');
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('error', 'Cet email est déjà utilisé. Veuillez en choisir un autre.');
            }
        } elseif ($form->isSubmitted()) {
            $this->addFlash('error', 'Veuillez corriger les erreurs dans le formulaire.');
        }

        return $this->render('registration_controller2/index.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
