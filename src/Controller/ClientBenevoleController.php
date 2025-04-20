<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ClientBenevoleController extends AbstractController
{
    #[Route('/client/benevole', name: 'app_client_benevole')]
    public function index(): Response
    {
        // Static data matching benevolat table structure
        $benevolats = [
            [
                'id_benevolat' => 1,
                'titre' => 'Nettoyage de la Plage',
                'description' => 'Rejoignez-nous pour une journée de nettoyage de la plage afin de préserver notre environnement. Fournissons des sacs poubelles et des gants.',
                'date_benevolat' => new \DateTime('2025-04-25 09:00:00'),
                'lieu' => 'Plage de la Marsa, Tunisie',
                'imageName' => 'https://www.eskaledarmor.com/uploads/2024/02/nettoyage-plage-binic-28-fevrier-750x562.png',
                'status' => 'ouvert',
                'partenaire' => [
                    'nom_entreprise' => 'EcoTunisie',
                    'logo' => 'https://cdn-icons-png.flaticon.com/512/3099/3099076.png'
                ]
            ],
            [
                'id_benevolat' => 2,
                'titre' => 'Distribution de Repas',
                'description' => 'Aidez-nous à préparer et distribuer des repas chauds aux personnes dans le besoin dans différents quartiers de la ville.',
                'date_benevolat' => new \DateTime('2025-05-10 08:00:00'),
                'lieu' => 'Centre Ville, Tunis',
                'imageName' => 'https://images.pexels.com/photos/6646877/pexels-photo-6646877.jpeg',
                'status' => 'ouvert',
                'partenaire' => [
                    'nom_entreprise' => 'SolidaritéTN',
                    'logo' => 'https://cdn-icons-png.flaticon.com/512/619/619017.png'
                ]
            ],
            [
                'id_benevolat' => 3,
                'titre' => 'Soutien Scolaire',
                'description' => 'Aidez des enfants défavorisés dans leur parcours scolaire en fournissant un soutien en mathématiques et français.',
                'date_benevolat' => new \DateTime('2025-05-15 16:00:00'),
                'lieu' => 'Espace Associatif, Sousse',
                'imageName' => 'https://campussuite-storage.s3.amazonaws.com/prod/1558712/d5d32b58-674e-11e9-aaaf-0a6d339d5cd0/2779768/eeef908a-0885-11ef-b550-0affcfac6717/optimizations/1',
                'status' => 'ouvert',
                'partenaire' => [
                    'nom_entreprise' => 'Éducation Pour Tous',
                    'logo' => 'https://cdn-icons-png.flaticon.com/512/3079/3079158.png'
                ]
            ]
        ];

        // Security check
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('warning', 'Veuillez vous connecter pour accéder aux opportunités de bénévolat');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('client_benevole/index.html.twig', [
            'benevolats' => $benevolats,
            'user' => $user,
        ]);
    }
}