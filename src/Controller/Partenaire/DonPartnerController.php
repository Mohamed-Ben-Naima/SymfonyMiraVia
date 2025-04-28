<?php

namespace App\Controller\Partenaire;

use App\Repository\DonsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DonPartnerController extends AbstractController
{
    #[Route('/don/partner', name: 'app_don_partner')]
    public function index(DonsRepository $donsRepository): Response
    {
        // Fetch donations for id_partenaire = 1
        $dons = $donsRepository->findByPartenaire(1);

        return $this->render('Partenaire/don_partner/don.html.twig', [
            'dons' => $dons,
        ]);
    }
}