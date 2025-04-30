<?php

namespace App\Controller\Partenaire;
use App\Entity\Partenaires;
use App\Form\PartenaireFormType;
use App\Form\SignUpFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Psr\Log\LoggerInterface;
use App\Entity\Utilisateurs;
use Gregwar\CaptchaBundle\Validator\Constraints;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Gregwar\CaptchaBundle\Validator\Constraints\Captcha as CaptchaConstraint;
final class SignUpControllerPartnerController extends AbstractController
{
    #[Route('/sign-up/partner', name: 'app_sign_up_controller_partner')]
    public function signUp(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = new Utilisateurs();
        $form = $this->createForm(SignUpFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier le CAPTCHA auto
       
            // Encoder le mot de passe
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getMotDePasse()
            );
            $user->setMotDePasse($hashedPassword);
      
            // Définir des valeurs par défaut
            $user->setDateInscription(new \DateTime());
            $user->setTypeUtilisateur('partenaire');
            $user->setStatutVerification('en attente');
            $user->setStatus('actif');

            // Sauvegarder dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Rediriger ou afficher un message de succès
            $this->addFlash('success', 'Inscription réussie !');
            return $this->redirectToRoute('app_login_controller_partner');
        }

        return $this->render('Partenaire/sign_up_controller_partner/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/partenaire/ajouter', name: 'partenaire_ajouter')]
    public function ajouter(Request $request): Response
    {
        $partenaire = new Partenaires();

        // Définir les valeurs par défaut pour 'etat' et 'dateExpiration'
        $partenaire->setEtat(false); // ou 0
        $partenaire->setDateExpiration(new \DateTime()); // Date actuelle, ou une autre valeur si tu préfères

        $form = $this->createForm(PartenairesType::class, $partenaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder l'entité partenaire en base de données
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($partenaire);
            $entityManager->flush();

            return $this->redirectToRoute('partenaire_liste');
        }

        return $this->render('partenaire/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    }