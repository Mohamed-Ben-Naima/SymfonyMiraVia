<?php

namespace App\Repository;

use App\Entity\Benevolat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Benevolat>
 *
 * @method Benevolat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Benevolat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Benevolat[]    findAll()
 * @method Benevolat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BenevolatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Benevolat::class);
    }

//    /**
//     * @return Benevolat[] Returns an array of Benevolat objects
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

//    public function findOneBySomeField($value): ?Benevolat
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
