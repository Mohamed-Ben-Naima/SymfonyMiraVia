<?php
namespace App\Controller;
use App\Entity\Utilisateurs;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
class AdminDemandeAideController extends AbstractController
{
    #[Route('/admin/demande/aide', name: 'app_admin_aid_requests')]
    public function index(Connection $connection): Response
    {
        // SQL query to fetch required data
        $sql = "
        SELECT da.id_demande,
               da.type_aide,
               da.description,
               da.montant_demande,
               da.date_demande,
               da.status,
               da.preuves,
               u_client.nom AS client_nom, 
               u_client.prenom AS client_prenom,
               u_partenaire.nom AS partenaire_nom,
               u_partenaire.prenom AS partenaire_prenom
        FROM demandes_aide da
        LEFT JOIN utilisateurs u_client ON da.id_client = u_client.id_utilisateur
        LEFT JOIN utilisateurs u_partenaire ON da.id_partenaire = u_partenaire.id_utilisateur
        ";

        // Execute the query
        $demandesAide = $connection->fetchAllAssociative($sql);

        // Path to the public/uploads directory
        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/';

        // Process each demande to handle preuves
        foreach ($demandesAide as &$demande) {
            if (!empty($demande['preuves'])) {
                $originalPath = $demande['preuves']; // Original path from database
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
                $demande['preuves'] = '/uploads/' . $fileName;
            }
        }

        // Pass the data to the Twig template
        return $this->render('admin_demande_aide/index.html.twig', [
            'demandesAide' => $demandesAide,
            'pageTitle' => 'Demande aide',
        ]);
    }


    #[Route('/admin/demande/aide/update-status', name: 'update_aide_status', methods: ['POST'])]
    public function updateStatus(Request $request, Connection $connection, MailerInterface $mailer): JsonResponse
    {
        // Decode JSON payload
        $data = json_decode($request->getContent(), true);
    
        // Validate input
        if (!isset($data['id_demande']) || !isset($data['status'])) {
            return new JsonResponse(['error' => 'Invalid input'], 400);
        }
    
        $idDemande = $data['id_demande'];
        $newStatus = $data['status'];
    
        // Fetch the demande details
        $sql = "
        SELECT da.id_demande,
               u_client.email AS client_email,
               u_client.nom AS client_nom,
               u_client.prenom AS client_prenom,
               u_partenaire.email AS partenaire_email,
               u_partenaire.nom AS partenaire_nom,
               u_partenaire.prenom AS partenaire_prenom
        FROM demandes_aide da
        LEFT JOIN utilisateurs u_client ON da.id_client = u_client.id_utilisateur
        LEFT JOIN utilisateurs u_partenaire ON da.id_partenaire = u_partenaire.id_utilisateur
        WHERE da.id_demande = :id_demande
        ";
        $demande = $connection->fetchAssociative($sql, ['id_demande' => $idDemande]);
    
        if (!$demande) {
            return new JsonResponse(['error' => 'Demande not found'], 404);
        }
    
        // Update the status in the database
        $updateSql = "UPDATE demandes_aide SET status = :status WHERE id_demande = :id_demande";
        $connection->executeStatement($updateSql, [
            'status' => $newStatus,
            'id_demande' => $idDemande,
        ]);
    
        // Send emails to client and partner
        try {
            $clientEmail = (new Email())
                ->from('admin@example.com')
                ->to($demande['client_email'])
                ->subject('Mise à jour de votre demande d\'aide')
                ->text(sprintf(
                    "Bonjour %s %s,\n\nVotre demande d'aide a été mise à jour.\n\nStatut actuel : %s.",
                    $demande['client_nom'],
                    $demande['client_prenom'],
                    $newStatus
                ));
    
            $partnerEmail = (new Email())
                ->from('admin@example.com')
                ->to($demande['partenaire_email'])
                ->subject('Mise à jour de la demande d\'aide')
                ->text(sprintf(
                    "Bonjour %s %s,\n\nLa demande d'aide que vous suivez a été mise à jour.\n\nStatut actuel : %s.",
                    $demande['partenaire_nom'],
                    $demande['partenaire_prenom'],
                    $newStatus
                ));
    
            $mailer->send($clientEmail);
            $mailer->send($partnerEmail);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to send emails: ' . $e->getMessage()], 500);
        }
    
        return new JsonResponse(['success' => true]);
    }
}
