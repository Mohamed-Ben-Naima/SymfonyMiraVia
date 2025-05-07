<?php

namespace App\Controller\Partenaire;

use App\Entity\Conversations;
use App\Entity\Messages;
use App\Entity\Utilisateurs;
use App\Repository\ConversationsRepository;
use App\Repository\MessagesRepository;
use App\Repository\UtilisateursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class MessageriePartnerController extends AbstractController
{
    #[Route('/dashboard/partenaire/messagerie', name: 'app_messagerie_partner')]
    public function index(
        Request $request,
        ConversationsRepository $conversationsRepository,
        MessagesRepository $messagesRepository,
        UtilisateursRepository $utilisateursRepository,
        EntityManagerInterface $em
    ): Response {
        // 1. Récupérer l'utilisateur connecté
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        // 2. Récupérer tous les contacts sauf l'utilisateur connecté
        $contacts = $utilisateursRepository->findAllExcept($user);
            // Récupérer le terme de recherche de la requête (s'il y en a un)
            $searchTerm = $request->query->get('search', '');

            // 2. Récupérer les contacts sauf l'utilisateur connecté (et filtrer par le terme de recherche)
            $contacts = $utilisateursRepository->findContactsExcept($user, $searchTerm);

        $contactData = [];
        foreach ($contacts as $contact) {
            // Chercher la conversation avec ce contact dans un sens puis dans l'autre
            $conversation = $conversationsRepository->findOneBy([
                'idUser1' => $user->getIdUtilisateur(),
                'idUser2' => $contact->getIdUtilisateur()
            ]) ?: $conversationsRepository->findOneBy([
                'idUser1' => $contact->getIdUtilisateur(),
                'idUser2' => $user->getIdUtilisateur()
            ]);

            // Récupérer le dernier message
            $lastMessageText = '';
            $lastMessageTime = '';
            if ($conversation) {
                $lastMessage = $messagesRepository->findOneBy(
                    ['idConversation' => $conversation->getIdConversation()],
                    ['dateEnvoi' => 'DESC']
                );
                $lastMessageText = $lastMessage ? $lastMessage->getMessage() : '';
                if ($lastMessage) {
                    $lastMessageTime = $this->timeAgo($lastMessage->getDateEnvoi()); // Format relatif
                }
            }

            // Compter le nombre de messages non lus pour ce contact
            $unreadCount = $messagesRepository->countUnreadMessages($user, $contact);

            $contactData[] = [
                'idUtilisateur' => $contact->getIdUtilisateur(),
                'name' => $contact->getNom() . ' ' . $contact->getPrenom(),
                'avatar' => $contact->getAvatar(),
                'lastMessage' => $lastMessageText,
                'lastMessageTime' => $lastMessageTime, // Afficher le temps relatif pour le dernier message
                'senderId' => $user->getIdUtilisateur(),
                'unreadCount' => $unreadCount, // Nombre de messages non lus

            ];
        }

        // 3. Récupérer toutes les conversations de l'utilisateur connecté
        $conversations = $conversationsRepository->findByUser($user);

        // 4. Déterminer le destinataire courant
        $receiverId = $request->query->get('receiver_id');
        if (!$receiverId && count($conversations) > 0) {
            $receiverId = $conversations[0]->getIdUser1() === $user->getIdUtilisateur()
                ? $conversations[0]->getIdUser2()
                : $conversations[0]->getIdUser1();
        }

        $currentReceiver = $receiverId ? $utilisateursRepository->find($receiverId) : null;
        $messages = [];

        // 5. Charger les messages si un destinataire est sélectionné
        if ($currentReceiver) {
            $conversation = $conversationsRepository->findOneBy([
                'idUser1' => $receiverId,
                'idUser2' => $user->getIdUtilisateur()
            ]) ?: $conversationsRepository->findOneBy([
                'idUser1' => $user->getIdUtilisateur(),
                'idUser2' => $receiverId
            ]);

            if ($conversation) {
                $messages = $messagesRepository->findBy(
                    ['idConversation' => $conversation->getIdConversation()],
                    ['dateEnvoi' => 'ASC']
                );

                // Mettre à jour le statut des messages uniquement pour ceux envoyés au destinataire
                foreach ($messages as $message) {
                    if ($message->getIdDestinataire() === $user->getIdUtilisateur() && $message->getStatut() !== 'lu') {
                        $message->setStatut('lu');
                        $em->persist($message);
                    }
                }
                $em->flush();
            }
        }

        // 6. Afficher la vue Twig avec toutes les données nécessaires
        return $this->render('Partenaire/messagerie_partner/index.html.twig', [
            'contacts' => $contactData,
            'conversations' => $conversations,
            'messages' => $messages,
            'currentUser' => $user,
            'senderId' => $user->getIdUtilisateur(),
            'currentReceiver' => $currentReceiver,
            'receiverId' => $receiverId,
            'searchTerm' => $searchTerm,
        ]);
    }

    #[Route('/dashboard/partenaire/messagerie/send-message', name: 'send_message', methods: ['POST'])]
    public function sendMessage(Request $request, EntityManagerInterface $em): JsonResponse
    {
        // Récupérer les données envoyées (en JSON)
        $data = json_decode($request->getContent(), true);
    
        // Récupérer l'utilisateur connecté (expéditeur)
        $sender = $this->getUser();
    
        // Si l'ID de l'expéditeur est envoyé, on peut le vérifier
        $senderId = $data['senderId'] ?? $sender->getIdUtilisateur();
    
        // Vérification si l'utilisateur est connecté
        if (!$sender) {
            return new JsonResponse(['success' => false, 'error' => 'Utilisateur non connecté'], 401);
        }
    
        // Vérification des données envoyées dans la requête
        $messageText = $data['message'] ?? null;
        $receiverId = $data['receiverId'] ?? null;
    
        if (!$receiverId || !$messageText) {
            return new JsonResponse(['success' => false, 'error' => 'Données incomplètes. Le destinataire ou le message manquent.'], 400);
        }
    
        // Récupérer le destinataire
        $receiver = $em->getRepository(Utilisateurs::class)->find($receiverId);
        if (!$receiver) {
            return new JsonResponse(['success' => false, 'error' => 'Destinataire introuvable'], 404);
        }
    
        // Vérifier si une conversation existe déjà entre l'expéditeur et le destinataire
        $conversation = $em->getRepository(Conversations::class)->findOneBy([
            'idUser1' => $senderId, // Utiliser senderId ici
            'idUser2' => $receiver->getIdUtilisateur()
        ]) ?: $em->getRepository(Conversations::class)->findOneBy([
            'idUser1' => $receiver->getIdUtilisateur(),
            'idUser2' => $senderId // Utiliser senderId ici
        ]);
    
        // Si aucune conversation n'existe, en créer une nouvelle
        if (!$conversation) {
            $conversation = new Conversations();
            $conversation->setIdUser1($senderId); // Utiliser senderId ici
            $conversation->setIdUser2($receiverId);
            $conversation->setSujet('Nouvelle conversation');
            $conversation->setDateCreation(new \DateTime());
            $em->persist($conversation);
            $em->flush(); // Sauvegarder la conversation
        }

        // Créer le message à envoyer
        $message = new Messages();
        $senderUser = $em->getRepository(Utilisateurs::class)->find($senderId);
        $receiverUser = $em->getRepository(Utilisateurs::class)->find($receiverId);
        $message->setExpediteur($senderUser);
        $message->setDestinataire($receiverUser);
        $message->setMessage($messageText);
        $message->setDateEnvoi(new \DateTime());
        $message->setConversation($conversation); // Utiliser la relation ManyToOne pour associer la conversation
        $message->setTypeMessage('utilisateur'); // Type message : utilisateur ou chatbot
        $message->setStatut('reçu'); // Statut initial
    
        // Sauvegarder le message
        $em->persist($message);
        $em->flush();
    
        // Répondre avec un JSON pour indiquer le succès
        return new JsonResponse(['success' => true, 'conversationId' => $conversation->getIdConversation()]);
    }

    #[Route('/dashboard/partenaire/messagerie/messages', name: 'ajax_get_messages', methods: ['GET'])]
    public function getMessages(Request $request, MessagesRepository $messageRepo, UtilisateursRepository $userRepo): JsonResponse
    {
        $receiverId = $request->query->get('receiver_id');
        $receiver = $userRepo->find($receiverId);
        $currentUser = $this->getUser();
    
        if (!$receiver || !$currentUser) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé.'], 404);
        }
    
        // Récupérer les messages entre l'utilisateur actuel et le destinataire
        $messages = $messageRepo->findMessagesBetween($currentUser, $receiver);
       
        // Mapper les entités Messages en tableau simple
        $messagesArray = array_map(function ($message) use ($userRepo) {
            // Récupérer l'expéditeur du message
            $senderId = $message->getIdExpediteur();
            $sender = $userRepo->find($senderId);
            return [
                'message' => $message->getMessage(),
                'senderId' => $message->getIdExpediteur(),
                'dateEnvoi' => $message->getDateEnvoi()->format('Y-m-d H:i:s'),
                'senderAvatar' => $sender && $sender->getAvatar() ? $sender->getAvatar() : 'user.png',
            ];
        }, $messages);
    
        return new JsonResponse(['messages' => $messagesArray]);
    }

    #[Route('/dashboard/partenaire/messagerie/mark-read', name: 'mark_message_as_read', methods: ['POST'])]
    public function markAsRead(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $messageId = $request->request->get('messageId');
        $message = $em->getRepository(Messages::class)->find($messageId);

        if (!$message) {
            return new JsonResponse(['error' => 'Message non trouvé'], 404);
        }

        // Mettre à jour le statut du message à 'lu'
        $message->setStatut('lu');
        $em->persist($message);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }

    // Fonction pour afficher un format relatif (par exemple : "À l'instant", "Il y a 5 minutes", "Il y a 2 heures", etc.)
    private function timeAgo(\DateTime $dateTime): string {
        $now = new \DateTime(); // Heure actuelle
        $interval = $now->diff($dateTime); // Calcul de la différence entre la date actuelle et celle du message
        
        // Affichage en fonction de la différence
        if ($interval->y > 0) {
            return 'Il y a ' . $interval->y . ' an' . ($interval->y > 1 ? 's' : '');
        } elseif ($interval->m > 0) {
            return 'Il y a ' . $interval->m . ' mois';
        } elseif ($interval->d > 0) {
            return 'Il y a ' . $interval->d . ' jour' . ($interval->d > 1 ? 's' : '');
        } elseif ($interval->h > 0) {
            return 'Il y a ' . $interval->h . ' heure' . ($interval->h > 1 ? 's' : '');
        } elseif ($interval->i > 0) {
            return 'Il y a ' . $interval->i . ' minute' . ($interval->i > 1 ? 's' : '');
        } elseif ($interval->s > 0) {
            return 'Il y a ' . $interval->s . ' seconde' . ($interval->s > 1 ? 's' : '');
        } else {
            return 'À l\'instant';
        }
    }
}
