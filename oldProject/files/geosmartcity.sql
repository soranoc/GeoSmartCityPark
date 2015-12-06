-- phpMyAdmin SQL Dump
-- version 4.4.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 13, 2015 at 08:24 PM
-- Server version: 10.0.19-MariaDB-log
-- PHP Version: 5.6.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `geosmartcity`
--

-- --------------------------------------------------------

--
-- Table structure for table `entrance_address`
--

CREATE TABLE IF NOT EXISTS `entrance_address` (
  `Segment` int(11) NOT NULL,
  `StreetName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `StreetNumber` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exit_address`
--

CREATE TABLE IF NOT EXISTS `exit_address` (
  `Segment` int(11) NOT NULL,
  `StreetName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `StreetNumber` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `extra`
--

CREATE TABLE IF NOT EXISTS `extra` (
  `Segment` int(11) NOT NULL,
  `Type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Value` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `Qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `is_implemented`
--

CREATE TABLE IF NOT EXISTS `is_implemented` (
  `Type` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Segment` int(20) NOT NULL,
  `Note` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `MinValue` int(11) DEFAULT NULL,
  `TypValue` int(11) DEFAULT NULL,
  `MaxValue` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE IF NOT EXISTS `options` (
  `Type` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `description` char(100) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `options`
--

INSERT INTO `options` (`Type`, `description`) VALUES
('Heated', 'Lot is heated'),
('nothing', 'No extra i'),
('Roof', 'Lot has a roof'),
('Surveillance', 'Camera Surveillance');

-- --------------------------------------------------------

--
-- Table structure for table `park_lot`
--

CREATE TABLE IF NOT EXISTS `park_lot` (
  `City` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Name` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_type`
--

CREATE TABLE IF NOT EXISTS `payment_type` (
  `Segment` int(11) NOT NULL,
  `OptionType` varchar(30) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `segment`
--

CREATE TABLE IF NOT EXISTS `segment` (
  `Segment` int(11) NOT NULL,
  `SegmentName` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `TotalSlotQty` int(11) DEFAULT '-1',
  `OccupiedQty` int(11) DEFAULT NULL,
  `Name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `City` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `GPSLongitude` char(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `GPSLatitude` char(10) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `time_open`
--

CREATE TABLE IF NOT EXISTS `time_open` (
  `Segment` int(11) NOT NULL,
  `OpenFrom` time NOT NULL DEFAULT '00:00:00',
  `CloseAt` time NOT NULL DEFAULT '23:59:59',
  `MaxHours` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `updatelog`
--

CREATE TABLE IF NOT EXISTS `updatelog` (
  `Line` int(11) NOT NULL,
  `EditedTable` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `OldValue` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `NewValue` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `UserName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `UpdateTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `useradmin`
--

CREATE TABLE IF NOT EXISTS `useradmin` (
  `UserID` varchar(12) COLLATE utf8_unicode_ci DEFAULT NULL,
  `UserPWD` varchar(12) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `useradmin`
--

INSERT INTO `useradmin` (`UserID`, `UserPWD`) VALUES
('kayttaja1', 'salasana1'),
('kayttaja2', 'salasana2'),
('asd', 'asd');

-- --------------------------------------------------------

--
-- Table structure for table `walking_address`
--

CREATE TABLE IF NOT EXISTS `walking_address` (
  `Segment` int(11) NOT NULL,
  `StreetName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `StreetNumber` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `entrance_address`
--
ALTER TABLE `entrance_address`
  ADD PRIMARY KEY (`Segment`,`StreetName`);

--
-- Indexes for table `exit_address`
--
ALTER TABLE `exit_address`
  ADD PRIMARY KEY (`Segment`,`StreetName`);

--
-- Indexes for table `extra`
--
ALTER TABLE `extra`
  ADD PRIMARY KEY (`Segment`,`Type`);

--
-- Indexes for table `is_implemented`
--
ALTER TABLE `is_implemented`
  ADD PRIMARY KEY (`Type`,`Segment`);

--
-- Indexes for table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`Type`);

--
-- Indexes for table `park_lot`
--
ALTER TABLE `park_lot`
  ADD PRIMARY KEY (`Name`);

--
-- Indexes for table `payment_type`
--
ALTER TABLE `payment_type`
  ADD PRIMARY KEY (`Segment`,`OptionType`);

--
-- Indexes for table `segment`
--
ALTER TABLE `segment`
  ADD PRIMARY KEY (`Segment`);

--
-- Indexes for table `time_open`
--
ALTER TABLE `time_open`
  ADD PRIMARY KEY (`Segment`);

--
-- Indexes for table `updatelog`
--
ALTER TABLE `updatelog`
  ADD PRIMARY KEY (`Line`);

--
-- Indexes for table `walking_address`
--
ALTER TABLE `walking_address`
  ADD PRIMARY KEY (`Segment`,`StreetName`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `segment`
--
ALTER TABLE `segment`
  MODIFY `Segment` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=85;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
