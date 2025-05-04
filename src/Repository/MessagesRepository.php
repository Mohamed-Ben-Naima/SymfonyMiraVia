<?php

namespace App\Repository;

use App\Entity\Messages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Messages>
 *
 * @method Messages|null find($id, $lockMode = null, $lockVersion = null)
 * @method Messages|null findOneBy(array $criteria, array $orderBy = null)
 * @method Messages[]    findAll()
 * @method Messages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Messages::class);
    }

// src/Repository/MessagesRepository.php

public function findMessagesBetween($user1, $user2): array
{
    return $this->createQueryBuilder('m')
        ->where('(m.expediteur = :user1 AND m.destinataire = :user2)')
        ->orWhere('(m.expediteur = :user2 AND m.destinataire = :user1)')
        ->setParameter('user1', $user1)
        ->setParameter('user2', $user2)
        ->orderBy('m.dateEnvoi', 'ASC')
        ->getQuery()
        ->getResult();
}
public function getMessages(EntityManagerInterface $em)
{
    $messages = $em->getRepository(Messages::class)
        ->createQueryBuilder('m')
        ->leftJoin('m.expediteur', 'e')
        ->leftJoin('m.destinataire', 'd')
        ->addSelect('e', 'd')
        ->getQuery()
        ->getResult();

    return $this->render('messagerie/chat.html.twig', [
        'messages' => $messages,
    ]);
}
}
