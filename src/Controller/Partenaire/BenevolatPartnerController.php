<?php

namespace App\Controller\Partenaire;
use App\Entity\Utilisateurs;
use App\Entity\Benevolat;
use App\Entity\Notifications;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\BenevolatRepository;
use App\Repository\UtilisateursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;


final class BenevolatPartnerController extends AbstractController
{
    #[Route('/dashboard/partenaire/benevolats', name: 'app_benevolat_partner')]

    public function Benevolat(
        UtilisateursRepository $utilisateursRepository,
        BenevolatRepository $repository,
        
    ): Response {
        $user = $this->getUser();

        if (!$user instanceof Utilisateurs) {
            throw $this->createAccessDeniedException('Vous devez être un partenaire connecté.');
        }
        
        $id_partenaire = $user->getIdUtilisateur();
        $benevolat = $repository->findByPartnerId($id_partenaire);
        

        return $this->render('Partenaire/benevolat_partner/benevolat.html.twig', [
            'benevolats' => $benevolat,
            'id_partenaire' => $id_partenaire,
            
        ]);
    }

    #[Route('/dashboard/partenaire/benevolats/ajouter', name: 'add_benevolat', methods: ['POST', 'GET'])]
    public function addBenevolat(Request $request, EntityManagerInterface $entityManager,UtilisateursRepository $utilisateursRepository
    ): Response
    {
        $user = $this->getUser();

        if (!$user instanceof Utilisateurs) {
            throw $this->createAccessDeniedException('Vous devez être un partenaire connecté.');
        }

        if ($request->isMethod('POST')) {
            $data = $request->request->all();
            dump($data); // Log pour vérifier les données du formulaire

            if (empty($data['titre']) || empty($data['description']) || empty($data['date_benevolat']) || empty($data['lieu'])) {
                $this->addFlash('error', 'Tous les champs sont requis.');
                return $this->redirectToRoute('app_benevolat_partner');
            }

            $benevolat = new Benevolat();
            $benevolat->setTitre($data['titre']);
            $benevolat->setDescription($data['description']);
            $benevolat->setDateBenevolat(new \DateTime($data['date_benevolat']));
            $benevolat->setLieu($data['lieu']);
            $benevolat->setIdPartenaire($user->getIdUtilisateur());
            $benevolat->setStatus($data['status'] ?? 'En cours');

            $file = $request->files->get('imageName');
            if ($file) {
                $fileName = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('benevolat_images_directory'), $fileName);
                $benevolat->setImageName($fileName);
            } else {
                $this->addFlash('error', 'Veuillez ajouter une image.');
                return $this->redirectToRoute('app_benevolat_partner');
            }
             // Notifications
        $notification = new Notifications();
        $notification->setMessage("Nouveau bénévolat ajouté.");
        $notification->setDateEnvoi(new DateTime());
        $notification->setTypeNotification('success');
        $notification->setIsRead(false);
        $notification->setIdUtilisateur($user->getIdUtilisateur());
        $entityManager->persist($notification);

        $admins = $utilisateursRepository->findAllAdmins();
        foreach ($admins as $admin) {
            $adminNotif = new Notifications();
            $adminNotif->setMessage(" Nouveau bénévolat ajouté par " . $user->getNom()." " . $user->getPrenom());
            $adminNotif->setDateEnvoi(new DateTime());
            $adminNotif->setTypeNotification('success');
            $adminNotif->setIsRead(false);
            $adminNotif->setIdUtilisateur($admin->getIdUtilisateur());
            $entityManager->persist($adminNotif);
        }

            $entityManager->persist($benevolat);
            $entityManager->flush();

            $this->addFlash('success', 'Bénévolat ajouté avec succès.');
            return $this->redirectToRoute('app_benevolat_partner');
        }

    return $this->render('Partenaire/benevolat_partner/add.html.twig');
    }

    #[Route('/dashboard/partenaire/benevolats/modifier/{id}', name: 'edit_benevolat', methods: ['POST', 'GET'])]
        public function editBenevolat(Request $request, Benevolat $benevolat, EntityManagerInterface $entityManager,  UtilisateursRepository $utilisateursRepository  ): Response
        {   
            $user = $this->getUser();
            if (!$user instanceof Utilisateurs) {
                throw $this->createAccessDeniedException('Vous devez être un partenaire connecté.');
            }

            if ($request->isMethod('POST')) {
                $data = $request->request->all();

                // Mise à jour des champs
                $benevolat->setTitre($data['titre']);
                $benevolat->setDescription($data['description']);
                $benevolat->setDateBenevolat(new \DateTime($data['dateBenevolat']));
                $benevolat->setLieu($data['lieu']);

                // Gestion de l'upload de l'image
                $file = $request->files->get('imageName');
                if ($file) {
                    $fileName = uniqid() . '.' . $file->guessExtension();
                    $file->move($this->getParameter('benevolat_images_directory'), $fileName);
                    $benevolat->setImageName($fileName);
                }
                // Notifications
                $notification = new Notifications();
                $notification->setMessage(" Bénévolat modifié .");
                $notification->setDateEnvoi(new DateTime());
                $notification->setTypeNotification('warning');
                $notification->setIsRead(false);
                $notification->setIdUtilisateur($user->getIdUtilisateur());
                $entityManager->persist($notification);

                $admins = $utilisateursRepository->findAllAdmins();
                foreach ($admins as $admin) {
                    $adminNotif = new Notifications();
                    $adminNotif->setMessage("Bénévolat modifié par " . $user->getNom()." " . $user->getPrenom());
                    $adminNotif->setDateEnvoi(new DateTime());
                    $adminNotif->setTypeNotification('warning');
                    $adminNotif->setIsRead(false);
                    $adminNotif->setIdUtilisateur($admin->getIdUtilisateur());
                    $entityManager->persist($adminNotif);
                }

                // Enregistrer les modifications
                $entityManager->flush();

                $this->addFlash('success', 'Bénévolat modifié avec succès.');
                return $this->redirectToRoute('app_benevolat_partner');
            }

            return $this->render('Partenaire/benevolat_partner/edit.html.twig', [
                'benevolat' => $benevolat,
            ]);
        }
    
        #[Route('/dashboard/partenaire/benevolat/upload-image/{id}', name: 'upload_benevolat_image', methods: ['POST'])]
        public function uploadImage(Request $request, int $id, EntityManagerInterface $entityManager,        UtilisateursRepository $utilisateursRepository
        ): Response
        {
            $user = $this->getUser();
            $benevolat = $entityManager->getRepository(Benevolat::class)->find($id);

            if (!$benevolat) {
                return $this->json(['error' => 'Bénévolat introuvable.'], Response::HTTP_NOT_FOUND);
            }

            if (!$user instanceof Utilisateurs || $benevolat->getIdPartenaire() !== $user->getIdUtilisateur()) {
                return $this->json(['error' => 'Accès refusé.'], Response::HTTP_FORBIDDEN);
            }

            $file = $request->files->get('image');
            if ($file) {
                $fileName = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('benevolat_images_directory'), $fileName);

                // Supprimez l'ancienne image si elle existe
                if ($benevolat->getImageName()) {
                    $oldImagePath = $this->getParameter('benevolat_images_directory') . '/' . $benevolat->getImageName();
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $benevolat->setImageName($fileName);
                $entityManager->flush();

                // Retourner le chemin de la nouvelle image
                return $this->json(['success' => true, 'fileName' => $fileName], Response::HTTP_OK);
            }

            return $this->json(['error' => 'Aucun fichier reçu.'], Response::HTTP_BAD_REQUEST);
        }
        
        #[Route('/dashboard/partenaire/benevolats/supprimer/{id}', name: 'delete_benevolat', methods: ['GET', 'DELETE', 'POST'])]
        public function deleteBenevolat(
            Request $request, 
            BenevolatRepository $benevolatRepository, 
            UtilisateursRepository $utilisateursRepository ,
            EntityManagerInterface $entityManager, $id): Response
        {
        $user = $this->getUser();
        $benevolat = $entityManager->getRepository(Benevolat::class)->find($id);

        if (!$benevolat) {
            $this->addFlash('error', 'Événement introuvable.');
            return $this->redirectToRoute('app_benevolat_partner');
        }

        if (!$user instanceof Utilisateurs || $benevolat->getIdPartenaire() !== $user->getIdUtilisateur()) {
            $this->addFlash('error', 'Accès refusé.');
            return $this->redirectToRoute('app_benevolat_partner');
        }
         // Notifications
         $notification = new Notifications();
         $notification->setMessage(" Bénévolat supprimé.");
         $notification->setDateEnvoi(new DateTime());
         $notification->setTypeNotification('danger');
         $notification->setIsRead(false);
         $notification->setIdUtilisateur($user->getIdUtilisateur());
         $entityManager->persist($notification);
 
         $admins = $utilisateursRepository->findAllAdmins();
         foreach ($admins as $admin) {
             $adminNotif = new Notifications();
             $adminNotif->setMessage("Bénévolat supprimé par " . $user->getNom()." " . $user->getPrenom());
             $adminNotif->setDateEnvoi(new DateTime());
             $adminNotif->setTypeNotification('danger');
             $adminNotif->setIsRead(false);
             $adminNotif->setIdUtilisateur($admin->getIdUtilisateur());
             $entityManager->persist($adminNotif);
         }

        // Supprimez l'événement
        $entityManager->remove($benevolat);
        $entityManager->flush();

        $this->addFlash('success', 'benevolat supprimé avec succès.');
        return $this->redirectToRoute('app_benevolat_partner');
    }
}
