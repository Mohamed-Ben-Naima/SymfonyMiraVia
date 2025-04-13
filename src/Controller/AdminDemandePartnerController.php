<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminDemandePartnerController extends AbstractController
{
    #[Route('/admin/demande/partner', name: 'app_admin_demande_partner')]
    public function index(): Response
    {
        return $this->render('admin_demande_partner/index.html.twig', [
            'controller_name' => 'AdminDemandePartnerController',
        ]);
    }
}
