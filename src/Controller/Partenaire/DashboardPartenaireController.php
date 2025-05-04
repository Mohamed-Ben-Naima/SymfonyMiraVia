<?php
namespace App\Controller\Partenaire;

use App\Entity\Utilisateurs;
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

        if (!$user instanceof Utilisateurs) {
            throw $this->createAccessDeniedException('Vous devez être un partenaire connecté.');
        }

        if ($user) {
            $this->addFlash('success', 'Bienvenue, ' . $user->getNom(). ' '. $user->getPrenom());
        } else {
            $this->addFlash('error', 'Utilisateur non connecté.');
        }
        

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
            $response = $this->askDeepSeek($message);
            return new JsonResponse(['response' => $response]);
        } catch (\Exception $e) {
            // Log additional details about the error
            $this->logger->error('Erreur ChatGPT: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Erreur lors de la communication avec OpenAI.'], 500);
        }
    }

    private function askDeepSeek(string $userMessage): string
{
    $apiKey = 'sk-91a20114695c4d1496e0884f4f295d2b'; // Remplace ceci par une variable d'environnement dans un projet sérieux

    $url = 'https://api.deepseek.com/chat/completions';

    $payload = [
        'model' => 'deepseek-chat',
        'messages' => [
            ['role' => 'system', 'content' => 'You are a helpful assistant.'],
            ['role' => 'user', 'content' => $userMessage]
        ],
        'stream' => false
    ];

    try {
        $response = $this->httpClient->request('POST', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => $payload,
        ]);

        $statusCode = $response->getStatusCode();
        $data = $response->toArray(false); // pour éviter les exceptions

        if ($statusCode !== 200 || !isset($data['choices'][0]['message']['content'])) {
            throw new \RuntimeException('Réponse inattendue ou erreur de serveur. Code HTTP: ' . $statusCode);
        }

        return trim($data['choices'][0]['message']['content']);

    } catch (\Exception $e) {
        $this->logger->error("Erreur lors de l'appel à DeepSeek: " . $e->getMessage());
        throw new \RuntimeException('Erreur de communication avec DeepSeek : ' . $e->getMessage());
    }
}
#[Route('/api/user/avatar', name: 'api_user_avatar', methods: ['GET'])]
public function getUserAvatar(): JsonResponse
{
    $user = $this->getUser();  // Récupérer l'utilisateur connecté

    if (!$user) {
        return new JsonResponse(['error' => 'Utilisateur non connecté.'], 400);
    }

    // Récupère le nom de l'avatar
    $avatarName = $user->getAvatar(); // Nom du fichier avatar (ex: avatar123.png)

    // Si aucun avatar n'est défini, utiliser l'avatar par défaut
    if (!$avatarName) {
        return new JsonResponse(['avatarUrl' => '/images-partner/user.png']);
    }

    // Chemin où les avatars sont stockés (dossier public/uploads/avatars)
    $avatarsDirectory = $this->getParameter('avatars_directory');
    $avatarPath = $avatarsDirectory . '/' . $avatarName;

    // Vérifie si le fichier existe
    if (!file_exists($avatarPath)) {
        return new JsonResponse(['avatarUrl' => '/images-partner/user.png']);
    }

    // Génère l'URL complète de l'avatar accessible publiquement
    $avatarUrl = '/uploads/avatars/' . $avatarName;

    return new JsonResponse(['avatarUrl' => $avatarUrl]);
}

}