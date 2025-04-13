<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SignUpControllerPartnerController extends AbstractController
{
    #[Route('/sign-up/partner', name: 'app_sign_up_controller_partner')]
    public function index(): Response
    {
        return $this->render('sign_up_controller_partner/index.html.twig', [
            'controller_name' => 'SignUpControllerPartnerController',
        ]);
    }
}
