-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 12 fév. 2026 à 14:15
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
(1, '2023-2024', 0, '2026-02-06 02:31:24', '2026-02-06 02:31:24'),
(2, '2024-2025', 1, '2026-02-06 02:31:39', '2026-02-06 02:31:44');

-- --------------------------------------------------------

--
-- Structure de la table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 'El001', 'erica', 'er', '2002-06-13', 'erica@gmail.com', 'Féminin', NULL, NULL, 4, '2026-02-06 03:37:19', '2026-02-06 03:37:19'),
(2, 'El006', 'Mina', 'Li', '2014-01-01', 'mina@gmail.com', 'Masculin', NULL, NULL, 6, '2026-02-06 09:39:59', '2026-02-06 09:39:59');

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
(1, 'Ens001', 'mi', 'mima', NULL, NULL, 'mima@gmail.com', NULL, 'Femme', NULL, 2, '2026-02-06 03:35:14', '2026-02-06 03:35:14'),
(2, 'Ens002', 're', 'remi', NULL, NULL, 'remi@gmail.com', NULL, 'Homme', NULL, 3, '2026-02-06 03:36:08', '2026-02-06 03:36:08'),
(3, 'Ens004', 'Divin', 'Laza', NULL, NULL, 'laza@gmail.com', NULL, NULL, NULL, 5, '2026-02-06 09:38:28', '2026-02-06 09:38:28');

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
(1, 'Scientifique', NULL, '2026-02-06 02:32:10', '2026-02-06 02:32:10'),
(2, 'Littéraire', NULL, '2026-02-06 02:32:25', '2026-02-06 02:32:25');

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
(1, 1, 13, 2, '2025-11-11', 'active', '2026-02-06 03:39:49', '2026-02-06 03:39:49');

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
(1, 'SVT', 2, 15, 1, '2026-02-06 03:38:13', '2026-02-06 03:38:13'),
(2, 'Mathematique', 3, 15, 1, '2026-02-06 03:38:37', '2026-02-06 03:38:37');

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
(1, 'Question sur le cours', 'mon note SVT de 3eme trimestre est 17', '2026-02-06 06:43:44', 1, 4, 2, 2, '2026-02-06 03:43:44', '2026-02-06 04:35:16');

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
(1, '11 ème A', NULL, 'primaire', NULL, '2026-02-06 02:33:03', '2026-02-06 02:33:03'),
(2, '11 ème B', NULL, 'primaire', NULL, '2026-02-06 02:33:18', '2026-02-06 02:33:18'),
(3, '10 ème A', NULL, 'primaire', NULL, '2026-02-06 02:33:27', '2026-02-06 02:33:27'),
(4, '10 ème B', NULL, 'primaire', NULL, '2026-02-06 02:33:46', '2026-02-06 02:33:46'),
(5, '9 ème A', NULL, 'primaire', NULL, '2026-02-06 02:33:58', '2026-02-06 02:33:58'),
(6, '9 ème B', NULL, 'primaire', NULL, '2026-02-06 02:34:14', '2026-02-06 02:34:14'),
(7, '8 ème A', NULL, 'primaire', NULL, '2026-02-06 02:34:26', '2026-02-06 02:34:26'),
(8, '8 ème B', NULL, 'primaire', NULL, '2026-02-06 02:34:38', '2026-02-06 02:34:38'),
(9, '7 ème A', NULL, 'primaire', NULL, '2026-02-06 02:34:49', '2026-02-06 02:34:49'),
(10, '7 ème B', NULL, 'primaire', NULL, '2026-02-06 02:34:59', '2026-02-06 02:34:59'),
(11, '6 ème A', NULL, 'college', NULL, '2026-02-06 02:35:24', '2026-02-06 02:35:24'),
(13, '6 ème B', NULL, 'college', NULL, '2026-02-06 02:36:12', '2026-02-06 02:36:12'),
(15, '5 ème A', NULL, 'college', NULL, '2026-02-06 02:36:48', '2026-02-06 02:37:00'),
(16, '5 ème B', NULL, 'college', NULL, '2026-02-06 02:37:18', '2026-02-06 02:37:18'),
(17, '4 ème A', NULL, 'college', NULL, '2026-02-06 02:37:35', '2026-02-06 02:37:35'),
(18, '4 ème B', NULL, 'college', NULL, '2026-02-06 02:37:49', '2026-02-06 02:37:49'),
(20, '3 ème A', NULL, 'college', NULL, '2026-02-06 02:38:43', '2026-02-06 02:38:43'),
(21, '3 ème B', NULL, 'college', NULL, '2026-02-06 02:39:43', '2026-02-06 02:39:43'),
(22, '2nde L A', NULL, 'lycee', 2, '2026-02-06 02:40:34', '2026-02-06 02:40:34'),
(23, '2nde L B', NULL, 'lycee', 2, '2026-02-06 02:41:10', '2026-02-06 02:41:10'),
(24, '1ère L A', NULL, 'lycee', 2, '2026-02-06 02:41:57', '2026-02-06 02:41:57'),
(25, '1ère L B', NULL, 'lycee', 2, '2026-02-06 02:44:14', '2026-02-06 02:44:14'),
(26, 'Terminale L A', NULL, 'lycee', 2, '2026-02-06 02:46:09', '2026-02-06 02:46:09'),
(27, 'Terminale L B', NULL, 'lycee', 2, '2026-02-06 02:46:34', '2026-02-06 02:46:34'),
(28, '2nde S A', NULL, 'lycee', 1, '2026-02-06 02:47:02', '2026-02-06 02:47:02'),
(29, '2nde S B', NULL, 'lycee', 1, '2026-02-06 02:47:34', '2026-02-06 02:48:04'),
(30, '1ère S A', NULL, 'lycee', 1, '2026-02-06 02:48:42', '2026-02-06 02:48:42'),
(31, '1ère S B', NULL, 'lycee', 1, '2026-02-06 02:49:09', '2026-02-06 02:49:09'),
(32, 'Terminale S A', NULL, 'lycee', 1, '2026-02-06 02:49:47', '2026-02-06 02:49:47'),
(33, 'Terminale S B', NULL, 'lycee', 1, '2026-02-06 02:50:11', '2026-02-06 02:50:11');

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
(1, 1, 2, 2, '1er', 14.00, '2026-02-06', NULL, '2026-02-06 03:40:33', '2026-02-06 03:40:33'),
(2, 1, 1, 1, '1er', 16.00, '2026-02-06', NULL, '2026-02-06 03:40:33', '2026-02-06 03:40:33'),
(3, 1, 2, 2, '2ème', 10.00, '2026-02-06', NULL, '2026-02-06 03:40:54', '2026-02-06 03:40:54'),
(4, 1, 1, 1, '2ème', 15.00, '2026-02-06', NULL, '2026-02-06 03:40:54', '2026-02-06 03:40:54'),
(5, 1, 2, 2, '3ème', 13.00, '2026-02-06', NULL, '2026-02-06 03:41:19', '2026-02-06 03:41:19'),
(6, 1, 1, 1, '3ème', 14.00, '2026-02-06', NULL, '2026-02-06 03:41:19', '2026-02-06 03:41:19');

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
(1, 'S001', 30, 1, '2026-02-06 02:51:10', '2026-02-06 02:51:10'),
(2, 'S002', 30, 2, '2026-02-06 02:51:28', '2026-02-06 02:51:28'),
(3, 'S003', 30, 3, '2026-02-06 02:51:44', '2026-02-06 02:51:44'),
(4, 'S004', 30, 4, '2026-02-06 02:52:03', '2026-02-06 02:52:03'),
(5, 'S005', 30, 5, '2026-02-06 02:52:33', '2026-02-06 02:52:33'),
(6, 'S006', 30, 6, '2026-02-06 02:52:54', '2026-02-06 02:52:54'),
(7, 'S007', 30, 7, '2026-02-06 02:53:15', '2026-02-06 02:53:15'),
(8, 'S008', 30, 8, '2026-02-06 02:53:29', '2026-02-06 02:53:29'),
(9, 'S009', 30, 9, '2026-02-06 02:54:03', '2026-02-06 02:54:03'),
(10, 'S010', 30, 10, '2026-02-06 02:54:27', '2026-02-06 02:54:27'),
(11, 'S011', 30, 11, '2026-02-06 02:54:50', '2026-02-06 02:54:50'),
(12, 'S012', 30, 13, '2026-02-06 02:55:08', '2026-02-06 02:55:08'),
(13, 'S013', 30, 15, '2026-02-06 02:55:32', '2026-02-06 02:55:32'),
(14, 'S014', 30, 16, '2026-02-06 02:55:51', '2026-02-06 02:55:51'),
(15, 'S015', 30, 17, '2026-02-06 02:56:08', '2026-02-06 02:56:08'),
(16, 'S016', 30, 18, '2026-02-06 02:56:33', '2026-02-06 02:56:33'),
(17, 'S017', 30, 20, '2026-02-06 02:57:04', '2026-02-06 02:57:04'),
(18, 'S018', 30, 21, '2026-02-06 02:57:40', '2026-02-06 02:57:40'),
(19, 'S019', 30, 22, '2026-02-06 02:57:56', '2026-02-06 02:57:56'),
(20, 'S020', 30, 23, '2026-02-06 02:58:37', '2026-02-06 02:58:37'),
(21, 'S021', 30, 24, '2026-02-06 03:05:39', '2026-02-06 03:05:39'),
(22, 'S022', 30, 25, '2026-02-06 03:05:57', '2026-02-06 03:05:57'),
(23, 'S023', 30, 26, '2026-02-06 03:06:21', '2026-02-06 03:06:21'),
(24, 'S024', 30, 27, '2026-02-06 03:06:36', '2026-02-06 03:06:36'),
(25, 'S025', 30, 28, '2026-02-06 03:06:56', '2026-02-06 03:06:56'),
(26, 'S026', 30, 29, '2026-02-06 03:07:13', '2026-02-06 03:07:13'),
(27, 'S027', 30, 30, '2026-02-06 03:07:27', '2026-02-06 03:07:27'),
(28, 'S028', 30, 31, '2026-02-06 03:07:45', '2026-02-06 03:07:45'),
(29, 'S029', 30, 32, '2026-02-06 03:08:08', '2026-02-06 03:08:08'),
(30, 'S030', 30, 33, '2026-02-06 03:08:30', '2026-02-06 03:09:07');

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
('pbYBQqeP2nIGDKtyZGTx4BQfvGoaQkoRLCVkxn2D', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZWFXN2l2THNTeWxSTHJpbVhldkRYaDB2bWF5M0FHVmNTaGQ4ZkZQZyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1770381950),
('tV6gwex9laC9Ww5mk5B7PMOCk19G3V3K3qai5E5e', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoia0YybmsyNzAzcExYdnJXeTJndUxuOWNIQ01CZHdCblVXTVFXZjI5TiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770363726);

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
(1, 'admin', 'ad', 'admin@gmail.com', NULL, '$2y$12$3qFAKtQFZOvNKsReBybuJujRPhL/rhAkHhCys5b044ete3559hzNy', 'admin', 1, 0, NULL, NULL, '2026-02-06 02:30:59', '2026-02-06 02:30:59'),
(2, 'mima', 'mi', 'mima@gmail.com', NULL, '$2y$12$Q0p5eiR8Q5KVNuzGW6QdMuy578n9BfsLaW4jcfl9TEvnrpub0n2wG', 'enseignant', 1, 0, NULL, NULL, '2026-02-06 03:35:14', '2026-02-06 03:35:14'),
(3, 'remi', 're', 'remi@gmail.com', NULL, '$2y$12$tlModB3ow/cVJ7GFRdN02eL5aEaa.6XiN1SB0HQ8IXRTRqLIIav6S', 'enseignant', 1, 0, NULL, NULL, '2026-02-06 03:36:08', '2026-02-06 03:36:08'),
(4, 'er', 'erica', 'erica@gmail.com', NULL, '$2y$12$NHJSuw85a1O0iMjStO4G8OTVed.xSafwxCvGXBIWWNvWuhNXdXQIG', 'eleve', 1, 0, NULL, NULL, '2026-02-06 03:37:18', '2026-02-06 03:37:18'),
(5, 'Laza', 'Divin', 'laza@gmail.com', NULL, '$2y$12$9/s5kiXxnj1D1By9lFCfK.ilC3ncfPB5M7YEYaOyOFXtz/dEGVI1a', 'enseignant', 1, 0, NULL, NULL, '2026-02-06 09:38:28', '2026-02-06 09:38:28'),
(6, 'Li', 'Mina', 'mina@gmail.com', NULL, '$2y$12$Bz6OCAUL0RpqCF/tRRQdsO58o/Ka5qciuTkg2zQwxQkXqvAefnY22', 'eleve', 1, 0, NULL, NULL, '2026-02-06 09:39:59', '2026-02-06 09:39:59');

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `eleves`
--
ALTER TABLE `eleves`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `inscriptions`
--
ALTER TABLE `inscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `matieres`
--
ALTER TABLE `matieres`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `niveaux`
--
ALTER TABLE `niveaux`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT pour la table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `salles`
--
ALTER TABLE `salles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
