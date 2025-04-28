<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

        return $this->render('dashboard_partenaire/index.html.twig', [
            'controller_name' => 'DashboardPartenaireController',
        ]);
    }
}