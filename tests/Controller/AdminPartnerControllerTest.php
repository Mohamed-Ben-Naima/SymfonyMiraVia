<?php
// tests/Controller/AdminPartnerControllerTest.php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminPartnerControllerTest extends WebTestCase
{
    public function testUpdateExpirationSuccess(): void
    {
        $client = static::createClient();

        $payload = [
            'id' => 1, // suppose que ce partenaire existe
            'expirationDate' => (new \DateTime('+5 days'))->format('Y-m-d'),
        ];

        $client->request('POST', '/admin/partner/update-expiration', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Date d\'expiration mise à jour avec succès.', $responseData['message']);
    }

    public function testUpdateExpirationWithPastDate(): void
    {
        $client = static::createClient();

        $payload = [
            'id' => 1,
            'expirationDate' => (new \DateTime('-2 days'))->format('Y-m-d'),
        ];

        $client->request('POST', '/admin/partner/update-expiration', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(400);

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('La date d\'expiration doit être postérieure ou égale à aujourd\'hui.', $responseData['error']);
    }

    public function testUpdateExpirationWithInvalidData(): void
    {
        $client = static::createClient();

        $payload = [ /* missing 'id' and 'expirationDate' */ ];

        $client->request('POST', '/admin/partner/update-expiration', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(400);

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Données invalides', $responseData['error']);
    }
}
