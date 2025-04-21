<?php

namespace App\Tests\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class UtilisateurControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $utilisateurRepository;
    private string $path = '/y/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->utilisateurRepository = $this->manager->getRepository(Utilisateur::class);

        foreach ($this->utilisateurRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Utilisateur index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'utilisateur[nom]' => 'Testing',
            'utilisateur[prenom]' => 'Testing',
            'utilisateur[email]' => 'Testing',
            'utilisateur[mot_de_passe]' => 'Testing',
            'utilisateur[telephone]' => 'Testing',
            'utilisateur[date_inscription]' => 'Testing',
            'utilisateur[type_utilisateur]' => 'Testing',
            'utilisateur[avatar]' => 'Testing',
            'utilisateur[statut_verification]' => 'Testing',
            'utilisateur[status]' => 'Testing',
            'utilisateur[birthday]' => 'Testing',
            'utilisateur[adresse_utilisateur]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->utilisateurRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Utilisateur();
        $fixture->setNom('My Title');
        $fixture->setPrenom('My Title');
        $fixture->setEmail('My Title');
        $fixture->setMot_de_passe('My Title');
        $fixture->setTelephone('My Title');
        $fixture->setDate_inscription('My Title');
        $fixture->setType_utilisateur('My Title');
        $fixture->setAvatar('My Title');
        $fixture->setStatut_verification('My Title');
        $fixture->setStatus('My Title');
        $fixture->setBirthday('My Title');
        $fixture->setAdresse_utilisateur('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Utilisateur');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Utilisateur();
        $fixture->setNom('Value');
        $fixture->setPrenom('Value');
        $fixture->setEmail('Value');
        $fixture->setMot_de_passe('Value');
        $fixture->setTelephone('Value');
        $fixture->setDate_inscription('Value');
        $fixture->setType_utilisateur('Value');
        $fixture->setAvatar('Value');
        $fixture->setStatut_verification('Value');
        $fixture->setStatus('Value');
        $fixture->setBirthday('Value');
        $fixture->setAdresse_utilisateur('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'utilisateur[nom]' => 'Something New',
            'utilisateur[prenom]' => 'Something New',
            'utilisateur[email]' => 'Something New',
            'utilisateur[mot_de_passe]' => 'Something New',
            'utilisateur[telephone]' => 'Something New',
            'utilisateur[date_inscription]' => 'Something New',
            'utilisateur[type_utilisateur]' => 'Something New',
            'utilisateur[avatar]' => 'Something New',
            'utilisateur[statut_verification]' => 'Something New',
            'utilisateur[status]' => 'Something New',
            'utilisateur[birthday]' => 'Something New',
            'utilisateur[adresse_utilisateur]' => 'Something New',
        ]);

        self::assertResponseRedirects('/y/');

        $fixture = $this->utilisateurRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getNom());
        self::assertSame('Something New', $fixture[0]->getPrenom());
        self::assertSame('Something New', $fixture[0]->getEmail());
        self::assertSame('Something New', $fixture[0]->getMot_de_passe());
        self::assertSame('Something New', $fixture[0]->getTelephone());
        self::assertSame('Something New', $fixture[0]->getDate_inscription());
        self::assertSame('Something New', $fixture[0]->getType_utilisateur());
        self::assertSame('Something New', $fixture[0]->getAvatar());
        self::assertSame('Something New', $fixture[0]->getStatut_verification());
        self::assertSame('Something New', $fixture[0]->getStatus());
        self::assertSame('Something New', $fixture[0]->getBirthday());
        self::assertSame('Something New', $fixture[0]->getAdresse_utilisateur());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Utilisateur();
        $fixture->setNom('Value');
        $fixture->setPrenom('Value');
        $fixture->setEmail('Value');
        $fixture->setMot_de_passe('Value');
        $fixture->setTelephone('Value');
        $fixture->setDate_inscription('Value');
        $fixture->setType_utilisateur('Value');
        $fixture->setAvatar('Value');
        $fixture->setStatut_verification('Value');
        $fixture->setStatus('Value');
        $fixture->setBirthday('Value');
        $fixture->setAdresse_utilisateur('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/y/');
        self::assertSame(0, $this->utilisateurRepository->count([]));
    }
}
