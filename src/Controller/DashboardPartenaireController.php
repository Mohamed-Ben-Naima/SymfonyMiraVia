<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardPartenaireController extends AbstractController{
    #[Route('/dashboard/partenaire', name: 'app_dashboard_partenaire')]
    public function index(): Response
    {
        return $this->render('dashboard_partenaire/dashboard.html.twig', [
            'controller_name' => 'DashboardPartenaireController',
        ]);
    }
}
