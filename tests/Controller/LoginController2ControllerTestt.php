<?php
// tests/Controller/LoginController2ControllerTest.php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

// ... (use statements from Step 2)

class LoginController2ControllerTestt extends WebTestCase
{
    public function testLoginPageLoads(): void
    {
        // 1. Create a client simulating a browser
        $client = static::createClient();

        // 2. Request the login page URL ('/login') via GET method
        $crawler = $client->request('GET', '/login'); // Matches #[Route('/login', name: 'app_login_controller2')]

        // 3. Assert the response was successful (HTTP status code 200)
        $this->assertResponseIsSuccessful();

        // 4. Assert that key elements of the login form are present
        $this->assertSelectorTextContains('h1', 'Connexion à votre compte'); // Check the main title
        $this->assertSelectorExists('form[method="post"]', 'Login form should exist');
        $this->assertSelectorExists('input[name="_username"]', 'Username (email) input should exist');
        $this->assertSelectorExists('input[name="_password"]', 'Password input should exist');
        $this->assertSelectorExists('input[name="_csrf_token"]', 'CSRF token input should exist');
        $this->assertSelectorExists('button[type="submit"]', 'Submit button should exist');
    }

    // ... other test methods
}