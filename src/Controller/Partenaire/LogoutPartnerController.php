<?php

namespace App\Controller\Partenaire;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LogoutPartnerController extends AbstractController
{
    #[Route('/logout/partner', name: 'app_logout_partner')]
    public function logout(Request $request): Response
    {
        // Invalider la session
        $session = $request->getSession();
        if ($session) {
            $session->invalidate();
        }

        // Supprimer le cookie de session
        $response = $this->redirectToRoute('app_home');
        $response->headers->clearCookie(session_name());

        return $response;
    }
}