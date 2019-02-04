-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 04, 2019 at 05:27 AM
-- Server version: 5.6.38
-- PHP Version: 7.2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `home_automation`
--

-- --------------------------------------------------------

--
-- Table structure for table `amc`
--

CREATE TABLE `amc` (
  `id` int(11) NOT NULL,
  `serial_no_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dealer`
--

CREATE TABLE `dealer` (
  `id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `address` varchar(100) NOT NULL,
  `city` varchar(25) NOT NULL,
  `email` varchar(50) NOT NULL,
  `mobile` varchar(12) NOT NULL,
  `code` varchar(20) NOT NULL,
  `password` varchar(50) NOT NULL,
  `type` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `dealer`
--

INSERT INTO `dealer` (`id`, `name`, `address`, `city`, `email`, `mobile`, `code`, `password`, `type`) VALUES
(3, 'Brijesh', '205, Nanddham Apartment', 'Surat', 'lakkadbrijesh@gmail.com', '7046167267', '', '123456bB', 'dealer'),
(7, 'Brijesh', '205, Nanddham Apartment, NR Ashok Vatika Society, Kapodra, Surat', 'Surat', 'brijeshlakkad22@gmail.com', '7046167268', '', '123456bB', 'dealer');

-- --------------------------------------------------------

--
-- Table structure for table `device`
--

CREATE TABLE `device` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `image` varchar(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `device`
--

INSERT INTO `device` (`id`, `name`, `image`) VALUES
(1, 'ac', 'ac.png'),
(2, 'camera', 'camera.png'),
(3, 'dvd', 'dvd.png'),
(4, 'fan', 'fan.png'),
(5, 'light', 'light.png'),
(6, 'projecter', 'projecter.png'),
(7, 'set top', 'set_top.png'),
(8, 'tv', 'tv.png');

-- --------------------------------------------------------

--
-- Table structure for table `devicevalue`
--

CREATE TABLE `devicevalue` (
  `id` int(11) NOT NULL,
  `did` int(11) NOT NULL,
  `value` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `devicevalue`
--

INSERT INTO `devicevalue` (`id`, `did`, `value`) VALUES
(5, 8, 0),
(3, 5, 71),
(7, 15, 4),
(15, 16, 0);

-- --------------------------------------------------------

--
-- Table structure for table `hardware`
--

CREATE TABLE `hardware` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `hid` int(11) NOT NULL,
  `rid` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `series` varchar(20) NOT NULL,
  `ip_value` varchar(25) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `hardware`
--

INSERT INTO `hardware` (`id`, `uid`, `hid`, `rid`, `name`, `series`, `ip_value`) VALUES
(4, 1, 1, 1, 'Nakshatra', '08ASDF10', '192.168.43.11'),
(2, 1, 1, 2, 'Star', '12QWER10', '192.168.0.10'),
(6, 1, 1, 1, 'sTRAR', '08asdf12', '192.168.0.10'),
(7, 14, 6, 6, 'brij', '12345678', '192.168.0.1'),
(10, 14, 18, 8, 'h12345', '123455', '0.0.0.0'),
(11, 14, 18, 8, 'h1234', '12345', '1.1.1.1');

-- --------------------------------------------------------

--
-- Table structure for table `home`
--

CREATE TABLE `home` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `homename` varchar(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `home`
--

INSERT INTO `home` (`id`, `uid`, `homename`) VALUES
(1, 1, 'My Home'),
(18, 14, 'home1'),
(16, 14, 'home2');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `s_rate` int(11) NOT NULL,
  `p_rate` int(11) NOT NULL,
  `product_code` varchar(20) NOT NULL,
  `description` text NOT NULL,
  `taxation` text NOT NULL,
  `hsncode` varchar(25) NOT NULL,
  `qty_name` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `name`, `s_rate`, `p_rate`, `product_code`, `description`, `taxation`, `hsncode`, `qty_name`) VALUES
(13, 'p1', 121, 12, '', '12', '12', '12', '12');

-- --------------------------------------------------------

--
-- Table structure for table `product_serial`
--

CREATE TABLE `product_serial` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `serial_no` varchar(20) NOT NULL,
  `dealer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `hid` int(11) NOT NULL,
  `roomname` varchar(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`id`, `uid`, `hid`, `roomname`) VALUES
(1, 1, 1, 'Main Hall'),
(2, 1, 1, 'Kitchen'),
(6, 14, 6, 'room1'),
(8, 14, 18, 'room1'),
(9, 14, 18, 'room2'),
(10, 14, 16, 'room1'),
(14, 14, 18, 'room3');

-- --------------------------------------------------------

--
-- Table structure for table `room_device`
--

CREATE TABLE `room_device` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `hid` int(11) NOT NULL,
  `hw_id` int(11) NOT NULL,
  `device_name` varchar(20) NOT NULL,
  `device_image` varchar(30) NOT NULL,
  `port` int(11) NOT NULL,
  `status` int(2) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `room_device`
--

INSERT INTO `room_device` (`id`, `uid`, `room_id`, `hid`, `hw_id`, `device_name`, `device_image`, `port`, `status`) VALUES
(6, 1, 1, 1, 4, 'light', 'light.png', 2, 0),
(5, 1, 1, 1, 4, 'fan', 'fan.png', 1, 0),
(8, 1, 1, 1, 4, 'ac', 'ac.png', 5, 1),
(14, 14, 8, 18, 10, 'd', 'camera.png', 1, 1),
(15, 14, 8, 18, 10, '2', 'ac.png', 1, 1),
(20, 14, 8, 18, 10, 'light', 'light.png', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `sale_to_id` int(11) NOT NULL,
  `sale_from_id` int(11) NOT NULL,
  `product_code_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `address` varchar(250) NOT NULL,
  `city` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `mobile` varchar(10) NOT NULL,
  `password` varchar(50) NOT NULL,
  `biomatrix` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `address`, `city`, `email`, `mobile`, `password`, `biomatrix`) VALUES
(1, 'bhadresh', 'borsad', 'borsad', 'bhadresh@gmail.com', '1234567890', '12345', ''),
(12, 'kelvin', 'anand', 'anand', 'kelvin@gmail.com', '1234567890', '1234', ''),
(13, 'sapnil', 'anand', 'anand', 'sapnil@gmail.com', '1234567890', '1qwerty', ''),
(14, 'Brijesh', 'Nanddham Apratment, Kapodra', 'Surat', 'brijeshlakkad22@gmail.com', '7046167267', '123456bB', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `amc`
--
ALTER TABLE `amc`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dealer`
--
ALTER TABLE `dealer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `device`
--
ALTER TABLE `device`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `devicevalue`
--
ALTER TABLE `devicevalue`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hardware`
--
ALTER TABLE `hardware`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `home`
--
ALTER TABLE `home`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_serial`
--
ALTER TABLE `product_serial`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_device`
--
ALTER TABLE `room_device`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `amc`
--
ALTER TABLE `amc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dealer`
--
ALTER TABLE `dealer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `device`
--
ALTER TABLE `device`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `devicevalue`
--
ALTER TABLE `devicevalue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `hardware`
--
ALTER TABLE `hardware`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `home`
--
ALTER TABLE `home`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `product_serial`
--
ALTER TABLE `product_serial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `room_device`
--
ALTER TABLE `room_device`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
