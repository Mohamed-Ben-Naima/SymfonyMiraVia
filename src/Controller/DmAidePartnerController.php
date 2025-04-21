<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DmAidePartnerController extends AbstractController
{
    #[Route('/dm/aide/partner', name: 'app_dm_aide_partner')]
    public function index(): Response
    {
        return $this->render('dm_aide_partner/index.html.twig', [
            'controller_name' => 'DmAidePartnerController',
        ]);
    }
}
