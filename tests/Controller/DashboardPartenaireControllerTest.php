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
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockResponse = $this->createMock(ResponseInterface::class);

        $mockResponse->method('getStatusCode')->willReturn(200);
        $mockResponse->method('toArray')->willReturn([
            'candidates' => [
                ['content' => ['parts' => [['text' => 'Réponse générée par Gemini.']]]]
            ]
        ]);

        $mockHttpClient->method('request')->willReturn($mockResponse);
        static::getContainer()->set(HttpClientInterface::class, $mockHttpClient);

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

    public function testChatbotApiWithEmptyMessage(): void
    {
        $this->client->request('POST', '/chatbot/api', [
            'message' => '',
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $responseData);
        $this->assertSame('Le message ne peut pas être vide.', $responseData['error']);
    }

    public function testChatbotApiGeminiFailure(): void
    {
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->method('request')->willThrowException(new \Exception('Erreur Gemini'));

        static::getContainer()->set(HttpClientInterface::class, $mockHttpClient);

        $this->client->request('POST', '/chatbot/api', [
            'message' => 'Provoquer une erreur',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $responseData);
        $this->assertSame('Une erreur est survenue lors du traitement.', $responseData['error']);
    }
}
