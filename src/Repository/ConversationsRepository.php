<?php

namespace App\Repository;

use App\Entity\Conversations;
use App\Entity\Utilisateurs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/**
 * @extends ServiceEntityRepository<Conversations>
 *
 * @method Conversations|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conversations|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conversations[]    findAll()
 * @method Conversations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConversationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversations::class);
    }
public function findByUser(Utilisateurs $user)
{
    return $this->createQueryBuilder('c')
        ->where('c.idUser1 = :userId OR c.idUser2 = :userId')
        ->setParameter('userId', $user->getIdUtilisateur())
        ->getQuery()
        ->getResult();
}
}
