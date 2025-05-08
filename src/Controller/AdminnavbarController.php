<?php

namespace App\Controller;

use App\Entity\Notifications;
use App\Entity\Utilisateurs;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminnavbarController extends AbstractController
{
    private $logger;
    private $entityManager;

    public function __construct(LoggerInterface $logger, EntityManagerInterface $entityManager)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }

    #[Route('/_fragment/navbar', name: 'admin_navbar_fragment', methods: ['GET'])]
    public function navbar(SessionInterface $session): Response
    {
        try {
            // Get the admin ID from the session
            $admin = $session->get('admin');
            $adminId = $admin['id'] ?? null;
            $this->logger->info('Navbar: Fetching admin ID from session', ['adminId' => $adminId]);

            if (!$adminId || !is_int($adminId)) {
                $this->logger->warning('Navbar: No valid admin ID in session', ['admin' => $admin]);
                $this->addFlash('error', 'Vous devez être connecté.');
                return $this->redirectToRoute('app_admin_login');
            }

            // Fetch the logged-in user
            $user = $this->entityManager->find(Utilisateurs::class, $adminId);
            if (!$user) {
                $this->logger->error('Navbar: User not found for admin ID', ['adminId' => $adminId]);
                $this->addFlash('error', 'Utilisateur non trouvé.');
                return $this->redirectToRoute('app_admin_login');
            }

            // Query to retrieve notifications for the connected admin
            $notifications = $this->entityManager->createQueryBuilder()
                ->select('n')
                ->from(Notifications::class, 'n')
                ->where('n.idUtilisateur = :adminId')
                ->setParameter('adminId', $adminId)
                ->orderBy('n.dateEnvoi', 'DESC')
                ->getQuery()
                ->getResult();

            $this->logger->info('Navbar: Fetched notifications', ['count' => count($notifications), 'adminId' => $adminId]);

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
        } catch (\Exception $e) {
            $this->logger->error('Navbar: Error rendering navbar', [
                'error' => $e->getMessage(),
                'adminId' => $adminId ?? 'none',
                'trace' => $e->getTraceAsString(),
            ]);
            $this->addFlash('error', 'Une erreur est survenue lors du chargement de la barre de navigation.');
            return $this->redirectToRoute('app_admin_login');
        }
    }

    #[Route('/_fragment/notification-count', name: 'admin_notification_count', methods: ['GET'])]
    public function notificationCount(SessionInterface $session): JsonResponse
    {
        try {
            // Get the admin ID from the session
            $admin = $session->get('admin');
            $adminId = $admin['id'] ?? null;
            $this->logger->info('Notification count: Fetching admin ID from session', ['adminId' => $adminId]);

            if (!$adminId || !is_int($adminId)) {
                $this->logger->warning('Notification count: No valid admin ID in session', ['admin' => $admin]);
                return new JsonResponse(['count' => 0], 200);
            }

            // Verify user exists
            $user = $this->entityManager->find(Utilisateurs::class, $adminId);
            if (!$user) {
                $this->logger->error('Notification count: User not found for admin ID', ['adminId' => $adminId]);
                return new JsonResponse(['count' => 0], 200);
            }

            // Query to count unread notifications for the admin
            $count = $this->entityManager->createQueryBuilder()
                ->select('COUNT(n.id)')
                ->from(Notifications::class, 'n')
                ->where('n.idUtilisateur = :adminId')
                ->andWhere('n.is_read = :isRead')
                ->setParameter('adminId', $adminId)
                ->setParameter('isRead', 0)
                ->getQuery()
                ->getSingleScalarResult();

            $this->logger->info('Notification count: Fetched unread count', ['count' => $count, 'adminId' => $adminId]);

            return new JsonResponse(['count' => (int) $count], 200);
        } catch (\Exception $e) {
            $this->logger->error('Notification count: Error fetching count', [
                'error' => $e->getMessage(),
                'adminId' => $adminId ?? 'none',
                'trace' => $e->getTraceAsString(),
            ]);
            return new JsonResponse(['count' => 0, 'error' => 'Unable to fetch notification count'], 200);
        }
    }

    #[Route('/_fragment/mark-notifications-read', name: 'admin_mark_notifications_read', methods: ['POST'])]
    public function markNotificationsRead(SessionInterface $session): JsonResponse
    {
        try {
            // Get the admin ID from the session
            $admin = $session->get('admin');
            $adminId = $admin['id'] ?? null;
            $this->logger->info('Mark notifications read: Fetching admin ID from session', ['adminId' => $adminId]);

            if (!$adminId || !is_int($adminId)) {
                $this->logger->warning('Mark notifications read: No valid admin ID in session', ['admin' => $admin]);
                return new JsonResponse(['success' => false, 'message' => 'No valid session'], 401);
            }

            // Verify user exists
            $user = $this->entityManager->find(Utilisateurs::class, $adminId);
            if (!$user) {
                $this->logger->error('Mark notifications read: User not found for admin ID', ['adminId' => $adminId]);
                return new JsonResponse(['success' => false, 'message' => 'User not found'], 404);
            }

            // Update all notifications for the user to is_read = 1
            $this->entityManager->createQueryBuilder()
                ->update(Notifications::class, 'n')
                ->set('n.is_read', ':isRead')
                ->where('n.idUtilisateur = :adminId')
                ->setParameter('isRead', 1)
                ->setParameter('adminId', $adminId)
                ->getQuery()
                ->execute();

            $this->logger->info('Mark notifications read: Updated notifications to read', ['adminId' => $adminId]);

            return new JsonResponse(['success' => true, 'message' => 'Notifications marked as read'], 200);
        } catch (\Exception $e) {
            $this->logger->error('Mark notifications read: Error updating notifications', [
                'error' => $e->getMessage(),
                'adminId' => $adminId ?? 'none',
                'trace' => $e->getTraceAsString(),
            ]);
            return new JsonResponse(['success' => false, 'message' => 'Unable to mark notifications as read'], 200);
        }
    }

    #[Route('/_fragment/user-data-fetch', name: 'admin_user_data_fetch', methods: ['GET'])]
    public function getUserData(SessionInterface $session): JsonResponse
    {
        try {
            // Get the admin ID from the session
            $admin = $session->get('admin');
            $adminId = $admin['id'] ?? null;
            $this->logger->info('getUserData: Fetching admin ID from session', ['adminId' => $adminId]);

            if (!$adminId || !is_int($adminId)) {
                $this->logger->warning('getUserData: No valid admin ID in session', ['admin' => $admin]);
                return new JsonResponse(['nom' => '', 'prenom' => ''], 401);
            }

            // Fetch the user
            $user = $this->entityManager->find(Utilisateurs::class, $adminId);
            if (!$user) {
                $this->logger->error('getUserData: User not found for admin ID', ['adminId' => $adminId]);
                return new JsonResponse(['nom' => '', 'prenom' => ''], 404);
            }

            $this->logger->info('getUserData: User found', [
                'adminId' => $adminId,
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
            ]);

            return new JsonResponse([
                'nom' => $user->getNom() ?? '',
                'prenom' => $user->getPrenom() ?? '',
            ], 200);
        } catch (\Exception $e) {
            $this->logger->error('getUserData: Error fetching user data', [
                'error' => $e->getMessage(),
                'adminId' => $adminId ?? 'none',
                'trace' => $e->getTraceAsString(),
            ]);
            return new JsonResponse(['nom' => '', 'prenom' => '', 'error' => 'Unable to fetch user data'], 200);
        }
    }
}