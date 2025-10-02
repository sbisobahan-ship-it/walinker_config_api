-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 02, 2025 at 04:19 AM
-- Server version: 11.8.2-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aycalyfo_walinker`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_controlar`
--

CREATE TABLE `admin_controlar` (
  `admin_controlar_id` int(11) NOT NULL,
  `help` varchar(255) NOT NULL,
  `service` varchar(255) NOT NULL,
  `policy` varchar(255) NOT NULL,
  `updating` varchar(255) NOT NULL,
  `server_activity` int(1) NOT NULL DEFAULT 0,
  `home_notification` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_controlar`
--

INSERT INTO `admin_controlar` (`admin_controlar_id`, `help`, `service`, `policy`, `updating`, `server_activity`, `home_notification`) VALUES
(1, 'Your data ', 'Your data ', 'Your data ', 'Your data ', 0, 'Your data');

-- --------------------------------------------------------

--
-- Table structure for table `admin_tokens`
--

CREATE TABLE `admin_tokens` (
  `id` int(11) NOT NULL,
  `fcm_token` varchar(255) NOT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_tokens`
--

INSERT INTO `admin_tokens` (`id`, `fcm_token`, `updated_at`) VALUES
(142, 'f3rE1PwkTy6Wkhxa84IhVD:APA91bHMaUxQOoQli96QAgRfCdhGZtNrWL5gpqT0hki3LNEMlIpib9l_9ea2Xj4Z7DPRSoPKSDYsHbhSYl7EC1RZMmaXJeaZZpvZx-uxhRO2OPUcTl6y0bA', '2025-10-02 09:11:40');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `category_img` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `category_img`) VALUES
(3, 'Electronics', 'https://example.com/images/electronics.png'),
(11, 'Game', ''),
(15, 'tyu', ''),
(19, 'Electronics', ''),
(20, 'Electronics', 'https://example.com/images/electronics.png');

-- --------------------------------------------------------

--
-- Table structure for table `click_log`
--

CREATE TABLE `click_log` (
  `click_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `click_log`
--

INSERT INTO `click_log` (`click_id`, `app_id`, `group_id`) VALUES
(46, 85, 151),
(47, 85, 146);

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

CREATE TABLE `country` (
  `country_id` int(11) NOT NULL,
  `country_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`country_id`, `country_name`) VALUES
(2, 'Bangladesh+ Pakistan'),
(13, 'Pakisan'),
(17, 'Queat'),
(21, 'Vutan'),
(26, 'gcc'),
(27, 'Bangladesh'),
(28, 'China'),
(29, 'সোবাহান ali');

-- --------------------------------------------------------

--
-- Table structure for table `dummy_data`
--

CREATE TABLE `dummy_data` (
  `id` int(11) NOT NULL,
  `data` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dummy_data`
--

INSERT INTO `dummy_data` (`id`, `data`) VALUES
(1, '1');

-- --------------------------------------------------------

--
-- Table structure for table `group`
--

CREATE TABLE `group` (
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `categories` int(11) NOT NULL,
  `group_link` varchar(255) NOT NULL,
  `views` int(11) NOT NULL DEFAULT 0,
  `clicks` int(11) NOT NULL DEFAULT 0,
  `reports` int(11) NOT NULL DEFAULT 0,
  `post_at` datetime NOT NULL DEFAULT current_timestamp(),
  `country` int(11) NOT NULL,
  `post_panding` tinyint(1) NOT NULL DEFAULT 1,
  `delete_group` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group`
--

INSERT INTO `group` (`group_id`, `user_id`, `categories`, `group_link`, `views`, `clicks`, `reports`, `post_at`, `country`, `post_panding`, `delete_group`) VALUES
(144, 71, 3, 'https://chat.whatsapp.com/LshN9AChIEX1L9nHaRoccd', 0, 0, 0, '2025-09-30 10:01:29', 2, 0, 0),
(146, 71, 3, 'https://www.whatsapp.com/channel/0029VbAriOlL2ATq6QSH7N26', 0, 0, 0, '2025-09-30 10:04:32', 2, 0, 0),
(147, 71, 3, 'https://www.whatsapp.com/channel/0029Vb71pGO65yD5ZOgMq730', 0, 0, 0, '2025-09-30 10:06:41', 2, 0, 0),
(148, 71, 3, 'https://chat.whatsapp.com/DAzN51Egzm3FffbC2xNfud', 0, 0, 0, '2025-09-30 10:09:50', 2, 0, 0),
(149, 71, 3, 'https://chat.whatsapp.com/IP3OrvxHdvWCjKOwXdDWZE', 0, 0, 0, '2025-09-30 10:14:56', 2, 0, 0),
(150, 71, 3, 'https://chat.whatsapp.com/JR8ijplvkoDKSd7XDWblC3', 0, 0, 0, '2025-09-30 10:19:28', 2, 0, 0),
(151, 71, 3, 'https://chat.whatsapp.com/K6CAuPok7779bbsVPuy6J8', 0, 0, 0, '2025-09-30 10:21:51', 2, 0, 0),
(152, 71, 3, 'https://chat.whatsapp.com/DqlBxYFRPU376isW47mDLF', 0, 0, 0, '2025-09-30 10:23:49', 2, 0, 0),
(153, 71, 3, 'https://chat.whatsapp.com/B4mjoqQzgQgHn103kgKN6F', 0, 0, 0, '2025-09-30 10:26:18', 2, 0, 0),
(154, 70, 3, 'https://chat.whatsapp.com/EmEeujmHcmMEawYXlapsHp', 0, 0, 0, '2025-09-30 10:27:23', 2, 0, 0),
(155, 71, 3, 'https://chat.whatsapp.com/KIgYV2iHdRtDrCqxRH5XpK', 0, 0, 0, '2025-09-30 10:28:34', 2, 0, 0),
(160, 76, 3, 'https://www.whatsapp.com/channel/0029Vb6eKItJP20woEAFSQ3A', 0, 0, 0, '2025-10-02 09:26:29', 2, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `group_info`
--

CREATE TABLE `group_info` (
  `group_info_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `group_name` varchar(45) NOT NULL,
  `image_link` varchar(2000) NOT NULL,
  `status` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group_info`
--

INSERT INTO `group_info` (`group_info_id`, `group_id`, `group_name`, `image_link`, `status`) VALUES
(54, 144, 'Unique world', 'https://pps.whatsapp.net/v/t61.24694-24/491878968_1401053457888430_4495650355980323557_n.jpg?ccb=11-4&oh=01_Q5Aa2gHIeccMUpMSqMtS6ZaBiYJT0NVyV8OJ0ry7WSxE_woenw&oe=68E85B5B&_nc_sid=5e03e0&_nc_cat=107', 'active'),
(55, 146, 'Woman Healthcare🤱', 'https://mmg.whatsapp.net/m1/v/t24/An8YLgEVfzp1fYGUI9nx_9i3Ftw_LkwkWGXTz-us7F1MYoXJMV63B58fdh9XLaUyDIUsnGlkiCHmYDaXgbDaptIaVMIJNfXG_eoiWuqHHAC22UyDpOash_P8ah6H5KZ2twDn_-lkdiqEBmUqeJob?_nc_gid=v_9SLSFyjTdUBU0CeWF7QQ&_nc_oc=Adl8GWOTwRqgjFaN_qrs2xlFaSiP6uTtO_qGcsG8SQh_8V2pvOVp0d_hqJeU33wQoI4&ccb=10-5&oh=01_Q5Aa2gHymNzcUI2WiVSHRShGQzaJ4yNV9E96CBh3ug5cSE7taw&oe=6902E385&_nc_sid=471a72', 'active'),
(56, 147, 'Test Group', 'http://example.com/img.jpg', 'active'),
(57, 148, 'Supreme online business only for India', 'https://pps.whatsapp.net/v/t61.24694-24/418541400_1530210117861557_2654658350675362135_n.jpg?ccb=11-4&oh=01_Q5Aa2gEU5gdC7vDGgLl3A28TH1CMJZmzys-FQv8faSu2gwlPMw&oe=68E86C7B&_nc_sid=5e03e0&_nc_cat=111', 'active'),
(58, 149, 'LOAN SCHEMES', 'https://pps.whatsapp.net/v/t61.24694-24/491870418_1220476816282939_7731585054847247475_n.jpg?ccb=11-4&oh=01_Q5Aa2gHmEYDSYMkceEbm0KRxL-zsqHMjMlsZSlJCBxtchI0Uxw&oe=68E88AE9&_nc_sid=5e03e0&_nc_cat=103', 'active'),
(59, 150, 'Job News1️⃣തൊഴിലവസരങ്ങൾ 👔💼', 'https://pps.whatsapp.net/v/t61.24694-24/222025286_170963171643227_2054678345738892545_n.jpg?ccb=11-4&oh=01_Q5Aa2gGlS3r0-1M9eUy0MloU7VccZgA76YtGVjqgRrcqlbg75w&oe=68E86A64&_nc_sid=5e03e0&_nc_cat=107', 'active'),
(60, 151, 'INTERDAY TRADING(stock market)', 'https://pps.whatsapp.net/v/t61.24694-24/534425750_1721723315885111_593207891281477350_n.jpg?ccb=11-4&oh=01_Q5Aa2gH-sPMx4atT2Usmue62lDctqWn4w0eJ8iW8edTipEQlCA&oe=68E8768A&_nc_sid=5e03e0&_nc_cat=100', 'active'),
(61, 152, '𝙏𝙃𝙀 𝙎𝙏𝙊𝘾𝙆𝙎 𝘽𝙐𝙇𝙇 ⭐⭐', 'https://pps.whatsapp.net/v/t61.24694-24/142956962_2901842463411883_2175370415226684051_n.jpg?ccb=11-4&oh=01_Q5Aa2gFb9Gu2WljRMld1Kg_JcVa8X0vfdMI4Mp1mM6XXl__UiA&oe=68E86BD9&_nc_sid=5e03e0&_nc_cat=102', 'active'),
(62, 153, 'STOCK MARKET SCHOOL 1 💸💸', 'https://pps.whatsapp.net/v/t61.24694-24/352815419_993764148310737_8512766574217033668_n.jpg?ccb=11-4&oh=01_Q5Aa2gHeRqBYU_F9ZcItN8JohdQeIq7PS_cusymc8M7srb8s6Q&oe=68E88386&_nc_sid=5e03e0&_nc_cat=106', 'active'),
(63, 154, 'Stock market Information', 'https://pps.whatsapp.net/v/t61.24694-24/256163356_997145577824441_590607775882255733_n.jpg?ccb=11-4&oh=01_Q5Aa2gEH7zaDuCql3MCuFFGjum55qzVnoVQ6Q4ggGk_w2zZhfQ&oe=68E86084&_nc_sid=5e03e0&_nc_cat=109', 'active'),
(64, 155, 'LIVE Stock Market News', 'https://pps.whatsapp.net/v/t61.24694-24/401581623_1063552981363722_4895555515824580378_n.jpg?ccb=11-4&oh=01_Q5Aa2gF7_5fjfpHdkQzbs1L2chbx520_aHL1mLBgCEhyqIkFLA&oe=68E874AA&_nc_sid=5e03e0&_nc_cat=104', 'active'),
(67, 160, 'LaiqaraCreation_Official47', 'https://mmg.whatsapp.net/m1/v/t24/An8r6S_ix21KhPp5wcxC3QEMg8vG7JSihQOGWrjUJpgXSqUN1l4_ze0lar58Icsxd0IFqLqq2P6g1Ss1EMGGDrxYDb1ALYxOs2OOg-zVrKulml_raJT8vXn0SbAFMBh5dR_5nXZL0wB95t0Bvtmi?_nc_gid=i6qFjIKfjyylaFEXRE7gCQ&_nc_oc=Adl26oH9GYbzXxf8r8AtxBA2rnY3zIWl82fbgmREEa3K6v1_5BiPktVGqMqHx-78Vkg&ccb=10-5&oh=01_Q5Aa2gGpPPw-FUrXdTAIFiLJwR_EH9ZZRvZKNJLQnsj-vm7aBw&oe=69057E80&_nc_sid=471a72', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `report_log`
--

CREATE TABLE `report_log` (
  `report_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `report_log`
--

INSERT INTO `report_log` (`report_id`, `app_id`, `group_id`) VALUES
(23, 66, 146),
(24, 71, 153),
(25, 71, 150),
(27, 71, 152),
(28, 72, 152),
(29, 73, 151),
(30, 73, 147),
(31, 73, 150),
(32, 74, 147),
(33, 75, 152),
(34, 75, 155),
(35, 75, 144),
(37, 75, 148),
(38, 75, 150),
(39, 75, 151),
(40, 76, 153),
(42, 76, 149),
(43, 76, 144),
(44, 76, 152);

-- --------------------------------------------------------

--
-- Table structure for table `send_sms`
--

CREATE TABLE `send_sms` (
  `sms_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sms` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `app_id` char(36) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) NOT NULL,
  `is_disable` tinyint(4) NOT NULL DEFAULT 0,
  `last_active` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `app_id`, `created_at`, `ip_address`, `is_disable`, `last_active`) VALUES
(66, 'eebbfcba-5605-4886-a656-0dda8cf857b9', '2025-09-28 09:32:56', '59.152.3.131', 0, '2025-10-02 09:32:56'),
(67, 'bfc0b139-1de2-458a-ac60-13a90870de16', '2025-09-28 09:34:02', '59.152.3.131', 0, '2025-10-02 09:34:02'),
(68, '4d7b5fd0-dee3-46f1-bc2d-98ecfbe76005', '2025-09-28 09:36:45', '59.152.3.131', 0, '2025-10-02 09:36:45'),
(69, 'e1854a24-419f-4ec1-b6c6-7ada0922868f', '2025-09-28 09:38:38', '59.152.3.131', 0, '2025-10-02 09:38:38'),
(70, '62ff3be7-fccd-40de-b4b4-322810cde228', '2025-09-28 12:53:03', '59.152.3.131', 0, '2025-10-02 12:53:03'),
(71, '1bd3fa54-8d9a-4081-9652-9338196f068d', '2025-09-30 03:39:21', '59.152.6.100', 0, '2025-10-02 03:39:21'),
(72, 'db456f56-4427-4314-bcd9-9a5968e19f96', '2025-09-30 06:04:03', '59.152.6.100', 0, '2025-10-02 06:04:03'),
(73, '9de84d77-bb5b-4a9f-8981-5b323dd4dfab', '2025-09-30 06:06:06', '59.152.6.100', 0, '2025-10-02 06:06:06'),
(74, 'e6b6c7ae-8085-4a3f-b471-b20f038bebcb', '2025-09-30 06:07:17', '59.152.6.100', 0, '2025-10-02 06:07:17'),
(75, '93f9395d-594c-40f9-8594-ea17090a7c6c', '2025-09-30 06:10:42', '59.152.6.100', 0, '2025-10-02 06:10:42'),
(76, '8adb619a-5057-4e4c-989c-04f8777afc0b', '2025-10-01 05:57:15', '116.58.200.129', 0, '2025-10-02 13:26:48'),
(77, '72a149d3-15a8-4c8a-99e2-f4e02e8a9ef4', '2025-10-01 13:29:26', '116.58.200.129', 0, '2025-10-02 13:29:26'),
(78, '202c2cbd-c58b-4d8b-a726-bd32da81e8dc', '2025-10-01 13:32:55', '116.58.200.129', 0, '2025-10-02 13:32:55'),
(79, '9dfaa442-2543-47de-8887-b555349ad5c0', '2025-10-01 13:37:15', '116.58.200.129', 0, '2025-10-02 13:37:15'),
(80, '813112f7-ffd9-4105-b855-0bda7915cde7', '2025-10-01 13:39:59', '116.58.200.129', 0, '2025-10-02 13:39:59'),
(82, '183e4567-e89b-12d3-a456-426614174008', '2025-10-02 04:00:26', '54.86.50.139', 0, '2025-10-02 04:00:26'),
(85, '590506e0-5d09-4d65-8091-f64a2e95da27', '2025-10-02 04:57:14', '116.58.205.126', 0, '2025-10-02 04:57:14'),
(86, '1bd3fa54-8d9a-4081-9652-9338196f068t', '2025-10-02 04:59:48', '::1', 0, '2025-10-02 04:59:48'),
(87, '123e4567-e89b-12d3-a456-426614177002', '2025-10-02 05:34:03', '::1', 0, '2025-10-02 05:34:03'),
(88, '123e4567-e89b-12d3-a456-426674174002', '2025-10-02 05:39:53', '::1', 0, '2025-10-02 05:39:53'),
(89, '123e4567-e89b-12d3-a456-426614174000', '2025-10-02 05:40:48', '', 0, '2025-10-02 05:40:48'),
(90, '123e4567-e89b-12d3-a456-426614174007', '2025-10-02 05:43:01', '::1', 0, '2025-10-02 05:43:01'),
(91, '123e4567-e89b-12d3-a456-426614174009', '2025-10-02 05:59:27', '', 0, '2025-10-02 05:59:27');

-- --------------------------------------------------------

--
-- Table structure for table `view_log`
--

CREATE TABLE `view_log` (
  `view_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `view_log`
--

INSERT INTO `view_log` (`view_id`, `app_id`, `group_id`) VALUES
(27, 76, 155),
(29, 76, 153),
(30, 76, 151),
(31, 76, 152),
(32, 76, 154),
(33, 76, 150),
(34, 76, 149),
(35, 76, 148),
(36, 76, 147),
(37, 76, 146),
(38, 76, 144),
(39, 78, 151),
(41, 78, 153),
(42, 78, 152),
(43, 78, 154),
(44, 78, 155),
(45, 78, 150),
(46, 78, 149),
(47, 78, 148),
(48, 78, 147),
(49, 78, 146),
(50, 78, 144),
(51, 79, 152),
(52, 79, 153),
(53, 79, 154),
(54, 79, 151),
(56, 79, 155),
(57, 79, 150),
(58, 79, 149),
(59, 79, 148),
(60, 79, 147),
(61, 79, 146),
(62, 79, 144),
(63, 80, 153),
(65, 80, 154),
(66, 80, 152),
(67, 80, 155),
(68, 80, 151),
(69, 80, 150),
(70, 80, 149),
(71, 80, 148),
(72, 80, 147),
(73, 80, 146),
(74, 80, 144),
(298, 85, 160),
(299, 85, 155),
(300, 85, 153),
(301, 85, 154),
(302, 85, 152),
(303, 85, 151),
(304, 85, 150),
(305, 85, 149),
(306, 85, 148),
(307, 85, 147),
(308, 85, 144),
(309, 85, 146);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_controlar`
--
ALTER TABLE `admin_controlar`
  ADD PRIMARY KEY (`admin_controlar_id`);

--
-- Indexes for table `admin_tokens`
--
ALTER TABLE `admin_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fcm_token` (`fcm_token`),
  ADD UNIQUE KEY `fcm_token_2` (`fcm_token`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `click_log`
--
ALTER TABLE `click_log`
  ADD PRIMARY KEY (`click_id`),
  ADD KEY `connect_app_id` (`app_id`),
  ADD KEY `connect_group_id` (`group_id`);

--
-- Indexes for table `country`
--
ALTER TABLE `country`
  ADD PRIMARY KEY (`country_id`);

--
-- Indexes for table `dummy_data`
--
ALTER TABLE `dummy_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `group`
--
ALTER TABLE `group`
  ADD PRIMARY KEY (`group_id`),
  ADD KEY `group_ibfk_1` (`user_id`),
  ADD KEY `group_ibfk_2` (`categories`),
  ADD KEY `group_ibfk_3` (`country`);

--
-- Indexes for table `group_info`
--
ALTER TABLE `group_info`
  ADD PRIMARY KEY (`group_info_id`),
  ADD KEY `connected_group` (`group_id`);

--
-- Indexes for table `report_log`
--
ALTER TABLE `report_log`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `fk_reportlog_appid` (`app_id`),
  ADD KEY `fk_reportlog_groupid` (`group_id`);

--
-- Indexes for table `send_sms`
--
ALTER TABLE `send_sms`
  ADD PRIMARY KEY (`sms_id`),
  ADD KEY `connect_user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `view_log`
--
ALTER TABLE `view_log`
  ADD PRIMARY KEY (`view_id`),
  ADD KEY `connected_app_id` (`app_id`),
  ADD KEY `connected_group_id` (`group_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_controlar`
--
ALTER TABLE `admin_controlar`
  MODIFY `admin_controlar_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `admin_tokens`
--
ALTER TABLE `admin_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=213;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `click_log`
--
ALTER TABLE `click_log`
  MODIFY `click_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `country`
--
ALTER TABLE `country`
  MODIFY `country_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `dummy_data`
--
ALTER TABLE `dummy_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `group`
--
ALTER TABLE `group`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;

--
-- AUTO_INCREMENT for table `group_info`
--
ALTER TABLE `group_info`
  MODIFY `group_info_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `report_log`
--
ALTER TABLE `report_log`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `send_sms`
--
ALTER TABLE `send_sms`
  MODIFY `sms_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `view_log`
--
ALTER TABLE `view_log`
  MODIFY `view_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=310;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `click_log`
--
ALTER TABLE `click_log`
  ADD CONSTRAINT `connect_app_id` FOREIGN KEY (`app_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `connect_group_id` FOREIGN KEY (`group_id`) REFERENCES `group` (`group_id`) ON DELETE CASCADE;

--
-- Constraints for table `group`
--
ALTER TABLE `group`
  ADD CONSTRAINT `group_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_ibfk_2` FOREIGN KEY (`categories`) REFERENCES `categories` (`category_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `group_ibfk_3` FOREIGN KEY (`country`) REFERENCES `country` (`country_id`) ON UPDATE CASCADE;

--
-- Constraints for table `group_info`
--
ALTER TABLE `group_info`
  ADD CONSTRAINT `connected_group` FOREIGN KEY (`group_id`) REFERENCES `group` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `report_log`
--
ALTER TABLE `report_log`
  ADD CONSTRAINT `fk_reportlog_appid` FOREIGN KEY (`app_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reportlog_groupid` FOREIGN KEY (`group_id`) REFERENCES `group` (`group_id`) ON DELETE CASCADE;

--
-- Constraints for table `send_sms`
--
ALTER TABLE `send_sms`
  ADD CONSTRAINT `connect_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `view_log`
--
ALTER TABLE `view_log`
  ADD CONSTRAINT `connected_app_id` FOREIGN KEY (`app_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `connected_group_id` FOREIGN KEY (`group_id`) REFERENCES `group` (`group_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
