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
        // Récupérer les demandes partenaires depuis la base de données
        $demandes = $entityManager->getRepository(DemandePartenaire::class)->findAll();

        // Chemin vers le répertoire public/uploads
        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/';

        foreach ($demandes as $demande) {
            if ($demande->getPreuve()) {
                $originalPath = $demande->getPreuve(); // Chemin absolu depuis la base de données
                $fileName = basename($originalPath); // Nom du fichier
                $publicPath = $uploadDir . $fileName; // Chemin cible dans public/uploads/

                // Si le fichier n'existe pas dans public/uploads, le copier
                if (!file_exists($publicPath) && file_exists($originalPath)) {
                    copy($originalPath, $publicPath);
                }

                // Mettre à jour le chemin relatif pour la vue
                $relativePath = 'uploads/' . $fileName;
                $demande->setPreuve('uploads/' . $fileName);
            }
        }

        return $this->render('admin_demande_partner/index.html.twig', [
            'controller_name' => 'AdminDemandePartnerController',
            'pageTitle' => 'Demandes Partenaires',
            'demandes' => $demandes,
        ]);
    }
}