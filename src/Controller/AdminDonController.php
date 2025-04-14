<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminDonController extends AbstractController
{
    #[Route('/admin/don', name: 'app_admin_donations')]
    public function index(): Response
    {
        return $this->render('admin_don/index.html.twig', [
            'controller_name' => 'AdminDonController',
        ]);
    }
}
