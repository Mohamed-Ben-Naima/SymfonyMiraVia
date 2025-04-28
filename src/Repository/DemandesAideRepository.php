<?php

namespace App\Repository;

use App\Entity\DemandesAide;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DemandesAideRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandesAide::class);
    }

    // Create (Add a new demande)
    public function addDemande(DemandesAide $demande, bool $flush = true): void
    {
        $this->_em->persist($demande);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // Read (Find all demandes for a specific partenaire)
    public function findByPartenaire(int $idPartenaire): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.idPartenaire = :id')
            ->setParameter('id', $idPartenaire)
            ->getQuery()
            ->getResult();
    }

    // Update (Save changes to an existing demande)
    public function updateDemande(DemandesAide $demande, bool $flush = true): void
    {
        if ($flush) {
            $this->_em->flush();
        }
    }

    // Delete (Remove a demande)
    public function deleteDemande(DemandesAide $demande, bool $flush = true): void
    {
        $this->_em->remove($demande);
        if ($flush) {
            $this->_em->flush();
        }
    }
}