<?php

namespace App\Controller;

use App\Entity\Notifications;
use App\Entity\Utilisateurs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class AdminnavbarController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/_fragment/navbar', name: 'admin_navbar_fragment')]
    public function navbar(EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        // Get the admin ID from the session
        $adminId = $session->get('admin')['id'] ?? null;
        $this->logger->info('Navbar: Admin ID from session', ['adminId' => $adminId]);

        if (!$adminId) {
            $this->logger->warning('Navbar: No admin ID in session');
            $this->addFlash('error', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_admin_login');
        }

        // Fetch the logged-in user
        $user = $entityManager->getRepository(Utilisateurs::class)->find($adminId);
        if (!$user) {
            $this->logger->error('Navbar: User not found for admin ID', ['adminId' => $adminId]);
            $this->addFlash('error', 'Utilisateur non trouvé.');
            return $this->redirectToRoute('app_admin_login');
        }

        // Query to retrieve notifications for the connected admin
        $notifications = $entityManager->createQueryBuilder()
            ->select('n')
            ->from(Notifications::class, 'n')
            ->where('n.idUtilisateur = :adminId')
            ->setParameter('adminId', $adminId)
            ->orderBy('n.dateEnvoi', 'DESC')
            ->getQuery()
            ->getResult();

        $formattedNotifications = array_map(function ($notification) {
            return [
                'type' => $notification->getTypeNotification(),
                'message' => $notification->getMessage(),
                'sender' => 'System',
                'time' => $notification->getDateEnvoi()->format('H:i'),
                'icon' => match ($notification->getTypeNotification()) {
                    'success' => 'bi-check-circle',
                    'warning' => 'bi-exclamation-circle',
                    'danger' => 'bi-x-circle',
                    default => 'bi-info-circle',
                },
            ];
        }, $notifications);

        return $this->render('partialAdmin/navbarAdmin.html.twig', [
            'notifications' => $formattedNotifications,
            'pageTitle' => 'Dashboard Admin',
            'user' => $user,
        ]);
    }

    #[Route('/_fragment/notification-count', name: 'admin_notification_count')]
    public function notificationCount(EntityManagerInterface $entityManager, SessionInterface $session): JsonResponse
    {
        $adminId = $session->get('admin')['id'] ?? null;
        $this->logger->info('Notification count: Admin ID from session', ['adminId' => $adminId]);

        if (!$adminId) {
            $this->logger->warning('Notification count: No admin ID in session');
            return new JsonResponse(['count' => 0]);
        }

        $count = $entityManager->createQueryBuilder()
            ->select('COUNT(n.id)')
            ->from(Notifications::class, 'n')
            ->where('n.idUtilisateur = :adminId')
            ->setParameter('adminId', $adminId)
            ->getQuery()
            ->getSingleScalarResult();

        return new JsonResponse(['count' => (int) $count]);
    }

    #[Route('/_fragment/user-data-fetch', name: 'admin_user_data_fetch')]
    public function getUserData(EntityManagerInterface $entityManager, SessionInterface $session): JsonResponse
    {
        $adminId = $session->get('admin')['id'] ?? null;
        $this->logger->info('getUserData: Admin ID from session', ['adminId' => $adminId]);

        if (!$adminId) {
            $this->logger->warning('getUserData: No admin ID in session');
            return new JsonResponse(['nom' => '', 'prenom' => ''], 401);
        }

        $user = $entityManager->getRepository(Utilisateurs::class)->find($adminId);
        if (!$user) {
            $this->logger->error('getUserData: User not found for admin ID', ['adminId' => $adminId]);
            return new JsonResponse(['nom' => '', 'prenom' => ''], 404);
        }

        $this->logger->info('getUserData: User found', [
            'adminId' => $adminId,
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom()
        ]);

        return new JsonResponse([
            'nom' => $user->getNom() ?? '',
            'prenom' => $user->getPrenom() ?? ''
        ]);
    }
}