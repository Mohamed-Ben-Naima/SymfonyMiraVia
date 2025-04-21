<?php

namespace App\Repository;

use App\Entity\DemandePartenaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DemandePartenaire>
 *
 * @method DemandePartenaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method DemandePartenaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method DemandePartenaire[]    findAll()
 * @method DemandePartenaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DemandePartenaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandePartenaire::class);
    }

//    /**
//     * @return DemandePartenaire[] Returns an array of DemandePartenaire objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DemandePartenaire
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
