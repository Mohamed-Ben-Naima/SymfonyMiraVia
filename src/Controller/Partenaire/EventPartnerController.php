<?php

namespace App\Controller\Partenaire;

use App\Entity\Evenements;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EventPartnerController extends AbstractController
{
    #[Route('/event/partner', name: 'app_event_partner')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Fetch events from the database
        $evenements = $entityManager->getRepository(Evenements::class)->findAll();

        // Pass events to the template
        return $this->render('Partenaire/event_partner/event.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    // Route to edit an event
    #[Route('/partenaire/evenement/edit/{id}', name: 'edit_event')]
    public function edit(int $id, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Fetch the event by its ID
        $event = $entityManager->getRepository(Evenements::class)->find($id);

        if (!$event) {
            throw $this->createNotFoundException('Événement introuvable.');
        }

        // Handle form submission (this is just an example, you should create a form)
        if ($request->isMethod('POST')) {
            $event->setTitre($request->request->get('titre'));
            $entityManager->flush();

            $this->addFlash('success', 'Événement modifié avec succès.');
            return $this->redirectToRoute('app_event_partner'); // Redirect to the event list
        }

        return $this->render('Partenaire/event_partner/edit.html.twig', [
            'event' => $event,
        ]);
    }
}