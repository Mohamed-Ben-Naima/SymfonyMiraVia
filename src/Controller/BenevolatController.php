<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BenevolatController extends AbstractController
{
    #[Route('/benevolat', name: 'app_benevolat')]
    public function index(): Response
    {
        return $this->render('benevolat/index.html.twig', [
            'controller_name' => 'BenevolatController',
        ]);
    }
}
