<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Evenements;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminEventController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    // Inject EntityManagerInterface via the constructor
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin/event', name: 'app_admin_events')]
    public function index(): Response
    {
        // Custom DQL query to join Evenements, Partenaires, and Utilisateurs
        $query = $this->entityManager->createQuery(
            'SELECT e.idEvenement, e.titre, e.description, e.dateEvenement, e.lieu, 
                    e.status, e.estBloque, e.imagename, 
                    u.nom AS nomPartenaire, u.prenom AS prenomPartenaire
             FROM App\Entity\Evenements e
             LEFT JOIN App\Entity\Partenaires p WITH e.idPartenaire = p.idPartenaire
             LEFT JOIN App\Entity\Utilisateurs u WITH p.idPartenaire = u.idUtilisateur'
        );

        $evenements = $query->getResult();

        return $this->render('admin_event/index.html.twig', [
            'controller_name' => 'AdminEventController',
            'pageTitle' => 'Evénement',
            'evenements' => $evenements, // Pass the events data to the template
        ]);
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
    
            $this->entityManager->persist($event);
            $this->entityManager->flush();
    
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

        $this->entityManager->persist($event);
        $this->entityManager->flush();

        return new JsonResponse(['success' => true, 'message' => 'Évènement activé avec succès.']);
    } catch (\Exception $e) {
        return new JsonResponse(['success' => false, 'message' => 'Erreur lors de l\'activation de l\'évènement.'], 500);
    }
}
}