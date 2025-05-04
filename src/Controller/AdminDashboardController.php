<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminDashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    public function index(Request $request, Connection $connection): Response
    {
        // Récupérer les données de l'utilisateur à partir de la session
        $session = $request->getSession();
        $userData = $session->get('admin');

        if (!$userData) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder au tableau de bord.');
            return $this->redirectToRoute('app_admin_login');
        }

        // Récupération des statistiques
        $totalClients = $connection->fetchOne('SELECT COUNT(*) FROM clients');
        $totalEvenements = $connection->fetchOne('SELECT COUNT(*) FROM evenements');
        $totalPartenaires = $connection->fetchOne('SELECT COUNT(*) FROM partenaires');
        $totalDons = $connection->fetchOne('SELECT COUNT(*) FROM dons');
        $totalDemandesAide = $connection->fetchOne('SELECT COUNT(*) FROM demandes_aide');

        // Récupération des événements pour le calendrier (table evenements) uniquement avec status "En cours"
        $events = $connection->fetchAllAssociative('
            SELECT id_evenement AS id, titre AS title, date_evenement AS start, description, lieu
            FROM evenements
            WHERE status = "En cours"
        ');

        // Récupération des événements de bénévolat pour le calendrier (table benevolat) uniquement avec status "Ouvert"
        $volunteerEvents = $connection->fetchAllAssociative('
            SELECT id_benevolat AS id, titre AS title, date_benevolat AS start, description, status, imageName
            FROM benevolat
            WHERE status = "Ouvert"
        ');

        // Formatage des événements (evenements)
        foreach ($events as &$event) {
            $event['start'] = (new \DateTime($event['start']))->format('Y-m-d\TH:i:s');
            $event['color'] = '#FFCDD2'; 
            $event['className'] = 'event-classique'; // Classe CSS pour les événements classiques
            $event['type'] = 'evenement';
            $event['icon'] = 'fas fa-calendar-alt'; // Icône pour les événements classiques
        }

        // Formatage des événements de bénévolat
        foreach ($volunteerEvents as &$volunteerEvent) {
            $volunteerEvent['start'] = (new \DateTime($volunteerEvent['start']))->format('Y-m-d\TH:i:s');
            $volunteerEvent['color'] = '#C8E6C9'; // Couleur différente pour les bénévolats
            $volunteerEvent['className'] = 'event-benevolat'; // Classe CSS pour les bénévolats
            $volunteerEvent['type'] = 'benevolat';
            $volunteerEvent['icon'] = 'fas fa-hands-helping'; // Icône FontAwesome pour les bénévolats
            $volunteerEvent['status'] = $volunteerEvent['status'];
            $volunteerEvent['imageName'] = $volunteerEvent['imageName'];
        }

        // Fusion des deux ensembles d'événements
        $allEvents = array_merge($events, $volunteerEvents);

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

        // Préparer les données pour le graphique des dons
        $monthLabels = array_column($donationsByMonth, 'month');
        $monthAmounts = array_column($donationsByMonth, 'total_amount');

        // Récupération des données pour le graphique des favoris par semaine (dernières 4 semaines)
        $favoritesByWeek = $connection->fetchAllAssociative('
            SELECT 
                CONCAT("Semaine ", WEEK(date_favori, 1) - WEEK(DATE_SUB(CURDATE(), INTERVAL 4 WEEK), 1) + 1) AS week,
                COUNT(*) AS total_favorites
            FROM favoris
            WHERE date_favori >= DATE_SUB(CURDATE(), INTERVAL 4 WEEK)
            GROUP BY WEEK(date_favori, 1)
            ORDER BY WEEK(date_favori, 1)
        ');

        // Préparer les données pour le graphique des favoris
        $weekLabels = ['Semaine 1', 'Semaine 2', 'Semaine 3', 'Semaine 4'];
        $weekFavorites = [0, 0, 0, 0]; // Initialiser à 0 pour les 4 semaines

        foreach ($favoritesByWeek as $weekData) {
            $weekNumber = (int) str_replace('Semaine ', '', $weekData['week']) - 1; // Convertir "Semaine X" en index (0-3)
            if ($weekNumber >= 0 && $weekNumber < 4) {
                $weekFavorites[$weekNumber] = (int) $weekData['total_favorites'];
            }
        }

        // Envoi des données au template, y compris les données utilisateur
        return $this->render('admin_dashboard/index.html.twig', [
            'totalClients' => $totalClients,
            'totalEvenements' => $totalEvenements,
            'totalPartenaires' => $totalPartenaires,
            'totalDons' => $totalDons,
            'totalDemandesAide' => $totalDemandesAide,
            'events' => json_encode($allEvents),
            'activityLabels' => json_encode($activityLabels),
            'activityCounts' => json_encode($activityCounts),
            'countryLabels' => json_encode($countryLabels),
            'countryCounts' => json_encode($countryCounts),
            'monthLabels' => json_encode($monthLabels),
            'monthAmounts' => json_encode($monthAmounts),
            'weekLabels' => json_encode($weekLabels),
            'weekFavorites' => json_encode($weekFavorites),
            'userData' => json_encode($userData), // Ajout des données utilisateur
            'pageTitle' => 'Tableau de bord',
        ]);
    }
}