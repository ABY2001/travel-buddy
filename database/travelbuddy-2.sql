-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: localhost    Database: travel_buddy
-- ------------------------------------------------------
-- Server version	8.0.40

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `connection_requests`
--

DROP TABLE IF EXISTS `connection_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `connection_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sender_id` int NOT NULL,
  `receiver_id` int NOT NULL,
  `status` enum('pending','accepted','rejected') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `sender_id` (`sender_id`),
  KEY `receiver_id` (`receiver_id`),
  CONSTRAINT `connection_requests_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `connection_requests_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `connection_requests`
--

LOCK TABLES `connection_requests` WRITE;
/*!40000 ALTER TABLE `connection_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `connection_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `group_trips`
--

DROP TABLE IF EXISTS `group_trips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `group_trips` (
  `id` int NOT NULL AUTO_INCREMENT,
  `destination` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `travel_date` date NOT NULL,
  `gender_preference` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `total_members` int NOT NULL,
  `created_by` int NOT NULL,
  `budget` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `group_trips_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_trips`
--

LOCK TABLES `group_trips` WRITE;
/*!40000 ALTER TABLE `group_trips` DISABLE KEYS */;
INSERT INTO `group_trips` VALUES (1,'Jammu','2025-05-01','any',4,7,1000),(2,'punjab','2025-05-01','female',4,7,1000),(3,'Haryana','2025-04-25','female',4,7,1000),(4,'Haryana','2025-04-17','female',4,7,1000),(5,'pakistan','2025-04-22','female',4,7,1000);
/*!40000 ALTER TABLE `group_trips` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `solo_trips`
--

DROP TABLE IF EXISTS `solo_trips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `solo_trips` (
  `id` int NOT NULL AUTO_INCREMENT,
  `destination` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `travel_date` date NOT NULL,
  `budget` decimal(10,2) NOT NULL,
  `gender_preference` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int NOT NULL,
  `buddy_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `buddy_id` (`buddy_id`),
  CONSTRAINT `solo_trips_ibfk_1` FOREIGN KEY (`buddy_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `solo_trips`
--

LOCK TABLES `solo_trips` WRITE;
/*!40000 ALTER TABLE `solo_trips` DISABLE KEYS */;
INSERT INTO `solo_trips` VALUES (1,'test','2025-04-03',200.00,'any','2025-03-30 15:54:38',0,NULL),(2,'test','2025-04-03',200.00,'any','2025-03-30 15:55:08',0,NULL),(3,'aby','2025-04-03',1000.00,'male','2025-03-30 17:51:00',0,NULL),(4,'Kerala','2025-04-04',2222.00,'female','2025-03-30 17:56:26',0,NULL),(5,'TN','2025-04-10',2222.00,'female','2025-03-30 18:01:46',0,NULL),(6,'KA','2025-04-04',900.00,'female','2025-03-30 18:04:16',0,NULL),(7,'KA','2025-04-18',900.00,'female','2025-04-01 12:41:03',7,NULL),(8,'aby','2025-04-23',200.00,'female','2025-04-01 12:47:47',7,NULL),(9,'punjab','2025-04-24',1000.00,'female','2025-04-01 14:44:59',7,NULL),(10,'Andhra','2025-04-23',1000.00,'any','2025-04-01 14:45:46',7,NULL),(11,'Andhra','2025-04-10',1000.00,'male','2025-04-01 14:50:58',7,NULL),(12,'Telengana','2025-04-24',1000.00,'male','2025-04-01 15:21:10',7,NULL),(13,'Gujarath','2025-04-23',1000.00,'male','2025-04-01 15:21:46',7,NULL),(14,'Gujarath','2025-04-25',1000.00,'male','2025-04-04 15:19:18',7,NULL),(15,'west bengal','2025-04-18',1005.00,'male','2025-04-04 15:43:05',7,NULL),(16,'USA','2025-04-23',2200.00,'any','2025-04-05 07:05:58',7,NULL),(17,'USA','2025-04-26',2200.00,'any','2025-04-05 07:13:37',7,NULL),(18,'USA','2025-04-18',2200.00,'any','2025-04-05 07:14:25',7,NULL),(19,'USA','2025-05-08',2200.00,'male','2025-04-05 07:16:08',7,NULL),(20,'USA','2025-04-18',2200.00,'male','2025-04-05 07:18:54',7,NULL),(21,'USA','2025-04-19',2200.00,'male','2025-04-05 07:19:54',7,NULL),(22,'USA','2025-04-25',2200.00,'male','2025-04-05 11:16:10',7,NULL),(23,'USA','2025-04-23',2200.00,'male','2025-04-05 11:25:35',7,NULL),(24,'USA','2025-05-02',2200.00,'male','2025-04-05 11:28:05',7,NULL),(25,'USA','2025-04-24',2200.00,'male','2025-04-05 11:29:30',7,NULL),(26,'USA','2025-04-18',2200.00,'male','2025-04-05 12:06:40',7,NULL),(27,'Jammu','2025-04-16',2000.00,'male','2025-04-05 12:08:27',7,NULL),(28,'srinagar','2025-04-17',1000.00,'female','2025-04-07 08:40:00',7,NULL);
/*!40000 ALTER TABLE `solo_trips` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `travel_partners`
--

DROP TABLE IF EXISTS `travel_partners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `travel_partners` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user1_id` int NOT NULL,
  `user2_id` int NOT NULL,
  `trip_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user1_id` (`user1_id`),
  KEY `user2_id` (`user2_id`),
  KEY `trip_id` (`trip_id`),
  CONSTRAINT `travel_partners_ibfk_1` FOREIGN KEY (`user1_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `travel_partners_ibfk_2` FOREIGN KEY (`user2_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `travel_partners_ibfk_3` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`trip_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `travel_partners`
--

LOCK TABLES `travel_partners` WRITE;
/*!40000 ALTER TABLE `travel_partners` DISABLE KEYS */;
/*!40000 ALTER TABLE `travel_partners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trip_members`
--

DROP TABLE IF EXISTS `trip_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trip_members` (
  `id` int NOT NULL AUTO_INCREMENT,
  `trip_id` int NOT NULL,
  `user_id` int NOT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `joined_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `trip_id` (`trip_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `trip_members_ibfk_1` FOREIGN KEY (`trip_id`) REFERENCES `group_trips` (`id`) ON DELETE CASCADE,
  CONSTRAINT `trip_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trip_members`
--

LOCK TABLES `trip_members` WRITE;
/*!40000 ALTER TABLE `trip_members` DISABLE KEYS */;
/*!40000 ALTER TABLE `trip_members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trips`
--

DROP TABLE IF EXISTS `trips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trips` (
  `trip_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `destination` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `date` date NOT NULL,
  `budget` decimal(10,2) NOT NULL,
  `preferences` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`trip_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `trips_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trips`
--

LOCK TABLES `trips` WRITE;
/*!40000 ALTER TABLE `trips` DISABLE KEYS */;
/*!40000 ALTER TABLE `trips` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `age` date NOT NULL,
  `phone_number` varchar(11) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_admin` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (3,'Athulya','athulya2002@gmail.com','$2y$12$JUbdYh/EVouBN62lGMd1a.pniBfugrjTwt6H6/9xp.lpVMhaV6I.W','2025-03-01 03:42:49','2002-03-06','7593324623',0),(4,'Aby','abyponnachan@gmail.com','$2y$12$jzjmh4xSSNrx2mavSl/ZteABDtke8095d3A72fByovPuBVAK3Brt6','2025-03-01 05:11:28','2001-05-21','7593042375',0),(5,'Anwar','anwar007@gmail.com','$2y$12$0zcW4igwpadOw.AB/QZXHOMXvq5Hbiiy/Sieg7Yo.34fyOzMrnUiK','2025-03-01 06:34:46','2003-06-10','7725238428',0),(6,'Archa','archa007@gmail.com','$2y$12$inuE4JGAz4p/R/E3kRy5n.cgujpj.bK3NU4/3EyJzwkakn5TMFJfK','2025-03-01 07:28:20','2003-06-09','4412445312',0),(7,'test','test123@gmail.com','$2y$12$YvGHfBe/8T0i79tpVpCbH.P65T.yBUH0qcbnT72wdvAV/aEaKeMoO','2025-03-09 13:16:44','2011-06-07','7593324623',0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;




--
-- Table structure for table `gender`
--

DROP TABLE IF EXISTS `gender`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gender` (
  `user_id` int NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `gender_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gender`
--

LOCK TABLES `gender` WRITE;
/*!40000 ALTER TABLE `gender` DISABLE KEYS */;
/*!40000 ALTER TABLE `gender` ENABLE KEYS */;
UNLOCK TABLES;


/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-04-07 14:18:17
