<?php

namespace App\Controller\Partenaire;

use App\Entity\Dons;
use App\Entity\Utilisateurs;
use App\Repository\DonsRepository;
use App\Repository\UtilisateursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DonPartnerController extends AbstractController
{
    #[Route('/dashboard/don/partner', name: 'app_don_partner')]
    public function don(
        DonsRepository $repository,
        UtilisateursRepository $utilisateursRepository
    ): Response {
        $user = $this->getUser();

        if (!$user instanceof Utilisateurs) {
            throw $this->createAccessDeniedException('Vous devez être un partenaire connecté.');
        }
        
        $id_partenaire = $user->getIdUtilisateur();
        $don = $repository->findByPartenaire($id_partenaire);
        $clients = $utilisateursRepository->findAllClients();

        return $this->render('Partenaire/don_partner/don.html.twig', [
            'dons' => $don,
            'id_partenaire' => $id_partenaire,
            'utilisateurs' => $clients
        ]);
    }

    #[Route('/dashboard/add-don', name: 'add_don', methods: ['POST'])]

    public function addDon(
            Request $request,
            EntityManagerInterface $entityManager,
            UtilisateursRepository $utilisateursRepository
    ): Response {


    // Récupérer l'utilisateur connecté
    $user = $this->getUser();

    if (!$user instanceof Utilisateurs) {
        throw $this->createAccessDeniedException('Vous devez être un partenaire connecté.');
    }

    // Récupérer les données du formulaire
    $data = $request->request->all();

    // Validation des données
    if (empty($data['id_client']) || empty($data['montant']) || empty($data['date_don'])) {
        $this->addFlash('error', 'Tous les champs sont requis pour ajouter un don.');
        return $this->redirectToRoute('app_don_partner');
    }

    // Vérifier si le client existe dans la base de données
    $client = $utilisateursRepository->find($data['id_client']);
    if (!$client) {
        $this->addFlash('error', 'Client introuvable.');
        return $this->redirectToRoute('app_don_partner');
    }

    $id_partenaire = $user->getIdUtilisateur();

    try {
        // Créer un nouvel objet Don
        $don = new Dons();
        $don->setIdClient($data['id_client']);
        $don->setIdPartenaire($id_partenaire); // ID du partenaire connecté
        $don->setMontant((float) $data['montant']);
        $don->setDateDon(new \DateTime($data['date_don'])); // Date du don
        $don->setStatus($data['status'] ?? 'En attente'); // Statut par défaut

        // Persister et enregistrer le don
        $entityManager->persist($don);
        $entityManager->flush();

        $this->addFlash('success', 'Don ajouté avec succès !');
    } catch (\Exception $e) {
        $this->addFlash('error', 'Une erreur s\'est produite lors de l\'ajout du don.');
    }

    return $this->redirectToRoute('app_don_partner');
}

    #[Route('/dashboard/edit-don/{id}', name: 'edit_don', methods: ['POST'])]
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

        $this->addFlash('success', 'Don modifié avec succès !');
        return $this->redirectToRoute('app_don_partner');
    }

    #[Route('/dashboard/delete-don/{id}', name: 'delete_don', methods: ['POST'])]
    public function deleteDon(Dons $don, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($don);
        $entityManager->flush();

        $this->addFlash('success', 'Don supprimé avec succès !');
        return $this->redirectToRoute('app_don_partner');
    }
}