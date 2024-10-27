-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: database-1.c704sumgm4qf.ap-southeast-2.rds.amazonaws.com    Database: maneclickv2
-- ------------------------------------------------------
-- Server version	8.0.35

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
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ '';

--
-- Table structure for table `archive_p`
--

DROP TABLE IF EXISTS `archive_p`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `archive_p` (
  `id` int NOT NULL AUTO_INCREMENT,
  `slp_id` int NOT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `disorder` varchar(255) DEFAULT NULL,
  `sex` enum('male','female','other') NOT NULL,
  `birthdate` date NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `guardian` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'inactive',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `archive_p`
--

LOCK TABLES `archive_p` WRITE;
/*!40000 ALTER TABLE `archive_p` DISABLE KEYS */;
INSERT INTO `archive_p` VALUES (2,3,'test','archive','arch@gmail.com','adhd','male','2008-01-04','archive address','archive guardian','inactive','2024-09-06 18:19:01');
/*!40000 ALTER TABLE `archive_p` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL,
  `action` text NOT NULL,
  `role` varchar(50) NOT NULL,
  `createdAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,'logout','adminmane logged out of the website','adminmane','2024-09-06 16:42:21','2024-09-06 16:42:21'),(2,'Register','New user registered, account name: sampleuser','SLP','2024-09-06 16:43:04','2024-09-06 16:43:04'),(3,'login','sampleuser logged in to the website','sampleuser','2024-09-06 16:43:31','2024-09-06 16:43:31'),(4,'logout','sampleuser logged out of the website','sampleuser','2024-09-06 16:49:32','2024-09-06 16:49:32'),(5,'login','adminmane logged in to the website','adminmane','2024-09-06 16:52:22','2024-09-06 16:52:22'),(6,'logout','adminmane logged out of the website','adminmane','2024-09-06 17:22:29','2024-09-06 17:22:29'),(7,'login','sampleuser logged in to the website','sampleuser','2024-09-06 17:22:46','2024-09-06 17:22:46'),(8,'logout','sampleuser logged out of the website','sampleuser','2024-09-06 17:24:51','2024-09-06 17:24:51'),(9,'Register','New user registered, account name: sampletwo','SLP','2024-09-06 17:25:32','2024-09-06 17:25:32'),(10,'login','admin logged in to the website','admin','2024-09-06 17:26:31','2024-09-06 17:26:31'),(11,'logout','admin logged out of the website','admin','2024-09-06 17:26:59','2024-09-06 17:26:59'),(12,'login','sampletwo logged in to the website','sampletwo','2024-09-06 17:27:11','2024-09-06 17:27:11'),(13,'Add Patient','sampletwo added a patient named sample patient one','sampletwo','2024-09-06 17:29:39','2024-09-06 17:29:39'),(14,'View Patient','sampletwo viewed patient details for sample patient one','sampletwo','2024-09-06 17:30:01','2024-09-06 17:30:01'),(15,'Add Therapy','sampletwo added therapy for patient sample patient one with DSI #1284872475','sampletwo','2024-09-06 17:30:38','2024-09-06 17:30:38'),(16,'View Patient','sampletwo viewed patient details for sample patient one','sampletwo','2024-09-06 17:30:42','2024-09-06 17:30:42'),(17,'Add Session','sampletwo added a session to patient with DSI 0000-00-00','sampletwo','2024-09-06 17:38:46','2024-09-06 17:38:46'),(18,'View Patient','sampletwo viewed patient details for sample patient one','sampletwo','2024-09-06 17:38:51','2024-09-06 17:38:51'),(19,'View Patient','sampletwo viewed patient details for sample patient one','sampletwo','2024-09-06 17:39:15','2024-09-06 17:39:15'),(20,'View Patient','sampletwo viewed patient details for sample patient one','sampletwo','2024-09-06 17:49:26','2024-09-06 17:49:26'),(21,'View Patient','sampletwo viewed patient details for sample patient one','sampletwo','2024-09-06 17:49:48','2024-09-06 17:49:48'),(22,'View Patient','sampletwo viewed patient details for sample patient one','sampletwo','2024-09-06 17:50:20','2024-09-06 17:50:20'),(23,'View Patient','sampletwo viewed patient details for sample patient one','sampletwo','2024-09-06 17:51:14','2024-09-06 17:51:14'),(24,'View Patient','sampletwo viewed patient details for sample patient one','sampletwo','2024-09-06 17:51:51','2024-09-06 17:51:51'),(25,'View Patient','sampletwo viewed patient details for sample patient one','sampletwo','2024-09-06 17:52:57','2024-09-06 17:52:57'),(26,'Add Session','sampletwo added a session to patient with DSI 0000-00-00','sampletwo','2024-09-06 17:53:42','2024-09-06 17:53:42'),(27,'View Patient','sampletwo viewed patient details for sample patient one','sampletwo','2024-09-06 17:53:46','2024-09-06 17:53:46'),(28,'View Patient','sampletwo viewed patient details for sample patient one','sampletwo','2024-09-06 17:54:40','2024-09-06 17:54:40'),(29,'View Patient','sampletwo viewed patient details for sample patient one','sampletwo','2024-09-06 17:58:38','2024-09-06 17:58:38'),(30,'Add Session','sampletwo added a session to patient with DSI 0000-00-00','sampletwo','2024-09-06 17:59:28','2024-09-06 17:59:28'),(31,'View Patient','sampletwo viewed patient details for sample patient one','sampletwo','2024-09-06 17:59:32','2024-09-06 17:59:32'),(32,'View Patient','sampletwo viewed patient details for sample patient one','sampletwo','2024-09-06 18:00:01','2024-09-06 18:00:01'),(33,'View Patient','sampletwo viewed patient details for sample patient one','sampletwo','2024-09-06 18:04:40','2024-09-06 18:04:40'),(34,'View Patient','sampletwo viewed patient details for sample patient one','sampletwo','2024-09-06 18:16:38','2024-09-06 18:16:38'),(35,'Generate Arima','sampletwo generated an arima report for patient.','sampletwo','2024-09-06 18:16:40','2024-09-06 18:16:40'),(36,'Generate Arima','sampletwo generated an arima report for patient.','sampletwo','2024-09-06 18:17:01','2024-09-06 18:17:01'),(37,'View Patient','sampletwo viewed patient details for sample patient one','sampletwo','2024-09-06 18:17:19','2024-09-06 18:17:19'),(38,'Generate Arima','sampletwo generated an arima report for patient.','sampletwo','2024-09-06 18:17:22','2024-09-06 18:17:22'),(39,'View Patient','sampletwo viewed patient details for sample patient one','sampletwo','2024-09-06 18:17:47','2024-09-06 18:17:47'),(40,'Add Patient','sampletwo added a patient named test archive','sampletwo','2024-09-06 18:18:52','2024-09-06 18:18:52'),(41,'Archive Patient','sampletwo archived patient test archive','sampletwo','2024-09-06 18:19:01','2024-09-06 18:19:01'),(42,'logout','sampletwo logged out of the website','sampletwo','2024-09-06 18:21:03','2024-09-06 18:21:03'),(43,'login','admin logged in to the website','admin','2024-09-06 18:21:10','2024-09-06 18:21:10'),(44,'login','admin logged in to the website','admin','2024-09-07 07:03:10','2024-09-07 07:03:10'),(45,'logout','admin logged out of the website','admin','2024-09-07 07:05:10','2024-09-07 07:05:10'),(46,'login','admin logged in to the website','admin','2024-09-11 11:59:55','2024-09-11 11:59:55'),(47,'logout','admin logged out of the website','admin','2024-09-11 12:00:12','2024-09-11 12:00:12'),(48,'login','admin logged in to the website','admin','2024-09-11 12:00:34','2024-09-11 12:00:34'),(49,'logout','admin logged out of the website','admin','2024-09-11 12:04:55','2024-09-11 12:04:55'),(50,'Register','New user registered, account name: slpchi','SLP','2024-09-11 12:05:54','2024-09-11 12:05:54'),(51,'login','admin logged in to the website','admin','2024-09-11 12:06:34','2024-09-11 12:06:34'),(52,'logout','admin logged out of the website','admin','2024-09-11 12:07:42','2024-09-11 12:07:42'),(53,'login','admin logged in to the website','admin','2024-09-12 05:26:04','2024-09-12 05:26:04'),(54,'logout','admin logged out of the website','admin','2024-09-12 05:26:41','2024-09-12 05:26:41'),(55,'login','slpchi logged in to the website','slpchi','2024-09-12 05:26:52','2024-09-12 05:26:52');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `patients`
--

DROP TABLE IF EXISTS `patients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `patients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `slp_id` int NOT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `disorder` varchar(255) DEFAULT NULL,
  `sex` enum('male','female','other') NOT NULL,
  `birthdate` date NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `guardian` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `patients`
--

LOCK TABLES `patients` WRITE;
/*!40000 ALTER TABLE `patients` DISABLE KEYS */;
INSERT INTO `patients` VALUES (1,3,'sample','patient one','samplepone@gmail.com','adhd','male','2016-05-03','123 adress','pone guardian','active','2024-09-06 17:29:38'),(2,3,'test','archive','arch@gmail.com','adhd','male','2008-01-04','archive address','archive guardian','inactive','2024-09-06 18:18:52');
/*!40000 ALTER TABLE `patients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plans_type`
--

DROP TABLE IF EXISTS `plans_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plans_type` (
  `id` int NOT NULL AUTO_INCREMENT,
  `plan_type` varchar(100) NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `patients` int NOT NULL,
  `access` varchar(255) DEFAULT NULL,
  `special_access` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plans_type`
--

LOCK TABLES `plans_type` WRITE;
/*!40000 ALTER TABLE `plans_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `plans_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prompts`
--

DROP TABLE IF EXISTS `prompts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `prompts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `prompt_name` varchar(255) NOT NULL,
  `score` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prompts`
--

LOCK TABLES `prompts` WRITE;
/*!40000 ALTER TABLE `prompts` DISABLE KEYS */;
INSERT INTO `prompts` VALUES (1,'Correct Independent Production',100),(2,'Visual Prompt',80),(3,'Verbal Prompt',60),(4,'Tactile Prompt',40),(5,'Hand under Hand Assistance',20);
/*!40000 ALTER TABLE `prompts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sesh_goals`
--

DROP TABLE IF EXISTS `sesh_goals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sesh_goals` (
  `id` int NOT NULL AUTO_INCREMENT,
  `patient_id` int NOT NULL,
  `goal` text NOT NULL,
  `is_goal_reached` enum('yes','no') NOT NULL DEFAULT 'no',
  `createdAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sesh_goals`
--

LOCK TABLES `sesh_goals` WRITE;
/*!40000 ALTER TABLE `sesh_goals` DISABLE KEYS */;
INSERT INTO `sesh_goals` VALUES (1,1,'teach patient how to read properly','yes','2024-09-06 10:38:45','2024-09-06 10:38:45'),(2,1,'improve from first','yes','2024-09-06 17:53:41','2024-09-06 17:53:41'),(3,1,'Really improve from last two sessions','yes','2024-09-06 17:59:27','2024-09-06 17:59:27');
/*!40000 ALTER TABLE `sesh_goals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscription`
--

DROP TABLE IF EXISTS `subscription`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscription` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `type` varchar(50) NOT NULL,
  `plan_cost` decimal(10,2) NOT NULL,
  `s_account_number` varchar(100) DEFAULT NULL,
  `s_account_name` varchar(100) DEFAULT NULL,
  `payrefnumber` varchar(100) DEFAULT NULL,
  `status` int DEFAULT '1',
  `createdAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscription`
--

LOCK TABLES `subscription` WRITE;
/*!40000 ALTER TABLE `subscription` DISABLE KEYS */;
INSERT INTO `subscription` VALUES (1,2,'Free Trial',1.00,NULL,NULL,NULL,1,'2024-09-06 16:44:14'),(2,3,'Standard Plan',499.00,NULL,NULL,NULL,1,'2024-09-06 17:28:03');
/*!40000 ALTER TABLE `subscription` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_sessions`
--

DROP TABLE IF EXISTS `t_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `t_sessions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `DSI` varchar(255) NOT NULL,
  `patient_id` int NOT NULL,
  `session_number` int NOT NULL,
  `word` varchar(100) NOT NULL,
  `prompt` varchar(255) DEFAULT NULL,
  `interpretation` text,
  `remarks` text,
  `createdAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_sessions`
--

LOCK TABLES `t_sessions` WRITE;
/*!40000 ALTER TABLE `t_sessions` DISABLE KEYS */;
INSERT INTO `t_sessions` VALUES (1,'0000-00-00',1,2147483647,'one','Visual prompt','good','good','2024-09-06 10:38:45','2024-09-06 10:38:45'),(2,'0000-00-00',1,2147483647,'two','Correct independent production','very good','very good','2024-09-06 10:38:45','2024-09-06 10:38:45'),(3,'0000-00-00',1,2147483647,'test','Correct independent production','very good','very good','2024-09-07 10:53:42','2024-09-07 10:53:42'),(4,'0000-00-00',1,2147483647,'apple','Correct independent production','very good','very good','2024-09-07 10:53:42','2024-09-07 10:53:42'),(5,'0000-00-00',1,2147483647,'games','Verbal prompt','not improving','not improving','2024-09-09 10:59:28','2024-09-09 10:59:28'),(6,'0000-00-00',1,2147483647,'eight','Tactile prompt','having hard time','having hard time','2024-09-09 10:59:28','2024-09-09 10:59:28');
/*!40000 ALTER TABLE `t_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `therapy`
--

DROP TABLE IF EXISTS `therapy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `therapy` (
  `id` int NOT NULL AUTO_INCREMENT,
  `patient_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `sex` enum('male','female','other') NOT NULL,
  `SLP` varchar(100) NOT NULL,
  `disorders` varchar(255) DEFAULT NULL,
  `DSI` date DEFAULT NULL,
  `DOE` date DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `FTD` date DEFAULT NULL,
  `TFD` date DEFAULT NULL,
  `overall_remarks` text,
  `status` enum('active','inactive') DEFAULT 'active',
  `createdAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `therapy`
--

LOCK TABLES `therapy` WRITE;
/*!40000 ALTER TABLE `therapy` DISABLE KEYS */;
INSERT INTO `therapy` VALUES (1,1,'sample patient one','male','sample two','adhd','0000-00-00','2024-10-31','2024-10-30','2024-09-10','2024-10-26',NULL,'active','2024-09-06 10:38:45');
/*!40000 ALTER TABLE `therapy` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `birthdate` date NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `gender` enum('male','female') NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `status` int DEFAULT '0',
  `prc_id` varchar(50) DEFAULT NULL,
  `prc_id_no` varchar(50) DEFAULT NULL,
  `createdAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','admin','1999-01-01','admin','09123221454','male','admin@gmail.com','admin','$2y$10$4y1tjAZqHDNuDe74f9GNYOKhWkJJkvHh3SxOlvZQv8NcUjTIBrGiW','admin',1,NULL,NULL,'2024-09-06 15:54:09','2024-09-06 17:26:26'),(2,'sample','user','2003-09-01','123 address sample','09124578778','female','sampleuser@gmail.com','sampleuser','$2y$10$wB6mPrD0kfwQO/bQxLY6..KxjmW55e3/UZ58gd4UYcCyMQh.R3rEO','SLP',1,'/MANECLICK-V.2/BACKEND/uploads/prc_id_66db3125ed0b','1111111','2024-09-06 16:43:04','2024-09-06 17:22:16'),(3,'sample','two','2000-02-03','sample two address','09124565454','male','sampletwo@gmail.com','sampletwo','$2y$10$b3uFmm2sJbUUXAkY8xaKu.FXusNWVaWZLRB0ogpFjdAo.pvQc86Qi','SLP',1,'/MANECLICK-V.2/BACKEND/uploads/prc_id_66db3b1b74d8','2222222','2024-09-06 17:25:32','2024-09-06 17:26:43'),(4,'Chi','Tiu','2003-08-13','Makati City','09771812345','female','chitiu732@gmail.com','slpchi','$2y$10$p6cnlZq5a8fB3FboNjAd8OMJXEnJ3L55Ude8bneczDdKGyWDcS/B6','SLP',1,'/MANECLICK-V.2/BACKEND/uploads/prc_id_66e187b91051','1234567','2024-09-11 12:05:54','2024-09-12 05:26:32');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'maneclickv2'
--
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-09-12 13:59:34
