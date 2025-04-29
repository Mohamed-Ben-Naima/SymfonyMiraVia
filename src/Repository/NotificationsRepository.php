<?php
// src/Repository/NotificationsRepository.php

namespace App\Repository;

use App\Entity\Notifications;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class NotificationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notifications::class);
    }

    /**
     * Supprime toutes les notifications d'un utilisateur donné.
     * 
     * @param int $userId L'ID de l'utilisateur dont on souhaite supprimer les notifications
     * 
     * @return int Le nombre de notifications supprimées
     */
    public function deleteAllForUser(int $userId): int
    {
        return $this->createQueryBuilder('n')
            ->delete()
            ->where('n.idUtilisateur = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->execute();
    }

    /**
     * Récupérer les notifications d'un utilisateur, triées par date d'envoi.
     * 
     * @param int $userId L'ID de l'utilisateur
     * @param int $limit Limite du nombre de notifications à récupérer
     * 
     * @return Notifications[] Liste des notifications
     */
    public function findNotificationsByUser(int $userId, int $limit = 10)
    {
        return $this->createQueryBuilder('n')
            ->where('n.idUtilisateur = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('n.dateEnvoi', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Ajouter une nouvelle notification pour un utilisateur donné.
     * 
     * @param Notifications $notification L'objet notification à ajouter
     * @return void
     */
    public function addNotification(Notifications $notification): void
    {
        $this->_em->persist($notification);
        $this->_em->flush();
    }
}
