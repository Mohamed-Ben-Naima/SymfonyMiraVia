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
        UtilisateursRepository $utilisateursRepository
    ): Response {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        // Récupérer tous les contacts sauf l'utilisateur connecté
        $contacts = array_filter(
            $utilisateursRepository->findAll(),
            fn($contact) => $contact->getIdUtilisateur() !== $user->getIdUtilisateur()
        );

        $contactData = [];
        foreach ($contacts as $contact) {
            // Récupérer la conversation avec ce contact
            $conversation = $conversationsRepository->findOneBy([
                'idUser1' => $user->getIdUtilisateur(),
                'idUser2' => $contact->getIdUtilisateur()
            ]) ?: $conversationsRepository->findOneBy([
                'idUser1' => $contact->getIdUtilisateur(),
                'idUser2' => $user->getIdUtilisateur()
            ]);

            $lastMessageText = '';
            if ($conversation) {
                $lastMessage = $messagesRepository->findOneBy(
                    ['idConversation' => $conversation->getIdConversation()],
                    ['dateEnvoi' => 'DESC']
                );
                $lastMessageText = $lastMessage ? $lastMessage->getMessage() : '';
            }

            $contactData[] = [
                'idUtilisateur' => $contact->getIdUtilisateur(),
                'name' => $contact->getNom() . ' ' . $contact->getPrenom(),
                'avatar' => '/assets/images/avatar-placeholder.png', // À adapter si tu as un champ avatar
                'lastMessage' => $lastMessageText,
            ];
        }

        // Récupérer les conversations liées à l'utilisateur
        $conversations = $conversationsRepository->findByUser($user);

        // Destinataire sélectionné
        $receiverId = $request->query->get('receiver_id');

        if (!$receiverId && count($conversations) > 0) {
            $receiverId = $conversations[0]->getIdUser1() === $user->getIdUtilisateur()
                ? $conversations[0]->getIdUser2()
                : $conversations[0]->getIdUser1();
        }

        $currentReceiver = $receiverId ? $utilisateursRepository->find($receiverId) : null;
        $messages = [];

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
            }
        }

        return $this->render('Partenaire/messagerie_partner/index.html.twig', [
            'contacts' => $contactData,
            'conversations' => $conversations,
            'messages' => $messages,
            'currentUser' => $user,
            'currentReceiver' => $currentReceiver,
            'receiverId' => $receiverId,
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
            $conversation->setIdUser2($receiver->getIdUtilisateur());
            $conversation->setSujet('Nouvelle conversation');
            $conversation->setDateCreation(new \DateTime());
            $em->persist($conversation);
            $em->flush(); // Sauvegarder la conversation
        }
    
        // Créer le message à envoyer
        $message = new Messages();
        $message->setIdExpediteur($senderId); 
        $message->setIdDestinataire($receiver->getIdUtilisateur());
        $message->setMessage($messageText);
        $message->setDateEnvoi(new \DateTime());
        $message->setConversation($conversation); // Utiliser la relation ManyToOne pour associer la conversation
        $message->setTypeMessage('utilisateur'); // Type message : utilisateur ou chatbot
        $message->setStatut('envoyé'); // Statut initial
    
        // Sauvegarder le message
        $em->persist($message);
        $em->flush();
    
        // Répondre avec un JSON pour indiquer le succès
        return new JsonResponse(['success' => true, 'conversationId' => $conversation->getIdConversation()]);
    }
    
    

    #[Route('/dashboard/partenaire/messagerie/messages', name: 'ajax_get_messages', methods: ['GET'])]
    public function getMessages(Request $request, MessagesRepository $messageRepo, UtilisateursRepository $userRepo): Response
    {
        $receiverId = $request->query->get('receiver_id');
        $receiver = $userRepo->find($receiverId);
        $currentUser = $this->getUser();
    
        if (!$receiver || !$currentUser) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé.'], 404);
        }
    
        $messages = $messageRepo->findMessagesBetween($currentUser, $receiver);
    
        $mappedMessages = array_map(function (Messages $msg) use ($currentUser) {
            return [
                'sender' => $msg->getIdExpediteur() === $currentUser ? 'me' : 'them',
                'text' => $msg->getMessage(),
                'time' => $msg->getDateEnvoi()->format('H:i'),
            ];
        }, $messages);
    
        return $this->render('Partenaire/messagerie_partner/index.html.twig', [
            'messages' => $mappedMessages,
            'receiverId' => $receiverId,
        ]);
    }
    

    
}
