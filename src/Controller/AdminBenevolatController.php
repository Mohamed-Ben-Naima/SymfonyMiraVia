<?php

namespace App\Controller;

use App\Entity\Benevolat;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

final class AdminBenevolatController extends AbstractController
{
    #[Route('/admin/benevolat', name: 'app_admin_volunteering')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupérer toutes les données de l'entité Benevolat
        $benevolats = $entityManager->getRepository(Benevolat::class)->findAll();

        return $this->render('admin_benevolat/index.html.twig', [
            'controller_name' => 'AdminBenevolatController',
            'pageTitle' => 'Bénévolat',
            'benevolats' => $benevolats, // Passer les données à la vue
        ]);
    }
}