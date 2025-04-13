<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminParameterController extends AbstractController
{
    #[Route('/admin/parameter', name: 'app_admin_settings')]
    public function index(): Response
    {
        return $this->render('admin_parameter/index.html.twig', [
            'controller_name' => 'AdminParameterController',
        ]);
    }
}
