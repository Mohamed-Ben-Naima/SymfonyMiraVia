<?php
namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

        // Pass the data to the Twig template
        return $this->render('admin_demande_aide/index.html.twig', [
            'demandesAide' => $demandesAide,
            'pageTitle' => 'Demande aide', // Le titre dynamique


        ]);
    }
    
}
