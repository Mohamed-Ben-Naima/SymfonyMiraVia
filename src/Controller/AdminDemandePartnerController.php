<?php

namespace App\Controller;

use App\Entity\DemandePartenaire;
use App\Entity\Utilisateurs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

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

    #[Route('/admin/demande/partner/update-status', name: 'app_admin_update_status', methods: ['POST'])]
    public function updateStatus(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $demande = $entityManager->getRepository(DemandePartenaire::class)->find($data['id']);
        
        if (!$demande) {
            return new JsonResponse(['message' => 'Demande non trouvée'], Response::HTTP_NOT_FOUND);
        }
        
        $demande->setEtat($data['etat']);
        $emailToSend = null;

        if ($data['etat'] === 'Acceptée') {
            // Find the user by email
            $utilisateur = $entityManager->getRepository(Utilisateurs::class)->findOneBy(['email' => $demande->getEmail()]);
            
            if (!$utilisateur) {
                return new JsonResponse(['message' => 'Utilisateur associé non trouvé.'], Response::HTTP_BAD_REQUEST);
            }
            
            try {
                // Update the user's status
                $utilisateur->setStatus('actif')->setStatutVerification('Actif');
                $entityManager->persist($utilisateur);
                
                // Send acceptance email
                $emailToSend = (new Email())
                    ->from('mayssajrad2002@gmail.com') // Replace with your email
                    ->to($demande->getEmail())
                    ->subject('Votre demande a été acceptée')
                    ->html($this->renderView('emails/acceptation.html.twig', [
                        'demandeur' => $demande->getDemandeur(),
                    ]));
            } catch (\Exception $e) {
                return new JsonResponse(['message' => 'Erreur lors de la mise à jour des données: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } elseif ($data['etat'] === 'Refusée') {
            // Send refusal email
            $emailToSend = (new Email())
                ->from('mayssajrad2002@gmail.com') // Replace with your email
                ->to($demande->getEmail())
                ->subject('Votre demande a été refusée')
                ->html($this->renderView('emails/refus.html.twig', [
                    'demandeur' => $demande->getDemandeur(),
                ]));
        }
        
        if ($emailToSend) {
            try {
                $mailer->send($emailToSend);
            } catch (\Exception $e) {
                return new JsonResponse(['message' => 'Erreur lors de l’envoi de l’email: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
        
        $entityManager->flush();
        
        return new JsonResponse(['message' => 'Statut mis à jour avec succès']);
    }
}