<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminClientController extends AbstractController
{
    #[Route('/admin/client', name: 'app_admin_client')]
    public function index(Connection $connection): Response
    {
        // Requête SQL brute pour récupérer les données
        $sql = "
        SELECT c.id_client, 
               c.adresse AS client_adresse, 
               c.points_fidelite, 
               c.photo_CIN, 
               c.autres_documents,
               u.nom AS utilisateur_nom, 
               u.prenom AS utilisateur_prenom, 
               u.email AS utilisateur_email, 
               u.telephone AS utilisateur_telephone,
               u.status AS utilisateur_status,
               u.date_inscription
        FROM clients c
        JOIN utilisateurs u ON c.id_client = u.id_utilisateur
        ";

        $clients = $connection->fetchAllAssociative($sql);

        // Passer les données au template Twig
        return $this->render('admin_client/index.html.twig', [
            'clients' => $clients, // Données récupérées
            'pageTitle' => 'Clients',

        ]);
    }


    #[Route('/admin/client/change-status', name: 'app_admin_client_change_status', methods: ['POST'])]
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
}