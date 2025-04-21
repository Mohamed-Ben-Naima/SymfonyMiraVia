<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SettingsPartnerController extends AbstractController
{
    #[Route('/settings/partner', name: 'app_settings_partner')]
    public function index(): Response
    {
        return $this->render('settings_partner/index.html.twig', [
            'controller_name' => 'SettingsPartnerController',
        ]);
    }
}
