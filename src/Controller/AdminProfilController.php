<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
} 