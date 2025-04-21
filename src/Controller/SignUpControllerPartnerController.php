<?php

namespace App\Controller;

use App\Form\SignUpFormType;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class SignUpControllerPartnerController extends AbstractController
{
    #[Route('/sign-up/partner', name: 'app_sign_up_controller_partner')]
    public function index(
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher,
        SessionInterface $session
    ): Response {
        // Créer le formulaire
        $form = $this->createForm(SignUpFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            /** @var Utilisateur $formData */
            $formData = $form->getData();

            // Créer une nouvelle entité Utilisateur
            $utilisateur = new Utilisateur();
            $utilisateur->setNom($formData->getNom());
            $utilisateur->setPrenom($formData->getPrenom());
            $utilisateur->setEmail($formData->getEmail());
            $utilisateur->setTelephone($formData->getTelephone());
            $utilisateur->setAdresseUtilisateur($formData->getAdresseUtilisateur());

            // Définir le statut par défaut comme "inactif"
            $utilisateur->setStatus('inactif');

            // Définir le statut de vérification par défaut comme "en attente"
            $utilisateur->setStatutVerification('en attente');

            // Définir la date de naissance si elle est fournie
           if ($formData->getBirthday() instanceof \DateTimeInterface) {
            $utilisateur->setBirthday($formData->getBirthday());
        } else {
            $utilisateur->setBirthday(null); // Handle invalid dates
        }
            // Hacher le mot de passe
            $hashedPassword = $passwordHasher->hashPassword($utilisateur, $formData->getMotDePasse());
            $utilisateur->setMotDePasse($hashedPassword);

            // Sauvegarder l'utilisateur dans la base de données
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            // Ajouter un message flash de succès
            $this->addFlash('success', 'Votre inscription a été réalisée avec succès. Vous pouvez maintenant vous connecter.');

            // Rediriger vers la page de connexion
            return $this->redirectToRoute('app_login_controller_partner');
        }

        // Afficher le formulaire
        return $this->render('sign_up_controller_partner/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}