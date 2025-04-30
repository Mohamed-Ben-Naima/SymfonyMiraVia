<?php

namespace App\Controller\Partenaire;

use DateTime;
use App\Entity\Notifications;
use App\Entity\DemandesAide;
use App\Entity\Utilisateurs;
use App\Repository\DemandesAideRepository;
use App\Repository\UtilisateursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class DmAidePartnerController extends AbstractController
{
    #[Route('/dashboard/partenaire/demande_aide', name: 'demande_aide')]
    public function demandeAide(
        DemandesAideRepository $repository,
        UtilisateursRepository $utilisateursRepository
    ): Response {
        // Fetch connected user
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }
        if (!$user instanceof Utilisateurs) {
            throw $this->createAccessDeniedException('Vous devez être un partenaire connecté.');
        }
        // Retrieve partner ID associated with the user
        $id_partenaire = $user->getIdUtilisateur();
        // Ajoutez cette ligne pour récupérer tous les utilisateurs
        $utilisateurs = $utilisateursRepository->findAll();

        // Fetch demands for this partner
        $demandesAide = $repository->findByPartenaire($id_partenaire);

        return $this->render('Partenaire/dm_aide_partner/demande_aide.html.twig', [
            'demandes_aide' => $demandesAide,
            'id_partenaire' => $id_partenaire,
            'utilisateurs' => $utilisateurs
            
        ]);
    }

    #[Route('/dashboard/partenaire/demande_aide/update/{id}', name: 'update_demande', methods: ['POST'])]
    public function updateDemande(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        DemandesAideRepository $repository
    ): Response {
        $demande = $repository->find($id);

        if (!$demande) {
            throw $this->createNotFoundException('Demande non trouvée');
        }

        // Update fields
        $data = $request->request->all();
        $file = $request->files->get('preuves');

        $demande->setTypeAide($data['type_aide']);
        $demande->setDescription($data['description']);
        $demande->setMontantDemande($data['montant_demande']);
        $dateDemande = $request->request->get('date_demande');

        if ($dateDemande) {
            try {
                // Convertissez la chaîne en objet DateTime
                $demande->setDateDemande(new \DateTime($dateDemande));
            } catch (\Exception $e) {
                // Gérez l'erreur si la conversion échoue
                $this->addFlash('error', 'La date et l\'heure sont invalides.');
            }
        }

        if ($file) {
            $uploadsDir = $this->getParameter('kernel.project_dir').'/public/uploads/preuves';
            $fileName = uniqid().'.'.$file->guessExtension();
            $file->move($uploadsDir, $fileName);
            $demande->setPreuves($fileName);
        }

        $entityManager->flush();

        $this->addFlash('success', 'Demande mise à jour avec succès !');
        return $this->redirectToRoute('demande_aide', ['id_partenaire' => $demande->getIdPartenaire()]);
    }

    #[Route('/dashboard/partenaire/demande_aide_add/{id_partenaire}', name: 'add_demande', methods: ['POST'])]
    public function addDemande(
        int $id_partenaire,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $data = $request->request->all();
        $file = $request->files->get('preuve');
        $user = $this->getUser(); // Get connected user

        $demande = new DemandesAide();
        $demande->setIdClient($data['id_client']);
        $demande->setIdPartenaire($id_partenaire);
        $demande->setTypeAide($data['type_aide']);
        $demande->setDescription($data['description'] ?? '');
        $demande->setMontantDemande($data['montant_demande'] ?? 0);
        $demande->setDateDemande(new \DateTime($data['date_demande'] ?? 'now'));
        $demande->setStatus('En attente');

        // Handle file upload
        if ($file) {
            $uploadsDir = $this->getParameter('kernel.project_dir').'/public/uploads/preuves';

            // Create directory if it doesn't exist
            if (!file_exists($uploadsDir)) {
                mkdir($uploadsDir, 0777, true);
            }

            $fileName = uniqid().'.'.$file->guessExtension();
            $file->move($uploadsDir, $fileName);
            $demande->setPreuves($fileName);
        }

        if (!$user instanceof Utilisateurs) {
            throw $this->createAccessDeniedException('Vous devez être un partenaire connecté.');
        }
        // Automatically add a notification
        $notification = new Notifications();
        $notification->setMessage("Nouvelle demande d’aide ajoutée.");
        $notification->setDateEnvoi(new DateTime());
        $notification->setTypeNotification('success');
        $notification->setIsRead(false);
        $notification->setIdUtilisateur($user->getIdUtilisateur());

        $entityManager->persist($notification);
        $entityManager->persist($demande);
        $entityManager->flush();

        $this->addFlash('success', 'Demande ajoutée avec succès!');
        return $this->redirectToRoute('demande_aide', ['id_partenaire' => $id_partenaire]);
    }

    #[Route('/dashboard/partenaire/demande_aide/delete/{id}', name: 'delete_demande', methods: ['POST'])]
    public function deleteDemande(
        int $id,
        EntityManagerInterface $entityManager,
        DemandesAideRepository $repository
    ): RedirectResponse {
        $demande = $repository->find($id);

        if (!$demande) {
            $this->addFlash('error', 'Demande non trouvée.');
            return $this->redirectToRoute('demande_aide');
        }

        // Ensure the demand is pending
        if ($demande->getStatus() !== 'En attente') {
            $this->addFlash('error', 'Vous ne pouvez supprimer que les demandes en attente.');
            return $this->redirectToRoute('demande_aide');
        }

        $entityManager->remove($demande);
        $entityManager->flush();

        $this->addFlash('success', 'Demande supprimée avec succès.');
        return $this->redirectToRoute('demande_aide');
    }
}