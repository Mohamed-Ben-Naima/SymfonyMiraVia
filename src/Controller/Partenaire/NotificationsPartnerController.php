<?php
/// src/Controller/Partenaire/NotificationsPartnerController.php

namespace App\Controller\Partenaire;

use App\Entity\Notifications;
use App\Repository\NotificationsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class NotificationsPartnerController extends AbstractController
{
    #[Route('/api/notifications', name: 'api_notifications', methods: ['GET'])]
    public function getNotifications(NotificationsRepository $notificationsRepository): JsonResponse
    {
        // Récupérer l'utilisateur connecté via la session
        $user = $this->getUser();

        // Vérification si l'utilisateur est authentifié
        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non connecté.'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Récupérer les notifications en fonction de l'utilisateur connecté
        $notifications = $notificationsRepository->findNotificationsByUser($user->getIdUtilisateur());

        // Si aucune notification n'est trouvée
        if (empty($notifications)) {
            return new JsonResponse(['message' => 'Aucune notification disponible.'], JsonResponse::HTTP_OK);
        }

        // Préparer les données des notifications
        $data = array_map(function (Notifications $notif) use ($user) {  // Passer $user à la fonction de mappage
            return [
                'id' => $notif->getIdNotification(),
                'message' => $notif->getMessage(),
                'type' => $notif->getTypeNotification(),
                'dateEnvoi' => $notif->getDateEnvoi()?->format('Y-m-d H:i:s'),
                'isRead' => $notif->getisRead() ? true : false,
                'auteur' => $user ? $user->getNom() : 'Système',  // Utilisation correcte de $user
            ];
        }, $notifications);

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    #[Route('/api/notifications/delete/{id}', name: 'delete_notification', methods: ['GET', 'DELETE'])]
    public function deleteNotification($id, NotificationsRepository $notificationRepository, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non connecté'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $notification = $notificationRepository->find($id);

        if (!$notification) {
            return new JsonResponse(['error' => 'Notification non trouvée'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Vérification que l'utilisateur est bien l'auteur de la notification
        if ($notification->getIdUtilisateur() !== $user->getidUtilisateur()) {
            return new JsonResponse(['error' => 'Vous ne pouvez pas supprimer cette notification'], JsonResponse::HTTP_FORBIDDEN);
        }

        // Supprimer la notification
        $em->remove($notification);
        $em->flush();

        return new JsonResponse(['success' => 'Notification supprimée avec succès'], JsonResponse::HTTP_OK);
    }

    #[Route('/api/notifications/clear', name: 'clear_notifications', methods: ['GET','DELETE'])]
    public function clearAllNotifications(NotificationsRepository $notificationRepository): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non connecté'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Récupérer l'ID de l'utilisateur connecté
        $userId = $user->getIdUtilisateur();

        // Supprimer toutes les notifications pour l'utilisateur
        $deletedCount = $notificationRepository->deleteAllForUser($userId);

        return new JsonResponse([
            'message' => "$deletedCount notification(s) supprimée(s)"
        ], JsonResponse::HTTP_OK);
    }

    #[Route('/api/notifications/add', name: 'add_notification', methods: ['POST'])]
    public function addNotification(Request $request, NotificationsRepository $notificationsRepository): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non connecté'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);

        // Valider les données reçues
        if (empty($data['message']) || empty($data['type'])) {
            return new JsonResponse(['error' => 'Les données de la notification sont invalides.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Créer la nouvelle notification
        $notification = new Notifications();
        $notification->setMessage($data['message']);
        $notification->setTypeNotification($data['type']);
        $notification->setIdUtilisateur($user); // L'utilisateur qui crée la notification
        $notification->setDateEnvoi(new \DateTime());
        $notification->setIsRead(false); 

        // Ajouter la notification via le repository
        $notificationsRepository->addNotification($notification);

        return new JsonResponse(['success' => 'Notification ajoutée avec succès.'], JsonResponse::HTTP_OK);
    }
#[Route('/api/notifications/mark-read', name: 'mark_all_as_read', methods: ['POST'])]
public function markAllAsRead(NotificationsRepository $notificationRepository, EntityManagerInterface $em): JsonResponse
{
    $user = $this->getUser();

    if (!$user) {
        return new JsonResponse(['error' => 'Utilisateur non connecté'], JsonResponse::HTTP_UNAUTHORIZED);
    }

    $count = $notificationRepository->markAllAsRead($user->getIdUtilisateur());

    return new JsonResponse(['success' => "$count notifications marquées comme lues"], JsonResponse::HTTP_OK);
}

}
