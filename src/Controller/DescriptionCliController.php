<?php
// src/Controller/DescriptionCliController.php

namespace App\Controller;

use App\Entity\Evenements;
use App\Entity\Partenaires; // Make sure this entity is correctly mapped
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route; // For PHP 8+ attributes
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException; // For 404 errors

class DescriptionCliController extends AbstractController
{
    /**
     * Displays the details of a specific event.
     * The route now accepts an {id} parameter which is the ID of the event.
     */
    #[Route('/description/{id}', name: 'app_description', requirements: ['id' => '\d+'])]
    public function descriptionPage(
        int $id, // Symfony will automatically try to convert the {id} from the URL to an integer
        EntityManagerInterface $entityManager // Service for database interaction
    ): Response {
        // 1. Fetch the Evenements entity from the database using the provided ID
        $event = $entityManager->getRepository(Evenements::class)->find($id);

        // 2. If the event is not found, throw a 404 Not Found exception
        if (!$event) {
            throw $this->createNotFoundException('L\'événement demandé n\'a pas été trouvé.');
        }

        // 3. Fetch the associated Partenaires entity
        $partner = null; // Initialize partner as null
        if ($event->getIdPartenaire()) {
            // Assuming getIdPartenaire() returns the ID that can be used to find a Partenaire.
            // If Partenaires primary key is linked to Utilisateurs.id_utilisateur and
            // Evenements.id_partenaire stores this Utilisateurs.id_utilisateur, this should work.
            $partner = $entityManager->getRepository(Partenaires::class)->find($event->getIdPartenaire());
            
            // Optional: If a partner ID is set on the event but the partner is not found,
            // you might want to log this or handle it, but for now, $partner will remain null
            // if not found, and the Twig template will handle it.
        }

        // 4. Render the Twig template, passing the event and partner objects
        return $this->render('description/description.html.twig', [
            'event' => $event,
            'partner' => $partner,
        ]);
    }
}