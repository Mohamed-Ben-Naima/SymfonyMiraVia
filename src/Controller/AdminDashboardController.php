<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminDashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    public function index(): Response
    {
        // Exemple de données pour les cartes
        $cards = [
            [
                'title' => 'Total Page Views',
                'value' => '4,42,236',
                'badge_class' => 'bg-light-primary border-primary',
                'icon' => 'ti ti-trending-up',
                'percentage' => '59.3%',
                'description' => 'You made an extra 35,000 this year',
            ],
            [
                'title' => 'Total Users',
                'value' => '78,250',
                'badge_class' => 'bg-light-success border-success',
                'icon' => 'ti ti-trending-up',
                'percentage' => '70.5%',
                'description' => 'You made an extra 8,900 this year',
            ],
            [
                'title' => 'Total Order',
                'value' => '18,800',
                'badge_class' => 'bg-light-warning border-warning',
                'icon' => 'ti ti-trending-down',
                'percentage' => '27.4%',
                'description' => 'You made an extra 1,943 this year',
            ],
            [
                'title' => 'Total Sales',
                'value' => '$35,078',
                'badge_class' => 'bg-light-danger border-danger',
                'icon' => 'ti ti-trending-down',
                'percentage' => '27.4%',
                'description' => 'You made an extra $20,395 this year',
            ],
        ];

        // Passage des données au template
        return $this->render('admin_dashboard/index.html.twig', [
            'controller_name' => 'AdminDashboardController',
            'cards' => $cards,
        ]);
    }
}