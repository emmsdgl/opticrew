-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 17, 2025 at 02:26 PM
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
-- Database: `opticrew`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `clock_in` timestamp NULL DEFAULT NULL,
  `clock_out` timestamp NULL DEFAULT NULL,
  `total_minutes_worked` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendances`
--

INSERT INTO `attendances` (`id`, `employee_id`, `clock_in`, `clock_out`, `total_minutes_worked`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-10-02 18:52:23', '2025-10-02 19:42:23', 50, '2025-10-02 18:52:17', '2025-10-02 18:52:40'),
(2, 1, '2025-10-02 18:52:25', '2025-10-02 19:32:25', 40, '2025-10-02 18:52:17', '2025-10-02 19:33:07'),
(3, 8, '2025-10-02 18:52:23', '2025-10-02 19:42:23', 50, '2025-10-02 18:52:17', '2025-10-02 18:52:40'),
(4, 8, '2025-10-02 18:52:25', '2025-10-02 19:32:25', 40, '2025-10-02 18:52:17', '2025-10-02 19:33:07'),
(5, 2, '2025-08-19 05:47:00', '2025-08-19 06:42:00', 55, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(6, 2, '2025-09-03 04:56:00', '2025-09-03 06:05:00', 69, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(7, 2, '2025-09-11 01:24:00', '2025-09-11 02:32:00', 68, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(8, 2, '2025-07-25 04:16:00', '2025-07-25 05:20:00', 64, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(9, 2, '2025-07-01 01:31:00', '2025-07-01 02:28:00', 57, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(10, 2, '2025-08-05 01:53:00', '2025-08-05 02:46:00', 53, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(11, 2, '2025-09-13 02:36:00', '2025-09-13 03:29:00', 53, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(12, 2, '2025-08-04 05:56:00', '2025-08-04 06:50:00', 54, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(13, 2, '2025-08-07 04:30:00', '2025-08-07 05:40:00', 70, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(14, 2, '2025-07-26 05:36:00', '2025-07-26 06:49:00', 73, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(15, 2, '2025-07-01 03:22:00', '2025-07-01 04:34:00', 72, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(16, 2, '2025-07-10 06:13:00', '2025-07-10 07:23:00', 70, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(17, 2, '2025-09-25 06:30:00', '2025-09-25 07:22:00', 52, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(18, 2, '2025-09-05 05:49:00', '2025-09-05 06:40:00', 51, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(19, 2, '2025-09-04 01:19:00', '2025-09-04 02:07:00', 48, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(20, 2, '2025-09-15 01:13:00', '2025-09-15 02:04:00', 51, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(21, 2, '2025-07-19 05:08:00', '2025-07-19 06:03:00', 55, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(22, 2, '2025-07-29 01:39:00', '2025-07-29 02:43:00', 64, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(23, 2, '2025-09-23 06:51:00', '2025-09-23 08:05:00', 74, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(24, 2, '2025-08-06 05:52:00', '2025-08-06 06:44:00', 52, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(25, 2, '2025-08-23 02:33:00', '2025-08-23 03:50:00', 77, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(26, 2, '2025-09-26 05:13:00', '2025-09-26 06:19:00', 66, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(27, 2, '2025-07-07 05:09:00', '2025-07-07 06:12:00', 63, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(28, 2, '2025-09-04 05:52:00', '2025-09-04 06:57:00', 65, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(29, 2, '2025-09-16 03:11:00', '2025-09-16 04:20:00', 69, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(30, 2, '2025-09-27 01:47:00', '2025-09-27 02:43:00', 56, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(31, 2, '2025-09-18 05:06:00', '2025-09-18 06:16:00', 70, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(32, 2, '2025-07-15 03:19:00', '2025-07-15 04:15:00', 56, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(33, 2, '2025-08-12 03:22:00', '2025-08-12 04:13:00', 51, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(34, 2, '2025-09-19 05:18:00', '2025-09-19 06:08:00', 50, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(35, 2, '2025-08-29 05:08:00', '2025-08-29 06:19:00', 71, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(36, 2, '2025-09-18 03:45:00', '2025-09-18 04:39:00', 54, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(37, 2, '2025-08-30 05:46:00', '2025-08-30 06:41:00', 55, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(38, 2, '2025-09-14 01:39:00', '2025-09-14 02:46:00', 67, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(39, 2, '2025-09-01 02:11:00', '2025-09-01 03:19:00', 68, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(40, 2, '2025-08-14 03:39:00', '2025-08-14 04:45:00', 66, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `car_name` varchar(255) NOT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`id`, `car_name`, `is_available`, `created_at`, `updated_at`) VALUES
(1, 'Van 1', 1, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(2, 'Van 2', 1, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(3, 'Sedan 1', 1, '2025-10-02 18:51:46', '2025-10-02 18:51:46');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `middle_initial` varchar(5) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `security_question_1` varchar(255) DEFAULT NULL,
  `security_answer_1` varchar(255) DEFAULT NULL,
  `security_question_2` varchar(255) DEFAULT NULL,
  `security_answer_2` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `user_id`, `first_name`, `last_name`, `middle_initial`, `birthdate`, `phone_number`, `security_question_1`, `security_answer_1`, `security_question_2`, `security_answer_2`, `created_at`, `updated_at`) VALUES
(1, 13, 'Emmaus', 'Digol', 'L', '2004-09-23', '9602790025', 'pet_name', '$2y$10$9teflEeavHDD/O0QeGMVQ.2AWHsZvgQPu1z0g0GAhb1nuH2M.1dgu', 'best_friend', '$2y$10$GmVkvp2HvwAOcf2jFy2n3O.Hw9w.OWD99xcY9qD.hufdDajbvY2IS', '2025-10-15 00:20:16', '2025-10-15 00:20:16');

-- --------------------------------------------------------

--
-- Table structure for table `contracted_clients`
--

CREATE TABLE `contracted_clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contracted_clients`
--

INSERT INTO `contracted_clients` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Kakslauttanen', '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(2, 'Aikamatkat', '2025-10-02 18:51:46', '2025-10-02 18:51:46');

-- --------------------------------------------------------

--
-- Table structure for table `daily_team_assignments`
--

CREATE TABLE `daily_team_assignments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `assignment_date` date NOT NULL,
  `car_id` bigint(20) UNSIGNED DEFAULT NULL,
  `contracted_client_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `daily_team_assignments`
--

INSERT INTO `daily_team_assignments` (`id`, `assignment_date`, `car_id`, `contracted_client_id`, `created_at`, `updated_at`) VALUES
(1, '2025-10-03', 1, 1, '2025-10-02 18:52:17', '2025-10-02 18:52:17'),
(2, '2025-07-01', 1, 1, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(3, '2025-07-01', 2, 1, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(4, '2025-07-01', 3, 1, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(5, '2025-07-01', 1, 1, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(6, '2025-07-01', 2, 1, '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(14, '2025-10-04', 1, 1, '2025-10-04 06:56:08', '2025-10-04 06:56:08'),
(198, '2025-10-11', 1, 1, '2025-10-09 22:14:40', '2025-10-09 22:14:40'),
(199, '2025-10-11', 1, 1, '2025-10-09 22:14:40', '2025-10-09 22:14:40'),
(200, '2025-10-11', 2, 2, '2025-10-09 22:14:41', '2025-10-09 22:14:41'),
(201, '2025-10-11', 2, 2, '2025-10-09 22:14:41', '2025-10-09 22:14:41'),
(202, '2025-10-11', 2, 2, '2025-10-09 22:14:41', '2025-10-09 22:14:41');

-- --------------------------------------------------------

--
-- Table structure for table `day_offs`
--

CREATE TABLE `day_offs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `type` enum('vacation','sick','personal','other') NOT NULL DEFAULT 'personal',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `skills` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`skills`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_day_off` tinyint(1) NOT NULL DEFAULT 0,
  `is_busy` tinyint(1) NOT NULL DEFAULT 0,
  `efficiency` decimal(3,2) NOT NULL DEFAULT 1.00 COMMENT 'Employee efficiency multiplier (0.5 to 2.0)',
  `has_driving_license` tinyint(1) NOT NULL DEFAULT 0,
  `years_of_experience` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `user_id`, `full_name`, `skills`, `is_active`, `is_day_off`, `is_busy`, `efficiency`, `has_driving_license`, `years_of_experience`, `created_at`, `updated_at`) VALUES
(1, 2, 'Vincent Rey Digol', '[\"Driving\",\"Cleaning\"]', 1, 0, 0, 1.00, 0, 0, '2025-10-02 18:51:46', '2025-10-17 03:40:36'),
(2, 3, 'Martin Yvann Leonardo', '[\"Driving\",\"Cleaning\"]', 1, 0, 0, 1.00, 0, 0, '2025-10-02 18:51:46', '2025-10-17 03:40:36'),
(3, 4, 'Earl Leonardo', '[\"Driving\",\"Cleaning\"]', 1, 0, 0, 1.00, 0, 0, '2025-10-02 18:51:46', '2025-10-17 03:40:36'),
(4, 5, 'Merlyn Guzman', '[\"Driving\",\"Cleaning\"]', 1, 0, 0, 1.00, 0, 0, '2025-10-02 18:51:46', '2025-10-17 03:40:36'),
(5, 6, 'Aries Guzman', '[\"Driving\",\"Cleaning\"]', 1, 0, 0, 1.00, 0, 0, '2025-10-02 18:51:46', '2025-10-17 03:40:36'),
(6, 7, 'Bella Ostan', '[\"Cleaning\"]', 1, 0, 0, 1.00, 0, 0, '2025-10-02 18:51:46', '2025-10-17 03:40:36'),
(7, 8, 'Jennylyn Saballero', '[\"Cleaning\"]', 1, 0, 0, 1.00, 0, 0, '2025-10-02 18:51:46', '2025-10-17 03:40:36'),
(8, 9, 'Rizza Estrella ', '[\"Cleaning\"]', 1, 0, 0, 1.00, 0, 0, '2025-10-02 18:51:46', '2025-10-17 03:40:36'),
(9, 10, 'Cherrylyn Morales ', '[\"Driving\",\"Cleaning\"]', 1, 0, 0, 1.00, 0, 0, '2025-10-02 18:51:46', '2025-10-17 03:40:36'),
(10, 11, 'John Carl Morales', '[\"Driving\",\"Cleaning\"]', 1, 0, 0, 1.00, 0, 0, '2025-10-02 18:51:46', '2025-10-17 03:40:36'),
(11, 12, 'John Kevin Morales', '[\"Driving\",\"Cleaning\"]', 1, 0, 0, 1.00, 0, 0, '2025-10-02 18:51:46', '2025-10-17 03:40:36');

-- --------------------------------------------------------

--
-- Table structure for table `employee_schedules`
--

CREATE TABLE `employee_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `work_date` date NOT NULL,
  `is_day_off` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_schedules`
--

INSERT INTO `employee_schedules` (`id`, `employee_id`, `work_date`, `is_day_off`, `created_at`, `updated_at`) VALUES
(1, 2, '2025-10-03', 1, '2025-10-02 18:52:06', '2025-10-02 18:52:06'),
(2, 3, '2025-10-03', 1, '2025-10-02 18:52:07', '2025-10-02 18:52:07'),
(3, 4, '2025-10-03', 1, '2025-10-02 18:52:07', '2025-10-02 18:52:07'),
(4, 5, '2025-10-03', 1, '2025-10-02 18:52:08', '2025-10-02 18:52:08'),
(5, 6, '2025-10-03', 1, '2025-10-02 18:52:08', '2025-10-02 18:52:08'),
(6, 7, '2025-10-03', 1, '2025-10-02 18:52:08', '2025-10-02 18:52:08'),
(7, 9, '2025-10-03', 1, '2025-10-02 18:52:09', '2025-10-02 18:52:09'),
(8, 10, '2025-10-03', 1, '2025-10-02 18:52:10', '2025-10-02 18:52:10'),
(9, 11, '2025-10-03', 1, '2025-10-02 18:52:10', '2025-10-02 18:52:10'),
(10, 2, '2025-10-04', 1, '2025-10-04 03:16:52', '2025-10-04 03:16:52'),
(12, 6, '2025-10-04', 1, '2025-10-04 03:16:55', '2025-10-04 03:16:55'),
(13, 8, '2025-10-04', 1, '2025-10-04 03:16:57', '2025-10-04 03:16:57'),
(14, 10, '2025-10-04', 1, '2025-10-04 03:16:58', '2025-10-04 03:16:58'),
(15, 1, '2025-11-04', 1, '2025-10-04 06:05:30', '2025-10-04 06:05:30'),
(16, 1, '2025-11-03', 1, '2025-10-04 06:05:31', '2025-10-04 06:05:31'),
(27, 1, '2025-10-04', 1, '2025-10-04 06:55:44', '2025-10-04 06:55:44'),
(28, 5, '2025-10-04', 1, '2025-10-04 06:55:48', '2025-10-04 06:55:48'),
(29, 7, '2025-10-04', 1, '2025-10-04 06:55:49', '2025-10-04 06:55:49'),
(30, 9, '2025-10-04', 1, '2025-10-04 06:55:49', '2025-10-04 06:55:49'),
(31, 11, '2025-10-04', 1, '2025-10-04 06:55:49', '2025-10-04 06:55:49'),
(47, 1, '2025-10-07', 1, '2025-10-07 03:41:42', '2025-10-07 03:41:42'),
(48, 3, '2025-10-07', 1, '2025-10-07 03:41:44', '2025-10-07 03:41:44'),
(49, 4, '2025-10-07', 1, '2025-10-07 03:41:44', '2025-10-07 03:41:44'),
(50, 8, '2025-10-07', 1, '2025-10-07 03:41:45', '2025-10-07 03:41:45'),
(51, 2, '2025-10-08', 1, '2025-10-07 03:52:44', '2025-10-07 03:52:44'),
(52, 3, '2025-10-08', 1, '2025-10-07 03:52:45', '2025-10-07 03:52:45'),
(54, 8, '2025-10-08', 1, '2025-10-07 03:52:51', '2025-10-07 03:52:51'),
(57, 4, '2025-10-10', 1, '2025-10-07 08:47:02', '2025-10-07 08:47:02'),
(58, 11, '2025-10-10', 1, '2025-10-07 08:47:13', '2025-10-07 08:47:13'),
(59, 9, '2025-10-09', 1, '2025-10-07 20:09:18', '2025-10-07 20:09:18'),
(60, 10, '2025-10-09', 1, '2025-10-07 20:09:19', '2025-10-07 20:09:19'),
(61, 2, '2025-10-10', 1, '2025-10-09 22:13:32', '2025-10-09 22:13:32'),
(62, 3, '2025-10-10', 1, '2025-10-09 22:13:33', '2025-10-09 22:13:33');

-- --------------------------------------------------------

--
-- Table structure for table `invalid_tasks`
--

CREATE TABLE `invalid_tasks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `optimization_result_id` bigint(20) UNSIGNED DEFAULT NULL,
  `task_id` bigint(20) UNSIGNED NOT NULL,
  `rejection_reason` varchar(255) NOT NULL,
  `task_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`task_details`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contracted_client_id` bigint(20) UNSIGNED NOT NULL,
  `location_name` varchar(255) NOT NULL,
  `location_type` varchar(255) NOT NULL,
  `base_cleaning_duration_minutes` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `contracted_client_id`, `location_name`, `location_type`, `base_cleaning_duration_minutes`, `created_at`, `updated_at`) VALUES
(1, 1, 'Small Cabin #1', 'Small Cabin', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(2, 1, 'Small Cabin #2', 'Small Cabin', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(3, 1, 'Small Cabin #3', 'Small Cabin', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(4, 1, 'Small Cabin #4', 'Small Cabin', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(5, 1, 'Small Cabin #5', 'Small Cabin', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(6, 1, 'Small Cabin #6', 'Small Cabin', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(7, 1, 'Small Cabin #7', 'Small Cabin', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(8, 1, 'Small Cabin #8', 'Small Cabin', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(9, 1, 'Small Cabin #9', 'Small Cabin', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(10, 1, 'Small Cabin #10', 'Small Cabin', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(11, 1, 'Small Cabin #11', 'Small Cabin', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(12, 1, 'Small Cabin #12', 'Small Cabin', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(13, 1, 'Medium Cabin #1', 'Medium Cabin', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(14, 1, 'Medium Cabin #2', 'Medium Cabin', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(15, 1, 'Medium Cabin #3', 'Medium Cabin', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(16, 1, 'Medium Cabin #4', 'Medium Cabin', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(17, 1, 'Medium Cabin #5', 'Medium Cabin', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(18, 1, 'Medium Cabin #6', 'Medium Cabin', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(19, 1, 'Big Cabin #1', 'Big Cabin', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(20, 1, 'Big Cabin #2', 'Big Cabin', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(21, 1, 'Big Cabin #3', 'Big Cabin', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(22, 1, 'Big Cabin #4', 'Big Cabin', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(23, 1, 'Big Cabin #5', 'Big Cabin', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(24, 1, 'Big Cabin #6', 'Big Cabin', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(25, 1, 'Big Cabin #7', 'Big Cabin', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(26, 1, 'Big Cabin #8', 'Big Cabin', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(27, 1, 'Big Cabin #9', 'Big Cabin', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(28, 1, 'Big Cabin #10', 'Big Cabin', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(29, 1, 'Big Cabin #11', 'Big Cabin', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(30, 1, 'Big Cabin #12', 'Big Cabin', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(31, 1, 'Big Cabin #13', 'Big Cabin', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(32, 1, 'Queen Suite #1', 'Queen Suite', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(33, 1, 'Queen Suite #2', 'Queen Suite', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(34, 1, 'Queen Suite #3', 'Queen Suite', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(35, 1, 'Queen Suite #4', 'Queen Suite', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(36, 1, 'Queen Suite #5', 'Queen Suite', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(37, 1, 'Igloo #1', 'Igloo', 45, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(38, 1, 'Igloo #2', 'Igloo', 45, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(39, 1, 'Igloo #3', 'Igloo', 45, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(40, 1, 'Igloo #4', 'Igloo', 45, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(41, 1, 'Igloo #5', 'Igloo', 45, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(42, 1, 'Igloo #6', 'Igloo', 45, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(43, 1, 'Igloo #7', 'Igloo', 45, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(44, 1, 'Igloo #8', 'Igloo', 45, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(45, 1, 'Igloo #9', 'Igloo', 45, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(46, 1, 'Igloo #10', 'Igloo', 45, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(47, 1, 'Igloo #11', 'Igloo', 45, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(48, 1, 'Igloo #12', 'Igloo', 45, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(49, 1, 'Igloo #13', 'Igloo', 45, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(50, 1, 'Igloo #14', 'Igloo', 45, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(51, 1, 'Igloo #15', 'Igloo', 45, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(52, 1, 'Igloo #16', 'Igloo', 45, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(53, 1, 'Igloo #17', 'Igloo', 45, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(54, 1, 'Igloo #18', 'Igloo', 45, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(55, 1, 'Igloo #19', 'Igloo', 45, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(56, 1, 'Igloo #20', 'Igloo', 45, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(57, 1, 'Traditional House', 'Traditional House', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(58, 1, 'Turf Chamber', 'Turf Chamber', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(59, 2, 'Panimo Cabins #1', 'Panimo Cabins', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(60, 2, 'Panimo Cabins #2', 'Panimo Cabins', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(61, 2, 'Panimo Cabins #3', 'Panimo Cabins', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(62, 2, 'Panimo Cabins #4', 'Panimo Cabins', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(63, 2, 'Panimo Cabins #5', 'Panimo Cabins', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(64, 2, 'Panimo Cabins #6', 'Panimo Cabins', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(65, 2, 'Panimo Cabins #7', 'Panimo Cabins', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(66, 2, 'Panimo Cabins #8', 'Panimo Cabins', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(67, 2, 'Panimo Cabins #9', 'Panimo Cabins', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(68, 2, 'Panimo Cabins #10', 'Panimo Cabins', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(69, 2, 'Panimo Cabins #11', 'Panimo Cabins', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(70, 2, 'Panimo Cabins #12', 'Panimo Cabins', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(71, 2, 'Metsakoti A', 'Metsakoti A', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(72, 2, 'Metsakoti B', 'Metsakoti B', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(73, 2, 'Kermikkas', 'Kermikkas', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(74, 2, 'Hirvasaho A2 and B1', 'Hirvasaho A2 and B1', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(75, 2, 'Hirvasaho B2', 'Hirvasaho B2', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(76, 2, 'Hirvas Apartments', 'Hirvas Apartments', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(77, 2, 'Voursa 3A and 3B', 'Voursa 3A and 3B', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(78, 2, 'Voursa 3C', 'Voursa 3C', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(79, 2, 'Moitakuru C31 and C32', 'Moitakuru C31 and C32', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(80, 2, 'Luulampi', 'Luulampi', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(81, 2, 'Metashirvas', 'Metashirvas', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(82, 2, 'Kelotähti', 'Kelotähti', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(83, 2, 'Raahenmaja', 'Raahenmaja', 60, '2025-10-02 18:51:46', '2025-10-02 18:51:46');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(2, '2025_10_02_130207_create_all_tables', 1),
(3, '2025_10_05_133300_create_scheduling_logs_table', 2),
(5, '2025_10_08_162339_add_time_tracking_and_reporting_tables', 3),
(6, '2025_10_16_150828_create_optimization_runs_table', 4),
(7, '2025_10_16_150857_create_opt_gen_run_gen_idx', 5),
(8, '2025_10_16_150902_create_optimization_schedules_table', 5),
(9, '2025_10_16_150908_add_optimization_tracking_to_tasks_table', 5),
(10, '2025_10_17_110826_create_optimization_results_table', 6),
(11, '2025_10_17_110853_create_invalid_tasks_table', 6),
(12, '2025_10_17_110859_create_scenario_analyses_table', 6),
(13, '2025_10_17_111652_create_jobs_table', 7),
(14, '2025_10_17_112656_add_optimization_columns_to_employees_and_tasks_tables', 8),
(15, '2025_10_17_112849_create_day_offs_table', 9);

-- --------------------------------------------------------

--
-- Table structure for table `optimization_generations`
--

CREATE TABLE `optimization_generations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `optimization_run_id` bigint(20) UNSIGNED NOT NULL,
  `generation_number` int(11) NOT NULL,
  `best_fitness` decimal(8,4) NOT NULL,
  `average_fitness` decimal(8,4) NOT NULL,
  `worst_fitness` decimal(8,4) NOT NULL,
  `is_improvement` tinyint(1) NOT NULL DEFAULT 0,
  `best_schedule_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`best_schedule_data`)),
  `population_summary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`population_summary`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `optimization_results`
--

CREATE TABLE `optimization_results` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_date` date NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `schedule` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`schedule`)),
  `fitness_score` decimal(5,3) NOT NULL,
  `generation_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `optimization_runs`
--

CREATE TABLE `optimization_runs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_date` date NOT NULL,
  `triggered_by_task_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('running','completed','failed') NOT NULL DEFAULT 'running',
  `total_tasks` int(11) NOT NULL,
  `total_teams` int(11) NOT NULL,
  `total_employees` int(11) NOT NULL,
  `employee_allocation_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`employee_allocation_data`)),
  `greedy_result_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`greedy_result_data`)),
  `final_fitness_score` decimal(8,4) DEFAULT NULL,
  `generations_run` int(11) NOT NULL DEFAULT 0,
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `optimization_schedules`
--

CREATE TABLE `optimization_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `optimization_generation_id` bigint(20) UNSIGNED NOT NULL,
  `schedule_index` int(11) NOT NULL,
  `fitness_score` decimal(8,4) NOT NULL,
  `team_assignments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`team_assignments`)),
  `workload_distribution` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`workload_distribution`)),
  `is_elite` tinyint(1) NOT NULL DEFAULT 0,
  `is_final_result` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` varchar(255) NOT NULL DEFAULT 'random',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_reports`
--

CREATE TABLE `payroll_reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `pay_period_start` date NOT NULL,
  `pay_period_end` date NOT NULL,
  `total_hours` decimal(8,2) NOT NULL,
  `total_pay` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `scenario_analyses`
--

CREATE TABLE `scenario_analyses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_date` date NOT NULL,
  `scenario_type` varchar(255) NOT NULL,
  `parameters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`parameters`)),
  `modified_schedule` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`modified_schedule`)),
  `impact_analysis` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`impact_analysis`)),
  `recommendations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`recommendations`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `scheduling_logs`
--

CREATE TABLE `scheduling_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `schedule_date` date NOT NULL,
  `log_data` longtext NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `scheduling_logs`
--

INSERT INTO `scheduling_logs` (`id`, `schedule_date`, `log_data`, `created_at`, `updated_at`) VALUES
(33, '2025-10-08', '{\"service_date\":\"2025-10-08\",\"inputs\":{\"location_ids\":[79]},\"steps\":[{\"title\":\"Available Employees\",\"count\":8,\"data\":[\"Vincent Rey Digol\",\"Merlyn Guzman\",\"Aries Guzman\",\"Bella Ostan\",\"Jennylyn Saballero\",\"Cherrylyn Morales \",\"John Carl Morales\",\"John Kevin Morales\"]},{\"title\":\"Employee Allocation for Kakslauttanen\",\"count\":4,\"data\":[\"Merlyn Guzman\",\"John Carl Morales\",\"Bella Ostan\",\"Jennylyn Saballero\"]},{\"title\":\"Employee Allocation for Aikamatkat\",\"count\":4,\"data\":[\"Vincent Rey Digol\",\"Aries Guzman\",\"John Kevin Morales\",\"Cherrylyn Morales \"]},{\"title\":\"Team Formation for Kakslauttanen\",\"count\":2,\"data\":[[\"Merlyn Guzman\",\"Bella Ostan\"],[\"John Carl Morales\",\"Jennylyn Saballero\"]]},{\"title\":\"Greedy Algorithm Result for Kakslauttanen\",\"data\":[{\"team_members\":[{\"name\":\"Merlyn Guzman\",\"efficiency\":1.4357783595485927},{\"name\":\"Bella Ostan\",\"efficiency\":0.9872840277989049}],\"assigned_tasks\":[\"Small Cabin #1\",\"Small Cabin #3\",\"Small Cabin #5\",\"Small Cabin #7\",\"Small Cabin #9\",\"Small Cabin #10\",\"Small Cabin #12\",\"Big Cabin #6\",\"Igloo #1\",\"Igloo #2\"],\"total_tasks\":10,\"estimated_duration\":570,\"team_efficiency\":\"121%\",\"predicted_workload\":470},{\"team_members\":[{\"name\":\"John Carl Morales\",\"efficiency\":1.0096148636851658},{\"name\":\"Jennylyn Saballero\",\"efficiency\":0.871767647019525}],\"assigned_tasks\":[\"Small Cabin #2\",\"Small Cabin #4\",\"Small Cabin #6\",\"Small Cabin #8\",\"Small Cabin #11\",\"Medium Cabin #4\",\"Turf Chamber\",\"Igloo #20\"],\"total_tasks\":8,\"estimated_duration\":465,\"team_efficiency\":\"94%\",\"predicted_workload\":494}]},{\"title\":\"Genetic Algorithm Result for Kakslauttanen\",\"fitness_score\":0.3082,\"data\":[{\"team_members\":[{\"name\":\"Merlyn Guzman\",\"efficiency\":1.4357783595485927},{\"name\":\"Bella Ostan\",\"efficiency\":0.9872840277989049}],\"assigned_tasks\":[\"Igloo #2\",\"Small Cabin #6\",\"Small Cabin #1\",\"Small Cabin #10\",\"Small Cabin #11\",\"Medium Cabin #4\",\"Small Cabin #3\",\"Small Cabin #12\",\"Turf Chamber\",\"Small Cabin #2\"],\"total_tasks\":10,\"estimated_duration\":585,\"team_efficiency\":\"121%\",\"predicted_workload\":483},{\"team_members\":[{\"name\":\"John Carl Morales\",\"efficiency\":1.0096148636851658},{\"name\":\"Jennylyn Saballero\",\"efficiency\":0.871767647019525}],\"assigned_tasks\":[\"Small Cabin #4\",\"Small Cabin #7\",\"Small Cabin #5\",\"Small Cabin #8\",\"Small Cabin #9\",\"Big Cabin #6\",\"Igloo #20\",\"Igloo #1\"],\"total_tasks\":8,\"estimated_duration\":450,\"team_efficiency\":\"94%\",\"predicted_workload\":478}]},{\"title\":\"Team Formation for Aikamatkat\",\"count\":2,\"data\":[[\"Vincent Rey Digol\",\"Cherrylyn Morales \"],[\"Aries Guzman\",\"John Kevin Morales\"]]},{\"title\":\"Greedy Algorithm Result for Aikamatkat\",\"data\":[{\"team_members\":[{\"name\":\"Vincent Rey Digol\",\"efficiency\":1.1111666239545372},{\"name\":\"Cherrylyn Morales \",\"efficiency\":0.9871810509994557}],\"assigned_tasks\":[\"Panimo Cabins #1\",\"Panimo Cabins #6\",\"Panimo Cabins #8\",\"Voursa 3A and 3B\",\"Raahenmaja\"],\"total_tasks\":5,\"estimated_duration\":300,\"team_efficiency\":\"105%\",\"predicted_workload\":286},{\"team_members\":[{\"name\":\"Aries Guzman\",\"efficiency\":1.0096148636851658},{\"name\":\"John Kevin Morales\",\"efficiency\":0.9872840277989049}],\"assigned_tasks\":[\"Panimo Cabins #2\",\"Panimo Cabins #7\",\"Metsakoti A\",\"Moitakuru C31 and C32\"],\"total_tasks\":4,\"estimated_duration\":240,\"team_efficiency\":\"100%\",\"predicted_workload\":240}]},{\"title\":\"Genetic Algorithm Result for Aikamatkat\",\"fitness_score\":0.042,\"data\":[{\"team_members\":[{\"name\":\"Vincent Rey Digol\",\"efficiency\":1.1111666239545372},{\"name\":\"Cherrylyn Morales \",\"efficiency\":0.9871810509994557}],\"assigned_tasks\":[\"Panimo Cabins #1\",\"Panimo Cabins #6\",\"Panimo Cabins #8\",\"Voursa 3A and 3B\",\"Raahenmaja\"],\"total_tasks\":5,\"estimated_duration\":300,\"team_efficiency\":\"105%\",\"predicted_workload\":286},{\"team_members\":[{\"name\":\"Aries Guzman\",\"efficiency\":1.0096148636851658},{\"name\":\"John Kevin Morales\",\"efficiency\":0.9872840277989049}],\"assigned_tasks\":[\"Panimo Cabins #2\",\"Panimo Cabins #7\",\"Metsakoti A\",\"Moitakuru C31 and C32\"],\"total_tasks\":4,\"estimated_duration\":240,\"team_efficiency\":\"100%\",\"predicted_workload\":240}]}]}', '2025-10-07 19:39:41', '2025-10-07 19:39:41'),
(34, '2025-10-09', '{\"service_date\":\"2025-10-09\",\"inputs\":{\"location_ids\":[1,7,13,19,25,31,37,43,49,55,59,60,61,62,63]},\"steps\":[{\"title\":\"Available Employees\",\"count\":9,\"data\":[\"Vincent Rey Digol\",\"Martin Yvann Leonardo\",\"Earl Leonardo\",\"Merlyn Guzman\",\"Aries Guzman\",\"Bella Ostan\",\"Jennylyn Saballero\",\"Rizza Estrella \",\"John Kevin Morales\"]},{\"title\":\"Employee Allocation for Kakslauttanen\",\"count\":4,\"data\":[\"Merlyn Guzman\",\"Vincent Rey Digol\",\"Aries Guzman\",\"Bella Ostan\"]},{\"title\":\"Employee Allocation for Aikamatkat\",\"count\":5,\"data\":[\"Earl Leonardo\",\"Rizza Estrella \",\"John Kevin Morales\",\"Martin Yvann Leonardo\",\"Jennylyn Saballero\"]},{\"title\":\"Team Formation for Kakslauttanen\",\"count\":2,\"data\":[[\"Merlyn Guzman\",\"Bella Ostan\"],[\"Vincent Rey Digol\",\"Aries Guzman\"]]},{\"title\":\"Greedy Algorithm Result for Kakslauttanen\",\"data\":[{\"team_members\":[{\"name\":\"Merlyn Guzman\",\"efficiency\":1.4357783595485927},{\"name\":\"Bella Ostan\",\"efficiency\":0.9872840277989049}],\"assigned_tasks\":[\"Small Cabin #1\",\"Medium Cabin #1\",\"Big Cabin #7\",\"Igloo #1\",\"Igloo #13\"],\"total_tasks\":5,\"estimated_duration\":270,\"team_efficiency\":\"121%\",\"predicted_workload\":223},{\"team_members\":[{\"name\":\"Vincent Rey Digol\",\"efficiency\":1.1111666239545372},{\"name\":\"Aries Guzman\",\"efficiency\":1.0096148636851658}],\"assigned_tasks\":[\"Small Cabin #7\",\"Big Cabin #1\",\"Big Cabin #13\",\"Igloo #7\",\"Igloo #19\"],\"total_tasks\":5,\"estimated_duration\":270,\"team_efficiency\":\"106%\",\"predicted_workload\":255}]},{\"title\":\"Genetic Algorithm Result for Kakslauttanen\",\"fitness_score\":0.2763,\"data\":[{\"team_members\":[{\"name\":\"Merlyn Guzman\",\"efficiency\":1.4357783595485927},{\"name\":\"Bella Ostan\",\"efficiency\":0.9872840277989049}],\"assigned_tasks\":[\"Medium Cabin #1\",\"Big Cabin #13\",\"Big Cabin #1\",\"Igloo #1\",\"Small Cabin #1\"],\"total_tasks\":5,\"estimated_duration\":285,\"team_efficiency\":\"121%\",\"predicted_workload\":235},{\"team_members\":[{\"name\":\"Vincent Rey Digol\",\"efficiency\":1.1111666239545372},{\"name\":\"Aries Guzman\",\"efficiency\":1.0096148636851658}],\"assigned_tasks\":[\"Big Cabin #7\",\"Igloo #19\",\"Small Cabin #7\",\"Igloo #7\",\"Igloo #13\"],\"total_tasks\":5,\"estimated_duration\":255,\"team_efficiency\":\"106%\",\"predicted_workload\":240}]},{\"title\":\"Team Formation for Aikamatkat\",\"count\":2,\"data\":[[\"Earl Leonardo\",\"Rizza Estrella \",\"Jennylyn Saballero\"],[\"John Kevin Morales\",\"Martin Yvann Leonardo\"]]},{\"title\":\"Greedy Algorithm Result for Aikamatkat\",\"data\":[{\"team_members\":[{\"name\":\"Earl Leonardo\",\"efficiency\":1.277049202224446},{\"name\":\"Rizza Estrella \",\"efficiency\":1.35},{\"name\":\"Jennylyn Saballero\",\"efficiency\":0.871767647019525}],\"assigned_tasks\":[\"Panimo Cabins #1\",\"Panimo Cabins #3\",\"Panimo Cabins #5\"],\"total_tasks\":3,\"estimated_duration\":180,\"team_efficiency\":\"117%\",\"predicted_workload\":154},{\"team_members\":[{\"name\":\"John Kevin Morales\",\"efficiency\":0.9872840277989049},{\"name\":\"Martin Yvann Leonardo\",\"efficiency\":0.871767647019525}],\"assigned_tasks\":[\"Panimo Cabins #2\",\"Panimo Cabins #4\"],\"total_tasks\":2,\"estimated_duration\":120,\"team_efficiency\":\"93%\",\"predicted_workload\":129}]},{\"title\":\"Genetic Algorithm Result for Aikamatkat\",\"fitness_score\":0.0734,\"data\":[{\"team_members\":[{\"name\":\"Earl Leonardo\",\"efficiency\":1.277049202224446},{\"name\":\"Rizza Estrella \",\"efficiency\":1.35},{\"name\":\"Jennylyn Saballero\",\"efficiency\":0.871767647019525}],\"assigned_tasks\":[\"Panimo Cabins #1\",\"Panimo Cabins #3\",\"Panimo Cabins #5\"],\"total_tasks\":3,\"estimated_duration\":180,\"team_efficiency\":\"117%\",\"predicted_workload\":154},{\"team_members\":[{\"name\":\"John Kevin Morales\",\"efficiency\":0.9872840277989049},{\"name\":\"Martin Yvann Leonardo\",\"efficiency\":0.871767647019525}],\"assigned_tasks\":[\"Panimo Cabins #2\",\"Panimo Cabins #4\"],\"total_tasks\":2,\"estimated_duration\":120,\"team_efficiency\":\"93%\",\"predicted_workload\":129}]}]}', '2025-10-07 20:10:03', '2025-10-07 20:10:03'),
(35, '2025-10-11', '{\"service_date\":\"2025-10-11\",\"inputs\":{\"location_ids\":[49,55,1,7,13,19,25,31,37,43,2,8,14,20,26,32,38,44,50,56,59,65,71]},\"steps\":[{\"title\":\"Available Employees\",\"count\":11,\"data\":[\"Vincent Rey Digol\",\"Martin Yvann Leonardo\",\"Earl Leonardo\",\"Merlyn Guzman\",\"Aries Guzman\",\"Bella Ostan\",\"Jennylyn Saballero\",\"Rizza Estrella \",\"Cherrylyn Morales \",\"John Carl Morales\",\"John Kevin Morales\"]},{\"title\":\"Employee Allocation for Kakslauttanen\",\"count\":5,\"data\":[\"Merlyn Guzman\",\"Vincent Rey Digol\",\"Aries Guzman\",\"John Kevin Morales\",\"Cherrylyn Morales \"]},{\"title\":\"Employee Allocation for Aikamatkat\",\"count\":6,\"data\":[\"Earl Leonardo\",\"Rizza Estrella \",\"John Carl Morales\",\"Bella Ostan\",\"Martin Yvann Leonardo\",\"Jennylyn Saballero\"]},{\"title\":\"Team Formation for Kakslauttanen\",\"count\":2,\"data\":[[\"Merlyn Guzman\",\"Cherrylyn Morales \",\"John Kevin Morales\"],[\"Vincent Rey Digol\",\"Aries Guzman\"]]},{\"title\":\"Greedy Algorithm Result for Kakslauttanen\",\"data\":[{\"team_members\":[{\"name\":\"Merlyn Guzman\",\"efficiency\":1.4357783595485927},{\"name\":\"Cherrylyn Morales \",\"efficiency\":0.9871810509994557},{\"name\":\"John Kevin Morales\",\"efficiency\":0.9872840277989049}],\"assigned_tasks\":[\"Small Cabin #1\",\"Small Cabin #7\",\"Medium Cabin #1\",\"Big Cabin #1\",\"Big Cabin #7\",\"Big Cabin #13\",\"Igloo #1\",\"Igloo #7\",\"Igloo #13\",\"Igloo #19\"],\"total_tasks\":10,\"estimated_duration\":540,\"team_efficiency\":\"114%\",\"predicted_workload\":475},{\"team_members\":[{\"name\":\"Vincent Rey Digol\",\"efficiency\":1.1111666239545372},{\"name\":\"Aries Guzman\",\"efficiency\":1.0096148636851658}],\"assigned_tasks\":[\"Small Cabin #2\",\"Small Cabin #8\",\"Medium Cabin #2\",\"Big Cabin #2\",\"Big Cabin #8\",\"Queen Suite #1\",\"Igloo #2\",\"Igloo #8\",\"Igloo #14\",\"Igloo #20\"],\"total_tasks\":10,\"estimated_duration\":540,\"team_efficiency\":\"106%\",\"predicted_workload\":509}]},{\"title\":\"Genetic Algorithm Result for Kakslauttanen\",\"fitness_score\":0.2256,\"data\":[{\"team_members\":[{\"name\":\"Merlyn Guzman\",\"efficiency\":1.4357783595485927},{\"name\":\"Cherrylyn Morales \",\"efficiency\":0.9871810509994557},{\"name\":\"John Kevin Morales\",\"efficiency\":0.9872840277989049}],\"assigned_tasks\":[\"Medium Cabin #2\",\"Big Cabin #2\",\"Queen Suite #1\",\"Igloo #2\",\"Igloo #1\",\"Igloo #14\",\"Big Cabin #13\",\"Small Cabin #8\",\"Big Cabin #7\",\"Small Cabin #7\"],\"total_tasks\":10,\"estimated_duration\":555,\"team_efficiency\":\"114%\",\"predicted_workload\":488},{\"team_members\":[{\"name\":\"Vincent Rey Digol\",\"efficiency\":1.1111666239545372},{\"name\":\"Aries Guzman\",\"efficiency\":1.0096148636851658}],\"assigned_tasks\":[\"Igloo #20\",\"Big Cabin #8\",\"Igloo #13\",\"Igloo #19\",\"Igloo #7\",\"Igloo #8\",\"Small Cabin #1\",\"Medium Cabin #1\",\"Small Cabin #2\",\"Big Cabin #1\"],\"total_tasks\":10,\"estimated_duration\":525,\"team_efficiency\":\"106%\",\"predicted_workload\":495}]},{\"title\":\"Team Formation for Aikamatkat\",\"count\":3,\"data\":[[\"Earl Leonardo\",\"Rizza Estrella \"],[\"John Carl Morales\",\"Bella Ostan\"],[\"Martin Yvann Leonardo\",\"Jennylyn Saballero\"]]},{\"title\":\"Greedy Algorithm Result for Aikamatkat\",\"data\":[{\"team_members\":[{\"name\":\"Earl Leonardo\",\"efficiency\":1.277049202224446},{\"name\":\"Rizza Estrella \",\"efficiency\":1.35}],\"assigned_tasks\":[\"Panimo Cabins #1\"],\"total_tasks\":1,\"estimated_duration\":60,\"team_efficiency\":\"131%\",\"predicted_workload\":46},{\"team_members\":[{\"name\":\"John Carl Morales\",\"efficiency\":1.0096148636851658},{\"name\":\"Bella Ostan\",\"efficiency\":0.9872840277989049}],\"assigned_tasks\":[\"Panimo Cabins #7\"],\"total_tasks\":1,\"estimated_duration\":60,\"team_efficiency\":\"100%\",\"predicted_workload\":60},{\"team_members\":[{\"name\":\"Martin Yvann Leonardo\",\"efficiency\":0.871767647019525},{\"name\":\"Jennylyn Saballero\",\"efficiency\":0.871767647019525}],\"assigned_tasks\":[\"Metsakoti A\"],\"total_tasks\":1,\"estimated_duration\":60,\"team_efficiency\":\"87%\",\"predicted_workload\":69}]},{\"title\":\"Genetic Algorithm Result for Aikamatkat\",\"fitness_score\":0.0948,\"data\":[{\"team_members\":[{\"name\":\"Earl Leonardo\",\"efficiency\":1.277049202224446},{\"name\":\"Rizza Estrella \",\"efficiency\":1.35}],\"assigned_tasks\":[\"Panimo Cabins #1\"],\"total_tasks\":1,\"estimated_duration\":60,\"team_efficiency\":\"131%\",\"predicted_workload\":46},{\"team_members\":[{\"name\":\"John Carl Morales\",\"efficiency\":1.0096148636851658},{\"name\":\"Bella Ostan\",\"efficiency\":0.9872840277989049}],\"assigned_tasks\":[\"Panimo Cabins #7\"],\"total_tasks\":1,\"estimated_duration\":60,\"team_efficiency\":\"100%\",\"predicted_workload\":60},{\"team_members\":[{\"name\":\"Martin Yvann Leonardo\",\"efficiency\":0.871767647019525},{\"name\":\"Jennylyn Saballero\",\"efficiency\":0.871767647019525}],\"assigned_tasks\":[\"Metsakoti A\"],\"total_tasks\":1,\"estimated_duration\":60,\"team_efficiency\":\"87%\",\"predicted_workload\":69}]}]}', '2025-10-09 22:14:41', '2025-10-09 22:14:41');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_id` bigint(20) UNSIGNED DEFAULT NULL,
  `task_description` text NOT NULL,
  `estimated_duration_minutes` int(11) NOT NULL,
  `scheduled_date` date NOT NULL,
  `scheduled_time` time DEFAULT NULL,
  `duration` int(11) DEFAULT NULL COMMENT 'Task duration in minutes',
  `travel_time` int(11) NOT NULL DEFAULT 0 COMMENT 'Travel time to location in minutes',
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `required_equipment` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`required_equipment`)),
  `required_skills` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`required_skills`)),
  `status` enum('Pending','Scheduled','In-Progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `assigned_team_id` bigint(20) UNSIGNED DEFAULT NULL,
  `optimization_run_id` bigint(20) UNSIGNED DEFAULT NULL,
  `assigned_by_generation` int(11) DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `location_id`, `client_id`, `task_description`, `estimated_duration_minutes`, `scheduled_date`, `scheduled_time`, `duration`, `travel_time`, `latitude`, `longitude`, `required_equipment`, `required_skills`, `status`, `assigned_team_id`, `optimization_run_id`, `assigned_by_generation`, `started_at`, `created_at`, `updated_at`) VALUES
(1, 2, NULL, 'Daily Room Cleaning', 60, '2025-10-03', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 1, NULL, NULL, '2025-10-02 18:52:23', '2025-10-02 18:52:17', '2025-10-17 03:40:36'),
(2, 3, NULL, 'Daily Room Cleaning', 60, '2025-10-03', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 1, NULL, NULL, '2025-10-02 18:52:25', '2025-10-02 18:52:17', '2025-10-17 03:40:36'),
(1003, 12, NULL, 'Historical Cleaning', 60, '2025-08-17', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 3, NULL, NULL, '2025-07-31 05:37:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1004, 39, NULL, 'Historical Cleaning', 45, '2025-07-22', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 2, NULL, NULL, '2025-08-19 05:47:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1005, 77, NULL, 'Historical Cleaning', 60, '2025-09-17', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 4, NULL, NULL, '2025-09-25 05:10:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1006, 23, NULL, 'Historical Cleaning', 45, '2025-07-28', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 5, NULL, NULL, '2025-08-12 04:18:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1007, 46, NULL, 'Historical Cleaning', 60, '2025-07-10', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 5, NULL, NULL, '2025-07-02 03:52:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1008, 9, NULL, 'Historical Cleaning', 60, '2025-09-27', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 3, NULL, NULL, '2025-09-01 03:19:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1009, 28, NULL, 'Historical Cleaning', 60, '2025-08-11', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 3, NULL, NULL, '2025-07-29 05:52:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1010, 74, NULL, 'Historical Cleaning', 60, '2025-07-19', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 2, NULL, NULL, '2025-09-03 04:56:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1011, 30, NULL, 'Historical Cleaning', 45, '2025-09-02', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 4, NULL, NULL, '2025-08-18 03:40:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1012, 10, NULL, 'Historical Cleaning', 60, '2025-08-11', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 4, NULL, NULL, '2025-09-02 02:55:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1013, 1, NULL, 'Historical Cleaning', 60, '2025-07-10', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 6, NULL, NULL, '2025-08-14 01:14:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1014, 15, NULL, 'Historical Cleaning', 45, '2025-08-25', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 4, NULL, NULL, '2025-07-13 06:13:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1015, 22, NULL, 'Historical Cleaning', 60, '2025-07-11', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 2, NULL, NULL, '2025-09-11 01:24:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1016, 27, NULL, 'Historical Cleaning', 60, '2025-09-12', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 2, NULL, NULL, '2025-07-25 04:16:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1017, 73, NULL, 'Historical Cleaning', 60, '2025-09-26', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 3, NULL, NULL, '2025-07-04 05:35:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1018, 42, NULL, 'Historical Cleaning', 45, '2025-08-25', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 3, NULL, NULL, '2025-07-18 05:05:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1019, 14, NULL, 'Historical Cleaning', 60, '2025-08-11', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 4, NULL, NULL, '2025-09-11 01:22:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1020, 51, NULL, 'Historical Cleaning', 45, '2025-09-24', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 5, NULL, NULL, '2025-08-03 02:02:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1021, 70, NULL, 'Historical Cleaning', 60, '2025-07-16', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 6, NULL, NULL, '2025-07-16 04:19:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1022, 29, NULL, 'Historical Cleaning', 60, '2025-08-02', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 5, NULL, NULL, '2025-09-09 05:32:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1023, 60, NULL, 'Historical Cleaning', 60, '2025-09-25', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 5, NULL, NULL, '2025-07-06 02:13:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1024, 56, NULL, 'Historical Cleaning', 60, '2025-07-15', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 6, NULL, NULL, '2025-09-05 04:54:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1025, 79, NULL, 'Historical Cleaning', 45, '2025-07-06', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 5, NULL, NULL, '2025-08-02 01:05:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1026, 9, NULL, 'Historical Cleaning', 60, '2025-08-03', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 5, NULL, NULL, '2025-09-30 03:41:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1027, 54, NULL, 'Historical Cleaning', 60, '2025-08-16', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 6, NULL, NULL, '2025-08-30 05:08:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1028, 54, NULL, 'Historical Cleaning', 45, '2025-09-29', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 4, NULL, NULL, '2025-07-21 02:34:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1029, 58, NULL, 'Historical Cleaning', 45, '2025-09-20', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 2, NULL, NULL, '2025-07-01 01:31:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1030, 45, NULL, 'Historical Cleaning', 60, '2025-08-09', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 4, NULL, NULL, '2025-07-16 01:49:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1031, 45, NULL, 'Historical Cleaning', 60, '2025-09-09', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 6, NULL, NULL, '2025-07-12 03:39:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1032, 33, NULL, 'Historical Cleaning', 45, '2025-08-09', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 2, NULL, NULL, '2025-08-05 01:53:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1033, 58, NULL, 'Historical Cleaning', 60, '2025-09-05', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 4, NULL, NULL, '2025-09-24 02:23:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1034, 17, NULL, 'Historical Cleaning', 60, '2025-09-10', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 6, NULL, NULL, '2025-07-06 01:09:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1035, 48, NULL, 'Historical Cleaning', 60, '2025-09-11', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 5, NULL, NULL, '2025-08-15 04:07:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1036, 1, NULL, 'Historical Cleaning', 45, '2025-08-29', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 5, NULL, NULL, '2025-07-17 03:22:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1037, 50, NULL, 'Historical Cleaning', 45, '2025-08-28', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 4, NULL, NULL, '2025-09-16 01:04:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1038, 32, NULL, 'Historical Cleaning', 45, '2025-08-29', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 3, NULL, NULL, '2025-07-09 04:27:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1039, 25, NULL, 'Historical Cleaning', 60, '2025-08-24', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 6, NULL, NULL, '2025-08-25 04:00:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1040, 61, NULL, 'Historical Cleaning', 60, '2025-09-28', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 5, NULL, NULL, '2025-09-29 01:14:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1041, 26, NULL, 'Historical Cleaning', 45, '2025-07-08', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 3, NULL, NULL, '2025-09-07 05:53:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1042, 26, NULL, 'Historical Cleaning', 60, '2025-08-06', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 4, NULL, NULL, '2025-08-09 06:53:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1043, 60, NULL, 'Historical Cleaning', 45, '2025-08-28', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 5, NULL, NULL, '2025-07-15 06:47:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1044, 27, NULL, 'Historical Cleaning', 45, '2025-09-11', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 5, NULL, NULL, '2025-07-27 04:22:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1045, 70, NULL, 'Historical Cleaning', 45, '2025-09-10', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 3, NULL, NULL, '2025-08-14 02:31:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1046, 69, NULL, 'Historical Cleaning', 45, '2025-07-28', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 3, NULL, NULL, '2025-09-07 01:13:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1047, 4, NULL, 'Historical Cleaning', 45, '2025-07-09', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 4, NULL, NULL, '2025-08-29 02:25:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1048, 4, NULL, 'Historical Cleaning', 45, '2025-07-08', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 2, NULL, NULL, '2025-09-13 02:36:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1049, 59, NULL, 'Historical Cleaning', 45, '2025-08-24', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 5, NULL, NULL, '2025-07-28 04:58:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1050, 13, NULL, 'Historical Cleaning', 60, '2025-08-29', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 6, NULL, NULL, '2025-07-16 04:32:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1051, 46, NULL, 'Historical Cleaning', 60, '2025-07-31', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 6, NULL, NULL, '2025-07-28 03:10:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1052, 82, NULL, 'Historical Cleaning', 60, '2025-07-25', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 4, NULL, NULL, '2025-08-30 04:44:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1053, 68, NULL, 'Historical Cleaning', 60, '2025-09-27', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 6, NULL, NULL, '2025-08-24 02:06:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1054, 14, NULL, 'Historical Cleaning', 60, '2025-07-27', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 3, NULL, NULL, '2025-09-18 06:35:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1055, 81, NULL, 'Historical Cleaning', 45, '2025-09-12', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 5, NULL, NULL, '2025-09-21 02:34:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1056, 10, NULL, 'Historical Cleaning', 60, '2025-09-23', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 5, NULL, NULL, '2025-08-20 01:25:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1057, 79, NULL, 'Historical Cleaning', 45, '2025-07-13', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 2, NULL, NULL, '2025-08-04 05:56:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1058, 23, NULL, 'Historical Cleaning', 60, '2025-07-25', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 2, NULL, NULL, '2025-08-07 04:30:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1059, 15, NULL, 'Historical Cleaning', 60, '2025-09-29', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 2, NULL, NULL, '2025-07-26 05:36:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1060, 42, NULL, 'Historical Cleaning', 45, '2025-07-12', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 6, NULL, NULL, '2025-09-11 04:12:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1061, 37, NULL, 'Historical Cleaning', 60, '2025-09-24', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 6, NULL, NULL, '2025-09-26 04:28:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1062, 42, NULL, 'Historical Cleaning', 60, '2025-09-23', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 3, NULL, NULL, '2025-08-14 06:20:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1063, 58, NULL, 'Historical Cleaning', 60, '2025-08-16', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 2, NULL, NULL, '2025-07-01 03:22:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1064, 43, NULL, 'Historical Cleaning', 60, '2025-07-28', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 2, NULL, NULL, '2025-07-10 06:13:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1065, 27, NULL, 'Historical Cleaning', 45, '2025-08-25', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 2, NULL, NULL, '2025-09-25 06:30:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1066, 27, NULL, 'Historical Cleaning', 45, '2025-09-16', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 5, NULL, NULL, '2025-09-29 06:43:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1067, 2, NULL, 'Historical Cleaning', 45, '2025-07-23', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 2, NULL, NULL, '2025-09-05 05:49:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1068, 10, NULL, 'Historical Cleaning', 45, '2025-08-26', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 5, NULL, NULL, '2025-08-31 03:29:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1069, 72, NULL, 'Historical Cleaning', 45, '2025-07-26', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 4, NULL, NULL, '2025-07-09 02:55:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1070, 13, NULL, 'Historical Cleaning', 45, '2025-09-14', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 5, NULL, NULL, '2025-07-25 05:33:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),
(1071, 2, NULL, 'Historical Cleaning', 45, '2025-08-06', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Completed', 2, NULL, NULL, '2025-09-04 01:19:00', '2025-10-03 03:21:13', '2025-10-17 03:40:36'),

-- --------------------------------------------------------

--
-- Table structure for table `task_performance_histories`
--

CREATE TABLE `task_performance_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `task_id` bigint(20) UNSIGNED NOT NULL,
  `estimated_duration_minutes` int(11) NOT NULL,
  `actual_duration_minutes` int(11) NOT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `task_performance_histories`
--

INSERT INTO `task_performance_histories` (`id`, `task_id`, `estimated_duration_minutes`, `actual_duration_minutes`, `completed_at`, `created_at`, `updated_at`) VALUES
(2, 1003, 60, 52, '2025-07-31 06:40:00', '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(3, 1004, 45, 55, '2025-08-19 06:42:00', '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(4, 1005, 60, 62, '2025-09-25 06:15:00', '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(5, 1006, 45, 52, '2025-08-12 05:11:00', '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(6, 1007, 60, 63, '2025-07-02 04:52:00', '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(7, 1008, 60, 68, '2025-09-01 04:21:00', '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(8, 1009, 60, 60, '2025-07-29 07:01:00', '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(9, 1010, 60, 69, '2025-09-03 06:08:00', '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(10, 1011, 45, 43, '2025-08-18 04:23:00', '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(11, 1012, 60, 67, '2025-09-02 03:59:00', '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(12, 1013, 60, 49, '2025-08-14 02:13:00', '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(13, 1014, 45, 51, '2025-07-13 07:01:00', '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(14, 1015, 60, 68, '2025-09-11 02:32:00', '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(15, 1016, 60, 64, '2025-07-25 05:19:00', '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(16, 1017, 60, 49, '2025-07-04 06:30:00', '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(17, 1018, 45, 52, '2025-07-18 05:51:00', '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(18, 1019, 60, 64, '2025-09-11 02:24:00', '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(19, 1020, 45, 50, '2025-08-03 02:44:00', '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(20, 1021, 60, 57, '2025-07-16 05:15:00', '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(21, 1022, 60, 64, '2025-09-09 06:31:00', '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(22, 1023, 60, 58, '2025-07-06 03:10:00', '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(23, 1024, 60, 50, '2025-09-05 05:52:00', '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(24, 1025, 45, 42, '2025-08-02 01:47:00', '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(25, 1026, 60, 58, '2025-09-30 04:37:00', '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(26, 1027, 60, 55, '2025-08-30 06:04:00', '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(27, 1028, 45, 53, '2025-07-21 03:26:00', '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(28, 1029, 45, 57, '2025-07-01 02:18:00', '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(29, 1030, 60, 56, '2025-07-16 02:49:00', '2025-10-03 03:21:13', '2025-10-03 03:21:13'),
(30, 1031, 60, 59, '2025-07-12 04:33:00', '2025-10-03 03:21:13', '2025-10-03 03:21:13'),


-- --------------------------------------------------------

--
-- Table structure for table `team_members`
--

CREATE TABLE `team_members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `daily_team_id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `team_members`
--

INSERT INTO `team_members` (`id`, `daily_team_id`, `employee_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-10-04 14:45:31', '2025-10-04 14:45:31'),
(2, 1, 8, '2025-10-04 14:45:31', '2025-10-04 14:45:31'),
(3, 2, 2, '2025-10-04 14:45:31', '2025-10-04 14:45:31'),
(4, 2, 7, '2025-10-04 14:45:31', '2025-10-04 14:45:31'),
(5, 3, 3, '2025-10-04 14:45:31', '2025-10-04 14:45:31'),
(6, 3, 6, '2025-10-04 14:45:31', '2025-10-04 14:45:31'),
(7, 3, 11, '2025-10-04 14:45:31', '2025-10-04 14:45:31'),
(8, 4, 4, '2025-10-04 14:45:31', '2025-10-04 14:45:31'),
(9, 4, 9, '2025-10-04 14:45:31', '2025-10-04 14:45:31'),
(10, 5, 5, '2025-10-04 14:45:31', '2025-10-04 14:45:31'),
(11, 5, 10, '2025-10-04 14:45:31', '2025-10-04 14:45:31'),
(12, 6, 1, '2025-10-04 14:45:31', '2025-10-04 14:45:31'),
(13, 6, 3, '2025-10-04 14:45:31', '2025-10-04 14:45:31'),
(16, 14, 4, '2025-10-04 06:56:08', '2025-10-04 06:56:08'),
(17, 14, 3, '2025-10-04 06:56:08', '2025-10-04 06:56:08');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','employee','external_client') NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@opticrew.com', NULL, '$2y$10$C/Y15/YOU5NHpf0zFtpsDO6RL2R.HMjTRi3C2rfA0eizMVOwEBvq2', 'admin', 'Cjyg8pP2NQALRrbtifV3r9YgvlhnvvUTXLdwOE9m6NgRRg06RLtw7g9nRJz2', '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(2, 'Vincent Rey Digol', 'vincentreydigol@finnoys.com', NULL, '$2y$10$imi1zHLwUCdLQOg5.k39w.7XiWvU6DOoBcIjwSD624Q07XQqAzTQa', 'employee', NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(3, 'Martin Yvann Leonardo', 'martinyvannleonardo@finnoys.com', NULL, '$2y$10$fEn6ftE4hV6qwLE6Pu7i5uTTpwHeEKyXtviZ7oTI7mh2hKzE660GS', 'employee', NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(4, 'Earl Leonardo', 'earlleonardo@finnoys.com', NULL, '$2y$10$UyZcyuwzjz1SrB6dPNZ3J.hCGWcCJ9ixAA0NP59Y7n9tkR/1JGdDW', 'employee', NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(5, 'Merlyn Guzman', 'merlynguzman@finnoys.com', NULL, '$2y$10$KpT8QxYAqhp9HwVke31Wz.TEFENNoGFJQFdjhKUyyDJnFccFetEBi', 'employee', NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(6, 'Aries Guzman', 'ariesguzman@finnoys.com', NULL, '$2y$10$h85peir4XiLSdbJKuonExOoegZi/SBqQRGQ0xSYR5CzaERjEa2N3S', 'employee', NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(7, 'Bella Ostan', 'bellaostan@finnoys.com', NULL, '$2y$10$Zwd38lfJ4T5iG8JVXSxhBusbyZG/V4UWW8CePueSyXBh/IsY9D2Ju', 'employee', NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(8, 'Jennylyn Saballero', 'jennylynsaballero@finnoys.com', NULL, '$2y$10$zFxo49djPY3wMpN51qiYiOVxkkofeQ0RYRROfaphiP.cfs5BvHMQq', 'employee', NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(9, 'Rizza Estrella ', 'rizzaestrella@finnoys.com', NULL, '$2y$10$eH52zyHzV/ka6DJ1I/jIEegO6v7Bhtz3X6Tu51W7qzOCzg3.weuBy', 'employee', NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(10, 'Cherrylyn Morales ', 'cherrylynmorales@finnoys.com', NULL, '$2y$10$1swK95thyuDIKhCj1CDFJ.T591GF9ysJ4KvKJRaTfAiV7QTdU9Yaa', 'employee', NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(11, 'John Carl Morales', 'johncarlmorales@finnoys.com', NULL, '$2y$10$rLfpy6PWrrJYqVOkZUyFa.cLOLrTwLbKfkdp7tAS/vLQUotLTYOhi', 'employee', NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(12, 'John Kevin Morales', 'johnkevinmorales@finnoys.com', NULL, '$2y$10$/CV5zvi4rk3qSqg6JuYsme9FU7O06qgGq2eKBtDTSJXGhSPRAUAt.', 'employee', NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(13, 'client1', 'emmausldigol@gmail.com', NULL, '$2y$10$amSUxlc2clJ/5jOrYkUHWONE9Bkg1FgNY4pv6uBUjIlCnSR/Su1QK', 'external_client', NULL, '2025-10-15 00:20:16', '2025-10-15 10:08:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendances_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `clients_user_id_foreign` (`user_id`);

--
-- Indexes for table `contracted_clients`
--
ALTER TABLE `contracted_clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `contracted_clients_name_unique` (`name`);

--
-- Indexes for table `daily_team_assignments`
--
ALTER TABLE `daily_team_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `daily_team_assignments_car_id_foreign` (`car_id`),
  ADD KEY `daily_team_assignments_contracted_client_id_foreign` (`contracted_client_id`);

--
-- Indexes for table `day_offs`
--
ALTER TABLE `day_offs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `day_offs_employee_id_date_unique` (`employee_id`,`date`),
  ADD KEY `day_offs_date_index` (`date`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employees_user_id_foreign` (`user_id`);

--
-- Indexes for table `employee_schedules`
--
ALTER TABLE `employee_schedules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_schedules_employee_id_work_date_unique` (`employee_id`,`work_date`);

--
-- Indexes for table `invalid_tasks`
--
ALTER TABLE `invalid_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invalid_tasks_task_id_index` (`task_id`),
  ADD KEY `invalid_tasks_optimization_result_id_index` (`optimization_result_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `locations_contracted_client_id_foreign` (`contracted_client_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `optimization_generations`
--
ALTER TABLE `optimization_generations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `opt_gen_run_gen_idx` (`optimization_run_id`,`generation_number`);

--
-- Indexes for table `optimization_results`
--
ALTER TABLE `optimization_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `optimization_results_client_id_foreign` (`client_id`),
  ADD KEY `optimization_results_service_date_client_id_index` (`service_date`,`client_id`);

--
-- Indexes for table `optimization_runs`
--
ALTER TABLE `optimization_runs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `optimization_runs_triggered_by_task_id_foreign` (`triggered_by_task_id`);

--
-- Indexes for table `optimization_schedules`
--
ALTER TABLE `optimization_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `optimization_schedules_optimization_generation_id_foreign` (`optimization_generation_id`);

--
-- Indexes for table `payroll_reports`
--
ALTER TABLE `payroll_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payroll_reports_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `scenario_analyses`
--
ALTER TABLE `scenario_analyses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `scenario_analyses_service_date_scenario_type_index` (`service_date`,`scenario_type`);

--
-- Indexes for table `scheduling_logs`
--
ALTER TABLE `scheduling_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tasks_location_id_foreign` (`location_id`),
  ADD KEY `tasks_client_id_foreign` (`client_id`),
  ADD KEY `tasks_assigned_team_id_foreign` (`assigned_team_id`),
  ADD KEY `tasks_optimization_run_id_foreign` (`optimization_run_id`);

--
-- Indexes for table `task_performance_histories`
--
ALTER TABLE `task_performance_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_performance_histories_task_id_foreign` (`task_id`);

--
-- Indexes for table `team_members`
--
ALTER TABLE `team_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `team_members_daily_team_id_foreign` (`daily_team_id`),
  ADD KEY `team_members_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2064;

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contracted_clients`
--
ALTER TABLE `contracted_clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `daily_team_assignments`
--
ALTER TABLE `daily_team_assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=241;

--
-- AUTO_INCREMENT for table `day_offs`
--
ALTER TABLE `day_offs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `employee_schedules`
--
ALTER TABLE `employee_schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `invalid_tasks`
--
ALTER TABLE `invalid_tasks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `optimization_generations`
--
ALTER TABLE `optimization_generations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `optimization_results`
--
ALTER TABLE `optimization_results`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `optimization_runs`
--
ALTER TABLE `optimization_runs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `optimization_schedules`
--
ALTER TABLE `optimization_schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll_reports`
--
ALTER TABLE `payroll_reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `scenario_analyses`
--
ALTER TABLE `scenario_analyses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `scheduling_logs`
--
ALTER TABLE `scheduling_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3481;

--
-- AUTO_INCREMENT for table `task_performance_histories`
--
ALTER TABLE `task_performance_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1033;

--
-- AUTO_INCREMENT for table `team_members`
--
ALTER TABLE `team_members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=506;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `clients`
--
ALTER TABLE `clients`
  ADD CONSTRAINT `clients_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `daily_team_assignments`
--
ALTER TABLE `daily_team_assignments`
  ADD CONSTRAINT `daily_team_assignments_car_id_foreign` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`),
  ADD CONSTRAINT `daily_team_assignments_contracted_client_id_foreign` FOREIGN KEY (`contracted_client_id`) REFERENCES `contracted_clients` (`id`);

--
-- Constraints for table `day_offs`
--
ALTER TABLE `day_offs`
  ADD CONSTRAINT `day_offs_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_schedules`
--
ALTER TABLE `employee_schedules`
  ADD CONSTRAINT `employee_schedules_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invalid_tasks`
--
ALTER TABLE `invalid_tasks`
  ADD CONSTRAINT `invalid_tasks_optimization_result_id_foreign` FOREIGN KEY (`optimization_result_id`) REFERENCES `optimization_results` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invalid_tasks_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `locations`
--
ALTER TABLE `locations`
  ADD CONSTRAINT `locations_contracted_client_id_foreign` FOREIGN KEY (`contracted_client_id`) REFERENCES `contracted_clients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `optimization_generations`
--
ALTER TABLE `optimization_generations`
  ADD CONSTRAINT `optimization_generations_optimization_run_id_foreign` FOREIGN KEY (`optimization_run_id`) REFERENCES `optimization_runs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `optimization_results`
--
ALTER TABLE `optimization_results`
  ADD CONSTRAINT `optimization_results_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `optimization_runs`
--
ALTER TABLE `optimization_runs`
  ADD CONSTRAINT `optimization_runs_triggered_by_task_id_foreign` FOREIGN KEY (`triggered_by_task_id`) REFERENCES `tasks` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `optimization_schedules`
--
ALTER TABLE `optimization_schedules`
  ADD CONSTRAINT `optimization_schedules_optimization_generation_id_foreign` FOREIGN KEY (`optimization_generation_id`) REFERENCES `optimization_generations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payroll_reports`
--
ALTER TABLE `payroll_reports`
  ADD CONSTRAINT `payroll_reports_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_assigned_team_id_foreign` FOREIGN KEY (`assigned_team_id`) REFERENCES `daily_team_assignments` (`id`),
  ADD CONSTRAINT `tasks_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  ADD CONSTRAINT `tasks_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`),
  ADD CONSTRAINT `tasks_optimization_run_id_foreign` FOREIGN KEY (`optimization_run_id`) REFERENCES `optimization_runs` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `task_performance_histories`
--
ALTER TABLE `task_performance_histories`
  ADD CONSTRAINT `task_performance_histories_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `team_members`
--
ALTER TABLE `team_members`
  ADD CONSTRAINT `team_members_daily_team_id_foreign` FOREIGN KEY (`daily_team_id`) REFERENCES `daily_team_assignments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `team_members_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
