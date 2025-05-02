<?php

namespace App\Controller\Partenaire;

use App\Entity\Evenements;
use App\Entity\Utilisateurs;
use App\Repository\EvenementsRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UtilisateursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EventPartnerController extends AbstractController
{
    #[Route('/dashboard/partenaire/event_partner', name: 'app_event_partner')]
    public function don(
        EvenementsRepository $repository,
        
    ): Response {
        $user = $this->getUser();

        if (!$user instanceof Utilisateurs) {
            throw $this->createAccessDeniedException('Vous devez être un partenaire connecté.');
        }
        
        $id_partenaire = $user->getIdUtilisateur();
        $evenemet = $repository->findByPartnerId($id_partenaire);
        

        return $this->render('Partenaire/event_partner/event.html.twig', [
            'evenements' => $evenemet,
            'id_partenaire' => $id_partenaire,
            
        ]);
    }

    #[Route('/dashboard/partenaire/evenement/ajouter', name: 'add_event', methods: ['POST', 'GET'])]
    public function addEvent(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user instanceof Utilisateurs) {
            throw $this->createAccessDeniedException('Vous devez être un partenaire connecté.');
        }

        if ($request->isMethod('POST')) {
            $data = $request->request->all();

            // Validate the required fields
            if (empty($data['titre']) || empty($data['description']) || empty($data['date_evenement']) || empty($data['lieu'])) {
                $this->addFlash('error', 'Tous les champs sont requis.');
                return $this->redirectToRoute('app_event_partner');
            }

            // Create and persist the new event
            $event = new Evenements();
            $event->setTitre($data['titre']);
            $event->setDescription($data['description']);
            $event->setDateEvenement(new \DateTime($data['date_evenement']));
            $event->setLieu($data['lieu']);
            $event->setIdPartenaire($user->getIdUtilisateur());
            $event->setStatus($data['status'] ?? 'En cours');

            // Handle file upload for the image
            if ($request->files->get('imageName')) {
                $file = $request->files->get('imageName');
                $fileName = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('event_images_directory'), $fileName);
                $event->setImageName($fileName);
            }

            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'Événement ajouté avec succès.');
            return $this->redirectToRoute('app_event_partner');
        }

        return $this->render('Partenaire/event_partner/add.html.twig');
    }

    // Route to edit an event
    #[Route('/dashboard/partenaire/evenement/edit/{id}', name: 'edit_event')]
    public function edit(int $id, EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $this->getUser();
        $event = $entityManager->getRepository(Evenements::class)->find($id);

        if (!$event) {
            throw $this->createNotFoundException('Événement introuvable.');
        }

        if (!$user instanceof Utilisateurs) {
            throw $this->createAccessDeniedException('Vous devez être un partenaire connecté.');
        }

        if ($event->getIdPartenaire() !== $user->getIdUtilisateur()) {
            throw $this->createAccessDeniedException('Vous ne pouvez modifier que vos propres événements.');
        }

        if ($request->isMethod('POST')) {
            $data = $request->request->all();

            $event->setTitre($data['titre'] ?? $event->getTitre());
            $event->setDescription($data['description'] ?? $event->getDescription());
            $event->setDateEvenement(new \DateTime($data['date_evenement'] ?? $event->getDateEvenement()->format('Y-m-d H:i:s')));
            $event->setLieu($data['lieu'] ?? $event->getLieu());
            $event->setStatus($data['status'] ?? $event->getStatus());

            if ($request->files->get('imageName')) {
                $file = $request->files->get('imageName');
                $fileName = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('event_images_directory'), $fileName);

                if ($event->getImageName()) {
                    $oldImagePath = $this->getParameter('event_images_directory') . '/' . $event->getImageName();
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $event->setImageName($fileName);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Événement modifié avec succès.');
            return $this->redirectToRoute('app_event_partner');
        }

        return $this->render('Partenaire/event_partner/edit.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/dashboard/partenaire/evenement/upload-image/{id}', name: 'upload_event_image', methods: ['POST'])]
    public function uploadImage(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $event = $entityManager->getRepository(Evenements::class)->find($id);

        if (!$event) {
            return $this->json(['error' => 'Événement introuvable.'], Response::HTTP_NOT_FOUND);
        }

        if (!$user instanceof Utilisateurs || $event->getIdPartenaire() !== $user->getIdUtilisateur()) {
            return $this->json(['error' => 'Accès refusé.'], Response::HTTP_FORBIDDEN);
        }

        $file = $request->files->get('imageName');
        if ($file) {
            $fileName = uniqid() . '.' . $file->guessExtension();
            $file->move($this->getParameter('event_images_directory'), $fileName);

            // Supprimez l'ancienne image si elle existe
            if ($event->getImageName()) {
                $oldImagePath = $this->getParameter('event_images_directory') . '/' . $event->getImageName();
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $event->setImageName($fileName);
            $entityManager->flush();

            // Retourner le chemin de la nouvelle image
            return $this->json(['success' => true, 'imagePath' => '/uploads/event_images/' . $fileName]);
        }

        return $this->json(['error' => 'Aucun fichier reçu.'], Response::HTTP_BAD_REQUEST);
    }
    #[Route('/dashboard/partenaire/evenement/supprimer/{id}', name: 'delete_event', methods: ['GET', 'DELETE'])]
    public function deleteEvent(int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $event = $entityManager->getRepository(Evenements::class)->find($id);

        if (!$event) {
            $this->addFlash('error', 'Événement introuvable.');
            return $this->redirectToRoute('app_event_partner');
        }

        if (!$user instanceof Utilisateurs || $event->getIdPartenaire() !== $user->getIdUtilisateur()) {
            $this->addFlash('error', 'Accès refusé.');
            return $this->redirectToRoute('app_event_partner');
        }

        // Supprimez l'événement
        $entityManager->remove($event);
        $entityManager->flush();

        $this->addFlash('success', 'Événement supprimé avec succès.');
        return $this->redirectToRoute('app_event_partner');
    }
}