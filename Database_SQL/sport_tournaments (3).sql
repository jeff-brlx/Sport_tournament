-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 23 avr. 2024 à 21:26
-- Version du serveur : 8.2.0
-- Version de PHP : 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `sport_tournaments`
--

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `body` longtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `registration`
--

DROP TABLE IF EXISTS `registration`;
CREATE TABLE IF NOT EXISTS `registration` (
  `id` int NOT NULL AUTO_INCREMENT,
  `player_id` int NOT NULL,
  `tournament_id` int NOT NULL,
  `registration_date` date NOT NULL,
  `status` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_62A8A7A799E6F5DF` (`player_id`),
  KEY `IDX_62A8A7A733D1A3E7` (`tournament_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sport_match`
--

DROP TABLE IF EXISTS `sport_match`;
CREATE TABLE IF NOT EXISTS `sport_match` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tournament_id` int NOT NULL,
  `player2_id` int NOT NULL,
  `player1_id` int NOT NULL,
  `match_date` date NOT NULL,
  `score_player2` int DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `score_player1` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CE27A41C33D1A3E7` (`tournament_id`),
  KEY `IDX_CE27A41CD22CABCD` (`player2_id`),
  KEY `IDX_CE27A41CC0990423` (`player1_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tournament`
--

DROP TABLE IF EXISTS `tournament`;
CREATE TABLE IF NOT EXISTS `tournament` (
  `id` int NOT NULL AUTO_INCREMENT,
  `organizer_id` int NOT NULL,
  `tournament_name` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `location` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `description` longtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `max_participants` int NOT NULL,
  `status` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `sport` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `winner_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BD5FB8D9876C4DDA` (`organizer_id`),
  KEY `IDX_BD5FB8D95DFCD4B8` (`winner_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `last_name` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `user_name` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `email_address` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `roles` longtext COLLATE utf8mb3_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `registration`
--
ALTER TABLE `registration`
  ADD CONSTRAINT `FK_62A8A7A733D1A3E7` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`id`),
  ADD CONSTRAINT `FK_62A8A7A799E6F5DF` FOREIGN KEY (`player_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `sport_match`
--
ALTER TABLE `sport_match`
  ADD CONSTRAINT `FK_CE27A41C33D1A3E7` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`id`),
  ADD CONSTRAINT `FK_CE27A41CC0990423` FOREIGN KEY (`player1_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_CE27A41CD22CABCD` FOREIGN KEY (`player2_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `tournament`
--
ALTER TABLE `tournament`
  ADD CONSTRAINT `FK_BD5FB8D95DFCD4B8` FOREIGN KEY (`winner_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_BD5FB8D9876C4DDA` FOREIGN KEY (`organizer_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
