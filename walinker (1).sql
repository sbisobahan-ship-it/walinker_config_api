-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 17, 2025 at 07:08 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `walinker`
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
  `server_activity` char(1) NOT NULL DEFAULT '0',
  `home_notification` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_controlar`
--

INSERT INTO `admin_controlar` (`admin_controlar_id`, `help`, `service`, `policy`, `updating`, `server_activity`, `home_notification`) VALUES
(1, 'Your data ', 'Your data ', 'Your data ', 'Your data ', '0', 'Your data');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(3, 'Electronics & Gadgets'),
(4, 'Game'),
(5, 'Income'),
(6, 'Love'),
(8, 'Electronics');

-- --------------------------------------------------------

--
-- Table structure for table `click_log`
--

CREATE TABLE `click_log` (
  `click_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(2, 'Bangladesh'),
(3, 'Pakishan'),
(4, 'India'),
(6, 'japan'),
(7, 'Bangladesh'),
(8, 'Bangladesh');

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
(48, 21, 5, 'dfsdf', 0, 0, 0, '2025-09-13 22:04:15', 7, 1, 0),
(49, 22, 5, 'dsffdf', 0, 0, 0, '2025-09-13 22:04:57', 2, 1, 0),
(50, 23, 5, 'hfggffgfgdf', 0, 0, 0, '2025-09-13 22:06:21', 7, 1, 0),
(51, 21, 3, 'https://chat.whatsapp.com/AbCdEfGh13', 0, 0, 0, '2025-09-15 19:13:22', 2, 1, 0),
(52, 22, 3, 'https://chat.whatsapp.com/LAXugp2Un3B4s22ZhYLQk', 0, 0, 0, '2025-09-15 22:49:41', 2, 1, 0),
(53, 24, 3, 'https://chat.whatsapp.com/LAXugp2Un3B4s22ZhYLQkC', 0, 0, 0, '2025-09-15 22:50:18', 2, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `group_info`
--

CREATE TABLE `group_info` (
  `group_info_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `group_name` varchar(45) NOT NULL,
  `image_link` varchar(255) NOT NULL,
  `status` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report_log`
--

CREATE TABLE `report_log` (
  `report_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `send_sms`
--

CREATE TABLE `send_sms` (
  `sms_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sms` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
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
(20, '123e4567-e89b-12d3-a456-426614174005', '2025-09-16 05:47:32', '', 0, '2025-09-14 04:18:18'),
(21, '123e4567-e89b-12d3-a456-426614174002', '2025-09-14 05:05:45', '', 0, '2025-09-14 04:18:24'),
(22, '123e4567-e89b-12d3-a456-426614174008', '2025-09-14 04:20:04', '::1', 0, '2025-09-16 06:21:38'),
(23, '123e4567-e89b-12d3-a456-426614174007', '2025-09-14 05:06:04', '', 0, '2025-09-14 05:06:04'),
(24, '123e4567-e89b-12d3-a456-426614174009', '2025-09-16 05:51:01', '::1', 0, '2025-09-16 06:07:42');

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
(12, 24, 53),
(13, 22, 52),
(14, 22, 48);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_controlar`
--
ALTER TABLE `admin_controlar`
  ADD PRIMARY KEY (`admin_controlar_id`);

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
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `click_log`
--
ALTER TABLE `click_log`
  MODIFY `click_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `country`
--
ALTER TABLE `country`
  MODIFY `country_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `dummy_data`
--
ALTER TABLE `dummy_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `group`
--
ALTER TABLE `group`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `group_info`
--
ALTER TABLE `group_info`
  MODIFY `group_info_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `report_log`
--
ALTER TABLE `report_log`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `send_sms`
--
ALTER TABLE `send_sms`
  MODIFY `sms_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `view_log`
--
ALTER TABLE `view_log`
  MODIFY `view_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
