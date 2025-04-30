<?php

namespace App\Repository;

use App\Entity\Evenements;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EvenementsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evenements::class);
    }

    public function findByPartnerId(int $idPartenaire)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.idPartenaire = :idPartenaire')
            ->setParameter('idPartenaire', $idPartenaire)
            ->getQuery()
            ->getResult();
    }
}