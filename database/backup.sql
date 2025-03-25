-- MySQL dump 10.13  Distrib 8.0.39, for Win64 (x86_64)
--
-- Host: localhost    Database: discord_bot
-- ------------------------------------------------------
-- Server version	8.0.39

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `channels`
--

DROP TABLE IF EXISTS `channels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `channels` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dchannel_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `channel_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `channels_dchannel_id_unique` (`dchannel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `channels`
--

LOCK TABLES `channels` WRITE;
/*!40000 ALTER TABLE `channels` DISABLE KEYS */;
INSERT INTO `channels` VALUES (1,'1311410753965920367','runs',NULL,NULL),(3,'1350385462082273280','test',NULL,NULL);
/*!40000 ALTER TABLE `channels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_03_15_075955_create_channels_table',1),(8,'2025_03_15_103135_create_runs_table',2),(9,'2025_03_16_182509_add_pot_to_runs_table',3),(10,'2025_03_16_205508_add_pay_user_to_runs',4);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `runs`
--

DROP TABLE IF EXISTS `runs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `runs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `count` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dungeons` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `boosters` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `boosters_count` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pot` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adv` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pay_user` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `paid` tinyint NOT NULL DEFAULT '0',
  `depleted` tinyint NOT NULL DEFAULT '0',
  `user_id` bigint unsigned DEFAULT NULL,
  `channel_id` bigint unsigned DEFAULT NULL,
  `dmessage_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dmessage_link` text COLLATE utf8mb4_unicode_ci,
  `paid_at` datetime DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `runs_user_id_foreign` (`user_id`),
  KEY `runs_channel_id_foreign` (`channel_id`),
  CONSTRAINT `runs_channel_id_foreign` FOREIGN KEY (`channel_id`) REFERENCES `channels` (`id`),
  CONSTRAINT `runs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `runs`
--

LOCK TABLES `runs` WRITE;
/*!40000 ALTER TABLE `runs` DISABLE KEYS */;
INSERT INTO `runs` VALUES (4,'1','10','TOP','[\"mmdraven\",\"amirparse\",\"funn3r\",\"mhnhero(rouge)\"]','4','200','800','T','madshamy',NULL,'',0,0,6,1,'1350791215209250908','https://discord.com/channels/878241085535715380/1311410753965920367/1350791215209250908',NULL,'**1×10 Madshamy**\n\n**Run ID**: 4\n**Date**: 2025-03-16 14:52:42\n**Pot**: 200T\n**Advertiser**: Madshamy\n**Dungeons**: FG\n\n**Boosters**\nMmdraven\nAmirparse\nFunn3r\nMhnhero(rouge)\n','2025-03-16 11:22:42','2025-03-17 11:07:14','2025-03-17 11:07:14'),(5,'1','10','CB','[\"mmdraven\",\"ccain\",\"samann12\",\"amirparse\"]','4','200','800','T','madshamy',NULL,'',0,0,6,1,'1350798434227195998','https://discord.com/channels/878241085535715380/1311410753965920367/1350798434227195998',NULL,'**1×10 Madshamy**\n\n**Run ID**: 5\n**Date**: 2025-03-16 15:21:23\n**Pot**: 200T\n**Advertiser**: Madshamy\n**Dungeons**: CB\n\n**Boosters**\nMmdraven\nCcain\nSamann12\n','2025-03-16 11:51:23','2025-03-17 11:09:05','2025-03-17 11:09:05'),(6,'1','11','TOP','[\"amirparse\",\"amirparse\",\"gargoxqt\",\"funn3r\"]','4','250','1000','t','Saintelf','funn3r','',1,0,6,1,'1350857543714341024','https://discord.com/channels/878241085535715380/1311410753965920367/1350857543714341024','2025-03-21 12:13:52','**1×11 Saintelf**\n\n**Run ID**: 6\n**Date**: 2025-03-16 19:16:16\n**Pot**: T\n**Cut**: 250T\n**Advertiser**: Saintelf\n**Dungeons**: TOP\n\n**Boosters**\nAmirparse\nAmirparse\nGargoxqt\nFunner\n','2025-03-16 15:46:16','2025-03-21 08:43:52',NULL),(7,'1','11','TOP','[\"amirparse\",\"extro\",\"funn3r\",\"scott9720\"]','4','25.00','100','K','Crazy','funn3r',NULL,1,0,3,1,'1350866230772371568','https://discord.com/channels/878241085535715380/1311410753965920367/1350866230772371568','2025-03-22 20:28:15','**1×11 Crazy**\n\n**Run ID**: 7\n**Date**: 2025-03-16 19:50:48\n**Pot**: 400K\n**Cut**: 100K\n**Advertiser**: Crazy\n**Dungeons**: TOP\n\n**Boosters**\nAmirparse\nExtro\nFunn3r\nScott9720\n','2025-03-16 16:20:48','2025-03-22 16:58:15',NULL),(10,'1','11','FG','[\"amirparse\",\"masihyat\",\"funn3r\",\"original221\"]','4','87.5','350','K','xappl3','funn3r','',1,0,6,1,'1350894065574739969','https://discord.com/channels/878241085535715380/1311410753965920367/1350894065574739969','2025-03-22 20:28:21','**1×11 Xappl3**\n\n**Run ID**: 10\n**Date**: 2025-03-16 21:41:23\n**Pot**: 350K\n**Cut**: 87.5K\n**Advertiser**: Xappl3\n**Dungeons**: FG\n\n**Boosters**\nAmirparse\nMasihyat\nFunn3r\nOriginal221\n','2025-03-16 18:11:23','2025-03-22 16:58:21',NULL),(11,'1','10','DFC','[\"amirparse\",\"artorias\",\"funn3r\",\"romance1993\"]','4','70','280','K','amirpolice','funn3r','',1,0,6,1,'1350923918113898546','https://discord.com/channels/878241085535715380/1311410753965920367/1350923918113898546','2025-03-22 20:28:27','**1×10 Amirpolice**\n\n**Run ID**: 11\n**Date**: 2025-03-16 23:40:00\n**Pot**: 280K\n**Cut**: 70K\n**Advertiser**: Amirpolice\n**Dungeons**: DFC\n\n**Boosters**\nAmirparse\nArtorias\nFunn3r\nRomance1993\n','2025-03-16 20:10:00','2025-03-22 16:58:27',NULL),(15,'1','10','CB','[\"mmdraven\",\"mmdraven\",\"funn3r\",\"extro\"]','4','70','280','K','mmdpanda','funn3r','give cut from FuNn3R',1,0,3,1,'1351149511631699978','https://discord.com/channels/878241085535715380/1311410753965920367/1351149511631699978','2025-03-18 19:17:57','**1×10 Mmdpanda**\n\n**Run ID**: 15\n**Date**: 2025-03-17 14:36:19\n**Pot**: 280K\n**Cut**: 70K\n**Advertiser**: Mmdpanda\n**Dungeons**: CB\n\n**Boosters**\nMmdraven\nMmdraven\nFunn3r\nExtro\n\n**Note**: give cut from FuNn3R','2025-03-17 11:06:19','2025-03-18 15:47:57',NULL),(30,'1','10','PSF','[\"mmdraven\",\"amirparse\",\"funn3r\",\"extro\"]','4','180','720','t','Boostaminophen','funn3r',NULL,1,0,6,1,'1351246099775492147','https://discord.com/channels/878241085535715380/1311410753965920367/1351246099775492147','2025-03-21 12:14:10','**1×10 Boostaminophen**\n\n**Run ID**: 30\n**Date**: 2025-03-17 21:00:11\n**Pot**: 720T\n**Cut**: 180T\n**Advertiser**: Boostaminophen\n**Dungeons**: PSF\n\n**Boosters**\nMmdraven\nAmirparse\nFunn3r\nExtro\n','2025-03-17 17:30:11','2025-03-21 08:44:10',NULL),(31,'1','10','FG','[\"mmdraven\",\"amirparse\",\"funn3r\",\"minichua(paladin)\"]','4','180','720','T','Boostaminophen','funn3r',NULL,1,0,6,1,'1351246540055777312','https://discord.com/channels/878241085535715380/1311410753965920367/1351246540055777312','2025-03-21 12:14:16','**1×10 Boostaminophen**\n\n**Run ID**: 31\n**Date**: 2025-03-17 21:02:03\n**Pot**: 720T\n**Cut**: 180T\n**Advertiser**: Boostaminophen\n**Dungeons**: FG\n\n**Boosters**\nMmdraven\nAmirparse\nFunn3r\nMinichua(paladin)\n','2025-03-17 17:32:03','2025-03-21 08:44:16',NULL),(32,'1','11','DFC','[\"amirparse\",\"funn3r\",\"paladin\",\"paladin\"]','4','70','280','k','amirpolice',NULL,NULL,0,0,6,1,'1351254638413742121','https://discord.com/channels/878241085535715380/1311410753965920367/1351254638413742121',NULL,'**1×11 Amirpolice**\n\n**Run ID**: 32\n**Date**: 2025-03-17 21:34:14\n**Pot**: 280K\n**Cut**: 70K\n**Advertiser**: Amirpolice\n**Dungeons**: DFC\n\n**Boosters**\nAmirparse\nFunn3r\nPaladin\nPaladin\n','2025-03-17 18:04:14','2025-03-22 16:59:43','2025-03-22 16:59:43'),(33,'1','12','CB','[\"mmdraven\",\"amirparse\",\"extro\",\"funn3r\"]','4','275','1100','t','madshamy','funn3r',NULL,1,0,6,1,'1351255042862223380','https://discord.com/channels/878241085535715380/1311410753965920367/1351255042862223380','2025-03-22 20:26:47','**1×12 Madshamy**\n\n**Run ID**: 33\n**Date**: 2025-03-17 21:35:50\n**Pot**: 1100T\n**Cut**: 275T\n**Advertiser**: Madshamy\n**Dungeons**: CB\n\n**Boosters**\nMmdraven\nAmirparse\nExtro\nFunn3r\n','2025-03-17 18:05:50','2025-03-22 16:56:47',NULL),(34,'1','12','TOP','[\"mmdraven\",\"amirparse\",\"funn3r\",\"legendary\"]','4','275','1100','t','madshamy','funn3r',NULL,1,0,6,1,'1351283102571430032','https://discord.com/channels/878241085535715380/1311410753965920367/1351283102571430032','2025-03-22 20:26:53','**1×12 Madshamy**\n\n**Run ID**: 34\n**Date**: 2025-03-17 23:27:20\n**Pot**: 1100T\n**Cut**: 275T\n**Advertiser**: Madshamy\n**Dungeons**: TOP\n\n**Boosters**\nMmdraven\nAmirparse\nFunn3r\nLegendary\n','2025-03-17 19:57:20','2025-03-22 16:56:53',NULL),(35,'2','12','ML-PSF','[\"Funn3r\",\"mmdraven\",\"Amirparse\",\"Extro\"]','4','550','2200','t','Madshamy','kallagh','Cut from mmdraven',1,0,7,1,'1351310676244693034','https://discord.com/channels/878241085535715380/1311410753965920367/1351310676244693034','2025-03-22 19:29:21','**2×12 Madshamy**\n\n**Run ID**: 35\n**Date**: 2025-03-18 01:16:51\n**Pot**: 2200\n**Cut**: 550\n**Advertiser**: Madshamy\n**Dungeons**: ML-PSF\n\n**Boosters**\nFunn3r\nMmdraven\nAmirparse\nExtro\n\n**Note**: Cut from mmdraven','2025-03-17 21:46:51','2025-03-22 15:59:21',NULL),(36,'1','10','CB','[\"mmdraven\",\"amirparse\",\"funn3r\"]','3','158.33','475','t','GodofWar',NULL,NULL,0,0,6,1,'1351572044373823508','https://discord.com/channels/878241085535715380/1311410753965920367/1351572044373823508',NULL,'**1×10 GodofWar**\n\n**Run ID**: 36\n**Date**: 2025-03-18 18:35:27\n**Pot**: 475T\n**Cut**: 158.33333333333T\n**Advertiser**: GodofWar\n**Dungeons**: CB\n\n**Boosters**\nMmdraven\nAmirparse\nFunn3r\n','2025-03-18 15:05:27','2025-03-18 15:05:36',NULL),(37,'1','10','Rook','[\"mmdraven\",\"amirparse\",\"funn3r\",\"guts\"]','4','175.00','700','t','madshamy','funn3r',NULL,1,0,6,1,'1351581639683346452','https://discord.com/channels/878241085535715380/1311410753965920367/1351581639683346452','2025-03-22 20:27:39','**1×10 Madshamy**\n\n**Run ID**: 37\n**Date**: 2025-03-18 19:13:32\n**Pot**: 700T\n**Cut**: 175.00T\n**Advertiser**: Madshamy\n**Dungeons**: ROOK\n\n**Boosters**\nMmdraven\nAmirparse\nFunn3r\nGuts\n','2025-03-18 15:43:32','2025-03-22 16:57:39',NULL),(38,'1','10','DFC','[\"amirparse\",\"mmdraven\",\"funn3r\",\"funn3r\"]','4','65.00','260','k','Amir_Curseflame','kallagh','cut from mmdraven',1,0,7,1,'1351624039919255602','https://discord.com/channels/878241085535715380/1311410753965920367/1351624039919255602','2025-03-19 23:29:57','**1×10 Amir_Curseflame**\n\n**Run ID**: 38\n**Date**: 2025-03-18 22:02:07\n**Pot**: 260K\n**Cut**: 65.00K\n**Advertiser**: Amir_Curseflame\n**Dungeons**: DFC\n\n**Boosters**\nAmirparse\nMmdraven\nFunn3r\nArefzare\n\n**Note**: cut from mmdraven','2025-03-18 18:32:07','2025-03-19 19:59:57',NULL),(39,'1','10','ML','[\"funn3r\",\"funn3r\",\"amirparse\",\"mmdraven\"]','4','60.00','240','k','Xapple','kallagh','Cut from mmdraven',1,0,7,1,'1351635846830227650','https://discord.com/channels/878241085535715380/1311410753965920367/1351635846830227650','2025-03-19 23:30:05','**1×10 Xapple**\n\n**Run ID**: 39\n**Date**: 2025-03-18 22:49:02\n**Pot**: 240\n**Cut**: 60.00\n**Advertiser**: Xapple\n**Dungeons**: ML\n\n**Boosters**\nAmirparse\nMmdraven\nFunn3r\nMojtaba.m\n\n**Note**: Cut from mmdraven','2025-03-18 19:19:02','2025-03-19 20:00:05',NULL),(40,'3','10','CB-DFC-ML','[\"amirparse\",\"mmdraven\",\"funner\"]','3','225.00','675','k','Aegon','funn3r','',1,0,6,1,'1351636978256445480','https://discord.com/channels/878241085535715380/1311410753965920367/1351636978256445480','2025-03-22 20:28:48','**3×10 Aegon**\n\n**Run ID**: 40\n**Date**: 2025-03-18 22:53:32\n**Pot**: 675K\n**Cut**: 225.00K\n**Advertiser**: Aegon\n**Dungeons**: CB-DFC-ML\n\n**Boosters**\nAmirparse\nRaven\nFunner\n','2025-03-18 19:23:32','2025-03-22 16:58:48',NULL),(41,'1','10','ROOK','[\"amirparse\",\"Mmdraven\",\"funner\",\"guts\"]','4','56.25','225','k','Aegon','funn3r','',1,0,6,1,'1351637237556711519','https://discord.com/channels/878241085535715380/1311410753965920367/1351637237556711519','2025-03-22 20:28:42','**1×10 Aegon**\n\n**Run ID**: 41\n**Date**: 2025-03-18 22:54:34\n**Pot**: 225K\n**Cut**: 56.25K\n**Advertiser**: Aegon\n**Dungeons**: ROOK\n\n**Boosters**\nAmirparse\nMmdraven\nFunner\nGuts\n','2025-03-18 19:24:34','2025-03-22 16:58:42',NULL),(42,'1','10','CB','[\"mmdraven\",\"amirparse\",\"funn3r\",\"funn3r\"]','4','175.00','700','t','kourosh2548','funn3r','by funn3r',1,0,3,1,'1351663622757159065','https://discord.com/channels/878241085535715380/1311410753965920367/1351663622757159065','2025-03-19 00:44:37','**1×10 Kourosh2548**\n\n**Run ID**: 42\n**Date**: 2025-03-19 00:39:20\n**Pot**: 700T\n**Cut**: 175.00T\n**Advertiser**: Kourosh2548\n**Dungeons**: DB\n\n**Boosters**\nMmdraven\nAmirparse\nFunn3r\nFunn3r\n\n**Note**: by funn3r','2025-03-18 21:09:20','2025-03-18 21:14:37',NULL),(43,'1','10','ML','[\"amirparse\",\"mmdraven\",\"funn3r\",\"percivalw_92611\"]','4','212.50','850','T','Godofwar',NULL,NULL,0,0,6,1,'1351692614407295027','https://discord.com/channels/878241085535715380/1311410753965920367/1351692614407295027',NULL,'**1×10 Godofwar**\n\n**Run ID**: 43\n**Date**: 2025-03-19 02:34:37\n**Pot**: 850\n**Cut**: 212.50\n**Advertiser**: Godofwar\n**Dungeons**: DFC\n\n**Boosters**\nAmirparse\nMmdraven\nFunn3r\nPercivalw_92611\n','2025-03-18 23:04:37','2025-03-18 23:04:43',NULL),(44,'1','10','Flood','[\"amirparse\",\"mmdraven\",\"funn3r\",\"afshin8445\"]','4','212.50','850','t','Godofwar',NULL,NULL,0,0,6,1,'1351692761207672832','https://discord.com/channels/878241085535715380/1311410753965920367/1351692761207672832',NULL,'**1×10 Godofwar**\n\n**Run ID**: 44\n**Date**: 2025-03-19 02:35:11\n**Pot**: 850\n**Cut**: 212.50\n**Advertiser**: Godofwar\n**Dungeons**: FLOOD\n\n**Boosters**\nAmirparse\nMmdraven\nFunn3r\nAfshin8445\n','2025-03-18 23:05:11','2025-03-18 23:05:18',NULL),(45,'1','10','FG','[\"amirparse\",\"funn3r\",\"mmdraven\"]','3','283.33','850','T','GodofWar',NULL,NULL,0,0,3,1,'1351702767382564894','https://discord.com/channels/878241085535715380/1311410753965920367/1351702767382564894',NULL,'**10×1 GodofWar**\n\n**Run ID**: 45\n**Date**: 2025-03-19 03:14:57\n**Pot**: 850T\n**Cut**: 283.33T\n**Advertiser**: GodofWar\n**Dungeons**: FG\n\n**Boosters**\nAmirparse\nFunn3r\nMmdraven\n','2025-03-18 23:44:57','2025-03-22 16:15:07',NULL),(46,'1','12','PSF','[\"amirparse\",\"mmdraven\",\"funn3r\",\"thethinker\"]','4','250.00','1000','t','madsham','funn3r',NULL,1,0,6,1,'1352018513702682785','https://discord.com/channels/878241085535715380/1311410753965920367/1352018513702682785','2025-03-22 20:28:35','**1×12 Madsham**\n\n**Run ID**: 46\n**Date**: 2025-03-20 00:09:37\n**Pot**: 1000T\n**Cut**: 250.00T\n**Advertiser**: Madsham\n**Dungeons**: PSF\n\n**Boosters**\nAmirparse\nMmdraven\nFunn3r\nThethinker\n','2025-03-19 20:39:37','2025-03-22 16:58:35',NULL),(47,'1','8','OLD','[\"amirparse\",\"mmdraven\",\"silent\",\"invisible4469\"]','4','425.00','1700','t','Godofwar',NULL,'',0,0,6,1,'1353051925251096732','https://discord.com/channels/878241085535715380/1311410753965920367/1353051925251096732',NULL,'**1×8 Godofwar**\n\n**Run ID**: 47\n**Date**: 2025-03-22 20:36:12\n**Pot**: 1700T\n**Cut**: 425.00T\n**Advertiser**: Godofwar\n**Dungeons**: OLD\n\n**Boosters**\nAmirparse\nMmdraven\nSilent\nInvisible4469\n','2025-03-22 17:06:12','2025-03-22 17:06:18',NULL),(48,'1','8','OLD','[\"amirparse\",\"mmdraven\",\"funner\",\"effect4502\"]','4','425.00','1700','t','Godofwar',NULL,'',0,0,6,1,'1353052204097081426','https://discord.com/channels/878241085535715380/1311410753965920367/1353052204097081426',NULL,'**1×8 Godofwar**\n\n**Run ID**: 48\n**Date**: 2025-03-22 20:37:19\n**Pot**: 1700T\n**Cut**: 425.00T\n**Advertiser**: Godofwar\n**Dungeons**: OLD\n\n**Boosters**\nAmirparse\nMmdraven\nFunner\nEffect4502\n','2025-03-22 17:07:19','2025-03-22 17:07:25',NULL),(49,'1','8','OLD','[\"amirparse\",\"mmdraven\",\"funner\",\"Shoaib\"]','4','300.00','1200','t','Goodofwar',NULL,'',0,0,6,1,'1353053147844710574','https://discord.com/channels/878241085535715380/1311410753965920367/1353053147844710574',NULL,'**1×8 Goodofwar**\n\n**Run ID**: 49\n**Date**: 2025-03-22 20:41:04\n**Pot**: 1200T\n**Cut**: 300.00T\n**Advertiser**: Goodofwar\n**Dungeons**: OLD\n\n**Boosters**\nAmirparse\nMmdraven\nFunner\nShoaib\n','2025-03-22 17:11:04','2025-03-22 17:11:10',NULL),(50,'1','10','OLD','[\"amirparse\",\"mmdraven\",\"funner\",\"amirreza\"]','4','450.00','1800','t','Godofwar',NULL,'',0,0,6,1,'1353053242371739840','https://discord.com/channels/878241085535715380/1311410753965920367/1353053242371739840',NULL,'**1×10 Godofwar**\n\n**Run ID**: 50\n**Date**: 2025-03-22 20:41:25\n**Pot**: 1800T\n**Cut**: 450.00T\n**Advertiser**: Godofwar\n**Dungeons**: OLD\n\n**Boosters**\nAmirparse\nMmdraven\nFunner\nAmirreza\n','2025-03-22 17:11:25','2025-03-22 17:11:32',NULL),(51,'1','10','OLD','[\"amirparse\",\"mmdraven\",\"funner\",\"axispirit\"]','4','400.00','1600','t','Godofwar',NULL,'',0,0,6,1,'1353053340933689374','https://discord.com/channels/878241085535715380/1311410753965920367/1353053340933689374',NULL,'**1×10 Godofwar**\n\n**Run ID**: 51\n**Date**: 2025-03-22 20:41:50\n**Pot**: 1600T\n**Cut**: 400.00T\n**Advertiser**: Godofwar\n**Dungeons**: OLD\n\n**Boosters**\nAmirparse\nMmdraven\nFunner\nAxispirit\n','2025-03-22 17:11:50','2025-03-22 17:11:56',NULL),(52,'1','10','CB','[\"amirparse\",\"raven\",\"funner\",\"deadboy47\"]','4','262.50','1050','t','FTG',NULL,'',0,0,6,1,'1353053629115924621','https://discord.com/channels/878241085535715380/1311410753965920367/1353053629115924621',NULL,'**1×10 FTG**\n\n**Run ID**: 52\n**Date**: 2025-03-22 20:42:59\n**Pot**: 1050T\n**Cut**: 262.50T\n**Advertiser**: FTG\n**Dungeons**: CB\n\n**Boosters**\nAmirparse\nRaven\nFunner\nDeadboy47\n','2025-03-22 17:12:59','2025-03-22 17:13:04',NULL),(53,'1','12','FLOOD','[\"amirparse\",\"raven\",\"thegilimish\",\"guts\"]','4','250.00','1000','t','Aegon',NULL,'',0,0,6,1,'1353053876894564522','https://discord.com/channels/878241085535715380/1311410753965920367/1353053876894564522',NULL,'**1×12 Aegon**\n\n**Run ID**: 53\n**Date**: 2025-03-22 20:43:58\n**Pot**: 1000T\n**Cut**: 250.00T\n**Advertiser**: Aegon\n**Dungeons**: FLOOD\n\n**Boosters**\nAmirparse\nRaven\nThegilimish\nGuts\n','2025-03-22 17:13:58','2025-03-22 17:14:03',NULL),(54,'1','13','FLOOD','[\"amirparse\",\"raven\",\"guts\",\"general\"]','4','200.00','800','k','Crazy',NULL,'',0,0,6,1,'1353053995136057354','https://discord.com/channels/878241085535715380/1311410753965920367/1353053995136057354',NULL,'**1×13 Crazy**\n\n**Run ID**: 54\n**Date**: 2025-03-22 20:44:26\n**Pot**: 800K\n**Cut**: 200.00K\n**Advertiser**: Crazy\n**Dungeons**: FLOOD\n\n**Boosters**\nAmirparse\nRaven\nGuts\nGeneral\n','2025-03-22 17:14:26','2025-03-22 17:14:32',NULL);
/*!40000 ALTER TABLE `runs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('2FGocX3w4Z5tvPLAwQHgFlwTmsWmBI3Be8uymZ2P',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVWxObFZQMVJOUW5VRzRaRUd0Unl5S0doa0JwT0txbW5WYTZvOHIySiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1742721414);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duser_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_duser_id_unique` (`duser_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (3,'FuNn3R','funn3r','358758841850265610','2025-03-15 10:53:11','2025-03-15 10:53:11'),(6,'AmirParse','amirparse','470470501849104384','2025-03-16 11:14:24','2025-03-16 11:14:24'),(7,'mmdraven','kallagh','573475505169367050','2025-03-16 11:15:23','2025-03-16 11:15:23');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-03-25 15:34:39
