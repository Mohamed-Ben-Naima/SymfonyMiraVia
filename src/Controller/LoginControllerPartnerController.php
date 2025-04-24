<?php

namespace App\Controller;

use App\Entity\Utilisateurs;
use App\Form\LoginPartnerFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class LoginControllerPartnerController extends AbstractController
{
    #[Route('/login/partner', name: 'app_login_controller_partner')]
    public function login(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        // Créer le formulaire
        $form = $this->createForm(LoginPartnerFormType::class);
        $form->handleRequest($request);

        // Vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $email = $data['email'];
            $password = $data['password'];

            // Rechercher l'utilisateur par email
            $user = $entityManager->getRepository(Utilisateurs::class)->findOneBy(['email' => $email]);

            if (!$user) {
                // Ajouter un message flash pour l'erreur
                $this->addFlash('error', 'Email ou mot de passe incorrect.');
                return $this->redirectToRoute('app_login_controller_partner');
            }

            // Vérifier le mot de passe
            if (!$passwordHasher->isPasswordValid($user, $password)) {
                $this->addFlash('error', 'Email ou mot de passe incorrect.');
                return $this->redirectToRoute('app_login_controller_partner');
            }

            // Vérifier si l'utilisateur est de type partenaire
            if ($user->getTypeUtilisateur() === 'partenaire' && $user->getStatutVerification() === 'actif') {
                // Rediriger vers le tableau de bord partenaire
                return $this->redirectToRoute('app_dashboard_partenaire');
            }

            // Si l'utilisateur n'est pas un partenaire, afficher une erreur
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à accéder à cette section.');
            return $this->redirectToRoute('app_home');
        }

        // Rendre la vue avec le formulaire
        return $this->render('login_controller_partner/index.html.twig', [
            'loginPartnerForm' => $form->createView(),
        ]);
    }
}