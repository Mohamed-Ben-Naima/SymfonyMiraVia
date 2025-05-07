<?php

namespace App\Repository;

use App\Entity\Utilisateurs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Utilisateurs>
 *
 * @method Utilisateurs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Utilisateurs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Utilisateurs[]    findAll()
 * @method Utilisateurs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtilisateursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateurs::class);
    }
    public function findAllExcept(Utilisateurs $user): array
{
    return $this->createQueryBuilder('u')
        ->where('u.idUtilisateur != :id')
        ->setParameter('id', $user->getIdUtilisateur())
        ->getQuery()
        ->getResult();
}

public function findContactsExcept(Utilisateurs $user, string $searchTerm = '') {
    $qb = $this->createQueryBuilder('u')
        ->where('u.idUtilisateur != :userId')
        ->setParameter('userId', $user->getIdUtilisateur());

    if ($searchTerm) {
        $qb->andWhere('u.nom LIKE :searchTerm OR u.prenom LIKE :searchTerm')
           ->setParameter('searchTerm', '%' . $searchTerm . '%');
    }

    return $qb->getQuery()->getResult();
}

    public function findAllAdmins(): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.typeUtilisateur = :type')
            ->setParameter('type', 'admin')
            ->getQuery()
            ->getResult();
    }
    

    public function findAllUsers(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT id_utilisateur, nom
            FROM utilisateurs
        ';

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();

        return $result->fetchAllAssociative();
    }
    // Dans UtilisateursRepository.php
public function findAllClients(): array
{
    $conn = $this->getEntityManager()->getConnection();

    $sql = '
        SELECT u.* 
        FROM utilisateurs u
        JOIN clients c ON u.id_utilisateur = id_client ;
    ';
    
    $stmt = $conn->prepare($sql);
    $result = $stmt->executeQuery();

    return $result->fetchAllAssociative();
}
}
