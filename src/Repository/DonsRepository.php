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
// src/Repository/DonRepository.php

public function getTotalMontant(): float
{
    return (float) $this->createQueryBuilder('d')
        ->select('SUM(d.montant)')
        ->getQuery()
        ->getSingleScalarResult();
}

    public function getDonationsByYear(int $idPartenaire): array
{
    $conn = $this->getEntityManager()->getConnection();

    $sql = '
        SELECT YEAR(d.date_don) as year, SUM(d.montant) as total
        FROM dons d
        WHERE d.id_partenaire = :idPartenaire
        GROUP BY YEAR(d.date_don)
        ORDER BY year DESC
    ';

    $stmt = $conn->prepare($sql);
    $result = $stmt->executeQuery(['idPartenaire' => $idPartenaire]);

    return array_column($result->fetchAllAssociative(), 'total', 'year');
}

    public function getAvailableYears(int $idPartenaire): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT DISTINCT YEAR(d.date_don) as year
            FROM dons d
            WHERE d.id_partenaire = :idPartenaire
            ORDER BY year DESC
        ';

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery(['idPartenaire' => $idPartenaire]);

        return array_column($result->fetchAllAssociative(), 'year');
    }



    
        public function getTotalRejectedDonations(int $idPartenaire): float
        {
            $conn = $this->getEntityManager()->getConnection();

            $sql = '
                SELECT SUM(d.montant) as total
                FROM dons d
                WHERE d.id_partenaire = :idPartenaire AND d.status = :status
            ';

            $stmt = $conn->prepare($sql);
            $result = $stmt->executeQuery([
                'idPartenaire' => $idPartenaire,
                'status' => 'Annulé',
            ]);

            return (float) $result->fetchOne();
        }

    public function getTotalAcceptedDonations(int $idPartenaire): float
        {
            $conn = $this->getEntityManager()->getConnection();

            $sql = '
                SELECT SUM(d.montant) as total
                FROM dons d
                WHERE d.id_partenaire = :idPartenaire 
                AND d.status = :status 
                
            ';

            $stmt = $conn->prepare($sql);
            $result = $stmt->executeQuery([
                'idPartenaire' => $idPartenaire,
                'status' => 'Accepté',
                
            ]);

            return (float) $result->fetchOne();
        }

        public function getTotalPendingDonations(int $idPartenaire): float
        {
            $conn = $this->getEntityManager()->getConnection();

            $sql = '
                SELECT SUM(d.montant) as total
                FROM dons d
                WHERE d.id_partenaire = :idPartenaire AND d.status = :status
            ';

            $stmt = $conn->prepare($sql);
            $result = $stmt->executeQuery([
                'idPartenaire' => $idPartenaire,
                'status' => 'En attente',
            ]);

            return (float) $result->fetchOne();
        }

        public function getTotalAcceptedDonationsByYear(int $idPartenaire, int $year): float
        {
            $conn = $this->getEntityManager()->getConnection();

            $sql = '
                SELECT SUM(d.montant) as total
                FROM dons d
                WHERE d.id_partenaire = :idPartenaire 
                AND d.status = :status 
                AND YEAR(d.date_don) = :year
            ';

            $stmt = $conn->prepare($sql);
            $result = $stmt->executeQuery([
                'idPartenaire' => $idPartenaire,
                'status' => 'Accepté',
                'year' => $year,
            ]);

            return (float) $result->fetchOne();
        }

        public function getTotalPendingDonationsByYear(int $idPartenaire, int $year): float
        {
            $conn = $this->getEntityManager()->getConnection();

            $sql = '
                SELECT SUM(d.montant) as total
                FROM dons d
                WHERE d.id_partenaire = :idPartenaire 
                AND d.status = :status 
                AND YEAR(d.date_don) = :year
            ';

            $stmt = $conn->prepare($sql);
            $result = $stmt->executeQuery([
                'idPartenaire' => $idPartenaire,
                'status' => 'En attente',
                'year' => $year,
            ]);

            return (float) $result->fetchOne();
        }

    
        public function getDonationsByMonth(int $idPartenaire): array
    {
        $conn = $this->getEntityManager()->getConnection();

        // Requête SQL corrigée
        $sql = '
            SELECT YEAR(d.date_don) as year, MONTH(d.date_don) as month, SUM(d.montant) as total
            FROM dons d
            WHERE d.id_partenaire = :idPartenaire
            GROUP BY year, month
            ORDER BY year ASC, month ASC
        ';

        // Préparation et exécution de la requête avec le paramètre correct
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery(['idPartenaire' => $idPartenaire]); // Assurez-vous que la clé correspond à :idPartenaire dans la requête

        return $result->fetchAllAssociative();
    }

    public function findByPartenaire(int $idPartenaire): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT 
                d.*, 
                u.nom AS client_nom,
                u.id_utilisateur AS client_id 
            FROM dons d
            JOIN utilisateurs u ON d.id_client = u.id_utilisateur
            WHERE d.id_partenaire = :id_partenaire ' ;
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery(['id_partenaire' => $idPartenaire]);

        return $result->fetchAllAssociative();
    }
    
    public function findDonsWithClientNames(int $idPartenaire): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT 
                d.id_don AS id_don, 
                d.id_client, 
                d.id_partenaire, 
                d.montant, 
                d.date_don, 
                d.status, 
                u.nom AS client_nom,
                u.id_utilisateur AS utilisateur_id
            FROM dons d
            JOIN clients c ON d.id_client = c.id_client
            JOIN utilisateurs u ON c.id_client = u.id_utilisateur
            WHERE d.id_partenaire = :id_partenaire
        ';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['id_partenaire' => $idPartenaire]);

        return $resultSet->fetchAllAssociative(); // Returns data as an associative array
    }
    
}
