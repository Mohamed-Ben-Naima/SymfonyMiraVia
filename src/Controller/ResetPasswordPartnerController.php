<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ResetPasswordPartnerController extends AbstractController
{
    #[Route('/reset/password/partner', name: 'app_reset_password_partner')]
    public function index(): Response
    {
        return $this->render('reset_password_partner/index.html.twig', [
            'controller_name' => 'ResetPasswordPartnerController',
        ]);
    }
}
