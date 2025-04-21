<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminPartnerController extends AbstractController
{
    #[Route('/admin/partner', name: 'app_admin_partner')]
    public function index(): Response
    {
        return $this->render('admin_partner/index.html.twig', [
            'controller_name' => 'AdminPartnerController',
            'pageTitle' => 'Partenaire', // Le titre dynamique

        ]);
    }
    
}
