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
  `referee` varchar(100) DEFAULT NULL,
  `is_live` tinyint(1) DEFAULT 0,
  `home_score` int(11) DEFAULT NULL,
  `away_score` int(11) DEFAULT NULL,
  `home_scorers` text DEFAULT NULL,
  `away_scorers` text DEFAULT NULL,
  `home_cards` text DEFAULT NULL,
  `away_cards` text DEFAULT NULL,
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
  CONSTRAINT `cup_fixtures_ibfk_1` FOREIGN KEY (`cup_id`) REFERENCES `cups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cup_fixtures_ibfk_2` FOREIGN KEY (`round_id`) REFERENCES `cup_rounds` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cup_fixtures_ibfk_3` FOREIGN KEY (`home_team_id`) REFERENCES `teams` (`id`),
  CONSTRAINT `cup_fixtures_ibfk_4` FOREIGN KEY (`away_team_id`) REFERENCES `teams` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cup_fixtures`
--

LOCK TABLES `cup_fixtures` WRITE;
/*!40000 ALTER TABLE `cup_fixtures` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `cup_fixtures` VALUES
(14,3,7,18,21,'2026-01-31','15:00:00',NULL,NULL,0,0,0,'\"\"','\"\"','\"\"','\"\"',1,1,1,1,5,1,'home','2026-02-02 21:44:23'),
(15,3,7,19,22,'2026-01-31','15:00:00',NULL,NULL,0,3,2,'\"\"','\"\"','\"\"','\"\"',0,NULL,NULL,0,NULL,NULL,'home','2026-02-02 21:44:23'),
(16,3,7,20,24,'2026-01-31','15:00:00',NULL,NULL,0,0,4,'\"\"','\"\"','\"\"','\"\"',0,NULL,NULL,0,NULL,NULL,'away','2026-02-02 21:44:23'),
(17,3,7,25,23,'2026-01-31','15:00:00',NULL,NULL,0,1,4,'\"\"','\"\"','\"\"','\"\"',0,NULL,NULL,0,NULL,NULL,'away','2026-02-02 21:44:23'),
(18,3,8,18,19,'2026-02-13','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,'2026-02-02 21:44:23'),
(19,3,8,24,23,'2026-02-13','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,'2026-02-02 21:44:23'),
(20,3,9,NULL,NULL,'2026-02-20','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,'2026-02-02 21:44:23'),
(38,10,20,19,20,'2026-02-03','19:00:00',NULL,NULL,0,1,0,'\"\"','\"\"','\"\"','\"\"',0,0,0,0,0,0,'home','2026-02-03 18:37:14'),
(39,10,20,18,21,'2026-02-03','19:00:00',NULL,NULL,0,2,1,'\"\"','\"\"','\"\"','\"\"',0,0,0,0,0,0,'home','2026-02-03 18:37:14'),
(40,10,21,19,18,'2026-02-10','19:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,'2026-02-03 18:37:14'),
(44,11,24,24,25,'2026-02-07','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,'2026-02-04 01:53:59'),
(45,11,24,22,23,'2026-02-07','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,'2026-02-04 01:53:59'),
(46,11,25,NULL,NULL,'2026-02-14','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,'2026-02-04 01:53:59');
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
INSERT INTO `cup_rounds` VALUES
(7,3,'Quarter-Final',0),
(8,3,'Semi-Final',1),
(9,3,'Final',2),
(20,10,'Semi-Final',0),
(21,10,'Final',1),
(24,11,'Semi-Final',0),
(25,11,'Final',1);
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
INSERT INTO `cup_teams` VALUES
(3,18),
(3,19),
(3,20),
(3,21),
(3,22),
(3,23),
(3,24),
(3,25),
(10,18),
(10,19),
(10,20),
(10,21),
(11,22),
(11,23),
(11,24),
(11,25);
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
INSERT INTO `cups` VALUES
(3,5,'WFCS Cup','wfcs-cup','2026-02-06','weekly','15:00:00','2026-02-02 21:44:23','2026-02-03 18:43:29'),
(10,5,'Senior Cup','senior-cup','2026-02-03','weekly','19:00:00','2026-02-03 18:37:14','2026-02-03 18:37:14'),
(11,5,'Junior Cup','junior-cup','2026-02-07','weekly','15:00:00','2026-02-04 01:53:59','2026-02-04 01:53:59');
/*!40000 ALTER TABLE `cups` ENABLE KEYS */;
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
  `referee` varchar(100) DEFAULT NULL,
  `is_live` tinyint(1) DEFAULT 0,
  `home_score` int(11) DEFAULT NULL,
  `away_score` int(11) DEFAULT NULL,
  `home_scorers` text DEFAULT NULL,
  `away_scorers` text DEFAULT NULL,
  `home_cards` text DEFAULT NULL,
  `away_cards` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `league_id` (`league_id`),
  KEY `home_team_id` (`home_team_id`),
  KEY `away_team_id` (`away_team_id`),
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
(49,5,18,21,'2026-01-29','21:00:00','1','jim',1,5,4,'[]','[]','{\"sinBins\":[],\"blue\":[],\"yellow\":[],\"red\":[]}','{\"sinBins\":[],\"blue\":[],\"yellow\":[],\"red\":[]}','2026-02-02 21:44:23'),
(50,5,19,20,'2026-01-29','21:00:00','','',0,2,1,'[]','[]','{\"sinBins\":[],\"blue\":[],\"yellow\":[],\"red\":[]}','{\"sinBins\":[],\"blue\":[],\"yellow\":[],\"red\":[]}','2026-02-02 21:44:23'),
(51,5,20,18,'2026-02-05','21:00:00','','',0,0,2,'[]','[]','{\"sinBins\":[],\"blue\":[],\"yellow\":[],\"red\":[]}','{\"sinBins\":[],\"blue\":[],\"yellow\":[],\"red\":[]}','2026-02-02 21:44:23'),
(52,5,21,19,'2026-02-05','21:00:00','','',0,1,1,'[]','[]','{\"sinBins\":[],\"blue\":[],\"yellow\":[],\"red\":[]}','{\"sinBins\":[],\"blue\":[],\"yellow\":[],\"red\":[]}','2026-02-02 21:44:23'),
(53,5,18,19,'2026-02-12','21:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 21:44:23'),
(54,5,20,21,'2026-02-12','21:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 21:44:23'),
(55,5,21,18,'2026-02-26','21:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 21:44:23'),
(56,5,20,19,'2026-02-26','21:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 21:44:23'),
(57,5,18,20,'2026-03-05','21:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 21:44:23'),
(58,5,19,21,'2026-03-05','21:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 21:44:23'),
(59,5,19,18,'2026-03-12','21:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 21:44:23'),
(60,5,21,20,'2026-03-12','21:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 21:44:23'),
(61,6,22,25,'2026-01-30','19:00:00',NULL,NULL,0,2,1,'\"\"','\"\"','\"\"','\"\"','2026-02-02 21:44:23'),
(62,6,23,24,'2026-01-30','19:00:00',NULL,NULL,0,1,1,'\"\"','\"\"','\"\"','\"\"','2026-02-02 21:44:23'),
(63,6,24,22,'2026-02-06','19:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 21:44:23'),
(64,6,25,23,'2026-02-06','19:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 21:44:23'),
(65,6,22,23,'2026-02-13','19:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 21:44:23'),
(66,6,24,25,'2026-02-13','19:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 21:44:23'),
(67,6,25,22,'2026-02-27','19:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 21:44:23'),
(68,6,24,23,'2026-02-27','19:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 21:44:23'),
(69,6,22,24,'2026-03-06','19:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 21:44:23'),
(70,6,23,25,'2026-03-06','19:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 21:44:23'),
(71,6,23,22,'2026-03-13','19:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 21:44:23'),
(72,6,25,24,'2026-03-13','19:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 21:44:23'),
(74,5,18,19,'2026-02-15','14:30:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 21:44:58'),
(75,5,18,19,'2026-03-01','16:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 21:45:35'),
(76,5,18,19,'2026-04-01','18:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-02 21:45:50'),
(245,7,18,25,'2026-02-07','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(246,7,19,24,'2026-02-07','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(247,7,20,23,'2026-02-07','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(248,7,21,22,'2026-02-07','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(249,7,24,18,'2026-02-14','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(250,7,25,23,'2026-02-14','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(251,7,19,22,'2026-02-14','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(252,7,20,21,'2026-02-14','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(253,7,18,23,'2026-02-21','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(254,7,24,22,'2026-02-21','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(255,7,25,21,'2026-02-21','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(256,7,19,20,'2026-02-21','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(257,7,22,18,'2026-02-28','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(258,7,23,21,'2026-02-28','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(259,7,24,20,'2026-02-28','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(260,7,25,19,'2026-02-28','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(261,7,18,21,'2026-03-07','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(262,7,22,20,'2026-03-07','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(263,7,23,19,'2026-03-07','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(264,7,24,25,'2026-03-07','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(265,7,20,18,'2026-03-14','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(266,7,21,19,'2026-03-14','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(267,7,22,25,'2026-03-14','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(268,7,23,24,'2026-03-14','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(269,7,18,19,'2026-03-21','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(270,7,20,25,'2026-03-21','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(271,7,21,24,'2026-03-21','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(272,7,22,23,'2026-03-21','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(273,7,25,18,'2026-04-04','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(274,7,24,19,'2026-04-04','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(275,7,23,20,'2026-04-04','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(276,7,22,21,'2026-04-04','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(277,7,18,24,'2026-04-11','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(278,7,23,25,'2026-04-11','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(279,7,22,19,'2026-04-11','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(280,7,21,20,'2026-04-11','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(281,7,23,18,'2026-04-18','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(282,7,22,24,'2026-04-18','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(283,7,21,25,'2026-04-18','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(284,7,20,19,'2026-04-18','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(285,7,18,22,'2026-04-25','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(286,7,21,23,'2026-04-25','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(287,7,20,24,'2026-04-25','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(288,7,19,25,'2026-04-25','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(289,7,21,18,'2026-05-02','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(290,7,20,22,'2026-05-02','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(291,7,19,23,'2026-05-02','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(292,7,25,24,'2026-05-02','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(293,7,18,20,'2026-05-09','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(294,7,19,21,'2026-05-09','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(295,7,25,22,'2026-05-09','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(296,7,24,23,'2026-05-09','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(297,7,19,18,'2026-05-16','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(298,7,25,20,'2026-05-16','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(299,7,24,21,'2026-05-16','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36'),
(300,7,23,22,'2026-05-16','15:00:00',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-04 02:28:36');
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
(5,18),
(5,19),
(5,20),
(5,21),
(6,22),
(6,23),
(6,24),
(6,25),
(7,18),
(7,19),
(7,20),
(7,21),
(7,22),
(7,23),
(7,24),
(7,25);
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
(5,5,'Oldies','oldies','2026-01-29','weekly','21:00:00','2026-02-02 21:44:23','2026-02-02 21:44:23'),
(6,5,'Under 16\'s','under-16s','2026-01-30','weekly','19:00:00','2026-02-02 21:44:23','2026-02-02 21:44:23'),
(7,5,'Mixed','mixed','2026-02-07','weekly','15:00:00','2026-02-04 02:26:06','2026-02-04 02:28:36');
/*!40000 ALTER TABLE `leagues` ENABLE KEYS */;
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
  `team_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
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
(107,18,'Pavel Srnicek'),
(108,18,'Shaka Hislop'),
(109,18,'Warren Barton'),
(110,18,'John Beresford'),
(111,18,'Darren Peacock'),
(112,18,'Steve Howey'),
(113,18,'Steve Watson'),
(114,18,'Robbie Elliott'),
(115,18,'Philippe Albert'),
(116,18,'Rob Lee'),
(117,18,'Lee Clark'),
(118,18,'David Ginola'),
(119,18,'Keith Gillespie'),
(120,18,'Peter Beardsley'),
(121,18,'Les Ferdinand'),
(122,18,'Faustino Asprilla'),
(123,18,'Paul Kitson');
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
(5,'2025/26','202526','2026-01-29','2026-05-31',1,'2026-02-02 21:44:23','2026-02-03 22:44:05'),
(6,'2026/27','202627','2026-09-01','2027-05-31',0,'2026-02-03 23:23:13','2026-02-03 23:23:13');
/*!40000 ALTER TABLE `seasons` ENABLE KEYS */;
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
  `contact` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
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
(18,'The Entertainers','the-entertainers','Kevin Keegan','07700111222','email@emample.com','#241f20','2026-02-02 21:44:23','2026-02-03 18:08:09'),
(19,'Oldies 2','oldies-2','','','','#1a5f2a','2026-02-02 21:44:23','2026-02-03 18:39:16'),
(20,'Oldies 3','oldies-3','',NULL,'','#c6c13f','2026-02-02 21:44:23','2026-02-02 21:44:23'),
(21,'Oldies 4','oldies-4','','','','#dc143c','2026-02-02 21:44:23','2026-02-03 18:39:34'),
(22,'Young Team 1','young-team-1','',NULL,'','#1a5f2a','2026-02-02 21:44:23','2026-02-02 21:44:23'),
(23,'Young Team 2','young-team-2','','','','#ff6600','2026-02-02 21:44:23','2026-02-03 18:40:07'),
(24,'Young Team 3','young-team-3','','','','#6cabdd','2026-02-02 21:44:23','2026-02-03 18:40:31'),
(25,'Young Team 4','young-team-4','','','','#dd0000','2026-02-02 21:44:23','2026-02-03 18:41:08');
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

-- Dump completed on 2026-02-05 22:39:05
