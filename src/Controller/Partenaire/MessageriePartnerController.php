<?php

namespace App\Controller\Partenaire;
use  Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class MessageriePartnerController extends AbstractController
{
    #[Route('/dashboard/partenaire/messagerie', name: 'app_messagerie_partner')]
    public function index(Request $request): Response
    
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }
        // Données statiques pour les contacts
        $contacts = [
            ['id' => 1, 'name' => 'John Doe', 'avatar' => 'https://via.placeholder.com/40', 'lastMessage' => 'Je vais bien, merci !'],
            ['id' => 2, 'name' => 'Jane Smith', 'avatar' => 'https://via.placeholder.com/40', 'lastMessage' => 'À bientôt !'],
        ];

        // Récupérer l'identifiant du destinataire depuis la requête
        $receiverId = $request->query->get('receiver_id', 1); // Par défaut, le premier contact
        $currentReceiver = array_filter($contacts, fn($contact) => $contact['id'] == $receiverId);
        $currentReceiver = reset($currentReceiver);

        // Données statiques pour les messages
        $messages = [
            ['sender' => 'me', 'text' => 'Bonjour, comment ça va ?', 'time' => '10:00 AM'],
            ['sender' => 'John Doe', 'text' => 'Je vais bien, merci ! Et toi ?', 'time' => '10:05 AM'],
            ['sender' => 'me', 'text' => 'Je vais bien aussi, merci.', 'time' => '10:10 AM'],
        ];

        return $this->render('Partenaire/messagerie_partner/index.html.twig', [
            'contacts' => $contacts,
            'messages' => $messages,
            'currentUser' => ['id' => 0, 'name' => 'Me'], // Utilisateur actuel simulé
            'currentReceiver' => $currentReceiver,
            
            
        ]);
    }

    #[Route('/dashboard/partenaire/send', name: 'app_messagerie_partner_send', methods: ['POST'])]
    public function sendMessage(Request $request): Response
    {
        // Récupérer les données du formulaire
        $receiverId = $request->request->get('receiver_id');
        $messageText = $request->request->get('message');

        // Simuler l'envoi d'un message
        $this->addFlash('success', "Message envoyé à l'utilisateur avec l'ID $receiverId : $messageText");

        return $this->redirectToRoute('app_messagerie_partner', ['receiver_id' => $receiverId]);
    }
}