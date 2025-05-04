<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; // Utiliser l'interface correcte
use Doctrine\ORM\EntityManagerInterface;
use App\Form\AdminLoginFormType;
use App\Entity\Utilisateurs;
use App\Entity\Admins;

final class AdminLoginController extends AbstractController
{
    private $entityManager;
    private $passwordHasher; // Remplacement de la propriété

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher // Remplacement du type
    ) {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher; // Remplacement de l'argument
    }

    #[Route('/admin/login', name: 'app_admin_login')]
    public function index(Request $request): Response
    {
        // Créez le formulaire
        $form = $this->createForm(AdminLoginFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $email = $data['email'];
            $password = $data['password'];

            // Vérifier si un utilisateur existe avec cet email
            $user = $this->entityManager->getRepository(Utilisateurs::class)->findOneBy(['email' => $email]);

            if (!$user) {
              $this->addFlash('error', 'Utilisateur non trouvé avec cet email.');
              return $this->redirectToRoute('app_admin_login');
          }
          
          if ($user->getMotDePasse() !== $password) {
            $this->addFlash('error', 'Mot de passe incorrect.');
            return $this->redirectToRoute('app_admin_login');
        }
          
          $this->addFlash('success', 'Connexion réussie.');

            // Vérifier si cet utilisateur est un admin
            $admin = $this->entityManager->getRepository(Admins::class)->findOneBy(['idAdmin' => $user->getIdUtilisateur()]);

            if (!$admin) {
                $this->addFlash('error', 'Accès réservé aux administrateurs.');
                return $this->redirectToRoute('app_admin_login');
            }

            // Créer une session pour l'admin
            $session = $request->getSession();
            $sessionData = [
              'id' => $admin->getIdAdmin(),
              'email' => $user->getEmail(),
              'role' => $admin->getRole(),
              'permissions' => $admin->getPermissions(),
          ];
          
          $session->set('admin', $sessionData);
          
          // Debug dans la console ou le navigateur
          dump($sessionData); // Affiche les données dans la barre de débogage Symfony

            // Redirection vers le tableau de bord
            return $this->redirectToRoute('app_admin_dashboard');
        }

        // Affichez le formulaire dans la vue
        return $this->render('admin_login/index.html.twig', [
            'loginAdminForm' => $form->createView(),
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(RequestStack $requestStack): void
    {
        $session = $requestStack->getSession();
        $session->invalidate(); // Invalide la session en cours
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}