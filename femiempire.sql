-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 05, 2026 at 02:59 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `femiempire`
--

-- --------------------------------------------------------

--
-- Table structure for table `commandes`
--

CREATE TABLE `commandes` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `formation_id` int(11) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `status` enum('payee','en_attente','annulee') DEFAULT 'en_attente',
  `transaction_id` varchar(100) DEFAULT NULL,
  `logs` text DEFAULT NULL,
  `progression` int(11) DEFAULT 0,
  `modules_done` int(11) DEFAULT 0,
  `reference` varchar(50) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_obtention` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `commandes`
--

INSERT INTO `commandes` (`id`, `utilisateur_id`, `formation_id`, `montant`, `status`, `transaction_id`, `logs`, `progression`, `modules_done`, `reference`, `date_creation`, `date_obtention`) VALUES
(29, 4, 2, 25000.00, 'payee', '487161', '{\"klass\":\"v1\\/transaction\",\"id\":487161,\"reference\":\"trx_ptE_1786693582162\",\"amount\":25000,\"description\":\"Onglerie avancé\",\"callback_url\":\"https:\\/\\/femiempire.free.nf\\/pages\\/callback.php\",\"status\":\"pending\",\"customer_id\":112718,\"currency_id\":1,\"mode\":null,\"operation\":\"payment\",\"metadata\":{\"expire_schedule_jobid\":\"39d3a2fc59d6c0e6dcbef039\"},\"commission\":null,\"fees\":null,\"fixed_commission\":0,\"amount_transferred\":null,\"created_at\":\"2026-08-14T07:46:22.247Z\",\"updated_at\":\"2026-08-14T07:46:22.324Z\",\"approved_at\":null,\"canceled_at\":null,\"declined_at\":null,\"refunded_at\":null,\"transferred_at\":null,\"deleted_at\":null,\"last_error_code\":null,\"custom_metadata\":null,\"amount_debited\":null,\"receipt_url\":null,\"payment_method_id\":null,\"sub_accounts_commissions\":null,\"transaction_key\":null,\"merchant_reference\":null,\"account_id\":22551,\"balance_id\":null,\"payment_token\":\"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjQ4NzE2MSwiZXhwIjoxNzg2Nzc5OTgyfQ.K7V1g4wanUocFDB0D0KCqFyGgy5ySw_BKtXv7sfTogU\",\"payment_url\":\"https:\\/\\/sandbox-process.fedapay.com\\/eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjQ4NzE2MSwiZXhwIjoxNzg2Nzc5OTgyfQ.K7V1g4wanUocFDB0D0KCqFyGgy5ySw_BKtXv7sfTogU\",\"flags\":[],\"to_be_transferred_at\":null}', 25, 0, 'CMD-20260814094620-2C921A', '2026-08-14 07:46:20', NULL),
(30, 4, 3, 20000.00, 'en_attente', '499537', '{\"klass\":\"v1\\/transaction\",\"id\":499537,\"reference\":\"trx_yc4_1788561596071\",\"amount\":20000,\"description\":\"Soins du visage\",\"callback_url\":\"https:\\/\\/femiempire.free.nf\\/pages\\/callback.php\",\"status\":\"pending\",\"customer_id\":117776,\"currency_id\":1,\"mode\":null,\"operation\":\"payment\",\"metadata\":{\"expire_schedule_jobid\":\"d9cdccd4ce5b7672a06832f4\"},\"commission\":null,\"fees\":null,\"fixed_commission\":0,\"amount_transferred\":null,\"created_at\":\"2026-09-04T22:39:56.136Z\",\"updated_at\":\"2026-09-04T22:39:56.216Z\",\"approved_at\":null,\"canceled_at\":null,\"declined_at\":null,\"refunded_at\":null,\"transferred_at\":null,\"deleted_at\":null,\"last_error_code\":null,\"custom_metadata\":null,\"amount_debited\":null,\"receipt_url\":null,\"payment_method_id\":null,\"sub_accounts_commissions\":null,\"transaction_key\":null,\"merchant_reference\":null,\"account_id\":22551,\"balance_id\":null,\"payment_token\":\"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjQ5OTUzNywiZXhwIjoxNzg4NjQ3OTk2fQ.MM_lq_XSC5xSsgyDfyiDQXunuOeKpH7VAM8tdVdfIQg\",\"payment_url\":\"https:\\/\\/sandbox-process.fedapay.com\\/eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjQ5OTUzNywiZXhwIjoxNzg4NjQ3OTk2fQ.MM_lq_XSC5xSsgyDfyiDQXunuOeKpH7VAM8tdVdfIQg\",\"flags\":[],\"to_be_transferred_at\":null}', 0, 0, 'CMD-20260905003953-BEDC9A', '2026-08-14 07:56:19', NULL),
(31, 4, 4, 18000.00, 'en_attente', '487164', '{\"klass\":\"v1\\/transaction\",\"id\":487164,\"reference\":\"trx_7qA_1786694391136\",\"amount\":18000,\"description\":\"Maquillage professionnel\",\"callback_url\":\"https:\\/\\/femiempire.free.nf\\/pages\\/callback.php\",\"status\":\"pending\",\"customer_id\":112720,\"currency_id\":1,\"mode\":null,\"operation\":\"payment\",\"metadata\":{\"expire_schedule_jobid\":\"08be293635d700a44bcc2f50\"},\"commission\":null,\"fees\":null,\"fixed_commission\":0,\"amount_transferred\":null,\"created_at\":\"2026-08-14T07:59:51.221Z\",\"updated_at\":\"2026-08-14T07:59:51.314Z\",\"approved_at\":null,\"canceled_at\":null,\"declined_at\":null,\"refunded_at\":null,\"transferred_at\":null,\"deleted_at\":null,\"last_error_code\":null,\"custom_metadata\":null,\"amount_debited\":null,\"receipt_url\":null,\"payment_method_id\":null,\"sub_accounts_commissions\":null,\"transaction_key\":null,\"merchant_reference\":null,\"account_id\":22551,\"balance_id\":null,\"payment_token\":\"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjQ4NzE2NCwiZXhwIjoxNzg2NzgwNzkxfQ.HY8qhQ4-l5_BHAm8N2_SHQiTB0uF1fHA2ge-1POdaVE\",\"payment_url\":\"https:\\/\\/sandbox-process.fedapay.com\\/eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjQ4NzE2NCwiZXhwIjoxNzg2NzgwNzkxfQ.HY8qhQ4-l5_BHAm8N2_SHQiTB0uF1fHA2ge-1POdaVE\",\"flags\":[],\"to_be_transferred_at\":null}', 0, 0, 'CMD-20260814095949-DC2C08', '2026-08-14 07:59:49', NULL),
(32, 4, 5, 12000.00, 'en_attente', '499397', '{\"klass\":\"v1\\/transaction\",\"id\":499397,\"reference\":\"trx_dpE_1788538756268\",\"amount\":12000,\"description\":\"Épilation\",\"callback_url\":\"https:\\/\\/femiempire.free.nf\\/pages\\/callback.php\",\"status\":\"pending\",\"customer_id\":117731,\"currency_id\":1,\"mode\":null,\"operation\":\"payment\",\"metadata\":{\"expire_schedule_jobid\":\"dc4d1ef5698c229fa580cd15\"},\"commission\":null,\"fees\":null,\"fixed_commission\":0,\"amount_transferred\":null,\"created_at\":\"2026-09-04T16:19:16.372Z\",\"updated_at\":\"2026-09-04T16:19:16.485Z\",\"approved_at\":null,\"canceled_at\":null,\"declined_at\":null,\"refunded_at\":null,\"transferred_at\":null,\"deleted_at\":null,\"last_error_code\":null,\"custom_metadata\":null,\"amount_debited\":null,\"receipt_url\":null,\"payment_method_id\":null,\"sub_accounts_commissions\":null,\"transaction_key\":null,\"merchant_reference\":null,\"account_id\":22551,\"balance_id\":null,\"payment_token\":\"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjQ5OTM5NywiZXhwIjoxNzg4NjI1MTU2fQ.nriBdNc_FghDMlwCvDL3hxhCjFz75TKij-K3Obda7RQ\",\"payment_url\":\"https:\\/\\/sandbox-process.fedapay.com\\/eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjQ5OTM5NywiZXhwIjoxNzg4NjI1MTU2fQ.nriBdNc_FghDMlwCvDL3hxhCjFz75TKij-K3Obda7RQ\",\"flags\":[],\"to_be_transferred_at\":null}', 0, 0, 'CMD-20260904181912-59E5B7', '2026-08-14 08:07:10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `exercices`
--

CREATE TABLE `exercices` (
  `id` int(11) NOT NULL,
  `formation_id` int(11) DEFAULT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `fichier` varchar(255) DEFAULT NULL,
  `date_limite` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exercices`
--

INSERT INTO `exercices` (`id`, `formation_id`, `titre`, `description`, `fichier`, `date_limite`, `created_at`) VALUES
(3, 2, 'Onglerie débutant', 'ri \'fv\"ryffh\' ihf\'fh\"\' _fg\"fhge yfu', '6a9b09e8501a8.pdf', '2026-09-12 23:59:59', '2026-09-04 20:11:52');

-- --------------------------------------------------------

--
-- Table structure for table `factures`
--

CREATE TABLE `factures` (
  `id` int(11) NOT NULL,
  `commande_id` int(11) NOT NULL,
  `numero` varchar(50) NOT NULL,
  `pdf` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `formations`
--

CREATE TABLE `formations` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `categorie` varchar(50) NOT NULL,
  `prix` decimal(10,2) NOT NULL DEFAULT 0.00,
  `duree` int(11) NOT NULL DEFAULT 0,
  `statut` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `formations`
--

INSERT INTO `formations` (`id`, `titre`, `description`, `image`, `categorie`, `prix`, `duree`, `statut`, `created_at`) VALUES
(1, 'Onglerie débutant', 'Apprenez les bases de l&amp;amp;amp;amp;amp;amp;amp;#039;onglerie : pose, remplissage et finitions.', '6a74679130c74.png', 'ongles', 14000.00, 120, 'inactive', '2026-07-22 09:00:31'),
(2, 'Onglerie avancé', 'Maîtrisez les techniques avancées : nail art, sculpture et gel.', NULL, 'onglerie', 25000.00, 180, 'active', '2026-07-22 09:00:31'),
(3, 'Soins du visage', 'Découvrez les techniques professionnelles de soins du visage.', NULL, 'visage', 20000.00, 150, 'active', '2026-07-22 09:00:31'),
(4, 'Maquillage professionnel', 'Formation complète au maquillage professionnel.', NULL, 'maquillage', 18000.00, 120, 'active', '2026-07-22 09:00:31'),
(5, 'Épilation', 'Techniques d\'épilation à la cire et au fil.', NULL, 'epilation', 12000.00, 90, 'active', '2026-07-22 09:00:31'),
(6, 'Manucure', 'Soins des mains et pose de vernis semi-permanent.', NULL, 'onglerie', 10000.00, 60, 'active', '2026-07-22 09:00:31'),
(7, 'Pédicure', 'Soins des pieds et pose de vernis.', NULL, 'onglerie', 10000.00, 60, 'active', '2026-07-22 09:00:31'),
(8, 'Extension de cils', 'Techniques de pose d\'extension de cils.', NULL, 'cils', 22000.00, 150, 'active', '2026-07-22 09:00:31');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `lu` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parametres`
--

CREATE TABLE `parametres` (
  `id` int(11) NOT NULL,
  `cle` varchar(100) NOT NULL,
  `valeur` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parametres`
--

INSERT INTO `parametres` (`id`, `cle`, `valeur`, `description`, `created_at`, `updated_at`) VALUES
(1, 'nom_site', 'FemiEmpire', 'Nom du site', '2026-08-05 22:11:27', '2026-08-05 22:11:27'),
(2, 'email_contact', 'contact@femiempire.com', 'Email de contact', '2026-08-05 22:11:27', '2026-08-05 22:11:27'),
(3, 'telephone', '+225 07 00 00 00', 'Numéro de téléphone', '2026-08-05 22:11:27', '2026-08-05 22:11:27'),
(4, 'adresse', 'Bénin, Calavi adresse imcomplet à renseigner', 'Adresse physique', '2026-08-05 22:11:27', '2026-08-06 11:31:04'),
(5, 'devise', 'CFA', 'Devise utilisée', '2026-08-05 22:11:27', '2026-08-06 11:29:51'),
(6, 'frais_inscription', '0', 'Frais d\'inscription par défaut', '2026-08-05 22:11:27', '2026-08-05 22:11:27'),
(7, 'admin_email', 'admin@femiempire.com', 'Email administrateur', '2026-08-05 22:11:27', '2026-08-05 22:11:27');

-- --------------------------------------------------------

--
-- Table structure for table `realisations`
--

CREATE TABLE `realisations` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `exercice_id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `fichier` varchar(255) DEFAULT NULL,
  `statut` enum('en_attente','validee','refusee') DEFAULT 'en_attente',
  `commentaire_admin` text DEFAULT NULL,
  `date_soumission` datetime DEFAULT current_timestamp(),
  `date_validation` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `realisations`
--

INSERT INTO `realisations` (`id`, `utilisateur_id`, `exercice_id`, `titre`, `description`, `fichier`, `statut`, `commentaire_admin`, `date_soumission`, `date_validation`) VALUES
(1, 4, 3, 'projet 1', 'ygi ibi ii', 'b19a73eaa01ae9f2_1788561222.pdf', 'validee', 'bravo', '2026-09-05 00:33:42', '2026-09-05 00:34:37');

-- --------------------------------------------------------

--
-- Table structure for table `supports`
--

CREATE TABLE `supports` (
  `id` int(11) NOT NULL,
  `formation_id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `type` varchar(100) NOT NULL,
  `fichier` varchar(255) DEFAULT NULL,
  `lien_externe` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supports`
--

INSERT INTO `supports` (`id`, `formation_id`, `titre`, `type`, `fichier`, `lien_externe`, `created_at`) VALUES
(1, 2, 'jvbf', 'document', NULL, 'https://www.youtube.com/watch?v=CxnJf0tWu48&amp;list=RDWAoXXfvcFuQ&amp;index=2', '2026-08-06 11:59:26'),
(2, 2, 'bonjour', 'document', NULL, 'https://www.youtube.com/watch?v=CxnJf0tWu48&amp;list=RDWAoXXfvcFuQ&amp;index=2', '2026-09-04 17:49:38');

-- --------------------------------------------------------

--
-- Table structure for table `transactions_fedapay`
--

CREATE TABLE `transactions_fedapay` (
  `id` int(11) NOT NULL,
  `commande_id` int(11) NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `reference` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) DEFAULT 'XOF',
  `status` enum('pending','approved','canceled','failed') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(50) DEFAULT NULL,
  `webhook_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`webhook_data`)),
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_validation` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('client','admin') DEFAULT 'client',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `prenom`, `nom`, `email`, `telephone`, `password`, `role`, `created_at`) VALUES
(1, 'Admin', 'ADMIN', 'admin@gmail.com', '+229 0153999957', 'Admin123', 'client', '2026-07-21 14:15:43'),
(2, 'Client', 'CLIENT', 'client@gmail.com', '+229 0153999957', 'Client123', 'client', '2026-07-21 14:15:43'),
(3, 'Date', 'DATE', 'prislauress@gmail.com', '+229 0153999957', '$2y$12$Mhj2yMJ3iaFR0ejBP2W3eu9nPclgbccYUju.IdBrQigWCs92peAhq', 'admin', '2026-07-22 08:56:31'),
(4, 'Date', 'DATA', 'date@gmail.com', '+229 0153999957', '$2y$12$w0GBq2EJQqGqo2Zoyy5LW.2O4GFEWeuGmYxHcbXzAw2btcGtFNJm2', 'client', '2026-07-27 09:13:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference` (`reference`),
  ADD KEY `utilisateur_id` (`utilisateur_id`),
  ADD KEY `formation_id` (`formation_id`);

--
-- Indexes for table `exercices`
--
ALTER TABLE `exercices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_formation` (`formation_id`);

--
-- Indexes for table `factures`
--
ALTER TABLE `factures`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero` (`numero`),
  ADD KEY `commande_id` (`commande_id`);

--
-- Indexes for table `formations`
--
ALTER TABLE `formations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Indexes for table `parametres`
--
ALTER TABLE `parametres`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cle` (`cle`);

--
-- Indexes for table `realisations`
--
ALTER TABLE `realisations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`),
  ADD KEY `exercice_id` (`exercice_id`);

--
-- Indexes for table `supports`
--
ALTER TABLE `supports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_supports_formation` (`formation_id`);

--
-- Indexes for table `transactions_fedapay`
--
ALTER TABLE `transactions_fedapay`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_transaction` (`transaction_id`),
  ADD KEY `commande_id` (`commande_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_reference` (`reference`);

--
-- Indexes for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `exercices`
--
ALTER TABLE `exercices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `factures`
--
ALTER TABLE `factures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `formations`
--
ALTER TABLE `formations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parametres`
--
ALTER TABLE `parametres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `realisations`
--
ALTER TABLE `realisations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `supports`
--
ALTER TABLE `supports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transactions_fedapay`
--
ALTER TABLE `transactions_fedapay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `commandes`
--
ALTER TABLE `commandes`
  ADD CONSTRAINT `commandes_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `commandes_ibfk_2` FOREIGN KEY (`formation_id`) REFERENCES `formations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exercices`
--
ALTER TABLE `exercices`
  ADD CONSTRAINT `exercices_ibfk_2` FOREIGN KEY (`formation_id`) REFERENCES `formations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `factures`
--
ALTER TABLE `factures`
  ADD CONSTRAINT `factures_ibfk_1` FOREIGN KEY (`commande_id`) REFERENCES `commandes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `realisations`
--
ALTER TABLE `realisations`
  ADD CONSTRAINT `realisations_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `realisations_ibfk_2` FOREIGN KEY (`exercice_id`) REFERENCES `exercices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `supports`
--
ALTER TABLE `supports`
  ADD CONSTRAINT `fk_supports_formation` FOREIGN KEY (`formation_id`) REFERENCES `formations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transactions_fedapay`
--
ALTER TABLE `transactions_fedapay`
  ADD CONSTRAINT `transactions_fedapay_ibfk_1` FOREIGN KEY (`commande_id`) REFERENCES `commandes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
