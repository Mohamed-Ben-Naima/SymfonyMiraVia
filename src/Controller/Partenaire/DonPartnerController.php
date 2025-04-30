<?php

namespace App\Controller\Partenaire;

use App\Entity\Dons;
use App\Repository\DonsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DonPartnerController extends AbstractController
{
    #[Route('/don/partner', name: 'app_don_partner')]
    public function index(DonsRepository $donsRepository): Response
    {
        // Fetch donations and client names for the current partner
        $dons = $donsRepository->findDonsWithClientNames(1); // Replace "1" with dynamic partner ID if needed

        return $this->render('Partenaire/don_partner/don.html.twig', [
            'dons' => $dons,
        ]);
    }

    #[Route('/add-don', name: 'add_don', methods: ['POST'])]
    public function addDon(Request $request, EntityManagerInterface $entityManager): Response
    {
        $clientName = $request->request->get('clientName'); // Retrieve client name from the form
        $montant = $request->request->get('montant');
        $dateDon = $request->request->get('dateDon');

        // Validate inputs
        if (!$clientName || !$montant || !$dateDon) {
            $this->addFlash('error', 'Tous les champs sont requis pour ajouter un don.');
            return $this->redirectToRoute('app_don_partner');
        }

        // Fetch client ID based on the provided client name
        $conn = $entityManager->getConnection();
        $sql = '
            SELECT c.id_client
            FROM clients c
            JOIN utilisateurs u ON c.id_client = u.id_utilisateur
            WHERE u.nom = :clientName
        ';
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery(['clientName' => $clientName])->fetchAssociative();

        if (!$result) {
            $this->addFlash('error', 'Client introuvable avec ce nom.');
            return $this->redirectToRoute('app_don_partner');
        }

        $idClient = $result['id_client']; // Get the client ID

        // Create a new donation
        $don = new Dons();
        $don->setIdClient($idClient);
        $don->setMontant($montant);
        $don->setDateDon(new \DateTime($dateDon));
        $don->setIdPartenaire(1); // Set the current partner ID (hardcoded or dynamic)
        $don->setStatus('En attente');
        
        $entityManager->persist($don);
        $entityManager->flush();

        $this->addFlash('success', 'Don ajouté avec succès !');
        return $this->redirectToRoute('app_don_partner');
    }

    #[Route('/edit-don/{id}', name: 'edit_don', methods: ['POST'])]
        public function editDon(Request $request, Dons $don, EntityManagerInterface $entityManager): Response
        {
            $montant = $request->request->get('montant');
            $dateDon = $request->request->get('dateDon');

            // Validate inputs
            if (!$montant || !$dateDon) {
                $this->addFlash('error', 'Tous les champs sont requis pour modifier un don.');
                return $this->redirectToRoute('app_don_partner'); // Redirect to the list page
            }

            // Update the donation entity
            $don->setMontant((float) $montant);
            $don->setDateDon(new \DateTime($dateDon));

            // Save changes to the database
            $entityManager->flush();

            // Provide feedback to the user
            $this->addFlash('success', 'Don modifié avec succès !');
            return $this->redirectToRoute('app_don_partner');
        }

    #[Route('/delete-don/{id}', name: 'delete_don', methods: ['POST'])]
    public function deleteDon(Dons $don, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($don);
        $entityManager->flush();

        $this->addFlash('success', 'Don supprimé avec succès !');
        return $this->redirectToRoute('app_don_partner');
    }
}