-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 29, 2026 at 07:59 PM
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
-- Database: `charity`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `aemail` varchar(100) NOT NULL,
  `apassword` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`aemail`, `apassword`) VALUES
('admin1@givingsphere.com', 'Petit101@'),
('admin@givingsphere.com', 'Petit101#'),
('admin@gmail.com', 'a');

-- --------------------------------------------------------

--
-- Table structure for table `beneficiaries`
--

CREATE TABLE `beneficiaries` (
  `beneficiary_id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `beneficiaries`
--

INSERT INTO `beneficiaries` (`beneficiary_id`, `first_name`, `last_name`, `email`, `phone_number`, `address`, `created_at`, `updated_at`) VALUES
(1, 'Alice', 'Nyambura', 'nyamburalice@gmail.com', '0729307009', 'Rongai', '2024-05-29 20:41:26', '2024-07-16 10:10:54'),
(2, 'John', 'Davis', 'johndavis@gmail.com', '0725354729', 'Ngong', '2024-05-29 20:41:26', '2024-07-16 10:21:01'),
(5, 'James', 'Kimutai', 'jamessang78@gmail.com', '0739307009', '55', '2024-07-15 22:41:03', '2024-07-15 23:03:18'),
(7, 'Denis', 'Kitur', 'denis@gmail.com', '0745180311', '55', '2024-07-15 23:46:47', '2024-07-15 23:53:07');

-- --------------------------------------------------------

--
-- Table structure for table `campaigns`
--

CREATE TABLE `campaigns` (
  `campaign_id` int(11) NOT NULL,
  `campaign_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `donation_id` int(11) NOT NULL,
  `donorid` int(11) NOT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `donation_date` date DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `beneficiary_id` int(11) DEFAULT NULL,
  `event_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`donation_id`, `donorid`, `amount`, `donation_date`, `payment_status`, `created_at`, `updated_at`, `beneficiary_id`, `event_id`) VALUES
(5, 3, 6.00, '2024-06-26', 'credit_card', '2024-06-26 09:48:07', '2024-06-26 09:48:07', NULL, NULL),
(18, 5, 45.00, '2024-06-27', 'credit_card', '2024-06-26 21:32:09', '2024-06-26 21:32:09', NULL, NULL),
(19, 5, 569.00, '2024-06-27', 'credit_card', '2024-06-27 06:01:43', '2024-06-27 06:01:43', NULL, NULL),
(20, 5, 565.00, '2024-06-27', 'paypal', '2024-06-27 14:22:47', '2024-06-27 14:22:47', NULL, NULL),
(21, 5, 6.00, '2024-06-27', 'credit_card', '2024-06-27 16:48:52', '2024-06-27 16:48:52', NULL, NULL),
(22, 4, 500.00, '2024-06-27', 'credit_card', '2024-06-27 16:53:06', '2024-06-27 16:53:06', NULL, NULL),
(23, 21, 700.00, '2024-06-27', 'credit_card', '2024-06-27 20:51:32', '2024-06-27 20:51:32', NULL, NULL),
(24, 21, 300.00, '2024-06-27', 'credit_card', '2024-06-27 20:52:32', '2024-06-27 20:52:32', NULL, NULL),
(25, 26, 200.00, '2024-06-28', 'paypal', '2024-06-27 21:46:34', '2024-06-27 21:46:34', NULL, NULL),
(26, 26, 600.00, '2024-06-28', 'mpesa', '2024-06-27 21:47:06', '2024-06-27 21:47:06', NULL, NULL),
(27, 28, 200.00, '2024-06-28', 'paypal', '2024-06-28 04:58:41', '2024-06-28 04:58:41', NULL, NULL),
(28, 30, 6.00, '2024-06-28', 'paypal', '2024-06-28 06:07:18', '2024-06-28 06:07:18', NULL, NULL),
(29, 31, 700.00, '2024-06-28', 'paypal', '2024-06-28 09:13:07', '2024-06-28 09:13:07', NULL, NULL),
(30, 31, 50.00, '2024-06-28', 'paypal', '2024-06-28 09:13:20', '2024-06-28 09:13:20', NULL, NULL),
(31, 28, 678.00, '2024-06-28', 'paypal', '2024-06-28 12:35:50', '2024-06-28 12:35:50', NULL, NULL),
(32, 32, 1000.00, '2024-06-28', 'paypal', '2024-06-28 17:39:11', '2024-06-28 17:39:11', NULL, NULL),
(33, 5, 200.00, '2024-06-29', 'mpesa', '2024-06-29 09:06:54', '2024-06-29 09:06:54', NULL, NULL),
(34, 35, 700.00, '2024-06-29', 'mpesa', '2024-06-29 10:04:20', '2024-06-29 10:04:20', NULL, NULL),
(35, 35, 400.00, '2024-06-29', 'paypal', '2024-06-29 10:04:38', '2024-06-29 10:04:38', NULL, NULL),
(36, 5, 5.00, '2024-07-02', '', '2024-07-02 09:30:35', '2024-07-02 09:30:35', NULL, NULL),
(37, 5, 5.00, '2024-07-02', '', '2024-07-02 09:30:58', '2024-07-02 09:30:58', NULL, NULL),
(38, 5, 5.00, '2024-07-02', '', '2024-07-02 09:32:30', '2024-07-02 09:32:30', NULL, NULL),
(39, 5, 5.00, '2024-07-02', '', '2024-07-02 09:35:19', '2024-07-02 09:35:19', NULL, NULL),
(40, 5, 5.00, '2024-07-02', '', '2024-07-02 09:35:23', '2024-07-02 09:35:23', NULL, NULL),
(41, 5, 5.00, '2024-07-02', '', '2024-07-02 09:35:28', '2024-07-02 09:35:28', NULL, NULL),
(42, 5, 7.00, '2024-07-12', '', '2024-07-12 13:42:11', '2024-07-12 13:42:11', NULL, NULL),
(43, 5, 7.00, '2024-07-12', '', '2024-07-12 13:42:25', '2024-07-12 13:42:25', NULL, NULL),
(44, 5, 7.00, '2024-07-12', '', '2024-07-12 13:42:29', '2024-07-12 13:42:29', NULL, NULL),
(45, 5, 7.00, '2024-07-12', '', '2024-07-12 13:44:09', '2024-07-12 13:44:09', NULL, NULL),
(46, 4, 20.00, '2024-07-13', '', '2024-07-13 14:32:53', '2024-07-13 14:32:53', NULL, NULL),
(47, 4, 789.00, '2024-07-13', '', '2024-07-13 14:33:19', '2024-07-13 14:33:19', NULL, NULL),
(48, 37, 2.00, '2024-07-15', '', '2024-07-15 13:46:18', '2024-07-15 13:46:18', NULL, NULL),
(49, 37, 5.00, '2024-07-15', 'paypal', '2024-07-15 21:20:49', '2024-07-15 21:20:49', NULL, NULL),
(50, 37, 5.00, '2024-07-15', 'paypal', '2024-07-15 21:23:01', '2024-07-15 21:23:01', NULL, NULL),
(51, 37, 6.00, '2024-07-15', 'paypal', '2024-07-16 01:00:55', '2024-07-16 01:00:55', NULL, NULL),
(52, 38, 500.00, '2024-07-16', 'paypal', '2024-07-16 11:14:51', '2024-07-16 11:14:51', NULL, NULL),
(53, 37, 6.00, '2024-07-16', 'paypal', '2024-07-16 11:26:23', '2024-07-16 11:26:23', NULL, NULL),
(54, 39, 4.00, '2024-07-19', 'paypal', '2024-07-19 15:35:34', '2024-07-19 15:35:34', NULL, NULL),
(55, 37, 1.00, '2024-07-19', 'paypal', '2024-07-19 23:53:41', '2024-07-19 23:53:41', NULL, NULL),
(56, 40, 1.00, '2024-07-19', 'paypal', '2024-07-20 00:05:59', '2024-07-20 00:05:59', NULL, NULL),
(57, 38, 600.00, '2024-07-23', 'paypal', '2024-07-23 09:07:50', '2024-07-23 09:07:50', NULL, NULL),
(58, 42, 100.00, '2024-07-23', 'paypal', '2024-07-23 18:09:47', '2024-07-23 18:09:47', NULL, NULL),
(59, 42, 100.00, '2024-07-23', 'paypal', '2024-07-23 18:11:46', '2024-07-23 18:11:46', NULL, NULL),
(73, 36, 7.00, '2024-07-25', 'paypal', '2024-07-25 12:52:07', '2024-07-25 12:52:07', NULL, NULL),
(74, 36, 4.00, '2024-07-26', 'paypal', '2024-07-26 07:09:39', '2024-07-26 07:09:39', NULL, NULL),
(75, 36, 6.00, '2024-07-26', 'paypal', '2024-07-26 07:12:16', '2024-07-26 07:12:16', NULL, NULL),
(76, 3, 4.00, '2024-07-27', 'paypal', '2024-07-27 10:46:14', '2024-07-27 10:46:14', NULL, NULL),
(77, 3, 5.00, '2024-07-27', 'paypal', '2024-07-27 10:47:51', '2024-07-27 10:47:51', NULL, NULL),
(78, 43, 10.00, '2026-09-23', 'Pending', '2026-09-23 10:40:08', '2026-09-23 10:40:08', NULL, NULL),
(79, 43, 10000.00, '2026-09-23', 'Pending', '2026-09-23 18:57:47', '2026-09-23 18:57:47', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `donors`
--

CREATE TABLE `donors` (
  `donorid` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `donemail` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum('male','female') NOT NULL,
  `donpassword` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donors`
--

INSERT INTO `donors` (`donorid`, `first_name`, `last_name`, `donemail`, `phone`, `address`, `dob`, `gender`, `donpassword`, `created_at`, `updated_at`) VALUES
(3, 'James', 'Kimutai', 'james@gmail.com', '0729307070', '55', '0003-12-23', 'male', '$2y$10$HoCR.wEzk4VVPNrEZC1JuO/RAbTC2tOzcnP1ho4IOmJiwxxM9Wz3e', '2024-06-26 09:16:36', '2024-07-13 18:27:52'),
(4, 'Paul', 'Kimutai', 'paul@gmail.com', '0719878998', '55', '2024-06-26', 'male', '$2y$10$g5aJ6O4HlbJbWDk5vq4ta.Z.UuKBdrOqkV.zGbPULsHJc2WPURtKm', '2024-06-26 09:56:55', '2024-07-13 22:39:07'),
(5, 'Neomi ', 'Wambui', 'wambuiparsanka@gmail.com', '0740090437', '55', '2005-06-13', 'female', '$2y$10$J24.4/XWG6gmmEwrvt.OW.qqjP/PcZhwrTGr3gNPUqn0mB.VM9CMq', '2024-06-26 21:25:00', '2024-06-28 08:35:22'),
(11, 'James', 'Kimutai', 'jamess78@gmail.com', '076507009', '55', '0007-05-06', 'male', '$2y$10$2Nu1hXHpECd6aTBJrYOsS.WFbbV8tvw1PE5G/UrtNZwZjotdaFjWu', '2024-06-27 11:42:53', '2024-06-27 11:42:53'),
(12, 'James', 'Kimutai', 'jag78@gmail.com', '0725507009', '55', '0022-12-12', 'male', '$2y$10$hX/Yr25UHBqTcd0L82xmQeMEE0bIYLcsw/EOrerk8GE.mvpBi5GvC', '2024-06-27 11:56:06', '2024-06-27 11:56:06'),
(13, 'James', 'Kimutai', 'jases78@gmail.com', '0722207009', '55', '0022-12-22', 'male', '$2y$10$caIgvuaRCA3qU67.Bg34.u5cpC2ZWJEJAYoRPMGdnTh1c5hUU/F82', '2024-06-27 11:58:25', '2024-06-27 11:58:25'),
(14, 'James', 'Kimutai', 'jamd8@gmail.com', '072329307009', '55', '0003-02-23', 'male', '$2y$10$EGo6syH0VW1QIMFyO1cJf.IKGSKNvVul0sFDcxJhHcWWt3mOu1Eq2', '2024-06-27 12:07:02', '2024-06-27 12:07:02'),
(15, 'James', 'Kimutai', 'hy@gmal.com', '789', '55', '5678-04-04', 'male', '$2y$10$S9x0G7m66xXQIDPsmnxSV.28bipjNSHSlTuGP2E.VJuyJ7zsx/DDC', '2024-06-27 12:12:22', '2024-06-27 12:12:22'),
(16, 'James', 'Kimutai', 'jamessang78@gmail.com', '0729307009', '55', '0001-11-11', 'male', '$2y$10$1rMd7EN55TdrtuPyOcH.4ed4vHskb6cA1qflZx.Wa1.yWBjEKYAkG', '2024-06-27 12:40:07', '2024-06-27 12:40:07'),
(17, 'James', 'Kimutai', 'jamessang78@gmail.com', '0729307009', '55', '0678-07-05', 'male', '$2y$10$/NQe0Rdzl3srsT3gbcMqDOceYdBEEYJ6G/sv1MWWuirRkpfANEwry', '2024-06-27 12:40:20', '2024-06-27 12:40:20'),
(18, 'James', 'Kimutai', 'jamang78@gmail.com', '9876', '55', '0078-05-06', 'male', '$2y$10$aErmfypxX2s9FkDnAbXKoeDgkyQdEdk0p3v1d3ZL2h5R5SkBGsfj2', '2024-06-27 12:56:48', '2024-06-27 12:56:48'),
(20, 'James', 'Kimutai', 'jamila@gmail.com', '76678', '55', '0007-08-07', 'male', '$2y$10$hvYT1Xxk3sgOzIyAAARneu3lC7dNTc9Ev72M7V9H0Gn1b5fLhkYVK', '2024-06-27 13:17:27', '2024-06-27 13:17:27'),
(21, 'Mwatao', 'Jameson', 'matao@gmail.com', '07577875', '55', '0678-04-05', 'male', '$2y$10$RwMQmV0oO0y8kjxC4bR8vOWQWp4SVZc.Ow1DIg3nve9ojML54aVuu', '2024-06-27 20:42:05', '2024-06-27 20:42:05'),
(22, 'James', 'Kimutai', 'jamg78@gmail.com', '980-98', '55', '0078-05-06', 'male', '$2y$10$Jvaqtwkb/WJP7B7AdV8Nuu.z8o6nxJb11zEY45ugp7Y11xUq6zq4u', '2024-06-27 21:29:18', '2024-06-27 21:29:18'),
(23, 'James', 'Kimutai', 'jamh8@gmail.com', '767890', '55', '0789-05-06', 'male', '$2y$10$idxK8BFBQMslE5InkhUqY.aM5zNVPu56cb1V4.07JvAIG5foESILS', '2024-06-27 21:29:45', '2024-06-27 21:29:45'),
(24, 'James', 'Kimutai', 'jamo8@gmail.com', '0729707009', '55', '2000-02-02', 'male', '$2y$10$FFEt/rXUss61mI10VAmVoOi69d0h/KHJE48ioNERg07Wihf7GK1U6', '2024-06-27 21:37:32', '2024-06-27 21:37:32'),
(25, 'j', 's', 'j@g.com', '3232323232', '7', '2000-11-12', 'male', '$2y$10$4ulQhcD2Xhv/lVOYc1q37OzJNFgamGp.gnw0VNaELUI0w.WKjct9K', '2024-06-27 21:44:51', '2024-06-27 21:44:51'),
(26, 'Moses', 'Nzuga', 'nzuga@gmail.com', '0729878766', '55', '1999-02-01', 'male', '$2y$10$E5fp5pzVtHuvZ3s/VsUtwuE6RxG7uHCSN1HREMn6HnaZe/bmfil7m', '2024-06-27 21:46:04', '2024-06-27 21:46:04'),
(27, 'Michael', 'Ndegwa', 'ndegwa@gmail.com', '0769672609', '55', '2008-02-25', 'male', '$2y$10$c68fUd65.b0NyYIPOHkQtOWdlu/KrQShomhVDkow10f9qdezMvniC', '2024-06-28 04:10:00', '2024-06-28 04:10:00'),
(28, 'Njenga', 'James', 'njenga@gmail.com', '0723244764', '55', '2000-12-12', 'male', '$2y$10$ki.PXbnZJ6cpn9iL0Wt1l.HD7Cz3m.og.L5Z3l7Luwip8LVx8RDK.', '2024-06-28 04:49:45', '2024-06-28 04:49:45'),
(29, 'Zandy', 'Geit', 'admin@gmail.com', '0752882625', '67', '1999-02-02', 'male', '$2y$10$FF1/UFHFFts3LsoBobnDmOodHhdKWhzTp6E2uAGCMUNDbXEUoSTK6', '2024-06-28 05:15:32', '2024-06-28 05:15:32'),
(30, 'Admin', 'Jamo', 'admin@givingsphere.com', '0729323409', '76', '2001-04-06', 'male', '$2y$10$uh1YMGBBl49JeQNx228WcOEBFD.FtrWXAW4ni8MLuS28rdjWgjaQa', '2024-06-28 05:24:10', '2024-07-25 12:33:05'),
(31, 'Ndiema', 'John', 'ndiema@gmail.com', '0723534265', '55', '2007-06-05', 'male', '$2y$10$vgGSM8/XkbOIuidKHoh.nemly5H1ysHICIHZ2wxpUfgIUvvhH.AOS', '2024-06-28 09:04:40', '2024-06-28 09:04:40'),
(32, 'John', 'Bede', 'achiengjohn@gmail.com', '1234567890', '33', '0012-12-12', 'male', '$2y$10$B64iWSUm/Z2pkA/A.P3yHeRJfyKzT4Swc6uPMIztlLaQnYs8peqfC', '2024-06-28 17:38:42', '2024-06-28 17:39:51'),
(34, 'Omondi', 'Jacob', 'omondi@gmail.com', '0723452324', '23', '1999-02-02', 'male', '$2y$10$wUoiZZztxNifkxE3Tm8LluMAhkdm1.6rENtzuwLu05QJ1qKuDRaZy', '2024-06-29 08:44:04', '2024-06-29 08:44:04'),
(35, 'Moses', 'Oneko', '0@gmail.com', '0202014488', 'x', '2004-02-02', 'male', '$2y$10$OTuoMn.a5eYR/wNnbnfkG.IC6B3s8E.xCsGZ5DCs.7X1kO.drJnWS', '2024-06-29 10:03:09', '2024-06-29 10:03:09'),
(36, 'luckyshar', 'christrine', 'christineluckyshar@gmail.com', '0110857312', 'ongata', '2004-04-09', 'female', '$2y$10$2zxCU5CUsMRuTdvgm7Uwzu.5358lVu4E1HnX0ty82TvwYy2zUDkXq', '2024-07-12 16:33:09', '2024-07-25 12:30:50'),
(37, 'Achungo', 'Kurstin', 'achungo8@gmail.com', '0798425309', '55', '2003-07-09', 'female', '$2y$10$DxnH9B9SZwgZ0OuyjIgmp.VqeK3H7nkpgf6so135Y40K6SGGAV3JS', '2024-07-13 22:49:27', '2024-07-24 06:20:08'),
(38, 'John', 'Mwai', 'njengajohnmwai@gmail.com', '0111511358', 'KONA BLDG 1ST FLR KIMATHI WAY, NYERI', '1999-10-28', 'male', '$2y$10$PM6bJ4yZ6zzryJvr.hmQVOkRhL5ULLns/ucQhfn1g0kLFzWuV69my', '2024-07-14 18:43:35', '2024-07-14 18:43:35'),
(39, 'Job', 'Job', 'job@gmail.com', '0789897788', '21', '2000-12-12', 'male', '$2y$10$fm00e5fLt5Bwn87qEafkpOiyKLMTHo5siKUMwjmi4sRg/Xmh6sIJW', '2024-07-19 15:33:46', '2024-07-19 15:33:46'),
(40, 'Mercy', 'Thuku', 'mercy9@gmail.com', '0757679910', 'Rongai', '1999-12-29', 'female', '$2y$10$TUnh.LpkabfEXKd2GqMZSedJCiO9ThhwLH1QTcZnTkmRoKcn5.oka', '2024-07-20 00:04:31', '2024-07-20 00:04:31'),
(41, 'Emily', 'Wambui', 'emily@gmail.com', '0735645478', '623-00600 Nairobi ', '2005-01-14', 'female', '$2y$10$pbJdW/H/WJJUF/SmbRvnEuw.rASbPnN9ABeNxtUAp7IkQLl8DMedG', '2024-07-21 07:56:43', '2024-07-21 07:56:43'),
(42, 'Mercy', 'Chebet', 'mercy@gmail.com', '0788977466', '00200', '1998-07-23', 'female', '$2y$10$3iVHSkpPLwIYGzHpCYYMIuu.pCMQtMbFsxx3gvUEEaCOVbtide3tS', '2024-07-23 18:06:05', '2024-07-23 18:06:05'),
(43, 'Michael', 'Mwanza', 'surusiadrian@gmail.com', '0784612294', 'Langata', '2006-09-29', 'male', '$2y$10$NfqpgWd.OXG2cxWtuJsynOvFZQDnC/NwJgB2hqOPiu1kBhsvrG8OG', '2026-09-23 10:34:19', '2026-09-23 10:34:19');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `event_date` date NOT NULL,
  `event_location` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `campaign_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `event_name`, `event_date`, `event_location`, `created_at`, `updated_at`, `campaign_id`) VALUES
(1, 'Comunity Clean', '2024-07-24', 'Rongai', '2024-07-16 10:17:29', '2024-07-16 10:17:29', NULL),
(2, 'Recreation', '2024-07-25', 'Bees Park', '2024-07-16 13:31:55', '2024-07-16 13:43:55', NULL),
(3, 'Meeting', '2024-07-31', 'Bees Park', '2024-07-16 17:26:56', '2024-07-16 20:09:22', NULL),
(4, 'Comunity Cleaning', '2024-07-31', 'Rimpa', '2024-07-16 17:42:45', '2024-07-16 17:42:56', NULL),
(5, 'Meeting hjsdfgh', '2024-08-01', 'sdfgyhujkl', '2024-07-24 10:27:09', '2024-07-24 10:27:09', NULL),
(6, 'Recreation', '2024-07-08', 'Nakuru', '2024-07-25 00:35:10', '2024-07-25 00:35:10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `donor_id` int(11) NOT NULL,
  `event_id` int(11) DEFAULT NULL,
  `donation_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read') NOT NULL DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `donor_id`, `event_id`, `donation_id`, `message`, `status`, `created_at`) VALUES
(1, 5, 2, NULL, 'Your participation request for event ID 2 has been Approved.', 'read', '2024-07-25 06:29:38'),
(2, 5, 2, NULL, 'Your participation request for event ID 2 has been Rejected.', 'read', '2024-07-25 06:29:48'),
(3, 5, 4, NULL, 'Your participation request for event ID 4 has been Rejected.', 'read', '2024-07-25 06:36:24'),
(4, 5, 2, NULL, 'Your participation request for event ID 2 has been Rejected.', 'read', '2024-07-25 07:30:22'),
(5, 5, 2, NULL, 'Your participation request for event ID 2 has been Rejected.', 'read', '2024-07-25 07:45:31'),
(6, 5, 2, NULL, 'Your participation request for event ID 2 has been Approved.', 'read', '2024-07-25 07:50:41'),
(7, 5, 2, NULL, 'Your participation request for event ID 2 has been Rejected.', 'read', '2024-07-25 08:25:56'),
(8, 10, 6, NULL, 'Your participation request for event ID 6 has been Rejected.', 'unread', '2024-07-25 09:11:25'),
(9, 4, 2, NULL, 'Your participation request for event ID 2 has been Rejected.', 'read', '2024-07-25 09:11:38'),
(10, 5, 2, NULL, 'Your participation request for event ID 2 has been Approved.', 'read', '2024-07-25 09:43:24'),
(11, 5, 2, NULL, 'Your participation request for event ID 2 has been Approved.', 'read', '2024-07-25 10:03:08'),
(12, 5, 2, NULL, 'Your participation request for event ID 2 has been Approved.', 'unread', '2024-07-25 10:10:27'),
(13, 5, 2, NULL, 'Your participation request for event ID 2 has been Rejected.', 'unread', '2024-07-25 10:10:42'),
(14, 4, 4, NULL, 'Your participation request for event ID 4 has been Approved.', 'read', '2024-07-25 12:25:42'),
(15, 36, 1, NULL, 'Your participation request for event ID 1 has been Approved.', 'read', '2024-07-25 12:51:23');

-- --------------------------------------------------------

--
-- Table structure for table `participation_requests`
--

CREATE TABLE `participation_requests` (
  `request_id` int(11) NOT NULL,
  `donorid` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `participation_requests`
--

INSERT INTO `participation_requests` (`request_id`, `donorid`, `event_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 5, 2, 'Rejected', '2024-07-16 14:58:21', '2024-07-25 10:10:42'),
(2, 5, 3, 'Rejected', '2024-07-16 15:08:00', '2024-07-17 13:44:21'),
(3, 36, 3, 'Rejected', '2024-07-16 15:25:51', '2024-07-17 14:03:38'),
(4, 36, 4, 'Rejected', '2024-07-16 15:31:07', '2024-07-16 16:23:30'),
(5, 36, 2, 'Rejected', '2024-07-16 16:26:34', '2024-07-16 16:29:37'),
(6, 5, 4, 'Rejected', '2024-07-16 16:28:26', '2024-07-24 06:25:52'),
(7, 41, 3, 'Approved', '2024-07-17 13:52:49', '2024-07-17 13:59:50'),
(8, 10, 6, 'Rejected', '2024-07-24 21:19:54', '2024-07-25 09:11:25'),
(9, 4, 2, 'Rejected', '2024-07-25 09:11:11', '2024-07-25 09:11:38'),
(79, 4, 4, 'Approved', '2024-07-25 12:24:31', '2024-07-25 12:25:42'),
(80, 36, 1, 'Approved', '2024-07-25 12:51:03', '2024-07-25 12:51:23'),
(81, 3, 1, 'Pending', '2024-11-13 06:49:19', '2024-11-13 06:49:19'),
(82, 43, 1, 'Pending', '2026-09-23 10:42:55', '2026-09-23 10:42:55'),
(83, 43, 1, 'Pending', '2026-09-23 10:43:03', '2026-09-23 10:43:03'),
(84, 43, 2, 'Pending', '2026-09-23 10:43:09', '2026-09-23 10:43:09');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expire` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `expire`) VALUES
(1, 'achungo8@gmail.com', 'be29dbaf6ba932e950770b4155a40a33d405de30f0dca64bd6fe178dadf4f6f062cbd95c6344cd9e94e4e4c0684516d1fb5c', 1721504103);

-- --------------------------------------------------------

--
-- Table structure for table `replies`
--

CREATE TABLE `replies` (
  `reply_id` int(11) NOT NULL,
  `notification_id` int(11) NOT NULL,
  `donor_id` int(11) NOT NULL,
  `reply_message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `replies`
--

INSERT INTO `replies` (`reply_id`, `notification_id`, `donor_id`, `reply_message`, `created_at`) VALUES
(1, 6, 5, 'jtjtj', '2024-07-25 08:18:08'),
(2, 6, 5, 'jtjtj\r\njg', '2024-07-25 08:18:35'),
(3, 6, 5, 'jtjtj\r\njg', '2024-07-25 08:18:57');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `donorid` int(11) NOT NULL,
  `transaction_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `transaction_amount` decimal(10,2) NOT NULL,
  `transaction_status` enum('Pending','Completed','Refunded','Cancelled') NOT NULL,
  `transaction_currency` varchar(3) NOT NULL,
  `transaction_description` text DEFAULT NULL,
  `paypal_transaction_id` varchar(50) NOT NULL,
  `paypal_payer_id` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `donorid`, `transaction_time`, `transaction_amount`, `transaction_status`, `transaction_currency`, `transaction_description`, `paypal_transaction_id`, `paypal_payer_id`, `created_at`, `updated_at`) VALUES
(1, 5, '2024-07-18 09:49:19', 5.00, 'Pending', '', NULL, '', '', '2024-07-18 09:49:19', '2024-07-18 09:49:19'),
(2, 5, '2024-07-18 09:59:06', 5.00, 'Pending', 'USD', 'Donation via PayPal', '', '', '2024-07-18 09:59:06', '2024-07-18 09:59:06'),
(3, 5, '2024-07-18 10:11:33', 5.00, 'Pending', 'USD', 'Donation', '', '', '2024-07-18 10:11:33', '2024-07-18 10:11:33'),
(4, 5, '2024-07-18 10:13:01', 5.00, 'Pending', 'USD', 'Donation', '', '', '2024-07-18 10:13:01', '2024-07-18 10:13:01'),
(5, 5, '2024-07-18 10:13:07', 5.00, 'Pending', 'USD', 'Donation', '', '', '2024-07-18 10:13:07', '2024-07-18 10:13:07'),
(6, 5, '2024-07-18 10:14:30', 5.00, 'Pending', 'USD', 'Donation', '', '', '2024-07-18 10:14:30', '2024-07-18 10:14:30'),
(7, 5, '2024-07-18 10:17:26', 5.00, 'Pending', 'USD', 'Donation via PayPal', '', '', '2024-07-18 10:17:26', '2024-07-18 10:17:26'),
(8, 5, '2024-07-18 10:31:33', 5.00, 'Pending', 'USD', 'Donation via PayPal', '', '', '2024-07-18 10:31:33', '2024-07-18 10:31:33'),
(9, 36, '2024-07-18 10:34:48', 3.00, 'Pending', 'USD', 'Donation via PayPal', '', '', '2024-07-18 10:34:48', '2024-07-18 10:34:48'),
(10, 3, '2024-07-27 10:47:51', 5.00, 'Pending', 'USD', 'Donation via PayPal', '', '', '2024-07-27 10:47:51', '2024-07-27 10:47:51');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `email` varchar(100) NOT NULL,
  `usertype` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`email`, `usertype`) VALUES
('0@gmail.com', 'd'),
('achiengjohn@gmail.com', 'd'),
('admin@givingsphere.com', 'a'),
('christineluckyshar@gmail.com', 'd'),
('duplexsang@gmail.com', 'd'),
('hy@gmal.com', 'd'),
('j@g.com', 'd'),
('jag78@gmail.com', 'd'),
('jam8@gmail.com', 'd'),
('jamang78@gmail.com', 'd'),
('jamd8@gmail.com', 'd'),
('james@gmail.com', 'd'),
('jamess78@gmail.com', 'd'),
('jamg78@gmail.com', 'd'),
('jamh8@gmail.com', 'd'),
('jamila@gmail.com', 'd'),
('jamo8@gmail.com', 'd'),
('jamo@gmai.com', 'd'),
('jases78@gmail.com', 'd'),
('jm@gmail.com', 'd'),
('kmo@gmail.com', 'd'),
('matao@gmail.com', 'd'),
('ndegwa@gmail.com', 'd'),
('ndiema@gmail.com', 'd'),
('njenga@gmail.com', 'd'),
('nzuga@gmail.com', 'd'),
('omondi@gmail.com', 'd'),
('paul@gmail.com', 'd'),
('surusiadrian@gmail.com', 'd'),
('waina7@gmail.com', 'd'),
('wambuiparsanka@gmail.com', 'd');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`aemail`);

--
-- Indexes for table `beneficiaries`
--
ALTER TABLE `beneficiaries`
  ADD PRIMARY KEY (`beneficiary_id`);

--
-- Indexes for table `campaigns`
--
ALTER TABLE `campaigns`
  ADD PRIMARY KEY (`campaign_id`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`donation_id`),
  ADD KEY `beneficiary_id` (`beneficiary_id`),
  ADD KEY `fk_donorid` (`donorid`),
  ADD KEY `fk_event_id` (`event_id`);

--
-- Indexes for table `donors`
--
ALTER TABLE `donors`
  ADD PRIMARY KEY (`donorid`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `fk_campaign_id_events` (`campaign_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `fk_notifications_donor` (`donor_id`),
  ADD KEY `fk_notifications_event` (`event_id`),
  ADD KEY `fk_notifications_donation` (`donation_id`);

--
-- Indexes for table `participation_requests`
--
ALTER TABLE `participation_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `donorid` (`donorid`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `replies`
--
ALTER TABLE `replies`
  ADD PRIMARY KEY (`reply_id`),
  ADD KEY `notification_id` (`notification_id`),
  ADD KEY `donor_id` (`donor_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `donorid` (`donorid`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `beneficiaries`
--
ALTER TABLE `beneficiaries`
  MODIFY `beneficiary_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `campaigns`
--
ALTER TABLE `campaigns`
  MODIFY `campaign_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `donation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `donors`
--
ALTER TABLE `donors`
  MODIFY `donorid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `participation_requests`
--
ALTER TABLE `participation_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `replies`
--
ALTER TABLE `replies`
  MODIFY `reply_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `donations`
--
ALTER TABLE `donations`
  ADD CONSTRAINT `donations_ibfk_1` FOREIGN KEY (`beneficiary_id`) REFERENCES `beneficiaries` (`beneficiary_id`),
  ADD CONSTRAINT `fk_donorid` FOREIGN KEY (`donorid`) REFERENCES `donors` (`donorid`),
  ADD CONSTRAINT `fk_event_id` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`);

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `fk_campaign_id_events` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`campaign_id`);

--
-- Constraints for table `replies`
--
ALTER TABLE `replies`
  ADD CONSTRAINT `replies_ibfk_1` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`notification_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `replies_ibfk_2` FOREIGN KEY (`donor_id`) REFERENCES `donors` (`donorid`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`donorid`) REFERENCES `donors` (`donorid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
