<?php

namespace App\Controller;

use App\Entity\Notifications;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminNotificationController extends AbstractController
{
    #[Route('/admin/notification', name: 'app_admin_notification')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Fetch notifications for admin users directly in the controller
        $notifications = $entityManager->createQueryBuilder()
            ->select('n, u.nom, u.prenom')
            ->from(Notifications::class, 'n')
            ->leftJoin('App\Entity\Utilisateurs', 'u', 'WITH', 'u.idUtilisateur = n.idUtilisateur')
            ->where('u.typeUtilisateur = :type')
            ->setParameter('type', 'Admin')
            ->orderBy('n.dateEnvoi', 'DESC')
            ->getQuery()
            ->getResult();

        // Format notifications for the template
        $formattedNotifications = array_map(function ($notification) {
            $iconMap = [
                'success' => 'bi-check-circle',
                'warning' => 'bi-exclamation-circle',
                'danger' => 'bi-x-circle',
            ];

            // Safely handle nom and prenom
            $nom = $notification['nom'] ?? 'Inconnu';
            $prenom = $notification['prenom'] ?? '';
            $sender = trim($prenom . ' ' . $nom);

            return [
                'icon' => $iconMap[$notification[0]->getTypeNotification()] ?? 'bi-info-circle',
                'message' => $notification[0]->getMessage() ?? 'Aucun message',
                'sender' => $sender ?: 'Système',
                'time' => $notification[0]->getDateEnvoi() ? $notification[0]->getDateEnvoi()->format('h:i A') : 'Inconnu',
            ];
        }, $notifications);

        return $this->render('admin_notification/index.html.twig', [
            'controller_name' => 'AdminNotificationController',
            'notifications' => $formattedNotifications,
        ]);
    }
}