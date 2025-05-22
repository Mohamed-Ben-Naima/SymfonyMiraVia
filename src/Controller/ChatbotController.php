<?php

namespace App\Controller;

use App\Service\GeminiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route; // Corrected namespace
use Psr\Log\LoggerInterface;


class ChatbotController extends AbstractController
{
    private GeminiService $geminiService;
    private LoggerInterface $logger;

    public function __construct(GeminiService $geminiService, LoggerInterface $logger)
    {
        $this->geminiService = $geminiService;
        $this->logger = $logger;
    }

    #[Route('/api/chatbot/ask', name: 'api_chatbot_ask', methods: ['POST'])]
    public function ask(Request $request): JsonResponse
    {
        $this->logger->info('Chatbot API endpoint hit.');
        try {
            $content = $request->getContent();
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->logger->error('Invalid JSON received: ' . json_last_error_msg(), ['content' => $content]);
                return new JsonResponse(['reply' => 'Invalid request format. Expected JSON.'], 400);
            }

            $userMessage = $data['message'] ?? null;

            if (empty($userMessage)) {
                $this->logger->warning('No message received in chatbot request.');
                return new JsonResponse(['reply' => 'No message received.'], 400);
            }
            
            $this->logger->info('User message received: ' . $userMessage);

            $botReply = $this->geminiService->getChatbotResponse($userMessage);

            return new JsonResponse(['reply' => $botReply]);

        } catch (\Throwable $e) {
            $this->logger->critical('Unhandled exception in chatbot controller: ' . $e->getMessage(), ['exception' => $e]);
            return new JsonResponse(['reply' => 'An unexpected error occurred on the server.'], 500);
        }
    }
}