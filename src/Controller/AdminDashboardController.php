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
            $event['start'] = (new \DateTime($event['start']))->format('Y-m-d\TH:i:s');
            $event['color'] = '#' . substr(md5(rand()), 0, 6);
        }

        // Récupération des données pour le Radar Chart (types d'activité)
        $activityTypes = $connection->fetchAllAssociative('
            SELECT type_activite, COUNT(*) as count
            FROM partenaires
            GROUP BY type_activite
        ');

        $activityLabels = array_column($activityTypes, 'type_activite');
        $activityCounts = array_column($activityTypes, 'count');

        // Récupération des données pour le Pie Chart (pays)
        $countries = $connection->fetchAllAssociative('
            SELECT SUBSTRING_INDEX(adresse, ",", 1) AS country, COUNT(*) as count
            FROM partenaires
            GROUP BY SUBSTRING_INDEX(adresse, ",", 1)
        ');

        $countryLabels = array_column($countries, 'country');
        $countryCounts = array_column($countries, 'count');

        // Récupération des données pour le graphique des dons par mois
        $donationsByMonth = $connection->fetchAllAssociative('
            SELECT 
                MONTHNAME(date_don) AS month,
                SUM(montant) AS total_amount
            FROM dons
            GROUP BY MONTH(date_don)
            ORDER BY MONTH(date_don)
        ');

        // Préparer les données pour le graphique
        $monthLabels = array_column($donationsByMonth, 'month');
        $monthAmounts = array_column($donationsByMonth, 'total_amount');

        // Envoi des données au template
        return $this->render('admin_dashboard/index.html.twig', [
            'totalClients' => $totalClients,
            'totalEvenements' => $totalEvenements,
            'totalPartenaires' => $totalPartenaires,
            'totalDons' => $totalDons,
            'totalDemandesAide' => $totalDemandesAide,
            'events' => json_encode($events),
            'activityLabels' => json_encode($activityLabels),
            'activityCounts' => json_encode($activityCounts),
            'countryLabels' => json_encode($countryLabels),
            'countryCounts' => json_encode($countryCounts),
            'monthLabels' => json_encode($monthLabels),
            'monthAmounts' => json_encode($monthAmounts),
            'pageTitle' => 'Tableau de bord',
        ]);
    }
}