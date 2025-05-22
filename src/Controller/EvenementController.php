<?php

namespace App\Controller;

use App\Entity\Evenements;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EvenementController extends AbstractController
{
    #[Route('/evenement', name: 'app_evenement')]
    public function index(
        EntityManagerInterface $em,
        Request $request
    ): Response {
        // Get filter parameters from request
        $searchTerm = $request->query->get('q');
        $location = $request->query->get('lieu');
        $dateFilter = $request->query->get('date');

        // Create query builder
        $qb = $em->createQueryBuilder();
        $qb->select('e')
            ->from(Evenements::class, 'e');

        // Apply filters
        if ($searchTerm) {
            $qb->andWhere('LOWER(e.titre) LIKE :search OR LOWER(e.description) LIKE :search')
               ->setParameter('search', '%'.strtolower($searchTerm).'%');
        }

        if ($location) {
            $qb->andWhere('LOWER(e.lieu) LIKE :location')
               ->setParameter('location', '%'.strtolower($location).'%');
        }

        // Date filtering
        $now = new \DateTime();
        if ($dateFilter) {
            switch ($dateFilter) {
                case 'today':
                    $start = clone $now;
                    $end = clone $now;
                    $qb->andWhere('e.dateEvenement BETWEEN :start AND :end')
                       ->setParameter('start', $start->format('Y-m-d 00:00:00'))
                       ->setParameter('end', $end->format('Y-m-d 23:59:59'));
                    break;
                case 'week':
                    $start = clone $now;
                    $end = clone $now;
                    $start->modify('this week');
                    $end->modify('this week +6 days');
                    $qb->andWhere('e.dateEvenement BETWEEN :start AND :end')
                       ->setParameter('start', $start->format('Y-m-d 00:00:00'))
                       ->setParameter('end', $end->format('Y-m-d 23:59:59'));
                    break;
                case 'month':
                    $start = clone $now;
                    $end = clone $now;
                    $start->modify('first day of this month');
                    $end->modify('last day of this month');
                    $qb->andWhere('e.dateEvenement BETWEEN :start AND :end')
                       ->setParameter('start', $start->format('Y-m-d 00:00:00'))
                       ->setParameter('end', $end->format('Y-m-d 23:59:59'));
                    break;
                case 'future':
                    $qb->andWhere('e.dateEvenement >= :now')
                       ->setParameter('now', $now->format('Y-m-d H:i:s'));
                    break;
                case 'past':
                    $qb->andWhere('e.dateEvenement < :now')
                       ->setParameter('now', $now->format('Y-m-d H:i:s'));
                    break;
            }
        }

        // Order by date (future events first)
        $qb->addOrderBy('CASE WHEN e.dateEvenement >= :now THEN 0 ELSE 1 END', 'ASC')
           ->addOrderBy('e.dateEvenement', 'ASC')
           ->setParameter('now', $now->format('Y-m-d H:i:s'));

        // Get all results
        $evenements = $qb->getQuery()->getResult();

        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenements,
            'searchTerm' => $searchTerm,
            'location' => $location,
            'dateFilter' => $dateFilter,
        ]);
    }
}