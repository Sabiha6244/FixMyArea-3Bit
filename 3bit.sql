-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 29, 2025 at 02:06 AM
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
-- Database: `3bit`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `active`) VALUES
(1, 'Road Repair', 'Potholes, road damage, sidewalk issues', 1),
(2, 'Sanitation', 'Garbage collection, public cleanliness', 1),
(3, 'Utilities', 'Water, electricity, gas issues', 1),
(4, 'Public Safety', 'Street lights, traffic signals, safety hazards', 1);

-- --------------------------------------------------------

--
-- Table structure for table `issues`
--

CREATE TABLE `issues` (
  `id` int(11) NOT NULL,
  `citizen_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category` text DEFAULT NULL,
  `status` enum('reported','in_progress','resolved','cancelled') NOT NULL DEFAULT 'reported',
  `location` point NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `hired_service_provider_id` int(11) DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `issues`
--

INSERT INTO `issues` (`id`, `citizen_id`, `title`, `description`, `category`, `status`, `location`, `created_at`, `hired_service_provider_id`, `photo_path`, `updated_at`, `category_id`) VALUES
(2, 3, 'Road Needs to Fix', 'The road of  Taltola Colony is broken..', 'Road Repair', '', 0x000000000101000000e10b93a9829b5640656f29e78bbd3740, '2025-04-07 17:17:21', NULL, 'uploads/issue_6809d4f714300.jpeg', '2025-04-24 06:06:47', NULL),
(4, 3, 'Collect the garbage', 'Area needs to be cleaned near Taltola', 'Public Safety', 'in_progress', 0x000000000101000000e10b93a9829b5640656f29e78bbd3740, '2025-04-14 15:43:04', 4, 'uploads/issue_680a2863addb1.jpeg', '2025-04-24 12:02:43', NULL),
(5, 25, 'Broken Street Light at Mirpur', 'Street light needs to be fixed near Mirpur Colony.Please fix the problem as far as possible.', 'Public Safety', 'in_progress', 0x000000000101000000edd632198e985640e2ae5e4546c73740, '2025-04-24 04:00:20', 4, 'uploads/issue_6809d213e7dc1.jpeg', '2025-04-24 05:54:27', NULL),
(6, 31, 'Clean the Lake', 'the Lake near Mohammadpur needs to be cleaned.', 'Road Repair', '', 0x000000000101000000edd632198e985640b03907cf84c63740, '2025-04-28 13:19:34', NULL, 'uploads/img_680f8066a45027.56795554.jpeg', '2025-04-28 13:19:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `issue_id` int(11) DEFAULT NULL,
  `service_request_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `issue_id`, `service_request_id`, `message`, `timestamp`, `is_read`) VALUES
(1, 1, 4, 4, NULL, 'hello', '2025-04-22 08:06:31', 0),
(2, 1, 4, 5, NULL, 'hi', '2025-04-24 10:01:19', 0),
(3, 1, 4, 5, NULL, 'hello', '2025-04-24 10:53:00', 0),
(4, 4, 1, 5, NULL, 'What do You want?', '2025-04-24 11:15:46', 0),
(5, 1, 4, 5, NULL, 'Can You fix this?', '2025-04-24 12:23:35', 0),
(6, 4, 1, 5, NULL, 'Okay..Lets Talk about the payment.How much will you pay to fix this?', '2025-04-24 12:28:52', 0),
(7, 1, 4, 4, NULL, 'Can You solve this issue?', '2025-04-26 08:05:10', 0);

-- --------------------------------------------------------

--
-- Table structure for table `service_providers`
--

CREATE TABLE `service_providers` (
  `user_id` int(11) NOT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `service_type` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `service_area` varchar(255) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT NULL,
  `verified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_providers`
--

INSERT INTO `service_providers` (`user_id`, `company_name`, `service_type`, `description`, `service_area`, `rating`, `verified`) VALUES
(4, 'Okimuro', 'Public Safety', 'We are The okimuro Corporation who works to ensure public safety.', 'Mirpur', NULL, 1),
(28, 'Paragon', 'Utilities', 'Paragon is a trusted service provider specializing in utilities such as electricity, water, and gas. With a focus on efficient solutions and timely support, Paragon ensures that customers\' utility issues are addressed swiftly and professionally. Their dedicated team works to deliver reliable services that meet the highest industry standards.', 'Agargaon', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `service_requests`
--

CREATE TABLE `service_requests` (
  `id` int(11) NOT NULL,
  `citizen_id` int(11) DEFAULT NULL,
  `provider_id` int(11) DEFAULT NULL,
  `issue_id` int(11) DEFAULT NULL,
  `status` enum('pending','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending',
  `payment_status` enum('pending','paid') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `admin_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_requests`
--

INSERT INTO `service_requests` (`id`, `citizen_id`, `provider_id`, `issue_id`, `status`, `payment_status`, `created_at`, `admin_id`) VALUES
(7, 25, 4, 5, 'in_progress', 'pending', '2025-04-24 06:08:31', 1),
(10, 3, 4, 4, 'in_progress', 'pending', '2025-04-26 01:41:18', 1),
(11, 3, 4, 4, 'in_progress', 'pending', '2025-04-26 02:04:04', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('citizen','service_provider','admin') NOT NULL DEFAULT 'citizen',
  `status` enum('active','inactive','banned') NOT NULL DEFAULT 'active',
  `profile_picture` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `otp_code` varchar(6) DEFAULT NULL,
  `otp_expires_at` datetime DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `division` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `city_corporation` varchar(100) DEFAULT NULL,
  `upazila` varchar(100) DEFAULT NULL,
  `postcode` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`, `status`, `profile_picture`, `created_at`, `updated_at`, `otp_code`, `otp_expires_at`, `is_verified`, `division`, `district`, `city_corporation`, `upazila`, `postcode`) VALUES
(1, 'Sabiha', 'sabiha.akter.6244@gmail.com', '01554869266', '3627909a29c31381a071ec27f7c9ca97726182aed29a7ddd2e54353322cfb30abb9e3a6df2ac2c20fe23436311d678564d0c8d305930575f60e2d3d048184d79', 'admin', 'active', 'uploads/1745477620_wp3853220.jpg', '2025-04-04 03:54:48', '2025-04-24 06:53:40', NULL, NULL, 0, 'Dhaka', 'Dhaka', 'Sher-e-Bangla Nagar', NULL, '1207'),
(3, 'Robin milford', 'robin@gmail.com', '01921292260', '3627909a29c31381a071ec27f7c9ca97726182aed29a7ddd2e54353322cfb30abb9e3a6df2ac2c20fe23436311d678564d0c8d305930575f60e2d3d048184d79', 'citizen', 'active', 'uploads/1745253732_Robin.jpg', '2025-04-04 03:56:56', '2025-04-21 23:54:28', NULL, NULL, 0, 'Dhaka', 'Dhaka', 'Mirpur', NULL, '1216'),
(4, 'Kishore', 'k@gmail.com', '01554869260', '22e7e9d85b7fe6004f7b9f3aa592ea9ec9ce098682e8192fa83785f1784c768d1d1ac3b8afcae88666f66aec24739ac133e9d4adc7506f1a5f1f6078cb27c674', 'service_provider', 'active', 'uploads/1745239885_ace.jpg', '2025-04-15 01:28:46', '2025-04-21 14:34:25', NULL, NULL, 0, 'Dhaka', 'Dhaka', 'Mirpur', NULL, '1216'),
(23, 'Sabiha', 'sabiha.akter.cse@ulab.edu.bd', '', 'd047696682e5dd12e447822c11f45a490da0c233b5b2d3630115c741c1735d56fdac69089b5a1ce6ebe2cf79fe9d84ccf6df54e5fe911bd204d1e223ea243a58', 'citizen', 'active', 'uploads/1745127994_8.png', '2025-04-19 02:14:01', '2025-04-20 05:46:34', NULL, NULL, 1, 'Dhaka', 'Dhaka', 'Agargaon', NULL, NULL),
(24, 'Jina', 'jina0657@gmail.com', '01921292244', 'a73ae84199edd6790cfc5497e7d8fe7b600c71542c6b9fc77e3f43834564905dea73a533858cd0ddad1702074f32f0d9a44545c28ac17b4138204a746df393e1', 'citizen', 'active', 'uploads/1745129387_Robin.jpg', '2025-04-20 06:05:54', '2025-04-20 06:09:47', NULL, NULL, 1, 'Dhaka', 'Dhaka', 'Agargaon', NULL, NULL),
(25, 'Prapti', 'marjanul.jannat.cse@ulab.edu.bd', '01554869233', '234911ed16092b578fa6db455ddefd9073d84e631659a9c9a9f00a03cdd3e0a11134cc13349b0c485c07664a3fa28b0f7abf7c140032d0ab488e6ad4f6fb0d97', 'citizen', 'active', 'uploads/1745467145_U.jpg', '2025-04-22 05:33:47', '2025-04-24 03:59:05', NULL, NULL, 1, 'Dhaka', 'Dhaka', 'Mirpur', NULL, '1216'),
(27, 'Moni', 'moitree.mazumder.cse@ulab.edu.bd', '01554869272', '6f017f6885ba78a82ba253b230205f069700f74d019222db0b04b1223b3a3cb77497152077ee3db0e49e992cf6980fa2d6771778b3a17310a978dfd22ebf5301', 'citizen', 'active', 'uploads/1745302093_5.png', '2025-04-22 06:06:56', '2025-04-22 06:08:13', NULL, NULL, 1, 'Dhaka', 'Dhaka', 'Mirpur', NULL, '1216'),
(28, 'Mahabub ', 'sj22-11s-652@sjs.edu.bd', '01554869221', 'a3fbeae90a9f3b5669feb4e971ea9b6dfdea00a97643b401bf7792767e00a917f02b7d0378d1e443b84f53adf52c8fe1cb8151b625242bb2a2a6c75ec4fb7b3a', 'service_provider', 'active', 'uploads/1745629031_Ronoroa.png', '2025-04-26 00:51:38', '2025-04-26 00:57:11', NULL, NULL, 1, 'Dhaka', 'Dhaka', 'Agargaon', NULL, '1207'),
(30, 'Sumaia', 'sumaia.akter.1173@gmail.com', '01554869263', 'a87a56c6f7eb5bf42d0165cdf2d9b86af57137861a380bb34aea110a81958280c6b8946e1b141273ec5cbe30df6dea5a28013a4a9604e330efca8cd14a0ba1c3', 'citizen', 'active', NULL, '2025-04-28 12:47:03', '2025-04-28 12:47:03', '152265', '2025-04-28 18:57:03', 0, NULL, NULL, NULL, NULL, NULL),
(31, 'Musa aman', 'rmahabub363@gmail.com', '01554869254', '1a75ad4c850f53f203e71a506208f0c2686d03e8a238e369475a56cc41373ddf4ba41649c4d1c7d9cddc72e0576b587633b1c60a67bdf8f8cbf31dae7413874b', 'citizen', 'active', 'uploads/1745845854_LAS.png', '2025-04-28 12:55:54', '2025-04-28 13:10:54', NULL, NULL, 1, 'Dhaka', 'Dhaka', 'Sher-e-Bangla Nagar', NULL, '1207');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `issues`
--
ALTER TABLE `issues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `citizen_id` (`citizen_id`),
  ADD KEY `hired_service_provider_id` (`hired_service_provider_id`),
  ADD KEY `fk_issue_category` (`category_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `issue_id` (`issue_id`),
  ADD KEY `service_request_id` (`service_request_id`);

--
-- Indexes for table `service_providers`
--
ALTER TABLE `service_providers`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `service_requests`
--
ALTER TABLE `service_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `citizen_id` (`citizen_id`),
  ADD KEY `provider_id` (`provider_id`),
  ADD KEY `issue_id` (`issue_id`),
  ADD KEY `idx_payment_status` (`payment_status`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD KEY `idx_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `issues`
--
ALTER TABLE `issues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `service_requests`
--
ALTER TABLE `service_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `issues`
--
ALTER TABLE `issues`
  ADD CONSTRAINT `fk_issue_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `issues_ibfk_1` FOREIGN KEY (`citizen_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `issues_ibfk_2` FOREIGN KEY (`hired_service_provider_id`) REFERENCES `service_providers` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `messages_ibfk_4` FOREIGN KEY (`service_request_id`) REFERENCES `service_requests` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `service_providers`
--
ALTER TABLE `service_providers`
  ADD CONSTRAINT `service_providers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_requests`
--
ALTER TABLE `service_requests`
  ADD CONSTRAINT `service_requests_ibfk_1` FOREIGN KEY (`citizen_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_requests_ibfk_2` FOREIGN KEY (`provider_id`) REFERENCES `service_providers` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_requests_ibfk_3` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
