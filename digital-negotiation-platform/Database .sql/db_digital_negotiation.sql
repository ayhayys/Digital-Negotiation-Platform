-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 17, 2026 at 03:51 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_digital_negotiation`
--

-- --------------------------------------------------------

--
-- Table structure for table `contracts`
--

CREATE TABLE `contracts` (
  `contract_id` int NOT NULL,
  `title` varchar(150) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `status` enum('draft','in_review','signed','cancelled') NOT NULL DEFAULT 'draft',
  `initiator_id` int NOT NULL,
  `client_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `contracts`
--

INSERT INTO `contracts` (`contract_id`, `title`, `file_path`, `status`, `initiator_id`, `client_id`, `created_at`) VALUES
(4, 'Kontrak Kerja Sama', '/storage/docs/contract_6a329a9f646b8.pdf', 'in_review', 3, NULL, '2026-06-17 13:01:19'),
(7, 'Kontrak Kerja Sama', '/storage/docs/contract_6a32a7c188121.docx', 'signed', 1, 2, '2026-06-17 13:57:21'),
(8, 'Kontrak Penjalin Silaturahmi', '/storage/docs/contract_6a32b1ad6a666.pdf', 'signed', 1, 2, '2026-06-17 14:39:41');

-- --------------------------------------------------------

--
-- Table structure for table `culture_simulations`
--

CREATE TABLE `culture_simulations` (
  `simulation_id` int NOT NULL,
  `user_id` int NOT NULL,
  `culture_type` varchar(100) NOT NULL,
  `performance_score` int NOT NULL,
  `feedback_text` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `culture_simulations`
--

INSERT INTO `culture_simulations` (`simulation_id`, `user_id`, `culture_type`, `performance_score`, `feedback_text`, `created_at`) VALUES
(1, 1, 'Jepang', 100, 'Luar biasa! Pemahaman budaya sangat baik.', '2026-06-16 18:05:33'),
(2, 1, 'Jepang', 20, 'Perlu mempelajari kembali karakteristik budaya ini.', '2026-06-16 18:05:39');

-- --------------------------------------------------------

--
-- Table structure for table `non_verbal_analytics`
--

CREATE TABLE `non_verbal_analytics` (
  `analytic_id` int NOT NULL,
  `contract_id` int NOT NULL,
  `user_id` int NOT NULL,
  `emotion_score` decimal(4,2) NOT NULL,
  `stress_level` decimal(4,2) NOT NULL,
  `cultural_alert` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `non_verbal_analytics`
--

INSERT INTO `non_verbal_analytics` (`analytic_id`, `contract_id`, `user_id`, `emotion_score`, `stress_level`, `cultural_alert`, `timestamp`) VALUES
(1, 7, 1, 3.62, 1.38, '', '2026-06-17 14:11:44'),
(2, 7, 2, 2.62, 3.51, 'Tingkat stres tinggi terdeteksi! Pertimbangkan untuk memberikan waktu istirahat.', '2026-06-17 14:13:36');

-- --------------------------------------------------------

--
-- Table structure for table `redlining_logs`
--

CREATE TABLE `redlining_logs` (
  `log_id` int NOT NULL,
  `contract_id` int NOT NULL,
  `user_id` int NOT NULL,
  `original_text` text NOT NULL,
  `revised_text` text NOT NULL,
  `translated_text` text,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `signatures`
--

CREATE TABLE `signatures` (
  `signature_id` int NOT NULL,
  `contract_id` int NOT NULL,
  `user_id` int NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `jurisdiction_code` varchar(50) NOT NULL,
  `certificate_hash` varchar(255) NOT NULL,
  `signed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `signatures`
--

INSERT INTO `signatures` (`signature_id`, `contract_id`, `user_id`, `otp_code`, `jurisdiction_code`, `certificate_hash`, `signed_at`) VALUES
(2, 7, 2, '135630', 'ID-IDN', 'c5d4ef283737e7ae1741bebc97bb26d910b271888325818245401fb00434195f', '2026-06-17 14:25:08'),
(3, 7, 1, '345726', 'ID-IDN', 'bef8b552399243dde8d4c7c16ec62c14b8fd2c8fefd7c5142c03ef00d6db711f', '2026-06-17 14:25:32'),
(4, 8, 1, '578243', 'ID-IDN', 'a4b4a5a486df71fd8f0e576449a4c27cbe9ecd45853285ff6ce23d113d9bbf1a', '2026-06-17 15:35:20'),
(5, 8, 2, '227545', 'ID-IDN', '9476ef757d124bdcee2049578faa3c464bc52b55cec90d7676779a28dafe261f', '2026-06-17 15:35:43');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('internal','client','admin') NOT NULL DEFAULT 'client',
  `fullname` varchar(150) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `email`, `role`, `fullname`, `created_at`) VALUES
(1, 'tim_legal_1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@domain.com', 'internal', 'Tim Legal Utama', '2026-06-16 13:52:44'),
(2, 'klien_alpha', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'klien@alpha.com', 'client', 'Perusahaan Alpha', '2026-06-16 13:52:44'),
(3, 'yahya', '$2y$10$wLACKfVkYcQ4aRze3aXM2un0oNscVC9AIE1qlxySfPh6zXSSmPTxe', 'yahya@ini.com', 'client', 'Yahya', '2026-06-16 17:49:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contracts`
--
ALTER TABLE `contracts`
  ADD PRIMARY KEY (`contract_id`),
  ADD KEY `initiator_id` (`initiator_id`),
  ADD KEY `fk_client` (`client_id`);

--
-- Indexes for table `culture_simulations`
--
ALTER TABLE `culture_simulations`
  ADD PRIMARY KEY (`simulation_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `non_verbal_analytics`
--
ALTER TABLE `non_verbal_analytics`
  ADD PRIMARY KEY (`analytic_id`),
  ADD KEY `contract_id` (`contract_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `redlining_logs`
--
ALTER TABLE `redlining_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `contract_id` (`contract_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `signatures`
--
ALTER TABLE `signatures`
  ADD PRIMARY KEY (`signature_id`),
  ADD KEY `contract_id` (`contract_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `contract_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `culture_simulations`
--
ALTER TABLE `culture_simulations`
  MODIFY `simulation_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `non_verbal_analytics`
--
ALTER TABLE `non_verbal_analytics`
  MODIFY `analytic_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `redlining_logs`
--
ALTER TABLE `redlining_logs`
  MODIFY `log_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `signatures`
--
ALTER TABLE `signatures`
  MODIFY `signature_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `contracts`
--
ALTER TABLE `contracts`
  ADD CONSTRAINT `contracts_ibfk_1` FOREIGN KEY (`initiator_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_client` FOREIGN KEY (`client_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `culture_simulations`
--
ALTER TABLE `culture_simulations`
  ADD CONSTRAINT `culture_simulations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `non_verbal_analytics`
--
ALTER TABLE `non_verbal_analytics`
  ADD CONSTRAINT `non_verbal_analytics_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`contract_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `non_verbal_analytics_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `redlining_logs`
--
ALTER TABLE `redlining_logs`
  ADD CONSTRAINT `redlining_logs_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`contract_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `redlining_logs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `signatures`
--
ALTER TABLE `signatures`
  ADD CONSTRAINT `signatures_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`contract_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `signatures_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
