<?php

namespace App\Controller;

use App\Entity\Benevolat;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

final class AdminBenevolatController extends AbstractController
{
    #[Route('/admin/benevolat', name: 'app_admin_volunteering')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur est connecté en vérifiant la session
        $session = $request->getSession();
        $adminData = $session->get('admin');

        if (!$adminData) {
            // Rediriger vers la page de connexion avec un message flash
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('app_admin_login');
        }

        // Récupérer toutes les données de l'entité Benevolat
        $benevolats = $entityManager->getRepository(Benevolat::class)->findAll();

        return $this->render('admin_benevolat/index.html.twig', [
            'benevolats' => $benevolats,
            'pageTitle' => 'Bénévolat',
        ]);
    }
}