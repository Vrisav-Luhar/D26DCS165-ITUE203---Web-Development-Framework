-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 23, 2026 at 03:40 PM
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
-- Database: `studenthub_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `audit_id` bigint(20) UNSIGNED NOT NULL,
  `actor_student_id` int(10) UNSIGNED DEFAULT NULL,
  `actor_email` varchar(150) DEFAULT NULL,
  `actor_role` varchar(30) NOT NULL DEFAULT 'unknown',
  `action` varchar(50) NOT NULL,
  `entity_type` varchar(50) NOT NULL,
  `entity_id` bigint(20) DEFAULT NULL,
  `details` varchar(1000) NOT NULL DEFAULT '',
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`audit_id`, `actor_student_id`, `actor_email`, `actor_role`, `action`, `entity_type`, `entity_id`, `details`, `ip_address`, `created_at`) VALUES
(1, 5, 'vrisav@gmail.com', 'admin', 'UPDATE', 'event', 4, 'Updated event: Test2', '::1', '2026-10-23 12:04:23');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` datetime NOT NULL,
  `venue` varchar(150) NOT NULL,
  `capacity` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `poster_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `title`, `description`, `event_date`, `venue`, `capacity`, `created_at`, `poster_path`) VALUES
(1, 'Web Development Workshop', 'A practical workshop on HTML, CSS, JavaScript and PHP.', '2027-01-15 10:00:00', 'Computer Lab 1', 60, '2026-10-22 16:33:16', 'uploads/event-posters/e29bee59750b54aafab6aae8ad15a425.png'),
(2, 'AI and Machine Learning Seminar', 'An introductory seminar on AI and machine learning.', '2027-01-22 11:00:00', 'Seminar Hall', 100, '2026-10-22 16:33:16', NULL),
(4, 'Test2', 'Practical Testing', '2026-10-23 03:27:00', 'Auditorium', 50, '2026-10-22 18:55:05', NULL),
(5, 'Audit', 'Audit Log', '2026-10-01 07:35:00', 'Zoom', 50, '2026-10-23 12:03:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `registration_id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `event_id` int(10) UNSIGNED NOT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('registered','cancelled','attended') NOT NULL DEFAULT 'registered'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `remember_me_tokens`
--

CREATE TABLE `remember_me_tokens` (
  `token_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `selector` char(24) NOT NULL,
  `validator_hash` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `remember_me_tokens`
--

INSERT INTO `remember_me_tokens` (`token_id`, `student_id`, `selector`, `validator_hash`, `expires_at`, `created_at`) VALUES
(2, 4, '3071319df72e987eeab812f6', '4351aa3299ad6678484efafb5a65fb2ad3f763b4a0eccda0290ec49c565a4c2f', '2026-11-21 19:29:56', '2026-10-22 17:29:56'),
(15, 5, '5ef8cdfb62e115a8265e90a4', 'befa33edb223452ccf7e5beb465ad1636126442aa7b42ff0fb9db3b467ee22ca', '2026-11-22 11:16:17', '2026-10-23 09:16:17'),
(20, 5, 'ca8c5fcd4116c9ac2a1b1a7e', '7900e4fd1b06f662f2ba9a30af0fa939882bd9d08355d754c41673871ff3c238', '2026-11-22 13:44:07', '2026-10-23 11:44:07');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `study_year` tinyint(3) UNSIGNED DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `role` enum('admin','student') NOT NULL DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `full_name`, `email`, `mobile`, `course`, `study_year`, `password_hash`, `role`, `created_at`) VALUES
(3, 'Kabir Mehta', 'kabir.demo@example.com', '9876543212', 'B.Tech AI & Machine Learning', 1, NULL, 'student', '2026-10-22 16:33:16'),
(4, 'Vasu Luhar', 'vasu@gmail.com', '9171817575', 'B.Tech Computer Science', 2, '$2y$10$jYWhCR/i8frk69huyevD3.f8ap273D/CMZkQ51hDR5Q0L935zENVi', 'student', '2026-10-22 16:50:14'),
(5, 'Vrisav Panchal', 'vrisav@gmail.com', NULL, 'StudentHub Administrator', 1, '$2y$10$q4qKe67ENPMt4u6e8Yssy.Ifo2CUTuvnqIwHls3xO.1BpY7QM94wC', 'admin', '2026-10-22 17:26:47'),
(6, 'Neel', 'neel@gmail.com', '5465644566', 'B.Tech Computer Science', 2, '$2y$10$Y/QNzBXPBOlr8.3BDs1VJOTDfOxs3FulbDlh7wL6dUxjTcnvH8v5C', 'student', '2026-10-22 18:54:36'),
(7, 'Deep Krishnani', 'deep@gmail.com', '9885645216', 'B.Tech Computer Science', 2, '$2y$10$agIcxhwrEAB.SyPyxwq38O.ZHepKakydWFtmQfBGI5rTjcL.tx9.K', 'student', '2026-10-23 07:55:12'),
(8, 'Sneh Hirani', 'sneh@gmail.com', '7896532145', 'B.Tech Computer Science', 2, '$2y$10$7mQkcDiF09GVE7F2g3wyc.zfOL.Yq400HE.PLmX0KRbhf7U9weJS.', 'student', '2026-10-23 08:37:58'),
(9, 'Isaac Masih', 'rony@gmail.com', '7863541295', 'B.Tech Computer Science', 2, '$2y$10$Q0q8fABHf9cZFLmMQXxamuFyz7TCDeyjdxvTh1DvrVt3UA7mkmm5K', 'student', '2026-10-23 09:47:30'),
(10, 'Meet Dabhi', 'meet@gmail.com', '7896574125', 'Diploma Information Technology', 2, '$2y$10$oZY6tFyOB/.P3IkeAuVsTORqaQMbrhigRWUy0ScPNuD64a.K3Qpty', 'student', '2026-10-23 12:06:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`audit_id`),
  ADD KEY `idx_audit_created_at` (`created_at`),
  ADD KEY `idx_audit_actor` (`actor_student_id`),
  ADD KEY `idx_audit_action` (`action`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`registration_id`),
  ADD UNIQUE KEY `uq_student_event` (`student_id`,`event_id`),
  ADD KEY `idx_registration_event` (`event_id`),
  ADD KEY `idx_registration_student` (`student_id`);

--
-- Indexes for table `remember_me_tokens`
--
ALTER TABLE `remember_me_tokens`
  ADD PRIMARY KEY (`token_id`),
  ADD UNIQUE KEY `selector` (`selector`),
  ADD KEY `fk_remember_student` (`student_id`),
  ADD KEY `idx_remember_expires` (`expires_at`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `audit_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `registration_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `remember_me_tokens`
--
ALTER TABLE `remember_me_tokens`
  MODIFY `token_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `fk_registration_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_registration_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `remember_me_tokens`
--
ALTER TABLE `remember_me_tokens`
  ADD CONSTRAINT `fk_remember_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
