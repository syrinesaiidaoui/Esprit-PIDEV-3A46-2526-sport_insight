-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 25 fév. 2026 à 22:40
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `sport_insight`
--

-- --------------------------------------------------------

--
-- Structure de la table `annonce`
--

CREATE TABLE `annonce` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `poste_recherche` varchar(255) NOT NULL,
  `niveau_requis` varchar(255) NOT NULL,
  `date_publication` date NOT NULL,
  `statut` varchar(255) NOT NULL,
  `entraineur_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `annonce`
--

INSERT INTO `annonce` (`id`, `titre`, `description`, `poste_recherche`, `niveau_requis`, `date_publication`, `statut`, `entraineur_id`) VALUES
(1, 'JKJ', 'JKJKJPKMLJOL', 'KJLKJPLK?L', 'Intermédiaire', '2026-03-01', 'active', 14),
(2, 'attaquant', 'je cherche un joueur', 'attaquant', 'Avancé', '2026-03-21', 'active', 14);

-- --------------------------------------------------------

--
-- Structure de la table `chat_message`
--

CREATE TABLE `chat_message` (
  `id` int(11) NOT NULL,
  `message` longtext NOT NULL,
  `created_at` datetime NOT NULL,
  `auteur_id` int(11) NOT NULL,
  `destinataire_id` int(11) NOT NULL,
  `annonce_id` int(11) NOT NULL,
  `is_read` tinyint(1) NOT NULL,
  `notification_sent` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `commentaire`
--

CREATE TABLE `commentaire` (
  `id` int(11) NOT NULL,
  `contenu` longtext NOT NULL,
  `date_commentaire` date NOT NULL,
  `joueur_id` int(11) DEFAULT NULL,
  `annonce_id` int(11) NOT NULL,
  `auteur_anonyme` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `contrat_sponsor`
--

CREATE TABLE `contrat_sponsor` (
  `id` int(11) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `montant` double NOT NULL,
  `sponsor_id` int(11) NOT NULL,
  `equipe_id` int(11) NOT NULL,
  `description` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `contrat_sponsor`
--

INSERT INTO `contrat_sponsor` (`id`, `date_debut`, `date_fin`, `montant`, `sponsor_id`, `equipe_id`, `description`) VALUES
(1, '2026-02-18', '2026-03-01', 67, 2, 1, NULL),
(2, '2026-02-12', '2026-02-28', 67, 4, 2, 'hkhzk');

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20260207143915', '2026-02-07 15:39:35', 1589),
('DoctrineMigrations\\Version20260207144900', '2026-02-24 22:31:55', 339);

-- --------------------------------------------------------

--
-- Structure de la table `entrainement`
--

CREATE TABLE `entrainement` (
  `id` int(11) NOT NULL,
  `date_entrainement` date NOT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  `type` varchar(255) NOT NULL,
  `objectif` longtext NOT NULL,
  `lieu` varchar(255) NOT NULL,
  `entraineur_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `entrainement`
--

INSERT INTO `entrainement` (`id`, `date_entrainement`, `heure_debut`, `heure_fin`, `type`, `objectif`, `lieu`, `entraineur_id`) VALUES
(3, '2028-01-01', '03:00:00', '16:00:00', 'tactique', 'savoir comment partager lequipe', 'sfax', 7),
(4, '2030-01-01', '00:00:00', '13:00:00', 'technique', 'ameliorer le corps', 'tunis', 7),
(5, '2029-01-01', '04:00:00', '13:00:00', 'technique', 'savoir comment travailler en equipe', 'tunis', 14),
(6, '2028-01-01', '16:00:00', '15:00:00', 'technique', 'savoir comment defendre', 'tunis', 7),
(7, '2031-01-01', '16:00:00', '14:00:00', 'technique', 'savoir travailler en equipe', 'tunis', 18),
(8, '2031-01-01', '13:00:00', '04:00:00', 'technique', 'savoir comment gerer', 'tunis', 18),
(9, '2031-01-01', '18:00:00', '14:00:00', 'technique', 'voila entrainement', 'tunis', 18),
(10, '2029-01-01', '00:00:00', '12:00:00', 'tactique', 'quoi', 'tunis', 18),
(11, '2029-01-01', '12:00:00', '13:00:00', 'tactique', 'rzrrefd', 'tunis', 18),
(12, '2031-01-01', '10:00:00', '00:00:00', 'tactique', 'reefff', 'tunis', 18),
(13, '2031-01-01', '07:00:00', '12:00:00', 'technique', ',lk,l,mpk;^ù', 'sfax', 18),
(14, '2030-01-01', '00:00:00', '16:00:00', 'tactique', 'hikjojpojp', 'sfax', 18),
(15, '2029-01-15', '04:00:00', '00:00:00', 'tactique', 'kmlmmlmm', 'sfax', 18),
(16, '2030-01-01', '06:00:00', '00:00:00', 'tactique', 'edzfzfez', 'sfax', 18),
(17, '2027-01-01', '08:00:00', '00:00:00', 'tactique', 'gfvvghbg', 'sfax', 18),
(18, '2026-04-13', '05:00:00', '11:00:00', 'tactique', 'hguguguhu', 'sfax', 18),
(19, '2026-08-13', '14:00:00', '17:00:00', 'tactique', 'fafzef', 'sfax', 18);

-- --------------------------------------------------------

--
-- Structure de la table `entrainement_user`
--

CREATE TABLE `entrainement_user` (
  `entrainement_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `entrainement_user`
--

INSERT INTO `entrainement_user` (`entrainement_id`, `user_id`) VALUES
(3, 14),
(4, 10),
(5, 17),
(6, 17),
(7, 16),
(8, 17),
(9, 15),
(10, 15),
(11, 15),
(12, 15),
(13, 15),
(14, 15),
(15, 15),
(16, 15),
(17, 15),
(18, 15),
(19, 15);

-- --------------------------------------------------------

--
-- Structure de la table `equipe`
--

CREATE TABLE `equipe` (
  `id` int(11) NOT NULL,
  `id_equipe` varchar(100) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `coach` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `equipe`
--

INSERT INTO `equipe` (`id`, `id_equipe`, `nom`, `coach`) VALUES
(1, '111', 'nom', 'AAA'),
(2, '9', 'hahkad', 'dada');

-- --------------------------------------------------------

--
-- Structure de la table `evaluation`
--

CREATE TABLE `evaluation` (
  `id` int(11) NOT NULL,
  `note_physique` double NOT NULL,
  `note_technique` double NOT NULL,
  `note_tactique` double NOT NULL,
  `commentaire` longtext DEFAULT NULL,
  `entrainement_id` int(11) NOT NULL,
  `joueur_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `evaluation`
--

INSERT INTO `evaluation` (`id`, `note_physique`, `note_technique`, `note_tactique`, `commentaire`, `entrainement_id`, `joueur_id`) VALUES
(5, 4, 9, 0, NULL, 5, 7),
(9, 4, 3, 5, NULL, 4, 7),
(10, 4, 3, 5, NULL, 3, 14),
(11, 4, 3, 5, NULL, 3, 7),
(12, 4, 3, 5, NULL, 5, 17),
(13, 9, 7, 3, NULL, 5, 17),
(14, 9, 7, 3, NULL, 16, 14),
(15, 4, 2, 1, NULL, 16, 14),
(16, 10, 10, 10, NULL, 8, 14),
(17, 4, 2, 1, NULL, 18, 15);

-- --------------------------------------------------------

--
-- Structure de la table `matchs`
--

CREATE TABLE `matchs` (
  `id` int(11) NOT NULL,
  `id_match` varchar(100) NOT NULL,
  `date_match` date NOT NULL,
  `heure_debut` time NOT NULL,
  `lieu` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `statut` varchar(50) NOT NULL,
  `lineup_domicile` longtext NOT NULL,
  `lineup_exterieur` longtext DEFAULT NULL,
  `equipe_domicile_id` int(11) NOT NULL,
  `equipe_exterieur_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `matchs`
--

INSERT INTO `matchs` (`id`, `id_match`, `date_match`, `heure_debut`, `lieu`, `type`, `statut`, `lineup_domicile`, `lineup_exterieur`, `equipe_domicile_id`, `equipe_exterieur_id`) VALUES
(1, 'hiuhik', '2029-01-01', '14:00:00', 'hkhkhzk', 'tautguag', 'hiuhi', 'uiajhiak', 'yiuzhi', 1, 1),
(2, '9', '2027-01-01', '15:00:00', 'tunis', 'championat', 'actif', 'd', 'fajljdla', 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `content` longtext NOT NULL,
  `sent_at` datetime NOT NULL,
  `is_read` tinyint(4) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `messenger_messages`
--

INSERT INTO `messenger_messages` (`id`, `body`, `headers`, `queue_name`, `created_at`, `available_at`, `delivered_at`) VALUES
(1, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;N;i:1;N;i:2;s:1068:\\\"<h2>Thank you for your purchase — Sport Insight</h2>\n<p>Dear Lenglizz Ahmed,</p>\n<p>Thank you for your recent purchase. Here are the details of your order:</p>\n<table style=\\\"width:100%; border-collapse: collapse;\\\">\n    <thead>\n        <tr>\n            <th style=\\\"text-align:left; border-bottom:1px solid #ddd; padding:6px\\\">Product</th>\n            <th style=\\\"text-align:left; border-bottom:1px solid #ddd; padding:6px\\\">Quantity</th>\n            <th style=\\\"text-align:left; border-bottom:1px solid #ddd; padding:6px\\\">Price</th>\n        </tr>\n    </thead>\n    <tbody>\n                            <tr>\n                <td style=\\\"padding:6px\\\">uke</td>\n                <td style=\\\"padding:6px\\\">1</td>\n                <td style=\\\"padding:6px\\\">78</td>\n            </tr>\n                            <tr>\n            <td colspan=\\\"2\\\" style=\\\"padding:6px; font-weight:700\\\">Total</td>\n            <td style=\\\"padding:6px; font-weight:700\\\">78</td>\n        </tr>\n    </tbody>\n</table>\n\n<p>If you have any questions, reply to this email.</p>\n<p>Best regards,<br>Sport Insight Team</p>\n\\\";i:3;s:5:\\\"utf-8\\\";i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:28:\\\"no-reply@sport-insight.local\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:25:\\\"marzouk.mohamed@esprit.tn\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:43:\\\"Thank you for your purchase - Sport Insight\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2026-02-12 09:30:47', '2026-02-12 09:30:47', NULL),
(2, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;N;i:1;N;i:2;s:63:\\\"<p>Bonjour Nawres, un nouvel entraînement a été ajouté.</p>\\\";i:3;s:5:\\\"utf-8\\\";i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:22:\\\"coach@sportinsight.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:23:\\\"fekih.nawres7@gmail.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:40:\\\"🏋️ Nouvel entraînement planifié !\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2026-02-22 23:05:50', '2026-02-22 23:05:50', NULL),
(3, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;N;i:1;N;i:2;s:63:\\\"<p>Bonjour Khaled, un nouvel entraînement a été ajouté.</p>\\\";i:3;s:5:\\\"utf-8\\\";i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:22:\\\"coach@sportinsight.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:20:\\\"fekih.nawres@gnet.tn\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:40:\\\"🏋️ Nouvel entraînement planifié !\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2026-02-22 23:05:50', '2026-02-22 23:05:50', NULL),
(4, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;N;i:1;N;i:2;s:63:\\\"<p>Bonjour Nawres, un nouvel entraînement a été ajouté.</p>\\\";i:3;s:5:\\\"utf-8\\\";i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:22:\\\"coach@sportinsight.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:23:\\\"fekih.nawres7@gmail.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:40:\\\"🏋️ Nouvel entraînement planifié !\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2026-02-22 23:24:29', '2026-02-22 23:24:29', NULL),
(5, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;N;i:1;N;i:2;s:63:\\\"<p>Bonjour Khaled, un nouvel entraînement a été ajouté.</p>\\\";i:3;s:5:\\\"utf-8\\\";i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:22:\\\"coach@sportinsight.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:20:\\\"fekih.nawres@gnet.tn\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:40:\\\"🏋️ Nouvel entraînement planifié !\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2026-02-22 23:24:29', '2026-02-22 23:24:29', NULL),
(6, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;N;i:1;N;i:2;s:7262:\\\"<!DOCTYPE html>\r\n<html lang=\\\"fr\\\">\r\n<head>\r\n  <meta charset=\\\"UTF-8\\\">\r\n  <meta name=\\\"viewport\\\" content=\\\"width=device-width, initial-scale=1.0\\\">\r\n  <title>Nouvel entraînement — Sport Insight</title>\r\n</head>\r\n<body style=\\\"margin:0; padding:0; background:#f0f7f2; font-family:\\\'Segoe UI\\\', Arial, sans-serif;\\\">\r\n\r\n  <table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#f0f7f2; padding:40px 20px;\\\">\r\n    <tr>\r\n      <td align=\\\"center\\\">\r\n        <table width=\\\"600\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"max-width:600px; width:100%;\\\">\r\n\r\n          <!-- HEADER -->\r\n          <tr>\r\n            <td style=\\\"background:linear-gradient(135deg,#14532d 0%,#16a34a 60%,#22c55e 100%);\r\n                        border-radius:16px 16px 0 0; padding:36px 40px; text-align:center;\\\">\r\n              <div style=\\\"font-size:48px; margin-bottom:10px;\\\">🏋️</div>\r\n              <h1 style=\\\"margin:0; color:#ffffff; font-size:28px; font-weight:800; letter-spacing:1px;\\\">\r\n                Nouvel Entraînement Planifié !\r\n              </h1>\r\n              <p style=\\\"margin:8px 0 0; color:rgba(255,255,255,0.8); font-size:14px; letter-spacing:2px; text-transform:uppercase;\\\">\r\n                Sport Insight · Coach IA\r\n              </p>\r\n            </td>\r\n          </tr>\r\n\r\n          <!-- BODY -->\r\n          <tr>\r\n            <td style=\\\"background:#ffffff; padding:36px 40px;\\\">\r\n\r\n              <!-- Greeting -->\r\n              <p style=\\\"margin:0 0 24px; font-size:17px; color:#1a2e22; font-weight:600;\\\">\r\n                Bonjour Nawres 👋\r\n              </p>\r\n              <p style=\\\"margin:0 0 28px; font-size:15px; color:#4b7060; line-height:1.7;\\\">\r\n                Un nouvel entraînement a été planifié pour vous. Voici tous les détails importants à retenir :\r\n              </p>\r\n\r\n              <!-- Training Card -->\r\n              <table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:linear-gradient(135deg,#f0fdf4,#ecfdf5);\r\n                      border:1px solid #bbf7d0; border-radius:14px; margin-bottom:28px; overflow:hidden;\\\">\r\n                <tr>\r\n                  <td style=\\\"background:#16a34a; padding:14px 24px;\\\">\r\n                    <span style=\\\"color:#fff; font-size:18px; font-weight:800; text-transform:uppercase; letter-spacing:2px;\\\">\r\n                      🏟 technique\r\n                    </span>\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td style=\\\"padding:24px;\\\">\r\n                    <table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n\r\n                      <!-- Date -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0; border-bottom:1px solid #d1fae5;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">📅</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Date</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">Mercredi 01 Janvier 2031</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                      <!-- Horaire -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0; border-bottom:1px solid #d1fae5;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">⏰</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Horaire</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">13:00 → 04:00</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                      <!-- Lieu -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0; border-bottom:1px solid #d1fae5;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">📍</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Lieu</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">tunis</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                      <!-- Coach -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">🤝</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Entraîneur</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">Mahdi Mahdi</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                    </table>\r\n                  </td>\r\n                </tr>\r\n              </table>\r\n\r\n              <!-- Objectif -->\r\n              <div style=\\\"background:#f8fdf9; border-left:4px solid #16a34a; border-radius:0 10px 10px 0;\r\n                          padding:18px 20px; margin-bottom:28px;\\\">\r\n                <div style=\\\"font-size:12px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77;\r\n                            font-weight:700; margin-bottom:8px;\\\">🎯 Objectif de la séance</div>\r\n                <div style=\\\"font-size:15px; color:#1a2e22; line-height:1.7;\\\">savoir comment gerer</div>\r\n              </div>\r\n\r\n              <!-- CTA hint -->\r\n              <p style=\\\"margin:0; font-size:14px; color:#6b8f77; line-height:1.7; text-align:center;\\\">\r\n                Connectez-vous à votre espace <strong style=\\\"color:#16a34a;\\\">Sport Insight</strong>\r\n                pour confirmer votre participation.\r\n              </p>\r\n\r\n            </td>\r\n          </tr>\r\n\r\n          <!-- FOOTER -->\r\n          <tr>\r\n            <td style=\\\"background:#f0fdf4; border-top:1px solid #d1fae5; border-radius:0 0 16px 16px;\r\n                        padding:20px 40px; text-align:center;\\\">\r\n              <p style=\\\"margin:0; font-size:12px; color:#6b8f77;\\\">\r\n                © 2025 Sport Insight · Ce message a été envoyé automatiquement. Ne pas répondre.\r\n              </p>\r\n            </td>\r\n          </tr>\r\n\r\n        </table>\r\n      </td>\r\n    </tr>\r\n  </table>\r\n\r\n</body>\r\n</html>\\\";i:3;s:5:\\\"utf-8\\\";i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:24:\\\"noreply@sportinsight.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:23:\\\"fekih.nawres7@gmail.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:67:\\\"🏋️ Nouvel entraînement technique — Mercredi 01 Janvier 2031\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2026-02-22 23:32:48', '2026-02-22 23:32:48', NULL),
(7, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;N;i:1;N;i:2;s:7262:\\\"<!DOCTYPE html>\r\n<html lang=\\\"fr\\\">\r\n<head>\r\n  <meta charset=\\\"UTF-8\\\">\r\n  <meta name=\\\"viewport\\\" content=\\\"width=device-width, initial-scale=1.0\\\">\r\n  <title>Nouvel entraînement — Sport Insight</title>\r\n</head>\r\n<body style=\\\"margin:0; padding:0; background:#f0f7f2; font-family:\\\'Segoe UI\\\', Arial, sans-serif;\\\">\r\n\r\n  <table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#f0f7f2; padding:40px 20px;\\\">\r\n    <tr>\r\n      <td align=\\\"center\\\">\r\n        <table width=\\\"600\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"max-width:600px; width:100%;\\\">\r\n\r\n          <!-- HEADER -->\r\n          <tr>\r\n            <td style=\\\"background:linear-gradient(135deg,#14532d 0%,#16a34a 60%,#22c55e 100%);\r\n                        border-radius:16px 16px 0 0; padding:36px 40px; text-align:center;\\\">\r\n              <div style=\\\"font-size:48px; margin-bottom:10px;\\\">🏋️</div>\r\n              <h1 style=\\\"margin:0; color:#ffffff; font-size:28px; font-weight:800; letter-spacing:1px;\\\">\r\n                Nouvel Entraînement Planifié !\r\n              </h1>\r\n              <p style=\\\"margin:8px 0 0; color:rgba(255,255,255,0.8); font-size:14px; letter-spacing:2px; text-transform:uppercase;\\\">\r\n                Sport Insight · Coach IA\r\n              </p>\r\n            </td>\r\n          </tr>\r\n\r\n          <!-- BODY -->\r\n          <tr>\r\n            <td style=\\\"background:#ffffff; padding:36px 40px;\\\">\r\n\r\n              <!-- Greeting -->\r\n              <p style=\\\"margin:0 0 24px; font-size:17px; color:#1a2e22; font-weight:600;\\\">\r\n                Bonjour Khaled 👋\r\n              </p>\r\n              <p style=\\\"margin:0 0 28px; font-size:15px; color:#4b7060; line-height:1.7;\\\">\r\n                Un nouvel entraînement a été planifié pour vous. Voici tous les détails importants à retenir :\r\n              </p>\r\n\r\n              <!-- Training Card -->\r\n              <table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:linear-gradient(135deg,#f0fdf4,#ecfdf5);\r\n                      border:1px solid #bbf7d0; border-radius:14px; margin-bottom:28px; overflow:hidden;\\\">\r\n                <tr>\r\n                  <td style=\\\"background:#16a34a; padding:14px 24px;\\\">\r\n                    <span style=\\\"color:#fff; font-size:18px; font-weight:800; text-transform:uppercase; letter-spacing:2px;\\\">\r\n                      🏟 technique\r\n                    </span>\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td style=\\\"padding:24px;\\\">\r\n                    <table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n\r\n                      <!-- Date -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0; border-bottom:1px solid #d1fae5;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">📅</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Date</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">Mercredi 01 Janvier 2031</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                      <!-- Horaire -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0; border-bottom:1px solid #d1fae5;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">⏰</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Horaire</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">13:00 → 04:00</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                      <!-- Lieu -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0; border-bottom:1px solid #d1fae5;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">📍</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Lieu</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">tunis</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                      <!-- Coach -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">🤝</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Entraîneur</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">Mahdi Mahdi</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                    </table>\r\n                  </td>\r\n                </tr>\r\n              </table>\r\n\r\n              <!-- Objectif -->\r\n              <div style=\\\"background:#f8fdf9; border-left:4px solid #16a34a; border-radius:0 10px 10px 0;\r\n                          padding:18px 20px; margin-bottom:28px;\\\">\r\n                <div style=\\\"font-size:12px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77;\r\n                            font-weight:700; margin-bottom:8px;\\\">🎯 Objectif de la séance</div>\r\n                <div style=\\\"font-size:15px; color:#1a2e22; line-height:1.7;\\\">savoir comment gerer</div>\r\n              </div>\r\n\r\n              <!-- CTA hint -->\r\n              <p style=\\\"margin:0; font-size:14px; color:#6b8f77; line-height:1.7; text-align:center;\\\">\r\n                Connectez-vous à votre espace <strong style=\\\"color:#16a34a;\\\">Sport Insight</strong>\r\n                pour confirmer votre participation.\r\n              </p>\r\n\r\n            </td>\r\n          </tr>\r\n\r\n          <!-- FOOTER -->\r\n          <tr>\r\n            <td style=\\\"background:#f0fdf4; border-top:1px solid #d1fae5; border-radius:0 0 16px 16px;\r\n                        padding:20px 40px; text-align:center;\\\">\r\n              <p style=\\\"margin:0; font-size:12px; color:#6b8f77;\\\">\r\n                © 2025 Sport Insight · Ce message a été envoyé automatiquement. Ne pas répondre.\r\n              </p>\r\n            </td>\r\n          </tr>\r\n\r\n        </table>\r\n      </td>\r\n    </tr>\r\n  </table>\r\n\r\n</body>\r\n</html>\\\";i:3;s:5:\\\"utf-8\\\";i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:24:\\\"noreply@sportinsight.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:20:\\\"fekih.nawres@gnet.tn\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:67:\\\"🏋️ Nouvel entraînement technique — Mercredi 01 Janvier 2031\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2026-02-22 23:32:48', '2026-02-22 23:32:48', NULL);
INSERT INTO `messenger_messages` (`id`, `body`, `headers`, `queue_name`, `created_at`, `available_at`, `delivered_at`) VALUES
(8, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;N;i:1;N;i:2;s:7257:\\\"<!DOCTYPE html>\r\n<html lang=\\\"fr\\\">\r\n<head>\r\n  <meta charset=\\\"UTF-8\\\">\r\n  <meta name=\\\"viewport\\\" content=\\\"width=device-width, initial-scale=1.0\\\">\r\n  <title>Nouvel entraînement — Sport Insight</title>\r\n</head>\r\n<body style=\\\"margin:0; padding:0; background:#f0f7f2; font-family:\\\'Segoe UI\\\', Arial, sans-serif;\\\">\r\n\r\n  <table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#f0f7f2; padding:40px 20px;\\\">\r\n    <tr>\r\n      <td align=\\\"center\\\">\r\n        <table width=\\\"600\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"max-width:600px; width:100%;\\\">\r\n\r\n          <!-- HEADER -->\r\n          <tr>\r\n            <td style=\\\"background:linear-gradient(135deg,#14532d 0%,#16a34a 60%,#22c55e 100%);\r\n                        border-radius:16px 16px 0 0; padding:36px 40px; text-align:center;\\\">\r\n              <div style=\\\"font-size:48px; margin-bottom:10px;\\\">🏋️</div>\r\n              <h1 style=\\\"margin:0; color:#ffffff; font-size:28px; font-weight:800; letter-spacing:1px;\\\">\r\n                Nouvel Entraînement Planifié !\r\n              </h1>\r\n              <p style=\\\"margin:8px 0 0; color:rgba(255,255,255,0.8); font-size:14px; letter-spacing:2px; text-transform:uppercase;\\\">\r\n                Sport Insight · Coach IA\r\n              </p>\r\n            </td>\r\n          </tr>\r\n\r\n          <!-- BODY -->\r\n          <tr>\r\n            <td style=\\\"background:#ffffff; padding:36px 40px;\\\">\r\n\r\n              <!-- Greeting -->\r\n              <p style=\\\"margin:0 0 24px; font-size:17px; color:#1a2e22; font-weight:600;\\\">\r\n                Bonjour tes 👋\r\n              </p>\r\n              <p style=\\\"margin:0 0 28px; font-size:15px; color:#4b7060; line-height:1.7;\\\">\r\n                Un nouvel entraînement a été planifié pour vous. Voici tous les détails importants à retenir :\r\n              </p>\r\n\r\n              <!-- Training Card -->\r\n              <table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:linear-gradient(135deg,#f0fdf4,#ecfdf5);\r\n                      border:1px solid #bbf7d0; border-radius:14px; margin-bottom:28px; overflow:hidden;\\\">\r\n                <tr>\r\n                  <td style=\\\"background:#16a34a; padding:14px 24px;\\\">\r\n                    <span style=\\\"color:#fff; font-size:18px; font-weight:800; text-transform:uppercase; letter-spacing:2px;\\\">\r\n                      🏟 technique\r\n                    </span>\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td style=\\\"padding:24px;\\\">\r\n                    <table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n\r\n                      <!-- Date -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0; border-bottom:1px solid #d1fae5;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">📅</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Date</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">Mercredi 01 Janvier 2031</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                      <!-- Horaire -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0; border-bottom:1px solid #d1fae5;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">⏰</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Horaire</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">18:00 → 14:00</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                      <!-- Lieu -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0; border-bottom:1px solid #d1fae5;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">📍</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Lieu</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">tunis</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                      <!-- Coach -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">🤝</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Entraîneur</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">Mahdi Mahdi</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                    </table>\r\n                  </td>\r\n                </tr>\r\n              </table>\r\n\r\n              <!-- Objectif -->\r\n              <div style=\\\"background:#f8fdf9; border-left:4px solid #16a34a; border-radius:0 10px 10px 0;\r\n                          padding:18px 20px; margin-bottom:28px;\\\">\r\n                <div style=\\\"font-size:12px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77;\r\n                            font-weight:700; margin-bottom:8px;\\\">🎯 Objectif de la séance</div>\r\n                <div style=\\\"font-size:15px; color:#1a2e22; line-height:1.7;\\\">voila entrainement</div>\r\n              </div>\r\n\r\n              <!-- CTA hint -->\r\n              <p style=\\\"margin:0; font-size:14px; color:#6b8f77; line-height:1.7; text-align:center;\\\">\r\n                Connectez-vous à votre espace <strong style=\\\"color:#16a34a;\\\">Sport Insight</strong>\r\n                pour confirmer votre participation.\r\n              </p>\r\n\r\n            </td>\r\n          </tr>\r\n\r\n          <!-- FOOTER -->\r\n          <tr>\r\n            <td style=\\\"background:#f0fdf4; border-top:1px solid #d1fae5; border-radius:0 0 16px 16px;\r\n                        padding:20px 40px; text-align:center;\\\">\r\n              <p style=\\\"margin:0; font-size:12px; color:#6b8f77;\\\">\r\n                © 2025 Sport Insight · Ce message a été envoyé automatiquement. Ne pas répondre.\r\n              </p>\r\n            </td>\r\n          </tr>\r\n\r\n        </table>\r\n      </td>\r\n    </tr>\r\n  </table>\r\n\r\n</body>\r\n</html>\\\";i:3;s:5:\\\"utf-8\\\";i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:24:\\\"noreply@sportinsight.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:25:\\\"fekihtesnime101@gmail.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:67:\\\"🏋️ Nouvel entraînement technique — Mercredi 01 Janvier 2031\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2026-02-22 23:34:28', '2026-02-22 23:34:28', NULL),
(9, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;N;i:1;N;i:2;s:7260:\\\"<!DOCTYPE html>\r\n<html lang=\\\"fr\\\">\r\n<head>\r\n  <meta charset=\\\"UTF-8\\\">\r\n  <meta name=\\\"viewport\\\" content=\\\"width=device-width, initial-scale=1.0\\\">\r\n  <title>Nouvel entraînement — Sport Insight</title>\r\n</head>\r\n<body style=\\\"margin:0; padding:0; background:#f0f7f2; font-family:\\\'Segoe UI\\\', Arial, sans-serif;\\\">\r\n\r\n  <table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#f0f7f2; padding:40px 20px;\\\">\r\n    <tr>\r\n      <td align=\\\"center\\\">\r\n        <table width=\\\"600\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"max-width:600px; width:100%;\\\">\r\n\r\n          <!-- HEADER -->\r\n          <tr>\r\n            <td style=\\\"background:linear-gradient(135deg,#14532d 0%,#16a34a 60%,#22c55e 100%);\r\n                        border-radius:16px 16px 0 0; padding:36px 40px; text-align:center;\\\">\r\n              <div style=\\\"font-size:48px; margin-bottom:10px;\\\">🏋️</div>\r\n              <h1 style=\\\"margin:0; color:#ffffff; font-size:28px; font-weight:800; letter-spacing:1px;\\\">\r\n                Nouvel Entraînement Planifié !\r\n              </h1>\r\n              <p style=\\\"margin:8px 0 0; color:rgba(255,255,255,0.8); font-size:14px; letter-spacing:2px; text-transform:uppercase;\\\">\r\n                Sport Insight · Coach IA\r\n              </p>\r\n            </td>\r\n          </tr>\r\n\r\n          <!-- BODY -->\r\n          <tr>\r\n            <td style=\\\"background:#ffffff; padding:36px 40px;\\\">\r\n\r\n              <!-- Greeting -->\r\n              <p style=\\\"margin:0 0 24px; font-size:17px; color:#1a2e22; font-weight:600;\\\">\r\n                Bonjour Nawres 👋\r\n              </p>\r\n              <p style=\\\"margin:0 0 28px; font-size:15px; color:#4b7060; line-height:1.7;\\\">\r\n                Un nouvel entraînement a été planifié pour vous. Voici tous les détails importants à retenir :\r\n              </p>\r\n\r\n              <!-- Training Card -->\r\n              <table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:linear-gradient(135deg,#f0fdf4,#ecfdf5);\r\n                      border:1px solid #bbf7d0; border-radius:14px; margin-bottom:28px; overflow:hidden;\\\">\r\n                <tr>\r\n                  <td style=\\\"background:#16a34a; padding:14px 24px;\\\">\r\n                    <span style=\\\"color:#fff; font-size:18px; font-weight:800; text-transform:uppercase; letter-spacing:2px;\\\">\r\n                      🏟 technique\r\n                    </span>\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td style=\\\"padding:24px;\\\">\r\n                    <table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n\r\n                      <!-- Date -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0; border-bottom:1px solid #d1fae5;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">📅</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Date</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">Mercredi 01 Janvier 2031</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                      <!-- Horaire -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0; border-bottom:1px solid #d1fae5;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">⏰</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Horaire</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">18:00 → 14:00</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                      <!-- Lieu -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0; border-bottom:1px solid #d1fae5;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">📍</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Lieu</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">tunis</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                      <!-- Coach -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">🤝</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Entraîneur</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">Mahdi Mahdi</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                    </table>\r\n                  </td>\r\n                </tr>\r\n              </table>\r\n\r\n              <!-- Objectif -->\r\n              <div style=\\\"background:#f8fdf9; border-left:4px solid #16a34a; border-radius:0 10px 10px 0;\r\n                          padding:18px 20px; margin-bottom:28px;\\\">\r\n                <div style=\\\"font-size:12px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77;\r\n                            font-weight:700; margin-bottom:8px;\\\">🎯 Objectif de la séance</div>\r\n                <div style=\\\"font-size:15px; color:#1a2e22; line-height:1.7;\\\">voila entrainement</div>\r\n              </div>\r\n\r\n              <!-- CTA hint -->\r\n              <p style=\\\"margin:0; font-size:14px; color:#6b8f77; line-height:1.7; text-align:center;\\\">\r\n                Connectez-vous à votre espace <strong style=\\\"color:#16a34a;\\\">Sport Insight</strong>\r\n                pour confirmer votre participation.\r\n              </p>\r\n\r\n            </td>\r\n          </tr>\r\n\r\n          <!-- FOOTER -->\r\n          <tr>\r\n            <td style=\\\"background:#f0fdf4; border-top:1px solid #d1fae5; border-radius:0 0 16px 16px;\r\n                        padding:20px 40px; text-align:center;\\\">\r\n              <p style=\\\"margin:0; font-size:12px; color:#6b8f77;\\\">\r\n                © 2025 Sport Insight · Ce message a été envoyé automatiquement. Ne pas répondre.\r\n              </p>\r\n            </td>\r\n          </tr>\r\n\r\n        </table>\r\n      </td>\r\n    </tr>\r\n  </table>\r\n\r\n</body>\r\n</html>\\\";i:3;s:5:\\\"utf-8\\\";i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:24:\\\"noreply@sportinsight.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:23:\\\"fekih.nawres7@gmail.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:67:\\\"🏋️ Nouvel entraînement technique — Mercredi 01 Janvier 2031\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2026-02-22 23:34:28', '2026-02-22 23:34:28', NULL),
(10, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;N;i:1;N;i:2;s:7260:\\\"<!DOCTYPE html>\r\n<html lang=\\\"fr\\\">\r\n<head>\r\n  <meta charset=\\\"UTF-8\\\">\r\n  <meta name=\\\"viewport\\\" content=\\\"width=device-width, initial-scale=1.0\\\">\r\n  <title>Nouvel entraînement — Sport Insight</title>\r\n</head>\r\n<body style=\\\"margin:0; padding:0; background:#f0f7f2; font-family:\\\'Segoe UI\\\', Arial, sans-serif;\\\">\r\n\r\n  <table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#f0f7f2; padding:40px 20px;\\\">\r\n    <tr>\r\n      <td align=\\\"center\\\">\r\n        <table width=\\\"600\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"max-width:600px; width:100%;\\\">\r\n\r\n          <!-- HEADER -->\r\n          <tr>\r\n            <td style=\\\"background:linear-gradient(135deg,#14532d 0%,#16a34a 60%,#22c55e 100%);\r\n                        border-radius:16px 16px 0 0; padding:36px 40px; text-align:center;\\\">\r\n              <div style=\\\"font-size:48px; margin-bottom:10px;\\\">🏋️</div>\r\n              <h1 style=\\\"margin:0; color:#ffffff; font-size:28px; font-weight:800; letter-spacing:1px;\\\">\r\n                Nouvel Entraînement Planifié !\r\n              </h1>\r\n              <p style=\\\"margin:8px 0 0; color:rgba(255,255,255,0.8); font-size:14px; letter-spacing:2px; text-transform:uppercase;\\\">\r\n                Sport Insight · Coach IA\r\n              </p>\r\n            </td>\r\n          </tr>\r\n\r\n          <!-- BODY -->\r\n          <tr>\r\n            <td style=\\\"background:#ffffff; padding:36px 40px;\\\">\r\n\r\n              <!-- Greeting -->\r\n              <p style=\\\"margin:0 0 24px; font-size:17px; color:#1a2e22; font-weight:600;\\\">\r\n                Bonjour Khaled 👋\r\n              </p>\r\n              <p style=\\\"margin:0 0 28px; font-size:15px; color:#4b7060; line-height:1.7;\\\">\r\n                Un nouvel entraînement a été planifié pour vous. Voici tous les détails importants à retenir :\r\n              </p>\r\n\r\n              <!-- Training Card -->\r\n              <table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:linear-gradient(135deg,#f0fdf4,#ecfdf5);\r\n                      border:1px solid #bbf7d0; border-radius:14px; margin-bottom:28px; overflow:hidden;\\\">\r\n                <tr>\r\n                  <td style=\\\"background:#16a34a; padding:14px 24px;\\\">\r\n                    <span style=\\\"color:#fff; font-size:18px; font-weight:800; text-transform:uppercase; letter-spacing:2px;\\\">\r\n                      🏟 technique\r\n                    </span>\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td style=\\\"padding:24px;\\\">\r\n                    <table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n\r\n                      <!-- Date -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0; border-bottom:1px solid #d1fae5;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">📅</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Date</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">Mercredi 01 Janvier 2031</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                      <!-- Horaire -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0; border-bottom:1px solid #d1fae5;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">⏰</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Horaire</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">18:00 → 14:00</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                      <!-- Lieu -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0; border-bottom:1px solid #d1fae5;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">📍</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Lieu</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">tunis</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                      <!-- Coach -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">🤝</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Entraîneur</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">Mahdi Mahdi</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                    </table>\r\n                  </td>\r\n                </tr>\r\n              </table>\r\n\r\n              <!-- Objectif -->\r\n              <div style=\\\"background:#f8fdf9; border-left:4px solid #16a34a; border-radius:0 10px 10px 0;\r\n                          padding:18px 20px; margin-bottom:28px;\\\">\r\n                <div style=\\\"font-size:12px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77;\r\n                            font-weight:700; margin-bottom:8px;\\\">🎯 Objectif de la séance</div>\r\n                <div style=\\\"font-size:15px; color:#1a2e22; line-height:1.7;\\\">voila entrainement</div>\r\n              </div>\r\n\r\n              <!-- CTA hint -->\r\n              <p style=\\\"margin:0; font-size:14px; color:#6b8f77; line-height:1.7; text-align:center;\\\">\r\n                Connectez-vous à votre espace <strong style=\\\"color:#16a34a;\\\">Sport Insight</strong>\r\n                pour confirmer votre participation.\r\n              </p>\r\n\r\n            </td>\r\n          </tr>\r\n\r\n          <!-- FOOTER -->\r\n          <tr>\r\n            <td style=\\\"background:#f0fdf4; border-top:1px solid #d1fae5; border-radius:0 0 16px 16px;\r\n                        padding:20px 40px; text-align:center;\\\">\r\n              <p style=\\\"margin:0; font-size:12px; color:#6b8f77;\\\">\r\n                © 2025 Sport Insight · Ce message a été envoyé automatiquement. Ne pas répondre.\r\n              </p>\r\n            </td>\r\n          </tr>\r\n\r\n        </table>\r\n      </td>\r\n    </tr>\r\n  </table>\r\n\r\n</body>\r\n</html>\\\";i:3;s:5:\\\"utf-8\\\";i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:24:\\\"noreply@sportinsight.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:20:\\\"fekih.nawres@gnet.tn\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:67:\\\"🏋️ Nouvel entraînement technique — Mercredi 01 Janvier 2031\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2026-02-22 23:34:28', '2026-02-22 23:34:28', NULL),
(11, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;N;i:1;N;i:2;s:7239:\\\"<!DOCTYPE html>\r\n<html lang=\\\"fr\\\">\r\n<head>\r\n  <meta charset=\\\"UTF-8\\\">\r\n  <meta name=\\\"viewport\\\" content=\\\"width=device-width, initial-scale=1.0\\\">\r\n  <title>Nouvel entraînement — Sport Insight</title>\r\n</head>\r\n<body style=\\\"margin:0; padding:0; background:#f0f7f2; font-family:\\\'Segoe UI\\\', Arial, sans-serif;\\\">\r\n\r\n  <table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#f0f7f2; padding:40px 20px;\\\">\r\n    <tr>\r\n      <td align=\\\"center\\\">\r\n        <table width=\\\"600\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"max-width:600px; width:100%;\\\">\r\n\r\n          <!-- HEADER -->\r\n          <tr>\r\n            <td style=\\\"background:linear-gradient(135deg,#14532d 0%,#16a34a 60%,#22c55e 100%);\r\n                        border-radius:16px 16px 0 0; padding:36px 40px; text-align:center;\\\">\r\n              <div style=\\\"font-size:48px; margin-bottom:10px;\\\">🏋️</div>\r\n              <h1 style=\\\"margin:0; color:#ffffff; font-size:28px; font-weight:800; letter-spacing:1px;\\\">\r\n                Nouvel Entraînement Planifié !\r\n              </h1>\r\n              <p style=\\\"margin:8px 0 0; color:rgba(255,255,255,0.8); font-size:14px; letter-spacing:2px; text-transform:uppercase;\\\">\r\n                Sport Insight · Coach IA\r\n              </p>\r\n            </td>\r\n          </tr>\r\n\r\n          <!-- BODY -->\r\n          <tr>\r\n            <td style=\\\"background:#ffffff; padding:36px 40px;\\\">\r\n\r\n              <!-- Greeting -->\r\n              <p style=\\\"margin:0 0 24px; font-size:17px; color:#1a2e22; font-weight:600;\\\">\r\n                Bonjour tes 👋\r\n              </p>\r\n              <p style=\\\"margin:0 0 28px; font-size:15px; color:#4b7060; line-height:1.7;\\\">\r\n                Un nouvel entraînement a été planifié pour vous. Voici tous les détails importants à retenir :\r\n              </p>\r\n\r\n              <!-- Training Card -->\r\n              <table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:linear-gradient(135deg,#f0fdf4,#ecfdf5);\r\n                      border:1px solid #bbf7d0; border-radius:14px; margin-bottom:28px; overflow:hidden;\\\">\r\n                <tr>\r\n                  <td style=\\\"background:#16a34a; padding:14px 24px;\\\">\r\n                    <span style=\\\"color:#fff; font-size:18px; font-weight:800; text-transform:uppercase; letter-spacing:2px;\\\">\r\n                      🏟 tactique\r\n                    </span>\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td style=\\\"padding:24px;\\\">\r\n                    <table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n\r\n                      <!-- Date -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0; border-bottom:1px solid #d1fae5;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">📅</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Date</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">Lundi 01 Janvier 2029</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                      <!-- Horaire -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0; border-bottom:1px solid #d1fae5;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">⏰</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Horaire</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">00:00 → 12:00</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                      <!-- Lieu -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0; border-bottom:1px solid #d1fae5;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">📍</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Lieu</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">tunis</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                      <!-- Coach -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">🤝</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Entraîneur</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">Mahdi Mahdi</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                    </table>\r\n                  </td>\r\n                </tr>\r\n              </table>\r\n\r\n              <!-- Objectif -->\r\n              <div style=\\\"background:#f8fdf9; border-left:4px solid #16a34a; border-radius:0 10px 10px 0;\r\n                          padding:18px 20px; margin-bottom:28px;\\\">\r\n                <div style=\\\"font-size:12px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77;\r\n                            font-weight:700; margin-bottom:8px;\\\">🎯 Objectif de la séance</div>\r\n                <div style=\\\"font-size:15px; color:#1a2e22; line-height:1.7;\\\">quoi</div>\r\n              </div>\r\n\r\n              <!-- CTA hint -->\r\n              <p style=\\\"margin:0; font-size:14px; color:#6b8f77; line-height:1.7; text-align:center;\\\">\r\n                Connectez-vous à votre espace <strong style=\\\"color:#16a34a;\\\">Sport Insight</strong>\r\n                pour confirmer votre participation.\r\n              </p>\r\n\r\n            </td>\r\n          </tr>\r\n\r\n          <!-- FOOTER -->\r\n          <tr>\r\n            <td style=\\\"background:#f0fdf4; border-top:1px solid #d1fae5; border-radius:0 0 16px 16px;\r\n                        padding:20px 40px; text-align:center;\\\">\r\n              <p style=\\\"margin:0; font-size:12px; color:#6b8f77;\\\">\r\n                © 2025 Sport Insight · Ce message a été envoyé automatiquement. Ne pas répondre.\r\n              </p>\r\n            </td>\r\n          </tr>\r\n\r\n        </table>\r\n      </td>\r\n    </tr>\r\n  </table>\r\n\r\n</body>\r\n</html>\\\";i:3;s:5:\\\"utf-8\\\";i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:24:\\\"noreply@sportinsight.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:25:\\\"fekihtesnime101@gmail.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:63:\\\"🏋️ Nouvel entraînement tactique — Lundi 01 Janvier 2029\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2026-02-23 00:16:40', '2026-02-23 00:16:40', NULL);
INSERT INTO `messenger_messages` (`id`, `body`, `headers`, `queue_name`, `created_at`, `available_at`, `delivered_at`) VALUES
(12, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;N;i:1;N;i:2;s:7242:\\\"<!DOCTYPE html>\r\n<html lang=\\\"fr\\\">\r\n<head>\r\n  <meta charset=\\\"UTF-8\\\">\r\n  <meta name=\\\"viewport\\\" content=\\\"width=device-width, initial-scale=1.0\\\">\r\n  <title>Nouvel entraînement — Sport Insight</title>\r\n</head>\r\n<body style=\\\"margin:0; padding:0; background:#f0f7f2; font-family:\\\'Segoe UI\\\', Arial, sans-serif;\\\">\r\n\r\n  <table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#f0f7f2; padding:40px 20px;\\\">\r\n    <tr>\r\n      <td align=\\\"center\\\">\r\n        <table width=\\\"600\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"max-width:600px; width:100%;\\\">\r\n\r\n          <!-- HEADER -->\r\n          <tr>\r\n            <td style=\\\"background:linear-gradient(135deg,#14532d 0%,#16a34a 60%,#22c55e 100%);\r\n                        border-radius:16px 16px 0 0; padding:36px 40px; text-align:center;\\\">\r\n              <div style=\\\"font-size:48px; margin-bottom:10px;\\\">🏋️</div>\r\n              <h1 style=\\\"margin:0; color:#ffffff; font-size:28px; font-weight:800; letter-spacing:1px;\\\">\r\n                Nouvel Entraînement Planifié !\r\n              </h1>\r\n              <p style=\\\"margin:8px 0 0; color:rgba(255,255,255,0.8); font-size:14px; letter-spacing:2px; text-transform:uppercase;\\\">\r\n                Sport Insight · Coach IA\r\n              </p>\r\n            </td>\r\n          </tr>\r\n\r\n          <!-- BODY -->\r\n          <tr>\r\n            <td style=\\\"background:#ffffff; padding:36px 40px;\\\">\r\n\r\n              <!-- Greeting -->\r\n              <p style=\\\"margin:0 0 24px; font-size:17px; color:#1a2e22; font-weight:600;\\\">\r\n                Bonjour Nawres 👋\r\n              </p>\r\n              <p style=\\\"margin:0 0 28px; font-size:15px; color:#4b7060; line-height:1.7;\\\">\r\n                Un nouvel entraînement a été planifié pour vous. Voici tous les détails importants à retenir :\r\n              </p>\r\n\r\n              <!-- Training Card -->\r\n              <table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:linear-gradient(135deg,#f0fdf4,#ecfdf5);\r\n                      border:1px solid #bbf7d0; border-radius:14px; margin-bottom:28px; overflow:hidden;\\\">\r\n                <tr>\r\n                  <td style=\\\"background:#16a34a; padding:14px 24px;\\\">\r\n                    <span style=\\\"color:#fff; font-size:18px; font-weight:800; text-transform:uppercase; letter-spacing:2px;\\\">\r\n                      🏟 tactique\r\n                    </span>\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td style=\\\"padding:24px;\\\">\r\n                    <table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n\r\n                      <!-- Date -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0; border-bottom:1px solid #d1fae5;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">📅</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Date</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">Lundi 01 Janvier 2029</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                      <!-- Horaire -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0; border-bottom:1px solid #d1fae5;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">⏰</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Horaire</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">00:00 → 12:00</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                      <!-- Lieu -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0; border-bottom:1px solid #d1fae5;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">📍</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Lieu</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">tunis</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                      <!-- Coach -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">🤝</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Entraîneur</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">Mahdi Mahdi</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                    </table>\r\n                  </td>\r\n                </tr>\r\n              </table>\r\n\r\n              <!-- Objectif -->\r\n              <div style=\\\"background:#f8fdf9; border-left:4px solid #16a34a; border-radius:0 10px 10px 0;\r\n                          padding:18px 20px; margin-bottom:28px;\\\">\r\n                <div style=\\\"font-size:12px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77;\r\n                            font-weight:700; margin-bottom:8px;\\\">🎯 Objectif de la séance</div>\r\n                <div style=\\\"font-size:15px; color:#1a2e22; line-height:1.7;\\\">quoi</div>\r\n              </div>\r\n\r\n              <!-- CTA hint -->\r\n              <p style=\\\"margin:0; font-size:14px; color:#6b8f77; line-height:1.7; text-align:center;\\\">\r\n                Connectez-vous à votre espace <strong style=\\\"color:#16a34a;\\\">Sport Insight</strong>\r\n                pour confirmer votre participation.\r\n              </p>\r\n\r\n            </td>\r\n          </tr>\r\n\r\n          <!-- FOOTER -->\r\n          <tr>\r\n            <td style=\\\"background:#f0fdf4; border-top:1px solid #d1fae5; border-radius:0 0 16px 16px;\r\n                        padding:20px 40px; text-align:center;\\\">\r\n              <p style=\\\"margin:0; font-size:12px; color:#6b8f77;\\\">\r\n                © 2025 Sport Insight · Ce message a été envoyé automatiquement. Ne pas répondre.\r\n              </p>\r\n            </td>\r\n          </tr>\r\n\r\n        </table>\r\n      </td>\r\n    </tr>\r\n  </table>\r\n\r\n</body>\r\n</html>\\\";i:3;s:5:\\\"utf-8\\\";i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:24:\\\"noreply@sportinsight.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:23:\\\"fekih.nawres7@gmail.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:63:\\\"🏋️ Nouvel entraînement tactique — Lundi 01 Janvier 2029\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2026-02-23 00:16:40', '2026-02-23 00:16:40', NULL),
(13, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;N;i:1;N;i:2;s:7242:\\\"<!DOCTYPE html>\r\n<html lang=\\\"fr\\\">\r\n<head>\r\n  <meta charset=\\\"UTF-8\\\">\r\n  <meta name=\\\"viewport\\\" content=\\\"width=device-width, initial-scale=1.0\\\">\r\n  <title>Nouvel entraînement — Sport Insight</title>\r\n</head>\r\n<body style=\\\"margin:0; padding:0; background:#f0f7f2; font-family:\\\'Segoe UI\\\', Arial, sans-serif;\\\">\r\n\r\n  <table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#f0f7f2; padding:40px 20px;\\\">\r\n    <tr>\r\n      <td align=\\\"center\\\">\r\n        <table width=\\\"600\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"max-width:600px; width:100%;\\\">\r\n\r\n          <!-- HEADER -->\r\n          <tr>\r\n            <td style=\\\"background:linear-gradient(135deg,#14532d 0%,#16a34a 60%,#22c55e 100%);\r\n                        border-radius:16px 16px 0 0; padding:36px 40px; text-align:center;\\\">\r\n              <div style=\\\"font-size:48px; margin-bottom:10px;\\\">🏋️</div>\r\n              <h1 style=\\\"margin:0; color:#ffffff; font-size:28px; font-weight:800; letter-spacing:1px;\\\">\r\n                Nouvel Entraînement Planifié !\r\n              </h1>\r\n              <p style=\\\"margin:8px 0 0; color:rgba(255,255,255,0.8); font-size:14px; letter-spacing:2px; text-transform:uppercase;\\\">\r\n                Sport Insight · Coach IA\r\n              </p>\r\n            </td>\r\n          </tr>\r\n\r\n          <!-- BODY -->\r\n          <tr>\r\n            <td style=\\\"background:#ffffff; padding:36px 40px;\\\">\r\n\r\n              <!-- Greeting -->\r\n              <p style=\\\"margin:0 0 24px; font-size:17px; color:#1a2e22; font-weight:600;\\\">\r\n                Bonjour Khaled 👋\r\n              </p>\r\n              <p style=\\\"margin:0 0 28px; font-size:15px; color:#4b7060; line-height:1.7;\\\">\r\n                Un nouvel entraînement a été planifié pour vous. Voici tous les détails importants à retenir :\r\n              </p>\r\n\r\n              <!-- Training Card -->\r\n              <table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:linear-gradient(135deg,#f0fdf4,#ecfdf5);\r\n                      border:1px solid #bbf7d0; border-radius:14px; margin-bottom:28px; overflow:hidden;\\\">\r\n                <tr>\r\n                  <td style=\\\"background:#16a34a; padding:14px 24px;\\\">\r\n                    <span style=\\\"color:#fff; font-size:18px; font-weight:800; text-transform:uppercase; letter-spacing:2px;\\\">\r\n                      🏟 tactique\r\n                    </span>\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td style=\\\"padding:24px;\\\">\r\n                    <table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n\r\n                      <!-- Date -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0; border-bottom:1px solid #d1fae5;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">📅</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Date</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">Lundi 01 Janvier 2029</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                      <!-- Horaire -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0; border-bottom:1px solid #d1fae5;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">⏰</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Horaire</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">00:00 → 12:00</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                      <!-- Lieu -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0; border-bottom:1px solid #d1fae5;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">📍</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Lieu</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">tunis</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                      <!-- Coach -->\r\n                      <tr>\r\n                        <td style=\\\"padding:10px 0;\\\">\r\n                          <table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\">\r\n                            <tr>\r\n                              <td style=\\\"width:36px; font-size:22px;\\\">🤝</td>\r\n                              <td>\r\n                                <div style=\\\"font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;\\\">Entraîneur</div>\r\n                                <div style=\\\"font-size:16px; font-weight:700; color:#14532d;\\\">Mahdi Mahdi</div>\r\n                              </td>\r\n                            </tr>\r\n                          </table>\r\n                        </td>\r\n                      </tr>\r\n\r\n                    </table>\r\n                  </td>\r\n                </tr>\r\n              </table>\r\n\r\n              <!-- Objectif -->\r\n              <div style=\\\"background:#f8fdf9; border-left:4px solid #16a34a; border-radius:0 10px 10px 0;\r\n                          padding:18px 20px; margin-bottom:28px;\\\">\r\n                <div style=\\\"font-size:12px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77;\r\n                            font-weight:700; margin-bottom:8px;\\\">🎯 Objectif de la séance</div>\r\n                <div style=\\\"font-size:15px; color:#1a2e22; line-height:1.7;\\\">quoi</div>\r\n              </div>\r\n\r\n              <!-- CTA hint -->\r\n              <p style=\\\"margin:0; font-size:14px; color:#6b8f77; line-height:1.7; text-align:center;\\\">\r\n                Connectez-vous à votre espace <strong style=\\\"color:#16a34a;\\\">Sport Insight</strong>\r\n                pour confirmer votre participation.\r\n              </p>\r\n\r\n            </td>\r\n          </tr>\r\n\r\n          <!-- FOOTER -->\r\n          <tr>\r\n            <td style=\\\"background:#f0fdf4; border-top:1px solid #d1fae5; border-radius:0 0 16px 16px;\r\n                        padding:20px 40px; text-align:center;\\\">\r\n              <p style=\\\"margin:0; font-size:12px; color:#6b8f77;\\\">\r\n                © 2025 Sport Insight · Ce message a été envoyé automatiquement. Ne pas répondre.\r\n              </p>\r\n            </td>\r\n          </tr>\r\n\r\n        </table>\r\n      </td>\r\n    </tr>\r\n  </table>\r\n\r\n</body>\r\n</html>\\\";i:3;s:5:\\\"utf-8\\\";i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:24:\\\"noreply@sportinsight.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:20:\\\"fekih.nawres@gnet.tn\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:63:\\\"🏋️ Nouvel entraînement tactique — Lundi 01 Janvier 2029\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2026-02-23 00:16:40', '2026-02-23 00:16:40', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `notification`
--

CREATE TABLE `notification` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `is_read` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `notification`
--

INSERT INTO `notification` (`id`, `user_id`, `message`, `created_at`, `is_read`) VALUES
(1, 16, 'Nouvel entraînement de technique le 01/01/2028', '2026-02-23 00:05:48', 0),
(2, 17, 'Nouvel entraînement de technique le 01/01/2028', '2026-02-23 00:05:50', 0),
(3, 16, 'Nouvel entraînement de technique le 01/01/2031', '2026-02-23 00:24:28', 0),
(4, 17, 'Nouvel entraînement de technique le 01/01/2031', '2026-02-23 00:24:29', 0),
(5, 16, 'Nouvel entraînement de technique le 01/01/2031 à 13:00 — tunis', '2026-02-23 00:32:48', 0),
(6, 17, 'Nouvel entraînement de technique le 01/01/2031 à 13:00 — tunis', '2026-02-23 00:32:48', 0),
(7, 15, 'Nouvel entraînement de technique le 01/01/2031 à 18:00 — tunis', '2026-02-23 00:34:28', 0),
(8, 16, 'Nouvel entraînement de technique le 01/01/2031 à 18:00 — tunis', '2026-02-23 00:34:28', 0),
(9, 17, 'Nouvel entraînement de technique le 01/01/2031 à 18:00 — tunis', '2026-02-23 00:34:28', 0),
(10, 15, 'Nouvel entraînement de tactique le 01/01/2029 à 00:00 — tunis', '2026-02-23 01:16:36', 0),
(11, 16, 'Nouvel entraînement de tactique le 01/01/2029 à 00:00 — tunis', '2026-02-23 01:16:40', 0),
(12, 17, 'Nouvel entraînement de tactique le 01/01/2029 à 00:00 — tunis', '2026-02-23 01:16:40', 0),
(13, 15, 'Nouvel entraînement de tactique le 01/01/2029 à 12:00 — tunis', '2026-02-23 21:04:41', 0),
(14, 15, 'Nouvel entraînement de tactique le 01/01/2031 à 10:00 — tunis', '2026-02-23 21:06:38', 0),
(15, 15, 'Nouvel entraînement de technique le 01/01/2031 à 07:00 — sfax', '2026-02-23 21:15:37', 0),
(16, 15, 'Nouvel entraînement de tactique le 01/01/2030 à 00:00 — sfax', '2026-02-23 21:20:20', 0),
(17, 15, 'Nouvel entraînement de tactique le 15/01/2029 à 04:00 — sfax', '2026-02-23 21:35:42', 0),
(18, 15, 'Nouvel entraînement de tactique le 01/01/2030 à 06:00 — sfax', '2026-02-23 21:36:55', 0),
(19, 15, 'Nouvel entraînement de tactique le 01/01/2027 à 08:00 — sfax', '2026-02-24 14:29:56', 0),
(20, 15, 'Nouvel entraînement de tactique le 13/04/2026 à 05:00 — sfax', '2026-02-24 19:24:15', 0),
(21, 15, 'Nouvel entraînement de tactique le 13/08/2026 à 14:00 — sfax', '2026-02-24 19:42:39', 0);

-- --------------------------------------------------------

--
-- Structure de la table `order`
--

CREATE TABLE `order` (
  `id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `order_date` date NOT NULL,
  `status` varchar(20) NOT NULL,
  `product_id` int(11) NOT NULL,
  `entraineur_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `order`
--

INSERT INTO `order` (`id`, `quantity`, `order_date`, `status`, `product_id`, `entraineur_id`) VALUES
(1, 1, '2026-02-12', 'confirmed', 1, 14);

-- --------------------------------------------------------

--
-- Structure de la table `participation`
--

CREATE TABLE `participation` (
  `id` int(11) NOT NULL,
  `presence` varchar(255) NOT NULL,
  `justification_absence` longtext DEFAULT NULL,
  `entrainement_id` int(11) NOT NULL,
  `joueur_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `participation`
--

INSERT INTO `participation` (`id`, `presence`, `justification_absence`, `entrainement_id`, `joueur_id`) VALUES
(2, 'present', NULL, 3, 7),
(3, 'absent', NULL, 4, 7),
(4, 'present', NULL, 4, 10),
(5, 'absent', '', 4, 14),
(6, 'present', '', 3, 14),
(7, 'present', '', 5, 17),
(8, 'present', '', 5, 14),
(9, 'present', '', 16, 14),
(10, 'present', '', 15, 14),
(11, 'absent', '', 14, 14),
(12, 'present', '', 18, 15);

-- --------------------------------------------------------

--
-- Structure de la table `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `price` decimal(10,0) NOT NULL,
  `stock` int(11) NOT NULL,
  `size` varchar(10) DEFAULT NULL,
  `brand` varchar(30) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `product`
--

INSERT INTO `product` (`id`, `name`, `category`, `price`, `stock`, `size`, `brand`, `image`) VALUES
(1, 'uke', NULL, 78, 0, '36', 'ajkja', 'C:\\xampp\\tmp\\phpBC5D.tmp');

-- --------------------------------------------------------

--
-- Structure de la table `sponsor`
--

CREATE TABLE `sponsor` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `budget` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `sponsor`
--

INSERT INTO `sponsor` (`id`, `nom`, `email`, `telephone`, `budget`) VALUES
(1, 'ahmed lenglizz', 'marzouk.mohamed@esprit.tn', '28053933', 12344444),
(2, 'HKHK', 'fekih.nawres7@gmail.com', '06174485', 34),
(3, 'HKHAK', 'fekih.nawres7@gmail.com', '06174485', 34),
(4, 'rym', 'fekih.nawres7@gmail.com', '06174485', 34);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(180) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`roles`)),
  `password` varchar(255) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `statut` varchar(20) NOT NULL,
  `date_inscription` datetime NOT NULL,
  `cv_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `nom`, `prenom`, `telephone`, `date_naissance`, `photo`, `statut`, `date_inscription`, `cv_name`) VALUES
(7, 'user@test.com', '[\"ROLE_USER\"]', '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Utilisateur', 'Test', '87654321', '1995-05-15', NULL, 'actif', '2026-02-11 18:05:15', NULL),
(8, 'entraineur@test.com', '[]', '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dupont', 'Jean', '11223349', '1985-03-20', NULL, 'actif', '2026-02-11 18:05:15', NULL),
(9, 'joueur@test.com', '[\"ROLE_USER\"]', '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Martin', 'Pierre', '55667788', '2000-07-10', NULL, 'actif', '2026-02-11 18:05:15', NULL),
(10, 'blocked@test.com', '[\"ROLE_USER\"]', '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bloqué', 'Utilisateur', '99887766', '1992-12-25', NULL, 'bloque', '2026-02-11 18:05:15', NULL),
(14, 'marzouk.mohamed@esprit.tn', '[\"ROLE_ADMIN\"]', '$2y$13$6WxtXvfRyILphUu7TF/Cq.kWvjPjcLs3CK23Fc0qtZLBSVK3Ll4JO', 'Lenglizz', 'Ahmed', '+21628053933', '2000-01-11', '1-698cbfa983b5b.png', 'actif', '2026-02-11 18:43:04', NULL),
(15, 'fekihtesnime101@gmail.com', '[\"ROLE_JOUEUR\"]', '$2y$13$hUmF38WgUaLps0BrmVHuf.KVPrILI4CkutT/rkjY5c8ZDola2PA06', 'tes', 'tes', '+21695883688', '1999-03-05', NULL, 'actif', '2026-02-12 00:02:00', NULL),
(16, 'fekih.nawres7@gmail.com', '[\"ROLE_JOUEUR\"]', '$2y$13$4d0gN6HoFWnl.v0FlDpe/uX3jUewhGbrTkOLa7zXeaVGtL7vFHbK6', 'Khaled', 'Nawres', '+33617448577', '2009-02-13', NULL, 'actif', '2026-02-12 10:17:23', NULL),
(17, 'fekih.nawres@gnet.tn', '[\"ROLE_JOUEUR\"]', '$2y$13$4cldq6ZIBJLTgnPcCgs66ewt9NQcc.PnXEYPF/sbKz5zSiMP.XNtK', 'Khaled', 'Khaled', '+21697328545', '2008-02-08', NULL, 'actif', '2026-02-12 11:14:28', NULL),
(18, 'bennjimamariem99@gmail.com', '[\"ROLE_ENTRAINEUR\"]', '$2y$13$8aoPCTZvg2DEo1SnLl70sOIE8dQI1IhxVBXywzMTHzVXlXAFchwC6', 'Mahdi', 'Mahdi', '+21695883688', '1999-03-05', NULL, 'actif', '2026-02-23 00:23:17', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `annonce`
--
ALTER TABLE `annonce`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_F65593E5F8478A1` (`entraineur_id`);

--
-- Index pour la table `chat_message`
--
ALTER TABLE `chat_message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_FAB3FC1660BB6FE6` (`auteur_id`),
  ADD KEY `IDX_FAB3FC16A4F84F6E` (`destinataire_id`),
  ADD KEY `IDX_FAB3FC168805AB2F` (`annonce_id`);

--
-- Index pour la table `commentaire`
--
ALTER TABLE `commentaire`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_67F068BCA9E2D76C` (`joueur_id`),
  ADD KEY `IDX_67F068BC8805AB2F` (`annonce_id`);

--
-- Index pour la table `contrat_sponsor`
--
ALTER TABLE `contrat_sponsor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_28429AA212F7FB51` (`sponsor_id`),
  ADD KEY `IDX_28429AA26D861B89` (`equipe_id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `entrainement`
--
ALTER TABLE `entrainement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_A27444E5F8478A1` (`entraineur_id`);

--
-- Index pour la table `entrainement_user`
--
ALTER TABLE `entrainement_user`
  ADD PRIMARY KEY (`entrainement_id`,`user_id`),
  ADD KEY `IDX_EB3D3F70A15E8FD` (`entrainement_id`),
  ADD KEY `IDX_EB3D3F70A76ED395` (`user_id`);

--
-- Index pour la table `equipe`
--
ALTER TABLE `equipe`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `evaluation`
--
ALTER TABLE `evaluation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_1323A575A15E8FD` (`entrainement_id`),
  ADD KEY `IDX_1323A575A9E2D76C` (`joueur_id`);

--
-- Index pour la table `matchs`
--
ALTER TABLE `matchs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_6B1E60415FE1AEAD` (`equipe_domicile_id`),
  ADD KEY `IDX_6B1E604121ECD755` (`equipe_exterieur_id`);

--
-- Index pour la table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`);

--
-- Index pour la table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_BF5476CAA76ED395` (`user_id`);

--
-- Index pour la table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_F52993984584665A` (`product_id`),
  ADD KEY `IDX_F5299398F8478A1` (`entraineur_id`);

--
-- Index pour la table `participation`
--
ALTER TABLE `participation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_AB55E24FA15E8FD` (`entrainement_id`),
  ADD KEY `IDX_AB55E24FA9E2D76C` (`joueur_id`);

--
-- Index pour la table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `sponsor`
--
ALTER TABLE `sponsor`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `annonce`
--
ALTER TABLE `annonce`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `chat_message`
--
ALTER TABLE `chat_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `commentaire`
--
ALTER TABLE `commentaire`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `contrat_sponsor`
--
ALTER TABLE `contrat_sponsor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `entrainement`
--
ALTER TABLE `entrainement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `equipe`
--
ALTER TABLE `equipe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `evaluation`
--
ALTER TABLE `evaluation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `matchs`
--
ALTER TABLE `matchs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT pour la table `order`
--
ALTER TABLE `order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `participation`
--
ALTER TABLE `participation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `sponsor`
--
ALTER TABLE `sponsor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `annonce`
--
ALTER TABLE `annonce`
  ADD CONSTRAINT `FK_F65593E5F8478A1` FOREIGN KEY (`entraineur_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `chat_message`
--
ALTER TABLE `chat_message`
  ADD CONSTRAINT `FK_FAB3FC1660BB6FE6` FOREIGN KEY (`auteur_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_FAB3FC168805AB2F` FOREIGN KEY (`annonce_id`) REFERENCES `annonce` (`id`),
  ADD CONSTRAINT `FK_FAB3FC16A4F84F6E` FOREIGN KEY (`destinataire_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `commentaire`
--
ALTER TABLE `commentaire`
  ADD CONSTRAINT `FK_67F068BC8805AB2F` FOREIGN KEY (`annonce_id`) REFERENCES `annonce` (`id`),
  ADD CONSTRAINT `FK_67F068BCA9E2D76C` FOREIGN KEY (`joueur_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `contrat_sponsor`
--
ALTER TABLE `contrat_sponsor`
  ADD CONSTRAINT `FK_28429AA212F7FB51` FOREIGN KEY (`sponsor_id`) REFERENCES `sponsor` (`id`),
  ADD CONSTRAINT `FK_28429AA26D861B89` FOREIGN KEY (`equipe_id`) REFERENCES `equipe` (`id`);

--
-- Contraintes pour la table `entrainement`
--
ALTER TABLE `entrainement`
  ADD CONSTRAINT `FK_A27444E5F8478A1` FOREIGN KEY (`entraineur_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `entrainement_user`
--
ALTER TABLE `entrainement_user`
  ADD CONSTRAINT `FK_EB3D3F70A15E8FD` FOREIGN KEY (`entrainement_id`) REFERENCES `entrainement` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_EB3D3F70A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `evaluation`
--
ALTER TABLE `evaluation`
  ADD CONSTRAINT `FK_1323A575A15E8FD` FOREIGN KEY (`entrainement_id`) REFERENCES `entrainement` (`id`),
  ADD CONSTRAINT `FK_1323A575A9E2D76C` FOREIGN KEY (`joueur_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `matchs`
--
ALTER TABLE `matchs`
  ADD CONSTRAINT `FK_6B1E604121ECD755` FOREIGN KEY (`equipe_exterieur_id`) REFERENCES `equipe` (`id`),
  ADD CONSTRAINT `FK_6B1E60415FE1AEAD` FOREIGN KEY (`equipe_domicile_id`) REFERENCES `equipe` (`id`);

--
-- Contraintes pour la table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `FK_B6BD307FCD53EDB6` FOREIGN KEY (`receiver_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_B6BD307FF624B39D` FOREIGN KEY (`sender_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `FK_BF5476CAA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `FK_F52993984584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
  ADD CONSTRAINT `FK_F5299398F8478A1` FOREIGN KEY (`entraineur_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `participation`
--
ALTER TABLE `participation`
  ADD CONSTRAINT `FK_AB55E24FA15E8FD` FOREIGN KEY (`entrainement_id`) REFERENCES `entrainement` (`id`),
  ADD CONSTRAINT `FK_AB55E24FA9E2D76C` FOREIGN KEY (`joueur_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
