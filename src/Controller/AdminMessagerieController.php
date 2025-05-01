<?php

namespace App\Controller;

use App\Entity\Utilisateurs;
use App\Repository\UtilisateursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminMessagerieController extends AbstractController
{
    #[Route('/admin/messagerie', name: 'app_admin_messaging')]
    public function index(UtilisateursRepository $utilisateursRepository): Response
    {
        $partenaires = $utilisateursRepository->findBy(['typeUtilisateur' => 'Partenaire']);
        return $this->render('admin_messagerie/index.html.twig', [
            'controller_name' => 'AdminMessagerieController',
            'pageTitle' => 'Messagerie',
            'partenaires' => $partenaires,
        ]);
    }
}