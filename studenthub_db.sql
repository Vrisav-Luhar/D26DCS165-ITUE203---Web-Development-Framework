-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 22, 2026 at 10:02 PM
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
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` datetime NOT NULL,
  `venue` varchar(150) NOT NULL,
  `capacity` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `title`, `description`, `event_date`, `venue`, `capacity`, `created_at`) VALUES
(1, 'Web Development Workshop', 'A practical workshop on HTML, CSS, JavaScript and PHP.', '2027-01-15 10:00:00', 'Computer Lab 1', 60, '2026-10-22 16:33:16'),
(2, 'AI and Machine Learning Seminar', 'An introductory seminar on AI and machine learning.', '2027-01-22 11:00:00', 'Seminar Hall', 100, '2026-10-22 16:33:16'),
(4, 'Test2', 'Practical 13', '2026-10-23 03:27:00', 'Hall', 50, '2026-10-22 18:55:05');

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

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`registration_id`, `student_id`, `event_id`, `registered_at`, `status`) VALUES
(1, 1, 1, '2026-10-22 16:33:16', 'registered');

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
(8, 4, '036b36ccc6d5790f5381c087', '00a877077277317f86a31fb91bdecd9c106a297d477100f9f7ba1440257d5a6a', '2026-11-21 21:22:26', '2026-10-22 19:22:26');

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
(1, 'Aarav Patel', 'aarav.demo@example.com', '9876543210', 'B.Tech Computer Science', 2, NULL, 'student', '2026-10-22 16:33:16'),
(3, 'Kabir Mehta', 'kabir.demo@example.com', '9876543212', 'B.Tech AI & Machine Learning', 1, NULL, 'student', '2026-10-22 16:33:16'),
(4, 'Vasu Luhar', 'vasu@gmail.com', '9171817575', 'B.Tech Computer Science', 2, '$2y$10$jYWhCR/i8frk69huyevD3.f8ap273D/CMZkQ51hDR5Q0L935zENVi', 'student', '2026-10-22 16:50:14'),
(5, 'Vrisav Panchal', 'vrisav@gmail.com', NULL, 'StudentHub Administrator', 1, '$2y$10$q4qKe67ENPMt4u6e8Yssy.Ifo2CUTuvnqIwHls3xO.1BpY7QM94wC', 'admin', '2026-10-22 17:26:47'),
(6, 'Neel', 'neel@gmail.com', '5465644566', 'B.Tech Computer Science', 2, '$2y$10$Y/QNzBXPBOlr8.3BDs1VJOTDfOxs3FulbDlh7wL6dUxjTcnvH8v5C', 'student', '2026-10-22 18:54:36');

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `registration_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `remember_me_tokens`
--
ALTER TABLE `remember_me_tokens`
  MODIFY `token_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
