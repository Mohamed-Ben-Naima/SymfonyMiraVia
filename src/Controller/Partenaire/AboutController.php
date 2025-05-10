<?php

namespace App\Controller\Partenaire;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AboutController extends AbstractController
{
    #[Route('/dashboard/partenaire/about', name: 'app_partenaire_about')]
    public function index(): Response
    {
        return $this->render('partenaire/about/index.html.twig', [
        ]);
    }
}
