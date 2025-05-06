<?php

namespace App\Controller\Partenaire;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SettingsPartnerController extends AbstractController
{
    #[Route('/dashboard/partenaire/paramètres', name: 'app_settings_partner')]
    public function index(): Response
    {
        return $this->render('Partenaire/settings_partner/settings.html.twig', [
            'controller_name' => 'SettingsPartnerController',
        ]);
    }
}
