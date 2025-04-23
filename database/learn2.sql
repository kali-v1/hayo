-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 20, 2025 at 08:15 PM
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
-- Database: `learn2`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` enum('admin','instructor','data_entry') NOT NULL DEFAULT 'data_entry',
  `profile_image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `name`, `role`, `profile_image`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@example.com', '$2y$12$ff72bOqR6xQ9P7rcSx.J2eI12yo4EKGV7f4hNVKCSE3jGv5fJ/lOC', 'Admin User', 'admin', NULL, 1, '2025-04-19 18:39:18', '2025-04-17 21:20:35', '2025-04-19 21:39:18'),
(2, 'instructor', 'instructor@example.com', '$2y$12$ff72bOqR6xQ9P7rcSx.J2eI12yo4EKGV7f4hNVKCSE3jGv5fJ/lOC', 'Instructor User', 'instructor', NULL, 1, NULL, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(3, 'dataentry', 'dataentry@example.com', '$2y$12$ff72bOqR6xQ9P7rcSx.J2eI12yo4EKGV7f4hNVKCSE3jGv5fJ/lOC', 'Data Entry User', 'data_entry', NULL, 1, NULL, '2025-04-17 21:20:35', '2025-04-17 21:20:35');

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `certificate_number` varchar(50) NOT NULL,
  `issue_date` datetime DEFAULT current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `certificates`
--

INSERT INTO `certificates` (`id`, `user_id`, `course_id`, `certificate_number`, `issue_date`, `created_at`, `updated_at`) VALUES
(1, 2, 4, 'CERT-NET-2023-001', '2023-01-15 00:00:00', '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(2, 3, 4, 'CERT-NET-2023-002', '2023-01-20 00:00:00', '2025-04-17 21:20:35', '2025-04-17 21:20:35');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
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
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `title`, `slug`, `company_url`, `description`, `image`, `price`, `is_free`, `is_featured`, `is_published`, `status`, `admin_id`, `created_at`, `updated_at`) VALUES
(1, 'CCNA Certification', 'ccna-certification', NULL, 'Comprehensive course for Cisco Certified Network Associate certification. Learn networking fundamentals, routing and switching, and network security.', NULL, 99.99, 0, 1, 1, 'draft', 1, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(2, 'CCNP Enterprise', 'ccnp-enterprise', NULL, 'Advanced course for Cisco Certified Network Professional Enterprise certification. Master complex enterprise networking concepts.', NULL, 199.99, 0, 1, 1, 'draft', 1, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(3, 'Security+ Certification', 'security-plus-certification', NULL, 'Complete preparation for CompTIA Security+ certification. Learn cybersecurity fundamentals, threats, vulnerabilities, and security controls.', NULL, 79.99, 0, 1, 1, 'draft', 2, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(4, 'Network+ Basics', 'network-plus-basics', NULL, 'Introduction to networking concepts for CompTIA Network+ certification. Free course for beginners.', NULL, 0.00, 1, 0, 1, 'draft', 2, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(5, 'Linux Essentials', 'linux-essentials', NULL, 'Learn Linux fundamentals and prepare for the Linux Essentials certification.', NULL, 49.99, 0, 0, 1, 'draft', 2, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(6, 'Test Course', 'test-coursetest-course', 'http://example.com', 'This is a test course description', '', 0.00, 0, 0, 0, 'draft', 1, '2025-04-17 22:13:07', '2025-04-17 22:13:07'),
(7, 'Test Course with Status Field', 'test-course-with-status-field', '', 'This is a test course to verify that the status field is working correctly.', '', 99.99, 0, 0, 0, 'draft', 2, '2025-04-17 22:13:43', '2025-04-17 22:13:43'),
(8, 'دورة جديدة1', '-', 'https://cisc1o.com', 'دورة جديد1ة', '', 2.00, 0, 0, 0, 'published', 2, '2025-04-17 22:16:39', '2025-04-17 22:17:04'),
(9, 'boo', 'boo', 'https://cisco.com', 'boo', '', 0.00, 1, 0, 0, 'published', 1, '2025-04-18 00:44:54', '2025-04-18 00:44:54');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `enrollment_date` datetime DEFAULT current_timestamp(),
  `completion_date` datetime DEFAULT NULL,
  `is_completed` tinyint(1) DEFAULT 0,
  `progress` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `user_id`, `course_id`, `enrollment_date`, `completion_date`, `is_completed`, `progress`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-04-17 21:20:35', NULL, 0, 30, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(2, 1, 3, '2025-04-17 21:20:35', NULL, 0, 20, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(3, 2, 2, '2025-04-17 21:20:35', NULL, 0, 15, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(4, 2, 4, '2025-04-17 21:20:35', NULL, 1, 100, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(5, 3, 4, '2025-04-17 21:20:35', NULL, 1, 100, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(6, 3, 5, '2025-04-17 21:20:35', NULL, 0, 50, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(7, 4, 1, '2025-04-17 21:20:35', NULL, 0, 75, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(8, 5, 3, '2025-04-17 21:20:35', NULL, 0, 40, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(13, 1, 9, '2025-04-20 21:12:06', NULL, 0, 0, '2025-04-20 21:12:06', '2025-04-20 21:12:06'),
(14, 2, 9, '2025-04-20 21:12:06', NULL, 0, 0, '2025-04-20 21:12:06', '2025-04-20 21:12:06');

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `duration_minutes` int(11) NOT NULL DEFAULT 60,
  `passing_score` int(11) NOT NULL DEFAULT 70,
  `is_free` tinyint(1) DEFAULT 0,
  `is_published` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `title`, `slug`, `description`, `course_id`, `duration_minutes`, `passing_score`, `is_free`, `is_published`, `created_at`, `updated_at`) VALUES
(1, 'CCNA Practice Exam 1', 'ccna-practice-exam-1', 'Practice exam covering CCNA fundamentals and network basics.', 1, 90, 70, 0, 1, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(2, 'CCNA Practice Exam 2', 'ccna-practice-exam-2', 'Advanced practice exam covering CCNA routing and switching concepts.', 1, 90, 70, 0, 1, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(3, 'CCNP Enterprise Core Exam', 'ccnp-enterprise-core-exam', 'Practice exam for CCNP Enterprise Core (350-401 ENCOR).', 2, 120, 75, 0, 1, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(4, 'Security+ Practice Test', 'security-plus-practice-test', 'Comprehensive practice test for CompTIA Security+ certification.', 3, 90, 75, 0, 1, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(5, 'Network+ Sample Questions', 'network-plus-sample-questions', 'Free sample questions for Network+ certification.', 4, 30, 70, 1, 1, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(6, 'Linux Essentials Quiz', 'linux-essentials-quiz', 'Quick quiz to test your Linux knowledge.', 5, 45, 70, 0, 1, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(7, 'Updated Test Exam 123', 'updated-test-exam-123', 'This is an updated test exam description for CCNA certification.', 1, 60, 70, 0, 1, '2025-04-17 21:38:08', '2025-04-17 21:38:36'),
(8, 'Test Exam 123', 'test-exam-123', 'This is a test exam description for CCNA certification.', 1, 60, 70, 0, 0, '2025-04-17 21:38:55', '2025-04-17 21:58:36'),
(11, 'Test Exam with Course Recommendations', 'test-exam-with-course-recommendations', 'This is a test exam to demonstrate course recommendations functionality', 1, 60, 65, 0, 0, '2025-04-17 22:00:21', '2025-04-17 22:00:37'),
(12, 'تجربه', 'تجربه', 'تجربه', 1, 60, 70, 0, 1, '2025-04-17 22:02:32', '2025-04-17 22:02:32'),
(13, 'Test Exam', 'test-exam', 'This is a test exam description', 7, 60, 70, 0, 0, '2025-04-17 22:14:19', '2025-04-17 22:14:19'),
(14, 'T1est Exam for Verification', 't1est-exam-for-verification', 'This is a test exam to verify the admin dashboard functionality', 8, 60, 60, 0, 1, '2025-04-17 22:14:51', '2025-04-17 22:17:33'),
(15, '1', '1', '1', 8, 60, 70, 0, 1, '2025-04-17 22:17:57', '2025-04-17 22:17:57'),
(16, '22', '22', '22', NULL, 60, 70, 0, 1, '2025-04-18 00:35:06', '2025-04-18 00:35:06'),
(17, 'b1oo', 'b1oo', 'boo', NULL, 60, 70, 0, 0, '2025-04-18 00:45:08', '2025-04-18 00:45:33'),
(18, 'asd', 'asd', 'asd', NULL, 60, 70, 0, 0, '2025-04-18 15:57:40', '2025-04-18 15:57:40');

-- --------------------------------------------------------

--
-- Table structure for table `exam_attempts`
--

CREATE TABLE `exam_attempts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `started_at` datetime NOT NULL,
  `completed_at` datetime DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `is_passed` tinyint(1) DEFAULT 0,
  `answers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`answers`)),
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_attempts`
--

INSERT INTO `exam_attempts` (`id`, `user_id`, `exam_id`, `started_at`, `completed_at`, `score`, `is_passed`, `answers`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2023-01-15 10:00:00', '2023-01-15 11:15:00', 85, 1, '{\"1\": \"192.168.1.129 - 192.168.1.254\", \"2\": \"TCP\", \"3\": \"To resolve IP addresses to MAC addresses\"}', '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(2, 1, 2, '2023-01-20 14:00:00', '2023-01-20 15:30:00', 75, 1, '{\"1\": \"show arp\"}', '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(3, 2, 5, '2023-01-10 09:30:00', '2023-01-10 10:00:00', 90, 1, '{\"1\": [\"10.0.0.1\", \"172.16.0.1\", \"224.0.0.1\"]}', '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(4, 3, 5, '2023-01-12 16:00:00', '2023-01-12 16:25:00', 80, 1, '{\"1\": [\"10.0.0.1\", \"172.16.0.1\"]}', '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(5, 3, 6, '2023-01-25 11:00:00', '2023-01-25 11:40:00', 65, 0, '{\"1\": {\"ls\": \"Change directory\", \"cd\": \"List directory contents\", \"pwd\": \"Print working directory\", \"mkdir\": \"Create a new directory\", \"rm\": \"Remove files or directories\"}}', '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(6, 4, 1, '2023-01-18 13:00:00', '2023-01-18 14:15:00', 90, 1, '{\"1\": \"192.168.1.129 - 192.168.1.254\", \"2\": \"TCP\", \"3\": \"To resolve IP addresses to MAC addresses\"}', '2025-04-17 21:20:35', '2025-04-17 21:20:35');

-- --------------------------------------------------------

--
-- Table structure for table `exam_course_recommendations`
--

CREATE TABLE `exam_course_recommendations` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `priority` int(11) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_course_recommendations`
--

INSERT INTO `exam_course_recommendations` (`id`, `exam_id`, `course_id`, `priority`, `created_at`, `updated_at`) VALUES
(1, 8, 1, 1, '2025-04-17 21:58:36', '2025-04-17 21:58:36'),
(2, 8, 3, 2, '2025-04-17 21:58:36', '2025-04-17 21:58:36'),
(3, 11, 2, 1, '2025-04-17 22:00:37', '2025-04-17 22:00:37'),
(4, 11, 3, 2, '2025-04-17 22:00:37', '2025-04-17 22:00:37'),
(5, 14, 1, 1, '2025-04-17 22:17:33', '2025-04-17 22:17:33'),
(6, 16, 1, 1, '2025-04-18 00:35:06', '2025-04-18 00:35:06'),
(7, 16, 5, 2, '2025-04-18 00:35:06', '2025-04-18 00:35:06'),
(10, 17, 5, 1, '2025-04-18 00:45:33', '2025-04-18 00:45:33'),
(11, 18, 5, 1, '2025-04-18 12:57:40', '2025-04-18 12:57:40');

-- --------------------------------------------------------

--
-- Table structure for table `instructor_earnings`
--

CREATE TABLE `instructor_earnings` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `earning_type` varchar(50) DEFAULT 'course_sale',
  `earning_value` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `instructor_earnings`
--

INSERT INTO `instructor_earnings` (`id`, `admin_id`, `course_id`, `amount`, `created_at`, `earning_type`, `earning_value`) VALUES
(1, 2, 3, 20.00, '2025-04-20 18:09:33', 'percentage', 20.00);

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `content` text DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `duration` int(11) DEFAULT 0 COMMENT 'Duration in minutes',
  `order_number` int(11) DEFAULT 0,
  `is_free` tinyint(1) DEFAULT 0,
  `status` enum('draft','published') DEFAULT 'draft',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`id`, `course_id`, `title`, `description`, `content`, `video_url`, `duration`, `order_number`, `is_free`, `status`, `created_at`, `updated_at`) VALUES
(6, 9, 'مقدمة في الدورة', 'درس تمهيدي للدورة', '<p>محتوى الدرس الأول</p>', 'https://www.youtube.com/watch?v=example1', 15, 1, 1, 'published', '2025-04-20 21:14:42', '2025-04-20 21:14:42'),
(7, 9, 'أساسيات البرمجة', 'تعلم أساسيات البرمجة', '<p>محتوى الدرس الثاني</p>', 'https://www.youtube.com/watch?v=example2', 25, 2, 0, 'published', '2025-04-20 21:14:42', '2025-04-20 21:14:42'),
(8, 9, 'البرمجة المتقدمة', 'تعلم البرمجة المتقدمة', '<p>محتوى الدرس الثالث</p>', 'https://www.youtube.com/watch?v=example3', 30, 3, 0, 'draft', '2025-04-20 21:14:42', '2025-04-20 21:14:42'),
(9, 3, 'مقدمة في الأمان السيبراني', 'مقدمة في الأمان السيبراني', '<p>محتوى الدرس الأول</p>', 'https://www.youtube.com/watch?v=security1', 20, 1, 1, 'published', '2025-04-20 21:14:42', '2025-04-20 21:14:42'),
(10, 3, 'تقنيات الحماية', 'تعلم تقنيات الحماية', '<p>محتوى الدرس الثاني</p>', 'https://www.youtube.com/watch?v=security2', 35, 2, 0, 'published', '2025-04-20 21:14:42', '2025-04-20 21:14:42');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `status` enum('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
  `payment_date` datetime DEFAULT current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `user_id`, `course_id`, `amount`, `payment_method`, `transaction_id`, `status`, `payment_date`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 99.99, 'credit_card', 'TXN123456', 'completed', '2025-04-17 21:20:35', '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(2, 1, 3, 79.99, 'paypal', 'PP987654', 'completed', '2025-04-17 21:20:35', '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(3, 2, 2, 199.99, 'credit_card', 'TXN789012', 'completed', '2025-04-17 21:20:35', '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(4, 3, 5, 49.99, 'credit_card', 'TXN345678', 'completed', '2025-04-17 21:20:35', '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(5, 4, 1, 99.99, 'paypal', 'PP456789', 'completed', '2025-04-17 21:20:35', '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(10, 1, 9, 10.00, 'credit_card', 'TXN123456', 'completed', '2025-04-20 21:12:46', '2025-04-20 21:12:46', '2025-04-20 21:12:46'),
(11, 2, 9, 10.00, 'paypal', 'TXN789012', 'completed', '2025-04-20 21:12:46', '2025-04-20 21:12:46', '2025-04-20 21:12:46');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('single_choice','multiple_choice','drag_drop') NOT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`options`)),
  `correct_answer` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`correct_answer`)),
  `explanation` text DEFAULT NULL,
  `points` int(11) NOT NULL DEFAULT 1,
  `order_number` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `exam_id`, `question_text`, `question_type`, `options`, `correct_answer`, `explanation`, `points`, `order_number`, `created_at`, `updated_at`) VALUES
(1, 1, 'Which of the following is the valid host range for the subnet 192.168.1.128/25?', 'single_choice', '[\"192.168.1.129 - 192.168.1.254\", \"192.168.1.1 - 192.168.1.126\", \"192.168.1.129 - 192.168.1.255\", \"192.168.1.128 - 192.168.1.255\"]', '\"192.168.1.129 - 192.168.1.254\"', 'For a /25 subnet with network address 192.168.1.128, the valid host range is from 192.168.1.129 to 192.168.1.254. The address 192.168.1.255 is the broadcast address.', 1, 1, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(2, 1, 'Which protocol operates at the Transport layer of the OSI model?', 'single_choice', '[\"HTTP\", \"IP\", \"Ethernet\", \"TCP\"]', '\"TCP\"', 'TCP (Transmission Control Protocol) operates at the Transport layer (Layer 4) of the OSI model.', 1, 2, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(3, 1, 'What is the purpose of ARP?', 'single_choice', '[\"To resolve domain names to IP addresses\", \"To resolve IP addresses to MAC addresses\", \"To assign IP addresses dynamically\", \"To encrypt network traffic\"]', '\"To resolve IP addresses to MAC addresses\"', 'ARP (Address Resolution Protocol) is used to map IP network addresses to MAC addresses in a local network.', 1, 3, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(4, 2, 'Which command would you use to verify the Layer 2 to Layer 3 mapping on a Cisco router?', 'single_choice', '[\"show ip route\", \"show interfaces\", \"show arp\", \"show ip interface brief\"]', '\"show arp\"', 'The \"show arp\" command displays the ARP table, which shows the mapping between IP addresses (Layer 3) and MAC addresses (Layer 2).', 1, 1, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(5, 3, 'Which technology allows for the creation of multiple virtual routers within a single physical router?', 'single_choice', '[\"VTP\", \"VRF\", \"HSRP\", \"EIGRP\"]', '\"VRF\"', 'VRF (Virtual Routing and Forwarding) allows for multiple routing tables to exist in a router at the same time, effectively creating multiple virtual routers.', 2, 1, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(6, 4, 'Which of the following are symmetric encryption algorithms? (Select all that apply)', 'multiple_choice', '[\"AES\", \"RSA\", \"DES\", \"ECC\", \"3DES\"]', '[\"AES\", \"DES\", \"3DES\"]', 'AES, DES, and 3DES are symmetric encryption algorithms. RSA and ECC are asymmetric (public key) encryption algorithms.', 2, 1, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(7, 4, 'Which of the following are common security controls? (Select all that apply)', 'multiple_choice', '[\"Firewall\", \"Antivirus\", \"Social engineering\", \"Access control\", \"Phishing\"]', '[\"Firewall\", \"Antivirus\", \"Access control\"]', 'Firewalls, antivirus software, and access control are security controls. Social engineering and phishing are attack methods, not controls.', 2, 2, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(8, 5, 'Which of the following are valid IPv4 addresses? (Select all that apply)', 'multiple_choice', '[\"192.168.1.256\", \"10.0.0.1\", \"172.16.0.1\", \"256.0.0.1\", \"224.0.0.1\"]', '[\"10.0.0.1\", \"172.16.0.1\", \"224.0.0.1\"]', 'Valid IPv4 addresses must have each octet between 0 and 255. 192.168.1.256 and 256.0.0.1 are invalid because they contain octets greater than 255.', 1, 1, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(9, 6, 'Match the Linux commands with their descriptions.', 'drag_drop', '{\"ls\": \"List directory contents\", \"cd\": \"Change directory\", \"pwd\": \"Print working directory\", \"mkdir\": \"Create a new directory\", \"rm\": \"Remove files or directories\"}', '{\"ls\": \"List directory contents\", \"cd\": \"Change directory\", \"pwd\": \"Print working directory\", \"mkdir\": \"Create a new directory\", \"rm\": \"Remove files or directories\"}', 'These are basic Linux commands and their functions.', 3, 1, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(10, 3, 'Match the OSI layers with the correct protocols.', 'drag_drop', '{\"Application\": \"HTTP, FTP, SMTP\", \"Presentation\": \"SSL, TLS\", \"Session\": \"NetBIOS, RPC\", \"Transport\": \"TCP, UDP\", \"Network\": \"IP, ICMP\", \"Data Link\": \"Ethernet, PPP\", \"Physical\": \"Cables, Hubs\"}', '{\"Application\": \"HTTP, FTP, SMTP\", \"Presentation\": \"SSL, TLS\", \"Session\": \"NetBIOS, RPC\", \"Transport\": \"TCP, UDP\", \"Network\": \"IP, ICMP\", \"Data Link\": \"Ethernet, PPP\", \"Physical\": \"Cables, Hubs\"}', 'This question tests knowledge of which protocols operate at each layer of the OSI model.', 3, 2, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(11, 17, 'hola', 'drag_drop', '[\"asd\",\"asd\",\"asd\",\"asd\"]', '[\"asd\",\"asd\",\"asd\",\"asd\"]', NULL, 1, 0, '2025-04-18 00:46:33', '2025-04-18 00:46:33'),
(12, 17, 'asd', 'multiple_choice', '[\"\",\"\",\"\",\"\",\"asd\",\"asd\",\"asd\",\"asd\"]', '[\"\",\"\"]', NULL, 2, 0, '2025-04-18 01:09:06', '2025-04-18 01:09:06'),
(13, 17, 'asd', 'single_choice', '[\"qwe\",\"qwe\",\"qw\",\"hhhhhhhh\",\"\",\"\",\"\",\"\"]', '[\"hhhhhhhh\"]', NULL, 1, 0, '2025-04-18 01:10:25', '2025-04-18 01:10:25'),
(14, 13, 'testaaaaa', 'multiple_choice', '[\"1\",\"2\",\"3\",\"4\"]', '[\"2\",\"3\"]', NULL, 1, 0, '2025-04-19 21:39:51', '2025-04-19 21:41:28'),
(15, 8, 'TEST', 'multiple_choice', '[\"hola1\",\"hola2\",\"hola3\",\"hola4\"]', '[\"hola3\",\"hola4\"]', NULL, 1, 0, '2025-04-19 21:46:23', '2025-04-19 21:46:57');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `review` text DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `course_id`, `rating`, `review`, `is_approved`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 5, 'Excellent course! The content is well-structured and easy to follow. I passed my CCNA exam on the first attempt thanks to this course.', 1, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(2, 2, 4, 4, 'Good introduction to networking concepts. Would recommend for beginners.', 1, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(3, 3, 4, 5, 'Perfect for beginners. Clear explanations and good examples.', 1, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(4, 3, 5, 3, 'Content is good but could use more practical examples.', 1, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(5, 4, 1, 4, 'Very comprehensive course. The practice exams were particularly helpful.', 1, '2025-04-17 21:20:35', '2025-04-17 21:20:35');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
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
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `first_name`, `last_name`, `profile_image`, `bio`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'johndoe', 'john@example.com', '$2y$12$ff72bOqR6xQ9P7rcSx.J2eI12yo4EKGV7f4hNVKCSE3jGv5fJ/lOC', 'John', 'Doe', NULL, NULL, 1, NULL, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(2, 'janedoe', 'jane@example.com', '$2y$12$ff72bOqR6xQ9P7rcSx.J2eI12yo4EKGV7f4hNVKCSE3jGv5fJ/lOC', 'Jane', 'Doe', NULL, NULL, 1, NULL, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(3, 'bobsmith', 'bob@example.com', '$2y$12$ff72bOqR6xQ9P7rcSx.J2eI12yo4EKGV7f4hNVKCSE3jGv5fJ/lOC', 'Bob', 'Smith', NULL, NULL, 1, NULL, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(4, 'alicesmith', 'alice@example.com', '$2y$12$ff72bOqR6xQ9P7rcSx.J2eI12yo4EKGV7f4hNVKCSE3jGv5fJ/lOC', 'Alice', 'Smith', NULL, NULL, 1, NULL, '2025-04-17 21:20:35', '2025-04-17 21:20:35'),
(5, 'mohammedali', 'mohammed@example.com', '$2y$12$ff72bOqR6xQ9P7rcSx.J2eI12yo4EKGV7f4hNVKCSE3jGv5fJ/lOC', 'Mohammed', 'Ali', NULL, NULL, 1, NULL, '2025-04-17 21:20:35', '2025-04-17 21:20:35');

-- --------------------------------------------------------

--
-- Table structure for table `user_tokens`
--

CREATE TABLE `user_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `selector` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires` datetime NOT NULL,
  `type` enum('remember_me','password_reset') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `action` (`action`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `username_2` (`username`),
  ADD KEY `email_2` (`email`),
  ADD KEY `role` (`role`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `certificate_number` (`certificate_number`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `certificate_number_2` (`certificate_number`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `slug_2` (`slug`),
  ADD KEY `is_free` (`is_free`),
  ADD KEY `is_featured` (`is_featured`),
  ADD KEY `is_published` (`is_published`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`course_id`),
  ADD KEY `user_id_2` (`user_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `is_completed` (`is_completed`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `slug_2` (`slug`),
  ADD KEY `is_free` (`is_free`),
  ADD KEY `is_published` (`is_published`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `is_passed` (`is_passed`);

--
-- Indexes for table `exam_course_recommendations`
--
ALTER TABLE `exam_course_recommendations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_exam_course` (`exam_id`,`course_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `instructor_earnings`
--
ALTER TABLE `instructor_earnings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `question_type` (`question_type`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`course_id`),
  ADD KEY `user_id_2` (`user_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `rating` (`rating`),
  ADD KEY `is_approved` (`is_approved`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `username_2` (`username`),
  ADD KEY `email_2` (`email`);

--
-- Indexes for table `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `selector` (`selector`),
  ADD KEY `expires` (`expires`),
  ADD KEY `type` (`type`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `exam_course_recommendations`
--
ALTER TABLE `exam_course_recommendations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `instructor_earnings`
--
ALTER TABLE `instructor_earnings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_tokens`
--
ALTER TABLE `user_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `activity_logs_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `certificates_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  ADD CONSTRAINT `exam_attempts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_attempts_ibfk_2` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_course_recommendations`
--
ALTER TABLE `exam_course_recommendations`
  ADD CONSTRAINT `exam_course_recommendations_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_course_recommendations_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `instructor_earnings`
--
ALTER TABLE `instructor_earnings`
  ADD CONSTRAINT `instructor_earnings_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`),
  ADD CONSTRAINT `instructor_earnings_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);

--
-- Constraints for table `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `lessons_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
