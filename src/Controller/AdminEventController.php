<?php

namespace App\Controller;

use App\Entity\Evenements;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminEventController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    // Inject EntityManagerInterface via the constructor
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin/event', name: 'app_admin_events')]
    public function index(): Response
    {
        // Use the injected EntityManager to fetch data
        $evenements = $this->entityManager->getRepository(Evenements::class)->findAll();

        return $this->render('admin_event/index.html.twig', [
            'controller_name' => 'AdminEventController',
            'pageTitle' => 'Evénement',
            'evenements' => $evenements, // Pass the events data to the template
        ]);
    }
}