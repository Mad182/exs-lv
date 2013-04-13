-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 10, 2013 at 11:14 AM
-- Server version: 5.5.28
-- PHP Version: 5.3.10-1ubuntu3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `exs`
--

-- --------------------------------------------------------

--
-- Table structure for table `blacklisted_sites`
--

CREATE TABLE IF NOT EXISTS `blacklisted_sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=40 ;

--
-- Dumping data for table `blacklisted_sites`
--

INSERT INTO `blacklisted_sites` (`id`, `url`) VALUES
(1, 'awsurveys.com'),
(2, 'runescapemembership4free.com'),
(3, 'runescapepingenerator.com'),
(4, 'freeps3.tv'),
(5, 'bigmoneyptc.com'),
(6, 'servces-runescape.com'),
(7, 'servics-runescape.com'),
(8, 'services-runecape.com'),
(9, 'servics-runecape.com'),
(10, 'services-runescape.ws'),
(11, 'secure.runscape.com'),
(12, 'runeskapes.t35.com'),
(13, 'piratebay.com'),
(14, 'cut.lv/Q7M/'),
(15, 'pelninaudu.info'),
(16, 'moneystrategy.info'),
(17, '2shared.com/file/r9M0b2fG/SWE_DDoS_227.html'),
(18, 'megaupload.com/?d=XQK8B0IO'),
(19, 'is.gd/9ivAOU'),
(20, 'rapidshare.com/files/1499757716/SwiftKit.exe'),
(21, 'dahoodbrothas.com'),
(22, 'free-steam-games.com'),
(23, 'freeminecraft.me'),
(24, 'steam.store-powred.com.nu'),
(25, 'failiem.lv/u/gwrtqbc'),
(26, 'tinyurl.com/d2c86wo'),
(27, 'epicfreeprizes.com'),
(28, 'freeitems.info.tm'),
(29, 'steam-games-free.com'),
(30, 'money-friends.net'),
(31, 'esimsecura.com'),
(32, 'zoneexp.com'),
(33, 'freeleaguecodes.net'),
(34, 'freesteamgifts.com'),
(35, 'mta.dga.lv'),
(36, 'cs.noob.lv'),
(37, 'rp.slime.lv'),
(38, 'skilled.ez.lv'),
(39, 'noob.lv');

