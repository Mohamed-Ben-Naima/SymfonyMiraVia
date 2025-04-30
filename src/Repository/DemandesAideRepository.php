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

    /**
     * Find all demandes by partenaire ID.
     *
     * @param int $idPartenaire
     * @return array
     */
    public function findByPartenaire(int $idPartenaire): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT 
                da.*, 
                u.nom AS client_nom, -- Récupère le nom du client
                u.id_utilisateur AS client_id 
            FROM demandes_aide da
            JOIN utilisateurs u ON da.id_client = u.id_utilisateur
            WHERE da.id_partenaire = :id_partenaire ' ;
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery(['id_partenaire' => $idPartenaire]);

        return $result->fetchAllAssociative();
    }

    /**
     * Count the number of demandes by partenaire ID.
     *
     * @param int $idPartenaire
     * @return int
     */
    public function countDemandesByPartenaire(int $idPartenaire): int
    {
        return $this->createQueryBuilder('d')
            ->select('COUNT(d.id)')
            ->andWhere('d.idPartenaire = :idPartenaire')
            ->setParameter('idPartenaire', $idPartenaire)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Add a new demande to the database.
     *
     * @param DemandesAide $demande
     * @param bool $flush
     */
    public function addDemande(DemandesAide $demande, bool $flush = true): void
    {
        $this->_em->persist($demande);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Update an existing demande in the database.
     *
     * @param DemandesAide $demande
     * @param bool $flush
     */
    public function updateDemande(DemandesAide $demande, bool $flush = true): void
    {
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Delete a demande from the database.
     *
     * @param DemandesAide $demande
     * @param bool $flush
     */
    public function deleteDemande(DemandesAide $demande, bool $flush = true): void
    {
        $this->_em->remove($demande);
        if ($flush) {
            $this->_em->flush();
        }
    }
}