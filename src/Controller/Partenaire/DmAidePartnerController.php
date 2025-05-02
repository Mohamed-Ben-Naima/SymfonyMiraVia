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
        $user = $this->getUser();

        if (!$user instanceof Utilisateurs) {
            throw $this->createAccessDeniedException('Vous devez être un partenaire connecté.');
        }
        
        $id_partenaire = $user->getIdUtilisateur();
        $demandesAide = $repository->findByPartenaire($id_partenaire);
        $clients = $utilisateursRepository->findAllClients();

        return $this->render('Partenaire/dm_aide_partner/demande_aide.html.twig', [
            'demandes_aide' => $demandesAide,
            'id_partenaire' => $id_partenaire,
            'utilisateurs' => $clients
        ]);
    }

    #[Route('/dashboard/partenaire/demande_aide/update/{id}', name: 'update_demande', methods: ['POST'])]
    public function updateDemande(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        DemandesAideRepository $repository,
        UtilisateursRepository $utilisateursRepository
    ): Response {
        $user = $this->getUser();
        $demande = $repository->find($id);

        if (!$demande) {
            throw $this->createNotFoundException('Demande non trouvée.');
        }

        $data = $request->request->all();
        $file = $request->files->get('preuves');

        $demande->setTypeAide($data['type_aide']);
        $demande->setDescription($data['description']);
        $demande->setMontantDemande($data['montant_demande']);

        $dateDemande = $request->request->get('date_demande');
        if ($dateDemande) {
            try {
                $demande->setDateDemande(new \DateTime($dateDemande));
            } catch (\Exception $e) {
                $this->addFlash('error', 'La date et l\'heure sont invalides.');
            }
        }

        if ($file) {
            $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/preuves';
            $fileName = uniqid() . '.' . $file->guessExtension();
            $file->move($uploadsDir, $fileName);
            $demande->setPreuves($fileName);
        }

        $entityManager->flush();

        if (!$user instanceof Utilisateurs) {
            throw $this->createAccessDeniedException('Vous devez être un partenaire connecté.');
        }

        // Notifications
        $notification = new Notifications();
        $notification->setMessage("Demande d’aide modifiée.");
        $notification->setDateEnvoi(new DateTime());
        $notification->setTypeNotification('warning');
        $notification->setIsRead(false);
        $notification->setIdUtilisateur($user->getIdUtilisateur());
        $entityManager->persist($notification);

        $admins = $utilisateursRepository->findAllAdmins();
        foreach ($admins as $admin) {
            $adminNotif = new Notifications();
            $adminNotif->setMessage("Demande d’aide modifiée.");
            $adminNotif->setDateEnvoi(new DateTime());
            $adminNotif->setTypeNotification('warning');
            $adminNotif->setIsRead(false);
            $adminNotif->setIdUtilisateur($admin->getIdUtilisateur());
            $entityManager->persist($adminNotif);
        }

        $entityManager->flush();
        $this->addFlash('success', 'Demande mise à jour avec succès !');

        return $this->redirectToRoute('demande_aide', ['id_partenaire' => $demande->getIdPartenaire()]);
    }

        #[Route('/dashboard/partenaire/demande_aide_add', name: 'add_demande', methods: ['POST'])]
        public function addDemande(
            Request $request,
            EntityManagerInterface $entityManager,
            UtilisateursRepository $utilisateursRepository
        ): Response {
            $user = $this->getUser();

            if (!$user instanceof Utilisateurs) {
                throw $this->createAccessDeniedException('Vous devez être un partenaire connecté.');
            }

            $id_partenaire = $user->getIdUtilisateur();

            $data = $request->request->all();
            $file = $request->files->get('preuve');

            $demande = new DemandesAide();
            $demande->setIdClient($data['id_client']);
            $demande->setIdPartenaire($id_partenaire);
            $demande->setTypeAide($data['type_aide']);
            $demande->setDescription($data['description'] ?? '');
            $demande->setMontantDemande($data['montant_demande'] ?? 0);
            $demande->setDateDemande(new DateTime($data['date_demande'] ?? 'now'));
            $demande->setStatus('En attente');

            if ($file) {
                // Vérifiez si le fichier est bien un PDF
                if ($file->getMimeType() !== 'application/pdf') {
                    $this->addFlash('error', 'Seuls les fichiers PDF sont acceptés.');
                    return $this->redirectToRoute('demande_aide');
                }
            
                $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/preuves';
                if (!file_exists($uploadsDir)) {
                    mkdir($uploadsDir, 0777, true);
                }
                $fileName = uniqid() . '.' . $file->guessExtension();
                $file->move($uploadsDir, $fileName);
                $demande->setPreuves($fileName);
            } else {
                $this->addFlash('error', 'Veuillez ajouter une preuve au format PDF.');
                return $this->redirectToRoute('demande_aide');
            }

            $entityManager->persist($demande);

            // Notifications
            $notification = new Notifications();
            $notification->setMessage("Nouvelle demande d’aide ajoutée.");
            $notification->setDateEnvoi(new DateTime());
            $notification->setTypeNotification('success');
            $notification->setIsRead(false);
            $notification->setIdUtilisateur($user->getIdUtilisateur());
            $entityManager->persist($notification);

            $admins = $utilisateursRepository->findAllAdmins();
            foreach ($admins as $admin) {
                $adminNotif = new Notifications();
                $adminNotif->setMessage("Nouvelle demande d’aide ajoutée.");
                $adminNotif->setDateEnvoi(new DateTime());
                $adminNotif->setTypeNotification('success');
                $adminNotif->setIsRead(false);
                $adminNotif->setIdUtilisateur($admin->getIdUtilisateur());
                $entityManager->persist($adminNotif);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Demande ajoutée avec succès !');

            return $this->redirectToRoute('demande_aide');
        }

    #[Route('/dashboard/partenaire/demande_aide/delete/{id}', name: 'delete_demande', methods: ['POST'])]
    public function deleteDemande(
        int $id,
        EntityManagerInterface $entityManager,
        DemandesAideRepository $repository,
        UtilisateursRepository $utilisateursRepository
    ): RedirectResponse {
        $user = $this->getUser();
        $demande = $repository->find($id);

        if (!$demande) {
            $this->addFlash('error', 'Demande non trouvée.');
            return $this->redirectToRoute('demande_aide');
        }

        if ($demande->getStatus() !== 'En attente') {
            $this->addFlash('error', 'Vous ne pouvez supprimer que les demandes en attente.');
            return $this->redirectToRoute('demande_aide');
        }

        $entityManager->remove($demande);
        if (!$user instanceof Utilisateurs) {
            throw $this->createAccessDeniedException('Vous devez être un partenaire connecté.');
        }

        // Notifications
        $notification = new Notifications();
        $notification->setMessage("Demande d’aide supprimée.");
        $notification->setDateEnvoi(new DateTime());
        $notification->setTypeNotification('danger');
        $notification->setIsRead(false);
        $notification->setIdUtilisateur($user->getIdUtilisateur());
        $entityManager->persist($notification);

        $admins = $utilisateursRepository->findAllAdmins();
        foreach ($admins as $admin) {
            $adminNotif = new Notifications();
            $adminNotif->setMessage("Demande d’aide supprimée.");
            $adminNotif->setDateEnvoi(new DateTime());
            $adminNotif->setTypeNotification('danger');
            $adminNotif->setIsRead(false);
            $adminNotif->setIdUtilisateur($admin->getIdUtilisateur());
            $entityManager->persist($adminNotif);
        }

        $entityManager->flush();

        $this->addFlash('success', 'Demande supprimée avec succès.');

        return $this->redirectToRoute('demande_aide');
    }
}