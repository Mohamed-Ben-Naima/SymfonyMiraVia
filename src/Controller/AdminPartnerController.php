<?php
namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
final class AdminPartnerController extends AbstractController
{
    #[Route('/admin/partner', name: 'app_admin_partner')]
    public function index(Connection $connection): Response
    {
        // Mettre à jour automatiquement les statuts pour les partenaires expirés
        $today = (new \DateTime())->format('Y-m-d');
        $connection->executeStatement(
            'UPDATE utilisateurs u
             JOIN partenaires p ON u.id_utilisateur = p.id_partenaire
             SET u.status = "Inactif"
             WHERE p.date_expiration < :today AND u.status != "Inactif"',
            ['today' => $today]
        );
    
        // Requête SQL brute pour récupérer les données
        $sql = "
            SELECT p.id_partenaire, 
                   p.nom_entreprise, 
                   p.type_activite, 
                   p.site_web, 
                   p.adresse, 
                   p.telephone AS partenaire_telephone,
                   p.date_expiration, 
                   u.nom, 
                   u.prenom, 
                   u.email, 
                   u.telephone AS utilisateur_telephone,
                   u.status AS utilisateur_status
            FROM partenaires p
            JOIN utilisateurs u ON p.id_partenaire = u.id_utilisateur
        ";
    
        $partenaires = $connection->fetchAllAssociative($sql);
    
        // Passer les données au template Twig
        return $this->render('admin_partner/index.html.twig', [
            'controller_name' => 'AdminPartnerController',
            'pageTitle' => 'Partenaire',
            'partenaires' => $partenaires, // Données récupérées
        ]);
    }
    #[Route('/admin/partner/change-status', name: 'app_admin_partner_change_status', methods: ['POST'])]
public function changeStatus(Request $request, Connection $connection): JsonResponse
{
    $data = json_decode($request->getContent(), true); // Récupérer les données JSON envoyées
    $id = $data['id'] ?? null;
    $newStatus = $data['status'] ?? null;

    if (!$id || !$newStatus) {
        return new JsonResponse(['error' => 'Données invalides'], 400);
    }

    // Requête SQL pour mettre à jour le statut
    $connection->executeStatement(
        'UPDATE utilisateurs SET status = :status WHERE id_utilisateur = :id',
        ['status' => $newStatus, 'id' => $id]
    );

    // Retourner une réponse vide pour indiquer que l'opération est terminée
    return new JsonResponse(null, 204); // 204 No Content
}
#[Route('/admin/partner/update-expiration', name: 'app_admin_partner_update_expiration', methods: ['POST'])]
public function updateExpiration(Request $request, Connection $connection): JsonResponse
{
    $data = json_decode($request->getContent(), true); // Récupérer les données JSON envoyées
    $id = $data['id'] ?? null;
    $expirationDate = $data['expirationDate'] ?? null;

    if (!$id || !$expirationDate) {
        return new JsonResponse(['error' => 'Données invalides'], 400);
    }

    // Vérifier si la date d'expiration est antérieure à aujourd'hui
    $today = new \DateTime(); // Date actuelle
    $expirationDateObj = \DateTime::createFromFormat('Y-m-d', $expirationDate);

    if ($expirationDateObj < $today) {
        return new JsonResponse(['error' => 'La date d\'expiration doit être postérieure ou égale à aujourd\'hui.'], 400);
    }

    try {
        // Mettre à jour la date d'expiration dans la base de données
        $connection->executeStatement(
            'UPDATE partenaires SET date_expiration = :expirationDate WHERE id_partenaire = :id',
            ['expirationDate' => $expirationDate, 'id' => $id]
        );

        return new JsonResponse(['message' => 'Date d\'expiration mise à jour avec succès.'], 200);
    } catch (\Exception $e) {
        return new JsonResponse(['error' => 'Erreur lors de la mise à jour.'], 500);
    }
}
}