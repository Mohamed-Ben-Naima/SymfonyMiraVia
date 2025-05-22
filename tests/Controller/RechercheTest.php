<?php

namespace App\Tests\Controller;

use App\Service\AmadeusApiService; // Service to be mocked
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response; // For HTTP status codes

class RechercheTest extends WebTestCase
{
    private $client;
    private $mockAmadeusService;

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient(); // Create a test client

        // Create a mock for AmadeusApiService
        // ->disableOriginalConstructor() is important if your service has dependencies in its constructor
        // that you don't want to deal with during mocking for this specific test.
        // However, if the constructor is simple or you want to test its logic, you might not disable it.
        // For service mocking, disabling is often safer.
        $this->mockAmadeusService = $this->getMockBuilder(AmadeusApiService::class)
                                         ->disableOriginalConstructor() 
                                         ->getMock();

        // Replace the actual service with the mock in the service container
        // static::getContainer() gives access to the container in WebTestCase
        static::getContainer()->set(AmadeusApiService::class, $this->mockAmadeusService);
    }

    /**
     * Test for a successful round-trip flight search.
     * This is one of your "testRechercheAccepter" scenarios.
     */
    public function testRechercheAccepterRoundTrip(): void
    {
        // 1. Define the expected successful output from the AmadeusApiService
        $expectedFlightOffers = [
            [
                'id' => '1',
                'type' => 'flight-offer',
                'source' => 'GDS',
                'instantTicketingRequired' => false,
                'nonHomogeneous' => false,
                'oneWay' => false,
                'lastTicketingDate' => '2025-12-01',
                'numberOfBookableSeats' => 9,
                'itineraries' => [ /* ... detailed itinerary ... */ ],
                'price' => ['currency' => 'EUR', 'total' => '250.00'],
                // ... other flight data fields as returned by your service
            ],
        ];

        // 2. Configure the mock AmadeusApiService
        // Expect 'searchFlights' to be called once with specific arguments
        $this->mockAmadeusService->expects($this->once())
            ->method('searchFlights')
            ->with(
                $this->equalTo('CDG'),          // origin
                $this->equalTo('JFK'),          // destination
                $this->equalTo('2025-12-01'),   // departureDate
                $this->equalTo('2025-12-10'),   // returnDate
                $this->equalTo(2)               // adults
            )
            ->willReturn($expectedFlightOffers); // And return the predefined flight offers

        // 3. Make a GET request to your API endpoint with valid parameters
        $this->client->request(
            'GET',
            '/api/search-flights', // Your route name from VolController
            [
                'origin' => 'CDG',
                'destination' => 'JFK',
                'departureDate' => '2025-12-01',
                'returnDate' => '2025-12-10',
                'adults' => 2,
            ]
        );

        // 4. Assert the response
        $this->assertResponseIsSuccessful(); // Checks for 2xx status code
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseContent = $this->client->getResponse()->getContent();
        $this->assertJson($responseContent); // Check if the response is valid JSON
        // Compare the JSON response with the expected structure/data
        $this->assertJsonStringEqualsJsonString(json_encode($expectedFlightOffers), $responseContent);
    }

    /**
     * Test for a successful one-way flight search.
     * Another "testRechercheAccepter" scenario.
     */
    public function testRechercheAccepterOneWay(): void
    {
        $expectedFlightOffers = [
            ['id' => '2', 'price' => '180.50', /* ... other flight data ... */],
        ];

        $this->mockAmadeusService->expects($this->once())
            ->method('searchFlights')
            ->with(
                $this->equalTo('LHR'),
                $this->equalTo('AMS'),
                $this->equalTo('2025-11-15'),
                $this->isNull(), // For one-way, returnDate should be null when passed to service
                $this->equalTo(1)
            )
            ->willReturn($expectedFlightOffers);

        $this->client->request(
            'GET',
            '/api/search-flights',
            [
                'origin' => 'LHR',
                'destination' => 'AMS',
                'departureDate' => '2025-11-15',
                // 'returnDate' is omitted, which your controller handles as one-way
                'adults' => 1,
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertJsonStringEqualsJsonString(json_encode($expectedFlightOffers), $this->client->getResponse()->getContent());
    }

    /**
     * Test for a refused search due to missing required parameters.
     * This is one of your "testRechercheRefuser" scenarios.
     */
    public function testRechercheRefuserMissingParameters(): void
    {
        // 1. Make a GET request with missing 'destination'
        $this->client->request(
            'GET',
            '/api/search-flights',
            [
                'origin' => 'CDG',
                // 'destination' is missing
                'departureDate' => '2025-12-01',
                'adults' => 1,
            ]
        );

        // 2. Assert the response
        // Expecting a 400 Bad Request status
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseContent = $this->client->getResponse()->getContent();
        $this->assertJson($responseContent);
        $responseData = json_decode($responseContent, true);

        $this->assertArrayHasKey('error', $responseData);
        $this->assertEquals('Missing required flight search parameters.', $responseData['error']);

        // Ensure the AmadeusApiService was NOT called because validation should fail before
        $this->mockAmadeusService->expects($this->never())->method('searchFlights');
    }

    /**
     * Test for a scenario where the AmadeusApiService itself returns an error.
     * This could be considered a "refused" or "error" scenario from the client's perspective.
     */
    public function testRechercheRefuserWhenAmadeusServiceReturnsError(): void
    {
        // 1. Configure the mock AmadeusApiService to return an error structure
        $amadeusError = ['error' => 'Amadeus API Error', 'details' => 'Service unavailable or invalid request to Amadeus.'];
        $this->mockAmadeusService->expects($this->once())
            ->method('searchFlights')
            ->withAnyParameters() // We don't care about specific params here, just that it's called
            ->willReturn($amadeusError); // Simulate the service returning an error

        // 2. Make a GET request with valid-looking parameters (so it reaches the service call)
        $this->client->request(
            'GET',
            '/api/search-flights',
            [
                'origin' => 'XYZ',
                'destination' => 'ABC',
                'departureDate' => '2026-01-01',
                'adults' => 1,
            ]
        );

        // 3. Assert the response
        // Your controller forwards this as an HTTP_INTERNAL_SERVER_ERROR
        $this->assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseContent = $this->client->getResponse()->getContent();
        $this->assertJson($responseContent);
        $this->assertJsonStringEqualsJsonString(json_encode($amadeusError), $responseContent);
    }


    /**
     * This method is called after each test.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        // It's good practice to unset properties to free up memory,
        // especially if they hold large objects or resources.
        $this->client = null;
        $this->mockAmadeusService = null;
    }
}
