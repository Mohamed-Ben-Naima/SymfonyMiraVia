<?php

namespace App\Controller\Partenaire;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EventPartnerController extends AbstractController
{
    #[Route('/event/partner', name: 'app_event_partner')]
    public function index(): Response
    {
        return $this->render('Partenaire/event_partner/index.html.twig', [
            'controller_name' => 'EventPartnerController',
        ]);
    }
}
