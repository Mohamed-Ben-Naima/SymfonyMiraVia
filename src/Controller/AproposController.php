<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AproposController extends AbstractController
{
    private ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    #[Route('/apropos', name: 'app_apropos_page')] // Changed route name and path for clarity
    public function index(): Response // Renamed method to 'index' as it's a common convention
    {
        $geminiApiKey = $this->params->get('gemini_api_key');
        // Or: $geminiApiKey = $_ENV['GEMINI_API_KEY'] ?? '';

        return $this->render('apropos/propos.html.twig', [
            'gemini_api_key' => $geminiApiKey,
        ]);
    }
}