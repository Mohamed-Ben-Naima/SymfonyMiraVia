<?php

namespace App\Tests\Controller;

use App\Entity\DemandePartenaire;
use App\Entity\Utilisateurs;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mailer\Test\Transport\InMemoryTransport;

class AdminDemandePartnerControllerTest extends WebTestCase
{
    public function testAccepterDemandePartenaire(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        $entityManager = $container->get('doctrine')->getManager();

        // Créer un utilisateur de test
        $utilisateur = new Utilisateurs();
        $utilisateur->setEmail('test@demo.com');
        $utilisateur->setStatus('inactif');
        $utilisateur->setStatutVerification('Non vérifié');
        $entityManager->persist($utilisateur);

        // Créer une demande partenaire associée à l'utilisateur
        $demande = new DemandePartenaire();
        $demande->setEmail('test@demo.com');
        $demande->setEtat('En attente');
        $demande->setDemandeur('John Doe');
        $entityManager->persist($demande);

        $entityManager->flush();

        // Simuler l'envoi d'une requête POST pour accepter la demande
        $client->request(
            'POST',
            '/admin/demande/partner/update-status',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'id' => $demande->getId(),
                'etat' => 'Acceptée',
            ])
        );

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertStringContainsString('Statut mis à jour avec succès', $response->getContent());

        // Rafraîchir les entités pour vérifier les changements
        $entityManager->refresh($demande);
        $entityManager->refresh($utilisateur);

        $this->assertEquals('Acceptée', $demande->getEtat());
        $this->assertEquals('actif', $utilisateur->getStatus());
        $this->assertEquals('Actif', $utilisateur->getStatutVerification());

        // Vérifier si un email a été envoyé (nécessite Mailer testing via in-memory transport)
        /** @var InMemoryTransport $transport */
        $transport = $container->get('mailer.transport');
        $this->assertCount(1, $transport->getSentEmails());

        /** @var Email $email */
        $email = $transport->getSentEmails()[0];
        $this->assertEquals('Votre demande a été acceptée', $email->getSubject());
        $this->assertEquals('test@demo.com', $email->getTo()[0]->getAddress());
    }
}
