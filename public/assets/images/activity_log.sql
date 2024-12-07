-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 04, 2024 at 06:57 AM
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
-- Database: `pharmasync`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `action`, `user_id`, `description`, `timestamp`) VALUES
(1, 'edit', NULL, 'Updated user: Los Santos.', '2024-12-04 04:56:33'),
(4, 'edit', NULL, 'Updated user: Gabby Datu.', '2024-12-04 04:59:27'),
(10, 'edit', 3, 'Updated user: Marks Miranda.', '2024-12-04 05:16:35'),
(11, 'edit', 3, 'Updated user: Marks Santos.', '2024-12-04 05:16:45'),
(12, 'edit', 3, 'Updated user: Marks Santos.', '2024-12-04 05:16:55'),
(13, 'edit', 3, 'Updated user: Markss Santos.', '2024-12-04 05:17:08'),
(16, 'add', NULL, 'Added a new user: Ken Takakura.', '2024-12-04 05:40:10'),
(18, 'add', NULL, 'Added a new user:  .', '2024-12-04 05:46:59'),
(19, 'add', NULL, 'Added a new user: Jose Arnisto.', '2024-12-04 05:49:13'),
(20, 'add', NULL, 'Added a new user: Kevin Miranda.', '2024-12-04 05:49:48'),
(21, 'Edit', NULL, 'Updated user: Jose Arnisto.', '2024-12-04 05:51:34'),
(22, 'Delete', NULL, 'Deleted user: Gabby Datu.', '2024-12-04 05:54:27'),
(23, 'Add', NULL, 'Added a new user: Keihle Dianne Pascual.', '2024-12-04 05:55:11'),
(24, 'Edit', NULL, 'Updated user: Keihle Dianne Pascual.', '2024-12-04 05:55:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
