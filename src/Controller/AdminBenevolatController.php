<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminBenevolatController extends AbstractController
{
    #[Route('/admin/benevolat', name: 'app_admin_volunteering')]
    public function index(): Response
    {
        return $this->render('admin_benevolat/index.html.twig', [
            'controller_name' => 'AdminBenevolatController',
        ]);
    }
}
