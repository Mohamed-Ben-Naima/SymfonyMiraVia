<?php

namespace App\Controller;

use App\Entity\Benevolat;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BenevolatController extends AbstractController
{
    #[Route('/benevolat', name: 'app_benevolat')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        // Get all filter parameters
        $filters = [
            'status' => $request->query->get('status'),
            'lieu' => $request->query->get('lieu'),
            'date_from' => $request->query->get('date_from'),
            'date_to' => $request->query->get('date_to'),
            'search' => $request->query->get('search')
        ];

        $query = $em->createQueryBuilder()
            ->select('b')
            ->from(Benevolat::class, 'b');

        // Apply filters
        if ($filters['status']) {
            $query->andWhere('b.status = :status')
                 ->setParameter('status', $filters['status']);
        }

        if ($filters['lieu']) {
            $query->andWhere('LOWER(b.lieu) LIKE :lieu')
                 ->setParameter('lieu', '%'.strtolower($filters['lieu']).'%');
        }

        // Date filtering - using the correct property name dateBenevolat
        if ($filters['date_from'] && $filters['date_to']) {
            $query->andWhere('b.dateBenevolat BETWEEN :from AND :to')
                 ->setParameter('from', new \DateTime($filters['date_from']))
                 ->setParameter('to', new \DateTime($filters['date_to']));
        } elseif ($filters['date_from']) {
            $query->andWhere('b.dateBenevolat >= :from')
                 ->setParameter('from', new \DateTime($filters['date_from']));
        } elseif ($filters['date_to']) {
            $query->andWhere('b.dateBenevolat <= :to')
                 ->setParameter('to', new \DateTime($filters['date_to']));
        }

        if ($filters['search']) {
            $query->andWhere('LOWER(b.titre) LIKE :search OR LOWER(b.description) LIKE :search')
                 ->setParameter('search', '%'.strtolower($filters['search']).'%');
        }

        // Order by date (newest first)
        $query->orderBy('b.dateBenevolat', 'DESC');

        $benevolats = $query->getQuery()->getResult();

        return $this->render('benevolat/index.html.twig', [
            'benevolats' => $benevolats,
            'filters' => $filters
        ]);
    }
}