<?php

namespace App\Controller;

use App\Entity\Messages;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminMessagerieController extends AbstractController
{
    #[Route('/admin/messagerie', name: 'app_admin_messaging')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupérer les partenaires (type_utilisateur = 'Partenaire')
        $partenaires = $entityManager->getRepository(Utilisateur::class)
            ->findBy(['type_utilisateur' => 'Partenaire']);

        // Préparer les partenaires avec leurs id_conversation
        $partenairesData = [];
        $currentUser = $this->getUser();
        $currentUserId = $currentUser ? $currentUser->getIdUtilisateur() : null;

        foreach ($partenaires as $partenaire) {
            // Rechercher une conversation existante entre l'utilisateur connecté et le partenaire
            $message = $entityManager->getRepository(Messages::class)
                ->createQueryBuilder('m')
                ->where('(m.idExpediteur = :currentUserId AND m.idDestinataire = :partenaireId) OR (m.idExpediteur = :partenaireId AND m.idDestinataire = :currentUserId)')
                ->setParameter('currentUserId', $currentUserId)
                ->setParameter('partenaireId', $partenaire->getIdUtilisateur())
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            $partenairesData[] = [
                'partenaire' => $partenaire,
                'idConversation' => $message ? $message->getIdConversation() : null,
            ];
        }

        return $this->render('admin_messagerie/index.html.twig', [
            'controller_name' => 'AdminMessagerieController',
            'pageTitle' => 'Messagerie',
            'partenairesData' => $partenairesData,
        ]);
    }

    #[Route('/admin/messagerie/messages/{idConversation}', name: 'app_admin_messages')]
    public function getMessages(int $idConversation, EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupérer les messages pour une conversation donnée
        $messages = $entityManager->getRepository(Messages::class)
            ->findBy(['idConversation' => $idConversation], ['dateEnvoi' => 'ASC']);

        $messagesData = [];
        $currentUser = $this->getUser();
        $currentUserId = $currentUser ? $currentUser->getIdUtilisateur() : null;

        foreach ($messages as $message) {
            $messagesData[] = [
                'id' => $message->getIdMessage(),
                'expediteur' => $message->getExpediteur()
                    ? $message->getExpediteur()->getPrenom() . ' ' . $message->getExpediteur()->getNom()
                    : 'Inconnu',
                'message' => $message->getMessage(),
                'dateEnvoi' => $message->getDateEnvoi()->format('Y-m-d H:i:s'),
                'type' => $message->getTypeMessage(),
                'statut' => $message->getStatut(),
                'isSent' => $message->getIdExpediteur() === $currentUserId,
            ];
        }

        return new JsonResponse($messagesData);
    }

    #[Route('/admin/messagerie/send', name: 'app_admin_send_message', methods: ['POST'])]
    public function sendMessage(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $idDestinataire = $data['idDestinataire'] ?? null;
        $messageContent = $data['message'] ?? null;
        $idConversation = $data['idConversation'] ?? null;

        if (!$idDestinataire || !$messageContent) {
            return new JsonResponse(['error' => 'Données manquantes'], 400);
        }

        // Si aucune conversation n'existe, générer un nouvel id_conversation
        if (!$idConversation) {
            $lastMessage = $entityManager->getRepository(Messages::class)
                ->createQueryBuilder('m')
                ->select('MAX(m.idConversation)')
                ->getQuery()
                ->getSingleScalarResult();
            $idConversation = ($lastMessage ? $lastMessage + 1 : 1);
        }

        $message = new Messages();
        $message->setExpediteur($this->getUser());
        $message->setDestinataire($entityManager->getRepository(Utilisateur::class)->find($idDestinataire));
        $message->setMessage($messageContent);
        $message->setIdConversation($idConversation);
        $message->setDateEnvoi(new \DateTime());
        $message->setTypeMessage('utilisateur');
        $message->setStatut('envoyé');

        $entityManager->persist($message);
        $entityManager->flush();

        return new JsonResponse([
            'id' => $message->getIdMessage(),
            'message' => $message->getMessage(),
            'dateEnvoi' => $message->getDateEnvoi()->format('Y-m-d H:i:s'),
            'isSent' => true,
            'idConversation' => $idConversation,
        ]);
    }
}