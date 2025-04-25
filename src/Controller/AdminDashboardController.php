<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminDashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    public function index(Connection $connection): Response
    {
        // Récupération des statistiques
        $totalClients = $connection->fetchOne('SELECT COUNT(*) FROM clients');
        $totalEvenements = $connection->fetchOne('SELECT COUNT(*) FROM evenements');
        $totalPartenaires = $connection->fetchOne('SELECT COUNT(*) FROM partenaires');
        $totalDons = $connection->fetchOne('SELECT COUNT(*) FROM dons');
        $totalDemandesAide = $connection->fetchOne('SELECT COUNT(*) FROM demandes_aide');

        // Récupération des événements pour le calendrier
        $events = $connection->fetchAllAssociative('
            SELECT id_evenement AS id, titre AS title, date_evenement AS start, description, lieu
            FROM evenements
        ');

        foreach ($events as &$event) {
            $event['start'] = (new \DateTime($event['start']))->format('Y-m-d\TH:i:s'); // Format ISO 8601
            $event['color'] = '#' . substr(md5(rand()), 0, 6); // Couleur aléatoire
        }

        // Envoi des données au template
        return $this->render('admin_dashboard/index.html.twig', [
            'totalClients' => $totalClients,
            'totalEvenements' => $totalEvenements,
            'totalPartenaires' => $totalPartenaires,
            'totalDons' => $totalDons,
            'totalDemandesAide' => $totalDemandesAide,
           'events' => json_encode($events),            
           'pageTitle' => 'Tableau de bord', // Le titre dynamique
        ]);
    }
}