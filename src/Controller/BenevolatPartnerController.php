<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BenevolatPartnerController extends AbstractController
{
    #[Route('/benevolat/partner', name: 'app_benevolat_partner')]
    public function index(): Response
    {
        return $this->render('benevolat_partner/index.html.twig', [
            'controller_name' => 'BenevolatPartnerController',
        ]);
    }
}
