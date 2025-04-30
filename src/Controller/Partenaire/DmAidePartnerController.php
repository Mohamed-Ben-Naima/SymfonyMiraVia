<?php

namespace App\Controller\Partenaire;
use DateTime;
use App\Entity\Notifications;
use App\Entity\DemandesAide;
use App\Repository\DemandesAideRepository;
use App\Repository\UtilisateursRepository; // Ajoutez ce namespace
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class DmAidePartnerController extends AbstractController
{
    #[Route('/dashboard/partenaire/demande_aide/{id_partenaire}', name: 'demande_aide')]
    public function demandeAide(
        int $id_partenaire,
        DemandesAideRepository $repository,
        UtilisateursRepository $utilisateursRepository // Injection du repository des utilisateurs
    ): Response {
        // Forcer l'ID partenaire à 1 si nécessaire
        $id_partenaire = 1;

        // Récupérer les demandes et les utilisateurs
        $demandesAide = $repository->findByPartenaire($id_partenaire);
        $utilisateurs = $utilisateursRepository->findAllUsers(); // Récupère tous les utilisateurs

        // Exemple : récupération d'un id_client (vous pouvez ajuster cette logique)
        $id_client = 4; // ID client par défaut ou récupéré dynamiquement

        return $this->render('Partenaire/dm_aide_partner/demande_aide.html.twig', [
            'demandes_aide' => $demandesAide,
            'id_partenaire' => $id_partenaire,
            'utilisateurs' => $utilisateurs, // Passez la liste des utilisateurs à Twig
            'id_client' => $id_client, // Passez également l'ID client
        ]);
    }
    #[Route('/dashboard/partenaire/demande_aide/update/{id}', name: 'update_demande', methods: ['POST'])]
    public function updateDemande(int $id, Request $request, EntityManagerInterface $entityManager, DemandesAideRepository $repository): Response
    {
    $demande = $repository->find($id);
    if (!$demande) {
        throw $this->createNotFoundException('Demande non trouvée');
    }

    // Mettre à jour les champs
    $data = $request->request->all();
    $file = $request->files->get('preuves');

    $demande->setTypeAide($data['type_aide']);
    $demande->setDescription($data['description']);
    $demande->setMontantDemande($data['montant_demande']);

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
        EntityManagerInterface $entityManager,
    ): Response {
        $data = $request->request->all();
        $file = $request->files->get('preuve');
        //Récupérer l'utilisateur connecté via la session
        $user = $this->getUser();

        $demande = new DemandesAide();
        $demande->setIdClient($data['id_client']);
        $demande->setIdPartenaire($id_partenaire);
        $demande->setTypeAide($data['type_aide']);
        $demande->setDescription($data['description'] ?? '');
        $demande->setMontantDemande($data['montant_demande'] ?? 0);
        $demande->setDateDemande(new \DateTime($data['date_demande'] ?? 'now'));
        $demande->setStatus('En attente');

       // Gestion du fichier
    if ($file) {
        $uploadsDir = $this->getParameter('kernel.project_dir').'/public/uploads/preuves';
        
        // Créer le dossier s'il n'existe pas
        if (!file_exists($uploadsDir)) {
            mkdir($uploadsDir, 0777, true);
        }

        // Générer un nom de fichier unique
        $fileName = uniqid().'.'.$file->guessExtension();
        
        // Déplacer le fichier
        $file->move($uploadsDir, $fileName);
        
        // Enregistrer uniquement le nom du fichier dans la base
        $demande->setPreuves($fileName);
    }
    //  Ajouter une notification automatiquement
    $notification = new Notifications();
    $notification->setMessage("Nouvelle demande d’aide ajoutée.");
    $notification->setDateEnvoi(new DateTime());
    $notification->setTypeNotification('success'); // ou 'success'
    $notification->setIsRead(false);
    $notification->setIdUtilisateur($user->getIdUtilisateur()); // ID de l'utilisateur connecté

        $entityManager->persist($notification);
        $entityManager->persist($demande);
        $entityManager->flush();
        $this->addFlash('success', 'Demande ajoutée avec succès!');
        return $this->redirectToRoute('demande_aide', ['id_partenaire' => $id_partenaire]);
    }


    #[Route('/dashboard/partenaire/demande_aide/delete/{id}', name: 'delete_demande', methods: ['POST'])]
        public function deleteDemande(int $id, EntityManagerInterface $entityManager, DemandesAideRepository $repository): RedirectResponse
        {
            $demande = $repository->find($id);

            if (!$demande) {
                $this->addFlash('error', 'Demande non trouvée.');
                return $this->redirectToRoute('demande_aide');
            }

            // Vérifier que la demande est en attente
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