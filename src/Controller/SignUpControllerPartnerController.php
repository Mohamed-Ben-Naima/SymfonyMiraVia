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
use Psr\Log\LoggerInterface;

final class SignUpControllerPartnerController extends AbstractController
{
    #[Route('/sign-up/partner', name: 'app_sign_up_controller_partner')]
    public function index(
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher,
        SessionInterface $session,
        LoggerInterface $logger // Inject the logger to capture errors
    ): Response {
        // Create the form
        $form = $this->createForm(SignUpFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                // Log validation errors
                foreach ($form->getErrors(true, false) as $error) {
                    if ($error instanceof \Symfony\Component\Form\FormError) { // Ensure error is an instance of FormError
                        $logger->error('Validation Error: ' . $error->getMessage());
                    }
                }
            
                // Display the errors in the console
                foreach ($form->getErrors(true, false) as $error) {
                    if ($error instanceof \Symfony\Component\Form\FormError) { // Ensure error is an instance of FormError
                        dump($error->getMessage());
                    }
                }
            
                dd('Validation failed'); // Stop execution for debugging
            }

            // Retrieve the form data
            /** @var Utilisateur $formData */
            $formData = $form->getData();

            // Create a new Utilisateur entity
            $utilisateur = new Utilisateur();
            $utilisateur->setNom($formData->getNom());
            $utilisateur->setPrenom($formData->getPrenom());
            $utilisateur->setEmail($formData->getEmail());
            $utilisateur->setTelephone($formData->getTelephone());
            $utilisateur->setAdresseUtilisateur($formData->getAdresseUtilisateur());

            // Set default statuses
            $utilisateur->setStatus('inactif');
            $utilisateur->setStatutVerification('en attente');

            // Set the birthday if provided
            if ($formData->getBirthday() instanceof \DateTimeInterface) {
                $utilisateur->setBirthday($formData->getBirthday());
            } else {
                $utilisateur->setBirthday(null); // Handle invalid dates
            }

            // Hash the password
            $hashedPassword = $passwordHasher->hashPassword($utilisateur, $formData->getMotDePasse());
            $utilisateur->setMotDePasse($hashedPassword);

            try {
                // Save the user to the database
                $entityManager->persist($utilisateur);
                $entityManager->flush();

                // Add a success flash message
                $this->addFlash('success', 'Votre inscription a été réalisée avec succès. Vous pouvez maintenant vous connecter.');

                // Redirect to the login page
                return $this->redirectToRoute('app_login_controller_partner');
            } catch (\Exception $e) {
                // Log database errors
                $logger->error('Error saving the user: ' . $e->getMessage());

                // Display the error in the console
                dd($e->getMessage());
            }
        }

        // Render the form
        return $this->render('sign_up_controller_partner/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}