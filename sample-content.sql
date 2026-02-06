/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-12.1.2-MariaDB, for osx10.20 (arm64)
--
-- Host: localhost    Database: wfcs
-- ------------------------------------------------------
-- Server version	10.11.11-MariaDB-embedded

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `cup_fixtures`
--

DROP TABLE IF EXISTS `cup_fixtures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cup_fixtures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cup_id` int(11) NOT NULL,
  `round_id` int(11) NOT NULL,
  `home_team_id` int(11) DEFAULT NULL,
  `away_team_id` int(11) DEFAULT NULL,
  `match_date` date DEFAULT NULL,
  `match_time` time DEFAULT NULL,
  `pitch` varchar(50) DEFAULT NULL,
  `referee_id` int(11) DEFAULT NULL,
  `is_live` tinyint(1) DEFAULT 0,
  `status` enum('scheduled','in_progress','completed','postponed','cancelled') DEFAULT 'scheduled',
  `match_report` text DEFAULT NULL,
  `live_stream_url` varchar(500) DEFAULT NULL,
  `full_match_url` varchar(500) DEFAULT NULL,
  `highlights_url` varchar(500) DEFAULT NULL,
  `home_score` int(11) DEFAULT NULL,
  `away_score` int(11) DEFAULT NULL,
  `extra_time` tinyint(1) DEFAULT 0,
  `home_score_et` int(11) DEFAULT NULL,
  `away_score_et` int(11) DEFAULT NULL,
  `penalties` tinyint(1) DEFAULT 0,
  `home_pens` int(11) DEFAULT NULL,
  `away_pens` int(11) DEFAULT NULL,
  `winner` enum('home','away') DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cup_id` (`cup_id`),
  KEY `round_id` (`round_id`),
  KEY `home_team_id` (`home_team_id`),
  KEY `away_team_id` (`away_team_id`),
  KEY `fk_cup_referee` (`referee_id`),
  CONSTRAINT `cup_fixtures_ibfk_1` FOREIGN KEY (`cup_id`) REFERENCES `cups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cup_fixtures_ibfk_2` FOREIGN KEY (`round_id`) REFERENCES `cup_rounds` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cup_fixtures_ibfk_3` FOREIGN KEY (`home_team_id`) REFERENCES `teams` (`id`),
  CONSTRAINT `cup_fixtures_ibfk_4` FOREIGN KEY (`away_team_id`) REFERENCES `teams` (`id`),
  CONSTRAINT `fk_cup_referee` FOREIGN KEY (`referee_id`) REFERENCES `team_staff` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cup_fixtures`
--

LOCK TABLES `cup_fixtures` WRITE;
/*!40000 ALTER TABLE `cup_fixtures` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `cup_fixtures` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `cup_rounds`
--

DROP TABLE IF EXISTS `cup_rounds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cup_rounds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cup_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `round_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cup_id` (`cup_id`),
  CONSTRAINT `cup_rounds_ibfk_1` FOREIGN KEY (`cup_id`) REFERENCES `cups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cup_rounds`
--

LOCK TABLES `cup_rounds` WRITE;
/*!40000 ALTER TABLE `cup_rounds` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `cup_rounds` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `cup_teams`
--

DROP TABLE IF EXISTS `cup_teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cup_teams` (
  `cup_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  PRIMARY KEY (`cup_id`,`team_id`),
  KEY `team_id` (`team_id`),
  CONSTRAINT `cup_teams_ibfk_1` FOREIGN KEY (`cup_id`) REFERENCES `cups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cup_teams_ibfk_2` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cup_teams`
--

LOCK TABLES `cup_teams` WRITE;
/*!40000 ALTER TABLE `cup_teams` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `cup_teams` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `cups`
--

DROP TABLE IF EXISTS `cups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `season_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `start_date` date DEFAULT NULL,
  `frequency` enum('weekly','fortnightly','monthly') DEFAULT 'weekly',
  `match_time` time DEFAULT '15:00:00',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `season_id` (`season_id`),
  CONSTRAINT `cups_ibfk_1` FOREIGN KEY (`season_id`) REFERENCES `seasons` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cups`
--

LOCK TABLES `cups` WRITE;
/*!40000 ALTER TABLE `cups` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `cups` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `fixture_photos`
--

DROP TABLE IF EXISTS `fixture_photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fixture_photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fixture_id` int(11) NOT NULL,
  `fixture_type` enum('league','cup') NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_fixture` (`fixture_id`,`fixture_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fixture_photos`
--

LOCK TABLES `fixture_photos` WRITE;
/*!40000 ALTER TABLE `fixture_photos` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `fixture_photos` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `league_fixtures`
--

DROP TABLE IF EXISTS `league_fixtures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `league_fixtures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `league_id` int(11) NOT NULL,
  `home_team_id` int(11) NOT NULL,
  `away_team_id` int(11) NOT NULL,
  `match_date` date DEFAULT NULL,
  `match_time` time DEFAULT NULL,
  `pitch` varchar(50) DEFAULT NULL,
  `referee_id` int(11) DEFAULT NULL,
  `is_live` tinyint(1) DEFAULT 0,
  `status` enum('scheduled','in_progress','completed','postponed','cancelled') DEFAULT 'scheduled',
  `match_report` text DEFAULT NULL,
  `live_stream_url` varchar(500) DEFAULT NULL,
  `full_match_url` varchar(500) DEFAULT NULL,
  `highlights_url` varchar(500) DEFAULT NULL,
  `home_score` int(11) DEFAULT NULL,
  `away_score` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `league_id` (`league_id`),
  KEY `home_team_id` (`home_team_id`),
  KEY `away_team_id` (`away_team_id`),
  KEY `fk_league_referee` (`referee_id`),
  CONSTRAINT `fk_league_referee` FOREIGN KEY (`referee_id`) REFERENCES `team_staff` (`id`) ON DELETE SET NULL,
  CONSTRAINT `league_fixtures_ibfk_1` FOREIGN KEY (`league_id`) REFERENCES `leagues` (`id`) ON DELETE CASCADE,
  CONSTRAINT `league_fixtures_ibfk_2` FOREIGN KEY (`home_team_id`) REFERENCES `teams` (`id`),
  CONSTRAINT `league_fixtures_ibfk_3` FOREIGN KEY (`away_team_id`) REFERENCES `teams` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `league_fixtures`
--

LOCK TABLES `league_fixtures` WRITE;
/*!40000 ALTER TABLE `league_fixtures` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `league_fixtures` VALUES
(1,1,5,3,'2026-01-30','20:00:00',NULL,NULL,0,'completed',NULL,NULL,NULL,NULL,7,6,'2026-02-06 08:06:57'),
(2,1,6,1,'2026-01-30','20:00:00',NULL,NULL,0,'completed',NULL,NULL,NULL,NULL,3,0,'2026-02-06 08:06:57'),
(3,1,4,2,'2026-01-30','20:00:00',NULL,NULL,0,'completed',NULL,NULL,NULL,NULL,3,3,'2026-02-06 08:06:57'),
(24,2,10,14,'2026-01-28','20:00:00',NULL,NULL,0,'completed',NULL,NULL,NULL,NULL,7,6,'2026-02-06 08:06:57'),
(25,2,7,13,'2026-01-28','20:00:00',NULL,NULL,0,'completed',NULL,NULL,NULL,NULL,4,3,'2026-02-06 08:06:57'),
(26,2,11,9,'2026-01-28','20:00:00',NULL,NULL,0,'completed',NULL,NULL,NULL,NULL,6,4,'2026-02-06 08:06:57'),
(27,2,12,8,'2026-01-28','20:00:00',NULL,NULL,0,'completed',NULL,NULL,NULL,NULL,2,9,'2026-02-06 08:06:57'),
(28,2,13,10,'2026-02-04','20:00:00',NULL,NULL,0,'completed',NULL,NULL,NULL,NULL,6,6,'2026-02-06 08:06:57'),
(29,2,11,14,'2026-02-04','20:00:00',NULL,NULL,0,'completed',NULL,NULL,NULL,NULL,5,3,'2026-02-06 08:06:57'),
(30,2,12,7,'2026-02-04','20:00:00',NULL,NULL,0,'completed',NULL,NULL,NULL,NULL,5,1,'2026-02-06 08:06:57'),
(31,2,8,9,'2026-02-04','20:00:00',NULL,NULL,0,'completed',NULL,NULL,NULL,NULL,5,8,'2026-02-06 08:06:57'),
(63,3,2,21,'2026-01-30','21:00:00',NULL,NULL,0,'completed',NULL,NULL,NULL,NULL,6,1,'2026-02-06 08:06:57'),
(64,3,20,22,'2026-01-30','21:00:00',NULL,NULL,0,'completed',NULL,NULL,NULL,NULL,1,5,'2026-02-06 08:06:57'),
(86,1,1,6,'2025-09-01','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:12:00'),
(87,1,2,5,'2025-09-01','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:12:00'),
(88,1,3,4,'2025-09-01','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:12:00'),
(89,1,5,1,'2025-09-08','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:12:00'),
(90,1,6,4,'2025-09-08','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:12:00'),
(91,1,2,3,'2025-09-08','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:12:00'),
(92,1,1,4,'2025-09-15','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:12:00'),
(93,1,5,3,'2025-09-15','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:12:00'),
(94,1,6,2,'2025-09-15','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:12:00'),
(95,1,3,1,'2025-09-22','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:12:00'),
(96,1,4,2,'2025-09-22','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:12:00'),
(97,1,5,6,'2025-09-22','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:12:00'),
(98,1,1,2,'2025-09-29','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:12:00'),
(99,1,3,6,'2025-09-29','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:12:00'),
(100,1,4,5,'2025-09-29','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:12:00'),
(101,1,6,1,'2025-10-13','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:12:00'),
(102,1,5,2,'2025-10-13','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:12:00'),
(103,1,4,3,'2025-10-13','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:12:00'),
(104,1,1,5,'2025-10-20','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:12:00'),
(105,1,4,6,'2025-10-20','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:12:00'),
(106,1,3,2,'2025-10-20','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:12:00'),
(107,1,4,1,'2025-10-27','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:12:00'),
(108,1,3,5,'2025-10-27','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:12:00'),
(109,1,2,6,'2025-10-27','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:12:00'),
(110,1,1,3,'2025-11-03','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:12:00'),
(111,1,2,4,'2025-11-03','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:12:00'),
(112,1,6,5,'2025-11-03','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:12:00'),
(113,1,2,1,'2025-11-10','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:12:00'),
(114,1,6,3,'2025-11-10','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:12:00'),
(115,1,5,4,'2025-11-10','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:12:00'),
(116,3,16,23,'2025-09-01','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(117,3,17,22,'2025-09-01','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(118,3,18,21,'2025-09-01','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(119,3,19,20,'2025-09-01','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(120,3,23,15,'2025-09-08','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(121,3,16,21,'2025-09-08','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(122,3,17,20,'2025-09-08','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(123,3,18,19,'2025-09-08','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(124,3,15,22,'2025-09-15','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(125,3,23,21,'2025-09-15','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(126,3,16,19,'2025-09-15','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(127,3,17,18,'2025-09-15','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(128,3,21,15,'2025-09-22','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(129,3,22,20,'2025-09-22','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(130,3,23,19,'2025-09-22','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(131,3,16,17,'2025-09-22','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(132,3,15,20,'2025-09-29','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(133,3,21,19,'2025-09-29','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(134,3,22,18,'2025-09-29','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(135,3,23,17,'2025-09-29','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(136,3,19,15,'2025-10-06','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(137,3,20,18,'2025-10-06','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(138,3,21,17,'2025-10-06','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(139,3,22,16,'2025-10-06','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(140,3,15,18,'2025-10-13','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(141,3,19,17,'2025-10-13','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(142,3,20,16,'2025-10-13','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(143,3,22,23,'2025-10-13','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(144,3,17,15,'2025-10-20','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(145,3,18,16,'2025-10-20','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(146,3,20,23,'2025-10-20','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(147,3,21,22,'2025-10-20','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(148,3,15,16,'2025-10-27','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(149,3,18,23,'2025-10-27','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(150,3,19,22,'2025-10-27','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(151,3,20,21,'2025-10-27','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(152,3,23,16,'2025-11-10','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(153,3,22,17,'2025-11-10','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(154,3,21,18,'2025-11-10','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(155,3,20,19,'2025-11-10','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(156,3,15,23,'2025-11-17','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(157,3,21,16,'2025-11-17','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(158,3,20,17,'2025-11-17','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(159,3,19,18,'2025-11-17','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(160,3,22,15,'2025-11-24','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(161,3,21,23,'2025-11-24','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(162,3,19,16,'2025-11-24','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(163,3,18,17,'2025-11-24','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(164,3,15,21,'2025-12-01','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(165,3,20,22,'2025-12-01','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(166,3,19,23,'2025-12-01','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(167,3,17,16,'2025-12-01','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(168,3,20,15,'2025-12-08','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(169,3,19,21,'2025-12-08','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(170,3,18,22,'2025-12-08','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(171,3,17,23,'2025-12-08','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(172,3,15,19,'2025-12-15','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(173,3,18,20,'2025-12-15','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(174,3,17,21,'2025-12-15','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(175,3,16,22,'2025-12-15','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(176,3,18,15,'2025-12-22','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(177,3,17,19,'2025-12-22','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(178,3,16,20,'2025-12-22','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(179,3,23,22,'2025-12-22','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(180,3,15,17,'2025-12-29','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(181,3,16,18,'2025-12-29','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(182,3,23,20,'2025-12-29','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(183,3,22,21,'2025-12-29','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(184,3,16,15,'2026-01-05','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(185,3,23,18,'2026-01-05','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(186,3,22,19,'2026-01-05','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(187,3,21,20,'2026-01-05','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:13:12'),
(188,2,7,14,'2025-09-01','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(189,2,8,13,'2025-09-01','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(190,2,9,12,'2025-09-01','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(191,2,10,11,'2025-09-01','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(192,2,13,7,'2025-09-08','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(193,2,14,12,'2025-09-08','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(194,2,8,11,'2025-09-08','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(195,2,9,10,'2025-09-08','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(196,2,7,12,'2025-09-15','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(197,2,13,11,'2025-09-15','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(198,2,14,10,'2025-09-15','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(199,2,8,9,'2025-09-15','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(200,2,11,7,'2025-09-22','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(201,2,12,10,'2025-09-22','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(202,2,13,9,'2025-09-22','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(203,2,14,8,'2025-09-22','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(204,2,7,10,'2025-09-29','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(205,2,11,9,'2025-09-29','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(206,2,12,8,'2025-09-29','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(207,2,13,14,'2025-09-29','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(208,2,9,7,'2025-10-06','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(209,2,10,8,'2025-10-06','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(210,2,11,14,'2025-10-06','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(211,2,12,13,'2025-10-06','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(212,2,7,8,'2025-10-13','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(213,2,9,14,'2025-10-13','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(214,2,10,13,'2025-10-13','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(215,2,11,12,'2025-10-13','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(216,2,14,7,'2025-10-27','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(217,2,13,8,'2025-10-27','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(218,2,12,9,'2025-10-27','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(219,2,11,10,'2025-10-27','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(220,2,7,13,'2025-11-03','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(221,2,12,14,'2025-11-03','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(222,2,11,8,'2025-11-03','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(223,2,10,9,'2025-11-03','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(224,2,12,7,'2025-11-10','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(225,2,11,13,'2025-11-10','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(226,2,10,14,'2025-11-10','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(227,2,9,8,'2025-11-10','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(228,2,7,11,'2025-11-17','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(229,2,10,12,'2025-11-17','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(230,2,9,13,'2025-11-17','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(231,2,8,14,'2025-11-17','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(232,2,10,7,'2025-11-24','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(233,2,9,11,'2025-11-24','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(234,2,8,12,'2025-11-24','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(235,2,14,13,'2025-11-24','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(236,2,7,9,'2025-12-01','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(237,2,8,10,'2025-12-01','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(238,2,14,11,'2025-12-01','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(239,2,13,12,'2025-12-01','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(240,2,8,7,'2025-12-08','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(241,2,14,9,'2025-12-08','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(242,2,13,10,'2025-12-08','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29'),
(243,2,12,11,'2025-12-08','20:00:00',NULL,NULL,0,'scheduled',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-06 08:38:29');
/*!40000 ALTER TABLE `league_fixtures` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `league_teams`
--

DROP TABLE IF EXISTS `league_teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `league_teams` (
  `league_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  PRIMARY KEY (`league_id`,`team_id`),
  KEY `team_id` (`team_id`),
  CONSTRAINT `league_teams_ibfk_1` FOREIGN KEY (`league_id`) REFERENCES `leagues` (`id`) ON DELETE CASCADE,
  CONSTRAINT `league_teams_ibfk_2` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `league_teams`
--

LOCK TABLES `league_teams` WRITE;
/*!40000 ALTER TABLE `league_teams` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `league_teams` VALUES
(1,1),
(1,2),
(1,3),
(1,4),
(1,5),
(1,6),
(2,7),
(2,8),
(2,9),
(2,10),
(2,11),
(2,12),
(2,13),
(2,14),
(3,15),
(3,16),
(3,17),
(3,18),
(3,19),
(3,20),
(3,21),
(3,22),
(3,23);
/*!40000 ALTER TABLE `league_teams` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `leagues`
--

DROP TABLE IF EXISTS `leagues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `leagues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `season_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `start_date` date DEFAULT NULL,
  `frequency` enum('weekly','fortnightly','monthly') DEFAULT 'weekly',
  `match_time` time DEFAULT '15:00:00',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `season_id` (`season_id`),
  CONSTRAINT `leagues_ibfk_1` FOREIGN KEY (`season_id`) REFERENCES `seasons` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leagues`
--

LOCK TABLES `leagues` WRITE;
/*!40000 ALTER TABLE `leagues` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `leagues` VALUES
(1,1,'Well Foundation Premiership','well-foundation-premiership','2025-09-01','weekly','20:00:00','2026-02-06 08:06:57','2026-02-06 08:12:00'),
(2,1,'Well Foundation Community League','well-foundation-community-league','2025-09-01','weekly','20:00:00','2026-02-06 08:06:57','2026-02-06 08:38:29'),
(3,1,'Well Foundation Super League','well-foundation-super-league','2025-09-01','weekly','20:00:00','2026-02-06 08:06:57','2026-02-06 08:13:12');
/*!40000 ALTER TABLE `leagues` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `match_events`
--

DROP TABLE IF EXISTS `match_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `match_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fixture_type` enum('league','cup') NOT NULL,
  `fixture_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `player_id` int(11) DEFAULT NULL,
  `event_type` enum('goal','yellow_card','red_card','blue_card','sin_bin','assist') NOT NULL,
  `minute` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_fixture` (`fixture_type`,`fixture_id`),
  KEY `idx_player` (`player_id`),
  KEY `idx_team` (`team_id`),
  CONSTRAINT `match_events_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`) ON DELETE SET NULL,
  CONSTRAINT `match_events_ibfk_2` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `match_events`
--

LOCK TABLES `match_events` WRITE;
/*!40000 ALTER TABLE `match_events` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `match_events` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `player_stats`
--

DROP TABLE IF EXISTS `player_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `player_stats` (
  `player_id` int(11) NOT NULL,
  `team_id` int(11) DEFAULT NULL,
  `total_goals` int(11) DEFAULT 0,
  `total_assists` int(11) DEFAULT 0,
  `yellow_cards` int(11) DEFAULT 0,
  `red_cards` int(11) DEFAULT 0,
  `blue_cards` int(11) DEFAULT 0,
  `sin_bins` int(11) DEFAULT 0,
  `matches_played` int(11) DEFAULT 0,
  `last_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`player_id`),
  KEY `team_id` (`team_id`),
  CONSTRAINT `player_stats_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`) ON DELETE CASCADE,
  CONSTRAINT `player_stats_ibfk_2` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `player_stats`
--

LOCK TABLES `player_stats` WRITE;
/*!40000 ALTER TABLE `player_stats` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `player_stats` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `players`
--

DROP TABLE IF EXISTS `players`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `players` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `team_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `squad_number` int(11) DEFAULT NULL,
  `status` enum('active','injured','suspended','unavailable') DEFAULT 'active',
  `is_pool_player` tinyint(1) DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_squad_number_per_team` (`team_id`,`squad_number`),
  UNIQUE KEY `slug` (`slug`),
  KEY `team_id` (`team_id`),
  CONSTRAINT `players_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `players`
--

LOCK TABLES `players` WRITE;
/*!40000 ALTER TABLE `players` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `players` VALUES
(1,1,'Aiden Carrol','aiden-carrol','Goalkeeper',1,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(2,1,'Pedzi','pedzi','Defender',2,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(3,1,'Doza','doza','Defender',3,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(4,1,'Praise Azeez','praise-azeez','Defender',4,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(5,1,'Moiz','moiz','Defender',5,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(6,1,'Uri','uri','Midfielder',6,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(7,1,'Yusuf Isa','yusuf-isa','Midfielder',7,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(8,1,'Favour','favour','Midfielder',8,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(9,1,'Steven','steven','Midfielder',9,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(10,1,'Gaby Goicea','gaby-goicea','Midfielder',10,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(11,1,'Eric','eric','Midfielder',11,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(12,1,'Jake','jake','Midfielder',12,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(13,1,'Rameez Javaid','rameez-javaid','Striker',13,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(14,1,'Fodey Keren','fodey-keren','Striker',14,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(15,2,'Ajay','ajay','Goalkeeper',1,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(16,2,'Stephen','stephen','Defender',2,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(17,2,'Blessing','blessing','Defender',3,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(18,2,'Aiden','aiden','Defender',4,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(19,2,'Murray','murray','Defender',5,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(20,2,'Veron','veron','Midfielder',6,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(21,2,'Panashe Mutazu','panashe-mutazu','Midfielder',7,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(22,2,'Kieran','kieran','Midfielder',8,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(23,2,'Craig','craig','Midfielder',9,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(24,2,'Nathon','nathon','Midfielder',10,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(25,2,'Uche','uche','Striker',11,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(26,2,'Tomiya','tomiya','Striker',12,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(27,2,'Benjamin','benjamin','Striker',13,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(28,2,'Kyi','kyi','Midfielder',14,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(29,2,'Hamid Zahoor','hamid-zahoor','Defender',15,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(30,3,'Sam Gillies','sam-gillies','Defender',1,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(31,3,'Calvin Owusu','calvin-owusu','Defender',2,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(32,3,'Chris Smith','chris-smith','Defender',3,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(33,3,'Daniel Senior','daniel-senior','Defender',4,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(34,3,'Micheal McFadden','micheal-mcfadden','Defender',5,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(35,3,'Aiden Voughn','aiden-voughn','Defender',6,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(36,3,'Samuel Gills','samuel-gills','Midfielder',7,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(37,3,'Andy Beltrami','andy-beltrami','Midfielder',8,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(38,3,'Aaron Kelly','aaron-kelly','Defender',9,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(39,3,'Braiden Govan','braiden-govan','Midfielder',10,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(40,3,'Zak Morrison','zak-morrison','Midfielder',11,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(41,3,'Dominic Henzler','dominic-henzler','Midfielder',12,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(42,3,'Shay McColoch','shay-mccoloch','Striker',13,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(43,3,'Joseph Galagher','joseph-galagher','Striker',14,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(44,3,'Dylan Gennet Oliver','dylan-gennet-oliver','Striker',15,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(45,4,'Hashir Khan','hashir-khan','Goalkeeper',1,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(46,4,'Fraizer','fraizer','Defender',2,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(47,4,'Sherjeel Ramzan','sherjeel-ramzan','Defender',3,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(48,4,'Bav','bav','Defender',4,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(49,4,'Areeb Naeem','areeb-naeem','Defender',5,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(50,4,'Ibraheem Asif','ibraheem-asif','Midfielder',6,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(51,4,'Wasim Arshad','wasim-arshad','Midfielder',7,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(52,4,'Afnaan Majid','afnaan-majid','Midfielder',8,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(53,4,'Adil Ameen','adil-ameen','Defender',9,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(54,4,'Ross Lennox','ross-lennox','Midfielder',10,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(55,4,'Zakir Naeem','zakir-naeem','Midfielder',11,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(56,4,'Taz Ahmed','taz-ahmed','Midfielder',12,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(57,4,'Ben','ben','Midfielder',13,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(58,4,'Adil Ali','adil-ali','Striker',14,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(59,4,'Callan Hay','callan-hay','Striker',15,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(60,5,'Idrees Baqir','idrees-baqir','Goalkeeper',1,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(61,5,'Haroon Saleem','haroon-saleem','Defender',2,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(62,5,'Shaweez Khan','shaweez-khan','Defender',3,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(63,5,'Ahmed Mahmood','ahmed-mahmood','Defender',4,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(64,5,'Hanzla Raheel','hanzla-raheel','Midfielder',5,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(65,5,'Teiyib Ashraf','teiyib-ashraf','Midfielder',6,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(66,5,'Adam Saleem','adam-saleem','Midfielder',7,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(67,5,'Sami Khalid','sami-khalid','Midfielder',8,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(68,5,'Zee Aziz','zee-aziz','Midfielder',9,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(69,5,'Abu Bakir','abu-bakir','Midfielder',10,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(70,5,'Eze','eze','Midfielder',11,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(71,5,'Waqas Amjud','waqas-amjud','Striker',12,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(72,5,'Ibrahim Shauq','ibrahim-shauq','Striker',13,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(73,5,'Ashton','ashton','Striker',14,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(74,6,'Awais','awais','Goalkeeper',1,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(75,6,'David Amayo','david-amayo','Defender',2,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(76,6,'Steg','steg','Defender',3,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(77,6,'Lewis Baird','lewis-baird','Defender',4,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(78,6,'Kaab Shakiel','kaab-shakiel','Defender',5,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(79,6,'Kayden','kayden','Midfielder',6,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(80,6,'Danyal Asgar','danyal-asgar','Midfielder',8,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(81,6,'Josh McBride','josh-mcbride','Midfielder',9,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(82,6,'Keyaan Baqir','keyaan-baqir','Midfielder',10,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(83,6,'Camran Shoaib','camran-shoaib','Midfielder',11,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(84,6,'Aaron','aaron','Midfielder',12,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(85,6,'Godstime Amayo','godstime-amayo','Striker',13,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(86,6,'Craigy','craigy','Striker',14,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(87,6,'Duke','duke','Midfielder',15,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(88,NULL,'Altamash','altamash','Goalkeeper',NULL,'active',1,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(89,NULL,'Hussain Saleem','hussain-saleem','Goalkeeper',NULL,'active',1,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(90,NULL,'Anzar Patel','anzar-patel','Goalkeeper',NULL,'active',1,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(91,NULL,'Robbie Greenshilds','robbie-greenshilds','Goalkeeper',NULL,'active',1,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(92,NULL,'Lee Fleming','lee-fleming','Defender',NULL,'active',1,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(93,NULL,'Zohaib Khalid','zohaib-khalid','Defender',NULL,'active',1,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(94,NULL,'Naveed Shauq','naveed-shauq','Defender',NULL,'active',1,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(95,NULL,'Yusuf Isa','yusuf-isa-2','Defender',NULL,'active',1,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(96,NULL,'Imran Hussain','imran-hussain','Defender',NULL,'active',1,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(97,NULL,'Fahid Javid','fahid-javid','Defender',NULL,'active',1,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(98,NULL,'Hamid Zaheer','hamid-zaheer','Defender',NULL,'active',1,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(99,NULL,'Nesrin','nesrin','Midfielder',NULL,'active',1,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(100,NULL,'Haider Ali','haider-ali','Midfielder',NULL,'active',1,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(101,NULL,'Qadees Ahmed','qadees-ahmed','Midfielder',NULL,'active',1,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(102,NULL,'Aaron Davis','aaron-davis','Midfielder',NULL,'active',1,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(103,NULL,'Mudassar Rafi','mudassar-rafi','Midfielder',NULL,'active',1,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(104,NULL,'Gaby','gaby','Midfielder',NULL,'active',1,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(105,NULL,'Ashton','ashton-2','Striker',NULL,'active',1,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(106,NULL,'Adnan Arif','adnan-arif','Striker',NULL,'active',1,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(107,15,'AIDEN CARROLL','aiden-carroll',NULL,1,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(108,15,'MO ADAM','mo-adam',NULL,2,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(109,15,'LEWIS BAIRD','lewis-baird-2',NULL,3,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(110,15,'SHAWEEZ KHAN','shaweez-khan-2',NULL,4,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(111,15,'ALI FADAAQ','ali-fadaaq',NULL,5,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(112,15,'NABIL AL-HUMAIDI','nabil-al-humaidi',NULL,6,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(113,15,'ZORO AKLABOODI','zoro-aklaboodi',NULL,7,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(114,15,'MALEK AKLABOODI','malek-aklaboodi',NULL,8,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(115,15,'SAMI KHALID','sami-khalid-2',NULL,9,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(116,15,'PENIEL BAKULU','peniel-bakulu',NULL,10,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(117,15,'HUSSAIN AHMED','hussain-ahmed',NULL,11,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(118,15,'NOAH YOUNG','noah-young',NULL,12,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(119,15,'VICTOR EMORDI','victor-emordi',NULL,13,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(120,15,'ADAM SALEEM','adam-saleem-2',NULL,14,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(121,15,'RICHARD TAMBWE','richard-tambwe',NULL,15,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(122,15,'CHERIF','cherif',NULL,16,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(123,15,'DAVID FIADO','david-fiado',NULL,NULL,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(124,16,'ADEEL AHMED','adeel-ahmed',NULL,1,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(125,16,'LEWIS WILSON','lewis-wilson',NULL,2,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(126,16,'HASSAN','hassan',NULL,3,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(127,16,'HANZLA RAHEEL','hanzla-raheel-2',NULL,4,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(128,16,'EZEKIEL','ezekiel',NULL,5,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(129,16,'YUSUF SATTAR','yusuf-sattar',NULL,6,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(130,16,'UTHMAAN','uthmaan',NULL,7,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(131,16,'URIEL EDZII','uriel-edzii',NULL,8,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(132,16,'ISMAIL DAR','ismail-dar',NULL,9,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(133,16,'NABEEL AHMED','nabeel-ahmed',NULL,10,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(134,16,'RAMEEZ JAVED','rameez-javed',NULL,11,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(135,16,'LUKE HAMILTON','luke-hamilton',NULL,12,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(136,16,'ROBBIE BROWN','robbie-brown',NULL,14,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(137,16,'SAIQUE RAHEEL','saique-raheel',NULL,NULL,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(138,17,'YAQUB HUSSAIN','yaqub-hussain',NULL,1,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(139,17,'ABDUSAMEE','abdusamee',NULL,2,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(140,17,'ILLYAS','illyas',NULL,3,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(141,17,'KESS SAJJAD','kess-sajjad',NULL,5,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(142,17,'SHAY','shay',NULL,6,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(143,17,'OSAFO ATUAH','osafo-atuah',NULL,7,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(144,17,'GOHAR AHMED','gohar-ahmed',NULL,8,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(145,17,'SAIF SAJJAD','saif-sajjad',NULL,9,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(146,17,'ARIF AHMED','arif-ahmed',NULL,10,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(147,17,'ALI SHIRE','ali-shire',NULL,11,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(148,17,'ZAIN ASHRAF','zain-ashraf',NULL,12,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(149,17,'MO AWAN','mo-awan',NULL,13,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(150,17,'CHRISTIAN','christian',NULL,14,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(151,18,'JOMI','jomi',NULL,1,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(152,18,'EDWIN','edwin',NULL,2,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(153,18,'EDMUND','edmund',NULL,3,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(154,18,'NATHAN','nathan',NULL,4,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(155,18,'THANDO','thando',NULL,5,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(156,18,'JOEL','joel',NULL,6,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(157,18,'JULES','jules',NULL,7,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(158,18,'CHINO','chino',NULL,8,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(159,18,'CHEMBE','chembe',NULL,9,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(160,18,'NATHEN','nathen',NULL,10,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(161,18,'CHEIK','cheik',NULL,11,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(162,18,'ESROM','esrom',NULL,12,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(163,18,'KEYAAN BAQIR','keyaan-baqir-2',NULL,13,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(164,18,'CAMERON','cameron',NULL,14,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(165,19,'SAKARIA JAFERIA','sakaria-jaferia',NULL,2,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(166,19,'UMAIR IQBAL','umair-iqbal',NULL,3,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(167,19,'MOHAMMED ALKADER','mohammed-alkader',NULL,4,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(168,19,'MOMO SUGUFARA','momo-sugufara',NULL,5,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(169,19,'ALI-AKBAR KACHMAR','ali-akbar-kachmar',NULL,6,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(170,19,'AQIB AHMED','aqib-ahmed',NULL,9,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(171,19,'TALEB GHAZAL','taleb-ghazal',NULL,13,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(172,19,'AMIR GHAZAL','amir-ghazal',NULL,14,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(173,20,'ZAIN','zain',NULL,1,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(174,20,'SALIM AHMED','salim-ahmed',NULL,2,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(175,20,'ALI ALFAILEY','ali-alfailey',NULL,3,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(176,20,'METRO ZAID','metro-zaid',NULL,4,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(177,20,'KEEGAN THOMSON','keegan-thomson',NULL,5,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(178,20,'ZAHIR CHOUDHRY','zahir-choudhry',NULL,6,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(179,20,'HIMI IBRAHIM JAVED','himi-ibrahim-javed',NULL,7,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(180,20,'AYMAN','ayman',NULL,8,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(181,20,'PADDY OKEDION','paddy-okedion',NULL,9,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(182,20,'AMIR CHOUDHRY','amir-choudhry',NULL,10,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(183,20,'ROLAND','roland',NULL,11,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(184,20,'ZAHID CHOUDHRY','zahid-choudhry',NULL,13,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(185,20,'ILIES KRARIA','ilies-kraria',NULL,14,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(186,20,'JUNIOR GWETH','junior-gweth',NULL,15,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(187,20,'ADIL AMEEN','adil-ameen-2',NULL,16,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(188,21,'ROBBIE GREENWOOD','robbie-greenwood',NULL,1,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(189,21,'ZEE AZIZ','zee-aziz-2',NULL,2,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(190,21,'HAYDEN McQUE','hayden-mcque',NULL,3,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(191,21,'KEVIN ONANU','kevin-onanu',NULL,4,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(192,21,'LEWIS CAMPBELL','lewis-campbell',NULL,5,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(193,21,'BRIDELY BROWN','bridely-brown',NULL,6,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(194,21,'CLINTON ONOTU','clinton-onotu',NULL,7,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(195,21,'MAISARA','maisara',NULL,8,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(196,21,'RUAIRIDH HUGHES','ruairidh-hughes',NULL,9,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(197,21,'TARIQ HASSAN','tariq-hassan',NULL,10,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(198,21,'FODAY','foday',NULL,11,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(199,21,'EXAUCE LMBO','exauce-lmbo',NULL,12,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(200,21,'YUSUF ISA','yusuf-isa-3',NULL,13,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(201,21,'FEYSAL','feysal',NULL,14,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(202,21,'BRIDELY BROWN','bridely-brown-2',NULL,15,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(203,21,'MARTIN DESTA','martin-desta',NULL,16,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(204,22,'AKEEL HUSSAIN','akeel-hussain',NULL,1,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(205,22,'ZUBY ZURRETA','zuby-zurreta',NULL,2,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(206,22,'SANA SALAR','sana-salar',NULL,3,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(207,22,'NOAH ABEVI','noah-abevi',NULL,4,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(208,22,'DANYAR HASSAN','danyar-hassan',NULL,5,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(209,22,'SHARO ABDULLA','sharo-abdulla',NULL,6,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(210,22,'BAWAN JAMAL','bawan-jamal',NULL,7,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(211,22,'ALAN NASADI','alan-nasadi',NULL,8,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(212,22,'JUSTO BERNARDO','justo-bernardo',NULL,9,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(213,22,'YUSAF HADI','yusaf-hadi',NULL,10,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(214,22,'SHVAN NOURI','shvan-nouri',NULL,11,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(215,22,'KRIS AMPONSAH','kris-amponsah',NULL,12,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(216,22,'ABDULLAH HEMN','abdullah-hemn',NULL,13,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(217,22,'JAY McFARLINE','jay-mcfarline',NULL,14,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(218,22,'ABDUL NADEEM','abdul-nadeem',NULL,15,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(219,22,'RICHARD OMOBUDE','richard-omobude',NULL,16,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(220,22,'ASIF IQBAL','asif-iqbal',NULL,NULL,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(221,23,'CLERKS BAINS','clerks-bains',NULL,1,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(222,23,'ANIL ATWAL','anil-atwal',NULL,2,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(223,23,'DAVI SINGH','davi-singh',NULL,3,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(224,23,'AMRIT SINGH','amrit-singh',NULL,4,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(225,23,'LANNY HUSSAIN','lanny-hussain',NULL,5,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(226,23,'GURGEET SINGH','gurgeet-singh',NULL,6,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(227,23,'SALMAN RASHID','salman-rashid',NULL,7,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(228,23,'SAMEER KHAN','sameer-khan',NULL,8,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(229,23,'PIASH KHAN','piash-khan',NULL,9,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(230,23,'BALWINDER ATWAL','balwinder-atwal',NULL,10,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(231,23,'RICARDO','ricardo',NULL,11,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(232,23,'KARAN SINGH','karan-singh',NULL,12,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(233,23,'NIHAAL KHAN','nihaal-khan',NULL,13,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(234,23,'AMAN SINGH','aman-singh',NULL,14,'active',0,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(235,NULL,'KAAB SHAKEEL','kaab-shakeel',NULL,NULL,'active',1,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(236,NULL,'ROBBIE GREENSHIELD','robbie-greenshield',NULL,NULL,'active',1,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(237,NULL,'AWAIS NAWAZ','awais-nawaz',NULL,NULL,'active',1,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(238,NULL,'HAMID ZAHOOR','hamid-zahoor-2',NULL,NULL,'active',1,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(239,NULL,'USMAN MUKTHAR','usman-mukthar',NULL,NULL,'active',1,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(240,NULL,'ABDULELAH ALANAZI','abdulelah-alanazi',NULL,NULL,'active',1,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(241,NULL,'MEHRAN BAIG','mehran-baig',NULL,NULL,'active',1,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(242,NULL,'ADAM ALI','adam-ali',NULL,NULL,'active',1,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(243,NULL,'ALI SHEESHA','ali-sheesha',NULL,NULL,'active',1,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(244,NULL,'REHMAN HUSSAIN','rehman-hussain',NULL,NULL,'active',1,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(245,NULL,'BENNY JACKSON','benny-jackson',NULL,NULL,'active',1,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(246,NULL,'RICHARD TAMBWE','richard-tambwe-2',NULL,NULL,'active',1,'2026-02-06 08:06:57','2026-02-06 08:06:57');
/*!40000 ALTER TABLE `players` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `seasons`
--

DROP TABLE IF EXISTS `seasons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `seasons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seasons`
--

LOCK TABLES `seasons` WRITE;
/*!40000 ALTER TABLE `seasons` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `seasons` VALUES
(1,'2025-26','2025-26','2025-09-01','2026-06-30',1,'2026-02-06 08:06:57','2026-02-06 08:06:57');
/*!40000 ALTER TABLE `seasons` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `team_staff`
--

DROP TABLE IF EXISTS `team_staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `team_staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `team_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `role` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `team_id` (`team_id`),
  CONSTRAINT `team_staff_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team_staff`
--

LOCK TABLES `team_staff` WRITE;
/*!40000 ALTER TABLE `team_staff` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `team_staff` VALUES
(1,1,'Jay','referee',NULL,NULL,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(2,1,'Stuart','referee',NULL,NULL,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(3,1,'Zuby Z','referee',NULL,NULL,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(4,1,'Sohail A','referee',NULL,NULL,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(5,1,'Nadeem B','referee',NULL,NULL,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(6,1,'Saique R','referee',NULL,NULL,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(7,1,'Feem Baq','referee',NULL,NULL,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(8,1,'Zoro','referee',NULL,NULL,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(9,1,'Feem Baq','referee',NULL,NULL,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(10,1,'Sohail Ashraf','referee',NULL,NULL,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(11,1,'Nadeem Baqir','referee',NULL,NULL,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(12,1,'Ross','referee',NULL,NULL,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(13,1,'Stuart','referee',NULL,NULL,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(14,1,'Marc','referee',NULL,NULL,'2026-02-06 08:06:57','2026-02-06 08:06:57'),
(15,1,'Gary Coupe','referee',NULL,NULL,'2026-02-06 08:06:57','2026-02-06 08:06:57');
/*!40000 ALTER TABLE `team_staff` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `teams`
--

DROP TABLE IF EXISTS `teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `colour` varchar(7) DEFAULT '#000000',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teams`
--

LOCK TABLES `teams` WRITE;
/*!40000 ALTER TABLE `teams` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `teams` VALUES
(1,'Athletico Pinks','athletico-pinks','#FFC0CB','2026-02-06 08:06:57','2026-02-06 08:06:57'),
(2,'Triple 7 (Black)','triple-7-black','#1C1C1C','2026-02-06 08:06:57','2026-02-06 08:06:57'),
(3,'Bhoys Green','bhoys-green','#228B22','2026-02-06 08:06:57','2026-02-06 08:06:57'),
(4,'Inter Sky Blue','inter-sky-blue','#0066CC','2026-02-06 08:06:57','2026-02-06 08:06:57'),
(5,'Purple Legends','purple-legends','#800080','2026-02-06 08:06:57','2026-02-06 08:06:57'),
(6,'Al Orange','al-orange','#FFA500','2026-02-06 08:06:57','2026-02-06 08:06:57'),
(7,'Yellow / Blue','yellow-blue','#0066CC','2026-02-06 08:06:57','2026-02-06 08:06:57'),
(8,'Purple','purple','#800080','2026-02-06 08:06:57','2026-02-06 08:06:57'),
(9,'White','white','#F5F5F5','2026-02-06 08:06:57','2026-02-06 08:06:57'),
(10,'Yellow','yellow','#FFD700','2026-02-06 08:06:57','2026-02-06 08:06:57'),
(11,'Sky / White (Argentina)','sky-white-argentina','#87CEEB','2026-02-06 08:06:57','2026-02-06 08:06:57'),
(12,'Green (Celtic)','green-celtic','#228B22','2026-02-06 08:06:57','2026-02-06 08:06:57'),
(13,'Black','black','#1C1C1C','2026-02-06 08:06:57','2026-02-06 08:06:57'),
(14,'Red','red','#DC143C','2026-02-06 08:06:57','2026-02-06 08:06:57'),
(15,'GALACTICO - RED','galactico-red','#DC143C','2026-02-06 08:06:57','2026-02-06 08:06:57'),
(16,'ALL STAR - BLACK','all-star-black','#1C1C1C','2026-02-06 08:06:57','2026-02-06 08:06:57'),
(17,'IVOR - GREEN','ivor-green','#228B22','2026-02-06 08:06:57','2026-02-06 08:06:57'),
(18,'TRIPLE 7 BLACKS','triple-7-blacks','#1C1C1C','2026-02-06 08:06:57','2026-02-06 08:06:57'),
(19,'AL AIN WHITES','al-ain-whites','#F5F5F5','2026-02-06 08:06:57','2026-02-06 08:06:57'),
(20,'SPARTA - BLUES','sparta-blues','#0066CC','2026-02-06 08:06:57','2026-02-06 08:06:57'),
(21,'ASTRO PINKS','astro-pinks','#FFC0CB','2026-02-06 08:06:57','2026-02-06 08:06:57'),
(22,'GALAXY - SKY BLUE','galaxy-sky-blue','#0066CC','2026-02-06 08:06:57','2026-02-06 08:06:57'),
(23,'BRAYGOS - YELLOW','braygos-yellow','#FFD700','2026-02-06 08:06:57','2026-02-06 08:06:57');
/*!40000 ALTER TABLE `teams` ENABLE KEYS */;
UNLOCK TABLES;
commit;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-02-06  8:47:41
