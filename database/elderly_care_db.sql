-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 09, 2026 at 07:48 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `elderly_care_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `caregiver_assignments`
--

DROP TABLE IF EXISTS `caregiver_assignments`;
CREATE TABLE IF NOT EXISTS `caregiver_assignments` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `caregiver_id` int(10) UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `assigned_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_assignment` (`user_id`,`caregiver_id`),
  KEY `idx_caregiver` (`caregiver_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

DROP TABLE IF EXISTS `chat_messages`;
CREATE TABLE IF NOT EXISTS `chat_messages` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sender_id` int(10) UNSIGNED NOT NULL,
  `receiver_id` int(10) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `message_type` enum('text','image','alert') NOT NULL DEFAULT 'text',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `sent_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_conversation` (`sender_id`,`receiver_id`,`sent_at`),
  KEY `idx_receiver_unread` (`receiver_id`,`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `health_readings`
--

DROP TABLE IF EXISTS `health_readings`;
CREATE TABLE IF NOT EXISTS `health_readings` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `reading_type` enum('blood_pressure','blood_sugar','weight','temperature','heart_rate','oxygen_level') NOT NULL,
  `value` decimal(8,2) DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `systolic` decimal(5,1) DEFAULT NULL,
  `diastolic` decimal(5,1) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `recorded_at` datetime NOT NULL DEFAULT current_timestamp(),
  `recorded_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `recorded_by` (`recorded_by`),
  KEY `idx_user_reading` (`user_id`,`reading_type`,`recorded_at`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medication_logs`
--

DROP TABLE IF EXISTS `medication_logs`;
CREATE TABLE IF NOT EXISTS `medication_logs` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `medicine_id` int(10) UNSIGNED NOT NULL,
  `scheduled_time` datetime NOT NULL,
  `taken_time` datetime DEFAULT NULL,
  `status` enum('taken','missed','pending','skipped') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `logged_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `logged_by` (`logged_by`),
  KEY `idx_medicine_date` (`medicine_id`,`scheduled_time`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medicines`
--

DROP TABLE IF EXISTS `medicines`;
CREATE TABLE IF NOT EXISTS `medicines` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `medicine_name` varchar(150) NOT NULL,
  `dosage` varchar(80) NOT NULL,
  `frequency` varchar(80) NOT NULL,
  `scheduled_times` varchar(200) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `prescribing_doctor` varchar(120) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `added_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `stock_count` int(11) DEFAULT 30,
  PRIMARY KEY (`id`),
  KEY `added_by` (`added_by`),
  KEY `idx_user_medicine` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `routines`
--

DROP TABLE IF EXISTS `routines`;
CREATE TABLE IF NOT EXISTS `routines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `scheduled_time` time NOT NULL,
  `active` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `routines`
--

INSERT INTO `routines` (`id`, `name`, `scheduled_time`, `active`) VALUES
(1, 'Drink Water', '08:00:00', 1),
(2, 'Breakfast', '08:30:00', 1),
(3, 'Walk', '10:00:00', 1),
(4, 'Lunch', '13:00:00', 1),
(5, 'Hydration', '15:30:00', 1),
(6, 'Dinner', '19:30:00', 1),
(7, 'Sleep', '22:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `routine_logs`
--

DROP TABLE IF EXISTS `routine_logs`;
CREATE TABLE IF NOT EXISTS `routine_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `routine_id` int(11) NOT NULL,
  `scheduled_time` datetime NOT NULL,
  `status` enum('pending','done') DEFAULT 'pending',
  `completed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_routine_day` (`user_id`,`routine_id`,`scheduled_time`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `routine_logs`
--

INSERT INTO `routine_logs` (`id`, `user_id`, `routine_id`, `scheduled_time`, `status`, `completed_at`, `created_at`) VALUES
(1, 3, 1, '2026-03-09 08:00:00', 'done', '2026-03-09 15:33:10', '2026-03-09 15:27:44'),
(2, 3, 2, '2026-03-09 08:30:00', 'done', '2026-03-09 23:33:09', '2026-03-09 15:27:44'),
(3, 3, 3, '2026-03-09 10:00:00', 'done', '2026-03-09 23:33:10', '2026-03-09 15:27:44'),
(4, 3, 4, '2026-03-09 13:00:00', 'done', '2026-03-09 23:33:11', '2026-03-09 15:27:44'),
(5, 3, 5, '2026-03-09 15:30:00', 'done', '2026-03-09 23:33:12', '2026-03-09 15:27:44'),
(6, 3, 6, '2026-03-09 19:30:00', 'done', '2026-03-09 23:33:13', '2026-03-09 15:27:44'),
(7, 3, 7, '2026-03-09 22:00:00', 'done', '2026-03-09 23:33:14', '2026-03-09 15:27:44'),
(8, 4, 1, '2026-03-09 08:00:00', 'done', '2026-03-09 20:42:17', '2026-03-09 20:35:24'),
(9, 4, 2, '2026-03-09 08:30:00', 'done', '2026-03-09 20:42:18', '2026-03-09 20:35:24'),
(10, 4, 3, '2026-03-09 10:00:00', 'done', '2026-03-09 22:48:35', '2026-03-09 20:35:24'),
(11, 4, 4, '2026-03-09 13:00:00', 'pending', NULL, '2026-03-09 20:35:24'),
(12, 4, 5, '2026-03-09 15:30:00', 'pending', NULL, '2026-03-09 20:35:24'),
(13, 4, 6, '2026-03-09 19:30:00', 'pending', NULL, '2026-03-09 20:35:24'),
(14, 4, 7, '2026-03-09 22:00:00', 'pending', NULL, '2026-03-09 20:35:24'),
(15, 42, 1, '2026-03-09 08:00:00', 'pending', NULL, '2026-03-09 22:49:56'),
(16, 42, 2, '2026-03-09 08:30:00', 'pending', NULL, '2026-03-09 22:49:56'),
(17, 42, 3, '2026-03-09 10:00:00', 'pending', NULL, '2026-03-09 22:49:56'),
(18, 42, 4, '2026-03-09 13:00:00', 'pending', NULL, '2026-03-09 22:49:56'),
(19, 42, 5, '2026-03-09 15:30:00', 'pending', NULL, '2026-03-09 22:49:56'),
(20, 42, 6, '2026-03-09 19:30:00', 'pending', NULL, '2026-03-09 22:49:56'),
(21, 42, 7, '2026-03-09 22:00:00', 'pending', NULL, '2026-03-09 22:49:56');

-- --------------------------------------------------------

--
-- Table structure for table `sos_alerts`
--

DROP TABLE IF EXISTS `sos_alerts`;
CREATE TABLE IF NOT EXISTS `sos_alerts` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `alert_type` enum('sos','fall','medication','health') NOT NULL DEFAULT 'sos',
  `message` text NOT NULL,
  `severity` enum('low','medium','high') NOT NULL DEFAULT 'high',
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `status` enum('active','acknowledged','resolved') NOT NULL DEFAULT 'active',
  `triggered_at` datetime NOT NULL DEFAULT current_timestamp(),
  `resolved_at` datetime DEFAULT NULL,
  `resolution_notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_sos` (`user_id`,`triggered_at`),
  KEY `idx_status` (`status`),
  KEY `idx_sos_status` (`status`,`triggered_at`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sos_alerts`
--

INSERT INTO `sos_alerts` (`id`, `user_id`, `alert_type`, `message`, `severity`, `latitude`, `longitude`, `status`, `triggered_at`, `resolved_at`, `resolution_notes`) VALUES
(5, 3, 'sos', 'Emergency button pressed', 'high', 10.8505000, 76.2711000, 'active', '2026-03-08 09:24:59', NULL, NULL),
(6, 4, 'fall', 'Possible fall detected in living room', 'high', 10.8506000, 76.2712000, 'active', '2026-03-08 09:24:59', NULL, NULL),
(7, 3, 'medication', 'Morning medicine missed', 'high', NULL, NULL, 'active', '2026-03-08 09:24:59', NULL, NULL),
(8, 42, 'sos', 'Emergency button pressed', 'high', 10.8507000, 76.2713000, 'active', '2026-03-08 11:50:39', NULL, NULL),
(9, 42, 'fall', 'Possible fall detected in living room', 'high', 10.8507000, 76.2713000, 'active', '2026-03-08 11:50:39', NULL, NULL),
(10, 4, 'fall', 'Possible fall detected in living room', 'high', 10.8508000, 76.2714000, 'resolved', '2026-03-08 11:59:58', '2026-03-08 19:42:53', NULL),
(11, 4, 'sos', 'Emergency button pressed', 'high', 10.8508000, 76.2714000, 'active', '2026-03-08 11:59:58', NULL, NULL),
(12, 3, 'sos', 'Emergency button pressed by User', 'high', NULL, NULL, 'active', '2026-03-08 19:47:50', NULL, NULL),
(13, 3, 'sos', 'Emergency button pressed by User', 'high', NULL, NULL, 'active', '2026-03-09 12:09:31', NULL, NULL),
(14, 3, 'sos', 'Emergency button pressed by User', 'high', NULL, NULL, 'resolved', '2026-03-09 12:30:28', '2026-03-09 12:31:38', NULL),
(15, 3, 'sos', 'Emergency button pressed by User', 'high', NULL, NULL, 'resolved', '2026-03-09 12:48:09', '2026-03-09 12:49:50', NULL),
(16, 4, 'sos', 'Emergency button pressed by User', 'high', NULL, NULL, 'resolved', '2026-03-09 20:42:08', '2026-03-09 20:43:45', NULL),
(17, 4, 'sos', 'Emergency button pressed by User', 'high', NULL, NULL, 'active', '2026-03-09 21:54:11', NULL, NULL),
(18, 4, 'sos', 'Emergency button pressed by User', 'high', NULL, NULL, 'active', '2026-03-09 21:55:16', NULL, NULL),
(19, 4, 'sos', 'Emergency button pressed by User', 'high', NULL, NULL, 'active', '2026-03-09 22:00:14', NULL, NULL),
(20, 4, 'sos', 'Emergency button pressed by User', 'high', NULL, NULL, 'active', '2026-03-09 22:05:27', NULL, NULL),
(21, 4, 'sos', 'Emergency button pressed by User', 'high', NULL, NULL, 'active', '2026-03-09 22:07:04', NULL, NULL),
(22, 4, 'sos', 'Emergency button pressed by User', 'high', NULL, NULL, 'active', '2026-03-09 22:08:45', NULL, NULL),
(23, 4, 'sos', 'Emergency button pressed by User', 'high', NULL, NULL, 'active', '2026-03-09 22:12:55', NULL, NULL),
(24, 4, 'sos', 'Emergency button pressed by User', 'high', NULL, NULL, 'active', '2026-03-09 22:45:23', NULL, NULL),
(25, 4, 'sos', 'Emergency button pressed by User', 'high', NULL, NULL, 'active', '2026-03-09 22:45:46', NULL, NULL),
(26, 4, 'sos', 'Emergency button pressed by User', 'high', NULL, NULL, 'active', '2026-03-09 22:45:57', NULL, NULL),
(27, 4, 'sos', 'Emergency button pressed by User', 'high', NULL, NULL, 'active', '2026-03-09 22:46:22', NULL, NULL),
(28, 4, 'sos', 'Emergency button pressed by User', 'high', NULL, NULL, 'active', '2026-03-09 22:46:25', NULL, NULL),
(29, 4, 'sos', 'Emergency button pressed by User', 'high', NULL, NULL, 'active', '2026-03-09 22:46:47', NULL, NULL),
(30, 42, 'sos', 'Emergency button pressed by User', 'high', NULL, NULL, 'active', '2026-03-09 22:50:00', NULL, NULL),
(31, 3, 'sos', 'Emergency button pressed by User', 'high', NULL, NULL, 'active', '2026-03-09 22:50:55', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sos_notifications`
--

DROP TABLE IF EXISTS `sos_notifications`;
CREATE TABLE IF NOT EXISTS `sos_notifications` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `alert_id` int(10) UNSIGNED NOT NULL,
  `caregiver_id` int(10) UNSIGNED NOT NULL,
  `notified_at` datetime DEFAULT NULL,
  `is_acknowledged` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_alert_cg` (`alert_id`,`caregiver_id`),
  KEY `caregiver_id` (`caregiver_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sos_notifications`
--

INSERT INTO `sos_notifications` (`id`, `alert_id`, `caregiver_id`, `notified_at`, `is_acknowledged`) VALUES
(6, 8, 5, '2026-03-08 12:00:17', 0),
(7, 9, 5, '2026-03-08 12:00:17', 0),
(8, 12, 2, '2026-03-08 19:47:50', 0),
(9, 13, 2, '2026-03-09 12:09:31', 0),
(10, 14, 2, '2026-03-09 12:30:28', 0),
(11, 15, 2, '2026-03-09 12:48:09', 0),
(12, 16, 5, '2026-03-09 20:42:08', 0),
(13, 17, 5, '2026-03-09 21:54:11', 0),
(14, 18, 5, '2026-03-09 21:55:16', 0),
(15, 19, 5, '2026-03-09 22:00:14', 0),
(16, 20, 5, '2026-03-09 22:05:27', 0),
(17, 21, 5, '2026-03-09 22:07:04', 0),
(18, 22, 5, '2026-03-09 22:08:45', 0),
(19, 23, 5, '2026-03-09 22:12:55', 0),
(20, 24, 5, '2026-03-09 22:45:23', 0),
(21, 25, 5, '2026-03-09 22:45:46', 0),
(22, 26, 5, '2026-03-09 22:45:57', 0),
(23, 27, 5, '2026-03-09 22:46:22', 0),
(24, 28, 5, '2026-03-09 22:46:25', 0),
(25, 29, 5, '2026-03-09 22:46:47', 0),
(26, 31, 2, '2026-03-09 22:50:55', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `role` enum('user','caregiver','admin') NOT NULL DEFAULT 'user',
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `idx_role` (`role`),
  KEY `idx_username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `email`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES
(2, 'caregiver1', '1234', 'Sarah Johnson', 'caregiver', 'sarah@elderlycare.local', '8590949239', 1, '2026-03-06 19:51:21', '2026-03-09 21:53:26'),
(3, 'user1', '1234', 'Robert Thompson', 'user', 'robert@elderlycare.local', '0000000003', 1, '2026-03-06 19:51:21', '2026-03-06 21:11:40'),
(4, 'user2', '1234', 'Emma Williams', 'user', 'emma@elderlycare.local', '0000000004', 1, '2026-03-08 09:20:30', '2026-03-08 09:20:30'),
(5, 'caregiver2', '1234', 'John Smith', 'caregiver', 'john@elderlycare.local', '8590949239', 1, '2026-03-08 09:20:30', '2026-03-09 22:35:09'),
(6, 'admin2', '1234', 'Jane Doe', 'admin', 'jane@elderlycare.local', '0000000006', 1, '2026-03-08 09:20:30', '2026-03-08 09:20:30'),
(42, 'mj345', '1234', 'Mary Jane', 'user', NULL, '9870658342', 1, '2026-03-08 11:21:00', '2026-03-08 11:21:00');

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

DROP TABLE IF EXISTS `user_profiles`;
CREATE TABLE IF NOT EXISTS `user_profiles` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `emergency_contact_name` varchar(100) DEFAULT NULL,
  `emergency_contact_phone` varchar(20) DEFAULT NULL,
  `blood_type` varchar(3) DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `medical_notes` text DEFAULT NULL,
  `profile_photo_url` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`id`, `user_id`, `date_of_birth`, `gender`, `address`, `emergency_contact_name`, `emergency_contact_phone`, `blood_type`, `allergies`, `medical_notes`, `profile_photo_url`, `created_at`, `updated_at`) VALUES
(1, 3, '1950-04-15', 'male', '123 Main Street, City, Country', 'Sarah Johnson', '0000000002', 'O+', 'Diabetic, hypertension', 'Regular monitoring required.', NULL, '2026-03-06 19:51:21', '2026-03-08 09:26:16'),
(6, 42, '1955-06-20', 'female', '456 Elm Street, City, Country', 'John Doe', '9870658343', 'A+', 'Penicillin', 'Requires daily monitoring of blood pressure and sugar.', NULL, '2026-03-08 11:49:32', '2026-03-08 11:49:32'),
(7, 4, '1958-11-12', 'female', '789 Pine Street, City, Country', 'Michael Williams', '9870658345', 'B+', 'None', 'Requires weekly blood pressure check.', NULL, '2026-03-08 11:57:41', '2026-03-08 11:57:41');

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles_backup`
--

DROP TABLE IF EXISTS `user_profiles_backup`;
CREATE TABLE IF NOT EXISTS `user_profiles_backup` (
  `id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `user_id` int(10) UNSIGNED NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `address` text DEFAULT NULL,
  `emergency_contact_name` varchar(120) DEFAULT NULL,
  `emergency_contact_phone` varchar(20) DEFAULT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `medical_notes` text DEFAULT NULL,
  `profile_photo_url` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_profiles_backup`
--

INSERT INTO `user_profiles_backup` (`id`, `user_id`, `date_of_birth`, `gender`, `address`, `emergency_contact_name`, `emergency_contact_phone`, `blood_type`, `allergies`, `medical_notes`, `profile_photo_url`, `created_at`, `updated_at`) VALUES
(1, 3, '1950-04-15', 'male', NULL, NULL, NULL, 'O+', NULL, 'Diabetic, hypertension history. Regular monitoring required.', NULL, '2026-03-06 19:51:21', '2026-03-06 19:51:21');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `caregiver_assignments`
--
ALTER TABLE `caregiver_assignments`
  ADD CONSTRAINT `caregiver_assignments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `caregiver_assignments_ibfk_2` FOREIGN KEY (`caregiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `health_readings`
--
ALTER TABLE `health_readings`
  ADD CONSTRAINT `health_readings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `health_readings_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `medication_logs`
--
ALTER TABLE `medication_logs`
  ADD CONSTRAINT `medication_logs_ibfk_1` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medication_logs_ibfk_2` FOREIGN KEY (`logged_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `medicines`
--
ALTER TABLE `medicines`
  ADD CONSTRAINT `medicines_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medicines_ibfk_2` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sos_alerts`
--
ALTER TABLE `sos_alerts`
  ADD CONSTRAINT `sos_alerts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sos_notifications`
--
ALTER TABLE `sos_notifications`
  ADD CONSTRAINT `sos_notifications_ibfk_1` FOREIGN KEY (`alert_id`) REFERENCES `sos_alerts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sos_notifications_ibfk_2` FOREIGN KEY (`caregiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
