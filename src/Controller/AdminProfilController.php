<?php

namespace App\Controller;

use App\Entity\Admins;
use App\Entity\Utilisateurs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminProfilController extends AbstractController
{
    #[Route('/admin/profil', name: 'app_admin_profil')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Query to retrieve admin profile data with a join
        $query = $entityManager->createQuery(
            'SELECT u.idUtilisateur, u.nom, u.prenom, u.email, u.motDePasse, u.telephone,
                    u.dateInscription, u.typeUtilisateur, u.avatar, u.statutVerification,
                    u.status, u.birthday, u.adresseUtilisateur
             FROM App\Entity\Admins a
             JOIN App\Entity\Utilisateurs u WITH u.idUtilisateur = a.idAdmin'
        );

        $userData = $query->getOneOrNullResult(); // Fetch a single result

        return $this->render('admin_profil/index.html.twig', [
            'controller_name' => 'AdminProfilController',
            'pageTitle' => 'Profile',
            'userData' => $userData,
        ]);
    }

    #[Route('/admin/profil/update', name: 'app_admin_profil_update', methods: ['POST'])]
    public function update(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Retrieve the admin entity
        $adminId = $request->request->get('idUtilisateur');
        $admin = $entityManager->getRepository(Utilisateurs::class)->find($adminId);

        if (!$admin) {
            throw $this->createNotFoundException('Admin not found.');
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
        $this->addFlash('success', 'Profile updated successfully.');

        return $this->redirectToRoute('app_admin_profil');
    }
}