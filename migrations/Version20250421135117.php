<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250421135117 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE `user` (id_utilisateur INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, roles JSON NOT NULL COMMENT '(DC2Type:json)', nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, telephone VARCHAR(255) DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, birthday DATE DEFAULT NULL, adresse_utilisateur VARCHAR(255) DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, date_inscription DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id_utilisateur)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE conversations
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE admins
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE aeroport
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE clients
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE benevolat
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE candidatures
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE contrats_partenaire
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE demandes_aide
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE demande_partenaire
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE dons
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE flights
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE partenaires
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE evenements
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE favoris
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE notifications
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateurs ADD roles JSON NOT NULL COMMENT '(DC2Type:json)', CHANGE id_utilisateur id_utilisateur INT AUTO_INCREMENT NOT NULL, CHANGE nom nom VARCHAR(50) NOT NULL, CHANGE prenom prenom VARCHAR(50) DEFAULT '' NOT NULL, CHANGE email email VARCHAR(100) NOT NULL, CHANGE mot_de_passe mot_de_passe VARCHAR(255) NOT NULL, CHANGE date_inscription date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE type_utilisateur type_utilisateur VARCHAR(50) NOT NULL, CHANGE avatar avatar VARCHAR(255) DEFAULT 'https://example.com/default-avatar.png' NOT NULL, CHANGE statut_verification statut_verification VARCHAR(50) DEFAULT 'en attente' NOT NULL, CHANGE status status VARCHAR(50) DEFAULT 'actif' NOT NULL, ADD PRIMARY KEY (id_utilisateur)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_497B315EE7927C74 ON utilisateurs (email)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE conversations (id_conversation INT NOT NULL, sujet VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, date_creation DATETIME DEFAULT CURRENT_TIMESTAMP, id_user1 INT NOT NULL, id_user2 INT NOT NULL) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE admins (id_admin INT DEFAULT NULL, role VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, permissions JSON DEFAULT NULL COMMENT '(DC2Type:json)') DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE aeroport (id INT DEFAULT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, iata_code VARCHAR(10) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE clients (id_client INT DEFAULT NULL, adresse TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, points_fidelite INT DEFAULT 0, photo_CIN VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT 'default_value	' COLLATE `utf8mb4_general_ci`, autres_documents JSON DEFAULT NULL COMMENT '(DC2Type:json)') DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE benevolat (id_benevolat INT DEFAULT NULL, id_partenaire INT DEFAULT NULL, titre VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, description TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, date_benevolat DATETIME DEFAULT NULL, status VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT 'ouvert' COLLATE `utf8mb4_general_ci`, imageName VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE candidatures (id_candidature INT DEFAULT NULL, id_client INT DEFAULT NULL, id_benevolat INT DEFAULT NULL, status VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT 'en attente' COLLATE `utf8mb4_general_ci`, date_candidature DATETIME DEFAULT CURRENT_TIMESTAMP) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE contrats_partenaire (id_contrat INT DEFAULT NULL, id_partenaire INT DEFAULT NULL, date_signature DATETIME DEFAULT CURRENT_TIMESTAMP, date_debut DATE DEFAULT NULL, date_fin DATE DEFAULT NULL, montant_contrat NUMERIC(10, 2) DEFAULT NULL, type_partenaire VARCHAR(255) CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, conditions TEXT CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, statut VARCHAR(20) CHARACTER SET latin1 DEFAULT 'actif' COLLATE `latin1_swedish_ci`, document_contrat VARCHAR(255) CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, date_renouvellement DATE DEFAULT NULL) DEFAULT CHARACTER SET latin1 COLLATE `latin1_swedish_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE demandes_aide (id_demande INT DEFAULT NULL, id_client INT DEFAULT NULL, id_partenaire INT DEFAULT NULL, type_aide VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, description TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, montant_demande NUMERIC(10, 2) DEFAULT NULL, date_demande DATETIME DEFAULT CURRENT_TIMESTAMP, status VARCHAR(30) CHARACTER SET utf8mb4 DEFAULT 'en_attente' COLLATE `utf8mb4_general_ci`, preuves JSON DEFAULT NULL COMMENT '(DC2Type:json)') DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE demande_partenaire (id INT NOT NULL, demandeur VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, telephone VARCHAR(15) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, email VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, adresse VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, date_demande DATETIME DEFAULT CURRENT_TIMESTAMP, etat VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT 'en attente' COLLATE `utf8mb4_general_ci`, description TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, preuve VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, type_activite VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE dons (id_don INT NOT NULL, id_client INT DEFAULT NULL, id_partenaire INT DEFAULT NULL, montant NUMERIC(10, 2) DEFAULT NULL, date_don DATETIME DEFAULT CURRENT_TIMESTAMP, status VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT 'en attente' COLLATE `utf8mb4_general_ci`) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE flights (id INT NOT NULL, flight_id VARCHAR(10) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, origin VARCHAR(3) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, destination VARCHAR(3) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, departure_time DATETIME DEFAULT NULL, arrival_time DATETIME DEFAULT NULL, duration VARCHAR(10) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, carrier_code VARCHAR(10) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, flight_number VARCHAR(10) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, aircraft VARCHAR(10) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, stops INT DEFAULT NULL, price NUMERIC(10, 2) DEFAULT NULL, currency VARCHAR(3) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, available_seats INT DEFAULT NULL, last_ticketing_date DATE DEFAULT NULL) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE partenaires (id_partenaire INT DEFAULT NULL, nom_entreprise VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, type_activite VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, site_web VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, adresse VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, telephone VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, autres_documents JSON DEFAULT NULL COMMENT '(DC2Type:json)', etat INT DEFAULT 0, date_expiration DATE DEFAULT NULL) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE evenements (id_evenement INT DEFAULT NULL, titre VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, description TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, date_evenement DATETIME DEFAULT NULL, lieu VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, id_partenaire INT DEFAULT NULL, status VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT 'actif' COLLATE `utf8mb4_general_ci`, imageName VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, est_bloque TINYINT(1) DEFAULT 0, raisons_blocage VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, explication_blocage TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE favoris (id_favori INT DEFAULT NULL, id_utilisateur INT DEFAULT NULL, type_entite VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, id_entite INT NOT NULL) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE notifications (id_notification INT DEFAULT NULL, id_utilisateur INT DEFAULT NULL, message TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, type_notification VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE `user`
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateurs MODIFY id_utilisateur INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_497B315EE7927C74 ON utilisateurs
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `primary` ON utilisateurs
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateurs DROP roles, CHANGE id_utilisateur id_utilisateur INT DEFAULT NULL, CHANGE nom nom VARCHAR(50) DEFAULT NULL, CHANGE prenom prenom VARCHAR(50) DEFAULT '', CHANGE email email VARCHAR(100) DEFAULT NULL, CHANGE mot_de_passe mot_de_passe VARCHAR(255) DEFAULT NULL, CHANGE date_inscription date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE type_utilisateur type_utilisateur VARCHAR(20) DEFAULT NULL, CHANGE avatar avatar VARCHAR(255) DEFAULT 'https://example.com/default-avatar.png', CHANGE statut_verification statut_verification VARCHAR(30) DEFAULT 'en attente', CHANGE status status VARCHAR(10) DEFAULT 'actif'
        SQL);
    }
}
