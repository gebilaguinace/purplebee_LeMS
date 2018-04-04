-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 03, 2018 at 09:14 AM
-- Server version: 10.1.30-MariaDB
-- PHP Version: 7.2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `purplebee`
--

-- --------------------------------------------------------

--
-- Table structure for table `company_branches`
--

CREATE TABLE `company_branches` (
  `id` int(255) NOT NULL,
  `location` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `company_branches`
--

INSERT INTO `company_branches` (`id`, `location`) VALUES
(7, 'jujuuj'),
(6, 'Legaspi City'),
(5, 'Naga City');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id` int(255) NOT NULL,
  `maker_name` text,
  `comaker_name` text,
  `company` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `status` enum('N/A','Unpaid','Paid') NOT NULL DEFAULT 'N/A',
  `branch_id` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`id`, `maker_name`, `comaker_name`, `company`, `address`, `status`, `branch_id`) VALUES
(1, 'abc', 'def', 'edward', 'catmon 2', 'Unpaid', 6),
(2, 'jacin', 'kim', 'youjozz', 'All around', 'Unpaid', 6),
(3, 'teasd', 'asdasd', 'asda', 'sd', 'Paid', 6),
(6, 'kim', 'luta', 'mcdo', 'salingogn minalabac camarines sur', 'Unpaid', 6),
(7, 'kim', 'kim', 'mcdo', 'naga city', 'Paid', 6);

-- --------------------------------------------------------

--
-- Table structure for table `debt`
--

CREATE TABLE `debt` (
  `id` int(255) NOT NULL,
  `amount` int(255) NOT NULL,
  `balance` int(255) NOT NULL,
  `month` int(255) NOT NULL,
  `year` int(255) NOT NULL,
  `status` enum('PAID','UNPAID','PAST') NOT NULL DEFAULT 'UNPAID',
  `lending_id` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `debt`
--

INSERT INTO `debt` (`id`, `amount`, `balance`, `month`, `year`, `status`, `lending_id`) VALUES
(164, 368, 0, 4, 2018, 'PAID', 9),
(165, 368, 0, 5, 2018, 'PAID', 9),
(166, 368, 0, 6, 2018, 'PAID', 9),
(167, 6827, 0, 3, 2018, 'PAID', 10),
(168, 6827, 0, 4, 2018, 'PAID', 10),
(169, 38, 0, 3, 2018, 'PAID', 13),
(170, 38, 24, 4, 2018, 'UNPAID', 13),
(171, 38, 38, 5, 2018, 'UNPAID', 13),
(172, 2, 0, 1, 2009, 'PAID', 14),
(173, 2, 0, 2, 2009, 'PAID', 14),
(174, 2, 0, 3, 2009, 'PAID', 14),
(175, 2, 0, 4, 2009, 'PAID', 14),
(176, 2, 0, 5, 2009, 'PAID', 14),
(177, 2, 0, 6, 2009, 'PAID', 14),
(178, 2, 0, 7, 2009, 'PAID', 14),
(179, 2, 0, 8, 2009, 'PAID', 14),
(180, 2, 0, 9, 2009, 'PAID', 14),
(181, 2, 0, 10, 2009, 'PAID', 14),
(182, 2, 0, 11, 2009, 'PAID', 14),
(183, 2, 0, 12, 2009, 'PAID', 14),
(184, 14, 0, 1, 2009, 'PAID', 15),
(185, 74, 0, 1, 2009, 'PAID', 15),
(186, 74, 0, 2, 2009, 'PAID', 16),
(187, 74, 0, 3, 2009, 'PAID', 16),
(188, 2, 0, 3, 2018, 'PAID', 17),
(189, 2, 0, 4, 2018, 'PAID', 17),
(190, 2, 0, 5, 2018, 'PAID', 17),
(191, 2, 0, 6, 2018, 'PAID', 17),
(192, 2, 0, 7, 2018, 'PAID', 17),
(193, 2, 0, 8, 2018, 'PAID', 17),
(194, 2, 0, 9, 2018, 'PAID', 17),
(195, 2, 0, 10, 2018, 'PAID', 17),
(196, 2, 0, 11, 2018, 'PAID', 17),
(197, 2, 0, 12, 2018, 'PAID', 17),
(198, 2, 0, 1, 2018, 'PAID', 17),
(199, 2, 0, 2, 2018, 'PAID', 17),
(200, 2, 0, 3, 2018, 'PAID', 17),
(201, 2, 0, 4, 2018, 'PAID', 17),
(202, 2, 0, 5, 2018, 'PAID', 17),
(203, 2, 0, 6, 2018, 'PAID', 17),
(204, 2, 0, 7, 2018, 'PAID', 17),
(205, 2, 0, 8, 2018, 'PAID', 17),
(206, 2, 0, 9, 2018, 'PAID', 17),
(207, 2, 0, 10, 2018, 'PAID', 17),
(208, 2, 0, 11, 2018, 'PAID', 17),
(209, 2, 0, 12, 2018, 'PAID', 17),
(210, 2, 0, 1, 2018, 'PAID', 17),
(211, 2, 0, 2, 2018, 'PAID', 17),
(212, 2, 0, 3, 2018, 'PAID', 17),
(213, 2, 0, 4, 2018, 'PAID', 17),
(214, 2, 0, 5, 2018, 'PAID', 17),
(215, 2, 0, 6, 2018, 'PAID', 17),
(216, 2, 0, 7, 2018, 'PAID', 17),
(217, 2, 0, 8, 2018, 'PAID', 17),
(218, 2, 0, 9, 2018, 'PAID', 17),
(219, 2, 0, 10, 2018, 'PAID', 17),
(220, 2, 0, 11, 2018, 'PAID', 17),
(221, 2, 0, 12, 2018, 'PAID', 17),
(222, 2, 0, 1, 2018, 'PAID', 17),
(223, 2, 0, 2, 2018, 'PAID', 17),
(224, 2, 0, 3, 2018, 'PAID', 17),
(225, 2, 0, 4, 2018, 'PAID', 17),
(226, 2, 0, 5, 2018, 'PAID', 17),
(227, 2, 0, 6, 2018, 'PAID', 17),
(228, 2, 0, 7, 2018, 'PAID', 17),
(229, 2, 0, 8, 2018, 'PAID', 17),
(230, 2, 0, 9, 2018, 'PAID', 17),
(231, 2, 0, 10, 2018, 'PAID', 17),
(232, 2, 0, 11, 2018, 'PAID', 17),
(233, 2, 0, 12, 2018, 'PAID', 17),
(234, 2, 0, 1, 2018, 'PAID', 17),
(235, 2, 0, 2, 2018, 'PAID', 17),
(236, 2, 0, 3, 2018, 'PAID', 17),
(237, 2, 0, 4, 2018, 'PAID', 17),
(238, 2, 0, 5, 2018, 'PAID', 17),
(239, 2, 0, 6, 2018, 'PAID', 17),
(240, 2, 0, 7, 2018, 'PAID', 17),
(241, 2, 0, 8, 2018, 'PAID', 17),
(242, 2, 0, 9, 2018, 'PAID', 17),
(243, 2, 0, 10, 2018, 'PAID', 17),
(244, 2, 0, 11, 2018, 'PAID', 17),
(245, 2, 0, 12, 2018, 'PAID', 17),
(246, 2, 0, 1, 2018, 'PAID', 17),
(247, 2, 0, 2, 2018, 'PAID', 17),
(248, 2, 0, 3, 2018, 'PAID', 17),
(249, 2, 0, 4, 2018, 'PAID', 17),
(250, 2, 0, 5, 2018, 'PAID', 17),
(251, 2, 0, 6, 2018, 'PAID', 17),
(252, 2, 0, 7, 2018, 'PAID', 17),
(253, 2, 0, 8, 2018, 'PAID', 17),
(254, 2, 0, 9, 2018, 'PAID', 17),
(255, 2, 0, 10, 2018, 'PAID', 17),
(256, 2, 0, 11, 2018, 'PAID', 17),
(257, 2, 0, 12, 2018, 'PAID', 17),
(258, 2, 0, 1, 2018, 'PAID', 17),
(259, 2, 0, 2, 2018, 'PAID', 17),
(260, 2, 0, 3, 2018, 'PAID', 17),
(261, 2, 0, 4, 2018, 'PAID', 17),
(262, 2, 0, 5, 2018, 'PAID', 17),
(263, 2, 0, 6, 2018, 'PAID', 17),
(264, 2, 0, 7, 2018, 'PAID', 17),
(265, 2, 0, 8, 2018, 'PAID', 17),
(266, 2, 0, 9, 2018, 'PAID', 17),
(267, 2, 0, 10, 2018, 'PAID', 17),
(268, 2, 0, 11, 2018, 'PAID', 17),
(269, 2, 0, 12, 2018, 'PAID', 17),
(270, 2, 0, 1, 2018, 'PAID', 17),
(271, 2, 0, 2, 2018, 'PAID', 17),
(272, 2, 0, 3, 2018, 'PAID', 17),
(273, 2, 0, 4, 2018, 'PAID', 17),
(274, 2, 0, 5, 2018, 'PAID', 17),
(275, 2, 0, 6, 2018, 'PAID', 17),
(276, 2, 0, 7, 2018, 'PAID', 17),
(277, 2, 0, 8, 2018, 'PAID', 17),
(278, 2, 0, 9, 2018, 'PAID', 17),
(279, 2, 0, 10, 2018, 'PAID', 17),
(280, 2, 0, 11, 2018, 'PAID', 17),
(281, 2, 0, 12, 2018, 'PAID', 17),
(282, 2, 0, 1, 2018, 'PAID', 17),
(283, 2, 0, 2, 2018, 'PAID', 17),
(284, 2, 0, 3, 2018, 'PAID', 17),
(285, 2, 0, 4, 2018, 'PAID', 17),
(286, 2, 0, 5, 2018, 'PAID', 17),
(287, 2, 0, 6, 2018, 'PAID', 17),
(288, 2, 0, 7, 2018, 'PAID', 17),
(289, 2, 0, 8, 2018, 'PAID', 17),
(290, 2, 0, 9, 2018, 'PAID', 17),
(291, 2, 0, 10, 2018, 'PAID', 17),
(292, 2, 0, 11, 2018, 'PAID', 17),
(293, 2, 0, 12, 2018, 'PAID', 17),
(294, 2, 0, 1, 2018, 'PAID', 17),
(295, 2, 0, 2, 2018, 'PAID', 17),
(296, 2, 0, 3, 2018, 'PAID', 17),
(297, 2, 0, 4, 2018, 'PAID', 17),
(298, 2, 0, 5, 2018, 'PAID', 17),
(299, 2, 0, 6, 2018, 'PAID', 17),
(300, 2, 0, 7, 2018, 'PAID', 17),
(301, 2, 0, 8, 2018, 'PAID', 17),
(302, 2, 0, 9, 2018, 'PAID', 17),
(303, 2, 0, 10, 2018, 'PAID', 17),
(304, 2, 0, 11, 2018, 'PAID', 17),
(305, 2, 0, 12, 2018, 'PAID', 17),
(306, 2, 0, 1, 2018, 'PAID', 17),
(307, 2, 0, 2, 2018, 'PAID', 17),
(308, 2, 0, 3, 2018, 'PAID', 17),
(309, 2, 0, 4, 2018, 'PAID', 17),
(310, 2, 0, 5, 2018, 'PAID', 17),
(311, 2, 0, 6, 2018, 'PAID', 17),
(312, 1650, 0, 12, 2018, 'PAID', 18),
(313, 1650, 1000, 1, 2019, 'UNPAID', 18),
(314, 2, 0, 3, 2018, 'PAID', 19),
(315, 3668, 0, 3, 2018, 'PAID', 20),
(316, 3668, 2336, 4, 2018, 'UNPAID', 20),
(317, 3668, 3668, 5, 2018, 'UNPAID', 20),
(318, 5, 0, 3, 2018, 'PAID', 21),
(319, 5, 0, 4, 2018, 'PAID', 21),
(320, 5, 0, 5, 2018, 'PAID', 21);

-- --------------------------------------------------------

--
-- Table structure for table `employee_details`
--

CREATE TABLE `employee_details` (
  `user_id` int(255) NOT NULL,
  `id` int(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `branch_id` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `employee_details`
--

INSERT INTO `employee_details` (`user_id`, `id`, `fname`, `lname`, `branch_id`) VALUES
(9, 4, 'asda3', 'asda', 6),
(10, 5, 'asda3', 'asda', 6),
(11, 6, 'luta', 'luta', 6);

-- --------------------------------------------------------

--
-- Table structure for table `lending`
--

CREATE TABLE `lending` (
  `id` int(255) NOT NULL,
  `amount` int(255) NOT NULL,
  `loantype` enum('sl','bl','cl','hl','gl','pl') DEFAULT 'sl',
  `monthterm` int(255) NOT NULL,
  `fdd` date NOT NULL,
  `payroll_dates` varchar(255) NOT NULL,
  `customer_id` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lending`
--

INSERT INTO `lending` (`id`, `amount`, `loantype`, `monthterm`, `fdd`, `payroll_dates`, `customer_id`) VALUES
(9, 1000, 'bl', 3, '2018-04-19', '12,123,15', 2),
(10, 12412, 'bl', 2, '2018-03-24', '15,30', 3),
(13, 100, 'bl', 3, '2018-03-24', '13,15,30', 1),
(14, 12, 'bl', 12, '2009-01-10', 'asd', 3),
(15, 12, 'bl', 1, '2009-01-16', '12', 1),
(16, 200, 'bl', 3, '2009-01-17', '12,30', 1),
(17, 124, 'bl', 124, '2018-03-20', '13,50', 1),
(18, 3000, 'bl', 2, '2018-12-24', '15,30', 2),
(19, 1, 'bl', 1, '2018-03-20', '14', 1),
(20, 10000, 'bl', 3, '2018-03-21', '15 30 fridays', 6),
(21, 12, 'bl', 3, '2018-03-22', '15,30,monday', 7);

-- --------------------------------------------------------

--
-- Table structure for table `payment_history`
--

CREATE TABLE `payment_history` (
  `id` int(255) NOT NULL,
  `user_id` int(255) NOT NULL,
  `lending_id` int(255) NOT NULL,
  `amount` int(255) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `payment_history`
--

INSERT INTO `payment_history` (`id`, `user_id`, `lending_id`, `amount`, `date`) VALUES
(1, 9, 14, 1, '2018-03-19 12:33:06'),
(2, 9, 14, 1, '2018-03-19 12:33:45'),
(4, 9, 18, 1650, '2018-03-19 22:05:00'),
(5, 9, 18, 650, '2018-03-19 22:05:12'),
(6, 9, 14, 5, '2018-03-19 22:16:59'),
(7, 10, 19, 1, '2018-03-19 22:51:32'),
(8, 10, 19, 1, '2018-03-20 19:15:12'),
(9, 9, 9, 68, '2018-03-20 19:17:04'),
(10, 10, 9, 300, '2018-03-21 16:00:03'),
(11, 9, 20, 5000, '2018-03-21 16:31:52'),
(12, 10, 9, 400, '2018-03-21 16:34:13'),
(13, 9, 21, 15, '2018-03-22 15:12:17'),
(14, 9, 9, 400, '2018-03-22 15:14:42'),
(15, 9, 13, 12, '2018-03-22 15:35:27'),
(16, 9, 13, 1, '2018-03-24 14:51:12');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `emptype` enum('bm','os','ad','fc') DEFAULT 'fc'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `emptype`) VALUES
(5, 'admin', 'admin', 'ad'),
(9, 'kim', 'kim', 'bm'),
(10, 'jay', 'jay', 'fc'),
(11, 'luta', 'luta', 'os');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `company_branches`
--
ALTER TABLE `company_branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `location` (`location`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `debt`
--
ALTER TABLE `debt`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lending_id` (`lending_id`);

--
-- Indexes for table `employee_details`
--
ALTER TABLE `employee_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `lending`
--
ALTER TABLE `lending`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `payment_history`
--
ALTER TABLE `payment_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lending_id` (`lending_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `company_branches`
--
ALTER TABLE `company_branches`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `debt`
--
ALTER TABLE `debt`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=321;

--
-- AUTO_INCREMENT for table `employee_details`
--
ALTER TABLE `employee_details`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `lending`
--
ALTER TABLE `lending`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `payment_history`
--
ALTER TABLE `payment_history`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customer`
--
ALTER TABLE `customer`
  ADD CONSTRAINT `customer_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `company_branches` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `debt`
--
ALTER TABLE `debt`
  ADD CONSTRAINT `debt_ibfk_1` FOREIGN KEY (`lending_id`) REFERENCES `lending` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `employee_details`
--
ALTER TABLE `employee_details`
  ADD CONSTRAINT `employee_details_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `company_branches` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `employee_details_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `lending`
--
ALTER TABLE `lending`
  ADD CONSTRAINT `lending_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `payment_history`
--
ALTER TABLE `payment_history`
  ADD CONSTRAINT `payment_history_ibfk_1` FOREIGN KEY (`lending_id`) REFERENCES `lending` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `payment_history_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
