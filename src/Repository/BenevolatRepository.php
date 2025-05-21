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
// src/Repository/BenevolatRepository.php

public function countByStatuses(array $statuses): int
{
    return (int) $this->createQueryBuilder('b')
        ->select('COUNT(b.idBenevolat)')
        ->where('b.status IN (:statuses)')
        ->setParameter('statuses', $statuses)
        ->getQuery()
        ->getSingleScalarResult();}

    public function findByPartnerId(int $idPartenaire)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.idPartenaire = :idPartenaire')
            ->setParameter('idPartenaire', $idPartenaire)
            ->getQuery()
            ->getResult();
    }
}
