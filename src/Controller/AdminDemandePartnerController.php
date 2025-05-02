<?php

namespace App\Controller;

use App\Entity\DemandePartenaire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

final class AdminDemandePartnerController extends AbstractController
{
    #[Route('/admin/demande/partner', name: 'app_admin_demande_partner')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Fetch all partner requests
        $demandes = $entityManager->getRepository(DemandePartenaire::class)->findAll();

        // Path to the public/uploads directory
        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/';

        foreach ($demandes as $demande) {
            if ($demande->getPreuve()) {
                $originalPath = $demande->getPreuve(); // Original path from the database
                $fileName = basename($originalPath); // Extract filename
                $publicPath = $uploadDir . $fileName; // Target path in public/uploads/

                // Ensure the uploads directory exists
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                // Copy file to public/uploads if it doesn't exist
                if (!file_exists($publicPath) && file_exists($originalPath)) {
                    copy($originalPath, $publicPath);
                }

                // Set relative path for the view (e.g., /uploads/filename)
                $relativePath = '/uploads/' . $fileName;
                $demande->setPreuve($relativePath);
            }
        }

        return $this->render('admin_demande_partner/index.html.twig', [
            'controller_name' => 'AdminDemandePartnerController',
            'pageTitle' => 'Demandes Partenaires',
            'demandes' => $demandes,
        ]);
    }
}