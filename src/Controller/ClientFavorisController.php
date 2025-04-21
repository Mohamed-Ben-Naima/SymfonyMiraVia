<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ClientFavorisController extends AbstractController
{
    #[Route('/client/favoris', name: 'app_client_favoris')]
    public function index(): Response
    {
        // Static data combining both evenements and benevolats
        $favoris = [
            [
                'id_favori' => 1,
                'type_entite' => 'evenement',
                'entite' => [
                    'id' => 1,
                    'titre' => 'Festival des Lumières à Chiang Mai',
                    'date' => new \DateTime('2025-11-15'),
                    'lieu' => 'Chiang Mai, Thaïlande',
                    'image' => 'https://images.unsplash.com/photo-1603383928958-5e22e8e6d8d4',
                    'partenaire' => 'Voyages Thaï'
                ]
            ],
            [
                'id_favori' => 2,
                'type_entite' => 'benevolat',
                'entite' => [
                    'id' => 1,
                    'titre' => 'Nettoyage de la Plage',
                    'date' => new \DateTime('2025-04-25'),
                    'lieu' => 'Plage de la Marsa',
                    'image' => 'https://images.unsplash.com/photo-1582769923195-c6e60dc1d8dc',
                    'partenaire' => 'EcoTunisie'
                ]
            ],
            [
                'id_favori' => 3,
                'type_entite' => 'evenement',
                'entite' => [
                    'id' => 2,
                    'titre' => 'Séjours dans la Vallée du Douro',
                    'date' => new \DateTime('2025-09-01'),
                    'lieu' => 'Vallée du Douro, Portugal',
                    'image' => 'https://images.unsplash.com/photo-1584970230001-7cf9a1a9ec0f',
                    'partenaire' => 'Wine Experience PT'
                ]
            ]
        ];

        // Security check
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('warning', 'Veuillez vous connecter pour accéder à vos favoris');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('client_favoris/index.html.twig', [
            'favoris' => $favoris,
            'user' => $user,
        ]);
    }
}
