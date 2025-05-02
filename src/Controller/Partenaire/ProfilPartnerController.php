<?php

namespace App\Controller\Partenaire;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfilPartnerController extends AbstractController
{
    #[Route('dashboard/partenaire/paramètres/profil', name: 'app_profil_partner')]
    public function index(): Response
    {
        return $this->render('/Partenaire/ProfilePartner/profile-partner.html.twig', [
            'controller_name' => 'ProfilPartnerController',
        ]);
    }
}
