-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 30 déc. 2025 à 11:54
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
-- Base de données : `ges_academ`
--

-- --------------------------------------------------------

--
-- Structure de la table `annee_scolaires`
--

CREATE TABLE `annee_scolaires` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `libelle` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `annee_scolaires`
--

INSERT INTO `annee_scolaires` (`id`, `libelle`, `active`, `created_at`, `updated_at`) VALUES
(1, '2024-2025', 1, '2025-12-12 04:49:24', '2025-12-19 10:33:56'),
(5, '2023-2024', 0, '2025-12-16 15:10:01', '2025-12-18 06:56:10'),
(6, '2026-2927', 0, '2025-12-19 10:32:51', '2025-12-19 10:33:56');

-- --------------------------------------------------------

--
-- Structure de la table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('admin@gmail.com|127.0.0.1', 'i:1;', 1766744574),
('admin@gmail.com|127.0.0.1:timer', 'i:1766744574;', 1766744574);

-- --------------------------------------------------------

--
-- Structure de la table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `eleves`
--

CREATE TABLE `eleves` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `matricule` varchar(50) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `date_naissance` date DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `sexe` varchar(10) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `eleves`
--

INSERT INTO `eleves` (`id`, `matricule`, `nom`, `prenom`, `date_naissance`, `email`, `sexe`, `adresse`, `telephone`, `user_id`, `created_at`, `updated_at`) VALUES
(2, 'El001', 'Raz', 'Erica', '2009-06-13', 'erica@gmail.com', 'Féminin', NULL, NULL, 6, '2025-12-18 18:25:27', '2025-12-18 18:27:11'),
(3, 'El002', 'Nambi', 'Fifa', '2008-12-03', 'fifa@gmail.com', 'Masculin', NULL, NULL, 7, '2025-12-18 18:26:42', '2025-12-18 18:26:42'),
(4, 'El003', 'Laza', 'Rina', '2011-02-14', 'rina@gmail.com', 'Masculin', NULL, NULL, 8, '2025-12-18 18:28:14', '2025-12-18 18:28:14');

-- --------------------------------------------------------

--
-- Structure de la table `enseignants`
--

CREATE TABLE `enseignants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `matriculeEnseig` varchar(50) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `grade` varchar(50) DEFAULT NULL,
  `specialite` varchar(50) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `sexe` varchar(10) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `enseignants`
--

INSERT INTO `enseignants` (`id`, `matriculeEnseig`, `nom`, `prenom`, `grade`, `specialite`, `email`, `telephone`, `sexe`, `adresse`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'En001', 'mima', 'mi', NULL, NULL, 'mima@gmail.com', NULL, NULL, NULL, 2, '2025-12-12 04:15:36', '2025-12-12 04:15:36'),
(2, 'En002', 'Rakoto', 'Rolland', NULL, NULL, 'rolland@gmail.com', '034 45 485 50', 'Homme', NULL, 4, '2025-12-18 07:43:29', '2025-12-18 07:43:29'),
(3, 'En003', 'Landrie', 'Vero', NULL, NULL, 'vero@gmail.com', '034 40 000 01', 'Femme', NULL, 5, '2025-12-18 07:47:32', '2025-12-18 07:47:32');

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `filieres`
--

CREATE TABLE `filieres` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nomFiliere` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `filieres`
--

INSERT INTO `filieres` (`id`, `nomFiliere`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Scientifique', NULL, '2025-12-12 04:49:48', '2025-12-12 04:49:48'),
(2, 'Littéraire', NULL, '2025-12-12 04:58:12', '2025-12-12 04:58:12');

-- --------------------------------------------------------

--
-- Structure de la table `inscriptions`
--

CREATE TABLE `inscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `eleve_id` bigint(20) UNSIGNED NOT NULL,
  `salle_id` bigint(20) UNSIGNED NOT NULL,
  `annee_scolaire_id` bigint(20) UNSIGNED NOT NULL,
  `date_inscription` date NOT NULL,
  `etat` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `inscriptions`
--

INSERT INTO `inscriptions` (`id`, `eleve_id`, `salle_id`, `annee_scolaire_id`, `date_inscription`, `etat`, `created_at`, `updated_at`) VALUES
(2, 3, 19, 1, '2025-12-19', 'active', '2025-12-18 18:48:52', '2025-12-18 18:48:52'),
(3, 4, 16, 1, '2025-12-19', 'active', '2025-12-18 19:06:08', '2025-12-18 19:06:08'),
(4, 2, 33, 1, '2025-12-19', 'active', '2025-12-18 19:06:34', '2025-12-18 19:06:34');

-- --------------------------------------------------------

--
-- Structure de la table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `matieres`
--

CREATE TABLE `matieres` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nomMatiere` varchar(50) NOT NULL,
  `coefficient` double NOT NULL DEFAULT 1,
  `niveau_id` bigint(20) UNSIGNED DEFAULT NULL,
  `enseignant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `matieres`
--

INSERT INTO `matieres` (`id`, `nomMatiere`, `coefficient`, `niveau_id`, `enseignant_id`, `created_at`, `updated_at`) VALUES
(2, 'SVT', 2, 13, 1, '2025-12-18 18:29:01', '2025-12-18 18:29:01'),
(3, 'Calcul', 2, 11, 2, '2025-12-18 18:29:38', '2025-12-18 18:29:38'),
(4, 'Mathematique', 3, 13, 2, '2025-12-18 18:30:11', '2025-12-18 18:30:11'),
(5, 'SVT', 3, 28, 1, '2025-12-18 18:30:47', '2025-12-18 18:37:00'),
(6, 'Mathematique', 4, 28, 2, '2025-12-18 18:37:37', '2025-12-18 18:37:37'),
(7, 'Francais', 2, 13, 3, '2025-12-18 18:38:13', '2025-12-18 18:38:13'),
(8, 'Francais', 3, 28, 3, '2025-12-18 18:38:40', '2025-12-18 18:38:40');

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sujet` varchar(200) DEFAULT NULL,
  `contenu` text NOT NULL,
  `date_envoi` datetime NOT NULL,
  `lu` tinyint(1) NOT NULL DEFAULT 0,
  `expediteur_id` bigint(20) UNSIGNED DEFAULT NULL,
  `destinataire_id` bigint(20) UNSIGNED DEFAULT NULL,
  `annee_scolaire_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `messages`
--

INSERT INTO `messages` (`id`, `sujet`, `contenu`, `date_envoi`, `lu`, `expediteur_id`, `destinataire_id`, `annee_scolaire_id`, `created_at`, `updated_at`) VALUES
(1, 'eccolage', 'votre ecolage de Juillet', '2025-12-18 22:23:04', 1, 1, 6, 1, '2025-12-18 19:23:04', '2025-12-18 19:41:19'),
(2, 'Re: eccolage', 'merci 1111111111111111111111111111111111', '2025-12-26 10:19:37', 1, 6, 1, 1, '2025-12-26 07:19:37', '2025-12-26 07:20:00');

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_09_20_085226_create_annee_scolaires_table', 1),
(5, '2025_09_24_084430_create_filieres_table', 1),
(6, '2025_09_24_084457_create_niveaux_table', 1),
(7, '2025_09_25_084515_create_salles_table', 1),
(8, '2025_09_26_084541_create_enseignants_table', 1),
(9, '2025_09_26_084608_create_eleves_table', 1),
(10, '2025_09_27_072420_create_inscriptions_table', 1),
(11, '2025_09_27_084633_create_matieres_table', 1),
(12, '2025_09_27_084844_create_messages_table', 1),
(13, '2025_09_28_084746_create_notes_table', 1);

-- --------------------------------------------------------

--
-- Structure de la table `niveaux`
--

CREATE TABLE `niveaux` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nomNiveau` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `cycle` enum('primaire','college','lycee') NOT NULL,
  `filiere_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `niveaux`
--

INSERT INTO `niveaux` (`id`, `nomNiveau`, `description`, `cycle`, `filiere_id`, `created_at`, `updated_at`) VALUES
(1, '6èmeA', NULL, 'college', NULL, '2025-12-12 05:28:29', '2025-12-12 05:28:29'),
(2, '11èmeA', NULL, 'primaire', NULL, '2025-12-18 06:57:38', '2025-12-18 06:57:38'),
(3, '11èmeB', NULL, 'primaire', NULL, '2025-12-18 06:58:51', '2025-12-18 06:58:51'),
(4, '10èmeA', NULL, 'primaire', NULL, '2025-12-18 06:59:13', '2025-12-18 06:59:13'),
(5, '10èmeB', NULL, 'primaire', NULL, '2025-12-18 06:59:38', '2025-12-18 06:59:38'),
(6, '9èmeA', NULL, 'primaire', NULL, '2025-12-18 07:00:11', '2025-12-18 07:00:11'),
(7, '9èmeB', NULL, 'primaire', NULL, '2025-12-18 07:00:26', '2025-12-18 07:00:26'),
(8, '8èmeA', NULL, 'primaire', NULL, '2025-12-18 07:00:51', '2025-12-18 07:00:51'),
(9, '8èmeB', NULL, 'primaire', NULL, '2025-12-18 07:01:17', '2025-12-18 07:01:17'),
(10, '7èmeA', NULL, 'primaire', NULL, '2025-12-18 07:01:28', '2025-12-18 07:01:28'),
(11, '7èmeB', NULL, 'primaire', NULL, '2025-12-18 07:01:58', '2025-12-18 07:01:58'),
(12, '6èmeB', NULL, 'college', NULL, '2025-12-18 07:02:25', '2025-12-18 07:02:25'),
(13, '5èmeA', NULL, 'college', NULL, '2025-12-18 07:02:43', '2025-12-18 07:02:43'),
(16, '4èmeA', NULL, 'college', NULL, '2025-12-18 07:03:41', '2025-12-18 07:03:41'),
(17, '4èmeB', NULL, 'college', NULL, '2025-12-18 07:04:16', '2025-12-18 07:04:16'),
(18, '5èmeB', NULL, 'college', NULL, '2025-12-18 07:04:37', '2025-12-18 07:04:37'),
(19, '3èmeA', NULL, 'college', NULL, '2025-12-18 07:05:07', '2025-12-18 07:05:15'),
(20, '3èmeB', NULL, 'college', NULL, '2025-12-18 07:05:36', '2025-12-18 07:05:36'),
(21, '2nde L A', NULL, 'lycee', 2, '2025-12-18 07:06:02', '2025-12-18 07:06:02'),
(22, '2nde L B', NULL, 'lycee', 2, '2025-12-18 07:06:34', '2025-12-18 07:06:34'),
(23, '1ère L A', NULL, 'lycee', 2, '2025-12-18 07:07:45', '2025-12-18 07:07:45'),
(24, '1ère L B', NULL, 'lycee', 2, '2025-12-18 07:09:00', '2025-12-18 07:09:00'),
(25, 'Terminale L A', NULL, 'lycee', 2, '2025-12-18 07:09:36', '2025-12-18 07:09:36'),
(26, '2nde S A', NULL, 'lycee', 1, '2025-12-18 07:09:56', '2025-12-18 07:09:56'),
(27, '2nde S B', NULL, 'lycee', 1, '2025-12-18 07:10:25', '2025-12-18 07:10:25'),
(28, '1ère S A', NULL, 'lycee', 1, '2025-12-18 07:10:49', '2025-12-18 07:10:49'),
(29, '1ère S B', NULL, 'lycee', 1, '2025-12-18 07:11:22', '2025-12-18 07:11:22'),
(30, 'Terminale L B', NULL, 'lycee', 2, '2025-12-18 07:11:56', '2025-12-18 07:11:56'),
(31, 'Terminale S A', NULL, 'lycee', 1, '2025-12-18 07:12:15', '2025-12-18 07:12:15'),
(32, 'Terminale S B', NULL, 'lycee', 1, '2025-12-18 07:12:50', '2025-12-18 07:12:50');

-- --------------------------------------------------------

--
-- Structure de la table `notes`
--

CREATE TABLE `notes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inscription_id` bigint(20) UNSIGNED NOT NULL,
  `matiere_id` bigint(20) UNSIGNED NOT NULL,
  `enseignant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `trimestre` enum('1er','2ème','3ème') NOT NULL DEFAULT '1er',
  `note` decimal(4,2) NOT NULL COMMENT 'Note suur 20',
  `date_saisie` date DEFAULT NULL,
  `commentaire` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `notes`
--

INSERT INTO `notes` (`id`, `inscription_id`, `matiere_id`, `enseignant_id`, `trimestre`, `note`, `date_saisie`, `commentaire`, `created_at`, `updated_at`) VALUES
(4, 3, 3, 2, '1er', 14.00, '2025-12-18', NULL, '2025-12-18 19:07:16', '2025-12-18 19:08:30'),
(5, 3, 3, 2, '2ème', 12.00, '2025-12-18', NULL, '2025-12-18 19:07:33', '2025-12-18 19:08:13'),
(6, 3, 3, 2, '3ème', 13.50, '2025-12-18', NULL, '2025-12-18 19:08:06', '2025-12-18 19:08:06'),
(7, 2, 7, 3, '1er', 11.00, '2025-12-18', NULL, '2025-12-18 19:11:16', '2025-12-18 19:11:16'),
(8, 2, 4, 2, '1er', 13.00, '2025-12-18', NULL, '2025-12-18 19:11:16', '2025-12-18 19:11:16'),
(9, 2, 2, 1, '1er', 15.00, '2025-12-18', NULL, '2025-12-18 19:11:16', '2025-12-18 19:11:16'),
(10, 2, 7, 3, '2ème', 15.00, '2025-12-18', NULL, '2025-12-18 19:11:46', '2025-12-18 19:11:46'),
(11, 2, 4, 2, '2ème', 10.00, '2025-12-18', NULL, '2025-12-18 19:11:46', '2025-12-18 19:11:46'),
(12, 2, 2, 1, '2ème', 15.50, '2025-12-18', NULL, '2025-12-18 19:11:46', '2025-12-18 19:11:46'),
(13, 2, 7, 3, '3ème', 16.00, '2025-12-18', NULL, '2025-12-18 19:12:17', '2025-12-18 19:12:17'),
(14, 2, 4, 2, '3ème', 11.50, '2025-12-18', NULL, '2025-12-18 19:12:17', '2025-12-18 19:12:17'),
(15, 2, 2, 1, '3ème', 13.00, '2025-12-18', NULL, '2025-12-18 19:12:17', '2025-12-18 19:12:17'),
(16, 4, 8, 3, '1er', 14.00, '2025-12-18', NULL, '2025-12-18 19:13:35', '2025-12-18 19:13:35'),
(17, 4, 6, 2, '1er', 13.00, '2025-12-18', NULL, '2025-12-18 19:13:35', '2025-12-18 19:13:35'),
(18, 4, 5, 1, '1er', 11.00, '2025-12-18', NULL, '2025-12-18 19:13:35', '2025-12-18 19:13:35'),
(19, 4, 8, 3, '2ème', 10.00, '2025-12-18', NULL, '2025-12-18 19:14:09', '2025-12-18 19:14:09'),
(20, 4, 6, 2, '2ème', 16.00, '2025-12-18', NULL, '2025-12-18 19:14:09', '2025-12-18 19:14:09'),
(21, 4, 5, 1, '2ème', 11.75, '2025-12-18', NULL, '2025-12-18 19:14:09', '2025-12-18 19:14:09'),
(22, 4, 8, 3, '3ème', 15.00, '2025-12-18', NULL, '2025-12-18 19:14:27', '2025-12-18 19:14:27'),
(23, 4, 6, 2, '3ème', 10.00, '2025-12-18', NULL, '2025-12-18 19:14:27', '2025-12-18 19:14:56'),
(24, 4, 5, 1, '3ème', 11.00, '2025-12-18', NULL, '2025-12-18 19:14:27', '2025-12-18 19:14:27');

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `salles`
--

CREATE TABLE `salles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nomSalle` varchar(50) NOT NULL,
  `capacite` int(11) NOT NULL,
  `niveau_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `salles`
--

INSERT INTO `salles` (`id`, `nomSalle`, `capacite`, `niveau_id`, `created_at`, `updated_at`) VALUES
(7, 'S01', 20, 2, '2025-12-18 07:21:38', '2025-12-18 07:21:38'),
(8, 'S02', 20, 3, '2025-12-18 07:22:01', '2025-12-18 07:22:01'),
(9, 'S03', 20, 4, '2025-12-18 07:22:30', '2025-12-18 07:22:30'),
(10, 'S04', 20, 5, '2025-12-18 07:22:50', '2025-12-18 07:22:50'),
(11, 'S05', 20, 6, '2025-12-18 07:23:23', '2025-12-18 07:23:23'),
(12, 'S06', 20, 7, '2025-12-18 07:23:45', '2025-12-18 07:23:45'),
(13, 'S07', 20, 8, '2025-12-18 07:25:31', '2025-12-18 07:25:31'),
(14, 'S08', 20, 9, '2025-12-18 07:26:08', '2025-12-18 07:26:08'),
(15, 'S09', 20, 10, '2025-12-18 07:26:32', '2025-12-18 07:26:32'),
(16, 'S10', 20, 11, '2025-12-18 07:26:48', '2025-12-18 07:26:48'),
(17, 'S11', 20, 1, '2025-12-18 07:27:11', '2025-12-18 07:27:11'),
(18, 'S12', 20, 12, '2025-12-18 07:27:34', '2025-12-18 07:27:34'),
(19, 'S13', 20, 13, '2025-12-18 07:27:54', '2025-12-18 07:27:54'),
(20, 'S14', 20, 18, '2025-12-18 07:28:11', '2025-12-18 07:28:11'),
(21, 'S15', 20, 16, '2025-12-18 07:28:31', '2025-12-18 07:28:31'),
(22, 'S16', 20, 17, '2025-12-18 07:28:52', '2025-12-18 07:28:52'),
(23, 'S17', 20, 19, '2025-12-18 07:29:24', '2025-12-18 07:29:24'),
(24, 'S18', 20, 20, '2025-12-18 07:29:41', '2025-12-18 07:29:41'),
(25, 'S19', 20, 21, '2025-12-18 07:30:03', '2025-12-18 07:30:03'),
(26, 'S20', 20, 22, '2025-12-18 07:30:34', '2025-12-18 07:30:34'),
(27, 'S21', 20, 23, '2025-12-18 07:31:03', '2025-12-18 07:31:03'),
(28, 'S22', 20, 24, '2025-12-18 07:31:34', '2025-12-18 07:31:34'),
(29, 'S23', 20, 25, '2025-12-18 07:35:59', '2025-12-18 07:35:59'),
(30, 'S24', 20, 30, '2025-12-18 07:36:35', '2025-12-18 07:36:35'),
(31, 'S25', 20, 26, '2025-12-18 07:37:31', '2025-12-18 07:37:31'),
(32, 'S26', 20, 27, '2025-12-18 07:38:29', '2025-12-18 07:38:29'),
(33, 'S27', 20, 28, '2025-12-18 07:39:23', '2025-12-18 07:39:23'),
(34, 'S28', 20, 29, '2025-12-18 07:39:39', '2025-12-18 07:39:39'),
(35, 'S29', 20, 31, '2025-12-18 07:40:18', '2025-12-18 07:40:18'),
(36, 'S30', 20, 32, '2025-12-18 07:40:34', '2025-12-18 07:40:34');

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('JpLXwv6GsTRakWfTtdCdSxoiBPJDVujRZPeWaSXZ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiNkxOc2x6dlRRMk80WExwV2JsTnRabXVTTzVhYTlCM0trdUlhcmcwOCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1766744644);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'eleve',
  `force_password_change` tinyint(1) NOT NULL DEFAULT 1,
  `two_factor_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `prenom`, `nom`, `email`, `email_verified_at`, `password`, `role`, `force_password_change`, `two_factor_enabled`, `deleted_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'ad', 'admin@gmail.com', NULL, '$2y$12$Bj9v23BdLiQBi2aEzfTy3.HNcHKj9jpoiEpk7z.JH0WgbtGGU0JvS', 'admin', 1, 0, NULL, NULL, '2025-12-12 04:12:04', '2025-12-26 07:23:59'),
(2, 'mi', 'mima', 'mima@gmail.com', NULL, '$2y$12$BurWz8611LXCY5Q7zoZP4ONMboUx0gzHY33tqBfP.YRnlU4JPlLUq', 'enseignant', 1, 0, NULL, NULL, '2025-12-12 04:15:36', '2025-12-12 04:15:36'),
(4, 'Rolland', 'Rakoto', 'rolland@gmail.com', NULL, '$2y$12$rIswYVBDavkXTo4IkcymZuiVCqtRC0gwYsJW1yCAL5/ivQUGcJN2e', 'enseignant', 1, 0, NULL, NULL, '2025-12-18 07:43:29', '2025-12-18 07:43:29'),
(5, 'Vero', 'Landrie', 'vero@gmail.com', NULL, '$2y$12$Ayqr7QWvD8GSqcsYL3aY3O3KIjcXFpJRxLpAhpYjyzdZ/.DBDM36K', 'enseignant', 1, 0, NULL, NULL, '2025-12-18 07:47:32', '2025-12-18 07:47:32'),
(6, 'Erica', 'Raz', 'erica@gmail.com', NULL, '$2y$12$jNk7Pg6.fX6rtyMZhT8gFe.Gs8uBzPkr8LIPNgMpjt4xjVEhSpZs6', 'eleve', 1, 0, NULL, NULL, '2025-12-18 18:25:27', '2025-12-18 18:25:27'),
(7, 'Fifa', 'Nambi', 'fifa@gmail.com', NULL, '$2y$12$BWTIlxX1PKA5i7hNGZdmZObLMG3bBSjOfYQKmN3GIV6JPV4ed4fqy', 'eleve', 1, 0, NULL, NULL, '2025-12-18 18:26:42', '2025-12-18 18:26:42'),
(8, 'Rina', 'Laza', 'rina@gmail.com', NULL, '$2y$12$RZTbS3t0LDzXH7I42QzSqu8oM6wL6Y6fubvlqMB1xh3JFINEzWINy', 'eleve', 1, 0, NULL, NULL, '2025-12-18 18:28:14', '2025-12-18 18:28:14');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `annee_scolaires`
--
ALTER TABLE `annee_scolaires`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `annee_scolaires_libelle_unique` (`libelle`);

--
-- Index pour la table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Index pour la table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Index pour la table `eleves`
--
ALTER TABLE `eleves`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `eleves_matricule_unique` (`matricule`),
  ADD KEY `eleves_user_id_foreign` (`user_id`);

--
-- Index pour la table `enseignants`
--
ALTER TABLE `enseignants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `enseignants_matriculeenseig_unique` (`matriculeEnseig`),
  ADD UNIQUE KEY `enseignants_email_unique` (`email`),
  ADD KEY `enseignants_user_id_foreign` (`user_id`);

--
-- Index pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Index pour la table `filieres`
--
ALTER TABLE `filieres`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `filieres_nomfiliere_unique` (`nomFiliere`);

--
-- Index pour la table `inscriptions`
--
ALTER TABLE `inscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inscriptions_eleve_id_foreign` (`eleve_id`),
  ADD KEY `inscriptions_salle_id_foreign` (`salle_id`),
  ADD KEY `inscriptions_annee_scolaire_id_foreign` (`annee_scolaire_id`);

--
-- Index pour la table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Index pour la table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `matieres`
--
ALTER TABLE `matieres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `matieres_niveau_id_foreign` (`niveau_id`),
  ADD KEY `matieres_enseignant_id_foreign` (`enseignant_id`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `messages_expediteur_id_foreign` (`expediteur_id`),
  ADD KEY `messages_destinataire_id_foreign` (`destinataire_id`),
  ADD KEY `messages_annee_scolaire_id_foreign` (`annee_scolaire_id`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `niveaux`
--
ALTER TABLE `niveaux`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `niveaux_nomniveau_unique` (`nomNiveau`),
  ADD KEY `niveaux_filiere_id_foreign` (`filiere_id`);

--
-- Index pour la table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `notes_inscription_id_matiere_id_trimestre_unique` (`inscription_id`,`matiere_id`,`trimestre`),
  ADD KEY `notes_matiere_id_foreign` (`matiere_id`),
  ADD KEY `notes_enseignant_id_foreign` (`enseignant_id`);

--
-- Index pour la table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Index pour la table `salles`
--
ALTER TABLE `salles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `salles_niveau_id_unique` (`niveau_id`);

--
-- Index pour la table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `annee_scolaires`
--
ALTER TABLE `annee_scolaires`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `eleves`
--
ALTER TABLE `eleves`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `enseignants`
--
ALTER TABLE `enseignants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `filieres`
--
ALTER TABLE `filieres`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `inscriptions`
--
ALTER TABLE `inscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `matieres`
--
ALTER TABLE `matieres`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `niveaux`
--
ALTER TABLE `niveaux`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT pour la table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT pour la table `salles`
--
ALTER TABLE `salles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `eleves`
--
ALTER TABLE `eleves`
  ADD CONSTRAINT `eleves_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `enseignants`
--
ALTER TABLE `enseignants`
  ADD CONSTRAINT `enseignants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `inscriptions`
--
ALTER TABLE `inscriptions`
  ADD CONSTRAINT `inscriptions_annee_scolaire_id_foreign` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annee_scolaires` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inscriptions_eleve_id_foreign` FOREIGN KEY (`eleve_id`) REFERENCES `eleves` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inscriptions_salle_id_foreign` FOREIGN KEY (`salle_id`) REFERENCES `salles` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `matieres`
--
ALTER TABLE `matieres`
  ADD CONSTRAINT `matieres_enseignant_id_foreign` FOREIGN KEY (`enseignant_id`) REFERENCES `enseignants` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `matieres_niveau_id_foreign` FOREIGN KEY (`niveau_id`) REFERENCES `niveaux` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_annee_scolaire_id_foreign` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annee_scolaires` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `messages_destinataire_id_foreign` FOREIGN KEY (`destinataire_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `messages_expediteur_id_foreign` FOREIGN KEY (`expediteur_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `niveaux`
--
ALTER TABLE `niveaux`
  ADD CONSTRAINT `niveaux_filiere_id_foreign` FOREIGN KEY (`filiere_id`) REFERENCES `filieres` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_enseignant_id_foreign` FOREIGN KEY (`enseignant_id`) REFERENCES `enseignants` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `notes_inscription_id_foreign` FOREIGN KEY (`inscription_id`) REFERENCES `inscriptions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notes_matiere_id_foreign` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `salles`
--
ALTER TABLE `salles`
  ADD CONSTRAINT `salles_niveau_id_foreign` FOREIGN KEY (`niveau_id`) REFERENCES `niveaux` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
