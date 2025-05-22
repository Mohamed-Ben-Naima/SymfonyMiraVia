<?php
// src/Controller/SearchController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * Affiche la page principale avec les formulaires de recherche.
     * * Par défaut, cette route pourrait être la page d'accueil ou une page dédiée /recherche.
     * Changez le chemin de la route ('/') selon vos besoins.
     */
    #[Route('/serch', name: 'app_search_index')] // Ou par exemple #[Route('/recherche', name: 'app_search_index')]
    public function index(): Response
    {
        // Pour l'instant, nous rendons simplement le template statique.
        // Plus tard, vous pourrez passer des objets de formulaire Symfony à ce template.
        // Exemple :
        // $flightForm = $this->createForm(FlightSearchType::class);
        // $hotelForm = $this->createForm(HotelSearchType::class);
        //
        // return $this->render('search/index.html.twig', [
        //     'flight_form' => $flightForm->createView(),
        //     'hotel_form' => $hotelForm->createView(),
        //     // ... autres variables ...
        // ]);

        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController', // Juste une variable d'exemple
        ]);
    }
}