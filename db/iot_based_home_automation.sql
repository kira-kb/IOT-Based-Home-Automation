-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 09, 2024 at 11:29 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `iot_based_home_automation`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(10) NOT NULL,
  `device` varchar(20) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `password` text NOT NULL,
  `addedBy` varchar(50) NOT NULL DEFAULT 'company',
  `status` varchar(9) NOT NULL DEFAULT 'active',
  `price` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `device`, `email`, `password`, `addedBy`, `status`, `price`) VALUES
(9, 'deviceOne', 'dagimbirhan@gmail.com', '900150983cd24fb0d6963f7d28e17f72', 'company', 'suspend', 5000),
(15, 'deviceTwo', 'this@gmail.com', 'dagi123', 'company', 'suspend', 5000),
(16, 'deviceThree', 'kirubelbewket@gmail.com', '900150983cd24fb0d6963f7d28e17f72', 'company', 'suspend', 15000),
(17, 'deviceFour', 'bewket@gmail.com', 'bew123', 'company', 'active', 15000),
(18, 'deviceFive', 'efi@gmail.com', '900150983cd24fb0d6963f7d28e17f72', 'company', 'active', 12000),
(19, 'deviceSix', 'dagi@gmail.com', '900150983cd24fb0d6963f7d28e17f72', 'company', 'suspend', 12000),
(28, 'deviceSeven', 'kira@gmail.com', '8711b9f7e65545d4c5c23cbe3a88133a', 'company', 'active', 20000),
(32, 'deviceEight', 'me@gmail.com', '900150983cd24fb0d6963f7d28e17f72', 'company', 'active', 16000);

-- --------------------------------------------------------

--
-- Table structure for table `device`
--

CREATE TABLE `device` (
  `id` int(10) NOT NULL,
  `deviceName` varchar(20) NOT NULL,
  `led1` int(1) NOT NULL,
  `led2` int(1) NOT NULL,
  `led3` int(1) NOT NULL,
  `led4` int(1) NOT NULL,
  `led5` int(1) NOT NULL,
  `led6` int(1) NOT NULL,
  `deviceStatus` varchar(9) NOT NULL DEFAULT 'inactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `device`
--

INSERT INTO `device` (`id`, `deviceName`, `led1`, `led2`, `led3`, `led4`, `led5`, `led6`, `deviceStatus`) VALUES
(1, 'deviceOne', 0, 0, 0, 0, 0, 0, 'suspend'),
(6, 'deviceTwo', 0, 0, 0, 0, 0, 0, 'suspend'),
(11, 'deviceThree', 0, 0, 0, 0, 0, 0, 'suspend'),
(12, 'deviceFour', 0, 0, 0, 0, 0, 0, 'active'),
(14, 'deviceFive', 1, 0, 1, 0, 1, 0, 'active'),
(17, 'deviceSix', 0, 0, 0, 0, 0, 0, 'suspend'),
(20, 'deviceSeven', 0, 0, 0, 0, 0, 0, 'active'),
(21, 'deviceEight', 0, 0, 0, 0, 0, 0, 'active'),
(22, 'deviceNine', 0, 0, 0, 0, 0, 0, 'inactive'),
(23, 'deviceTen', 0, 0, 0, 0, 0, 0, 'inactive'),
(26, 'deviceEleven', 0, 0, 0, 0, 0, 0, 'inactive'),
(28, 'deviceTwelve', 0, 0, 0, 0, 0, 0, 'inactive');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(10) NOT NULL,
  `firstName` text NOT NULL,
  `lastName` text NOT NULL,
  `email` varchar(50) NOT NULL,
  `gender` varchar(1) NOT NULL,
  `password` varchar(40) NOT NULL,
  `sallary` int(10) NOT NULL,
  `profile picture` varchar(2048) NOT NULL,
  `role` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `firstName`, `lastName`, `email`, `gender`, `password`, `sallary`, `profile picture`, `role`) VALUES
(22, 'kirubel', 'bewket', 'kirubelbewket@gmail.com', 'M', '900150983cd24fb0d6963f7d28e17f72', 6543, './img/default/male avatar.jpg', 'admin'),
(44, 'getnet', 'mersha', 'getnetmersha@gmail.com', 'M', '900150983cd24fb0d6963f7d28e17f72', 4567, './img/default/male avatar.jpg', 'manufacturrer'),
(48, 'getnet', 'mersha', 'kira@gmail.com', 'M', '900150983cd24fb0d6963f7d28e17f72', 1252, './img/default/male avatar.jpg', 'seller'),
(49, 'kira', 'kira', 'kira@me.me', 'M', 'd0970714757783e6cf17b26fb8e2298f', 5432, './img/default/male avatar.jpg', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `feedbacks`
--

CREATE TABLE `feedbacks` (
  `id` int(10) NOT NULL,
  `email` varchar(30) NOT NULL,
  `subject` varchar(20) NOT NULL,
  `message` varchar(300) NOT NULL,
  `favorite` varchar(6) NOT NULL DEFAULT 'false',
  `testimonial` varchar(6) NOT NULL,
  `status` varchar(9) NOT NULL DEFAULT 'unreaded',
  `date` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedbacks`
--

INSERT INTO `feedbacks` (`id`, `email`, `subject`, `message`, `favorite`, `testimonial`, `status`, `date`) VALUES
(2, 'kirubelbewket@gmail.com', 'deviceThree', 'wow!, it is a greatest technology i ever seen, and am glad it is made by my country', 'true', 'true', 'readed', '2024-05-05'),
(6, 'dagimbirhan@gmail.com', 'deviceOne', 'am trying to control my home remotely', 'false', 'true', 'readed', '2024-05-05'),
(7, 'solomonsisay@gmail.com', 'GOOD DEVICE', 'lkadghapoidksljoadkjapoikldsfjdklvjlkghiklspoklxcllkadghapoidksljoadkjapoikldsfjdklvjlkghiklspoklxcllkadghapoidksljoadkjapoikldsfjdklvjlkghiklspoklxcllkadghapoidksljoadkjapoikldsfjdklvjlkghiklspoklxcl', 'false', 'true', 'unreaded', '2024-08-08'),
(8, 'getnetmersha2@gmail.com', 'GOOD DEVICE', 'lkadghapoidksljoadkjapoikldsfjdklvjlkghiklspoklxcllkadghapoidksljoadkjdkjapoikldsfjd', 'false', 'true', 'unreaded', '2024-08-08');

-- --------------------------------------------------------

--
-- Table structure for table `firmwares`
--

CREATE TABLE `firmwares` (
  `id` int(10) NOT NULL,
  `firmware` varchar(50) NOT NULL,
  `code` varchar(50) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `released_by` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `firmwares`
--

INSERT INTO `firmwares` (`id`, `firmware`, `code`, `date`, `released_by`) VALUES
(4, 'firmwares/3.bin', 'firmwares/3.cpp', '2024-05-01 16:22:18', 'kirubelbewket@gmail.com'),
(8, 'firmwares/8.bin', 'firmwares/8.cpp', '2024-05-02 05:51:13', 'kirubelbewket@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `pricing`
--

CREATE TABLE `pricing` (
  `id` int(10) NOT NULL,
  `amount` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pricing`
--

INSERT INTO `pricing` (`id`, `amount`) VALUES
(1, 50000);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(10) NOT NULL,
  `amount` int(10) NOT NULL,
  `reason` text NOT NULL,
  `type` text NOT NULL,
  `description` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `amount`, `reason`, `type`, `description`, `date`) VALUES
(1, 5000, 'Donation', 'sell', 'MIT company', '2024-03-12 18:08:17'),
(2, 50000, 'DeviceThree', 'sell', 'sell', '2024-03-12 18:08:42'),
(3, 87654, 'Donation', 'buy', 'Small enterprises', '2024-03-13 17:41:51'),
(4, 25600, 'Borrowed ', 'sell', 'Amhara Bank', '2024-03-20 03:39:26'),
(5, 5000, 'deviceOne', 'sell', 'sell', '2024-03-20 03:43:44'),
(6, 5000, 'Payment', 'buy', 'employee sallary', '2024-03-21 04:24:34'),
(7, 5000, 'Borrowed ', 'sell', 'CBE', '2024-03-23 14:36:05'),
(8, 5432, 'Donation', 'sell', 'world bank', '2024-03-25 00:05:27'),
(9, 12000, 'DeviceOne', 'sell', 'sell', '2024-03-25 00:07:20'),
(10, 12000, 'deviceFive', 'sell', 'sell', '2024-03-25 02:35:39'),
(11, 12000, 'deviceSix', 'sell', 'sell', '2024-03-26 10:52:20'),
(12, 20000, 'deviceSeven', 'sell', 'sell', '2024-04-17 18:32:51'),
(13, 20000, 'deviceSeven', 'sell', 'sell', '2024-04-17 18:34:05'),
(14, 20000, 'deviceSeven', 'sell', 'sell', '2024-04-17 18:35:38'),
(15, 13000, 'deviceEight', 'sell', 'sell', '2024-04-18 15:54:17'),
(16, 13000, 'deviceNine', 'sell', 'sell', '2024-04-18 15:58:48'),
(17, 13000, 'deviceTen', 'sell', 'sell', '2024-04-18 16:01:03'),
(18, 16000, 'deviceEight', 'sell', 'sell', '2024-05-02 10:09:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQUE` (`email`),
  ADD KEY `device` (`device`),
  ADD KEY `price` (`price`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `device`
--
ALTER TABLE `device`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `INDEX` (`deviceName`) USING BTREE,
  ADD KEY `status` (`deviceStatus`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `firmwares`
--
ALTER TABLE `firmwares`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pricing`
--
ALTER TABLE `pricing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `amount` (`amount`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `device`
--
ALTER TABLE `device`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `firmwares`
--
ALTER TABLE `firmwares`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pricing`
--
ALTER TABLE `pricing`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `deviceName` FOREIGN KEY (`device`) REFERENCES `device` (`deviceName`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
