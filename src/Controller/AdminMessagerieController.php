<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminMessagerieController extends AbstractController
{
    #[Route('/admin/messagerie', name: 'app_admin_messaging')]
    public function index(): Response
    {
        return $this->render('admin_messagerie/index.html.twig', [
            'controller_name' => 'AdminMessagerieController',
        ]);
    }
}
