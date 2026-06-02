-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 31 mars 2026 à 18:59
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `new_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `admins`
--

CREATE TABLE `admins` (
  `utilisateur_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `admins`
--

INSERT INTO `admins` (`utilisateur_id`, `created_at`) VALUES
(1, '2026-03-31 08:54:33');

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `nom`, `description`, `created_at`) VALUES
(1, 'Électronique', 'Appareils électroniques et accessoires', '2026-03-31 08:54:33'),
(2, 'Alimentation', 'Produits alimentaires et boissons', '2026-03-31 08:54:33'),
(3, 'Vêtements', 'Habillement et accessoires de mode', '2026-03-31 08:54:33'),
(4, 'Informatique', 'Matériel et logiciels informatiques', '2026-03-31 08:54:33'),
(5, 'Maison', 'Articles pour la maison et la cuisine', '2026-03-31 08:54:33');

-- --------------------------------------------------------

--
-- Structure de la table `fournisseurs`
--

CREATE TABLE `fournisseurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `contact` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `fournisseurs`
--

INSERT INTO `fournisseurs` (`id`, `nom`, `contact`, `email`, `telephone`, `adresse`, `utilisateur_id`, `created_at`) VALUES
(1, 'TechDistrib SA', 'Pierre Leblanc', 'contact@techdistrib.fr', '01 23 45 67 89', NULL, 3, '2026-03-31 08:54:33'),
(2, 'FoodSupply SARL', 'Alice Moreau', 'alice@foodsupply.fr', '04 56 78 90 12', NULL, 4, '2026-03-31 08:54:33'),
(3, 'ElectroPlus', 'Robert Denis', 'info@electroplus.com', '06 11 22 33 44', NULL, 5, '2026-03-31 08:54:33');

-- --------------------------------------------------------

--
-- Structure de la table `lignes_vente`
--

CREATE TABLE `lignes_vente` (
  `id` int(11) NOT NULL,
  `vente_id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  `prix_unitaire` decimal(10,2) NOT NULL,
  `sous_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `lignes_vente`
--

INSERT INTO `lignes_vente` (`id`, `vente_id`, `produit_id`, `quantite`, `prix_unitaire`, `sous_total`) VALUES
(1, 1, 3, 1, 89.99, 89.99),
(2, 1, 5, 2, 14.99, 29.98),
(3, 1, 8, 2, 249.99, 499.98),
(4, 2, 3, 1, 89.99, 89.99),
(5, 2, 4, 1, 4.99, 4.99),
(6, 2, 9, 1, 109.99, 109.99),
(7, 2, 10, 1, 3.49, 3.49);

-- --------------------------------------------------------

--
-- Structure de la table `parametres_app`
--

CREATE TABLE `parametres_app` (
  `cle` varchar(100) NOT NULL,
  `valeur` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `parametres_app`
--

INSERT INTO `parametres_app` (`cle`, `valeur`, `updated_at`) VALUES
('app_name', 'A&B stock', '2026-03-31 08:54:33'),
('base_url', 'http://localhost/magasin', '2026-03-31 08:54:33'),
('tva_taux', '20.00', '2026-03-31 08:54:33');

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

CREATE TABLE `produits` (
  `id` int(11) NOT NULL,
  `nom` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `prix_achat` decimal(10,2) NOT NULL DEFAULT 0.00,
  `prix_vente` decimal(10,2) NOT NULL DEFAULT 0.00,
  `quantite_stock` int(11) NOT NULL DEFAULT 0,
  `seuil_alerte` int(11) NOT NULL DEFAULT 5,
  `categorie_id` int(11) DEFAULT NULL,
  `fournisseur_id` int(11) DEFAULT NULL,
  `code_barre` varchar(100) DEFAULT NULL,
  `actif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id`, `nom`, `description`, `prix_achat`, `prix_vente`, `quantite_stock`, `seuil_alerte`, `categorie_id`, `fournisseur_id`, `code_barre`, `actif`, `created_at`, `updated_at`) VALUES
(1, 'Smartphone Galaxy X', 'Téléphone 6.5\" AMOLED 128Go', 350.00, 599.99, 25, 5, 1, 1, 'SG-001', 1, '2026-03-31 08:54:33', '2026-03-31 08:54:33'),
(2, 'Laptop ProBook 15', 'Ordinateur portable i7 16Go RAM', 700.00, 1199.99, 8, 3, 4, 1, 'LP-002', 1, '2026-03-31 08:54:33', '2026-03-31 08:54:33'),
(3, 'Casque Audio BT', 'Casque Bluetooth réduction de bruit', 45.00, 89.99, 1, 5, 1, 1, 'CA-003', 1, '2026-03-31 08:54:33', '2026-03-31 11:56:04'),
(4, 'Eau minérale 1.5L', 'Pack de 6 bouteilles eau minérale', 2.50, 4.99, 149, 20, 2, 2, 'EM-004', 1, '2026-03-31 08:54:33', '2026-03-31 11:56:04'),
(5, 'Café Arabica 500g', 'Café en grains origine Éthiopie', 7.00, 14.99, 58, 10, 2, 2, 'CG-005', 1, '2026-03-31 08:54:33', '2026-03-31 09:45:33'),
(6, 'T-Shirt Coton Bio', 'T-shirt 100% coton biologique', 8.00, 24.99, 4, 10, 3, NULL, 'TC-006', 1, '2026-03-31 08:54:33', '2026-03-31 08:54:33'),
(7, 'Souris optique USB', 'Souris filaire haute précision', 12.00, 24.99, 45, 8, 4, 1, 'SO-007', 1, '2026-03-31 08:54:33', '2026-03-31 08:54:33'),
(8, 'Cafetière Express', 'Machine expresso automatique', 120.00, 249.99, 0, 3, 5, 3, 'CE-008', 1, '2026-03-31 08:54:33', '2026-03-31 09:45:33'),
(9, 'Clavier Mécanique', 'Clavier gaming RGB switches Cherry', 55.00, 109.99, 19, 5, 4, 3, 'CM-009', 1, '2026-03-31 08:54:33', '2026-03-31 11:56:04'),
(10, 'Jus d orange 1L', 'Jus 100% pur jus pressé', 1.80, 3.49, 89, 15, 2, 2, 'JO-010', 1, '2026-03-31 08:54:33', '2026-03-31 11:56:04'),
(11, 'test', 'jai no', 545.00, 5455.00, 10, 5, 5, 3, '01', 1, '2026-03-31 09:42:31', '2026-03-31 09:44:05');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('admin','vendeur','fournisseur') NOT NULL DEFAULT 'vendeur',
  `actif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `role`, `actif`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'Super', 'admin@magasin.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, '2026-03-31 08:54:33', '2026-03-31 08:54:33'),
(2, 'Martin', 'Jean', 'vendeur@magasin.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'vendeur', 1, '2026-03-31 08:54:33', '2026-03-31 08:54:33'),
(3, 'Dupont', 'Marie', 'fournisseur@magasin.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'fournisseur', 1, '2026-03-31 08:54:33', '2026-03-31 08:54:33'),
(4, 'FoodSupply SARL', 'Alice Moreau', 'alice@foodsupply.fr', '.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'fournisseur', 1, '2026-03-31 10:27:43', '2026-03-31 10:27:43'),
(5, 'ElectroPlus', 'Robert Denis', 'info@electroplus.com', '.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'fournisseur', 1, '2026-03-31 10:27:43', '2026-03-31 10:27:43');

-- --------------------------------------------------------

--
-- Structure de la table `vendeurs`
--

CREATE TABLE `vendeurs` (
  `utilisateur_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `vendeurs`
--

INSERT INTO `vendeurs` (`utilisateur_id`, `created_at`) VALUES
(2, '2026-03-31 08:54:33');

-- --------------------------------------------------------

--
-- Structure de la table `ventes`
--

CREATE TABLE `ventes` (
  `id` int(11) NOT NULL,
  `reference` varchar(50) NOT NULL,
  `vendeur_id` int(11) NOT NULL,
  `total_ht` decimal(10,2) NOT NULL DEFAULT 0.00,
  `taux_tva` decimal(5,2) NOT NULL DEFAULT 20.00,
  `total_ttc` decimal(10,2) NOT NULL DEFAULT 0.00,
  `statut` enum('en_cours','validee','annulee') DEFAULT 'validee',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `ventes`
--

INSERT INTO `ventes` (`id`, `reference`, `vendeur_id`, `total_ht`, `taux_tva`, `total_ttc`, `statut`, `created_at`) VALUES
(1, 'VTE-20260331-6C719', 1, 619.95, 20.00, 743.94, 'validee', '2026-03-31 09:45:33'),
(2, 'VTE-20260331-9B7F8', 2, 208.46, 20.00, 250.15, 'validee', '2026-03-31 11:56:04');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`utilisateur_id`);

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `fournisseurs`
--
ALTER TABLE `fournisseurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_fournisseurs_utilisateur_id` (`utilisateur_id`);

--
-- Index pour la table `lignes_vente`
--
ALTER TABLE `lignes_vente`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vente_id` (`vente_id`),
  ADD KEY `produit_id` (`produit_id`);

--
-- Index pour la table `parametres_app`
--
ALTER TABLE `parametres_app`
  ADD PRIMARY KEY (`cle`);

--
-- Index pour la table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code_barre` (`code_barre`),
  ADD KEY `categorie_id` (`categorie_id`),
  ADD KEY `fournisseur_id` (`fournisseur_id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `vendeurs`
--
ALTER TABLE `vendeurs`
  ADD PRIMARY KEY (`utilisateur_id`);

--
-- Index pour la table `ventes`
--
ALTER TABLE `ventes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference` (`reference`),
  ADD KEY `vendeur_id` (`vendeur_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `fournisseurs`
--
ALTER TABLE `fournisseurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `lignes_vente`
--
ALTER TABLE `lignes_vente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `ventes`
--
ALTER TABLE `ventes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `fournisseurs`
--
ALTER TABLE `fournisseurs`
  ADD CONSTRAINT `fournisseurs_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `lignes_vente`
--
ALTER TABLE `lignes_vente`
  ADD CONSTRAINT `lignes_vente_ibfk_1` FOREIGN KEY (`vente_id`) REFERENCES `ventes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lignes_vente_ibfk_2` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`);

--
-- Contraintes pour la table `produits`
--
ALTER TABLE `produits`
  ADD CONSTRAINT `produits_ibfk_1` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `produits_ibfk_2` FOREIGN KEY (`fournisseur_id`) REFERENCES `fournisseurs` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `vendeurs`
--
ALTER TABLE `vendeurs`
  ADD CONSTRAINT `vendeurs_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `ventes`
--
ALTER TABLE `ventes`
  ADD CONSTRAINT `ventes_ibfk_1` FOREIGN KEY (`vendeur_id`) REFERENCES `utilisateurs` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
