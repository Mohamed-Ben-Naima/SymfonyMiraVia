<?php

namespace App\Controller;

use App\Repository\EvenementsRepository; // <-- Import the repository
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route; // Use this for PHP 8 attributes

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EvenementsRepository $evenementsRepository): Response // <-- Inject repository
    {
        // Fetch events where estBloque is 0 (meaning not blocked)
        $evenements = $evenementsRepository->findBy(['estBloque' => 0]);

        // If you want to fetch all and filter in Twig (less recommended for this):
        // $evenements = $evenementsRepository->findAll();

        return $this->render('home/index.html.twig', [ // Or 'review.html.twig' if that's the main file
            'controller_name' => 'HomeController',
            'evenements' => $evenements, // <-- Pass the fetched events
        ]);
    }
}