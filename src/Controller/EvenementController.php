<?php

namespace App\Controller;

use App\Entity\Evenements;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EvenementController extends AbstractController
{
    #[Route('/evenement', name: 'app_evenement')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        $status = $request->query->get('status'); // Filter by status (e.g., ?status=actif)
        $query = $entityManager->getRepository(Evenements::class)->createQueryBuilder('e');

        if ($status) {
            $query->andWhere('e.status = :status')->setParameter('status', $status);
        }

        $evenements = $query->getQuery()->getResult();
        return $this->render('evenement/index.html.twig', [
            'controller_name' => 'EvenementController',
            'evenements' => $evenements,
        ]);
    }
}
