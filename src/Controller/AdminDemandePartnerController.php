<?php

namespace App\Controller;

use App\Entity\DemandePartenaire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Utilisateurs; // Correctly import the Utilisateurs class
use App\Entity\Partenaires; // Correctly import the Partenaires class
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function updateStatus(Request $request, EntityManagerInterface $entityManager, \Symfony\Component\Mailer\MailerInterface $mailer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $demande = $entityManager->getRepository(DemandePartenaire::class)->find($data['id']);
        
        if (!$demande) {
            return new JsonResponse(['message' => 'Demande non trouvée'], Response::HTTP_NOT_FOUND);
        }
        
        $demande->setEtat($data['etat']);
        
        $emailToSend = null;
        
        if ($data['etat'] === 'Acceptée') {
            // Validate mandatory fields in DemandePartenaire
            if (
                !$demande->getDemandeur() ||
                !$demande->getEmail() ||
                !$demande->getTelephone() ||
                !$demande->getAdresse()
            ) {
                return new JsonResponse(['message' => 'Données manquantes dans la demande partenaire.'], Response::HTTP_BAD_REQUEST);
            }
        
            try {
                // Create a new Utilisateur
                $utilisateur = new Utilisateurs();
                $utilisateur->setNom($demande->getDemandeur())
                            ->setEmail($demande->getEmail())
                            ->setTelephone($demande->getTelephone())
                            ->setAdresseUtilisateur($demande->getAdresse())
                            ->setTypeUtilisateur('partenaire')
                            ->setDateInscription(new \DateTime())
                            ->setStatus('actif')
                            ->setStatutVerification('Actif'); // Set statut_verification to 'Actif'
            
                $entityManager->persist($utilisateur);
            
                // Flush Utilisateurs first to ensure it is saved
                $entityManager->flush();
            
                // Create a new Partenaire
                $partenaire = new Partenaires();
                $partenaire->setUtilisateur($utilisateur) // Link to the persisted Utilisateur
                           ->setNomEntreprise($demande->getDemandeur()) // Assuming the demandeur is the company name
                           ->setTypeActivite($demande->getTypeActivite())
                           ->setAdresse($demande->getAdresse())
                           ->setTelephone($demande->getTelephone())
                           ->setEtat(1)
                           ->setDateExpiration((new \DateTime())->modify('+1 year'));
            
                $entityManager->persist($partenaire);
            
                // Flush Partenaires
                $entityManager->flush();
    
                // Compose acceptance email
                $emailToSend = (new \Symfony\Component\Mime\Email())
                    ->from('mayssajrad2002@gmail.com') // Replace with your email
                    ->to($demande->getEmail())
                    ->subject('Votre demande a été acceptée')
                    ->html("
                        <p>Bonjour {$demande->getDemandeur()},</p>
                        <p>Votre demande de partenariat a été acceptée avec succès. Bienvenue parmi nos partenaires !</p>
                    ");
            } catch (\Exception $e) {
                return new JsonResponse(['message' => 'Erreur lors de l’ajout des données: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } elseif ($data['etat'] === 'Refusée') {
            // Compose refusal email
            $emailToSend = (new \Symfony\Component\Mime\Email())
                ->from('mayssajrad2002@gmail.com') // Replace with your email
                ->to($demande->getEmail())
                ->subject('Votre demande a été refusée')
                ->html("
                    <p>Bonjour {$demande->getDemandeur()},</p>
                    <p>Nous regrettons de vous informer que votre demande de partenariat a été refusée.</p>
                ");
        }
        
        // Send email notification
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