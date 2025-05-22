<?php

namespace App\Controller;

use App\Entity\DemandesAide;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DemandeAideController extends AbstractController
{
    #[Route('/demande/aide', name: 'app_demande_aide')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        // Get all filter parameters with defaults
        $filters = [
            'status' => $request->query->get('status', 'acceptée'),
            'type' => $request->query->get('type'),
            'keyword' => $request->query->get('q'),
            'min_amount' => $request->query->get('min_amount'),
            'max_amount' => $request->query->get('max_amount'),
            'date_from' => $request->query->get('date_from'),
            'date_to' => $request->query->get('date_to'),
            'sort' => $request->query->get('sort', 'date_desc')
        ];

        $qb = $entityManager->getRepository(DemandesAide::class)->createQueryBuilder('d');

        // Apply filters
        if ($filters['status']) {
            $qb->andWhere('d.status = :status')
               ->setParameter('status', $filters['status']);
        }

        if ($filters['type']) {
            $qb->andWhere('d.typeAide = :type')
               ->setParameter('type', $filters['type']);
        }

        if ($filters['keyword']) {
            $qb->andWhere('LOWER(d.description) LIKE :keyword OR LOWER(d.typeAide) LIKE :keyword')
               ->setParameter('keyword', '%' . strtolower($filters['keyword']) . '%');
        }

        // Amount range filter
        if ($filters['min_amount']) {
            $qb->andWhere('d.montantDemande >= :min_amount')
               ->setParameter('min_amount', $filters['min_amount']);
        }

        if ($filters['max_amount']) {
            $qb->andWhere('d.montantDemande <= :max_amount')
               ->setParameter('max_amount', $filters['max_amount']);
        }

        // Date range filter
        if ($filters['date_from'] && $filters['date_to']) {
            $qb->andWhere('d.dateDemande BETWEEN :from AND :to')
               ->setParameter('from', new \DateTime($filters['date_from']))
               ->setParameter('to', new \DateTime($filters['date_to']));
        } elseif ($filters['date_from']) {
            $qb->andWhere('d.dateDemande >= :from')
               ->setParameter('from', new \DateTime($filters['date_from']));
        } elseif ($filters['date_to']) {
            $qb->andWhere('d.dateDemande <= :to')
               ->setParameter('to', new \DateTime($filters['date_to']));
        }

        // Sorting
        switch ($filters['sort']) {
            case 'date_asc':
                $qb->orderBy('d.dateDemande', 'ASC');
                break;
            case 'amount_desc':
                $qb->orderBy('d.montantDemande', 'DESC');
                break;
            case 'amount_asc':
                $qb->orderBy('d.montantDemande', 'ASC');
                break;
            default: // date_desc
                $qb->orderBy('d.dateDemande', 'DESC');
        }

        $demandes = $qb->getQuery()->getResult();

        // Get unique types and statuses for filter dropdowns
        $types = $entityManager->getRepository(DemandesAide::class)
            ->createQueryBuilder('d')
            ->select('DISTINCT d.typeAide')
            ->where('d.typeAide IS NOT NULL')
            ->orderBy('d.typeAide', 'ASC')
            ->getQuery()
            ->getResult();

        $statuses = $entityManager->getRepository(DemandesAide::class)
            ->createQueryBuilder('d')
            ->select('DISTINCT d.status')
            ->where('d.status IS NOT NULL')
            ->orderBy('d.status', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('demande_aide/index.html.twig', [
            'demandes' => $demandes,
            'filters' => $filters,
            'types' => array_column($types, 'typeAide'),
            'statuses' => array_column($statuses, 'status')
        ]);
    }
}