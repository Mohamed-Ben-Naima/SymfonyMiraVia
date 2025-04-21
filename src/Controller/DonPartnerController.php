<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DonPartnerController extends AbstractController
{
    #[Route('/don/partner', name: 'app_don_partner')]
    public function index(): Response
    {
        return $this->render('don_partner/index.html.twig', [
            'controller_name' => 'DonPartnerController',
        ]);
    }
}
