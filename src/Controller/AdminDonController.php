<?php

namespace App\Controller;

use App\Entity\Dons;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminDonController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    // Injecter l'EntityManager via le constructeur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin/don', name: 'app_admin_donations')]
    public function index(): Response
    {
        // Requête SQL avec jointures pour récupérer les noms des clients et des partenaires
        $query = $this->entityManager->createQuery(
          'SELECT d.idDon AS idDon, d.montant, d.dateDon, d.status, 
                  clientUser.nom AS nomClient, clientUser.prenom AS prenomClient, 
                  partnerUser.nom AS nomPartenaire, partnerUser.prenom AS prenomPartenaire
           FROM App\Entity\Dons d
           LEFT JOIN App\Entity\Clients c WITH d.idClient = c.idClient
           LEFT JOIN App\Entity\Partenaires p WITH d.idPartenaire = p.utilisateur
           LEFT JOIN App\Entity\Utilisateurs clientUser WITH clientUser.idUtilisateur = c.idClient
           LEFT JOIN App\Entity\Utilisateurs partnerUser WITH partnerUser.idUtilisateur = p.utilisateur'
      );

        $dons = $query->getResult();

        return $this->render('admin_don/index.html.twig', [
            'controller_name' => 'AdminDonController',
            'pageTitle' => 'Don',
            'dons' => $dons, // Passer les dons à la vue
        ]);
    }
}