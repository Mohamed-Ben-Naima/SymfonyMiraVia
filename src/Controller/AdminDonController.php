<?php

namespace App\Controller;

use App\Entity\Dons;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminDonController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    // Injecter l'EntityManager via le constructeur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin/don', name: 'app_admin_donations')]
    public function index(): Response
    {
        // Récupérer les dons depuis la base de données
        $dons = $this->entityManager->getRepository(Dons::class)->findAll();

        return $this->render('admin_don/index.html.twig', [
            'controller_name' => 'AdminDonController',
            'pageTitle' => 'Don',
            'dons' => $dons, // Passer les dons à la vue
        ]);
    }
}