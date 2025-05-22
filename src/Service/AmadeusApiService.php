<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AmadeusApiService
{
    private HttpClientInterface $client;
    private CacheInterface $cache;
    private string $clientId;
    private string $clientSecret;
    private string $apiBaseUrl;
    // accessToken and tokenExpirationTime are not directly used here anymore
    // as the cache handles the token's lifecycle based on its expiry.

    public function __construct(
        HttpClientInterface $client,
        CacheInterface $cache,
        ParameterBagInterface $params
    ) {
        $this->client = $client;
        $this->cache = $cache;
        $this->clientId = $params->get('amadeus_client_id');
        $this->clientSecret = $params->get('amadeus_client_secret');
        $this->apiBaseUrl = $params->get('amadeus_api_base_url_test');
    }

    private function getAccessToken(): ?string
    {
        return $this->cache->get('amadeus_access_token', function (ItemInterface $item) {
            $response = $this->client->request('POST', $this->apiBaseUrl . '/v1/security/oauth2/token', [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'body' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                // In a real app, log $response->getContent(false) for error details
                error_log('Amadeus Token API Error: ' . $response->getContent(false));
                return null;
            }

            $data = $response->toArray();
            if (!isset($data['access_token']) || !isset($data['expires_in'])) {
                 error_log('Amadeus Token API Error: Invalid token response structure.');
                 return null;
            }
            $item->expiresAfter((int)$data['expires_in'] - 60); // Cache for slightly less than actual expiry
            return $data['access_token'];
        });
    }

    public function searchFlights(
        string $origin,
        string $destination,
        string $departureDate,
        ?string $returnDate, // Nullable for one-way
        int $adults = 1,
        string $currencyCode = 'TND'
        // bool $nonStop = false; // If you need to pass this based on form
    ): array {
        $token = $this->getAccessToken();
        if (!$token) {
            return ['error' => 'Unable to authenticate with Amadeus.'];
        }

        $queryParams = [
            'originLocationCode' => $origin,
            'destinationLocationCode' => $destination,
            'departureDate' => $departureDate,
            'adults' => $adults,
            'currencyCode' => $currencyCode,
            'max' => 25 // Limit results, good practice
        ];

        if ($returnDate) {
            $queryParams['returnDate'] = $returnDate;
        }
        // if ($nonStop) {
        //    $queryParams['nonStop'] = 'true';
        // }


        try {
            $response = $this->client->request('GET', $this->apiBaseUrl . '/v2/shopping/flight-offers', [ // Corrected line
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
                'query' => $queryParams,
            ]);

            if ($response->getStatusCode() !== 200) {
                error_log('Amadeus Flight Search API Error: ' . $response->getContent(false));
                return ['error' => 'Amadeus API request failed.', 'statusCode' => $response->getStatusCode(), 'details' => $response->toArray(false)];
            }

            $responseData = $response->toArray();
            return $this->formatFlightOffers($responseData);

        } catch (\Throwable $e) {
            error_log('Amadeus API Exception: ' . $e->getMessage());
            return ['error' => 'An exception occurred while calling Amadeus API.', 'details' => $e->getMessage()];
        }
    }

    private function formatFlightOffers(array $amadeusResponse): array
    {
        $formattedOffers = [];
        $data = $amadeusResponse['data'] ?? [];
        $dictionaries = $amadeusResponse['dictionaries'] ?? [];

        foreach ($data as $offer) {
            if (!isset($offer['itineraries']) || !is_array($offer['itineraries'])) continue;

            $formattedOffer = [
                'id' => $offer['id'] ?? 'N/A',
                'price' => $offer['price']['total'] ?? 'N/A',
                'currency' => $offer['price']['currency'] ?? 'N/A',
                'outbound' => null,
                'inbound' => null,
            ];

            foreach ($offer['itineraries'] as $index => $itinerary) {
                if (!isset($itinerary['segments']) || !is_array($itinerary['segments'])) continue;

                $leg = [
                    'duration' => $itinerary['duration'] ?? 'N/A',
                    'segments' => []
                ];
                
                $segmentCount = count($itinerary['segments']);
                $isDirect = $segmentCount === 1;
                $stopsDisplay = $isDirect ? 'Direct' : ($segmentCount - 1) . ' escale(s)';
                
                if (!$isDirect && $segmentCount > 1) {
                    $stopAirports = [];
                    for ($i = 0; $i < $segmentCount - 1; $i++) {
                        if (isset($itinerary['segments'][$i]['arrival']['iataCode'])) {
                            $stopAirports[] = $itinerary['segments'][$i]['arrival']['iataCode'];
                        }
                    }
                    if (!empty($stopAirports)) {
                         $stopsDisplay .= ' via ' . implode(', ', array_unique($stopAirports)); // Use array_unique
                    }
                }
                $leg['stopsDisplay'] = $stopsDisplay;

                foreach ($itinerary['segments'] as $segment) {
                    $carrierCode = $segment['carrierCode'] ?? 'N/A';
                    $aircraftCode = $segment['aircraft']['code'] ?? 'N/A';
                    $leg['segments'][] = [
                        'departureAirport' => $segment['departure']['iataCode'] ?? 'N/A',
                        'departureTime' => $segment['departure']['at'] ?? 'N/A',
                        'arrivalAirport' => $segment['arrival']['iataCode'] ?? 'N/A',
                        'arrivalTime' => $segment['arrival']['at'] ?? 'N/A',
                        'duration' => $segment['duration'] ?? 'N/A',
                        'flightNumber' => $carrierCode . ($segment['number'] ?? ''),
                        'carrier' => $dictionaries['carriers'][$carrierCode] ?? $carrierCode,
                        'aircraft' => $dictionaries['aircraft'][$aircraftCode] ?? $aircraftCode
                    ];
                }
                
                if ($index === 0) { // Outbound
                    $formattedOffer['outbound'] = $leg;
                } elseif ($index === 1) { // Inbound
                    $formattedOffer['inbound'] = $leg;
                }
            }
            $formattedOffers[] = $formattedOffer;
        }
        return $formattedOffers;
    }
}