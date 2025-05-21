<?php
namespace App\Tests\Controller\Partenaire;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class DashboardPartenaireControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testChatbotApiWithValidMessage(): void
    {
        // On simule le client HTTP Gemini avec une réponse factice
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockResponse = $this->createMock(ResponseInterface::class);

        $mockResponse->method('getStatusCode')->willReturn(200);
        $mockResponse->method('toArray')->willReturn([
            'candidates' => [
                ['content' => ['parts' => [['text' => 'Réponse générée par Gemini.']]]]
            ]
        ]);

        $mockHttpClient->method('request')->willReturn($mockResponse);

        // Injecte le client Gemini simulé dans le conteneur Symfony
        static::getContainer()->set(HttpClientInterface::class, $mockHttpClient);

        // Envoie la requête POST avec un message
        $this->client->request('POST', '/chatbot/api', [
            'message' => 'Bonjour, que fais-tu ?',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('response', $responseData);
        $this->assertSame('Réponse générée par Gemini.', $responseData['response']);
    }

    public function testChatbotApiWithNoMessage(): void
    {
        $this->client->request('POST', '/chatbot/api', []);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('error', $responseData);
        $this->assertSame('Le message est requis.', $responseData['error']);
    }
}
