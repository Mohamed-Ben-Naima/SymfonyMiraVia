<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class MessageriePartnerController extends AbstractController
{
    #[Route('/messagerie/partner', name: 'app_messagerie_partner')]
    public function index(): Response
    {
        $members = [
            [
                'name' => 'John Doe',
                'avatar' => 'https://mdbcdn.b-cdn.net/img/Photos/Avatars/avatar-8.webp',
                'message' => 'Hello, Are you there?',
                'time' => 'Just now',
                'unreadMessages' => 1,
                'isHighlighted' => true,
                'statusBadgeClass' => 'bg-success',
            ],
            [
                'name' => 'Danny Smith',
                'avatar' => 'https://mdbcdn.b-cdn.net/img/Photos/Avatars/avatar-1.webp',
                'message' => 'Lorem ipsum dolor sit.',
                'time' => '5 mins ago',
                'unreadMessages' => 0,
                'isHighlighted' => false,
                'statusBadgeClass' => 'bg-warning',
            ],
            [
                'name' => 'Alex Steward',
                'avatar' => 'https://mdbcdn.b-cdn.net/img/Photos/Avatars/avatar-2.webp',
                'message' => 'Lorem ipsum dolor sit.',
                'time' => 'Yesterday',
                'unreadMessages' => 0,
                'isHighlighted' => false,
                'statusBadgeClass' => 'bg-success',
            ],
        ];

        $chats = [
            [
                'align' => 'start',
                'avatar' => 'https://mdbcdn.b-cdn.net/img/Photos/Avatars/avatar-6.webp',
                'message' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                'time' => '12:00 PM | Aug 13',
                'bgClass' => 'bg-body-tertiary',
            ],
            [
                'align' => 'end',
                'avatar' => 'https://mdbcdn.b-cdn.net/img/Photos/Avatars/avatar-1.webp',
                'message' => 'Ut enim ad minim veniam, quis nostrud exercitation.',
                'time' => '12:05 PM | Aug 13',
                'bgClass' => 'bg-primary text-white',
            ],
        ];

        $currentUser = [
            'avatar' => 'https://mdbcdn.b-cdn.net/img/Photos/Avatars/avatar-8.webp',
        ];

        return $this->render('messagerie_partner/index.html.twig', [
            'members' => $members,
            'chats' => $chats,
            'currentUser' => $currentUser,
        ]);
    }
}