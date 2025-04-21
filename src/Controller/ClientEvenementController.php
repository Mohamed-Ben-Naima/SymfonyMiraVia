<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ClientEvenementController extends AbstractController
{
    #[Route('/client/evenement', name: 'app_client_evenement')]
    public function index(): Response
    {
        // Static data with high-quality image URLs
        $evenements = [
            [
                'id_evenement' => 1,
                'titre' => 'Festival des Lumières à Chiang Mai',
                'description' => 'Participez au festival des lanternes en séjournant chez des hôtes locaux, qui vous feront découvrir les traditions et la magie de cet événement unique.',
                'date_evenement' => new \DateTime('2025-11-15'),
                'lieu' => 'Chiang Mai, Thaïlande',
                'imageName' => 'https://media-cdn.tripadvisor.com/media/attractions-splice-spp-720x480/0f/af/c1/f7.jpg',
                'partenaire' => [
                    'nom_entreprise' => 'Voyages Thaï',
                    'logo' => 'https://cdn-icons-png.flaticon.com/512/196/196530.png'
                ]
            ],
            [
                'id_evenement' => 2,
                'titre' => 'Séjours dans la Vallée du Douro',
                'description' => 'Vivez une expérience authentique en logeant chez des hôtes locaux dans la région viticole du Douro.',
                'date_evenement' => new \DateTime('2025-09-01'),
                'lieu' => 'Vallée du Douro, Portugal',
                'imageName' => 'https://th.bing.com/th/id/R.b190bfbfaf2c3b7adf590b3c64940499?rik=rhUCjAd3r9v0VA&pid=ImgRaw&r=0',
                'partenaire' => [
                    'nom_entreprise' => 'Wine Experience PT',
                    'logo' => 'https://cdn-icons-png.flaticon.com/512/3062/3062634.png'
                ]
            ],
            [
                'id_evenement' => 3,
                'titre' => 'Rallye Aventure en 4x4 au Sahara',
                'description' => 'Parcourez les vastes étendues du désert du Sahara lors d\'un rallye aventure.',
                'date_evenement' => new \DateTime('2025-03-01'),
                'lieu' => 'Sahara, Tunisie',
                'imageName' => 'https://static.owaka.com/gallery/15302/5adventure-template-.jpg',
                'partenaire' => [
                    'nom_entreprise' => 'Désert Adventures',
                    'logo' => 'https://cdn-icons-png.flaticon.com/512/619/619032.png'
                ]
            ]
        ];

        
        // User verification
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('warning', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_login');
        }

        // Role verification (uncomment when ready)
        // if (!$this->isGranted('ROLE_CLIENT')) {
        //     $this->addFlash('danger', 'Accès non autorisé');
        //     return $this->redirectToRoute('app_home');
        // }

        return $this->render('client_evenement/index.html.twig', [
            'evenements' => $evenements,
            'user' => $user,
        ]);
    }
}