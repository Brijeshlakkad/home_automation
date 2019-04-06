-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 05, 2019 at 10:00 AM
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
-- Table structure for table `allowed_user`
--

CREATE TABLE `allowed_user` (
  `id` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `member_id` int(10) NOT NULL,
  `serial_no` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `allowed_user`
--

INSERT INTO `allowed_user` (`id`, `uid`, `member_id`, `serial_no`) VALUES
(44, 14, 13, '1212ABC12120'),
(46, 14, 20, '1212ABC12120'),
(49, 13, 14, '1212AAA11211'),
(50, 14, 20, '1212ABC12125'),
(51, 14, 20, '1212AAA11211');

-- --------------------------------------------------------

--
-- Table structure for table `amc`
--

CREATE TABLE `amc` (
  `id` int(11) NOT NULL,
  `serial_no_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `amc`
--

INSERT INTO `amc` (`id`, `serial_no_id`, `date`) VALUES
(1, 10, '2019-03-09 04:21:15'),
(3, 17, '2019-03-09 04:28:36'),
(4, 23, '2019-03-17 17:55:58'),
(5, 24, '2019-03-17 18:19:03'),
(6, 25, '2019-03-17 18:24:28'),
(7, 26, '2019-03-17 18:26:16'),
(8, 27, '2019-03-18 08:55:16'),
(9, 28, '2019-03-25 11:17:46'),
(10, 29, '2019-03-27 07:07:28'),
(11, 30, '2019-03-27 07:08:04'),
(12, 31, '2019-03-27 07:09:01'),
(13, 32, '2019-03-27 10:42:06'),
(14, 33, '2019-03-29 03:42:18'),
(15, 34, '2019-03-29 07:33:19'),
(16, 35, '2019-03-29 07:33:41'),
(17, 36, '2019-03-29 08:55:36'),
(18, 37, '2019-03-29 08:56:02'),
(19, 38, '2019-03-29 09:13:01'),
(20, 39, '2019-03-29 09:14:15'),
(21, 40, '2019-03-29 09:14:57'),
(22, 41, '2019-03-29 09:15:59'),
(23, 42, '2019-03-29 09:22:12');

-- --------------------------------------------------------

--
-- Table structure for table `assigned_user`
--

CREATE TABLE `assigned_user` (
  `id` int(10) NOT NULL,
  `serial_id` varchar(20) NOT NULL,
  `user_id` int(20) NOT NULL,
  `user_type` varchar(15) NOT NULL,
  `date` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `assigned_user`
--

INSERT INTO `assigned_user` (`id`, `serial_id`, `user_id`, `user_type`, `date`) VALUES
(1, '18', 7, 'dealer', '2019-03-27 08:38:26'),
(2, '5', 7, 'dealer', '2019-03-27 08:28:40'),
(3, '7', 7, 'dealer', '2019-03-27 08:29:35'),
(4, '15', 7, 'dealer', '2019-03-27 08:29:58'),
(5, '16', 7, 'dealer', '2019-03-27 08:29:58'),
(12, '18', 3, 'distributor', '2019-03-27 10:53:52'),
(13, '19', 7, 'dealer', '2019-03-27 09:59:18'),
(14, '19', 3, 'distributor', '2019-03-27 11:01:11'),
(15, '20', 7, 'dealer', '2019-03-27 13:01:43'),
(16, '21', 7, 'dealer', '2019-03-27 13:01:43'),
(17, '22', 7, 'dealer', '2019-03-27 13:01:43'),
(18, '23', 7, 'dealer', '2019-03-27 13:01:43'),
(19, '24', 7, 'dealer', '2019-03-27 13:01:43'),
(20, '25', 7, 'dealer', '2019-03-27 13:01:43'),
(21, '26', 7, 'dealer', '2019-03-27 13:01:43'),
(22, '27', 7, 'dealer', '2019-03-27 13:01:43'),
(23, '28', 7, 'dealer', '2019-03-27 13:01:43'),
(24, '29', 7, 'dealer', '2019-03-27 13:01:43'),
(25, '20', 3, 'distributor', '2019-03-27 13:02:17'),
(26, '21', 3, 'distributor', '2019-03-27 13:02:17'),
(27, '22', 3, 'distributor', '2019-03-27 13:02:17'),
(28, '23', 3, 'distributor', '2019-03-27 13:02:17'),
(29, '24', 3, 'distributor', '2019-03-27 13:02:17'),
(30, '25', 3, 'distributor', '2019-03-27 13:02:17'),
(31, '26', 3, 'distributor', '2019-03-27 13:02:17');

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
  `type` varchar(14) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `dealer`
--

INSERT INTO `dealer` (`id`, `name`, `address`, `city`, `email`, `mobile`, `code`, `password`, `type`) VALUES
(3, 'Brijesh', '205, Nanddham Apartment', 'Surat', 'lakkadbrijesh@gmail.com', '7046167267', '', '123456bB', 'distributor'),
(7, 'Brijesh', '205, Nanddham Apartment, NR Ashok Vatika Society, Kapodra, Surat', 'Surat', 'brijeshlakkad@gmail.com', '7046167268', '', '123456bB', 'dealer');

-- --------------------------------------------------------

--
-- Table structure for table `device`
--

CREATE TABLE `device` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `image` varchar(30) NOT NULL,
  `max_val` int(4) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `device`
--

INSERT INTO `device` (`id`, `name`, `image`, `max_val`) VALUES
(1, 'ac', 'ac.png', 40),
(2, 'camera', 'camera.png', 1),
(3, 'dvd', 'dvd.png', 1),
(4, 'fan', 'fan.png', 5),
(5, 'light', 'light.png', 1),
(6, 'projecter', 'projecter.png', 1),
(7, 'set top', 'set_top.png', 1),
(8, 'tv', 'tv.png', 1);

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
(33, 15, 0),
(15, 16, 0),
(34, 30, 30),
(36, 33, 0),
(35, 32, 0);

-- --------------------------------------------------------

--
-- Table structure for table `frequency`
--

CREATE TABLE `frequency` (
  `id` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `value` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
(37, 13, 36, 30, 'H2345', '1212AAA11211', '1.1.1.1'),
(36, 13, 36, 30, 'H234', '1212ABC12126', '1.1.1.1'),
(32, 14, 18, 8, 'H123', '1212ABC12125', '1.1.1.1'),
(33, 14, 18, 8, 'HNew', '1212ABC12120', '1.2.3.4'),
(42, 14, 18, 8, 'Brij', '1212AAA11211', '1.1.1.1');

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
(36, 13, 'Home1'),
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
(13, 'home_pro', 12000, 7000, '', '12', '12', '12', '11'),
(18, 'p1', 10000, 5000, '', '12', '12', '12', '12'),
(19, 'AutoMate', 100, 190, '', '1212', '12', '12', '12');

-- --------------------------------------------------------

--
-- Table structure for table `product_serial`
--

CREATE TABLE `product_serial` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `serial_no` varchar(20) NOT NULL,
  `assigned_dealer` int(10) DEFAULT NULL,
  `assigned_distributor` int(10) DEFAULT NULL,
  `sold_product_id` int(11) DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `product_serial`
--

INSERT INTO `product_serial` (`id`, `product_id`, `serial_no`, `assigned_dealer`, `assigned_distributor`, `sold_product_id`, `date`) VALUES
(5, 13, '1212ABC12121', 2, 8, NULL, '2019-03-26 19:33:36'),
(7, 13, '212121212122', 3, NULL, NULL, '2019-03-26 19:33:36'),
(15, 13, '1212ABC12124', 4, NULL, NULL, '2019-03-26 19:33:36'),
(16, 13, '1212GFGG1212', 5, NULL, NULL, '2019-03-26 19:33:36'),
(17, 13, '1212GFGG1211', NULL, NULL, NULL, '2019-03-26 19:33:36'),
(18, 18, '1212ACA12121', 1, 12, 1, '2019-03-26 19:33:36'),
(19, 18, '1212ABC12122', 13, 14, 4, '2019-03-26 19:33:36'),
(20, 18, '1212ABC12125', 15, 25, 5, '2019-03-26 19:33:36'),
(21, 18, '1212ABC12128', 16, 26, 6, '2019-03-26 19:33:36'),
(22, 18, '1212ABC12120', 17, 27, 7, '2019-03-26 19:33:36'),
(23, 18, '1212ABC12111', 18, 28, 8, '2019-03-26 19:33:36'),
(24, 18, '1212ABC12126', 19, 29, 9, '2019-03-26 19:33:36'),
(25, 18, '1212AAA11211', 20, 30, 10, '2019-03-26 19:33:36'),
(26, 18, '1212AAA11212', 21, 31, 11, '2019-03-26 19:33:36'),
(27, 18, '1212AAA11213', 22, NULL, NULL, '2019-03-26 19:33:36'),
(28, 18, '1212AAA12122', 23, NULL, NULL, '2019-03-26 19:33:36'),
(29, 18, '1212AAA12121', 24, NULL, NULL, '2019-03-26 19:33:36'),
(30, 18, '1212AAA12123', NULL, NULL, NULL, '2019-03-26 19:33:36'),
(31, 18, '1212SDS12121', NULL, NULL, NULL, '2019-03-26 19:33:36'),
(32, 18, '1212SDS12122', NULL, NULL, NULL, '2019-03-26 19:33:36'),
(33, 18, '1212SDS12123', NULL, NULL, NULL, '2019-03-26 19:33:36'),
(34, 18, '1212SDSD1212', NULL, NULL, NULL, '2019-03-26 19:33:36'),
(35, 18, '1212SDSD1213', NULL, NULL, NULL, '2019-03-26 19:33:36'),
(36, 18, '1212SDSD1214', NULL, NULL, NULL, '2019-03-26 19:33:36'),
(37, 18, '1212DSD12121', NULL, NULL, NULL, '2019-03-26 19:33:36'),
(38, 18, '1212DSD12125', NULL, NULL, NULL, '2019-03-26 19:33:36'),
(39, 18, '1212DSD12127', NULL, NULL, NULL, '2019-03-26 19:33:36'),
(40, 18, '1212ASAS1111', NULL, NULL, NULL, '2019-03-26 19:33:36'),
(41, 18, '1212ASAS1112', NULL, NULL, NULL, '2019-03-26 19:33:36'),
(42, 18, '1212ASAS1113', NULL, NULL, NULL, '2019-03-26 19:33:36');

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
(8, 14, 18, 'Room1'),
(9, 14, 18, 'Room2'),
(10, 14, 16, 'Room2'),
(14, 14, 18, 'Room3'),
(30, 13, 36, 'Hall');

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
  `status` int(2) NOT NULL DEFAULT '1',
  `from_scheduled_time` datetime NOT NULL,
  `to_scheduled_time` datetime NOT NULL,
  `after_status` varchar(20) NOT NULL,
  `frequency` varchar(6) NOT NULL DEFAULT '0',
  `date` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `room_device`
--

INSERT INTO `room_device` (`id`, `uid`, `room_id`, `hid`, `hw_id`, `device_name`, `device_image`, `port`, `status`, `from_scheduled_time`, `to_scheduled_time`, `after_status`, `frequency`, `date`) VALUES
(6, 1, 1, 1, 4, 'light', 'light.png', 2, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '0', '2019-04-05 08:25:36'),
(5, 1, 1, 1, 4, 'fan', 'fan.png', 1, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '0', '2019-04-05 08:25:36'),
(8, 1, 1, 1, 4, 'ac', 'ac.png', 5, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '0', '2019-04-05 08:25:36'),
(29, 14, 8, 18, 32, 'Light', 'light.png', 1, 1, '2019-04-05 12:59:00', '2019-04-06 12:59:00', '1', 'THU', '2019-04-05 08:25:36'),
(33, 14, 8, 18, 32, 'AC', 'ac.png', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '0', '2019-04-05 10:41:34');

-- --------------------------------------------------------

--
-- Table structure for table `sold_product`
--

CREATE TABLE `sold_product` (
  `id` int(10) NOT NULL,
  `serial_id` varchar(20) NOT NULL,
  `customer_email` varchar(30) NOT NULL,
  `date` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sold_product`
--

INSERT INTO `sold_product` (`id`, `serial_id`, `customer_email`, `date`) VALUES
(1, '18', 'brijeshlakkad22@gmail.com', '2019-03-27 11:07:54'),
(4, '19', 'sapnil@gmail.com', '2019-03-27 11:09:35'),
(5, '20', 'brijeshlakkad22@gmail.com', '2019-03-27 13:02:46'),
(6, '21', 'brijeshlakkad22@gmail.com', '2019-03-27 13:02:46'),
(7, '22', 'brijeshlakkad22@gmail.com', '2019-03-27 13:02:46'),
(8, '23', 'brijeshlakkad22@gmail.com', '2019-03-27 13:02:46'),
(9, '24', 'sapnil@gmail.com', '2019-03-27 13:02:59'),
(10, '25', 'sapnil@gmail.com', '2019-03-27 13:02:59'),
(11, '26', 'brijeshl@gmail.com', '2019-03-27 13:03:09');

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
(14, 'Brijesh', 'Nanddham Apratment, Kapodra', 'Surat', 'brijeshlakkad22@gmail.com', '7046167267', '123456bB', ''),
(20, 'Brijesh', 'madhav', 'Surat', 'brijeshl@gmail.com', '7046167261', '123456bB', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `allowed_user`
--
ALTER TABLE `allowed_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `amc`
--
ALTER TABLE `amc`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `assigned_user`
--
ALTER TABLE `assigned_user`
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
-- Indexes for table `frequency`
--
ALTER TABLE `frequency`
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
-- Indexes for table `sold_product`
--
ALTER TABLE `sold_product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `allowed_user`
--
ALTER TABLE `allowed_user`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `amc`
--
ALTER TABLE `amc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `assigned_user`
--
ALTER TABLE `assigned_user`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `frequency`
--
ALTER TABLE `frequency`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hardware`
--
ALTER TABLE `hardware`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `home`
--
ALTER TABLE `home`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `product_serial`
--
ALTER TABLE `product_serial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `room_device`
--
ALTER TABLE `room_device`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `sold_product`
--
ALTER TABLE `sold_product`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
