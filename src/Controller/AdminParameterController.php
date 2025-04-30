<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminParameterController extends AbstractController
{
    #[Route('/admin/parameter', name: 'app_admin_settings')]
    public function index(): Response
    {
        return $this->render('admin_parameter/index.html.twig', [
            'controller_name' => 'AdminParameterController',
            'pageTitle' => 'Paramètre', // Le titre dynamique
        ]);
    }

    #[Route('/admin/generate-report', name: 'app_admin_generate_report', methods: ['POST'])]
    public function generateReport(Request $request): JsonResponse
    {
        $reportType = $request->request->get('reportType');
        $reportPeriod = $request->request->get('reportPeriod');

        // Logique pour générer le rapport
        $data = [
            'type' => $reportType,
            'period' => $reportPeriod,
            'metrics' => [
                'active_users' => 1245,
                'average_time' => '12 min'
            ]
        ];

        return new JsonResponse(['status' => 'success', 'data' => $data]);
    }
}