<?php

namespace App\Controller;

use App\Entity\Evenements;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminEventController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    // Inject EntityManagerInterface via the constructor
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin/event', name: 'app_admin_events')]
    public function index(): Response
    {
        // Custom DQL query to join Evenements, Partenaires, and Utilisateurs
        $query = $this->entityManager->createQuery(
            'SELECT e.idEvenement, e.titre, e.description, e.dateEvenement, e.lieu, 
                    e.status, e.estBloque, e.imagename, 
                    u.nom AS nomPartenaire, u.prenom AS prenomPartenaire
             FROM App\Entity\Evenements e
             LEFT JOIN App\Entity\Partenaires p WITH e.idPartenaire = p.idPartenaire
             LEFT JOIN App\Entity\Utilisateurs u WITH p.idPartenaire = u.idUtilisateur'
        );

        $evenements = $query->getResult();

        return $this->render('admin_event/index.html.twig', [
            'controller_name' => 'AdminEventController',
            'pageTitle' => 'Evénement',
            'evenements' => $evenements, // Pass the events data to the template
        ]);
    }
}