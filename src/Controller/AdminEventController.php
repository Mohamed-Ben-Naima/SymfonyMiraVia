<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\Evenements;
use App\Entity\Utilisateurs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class AdminEventController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private MailerInterface $mailer;

    // Inject EntityManagerInterface and MailerInterface via the constructor
    public function __construct(EntityManagerInterface $entityManager, MailerInterface $mailer)
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
    }

    #[Route('/admin/event', name: 'app_admin_events')]
    public function index(): Response
    {
        // Mettre à jour les statuts des événements
        $this->updateEventStatus();

        // Requête DQL pour récupérer les événements avec leurs relations
        $query = $this->entityManager->createQuery(
            'SELECT e.idEvenement, e.titre, e.description, e.dateEvenement, e.lieu, 
                    e.status, e.estBloque, e.imagename, 
                    u.nom AS nomPartenaire, u.prenom AS prenomPartenaire
             FROM App\Entity\Evenements e
             LEFT JOIN App\Entity\Partenaires p WITH e.idPartenaire = p.utilisateur
             LEFT JOIN App\Entity\Utilisateurs u WITH p.utilisateur = u.idUtilisateur'
        );

        $evenements = $query->getResult();

        return $this->render('admin_event/index.html.twig', [
            'controller_name' => 'AdminEventController',
            'pageTitle' => 'Evénement',
            'evenements' => $evenements, // Passer les événements au template
        ]);
    }

    /**
     * Met à jour les statuts des événements selon leur date.
     */
    private function updateEventStatus()
    {
        $events = $this->entityManager->getRepository(Evenements::class)->findAll();
        $currentDate = new \DateTime(); // Date et heure actuelle

        foreach ($events as $event) {
            // Vérifier si l'événement est passé et mettre à jour son statut
            if ($event->getDateEvenement() < $currentDate && $event->getStatus() !== 'Terminé') {
                $event->setStatus('Terminé');
                $this->entityManager->persist($event);
            }
        }

        $this->entityManager->flush(); // Sauvegarder les changements
    }

    #[Route('/admin/event/block', name: 'block_event', methods: ['POST'])]
    public function blockEvent(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $eventId = $data['id'] ?? null;
        $reasons = $data['reasons'] ?? [];
        $explanation = $data['explanation'] ?? '';

        if (!$eventId || empty($reasons) || empty($explanation)) {
            return new JsonResponse(['success' => false, 'message' => 'Données invalides.'], 400);
        }

        try {
            $reasonsString = implode(', ', $reasons);

            // Fetch the event from the database
            $event = $this->entityManager->getRepository(Evenements::class)->find($eventId);
            if (!$event) {
                return new JsonResponse(['success' => false, 'message' => 'Évènement non trouvé.'], 404);
            }

            // Update the event fields
            $event->setEstBloque(true);
            $event->setRaisonsBlocage($reasonsString);
            $event->setExplicationBlocage($explanation);
            $event->setStatus('Annulé'); // Modifier le statut en Annulé

            $this->entityManager->persist($event);
            $this->entityManager->flush();

            // Get the partner's email
            $partner = $this->entityManager->getRepository(Utilisateurs::class)->find($event->getIdPartenaire());
            if ($partner && $partner->getEmail()) {
                // Render email content using Twig
                $emailContent = $this->renderView('emails/event_notification.html.twig', [
                    'prenom' => $partner->getPrenom(),
                    'nom' => $partner->getNom(),
                    'titre' => $event->getTitre(),
                    'status' => 'bloqué',
                    'raisons' => $reasonsString,
                    'explication' => $explanation,
                ]);

                // Send email notification
                $email = (new Email())
                    ->from('admin@example.com')
                    ->to($partner->getEmail())
                    ->subject('Évènement bloqué')
                    ->html($emailContent);
                $this->mailer->send($email);
            }

            return new JsonResponse(['success' => true, 'message' => 'Évènement bloqué avec succès.']);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Erreur lors du blocage de l\'évènement.'], 500);
        }
    }

#[Route('/admin/event/unblock', name: 'unblock_event', methods: ['POST'])]
public function unblockEvent(Request $request): JsonResponse
{
    $data = json_decode($request->getContent(), true);
    $eventId = $data['id'] ?? null;

    if (!$eventId) {
        return new JsonResponse(['success' => false, 'message' => 'ID de l\'évènement manquant.'], 400);
    }

    try {
        // Récupérer l'évènement depuis la base de données
        $event = $this->entityManager->getRepository(Evenements::class)->find($eventId);
        if (!$event) {
            return new JsonResponse(['success' => false, 'message' => 'Évènement non trouvé.'], 404);
        }

        // Mettre à jour l'état de l'évènement
        $event->setEstBloque(false); // Désactiver le blocage
        $event->setRaisonsBlocage(null); // Réinitialiser les raisons
        $event->setExplicationBlocage(null); // Réinitialiser l'explication
        $event->setStatus('En cours'); // Modifier le statut en En cours

        $this->entityManager->persist($event);
        $this->entityManager->flush();

        // Get the partner's email
        $partner = $this->entityManager->getRepository(Utilisateurs::class)->find($event->getIdPartenaire());
        if ($partner && $partner->getEmail()) {
            // Generate email content using Twig template
            $htmlContent = $this->renderView('emails/unblock_event_email.twig', [
                'partner' => $partner,
                'event' => $event,
            ]);

            // Send email notification
            $email = (new Email())
                ->from('admin@example.com')
                ->to($partner->getEmail())
                ->subject('Évènement débloqué')
                ->html($htmlContent);

            $this->mailer->send($email);
        }

        return new JsonResponse(['success' => true, 'message' => 'Évènement activé avec succès.']);
    } catch (\Exception $e) {
        return new JsonResponse(['success' => false, 'message' => 'Erreur lors de l\'activation de l\'évènement.'], 500);
    }
}
}
