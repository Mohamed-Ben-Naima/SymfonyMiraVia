<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class NotificationsPartnerController extends AbstractController
{
    #[Route('/notifications/partner', name: 'app_notifications_partner')]
    public function index(): Response
    {
        return $this->render('notifications_partner/index.html.twig', [
            'controller_name' => 'NotificationsPartnerController',
        ]);
    }
}
