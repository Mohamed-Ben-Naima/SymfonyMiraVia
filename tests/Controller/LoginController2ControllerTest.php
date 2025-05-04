<?php
// tests/Controller/LoginController2ControllerTest.php

namespace App\Tests\Controller;

use App\Entity\Utilisateurs; // Make sure this matches the namespace of your User entity
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; // Needed to create a test user

class LoginController2ControllerTest extends WebTestCase // Renamed class back (removed extra 't')
{
    /**
     * Test 1: Ensure the login page loads correctly.
     * This is a basic sanity check.
     */
    public function testLoginPageLoads(): void
    {
        // 1. Create a client simulating a browser
        $client = static::createClient();

        // 2. Request the login page URL ('/login') via GET method
        // Assumes your login route name 'app_login_controller2' maps to '/login' path
        $crawler = $client->request('GET', '/login');

        // 3. Assert the response was successful (HTTP status code 200)
        $this->assertResponseIsSuccessful();

        // 4. Assert that key elements of the login form are present based on your Twig template
        $this->assertSelectorTextContains('h1', 'Connexion à votre compte'); // Check the main title
        $this->assertSelectorExists('form[method="post"]', 'Login form should exist');
        // Use the 'name' attributes from your login_controller2/index.html.twig template
        $this->assertSelectorExists('input[name="_username"]', 'Username (email) input should exist');
        $this->assertSelectorExists('input[name="_password"]', 'Password input should exist');
        $this->assertSelectorExists('input[name="_csrf_token"]', 'CSRF token input should exist');
        $this->assertSelectorExists('button[type="submit"]', 'Submit button should exist');
        // Check button text if specific
        $this->assertSelectorTextContains('button[type="submit"]', 'Se connecter');
    }

    /**
     * Test Scenario: Login Failure (Incorrect Credentials)
     *
     * Simulates submitting the login form with incorrect username/password.
     * Verifies:
     * - Redirection back to the login page.
     * - Display of the "Invalid credentials" error message.
     * - User remains unauthenticated.
     */
    public function testLoginWithBadCredentials(): void
    {
        // 1. Create client
        $client = static::createClient();

        // 2. Go to the login page to get the form Crawler object
        $crawler = $client->request('GET', '/login');
        // Optional: $this->assertResponseIsSuccessful();

        // 3. Select the form using the submit button's text
        // Ensure "Se connecter" exactly matches the button text in your template
        $buttonCrawlerNode = $crawler->selectButton('Se connecter');
        $form = $buttonCrawlerNode->form();

        // 4. Fill in the form with INCORRECT credentials
        // Use the exact 'name' attributes from your template
        $form['_username'] = 'fake_user@fakedomain.com'; // An email that doesn't exist
        $form['_password'] = 'incorrectpassword';

        // 5. Submit the form
        // The client automatically sends a POST request to the form's action URL (/login)
        // Symfony's security layer intercepts this POST based on 'check_path' in security.yaml
        $client->submit($form);

        // 6. Assertions for Failed Login

        // --- Assert Redirect BACK to Login Page ---
        // When form_login fails, Symfony typically redirects back to the 'login_path'
        $this->assertResponseRedirects('/login', 302, "Failed login should redirect back to the login page.");

        // --- Follow the redirect ---
        $crawler = $client->followRedirect(); // $crawler now contains the HTML of the re-rendered login page

        // --- Assert Error Message is Displayed ---
        // Check the content of the redirected page (login page again) for the error alert div
        $this->assertSelectorExists('.alert-danger', 'Error message alert should be displayed after failed login.');
        // Check for the standard Symfony security error message text or translation key
        // Adjust 'Invalid credentials.' if you have customized translations in 'translations/security.en.yaml' etc.
        $this->assertSelectorTextContains('.alert-danger', 'Invalid credentials.', 'Standard "Invalid credentials." error message expected.');

        // --- Assert User is NOT Logged In ---
        // Access the security token storage service from the container
        // A null token means no user is authenticated for the current session/request
        $this->assertNull(
            $client->getContainer()->get('security.token_storage')->getToken(),
            'No user should be authenticated after failed login.'
        );
    }

    /**
     * Test Scenario: Login Success (Correct Credentials)
     *
     * Simulates logging in with valid credentials for a user that exists in the database.
     * Requires setting up a user in the test database first.
     * Verifies:
     * - Redirection to the expected target page after login (usually homepage '/').
     * - User becomes authenticated in the security context.
     */
    public function testLoginWithGoodCredentials(): void
    {
        // 1. Create client
        $client = static::createClient();

        // --- Setup Phase: Create a Test User in the Database ---
        // We need services from the container to create and save the user
        $container = static::getContainer();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get(EntityManagerInterface::class);
        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = $container->get(UserPasswordHasherInterface::class); // Service to hash passwords

        // Define credentials for our test user
        $testUserEmail = 'good.user.test@example.com';
        $testUserPassword = 'A-Secure-Password123!'; // Choose a password that meets any rules

        // Create a new Utilisateurs entity instance
        $testUser = new Utilisateurs();
        $testUser->setEmail($testUserEmail);
        // Set other necessary fields based on your Utilisateurs entity constraints/defaults
        $testUser->setNom('Good');
        $testUser->setPrenom('User');
        $testUser->setDateInscription(new \DateTime());
        $testUser->setTypeUtilisateur('client'); // Important for roles if you use them
        $testUser->setStatus('actif'); // Make sure the user is allowed to log in
        $testUser->setStatutVerification('vérifié'); // Assume verified status is needed
        $testUser->setAvatar('default_avatar.jpg'); // Set any other non-nullable fields

        // Hash the plain password using the configured hasher
        $hashedPassword = $passwordHasher->hashPassword($testUser, $testUserPassword);
        $testUser->setMotDePasse($hashedPassword); // Set the HASHED password on the entity

        // Save the user to the test database
        // (dama/doctrine-test-bundle should automatically wrap this in a transaction and roll back)
        $entityManager->persist($testUser);
        $entityManager->flush();
        // Optional: $entityManager->clear(); // Detach the saved entity


        // --- Execution Phase: Attempt Login ---

        // 2. Go to the login page
        $crawler = $client->request('GET', '/login');
        // Optional: $this->assertResponseIsSuccessful();

        // 3. Select the form
        $form = $crawler->selectButton('Se connecter')->form();

        // 4. Fill form with the CORRECT credentials of the user we just created
        $form['_username'] = $testUserEmail;
        $form['_password'] = $testUserPassword; // Use the PLAIN password here for the form submission

        // 5. Submit the form (POST to /login, handled by Symfony Security)
        $client->submit($form);

        // 6. Assertions for Successful Login

        // --- Assert Redirect to Target Page ---
        // Check redirection to the default target path after successful login.
        // This is often '/' (homepage) unless 'default_target_path' is set in config/packages/security.yaml firewall.
        // ** IMPORTANT: Adjust '/' if your application redirects elsewhere after login **
        $this->assertResponseRedirects('/', 302, "Successful login should redirect to the default target page (e.g., homepage '/').");

        // --- Follow the redirect ---
        $crawler = $client->followRedirect(); // $crawler now contains the HTML of the target page (e.g., homepage)

        // --- Assert Target Page Loaded ---
        // Check that the page we were redirected to loaded successfully
        $this->assertResponseIsSuccessful("The target page after successful login should load correctly.");
        // Optional: Add an assertion specific to the target page to be more robust
        // e.g., $this->assertSelectorTextContains('h1', 'Welcome to the Dashboard');

        // --- Assert User IS Logged In ---
        // Check the security context to confirm authentication
        $token = $client->getContainer()->get('security.token_storage')->getToken();
        $this->assertNotNull($token, 'A security token should exist after successful login.');

        // Verify the authenticated user is the correct type and has the correct identifier
        $loggedInUser = $token->getUser();
        $this->assertInstanceOf(Utilisateurs::class, $loggedInUser, 'Authenticated user should be an instance of Utilisateurs.');
        $this->assertSame($testUserEmail, $loggedInUser->getUserIdentifier(), "Logged in user's identifier (email) should match the one used for login.");

        // Optional: Check roles if important
        // $this->assertContains('ROLE_USER', $loggedInUser->getRoles());
        // $this->assertContains('ROLE_CLIENT', $loggedInUser->getRoles()); // Or whatever roles 'client' type gets
    }
}