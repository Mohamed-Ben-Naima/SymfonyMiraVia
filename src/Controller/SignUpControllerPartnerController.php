<?php

namespace App\Controller;

use App\Form\SigUpPartnerFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

final class SignUpControllerPartnerController extends AbstractController
{
    #[Route('/sign-up/partner', name: 'app_sign_up_controller_partner')]
    public function index(
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        // Create the form
        $form = $this->createForm(SigUpPartnerFormType::class);

        // Handle the form submission
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get form data
            $formData = $form->getData();

            // Create a new User entity
            $user = new User();
            $user->setFirstName($formData['first_name']);
            $user->setLastName($formData['last_name']);
            $user->setEmail($formData['email']);

            // Hash the password
            $hashedPassword = $passwordHasher->hashPassword($user, $formData['password']);
            $user->setPassword($hashedPassword);

            // Persist the user entity
            $entityManager->persist($user);
            $entityManager->flush();

            // Redirect to a success or login page
            return $this->redirectToRoute('app_login');
        }

        // Render the form in the template
        return $this->render('sign_up_controller_partner/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}