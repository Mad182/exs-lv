-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 22, 2013 at 10:32 AM
-- Server version: 5.5.28
-- PHP Version: 5.3.10-1ubuntu3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `exs`
--

-- --------------------------------------------------------

--
-- Table structure for table `dofollow_sites`
--

CREATE TABLE IF NOT EXISTS `dofollow_sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=14 ;

--
-- Dumping data for table `dofollow_sites`
--

INSERT INTO `dofollow_sites` (`id`, `url`) VALUES
(1, 'akredits.lv'),
(5, 'coding.lv'),
(6, 'exs.lv'),
(3, 'ezgif.com'),
(4, 'gif-avatars.com'),
(11, 'grab.lv'),
(7, 'img.exs.lv'),
(12, 'irdarbs.lv'),
(2, 'lfs.lv'),
(9, 'lol.exs.lv'),
(10, 'openidea.lv'),
(13, 'otrapuse.lv'),
(8, 'rp.exs.lv');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
