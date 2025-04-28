<?php

namespace App\Repository;

use App\Entity\Dons;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Dons>
 *
 * @method Dons|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dons|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dons[]    findAll()
 * @method Dons[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DonsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dons::class);
    }

    public function findByPartenaire(int $idPartenaire): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.idPartenaire = :idPartenaire')
            ->setParameter('idPartenaire', $idPartenaire)
            ->getQuery()
            ->getResult();
    }
}
