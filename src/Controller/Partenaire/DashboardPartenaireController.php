<?php
namespace App\Controller\Partenaire;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

final class DashboardPartenaireController extends AbstractController
{
    #[Route('/dashboard/partenaire', name: 'app_dashboard_partenaire')]
    public function index(): Response
    {
        $user = $this->getUser();

        // Vérifiez si l'utilisateur est connecté et a le rôle partenaire
        if (!$user || !in_array('ROLE_PARTENAIRE', $user->getRoles())) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à accéder à cette section.');
        }

        return $this->render('Partenaire/dashboard_partenaire/dashboard.html.twig', [
            'controller_name' => 'DashboardPartenaireController',
        ]);
    }
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;

    public function __construct(HttpClientInterface $httpClient, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    #[Route('/chatbot', name: 'chatbot_index', methods: ['GET'])]
    public function chatbotPage(): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('Partenaire/chatbot/index.html.twig');
    }

    #[Route('/chatbot/api', name: 'chatbot_api', methods: ['POST'])]
    public function chatbotApi(Request $request): JsonResponse
    {
        $message = $request->request->get('message');
        if (!$message) {
            return new JsonResponse(['error' => 'Le message est requis.'], 400);
        }

        try {
            $response = $this->askOpenAI($message);
            return new JsonResponse(['response' => $response]);
        } catch (\Exception $e) {
            // Log additional details about the error
            $this->logger->error('Erreur ChatGPT: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Erreur lors de la communication avec OpenAI.'], 500);
        }
    }

    private function askOpenAI(string $userMessage): string
    {
        $apiKey = $_ENV['OPENAI_API_KEY'] ?? null;
        if (!$apiKey) {
            throw new \RuntimeException('Clé API OpenAI non définie.');
        }
    
        $url = 'https://api.openai.com/v1/chat/completions';
        $payload = [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $userMessage]
            ],
            'max_tokens' => 150
        ];
    
        $retries = 3;
        $delay = 1;
    
        for ($attempt = 1; $attempt <= $retries; $attempt++) {
            try {
                $response = $this->httpClient->request('POST', $url, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $apiKey,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => $payload,
                ]);
    
                $statusCode = $response->getStatusCode();
                $data = $response->toArray(false); // évite l'exception automatique
    
                if ($statusCode === 429) {
                    // Log l'erreur et indique à l'utilisateur d'attendre
                    $this->logger->warning("Taux limite dépassé (429) - tentative $attempt");
                    sleep($delay); // Attente avant de réessayer
                    $delay *= 2; // Délai exponentiel
                    continue;
                }
    
                if ($statusCode !== 200 || !isset($data['choices'][0]['message']['content'])) {
                    throw new \RuntimeException('Réponse inattendue ou erreur de serveur. Code HTTP: ' . $statusCode);
                }
    
                return trim($data['choices'][0]['message']['content']);
            } catch (\Exception $e) {
                $this->logger->error("Erreur lors de l'appel à OpenAI: " . $e->getMessage());
                throw new \RuntimeException('Erreur de communication avec OpenAI : ' . $e->getMessage());
            }
        }
    
        throw new \RuntimeException('Trop de tentatives échouées (429).');
    }
}