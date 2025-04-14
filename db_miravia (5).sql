-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 21 mars 2025 à 08:56
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `db_miravia`
--

-- --------------------------------------------------------

--
-- Structure de la table `action_user`
--

CREATE TABLE `action_user` (
  `id_action_user` int(10) UNSIGNED NOT NULL,
  `libelle_action` varchar(100) NOT NULL,
  `group_text` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `action_user_checked`
--

CREATE TABLE `action_user_checked` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `id_action_user` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `admins`
--

CREATE TABLE `admins` (
  `id_admin` int(11) NOT NULL,
  `role` varchar(50) NOT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `admins`
--

INSERT INTO `admins` (`id_admin`, `role`, `permissions`) VALUES
(15, 'Super Admin', '{\"can_add_users\": true, \"can_delete_users\": true}');

-- --------------------------------------------------------

--
-- Structure de la table `aeroport`
--

CREATE TABLE `aeroport` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `iata_code` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `aeroport`
--

INSERT INTO `aeroport` (`id`, `nom`, `iata_code`) VALUES
(1, 'Tunis Carthage', 'TUN'),
(2, 'Paris Charles de Gaulle', 'CDG'),
(3, 'London Heathrow', 'LHR'),
(4, 'New York JFK', 'JFK'),
(5, 'Dubai International', 'DXB');

-- --------------------------------------------------------

--
-- Structure de la table `benevolat`
--

CREATE TABLE `benevolat` (
  `id_benevolat` int(11) NOT NULL,
  `id_partenaire` int(11) DEFAULT NULL,
  `titre` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `date_benevolat` datetime NOT NULL,
  `status` enum('ouvert','fermé','terminé') DEFAULT 'ouvert',
  `imageName` varchar(255) NOT NULL,
  `lieu` varchar(255) DEFAULT 'monastir'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `benevolat`
--

INSERT INTO `benevolat` (`id_benevolat`, `id_partenaire`, `titre`, `description`, `date_benevolat`, `status`, `imageName`, `lieu`) VALUES
(8, 6, 'Nettoyage de la Plage', 'Rejoignez-nous pour une journée de nettoyage de la plage afin de préserver notre environnement. Fournissons des sacs poubelles et des gants.', '2025-04-25 09:00:00', 'ouvert', 'nettoyage_plage.jpg', 'Plage de la Marsa'),
(10, 26, 'tDistribution de Repas', 'Nous organisons une distribution de repas pour les personnes dans le besoin. Rejoignez-nous pour aider à préparer et distribuer des repas chauds dans différents quartiers de la ville.', '2025-04-18 00:00:00', 'ouvert', 'distribution-repas.jpg', 'Centre Ville, Tunis');

-- --------------------------------------------------------

--
-- Structure de la table `candidatures`
--

CREATE TABLE `candidatures` (
  `id_candidature` int(11) NOT NULL,
  `id_client` int(11) DEFAULT NULL,
  `id_benevolat` int(11) DEFAULT NULL,
  `status` enum('en attente','acceptée','refusée') DEFAULT 'en attente',
  `date_candidature` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `chatbot_logs`
--

CREATE TABLE `chatbot_logs` (
  `id_log` int(11) NOT NULL,
  `id_utilisateur` int(11) DEFAULT NULL,
  `question` text NOT NULL,
  `reponse` text NOT NULL,
  `intent_reconnu` varchar(100) DEFAULT NULL,
  `date_interaction` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

CREATE TABLE `clients` (
  `id_client` int(11) NOT NULL,
  `adresse` text DEFAULT NULL,
  `points_fidelite` int(11) DEFAULT 0,
  `photo_CIN` varchar(255) NOT NULL DEFAULT 'default_value	',
  `autres_documents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`autres_documents`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `clients`
--

INSERT INTO `clients` (`id_client`, `adresse`, `points_fidelite`, `photo_CIN`, `autres_documents`) VALUES
(1, NULL, 0, 'default_photo.png', NULL),
(5, NULL, 0, 'default_photo.png', NULL),
(6, NULL, 0, 'default_photo.png', NULL),
(15, 'test', 0, '', NULL),
(20, NULL, 0, 'default_photo.png', NULL),
(23, NULL, 0, 'default_photo.png', NULL),
(24, NULL, 0, 'default_photo.png', NULL),
(25, NULL, 0, 'default_photo.png', NULL),
(27, NULL, 0, 'default_photo.png', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `contrats_partenaire`
--

CREATE TABLE `contrats_partenaire` (
  `id_contrat` int(11) NOT NULL,
  `id_partenaire` int(11) DEFAULT NULL,
  `date_signature` datetime DEFAULT current_timestamp(),
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `montant_contrat` decimal(10,2) DEFAULT NULL,
  `type_partenaire` enum('bénévolat','financier','matériel') NOT NULL,
  `conditions` text DEFAULT NULL,
  `statut` enum('actif','expiré','résilié') DEFAULT 'actif',
  `document_contrat` varchar(255) DEFAULT NULL,
  `date_renouvellement` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `conversations`
--

CREATE TABLE `conversations` (
  `id_conversation` int(11) NOT NULL,
  `sujet` varchar(255) DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp(),
  `id_user1` int(11) NOT NULL,
  `id_user2` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `conversations`
--

INSERT INTO `conversations` (`id_conversation`, `sujet`, `date_creation`, `id_user1`, `id_user2`) VALUES
(1, 'Conversation entre Jrad', '2025-03-16 00:28:43', 2, 2);

-- --------------------------------------------------------

--
-- Structure de la table `demandes_aide`
--

CREATE TABLE `demandes_aide` (
  `id_demande` int(11) NOT NULL,
  `id_client` int(11) DEFAULT NULL,
  `id_partenaire` int(11) DEFAULT NULL,
  `type_aide` enum('financiere','non_financiere') NOT NULL,
  `description` text DEFAULT NULL,
  `montant_demande` decimal(10,2) DEFAULT NULL,
  `date_demande` datetime DEFAULT current_timestamp(),
  `status` enum('en_attente','acceptee','refusee','en_traitement') DEFAULT 'en_attente',
  `preuves` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `demandes_aide`
--

INSERT INTO `demandes_aide` (`id_demande`, `id_client`, `id_partenaire`, `type_aide`, `description`, `montant_demande`, `date_demande`, `status`, `preuves`, `image`) VALUES
(1, 25, 19, 'financiere', 'i need 500dt', 500.00, '2025-03-26 14:35:06', 'acceptee', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `demande_partenaire`
--

CREATE TABLE `demande_partenaire` (
  `id` int(11) NOT NULL,
  `demandeur` varchar(100) NOT NULL,
  `telephone` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `date_demande` datetime DEFAULT current_timestamp(),
  `etat` enum('en attente','acceptée','refusée') DEFAULT 'en attente',
  `description` text NOT NULL,
  `preuve` varchar(255) NOT NULL,
  `type_activite` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `dons`
--

CREATE TABLE `dons` (
  `id_don` int(11) NOT NULL,
  `id_client` int(11) DEFAULT NULL,
  `id_partenaire` int(11) DEFAULT NULL,
  `montant` decimal(10,2) NOT NULL,
  `date_don` datetime DEFAULT current_timestamp(),
  `status` enum('en attente','validé','refusé') DEFAULT 'en attente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `dons`
--

INSERT INTO `dons` (`id_don`, `id_client`, `id_partenaire`, `montant`, `date_don`, `status`) VALUES
(1, 15, NULL, 500.00, '2025-03-18 00:28:24', 'en attente'),
(4, 1, 21, 500.00, '2025-03-19 00:00:00', 'en attente'),
(5, 25, 19, 500.00, '2025-03-20 14:53:58', 'validé'),
(6, 1, 26, 500.00, '2025-04-01 00:00:00', 'en attente');

-- --------------------------------------------------------

--
-- Structure de la table `equipe_partenaire`
--

CREATE TABLE `equipe_partenaire` (
  `id_utilisateur_partenaire` int(11) NOT NULL,
  `id_utilisateur` int(11) DEFAULT NULL,
  `id_partenaire` int(11) DEFAULT NULL,
  `role` varchar(50) NOT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `date_ajout` datetime DEFAULT current_timestamp(),
  `status` enum('actif','inactif') DEFAULT 'actif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `evenements`
--

CREATE TABLE `evenements` (
  `id_evenement` int(11) NOT NULL,
  `titre` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `date_evenement` datetime NOT NULL,
  `lieu` varchar(255) NOT NULL,
  `id_partenaire` int(11) DEFAULT NULL,
  `status` enum('actif','annulé','terminé') DEFAULT 'actif',
  `imageName` varchar(255) NOT NULL,
  `est_bloque` tinyint(1) DEFAULT 0,
  `raisons_blocage` varchar(255) DEFAULT NULL,
  `explication_blocage` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `evenements`
--

INSERT INTO `evenements` (`id_evenement`, `titre`, `description`, `date_evenement`, `lieu`, `id_partenaire`, `status`, `imageName`, `est_bloque`, `raisons_blocage`, `explication_blocage`) VALUES
(5, 'Salon International de l\'Agriculture', 'Le Salon International de l\'Agriculture de Tunis est le plus grand rendez-vous des professionnels du secteur agricole en Tunisie. Cet événement rassemble des agriculteurs, des chercheurs, des industriels et des décideurs politiques pour échanger sur les dernières innovations et technologies agricoles. Les visiteurs peuvent assister à des conférences, des ateliers et des démonstrations pratiques, ainsi qu\'à des expositions de matériel agricole et de produits du terroir. C\'est une plateforme idéale pour établir des partenariats, découvrir de nouvelles opportunités d\'affaires et contribuer au développement durable de l\'agriculture en Tunisie.', '2025-05-10 00:00:00', 'Parc des Expositions du Kram, Tunis', 26, 'actif', 'salon.jpg', 0, NULL, NULL),
(6, 'Marathon de Carthage', 'Le Marathon de Carthage est une course emblématique qui attire des coureurs de tous niveaux, venus des quatre coins du monde. Le parcours traverse les sites historiques de Carthage, offrant des vues imprenables sur les ruines antiques, la mer Méditerranée et les paysages pittoresques de Tunis. Les participants peuvent choisir entre plusieurs distances, allant du marathon complet au semi-marathon, en passant par des courses plus courtes pour les enfants et les débutants. En plus de la compétition, l\événement propose des animations, des concerts et des stands de restauration pour garantir une expérience festive et conviviale.', '2025-06-20 07:00:00', 'Carthage, Tunis', NULL, 'actif', 'marathon.jpg', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `favoris`
--

CREATE TABLE `favoris` (
  `id_favori` int(11) NOT NULL,
  `id_utilisateur` int(11) DEFAULT NULL,
  `type_entite` enum('evenement','benevolat') NOT NULL,
  `id_entite` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `flights`
--

CREATE TABLE `flights` (
  `id` int(11) NOT NULL,
  `flight_id` varchar(10) DEFAULT NULL,
  `origin` varchar(3) DEFAULT NULL,
  `destination` varchar(3) DEFAULT NULL,
  `departure_time` datetime DEFAULT NULL,
  `arrival_time` datetime DEFAULT NULL,
  `duration` varchar(10) DEFAULT NULL,
  `carrier_code` varchar(10) DEFAULT NULL,
  `flight_number` varchar(10) DEFAULT NULL,
  `aircraft` varchar(10) DEFAULT NULL,
  `stops` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `currency` varchar(3) DEFAULT NULL,
  `available_seats` int(11) DEFAULT NULL,
  `last_ticketing_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `jours`
--

CREATE TABLE `jours` (
  `id` int(10) UNSIGNED NOT NULL,
  `libelle` varchar(450) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `jours`
--

INSERT INTO `jours` (`id`, `libelle`) VALUES
(1, 'Lundi'),
(2, 'Mardi'),
(3, 'Mercredi'),
(4, 'Jeudi'),
(5, 'Vendredi'),
(6, 'Samedi'),
(7, 'Dimanche');

-- --------------------------------------------------------

--
-- Structure de la table `langue`
--

CREATE TABLE `langue` (
  `code_langue` varchar(45) NOT NULL,
  `nom_langue` varchar(450) NOT NULL,
  `flag_langue` varchar(450) NOT NULL,
  `code_route` varchar(45) NOT NULL,
  `etat` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `langue`
--

INSERT INTO `langue` (`code_langue`, `nom_langue`, `flag_langue`, `code_route`, `etat`) VALUES
('ar_SY', 'Arabe', 'flag_ar.png', 'ar', 0),
('en_US', 'Anglais', 'flag_en.png', 'en', 1),
('fr_FR', 'Français', 'flag_fr.png', 'fr', 1);

-- --------------------------------------------------------

--
-- Structure de la table `mail_msg`
--

CREATE TABLE `mail_msg` (
  `id_msg` varchar(45) NOT NULL,
  `texte_id` varchar(100) NOT NULL,
  `texte_start` varchar(1000) DEFAULT NULL,
  `texte_end` varchar(1000) DEFAULT NULL,
  `titre` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `mail_to_send`
--

CREATE TABLE `mail_to_send` (
  `id_mail` int(10) UNSIGNED NOT NULL,
  `from` varchar(150) NOT NULL,
  `to` varchar(150) NOT NULL,
  `subject` varchar(150) NOT NULL,
  `body` mediumtext NOT NULL,
  `cc` varchar(150) DEFAULT NULL,
  `bcc` varchar(150) DEFAULT NULL,
  `is_sent` tinyint(1) NOT NULL DEFAULT 0,
  `id_vente` int(10) UNSIGNED DEFAULT 0,
  `id_panier` int(10) UNSIGNED DEFAULT 0,
  `date_envoi` datetime DEFAULT NULL,
  `type_id_panier` int(4) DEFAULT 1 COMMENT '1: reference de vente et 2: ref dde prix et 3 : bons de commandes',
  `product_id` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `id_message` int(11) NOT NULL,
  `id_expediteur` int(11) DEFAULT NULL,
  `id_destinataire` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `reponse_chatbot` text DEFAULT NULL,
  `date_envoi` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `messages_reciption`
--

CREATE TABLE `messages_reciption` (
  `message_id` int(11) NOT NULL,
  `id_user_reception` int(11) NOT NULL,
  `id_user_sender` int(11) NOT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `chat_message_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notations`
--

CREATE TABLE `notations` (
  `id_nota` int(11) NOT NULL,
  `id_utilisateur` int(11) DEFAULT NULL,
  `type_entite` enum('evenement','benevolat','don','partenaire') NOT NULL,
  `id_entite` int(11) NOT NULL,
  `note` int(11) NOT NULL CHECK (`note` between 1 and 5),
  `commentaire` text DEFAULT NULL,
  `date_ajout` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id_notification` int(11) NOT NULL,
  `id_utilisateur` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `type_notification` enum('info','alerte','rappel') NOT NULL,
  `date_envoi` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `notifications`
--

INSERT INTO `notifications` (`id_notification`, `id_utilisateur`, `message`, `type_notification`, `date_envoi`) VALUES
(1, 16, 'Bienvenue cher partenaire test test', 'info', '2025-03-18 13:12:58'),
(2, 21, 'Bienvenue cher partenaire Ghassen chahdoura', 'info', '2025-03-19 01:25:36'),
(3, 21, 'Un nouveau don a été ajouté d\'un montant de 500.', 'info', '2025-03-19 01:26:07'),
(4, 21, 'Un nouveau don a été ajouté d\'un montant de 500.', 'info', '2025-03-19 01:27:53'),
(5, 21, 'Un nouveau don a été ajouté d\'un montant de 500.', 'info', '2025-03-19 01:28:30'),
(6, 16, 'Bienvenue cher partenaire test test', 'info', '2025-03-19 02:27:38'),
(7, 16, 'Bienvenue cher partenaire test test', 'info', '2025-03-19 02:28:49'),
(8, 16, 'Bienvenue cher partenaire test test', 'info', '2025-03-19 02:30:35'),
(9, 16, 'Bienvenue cher partenaire test test', 'info', '2025-03-19 02:31:38'),
(10, 16, 'Bienvenue cher partenaire test test', 'info', '2025-03-19 03:11:37'),
(11, 16, 'Bienvenue cher partenaire test test', 'info', '2025-03-19 03:34:56'),
(12, 16, 'Bienvenue cher partenaire test test', 'info', '2025-03-19 03:37:59'),
(13, 26, 'Bienvenue cher partenaire Ghassen Chahdoura', 'info', '2025-03-19 23:45:33'),
(14, 26, 'Bienvenue cher partenaire Ghassen Chahdoura', 'info', '2025-03-20 00:02:53'),
(15, 26, 'Bienvenue cher partenaire Ghassen Chahdoura', 'info', '2025-03-20 00:07:56'),
(16, 26, 'Bienvenue cher partenaire Ghassen Chahdoura', 'info', '2025-03-20 00:14:48'),
(17, 26, 'Bienvenue cher partenaire Ghassen Chahdoura', 'info', '2025-03-20 01:00:54'),
(18, 26, 'Bienvenue cher partenaire Ghassen Chahdoura', 'info', '2025-03-20 01:02:40'),
(19, 26, 'Bienvenue cher partenaire Ghassen Chahdoura', 'info', '2025-03-20 01:05:06'),
(20, 26, 'Bienvenue cher partenaire Ghassen Chahdoura', 'info', '2025-03-20 01:10:28'),
(21, 26, 'Bienvenue cher partenaire Ghassen Chahdoura', 'info', '2025-03-20 01:11:20'),
(22, 26, 'Bienvenue cher partenaire Ghassen Chahdoura', 'info', '2025-03-20 01:13:12'),
(23, 26, 'Bienvenue cher partenaire Ghassen Chahdoura', 'info', '2025-03-20 01:17:21'),
(24, 26, 'Bienvenue cher partenaire Ghassen Chahdoura', 'info', '2025-03-20 01:19:37'),
(25, 26, 'Bienvenue cher partenaire Ghassen Chahdoura', 'info', '2025-03-20 01:24:58'),
(26, 26, 'Bienvenue cher partenaire Ghassen Chahdoura', 'info', '2025-03-20 01:34:20'),
(27, 26, 'Bienvenue cher partenaire Ghassen Chahdoura', 'info', '2025-03-20 21:14:23'),
(28, 26, 'Bienvenue cher partenaire Ghassen Chahdoura', 'info', '2025-03-20 21:16:27'),
(29, 26, 'Bienvenue cher partenaire Ghassen Chahdoura', 'info', '2025-03-20 21:17:55'),
(30, 26, 'Bienvenue cher partenaire Ghassen Chahdoura', 'info', '2025-03-21 00:21:32'),
(31, 26, 'Un nouveau don a été ajouté d\'un montant de 500.', 'info', '2025-03-21 00:35:24'),
(32, 26, 'Bienvenue cher partenaire Ghassen Chahdoura', 'info', '2025-03-21 00:50:00');

-- --------------------------------------------------------

--
-- Structure de la table `partenaires`
--

CREATE TABLE `partenaires` (
  `id_partenaire` int(11) NOT NULL,
  `nom_entreprise` varchar(100) NOT NULL,
  `type_activite` varchar(50) NOT NULL,
  `site_web` varchar(255) NOT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `autres_documents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`autres_documents`)),
  `etat` int(1) DEFAULT 0,
  `date_expiration` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `partenaires`
--

INSERT INTO `partenaires` (`id_partenaire`, `nom_entreprise`, `type_activite`, `site_web`, `adresse`, `telephone`, `autres_documents`, `etat`, `date_expiration`) VALUES
(6, 'thfghfghfgh', '', 'fghfghfghfghfgh', 'fghfghfghfgh', '1234567890', '{\"document1\": \"url1\"}', 1, '2026-03-07'),
(19, 'Test', 'Test', 'https://partenaireA.com', 'Rue dejla', '12345678', '{\"document1\": \"url1\"}', 1, '2025-04-17');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id_utilisateur` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `telephone` varchar(50) DEFAULT NULL,
  `date_inscription` datetime DEFAULT current_timestamp(),
  `type_utilisateur` enum('admin','partenaire','client') NOT NULL,
  `avatar` varchar(255) DEFAULT 'https://example.com/default-avatar.png',
  `statut_verification` enum('en attente','validé','refusé ou bloqué') DEFAULT 'en attente',
  `status` enum('actif','inactif') DEFAULT 'actif',
  `birthday` date DEFAULT NULL,
  `adresse_utilisateur` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id_utilisateur`, `nom`, `prenom`, `email`, `mot_de_passe`, `telephone`, `date_inscription`, `type_utilisateur`, `avatar`, `statut_verification`, `status`, `birthday`, `adresse_utilisateur`) VALUES
(1, 'Ghassen', 'test', 'test@gmail.com', 'Gaston', '0000000000', '2025-03-13 22:50:46', 'client', 'https://example.com/default-avatar.png', 'en attente', 'actif', NULL, NULL),
(5, 'GHASSEN', '', 'gaston@gmail.com', '1Iq3a7jh', NULL, '2025-03-13 23:32:09', 'client', 'https://example.com/default-avatar.png', 'validé', 'actif', NULL, NULL),
(6, 'Gastona', '', 'gas@yahoo.fr', 'Gaston@557742', NULL, '2025-03-13 23:34:36', 'partenaire', 'f7b1467d-ee54-4bd9-b60f-739a35d0f40e.PNG', 'validé', 'actif', NULL, NULL),
(15, 'Habib', '', 'chaabenesarra3@gmail.com', 'hashed_password', '55774132', '2025-03-18 00:27:15', 'admin', 'f7b1467d-ee54-4bd9-b60f-739a35d0f40e.PNG', 'en attente', 'actif', '2015-03-17', 'Monastir'),
(16, 'test', 'test', 'gas@gmlail.com', '$2a$10$0FqE2gn4bGmkDy3R20Cwf.ZL7/qiI5oE3Qm9Q21gEdfttfqMG7HWC', '11223344', '2025-03-18 13:12:01', 'partenaire', NULL, 'en attente', 'actif', NULL, NULL),
(19, 'ghassen', 'chahdoura', 'chahdoura@gmail.com', 'test123456@', '13462782', '2025-03-19 22:50:25', 'partenaire', 'https://example.com/default-avatar.png', 'validé', 'actif', '2025-03-19', NULL),
(20, 'Ghassen', '', 'chahdouragh@gmail.com', 'Gaston12345@', '1122334455', '2025-03-18 23:44:20', 'client', 'file.png', 'validé', 'actif', '1999-03-12', 'Monastira'),
(21, 'Ghassen', 'chahdoura', 'ghassenchahdoura@yahoo.fr', '$2a$10$DaBRPVZ1HbAqOVhZn4.4hOog2xJPUuHOgB2Dk/RMHrUpwmQoVh1mK', '55774123', '2025-03-19 01:25:06', 'partenaire', NULL, 'en attente', 'actif', NULL, NULL),
(23, 'Ghassen', '', 'Gasaa@gmail.com', 'Test123456@', NULL, '2025-03-19 21:59:32', 'client', 'https://example.com/default-avatar.png', 'validé', 'actif', NULL, NULL),
(24, 'Ghassen', '', 'Gaaaa@gmail.com', 'Test1234@', NULL, '2025-03-19 22:16:03', 'client', 'https://example.com/default-avatar.png', 'validé', 'actif', NULL, NULL),
(25, 'Gaston', '', 'yahoo@yahoo.fr', 'Test1234@', NULL, '2025-03-19 23:33:16', 'client', 'https://example.com/default-avatar.png', 'validé', 'actif', NULL, NULL),
(26, 'Ghassen', 'Chahdoura', 'chahdouraghassen@gmail.com', '$2a$10$UM3O4DFTN881BJU3W4wOyuKCDLr19lWu0cLhkBvWy1YUDT6zN9S96', '55773211', '2025-03-19 23:43:54', 'partenaire', NULL, 'en attente', 'actif', NULL, NULL),
(27, 'ghassen', '', 'ghchahdoura@gmail.com', 'Test123456@', NULL, '2025-03-21 08:53:24', 'client', 'https://example.com/default-avatar.png', 'validé', 'actif', NULL, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id_admin`);

--
-- Index pour la table `aeroport`
--
ALTER TABLE `aeroport`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `benevolat`
--
ALTER TABLE `benevolat`
  ADD PRIMARY KEY (`id_benevolat`),
  ADD KEY `id_partenaire` (`id_partenaire`);

--
-- Index pour la table `candidatures`
--
ALTER TABLE `candidatures`
  ADD PRIMARY KEY (`id_candidature`),
  ADD KEY `id_client` (`id_client`),
  ADD KEY `id_benevolat` (`id_benevolat`);

--
-- Index pour la table `chatbot_logs`
--
ALTER TABLE `chatbot_logs`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id_client`);

--
-- Index pour la table `contrats_partenaire`
--
ALTER TABLE `contrats_partenaire`
  ADD PRIMARY KEY (`id_contrat`),
  ADD KEY `id_partenaire` (`id_partenaire`);

--
-- Index pour la table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id_conversation`);

--
-- Index pour la table `demandes_aide`
--
ALTER TABLE `demandes_aide`
  ADD PRIMARY KEY (`id_demande`),
  ADD KEY `id_client` (`id_client`),
  ADD KEY `id_partenaire` (`id_partenaire`);

--
-- Index pour la table `dons`
--
ALTER TABLE `dons`
  ADD PRIMARY KEY (`id_don`),
  ADD KEY `id_client` (`id_client`),
  ADD KEY `id_partenaire` (`id_partenaire`);

--
-- Index pour la table `equipe_partenaire`
--
ALTER TABLE `equipe_partenaire`
  ADD PRIMARY KEY (`id_utilisateur_partenaire`),
  ADD KEY `id_utilisateur` (`id_utilisateur`),
  ADD KEY `id_partenaire` (`id_partenaire`);

--
-- Index pour la table `evenements`
--
ALTER TABLE `evenements`
  ADD PRIMARY KEY (`id_evenement`),
  ADD KEY `id_partenaire` (`id_partenaire`),
  ADD KEY `idx_date_evenement` (`date_evenement`);

--
-- Index pour la table `favoris`
--
ALTER TABLE `favoris`
  ADD PRIMARY KEY (`id_favori`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `flights`
--
ALTER TABLE `flights`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id_message`),
  ADD KEY `id_expediteur` (`id_expediteur`),
  ADD KEY `id_destinataire` (`id_destinataire`);

--
-- Index pour la table `notations`
--
ALTER TABLE `notations`
  ADD PRIMARY KEY (`id_nota`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id_notification`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `partenaires`
--
ALTER TABLE `partenaires`
  ADD PRIMARY KEY (`id_partenaire`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id_utilisateur`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `telephone` (`telephone`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_status` (`status`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `aeroport`
--
ALTER TABLE `aeroport`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `benevolat`
--
ALTER TABLE `benevolat`
  MODIFY `id_benevolat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `candidatures`
--
ALTER TABLE `candidatures`
  MODIFY `id_candidature` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `chatbot_logs`
--
ALTER TABLE `chatbot_logs`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `contrats_partenaire`
--
ALTER TABLE `contrats_partenaire`
  MODIFY `id_contrat` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `demandes_aide`
--
ALTER TABLE `demandes_aide`
  MODIFY `id_demande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `dons`
--
ALTER TABLE `dons`
  MODIFY `id_don` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `equipe_partenaire`
--
ALTER TABLE `equipe_partenaire`
  MODIFY `id_utilisateur_partenaire` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `evenements`
--
ALTER TABLE `evenements`
  MODIFY `id_evenement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `favoris`
--
ALTER TABLE `favoris`
  MODIFY `id_favori` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `flights`
--
ALTER TABLE `flights`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id_message` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `notations`
--
ALTER TABLE `notations`
  MODIFY `id_nota` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id_notification` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `candidatures`
--
ALTER TABLE `candidatures`
  ADD CONSTRAINT `candidatures_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `clients` (`id_client`) ON DELETE CASCADE,
  ADD CONSTRAINT `candidatures_ibfk_2` FOREIGN KEY (`id_benevolat`) REFERENCES `benevolat` (`id_benevolat`) ON DELETE CASCADE;

--
-- Contraintes pour la table `chatbot_logs`
--
ALTER TABLE `chatbot_logs`
  ADD CONSTRAINT `chatbot_logs_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `clients`
--
ALTER TABLE `clients`
  ADD CONSTRAINT `clients_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `contrats_partenaire`
--
ALTER TABLE `contrats_partenaire`
  ADD CONSTRAINT `contrats_partenaire_ibfk_1` FOREIGN KEY (`id_partenaire`) REFERENCES `partenaires` (`id_partenaire`) ON DELETE CASCADE;

--
-- Contraintes pour la table `demandes_aide`
--
ALTER TABLE `demandes_aide`
  ADD CONSTRAINT `demandes_aide_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `clients` (`id_client`) ON DELETE CASCADE,
  ADD CONSTRAINT `demandes_aide_ibfk_2` FOREIGN KEY (`id_partenaire`) REFERENCES `partenaires` (`id_partenaire`) ON DELETE SET NULL;

--
-- Contraintes pour la table `equipe_partenaire`
--
ALTER TABLE `equipe_partenaire`
  ADD CONSTRAINT `equipe_partenaire_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE,
  ADD CONSTRAINT `equipe_partenaire_ibfk_2` FOREIGN KEY (`id_partenaire`) REFERENCES `partenaires` (`id_partenaire`) ON DELETE CASCADE;

--
-- Contraintes pour la table `favoris`
--
ALTER TABLE `favoris`
  ADD CONSTRAINT `favoris_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`id_expediteur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`id_destinataire`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

--
-- Contraintes pour la table `notations`
--
ALTER TABLE `notations`
  ADD CONSTRAINT `notations_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `partenaires`
--
ALTER TABLE `partenaires`
  ADD CONSTRAINT `partenaires_ibfk_1` FOREIGN KEY (`id_partenaire`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
