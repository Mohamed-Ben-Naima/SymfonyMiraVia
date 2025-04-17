<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminDemandeAideController extends AbstractController
{
    #[Route('/admin/demande/aide', name: 'app_admin_aid_requests')]
    public function index(): Response
    {
        return $this->render('admin_demande_aide/index.html.twig', [
            'controller_name' => 'AdminDemandeAideController',
            'pageTitle' => 'Demandes Aide', // Le titre dynamique
        ]);
    }
}
