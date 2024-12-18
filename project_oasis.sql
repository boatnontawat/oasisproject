-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 18, 2024 at 06:26 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project_oasis`
--

-- --------------------------------------------------------

--
-- Table structure for table `creation_logs`
--

CREATE TABLE `creation_logs` (
  `id` int(11) NOT NULL,
  `employee_name` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `creation_logs`
--

INSERT INTO `creation_logs` (`id`, `employee_name`, `action`, `item_name`, `created_at`) VALUES
(1, 'เพชรมณี', 'สร้าง item', 'มีด', '2024-12-17 13:16:00'),
(2, 'เพชรมณี', 'สร้าง item', 'มีด', '2024-12-17 13:20:08');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_price` decimal(10,2) NOT NULL,
  `item_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `item_name`, `item_price`, `item_image`, `created_at`, `created_by`) VALUES
(7, 'มีด', 5.00, 'item/053.JPG', '2024-12-17 14:05:04', 'เพชรมณี'),
(8, 'มีดหมอ', 5.00, 'item/055.JPG', '2024-12-17 17:20:59', 'เพชรมณี');

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` int(11) NOT NULL,
  `employee_name` varchar(255) NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`id`, `employee_name`, `login_time`) VALUES
(1, 'เพชรมณี', '2024-12-17 07:11:51'),
(2, 'เพชรมณี', '2024-12-17 07:19:18'),
(3, 'เพชรมณี', '2024-12-17 07:21:02'),
(4, 'เพชรมณี', '2024-12-17 07:23:34'),
(5, 'เพชรมณี', '2024-12-17 07:25:27'),
(6, 'เพชรมณี', '2024-12-17 07:28:10'),
(7, 'เพชรมณี', '2024-12-17 07:28:46'),
(8, 'เพชรมณี', '2024-12-17 07:30:25'),
(9, 'เพชรมณี', '2024-12-17 07:30:37'),
(10, 'เพชรมณี', '2024-12-17 07:31:53'),
(11, 'เพชรมณี', '2024-12-17 07:34:39'),
(12, 'เพชรมณี', '2024-12-17 13:03:16');

-- --------------------------------------------------------

--
-- Table structure for table `sets`
--

CREATE TABLE `sets` (
  `set_id` int(11) NOT NULL,
  `set_name` varchar(255) NOT NULL,
  `market_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `set_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_percentage` decimal(5,2) DEFAULT 0.00,
  `set_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sets`
--

INSERT INTO `sets` (`set_id`, `set_name`, `market_price`, `set_price`, `discount_percentage`, `set_image`, `created_at`, `created_by`) VALUES
(13, '12121', 0.00, 0.00, 0.00, 'set/142.JPG', '2024-12-17 17:25:36', ''),
(14, '12121', 1000.00, 0.00, 0.00, 'set/209.JPG', '2024-12-17 17:27:46', ''),
(15, '12121', 1000.00, 1000.00, 0.00, 'set/030.JPG', '2024-12-17 17:45:43', '');

-- --------------------------------------------------------

--
-- Table structure for table `set_items`
--

CREATE TABLE `set_items` (
  `id` int(11) NOT NULL,
  `set_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `set_items`
--

INSERT INTO `set_items` (`id`, `set_id`, `item_id`, `quantity`, `created_at`) VALUES
(6, 15, 7, 1, '2024-12-17 17:45:46'),
(7, 15, 8, 1, '2024-12-17 17:45:49'),
(8, 15, 7, 1, '2024-12-17 17:45:51');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `hospital_name` varchar(255) NOT NULL,
  `department` varchar(255) DEFAULT NULL,
  `employee_name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `hospital_name`, `department`, `employee_name`, `email`, `phone_number`, `created_at`) VALUES
(2, 'กรุงเทพภูเก็ต', 'med', 'เพชรมณี', 'boatzill12@gmail.com', '0652358858', '2024-12-17 07:18:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `creation_logs`
--
ALTER TABLE `creation_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sets`
--
ALTER TABLE `sets`
  ADD PRIMARY KEY (`set_id`);

--
-- Indexes for table `set_items`
--
ALTER TABLE `set_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `set_id` (`set_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_name` (`employee_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `creation_logs`
--
ALTER TABLE `creation_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `sets`
--
ALTER TABLE `sets`
  MODIFY `set_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `set_items`
--
ALTER TABLE `set_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `set_items`
--
ALTER TABLE `set_items`
  ADD CONSTRAINT `set_items_ibfk_1` FOREIGN KEY (`set_id`) REFERENCES `sets` (`set_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `set_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
