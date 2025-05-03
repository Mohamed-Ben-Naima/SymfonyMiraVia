<?php

namespace App\Controller;

use App\Entity\Benevolat;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BenevolatController extends AbstractController
{
    #[Route('/benevolat', name: 'app_benevolat')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        // Get all filter parameters
        $filters = [
            'status' => $request->query->get('status'),
            'theme' => $request->query->get('theme'),
            'duree' => $request->query->get('duree'),
            'destination' => $request->query->get('destination'),
            'date_from' => $request->query->get('date_from'),
            'date_to' => $request->query->get('date_to')
        ];

        $query = $entityManager->getRepository(Benevolat::class)->createQueryBuilder('b');

        // Apply filters
        if ($filters['status']) {
            $query->andWhere('b.status = :status')->setParameter('status', $filters['status']);
        }

        if ($filters['date_from'] && $filters['date_to']) {
            $query->andWhere('b.date_benevolat BETWEEN :from AND :to')
                ->setParameter('from', new \DateTime($filters['date_from']))
                ->setParameter('to', new \DateTime($filters['date_to']));
        }

        $benevolats = $query->getQuery()->getResult();

        return $this->render('benevolat/index.html.twig', [
            'benevolats' => $benevolats,
            'filters' => $filters
        ]);
    }
}
