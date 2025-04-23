/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.11-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: zubi
-- ------------------------------------------------------
-- Server version	10.11.11-MariaDB-0+deb12u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `admin_id` (`admin_id`),
  KEY `action` (`action`),
  CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `activity_logs_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` enum('admin','instructor','data_entry') NOT NULL DEFAULT 'data_entry',
  `profile_image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `username_2` (`username`),
  KEY `email_2` (`email`),
  KEY `role` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES
(1,'admin','admin@example.com','$2y$12$ff72bOqR6xQ9P7rcSx.J2eI12yo4EKGV7f4hNVKCSE3jGv5fJ/lOC','Admin User','admin',NULL,1,'2025-04-18 00:30:47','2025-04-17 21:20:35','2025-04-18 00:30:47'),
(2,'instructor','instructor@example.com','$2y$12$ff72bOqR6xQ9P7rcSx.J2eI12yo4EKGV7f4hNVKCSE3jGv5fJ/lOC','Instructor User','instructor',NULL,1,NULL,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(3,'dataentry','dataentry@example.com','$2y$12$ff72bOqR6xQ9P7rcSx.J2eI12yo4EKGV7f4hNVKCSE3jGv5fJ/lOC','Data Entry User','data_entry',NULL,1,NULL,'2025-04-17 21:20:35','2025-04-17 21:20:35');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `certificates`
--

DROP TABLE IF EXISTS `certificates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `certificates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `certificate_number` varchar(50) NOT NULL,
  `issue_date` datetime DEFAULT current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `certificate_number` (`certificate_number`),
  KEY `user_id` (`user_id`),
  KEY `course_id` (`course_id`),
  KEY `certificate_number_2` (`certificate_number`),
  CONSTRAINT `certificates_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `certificates_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `certificates`
--

LOCK TABLES `certificates` WRITE;
/*!40000 ALTER TABLE `certificates` DISABLE KEYS */;
INSERT INTO `certificates` VALUES
(1,2,4,'CERT-NET-2023-001','2023-01-15 00:00:00','2025-04-17 21:20:35','2025-04-17 21:20:35'),
(2,3,4,'CERT-NET-2023-002','2023-01-20 00:00:00','2025-04-17 21:20:35','2025-04-17 21:20:35');
/*!40000 ALTER TABLE `certificates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `company_url` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `is_free` tinyint(1) DEFAULT 0,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_published` tinyint(1) DEFAULT 0,
  `status` enum('draft','published') NOT NULL DEFAULT 'draft',
  `admin_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `slug_2` (`slug`),
  KEY `is_free` (`is_free`),
  KEY `is_featured` (`is_featured`),
  KEY `is_published` (`is_published`),
  KEY `admin_id` (`admin_id`),
  CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courses`
--

LOCK TABLES `courses` WRITE;
/*!40000 ALTER TABLE `courses` DISABLE KEYS */;
INSERT INTO `courses` VALUES
(1,'CCNA Certification','ccna-certification',NULL,'Comprehensive course for Cisco Certified Network Associate certification. Learn networking fundamentals, routing and switching, and network security.',NULL,99.99,0,1,1,'draft',1,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(2,'CCNP Enterprise','ccnp-enterprise',NULL,'Advanced course for Cisco Certified Network Professional Enterprise certification. Master complex enterprise networking concepts.',NULL,199.99,0,1,1,'draft',1,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(3,'Security+ Certification','security-plus-certification',NULL,'Complete preparation for CompTIA Security+ certification. Learn cybersecurity fundamentals, threats, vulnerabilities, and security controls.',NULL,79.99,0,1,1,'draft',2,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(4,'Network+ Basics','network-plus-basics',NULL,'Introduction to networking concepts for CompTIA Network+ certification. Free course for beginners.',NULL,0.00,1,0,1,'draft',2,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(5,'Linux Essentials','linux-essentials',NULL,'Learn Linux fundamentals and prepare for the Linux Essentials certification.',NULL,49.99,0,0,1,'draft',2,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(6,'Test Course','test-coursetest-course','http://example.com','This is a test course description','',0.00,0,0,0,'draft',1,'2025-04-17 22:13:07','2025-04-17 22:13:07'),
(7,'Test Course with Status Field','test-course-with-status-field','','This is a test course to verify that the status field is working correctly.','',99.99,0,0,0,'draft',2,'2025-04-17 22:13:43','2025-04-17 22:13:43'),
(8,'دورة جديدة1','-','https://cisc1o.com','دورة جديد1ة','',2.00,0,0,0,'published',2,'2025-04-17 22:16:39','2025-04-17 22:17:04'),
(9,'boo','boo','https://cisco.com','boo','',0.00,1,0,0,'published',1,'2025-04-18 00:44:54','2025-04-18 00:44:54');
/*!40000 ALTER TABLE `courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enrollments`
--

DROP TABLE IF EXISTS `enrollments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `enrollment_date` datetime DEFAULT current_timestamp(),
  `completion_date` datetime DEFAULT NULL,
  `is_completed` tinyint(1) DEFAULT 0,
  `progress` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`course_id`),
  KEY `user_id_2` (`user_id`),
  KEY `course_id` (`course_id`),
  KEY `is_completed` (`is_completed`),
  CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enrollments`
--

LOCK TABLES `enrollments` WRITE;
/*!40000 ALTER TABLE `enrollments` DISABLE KEYS */;
INSERT INTO `enrollments` VALUES
(1,1,1,'2025-04-17 21:20:35',NULL,0,30,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(2,1,3,'2025-04-17 21:20:35',NULL,0,20,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(3,2,2,'2025-04-17 21:20:35',NULL,0,15,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(4,2,4,'2025-04-17 21:20:35',NULL,1,100,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(5,3,4,'2025-04-17 21:20:35',NULL,1,100,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(6,3,5,'2025-04-17 21:20:35',NULL,0,50,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(7,4,1,'2025-04-17 21:20:35',NULL,0,75,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(8,5,3,'2025-04-17 21:20:35',NULL,0,40,'2025-04-17 21:20:35','2025-04-17 21:20:35');
/*!40000 ALTER TABLE `enrollments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_attempts`
--

DROP TABLE IF EXISTS `exam_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `started_at` datetime NOT NULL,
  `completed_at` datetime DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `is_passed` tinyint(1) DEFAULT 0,
  `answers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`answers`)),
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `exam_id` (`exam_id`),
  KEY `is_passed` (`is_passed`),
  CONSTRAINT `exam_attempts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exam_attempts_ibfk_2` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_attempts`
--

LOCK TABLES `exam_attempts` WRITE;
/*!40000 ALTER TABLE `exam_attempts` DISABLE KEYS */;
INSERT INTO `exam_attempts` VALUES
(1,1,1,'2023-01-15 10:00:00','2023-01-15 11:15:00',85,1,'{\"1\": \"192.168.1.129 - 192.168.1.254\", \"2\": \"TCP\", \"3\": \"To resolve IP addresses to MAC addresses\"}','2025-04-17 21:20:35','2025-04-17 21:20:35'),
(2,1,2,'2023-01-20 14:00:00','2023-01-20 15:30:00',75,1,'{\"1\": \"show arp\"}','2025-04-17 21:20:35','2025-04-17 21:20:35'),
(3,2,5,'2023-01-10 09:30:00','2023-01-10 10:00:00',90,1,'{\"1\": [\"10.0.0.1\", \"172.16.0.1\", \"224.0.0.1\"]}','2025-04-17 21:20:35','2025-04-17 21:20:35'),
(4,3,5,'2023-01-12 16:00:00','2023-01-12 16:25:00',80,1,'{\"1\": [\"10.0.0.1\", \"172.16.0.1\"]}','2025-04-17 21:20:35','2025-04-17 21:20:35'),
(5,3,6,'2023-01-25 11:00:00','2023-01-25 11:40:00',65,0,'{\"1\": {\"ls\": \"Change directory\", \"cd\": \"List directory contents\", \"pwd\": \"Print working directory\", \"mkdir\": \"Create a new directory\", \"rm\": \"Remove files or directories\"}}','2025-04-17 21:20:35','2025-04-17 21:20:35'),
(6,4,1,'2023-01-18 13:00:00','2023-01-18 14:15:00',90,1,'{\"1\": \"192.168.1.129 - 192.168.1.254\", \"2\": \"TCP\", \"3\": \"To resolve IP addresses to MAC addresses\"}','2025-04-17 21:20:35','2025-04-17 21:20:35');
/*!40000 ALTER TABLE `exam_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_course_recommendations`
--

DROP TABLE IF EXISTS `exam_course_recommendations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam_course_recommendations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exam_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `priority` int(11) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_exam_course` (`exam_id`,`course_id`),
  KEY `course_id` (`course_id`),
  CONSTRAINT `exam_course_recommendations_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exam_course_recommendations_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_course_recommendations`
--

LOCK TABLES `exam_course_recommendations` WRITE;
/*!40000 ALTER TABLE `exam_course_recommendations` DISABLE KEYS */;
INSERT INTO `exam_course_recommendations` VALUES
(1,8,1,1,'2025-04-17 21:58:36','2025-04-17 21:58:36'),
(2,8,3,2,'2025-04-17 21:58:36','2025-04-17 21:58:36'),
(3,11,2,1,'2025-04-17 22:00:37','2025-04-17 22:00:37'),
(4,11,3,2,'2025-04-17 22:00:37','2025-04-17 22:00:37'),
(5,14,1,1,'2025-04-17 22:17:33','2025-04-17 22:17:33'),
(6,16,1,1,'2025-04-18 00:35:06','2025-04-18 00:35:06'),
(7,16,5,2,'2025-04-18 00:35:06','2025-04-18 00:35:06'),
(10,17,5,1,'2025-04-18 00:45:33','2025-04-18 00:45:33');
/*!40000 ALTER TABLE `exam_course_recommendations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exams`
--

DROP TABLE IF EXISTS `exams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `exams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `duration_minutes` int(11) NOT NULL DEFAULT 60,
  `passing_score` int(11) NOT NULL DEFAULT 70,
  `is_free` tinyint(1) DEFAULT 0,
  `is_published` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `slug_2` (`slug`),
  KEY `is_free` (`is_free`),
  KEY `is_published` (`is_published`),
  KEY `course_id` (`course_id`),
  CONSTRAINT `exams_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exams`
--

LOCK TABLES `exams` WRITE;
/*!40000 ALTER TABLE `exams` DISABLE KEYS */;
INSERT INTO `exams` VALUES
(1,'CCNA Practice Exam 1','ccna-practice-exam-1','Practice exam covering CCNA fundamentals and network basics.',1,90,70,0,1,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(2,'CCNA Practice Exam 2','ccna-practice-exam-2','Advanced practice exam covering CCNA routing and switching concepts.',1,90,70,0,1,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(3,'CCNP Enterprise Core Exam','ccnp-enterprise-core-exam','Practice exam for CCNP Enterprise Core (350-401 ENCOR).',2,120,75,0,1,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(4,'Security+ Practice Test','security-plus-practice-test','Comprehensive practice test for CompTIA Security+ certification.',3,90,75,0,1,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(5,'Network+ Sample Questions','network-plus-sample-questions','Free sample questions for Network+ certification.',4,30,70,1,1,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(6,'Linux Essentials Quiz','linux-essentials-quiz','Quick quiz to test your Linux knowledge.',5,45,70,0,1,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(7,'Updated Test Exam 123','updated-test-exam-123','This is an updated test exam description for CCNA certification.',1,60,70,0,1,'2025-04-17 21:38:08','2025-04-17 21:38:36'),
(8,'Test Exam 123','test-exam-123','This is a test exam description for CCNA certification.',1,60,70,0,0,'2025-04-17 21:38:55','2025-04-17 21:58:36'),
(11,'Test Exam with Course Recommendations','test-exam-with-course-recommendations','This is a test exam to demonstrate course recommendations functionality',1,60,65,0,0,'2025-04-17 22:00:21','2025-04-17 22:00:37'),
(12,'تجربه','تجربه','تجربه',1,60,70,0,1,'2025-04-17 22:02:32','2025-04-17 22:02:32'),
(13,'Test Exam','test-exam','This is a test exam description',7,60,70,0,0,'2025-04-17 22:14:19','2025-04-17 22:14:19'),
(14,'T1est Exam for Verification','t1est-exam-for-verification','This is a test exam to verify the admin dashboard functionality',8,60,60,0,1,'2025-04-17 22:14:51','2025-04-17 22:17:33'),
(15,'1','1','1',8,60,70,0,1,'2025-04-17 22:17:57','2025-04-17 22:17:57'),
(16,'22','22','22',NULL,60,70,0,1,'2025-04-18 00:35:06','2025-04-18 00:35:06'),
(17,'b1oo','b1oo','boo',NULL,60,70,0,0,'2025-04-18 00:45:08','2025-04-18 00:45:33');
/*!40000 ALTER TABLE `exams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `status` enum('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
  `payment_date` datetime DEFAULT current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `course_id` (`course_id`),
  KEY `status` (`status`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES
(1,1,1,99.99,'credit_card','TXN123456','completed','2025-04-17 21:20:35','2025-04-17 21:20:35','2025-04-17 21:20:35'),
(2,1,3,79.99,'paypal','PP987654','completed','2025-04-17 21:20:35','2025-04-17 21:20:35','2025-04-17 21:20:35'),
(3,2,2,199.99,'credit_card','TXN789012','completed','2025-04-17 21:20:35','2025-04-17 21:20:35','2025-04-17 21:20:35'),
(4,3,5,49.99,'credit_card','TXN345678','completed','2025-04-17 21:20:35','2025-04-17 21:20:35','2025-04-17 21:20:35'),
(5,4,1,99.99,'paypal','PP456789','completed','2025-04-17 21:20:35','2025-04-17 21:20:35','2025-04-17 21:20:35');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `questions`
--

DROP TABLE IF EXISTS `questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exam_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('single_choice','multiple_choice','drag_drop') NOT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`options`)),
  `correct_answer` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`correct_answer`)),
  `explanation` text DEFAULT NULL,
  `points` int(11) NOT NULL DEFAULT 1,
  `order_number` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `exam_id` (`exam_id`),
  KEY `question_type` (`question_type`),
  CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `questions`
--

LOCK TABLES `questions` WRITE;
/*!40000 ALTER TABLE `questions` DISABLE KEYS */;
INSERT INTO `questions` VALUES
(1,1,'Which of the following is the valid host range for the subnet 192.168.1.128/25?','single_choice','[\"192.168.1.129 - 192.168.1.254\", \"192.168.1.1 - 192.168.1.126\", \"192.168.1.129 - 192.168.1.255\", \"192.168.1.128 - 192.168.1.255\"]','\"192.168.1.129 - 192.168.1.254\"','For a /25 subnet with network address 192.168.1.128, the valid host range is from 192.168.1.129 to 192.168.1.254. The address 192.168.1.255 is the broadcast address.',1,1,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(2,1,'Which protocol operates at the Transport layer of the OSI model?','single_choice','[\"HTTP\", \"IP\", \"Ethernet\", \"TCP\"]','\"TCP\"','TCP (Transmission Control Protocol) operates at the Transport layer (Layer 4) of the OSI model.',1,2,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(3,1,'What is the purpose of ARP?','single_choice','[\"To resolve domain names to IP addresses\", \"To resolve IP addresses to MAC addresses\", \"To assign IP addresses dynamically\", \"To encrypt network traffic\"]','\"To resolve IP addresses to MAC addresses\"','ARP (Address Resolution Protocol) is used to map IP network addresses to MAC addresses in a local network.',1,3,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(4,2,'Which command would you use to verify the Layer 2 to Layer 3 mapping on a Cisco router?','single_choice','[\"show ip route\", \"show interfaces\", \"show arp\", \"show ip interface brief\"]','\"show arp\"','The \"show arp\" command displays the ARP table, which shows the mapping between IP addresses (Layer 3) and MAC addresses (Layer 2).',1,1,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(5,3,'Which technology allows for the creation of multiple virtual routers within a single physical router?','single_choice','[\"VTP\", \"VRF\", \"HSRP\", \"EIGRP\"]','\"VRF\"','VRF (Virtual Routing and Forwarding) allows for multiple routing tables to exist in a router at the same time, effectively creating multiple virtual routers.',2,1,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(6,4,'Which of the following are symmetric encryption algorithms? (Select all that apply)','multiple_choice','[\"AES\", \"RSA\", \"DES\", \"ECC\", \"3DES\"]','[\"AES\", \"DES\", \"3DES\"]','AES, DES, and 3DES are symmetric encryption algorithms. RSA and ECC are asymmetric (public key) encryption algorithms.',2,1,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(7,4,'Which of the following are common security controls? (Select all that apply)','multiple_choice','[\"Firewall\", \"Antivirus\", \"Social engineering\", \"Access control\", \"Phishing\"]','[\"Firewall\", \"Antivirus\", \"Access control\"]','Firewalls, antivirus software, and access control are security controls. Social engineering and phishing are attack methods, not controls.',2,2,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(8,5,'Which of the following are valid IPv4 addresses? (Select all that apply)','multiple_choice','[\"192.168.1.256\", \"10.0.0.1\", \"172.16.0.1\", \"256.0.0.1\", \"224.0.0.1\"]','[\"10.0.0.1\", \"172.16.0.1\", \"224.0.0.1\"]','Valid IPv4 addresses must have each octet between 0 and 255. 192.168.1.256 and 256.0.0.1 are invalid because they contain octets greater than 255.',1,1,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(9,6,'Match the Linux commands with their descriptions.','drag_drop','{\"ls\": \"List directory contents\", \"cd\": \"Change directory\", \"pwd\": \"Print working directory\", \"mkdir\": \"Create a new directory\", \"rm\": \"Remove files or directories\"}','{\"ls\": \"List directory contents\", \"cd\": \"Change directory\", \"pwd\": \"Print working directory\", \"mkdir\": \"Create a new directory\", \"rm\": \"Remove files or directories\"}','These are basic Linux commands and their functions.',3,1,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(10,3,'Match the OSI layers with the correct protocols.','drag_drop','{\"Application\": \"HTTP, FTP, SMTP\", \"Presentation\": \"SSL, TLS\", \"Session\": \"NetBIOS, RPC\", \"Transport\": \"TCP, UDP\", \"Network\": \"IP, ICMP\", \"Data Link\": \"Ethernet, PPP\", \"Physical\": \"Cables, Hubs\"}','{\"Application\": \"HTTP, FTP, SMTP\", \"Presentation\": \"SSL, TLS\", \"Session\": \"NetBIOS, RPC\", \"Transport\": \"TCP, UDP\", \"Network\": \"IP, ICMP\", \"Data Link\": \"Ethernet, PPP\", \"Physical\": \"Cables, Hubs\"}','This question tests knowledge of which protocols operate at each layer of the OSI model.',3,2,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(11,17,'hola','drag_drop','[\"asd\",\"asd\",\"asd\",\"asd\"]','[\"asd\",\"asd\",\"asd\",\"asd\"]',NULL,1,0,'2025-04-18 00:46:33','2025-04-18 00:46:33'),
(12,17,'asd','multiple_choice','[\"\",\"\",\"\",\"\",\"asd\",\"asd\",\"asd\",\"asd\"]','[\"\",\"\"]',NULL,2,0,'2025-04-18 01:09:06','2025-04-18 01:09:06'),
(13,17,'asd','single_choice','[\"qwe\",\"qwe\",\"qw\",\"hhhhhhhh\",\"\",\"\",\"\",\"\"]','[\"hhhhhhhh\"]',NULL,1,0,'2025-04-18 01:10:25','2025-04-18 01:10:25');
/*!40000 ALTER TABLE `questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `review` text DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`course_id`),
  KEY `user_id_2` (`user_id`),
  KEY `course_id` (`course_id`),
  KEY `rating` (`rating`),
  KEY `is_approved` (`is_approved`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
INSERT INTO `reviews` VALUES
(1,1,1,5,'Excellent course! The content is well-structured and easy to follow. I passed my CCNA exam on the first attempt thanks to this course.',1,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(2,2,4,4,'Good introduction to networking concepts. Would recommend for beginners.',1,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(3,3,4,5,'Perfect for beginners. Clear explanations and good examples.',1,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(4,3,5,3,'Content is good but could use more practical examples.',1,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(5,4,1,4,'Very comprehensive course. The practice exams were particularly helpful.',1,'2025-04-17 21:20:35','2025-04-17 21:20:35');
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_tokens`
--

DROP TABLE IF EXISTS `user_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `selector` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires` datetime NOT NULL,
  `type` enum('remember_me','password_reset') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `selector` (`selector`),
  KEY `expires` (`expires`),
  KEY `type` (`type`),
  CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_tokens`
--

LOCK TABLES `user_tokens` WRITE;
/*!40000 ALTER TABLE `user_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `username_2` (`username`),
  KEY `email_2` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'johndoe','john@example.com','$2y$12$ff72bOqR6xQ9P7rcSx.J2eI12yo4EKGV7f4hNVKCSE3jGv5fJ/lOC','John','Doe',NULL,NULL,1,NULL,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(2,'janedoe','jane@example.com','$2y$12$ff72bOqR6xQ9P7rcSx.J2eI12yo4EKGV7f4hNVKCSE3jGv5fJ/lOC','Jane','Doe',NULL,NULL,1,NULL,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(3,'bobsmith','bob@example.com','$2y$12$ff72bOqR6xQ9P7rcSx.J2eI12yo4EKGV7f4hNVKCSE3jGv5fJ/lOC','Bob','Smith',NULL,NULL,1,NULL,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(4,'alicesmith','alice@example.com','$2y$12$ff72bOqR6xQ9P7rcSx.J2eI12yo4EKGV7f4hNVKCSE3jGv5fJ/lOC','Alice','Smith',NULL,NULL,1,NULL,'2025-04-17 21:20:35','2025-04-17 21:20:35'),
(5,'mohammedali','mohammed@example.com','$2y$12$ff72bOqR6xQ9P7rcSx.J2eI12yo4EKGV7f4hNVKCSE3jGv5fJ/lOC','Mohammed','Ali',NULL,NULL,1,NULL,'2025-04-17 21:20:35','2025-04-17 21:20:35');
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

-- Dump completed on 2025-04-18  1:20:30
