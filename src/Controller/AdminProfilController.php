<?php

namespace App\Controller;

namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Admins;
use App\Entity\Utilisateurs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack; // Add this import
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminProfilController extends AbstractController
{
  #[Route('/admin/profil', name: 'app_admin_profil')]
  public function index(RequestStack $requestStack, EntityManagerInterface $entityManager): Response
  {
      $session = $requestStack->getSession();

      // Get the admin ID from the session
      $adminId = $session->get('admin')['id'] ?? null;

      if (!$adminId) {
          // If no admin ID is found in the session, redirect to login
          $this->addFlash('error', 'Vous devez être connecté.');
          return $this->redirectToRoute('app_admin_login');
      }

      // Query to retrieve admin profile data
      $query = $entityManager->createQuery(
          'SELECT u.idUtilisateur, u.nom, u.prenom, u.email, u.motDePasse, u.telephone,
                  u.dateInscription, u.typeUtilisateur, u.avatar, u.statutVerification,
                  u.status, u.birthday, u.adresseUtilisateur
           FROM App\Entity\Admins a
           JOIN App\Entity\Utilisateurs u WITH u.idUtilisateur = a.idAdmin
           WHERE a.idAdmin = :adminId'
      )->setParameter('adminId', $adminId);

      $userData = $query->getOneOrNullResult(); // Fetch a single result

      if (!$userData) {
          throw $this->createNotFoundException('Profil administrateur introuvable.');
      }

      return $this->render('admin_profil/index.html.twig', [
          'controller_name' => 'AdminProfilController',
          'pageTitle' => 'Profil',
          'userData' => $userData,
      ]);
  }
  #[Route('/admin/profil/update', name: 'app_admin_profil_update', methods: ['POST'])]
  public function update(Request $request, RequestStack $requestStack, EntityManagerInterface $entityManager): Response
  {
      // Retrieve the admin ID from the session
      $session = $requestStack->getSession();
      $adminId = $session->get('admin')['id'] ?? null;
  
      if (!$adminId) {
          // If no admin ID is found in the session, redirect to login
          $this->addFlash('error', 'Vous devez être connecté.');
          return $this->redirectToRoute('app_admin_login');
      }
  
      // Retrieve the admin entity using the session ID
      $admin = $entityManager->getRepository(Utilisateurs::class)->find($adminId);
  
      if (!$admin) {
          throw $this->createNotFoundException('Admin introuvable.');
      }
  
      // Update admin profile data from the request
      $admin->setNom($request->request->get('nom'));
      $admin->setPrenom($request->request->get('prenom'));
      $admin->setEmail($request->request->get('email'));
      $admin->setTelephone($request->request->get('telephone'));
      $admin->setAvatar($request->request->get('avatar'));
      $admin->setBirthday(new \DateTime($request->request->get('birthday')));
      $admin->setAdresseUtilisateur($request->request->get('adresseUtilisateur'));
  
      // Save the changes
      $entityManager->persist($admin);
      $entityManager->flush();
  
      // Redirect back to the profile page with a success message
      $this->addFlash('success', 'Profil mis à jour avec succès.');
  
      return $this->redirectToRoute('app_admin_profil');
  }
}