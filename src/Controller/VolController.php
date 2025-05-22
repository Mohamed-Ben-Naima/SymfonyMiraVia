<?php
// src/Controller/VolController.php

namespace App\Controller;

use App\Repository\AeroportRepository;
use App\Service\AmadeusApiService; // Import the service
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse; // For JSON responses
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VolController extends AbstractController
{
    // Your existing index() method to render the page
    #[Route('/vol', name: 'app_vol_page')]
    public function index(AeroportRepository $aeroportRepository): Response
    {
        $aeroports = $aeroportRepository->findAll(); // You already do this
        return $this->render('vols/vol.html.twig', [
            'aeroports' => $aeroports,
        ]);
    }

    #[Route('/api/search-flights', name: 'api_search_flights', methods: ['GET'])]
    public function searchFlightsApi(Request $request, AmadeusApiService $amadeusService): JsonResponse
    {
        // Get parameters from the request (matches your JS form data)
        $origin = $request->query->get('origin');
        $destination = $request->query->get('destination');
        $departureDate = $request->query->get('departureDate');
        $returnDate = $request->query->get('returnDate'); // Will be null for one-way
        $adults = (int)$request->query->get('adults', 1);
        // $tripType = $request->query->get('tripType'); // 'one-way' or 'round-trip'

        // Basic validation (add more as needed)
        if (!$origin || !$destination || !$departureDate) {
            return new JsonResponse(['error' => 'Missing required flight search parameters.'], Response::HTTP_BAD_REQUEST);
        }

        // If it's a one-way trip, ensure returnDate is null before passing to service
        // The JS already handles not sending it if disabled, but good to double check
        $isOneWay = !$returnDate || $returnDate === ''; // A simple check

        $flightOffers = $amadeusService->searchFlights(
            $origin,
            $destination,
            $departureDate,
            $isOneWay ? null : $returnDate,
            $adults
        );

        if (isset($flightOffers['error'])) {
             return new JsonResponse($flightOffers, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($flightOffers);
    }
}