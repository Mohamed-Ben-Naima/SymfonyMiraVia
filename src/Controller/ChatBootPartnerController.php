<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ChatBootPartnerController extends AbstractController
{
    #[Route('/chat/boot/partner', name: 'app_chat_boot_partner')]
    public function index(): Response
    {
        return $this->render('chat_boot_partner/index.html.twig', [
            'controller_name' => 'ChatBootPartnerController',
        ]);
    }
}
