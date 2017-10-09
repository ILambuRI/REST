-- phpMyAdmin SQL Dump
-- version 4.4.15.7
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 09, 2017 at 02:19 PM
-- Server version: 5.7.13
-- PHP Version: 5.6.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rest`
--

-- --------------------------------------------------------

--
-- Table structure for table `rest_cars`
--

CREATE TABLE IF NOT EXISTS `rest_cars` (
  `id` int(11) NOT NULL,
  `mark` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `year` int(11) NOT NULL,
  `engine` int(11) NOT NULL,
  `color` varchar(50) NOT NULL,
  `speed` int(11) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `rest_cars`
--

INSERT INTO `rest_cars` (`id`, `mark`, `model`, `year`, `engine`, `color`, `speed`, `price`) VALUES
(1, 'BMW', 'X6', 2015, 500, 'white', 320, 120300),
(2, 'BMW', 'X6', 2016, 400, 'black', 280, 111000),
(3, 'BMW', 'X5', 2015, 300, 'black', 270, 110000),
(4, 'BMW', 'X5', 2014, 250, 'white', 280, 92000),
(5, 'BMW', 'X1', 2013, 280, 'red', 260, 90600),
(6, 'BMW', 'X1', 2012, 220, 'orange', 240, 88600),
(7, 'VW', 'Golf', 2012, 220, 'white', 210, 70600),
(8, 'VW', 'Golf 2', 2013, 230, 'black', 220, 73600),
(9, 'VW', 'Golf 3', 2015, 400, 'red', 280, 83400),
(10, 'VW', 'Golf 4', 2014, 500, 'orange', 300, 84900),
(11, 'Mercedes', 'S1', 2017, 500, 'orange', 320, 123342),
(12, 'Mercedes', 'S2', 2016, 400, 'red', 300, 432555),
(13, 'Mercedes', 'S3', 2015, 300, 'white', 280, 123453),
(14, 'Mercedes', 'S4', 2014, 280, 'black', 290, 132346);

-- --------------------------------------------------------

--
-- Table structure for table `rest_orders`
--

CREATE TABLE IF NOT EXISTS `rest_orders` (
  `id` int(11) NOT NULL,
  `id_cars` int(11) NOT NULL,
  `id_users` int(11) NOT NULL,
  `payment` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `rest_orders`
--

INSERT INTO `rest_orders` (`id`, `id_cars`, `id_users`, `payment`, `status`) VALUES
(1, 1, 1, 'cash', '0'),
(2, 2, 4, 'paypal', '1'),
(3, 3, 4, 'cash', '1'),
(4, 1, 3, 'card', '0'),
(5, 1, 3, 'cash', '0');

-- --------------------------------------------------------

--
-- Table structure for table `rest_users`
--

CREATE TABLE IF NOT EXISTS `rest_users` (
  `id` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `hash` varchar(50) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `rest_users`
--

INSERT INTO `rest_users` (`id`, `login`, `firstname`, `lastname`, `password`, `hash`, `time`) VALUES
(1, 'lambur', 'Dima', 'Lambru', '144aa3c5d69a3db8c6b236ea29f0d176', '2c682ae63f672794d0bb9af3217484b7', 1507487373),
(3, 'lam', 'Dima', 'Lambru', 'e10adc3949ba59abbe56e057f20f883e', 'd8253d853c8f185e97b8cde6fa91f5f0', 123456),
(4, 'lama', 'Dima', 'Lambru', 'e10adc3949ba59abbe56e057f20f883e', 'e10adc3949ba59abbe56e057f20f883', 123456);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `rest_cars`
--
ALTER TABLE `rest_cars`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rest_orders`
--
ALTER TABLE `rest_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rest_orders_fk0` (`id_cars`),
  ADD KEY `rest_orders_fk1` (`id_users`);

--
-- Indexes for table `rest_users`
--
ALTER TABLE `rest_users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `rest_cars`
--
ALTER TABLE `rest_cars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `rest_orders`
--
ALTER TABLE `rest_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `rest_users`
--
ALTER TABLE `rest_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `rest_orders`
--
ALTER TABLE `rest_orders`
  ADD CONSTRAINT `rest_orders_fk0` FOREIGN KEY (`id_cars`) REFERENCES `rest_cars` (`id`),
  ADD CONSTRAINT `rest_orders_fk1` FOREIGN KEY (`id_users`) REFERENCES `rest_users` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
