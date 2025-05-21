<?php
namespace App\Controller\Partenaire;

use App\Entity\Utilisateurs;
use App\Repository\BenevolatRepository;
use App\Repository\ClientsRepository;
use App\Repository\DonsRepository;
use App\Repository\DemandesAideRepository;
use App\Repository\EvenementsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

final class DashboardPartenaireController extends AbstractController
{
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;

    public function __construct(HttpClientInterface $httpClient, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    #[Route('/api/user/avatar', name: 'api_user_avatar', methods: ['GET'])]
    public function getUserAvatar(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof Utilisateurs) {
            return new JsonResponse(['error' => 'Utilisateur non connecté.'], 400);
        }

        $avatarName = $user->getAvatar();

        if (!$avatarName) {
            return new JsonResponse(['avatarUrl' => '/images-partner/user.png']);
        }

        $avatarsDirectory = $this->getParameter('avatars_directory');
        $avatarPath = $avatarsDirectory . '/' . $avatarName;

        if (!file_exists($avatarPath)) {
            return new JsonResponse(['avatarUrl' => '/images-partner/user.png']);
        }

        $avatarUrl = '/uploads/avatars/' . $avatarName;

        return new JsonResponse(['avatarUrl' => $avatarUrl]);
    }

    #[Route('/dashboard/partenaire', name: 'app_dashboard_partenaire')]
        public function index(
            Request $request,
            ClientsRepository $clientRepository,
            EvenementsRepository $eventRepository,
            BenevolatRepository $benevolatRepository,
            DemandesAideRepository $demandeAideRepository,
            DonsRepository $donsRepository
        ): Response {
            $user = $this->getUser();

            if (!$user instanceof Utilisateurs) {
                throw $this->createAccessDeniedException('Vous devez être un partenaire connecté.');
            }

            if (!$user || !in_array('ROLE_PARTENAIRE', $user->getRoles())) {
                throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à accéder à cette section.');
            }

            // Retrieve the selected year from the query parameters (default to the current year)
            $year = $request->query->get('year', date('Y'));

            // Fetch data for the partner
            $evenements = $eventRepository->findBy(['idPartenaire' => $user->getIdUtilisateur()]);
            $clientCount = $clientRepository->count([]);
            $eventCount = $eventRepository->count(['idPartenaire' => $user->getIdUtilisateur()]);
            $benevolatCount = $benevolatRepository->count(['idPartenaire' => $user->getIdUtilisateur()]);
            $demandeAideCount = $demandeAideRepository->count(['idPartenaire' => $user->getIdUtilisateur()]);
            $donationCount = $donsRepository->count(['idPartenaire' => $user->getIdUtilisateur()]);
            $donationsAccepted = $donsRepository->getTotalAcceptedDonationsByYear($user->getIdUtilisateur(), $year);
            $pendingDonations = $donsRepository->getTotalPendingDonationsByYear($user->getIdUtilisateur(), $year);
            $availableYears = $donsRepository->getAvailableYears($user->getIdUtilisateur());
            $donationsByMonth = $donsRepository->getDonationsByMonth($user->getIdUtilisateur());

            return $this->render('Partenaire/dashboard_partenaire/dashboard.html.twig', [
                'evenements' => $evenements,
                'clients' => $clientCount,
                'events' => $eventCount,
                'benevolats' => $benevolatCount,
                'demandes' => $demandeAideCount,
                'dons' => $donationCount,
                'donationsAccepted' => $donationsAccepted,
                'pendingDonations' => $pendingDonations,
                'availableYears' => $availableYears,
                'donationsByMonth' => $donationsByMonth,
                'selectedYear' => $year,
            ]);
        }

    #[Route('/chatbot', name: 'chatbot_index', methods: ['GET'])]
    public function chatbotPage(): Response
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
        $response = $this->askGemini($message);
        return new JsonResponse(['response' => $response]);
    } catch (\Exception $e) {
        $this->logger->error('Erreur Gemini: ' . $e->getMessage());
        return new JsonResponse(['error' => 'Erreur lors de la communication avec Gemini.'], 500);
    }
}
   private function askGemini(string $userMessage): string
{
    $apiKey = $_ENV['GEMINI_API_KEY'];

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}";

    $payload = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $userMessage]
                ]
            ]
        ]
    ];

    try {
        $response = $this->httpClient->request('POST', $url, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => $payload,
        ]);

        $statusCode = $response->getStatusCode();
        $data = $response->toArray(false);

        if ($statusCode !== 200 || !isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            throw new \RuntimeException('Réponse invalide ou erreur serveur.');
        }

        return trim($data['candidates'][0]['content']['parts'][0]['text']);
    } catch (\Exception $e) {
        $this->logger->error("Erreur lors de l'appel à Gemini : " . $e->getMessage());
        throw new \RuntimeException('Erreur de communication avec Gemini : ' . $e->getMessage());
    }
}

    
}
