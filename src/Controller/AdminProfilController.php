<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminProfilController extends AbstractController{
    #[Route('/admin/profil', name: 'app_admin_profil')]
    public function index(): Response
    {
        return $this->render('admin_profil/index.html.twig', [
            'controller_name' => 'AdminProfilController',
        ]);
    }
}
