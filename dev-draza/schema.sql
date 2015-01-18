-- phpMyAdmin SQL Dump
-- version 4.0.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 18, 2015 at 01:49 PM
-- Server version: 10.0.15-MariaDB-1~precise
-- PHP Version: 5.3.10-1ubuntu3.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
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
-- Table structure for table `ajax_comments`
--

CREATE TABLE IF NOT EXISTS `ajax_comments` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) DEFAULT '0',
  `parent` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `time` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `parent` (`parent`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=11577 ;

-- --------------------------------------------------------

--
-- Table structure for table `android_logs`
--

CREATE TABLE IF NOT EXISTS `android_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_by` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `created_ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `animations`
--

CREATE TABLE IF NOT EXISTS `animations` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) NOT NULL DEFAULT '0',
  `image` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `added_by` mediumint(9) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `image` (`image`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=191 ;

-- --------------------------------------------------------

--
-- Table structure for table `approve`
--

CREATE TABLE IF NOT EXISTS `approve` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `category` smallint(6) NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `author` mediumint(9) NOT NULL,
  `date` datetime NOT NULL,
  `ip` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_wide` tinyint(1) NOT NULL DEFAULT '0',
  `lang` tinyint(4) NOT NULL DEFAULT '1',
  `removed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `lang` (`lang`),
  KEY `removed` (`removed`),
  KEY `author` (`author`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=2663 ;

-- --------------------------------------------------------

--
-- Table structure for table `autoawards`
--

CREATE TABLE IF NOT EXISTS `autoawards` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) NOT NULL,
  `award` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `importance` mediumint(9) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `importance` (`importance`),
  KEY `user_id_2` (`user_id`,`importance`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=72736 ;

-- --------------------------------------------------------

--
-- Table structure for table `avatar_history`
--

CREATE TABLE IF NOT EXISTS `avatar_history` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) NOT NULL DEFAULT '0',
  `avatar` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `changed` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8819 ;

-- --------------------------------------------------------

--
-- Table structure for table `awards`
--

CREATE TABLE IF NOT EXISTS `awards` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `user` mediumint(9) NOT NULL,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `icon` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `link` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `date` (`date`),
  KEY `user_date` (`user`,`date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=93 ;

-- --------------------------------------------------------

--
-- Table structure for table `banned`
--

CREATE TABLE IF NOT EXISTS `banned` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) NOT NULL,
  `reason` text COLLATE utf8_unicode_ci NOT NULL,
  `time` int(11) NOT NULL DEFAULT '0',
  `length` int(11) NOT NULL DEFAULT '1',
  `author` mediumint(9) NOT NULL,
  `ip` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `lang` smallint(6) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `ip` (`ip`),
  KEY `time` (`time`),
  KEY `length` (`length`),
  KEY `lang` (`lang`),
  KEY `active` (`active`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=9605 ;

-- --------------------------------------------------------

--
-- Table structure for table `blacklisted_sites`
--

CREATE TABLE IF NOT EXISTS `blacklisted_sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=162 ;

-- --------------------------------------------------------

--
-- Table structure for table `bookmarks`
--

CREATE TABLE IF NOT EXISTS `bookmarks` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `userid` mediumint(9) NOT NULL,
  `pageid` mediumint(9) NOT NULL,
  `foreign_table` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pages',
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `pageid` (`pageid`),
  KEY `foreign_table` (`foreign_table`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=FIXED AUTO_INCREMENT=6479 ;

-- --------------------------------------------------------

--
-- Table structure for table `cat`
--

CREATE TABLE IF NOT EXISTS `cat` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `textid` varchar(46) COLLATE utf8_unicode_ci NOT NULL,
  `lang` tinyint(4) NOT NULL DEFAULT '1',
  `title` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `intro` tinyint(1) NOT NULL DEFAULT '1',
  `module` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'list',
  `has_mvc` tinyint(1) NOT NULL DEFAULT '0',
  `showall` tinyint(1) NOT NULL DEFAULT '0',
  `isblog` mediumint(9) NOT NULL DEFAULT '0',
  `isforum` tinyint(1) NOT NULL DEFAULT '0',
  `mods_only` tinyint(1) NOT NULL DEFAULT '0',
  `mods_only_post` tinyint(1) NOT NULL DEFAULT '0',
  `alphabetical` tinyint(1) NOT NULL DEFAULT '0',
  `parent` mediumint(9) NOT NULL DEFAULT '0',
  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `tmpl` varchar(12) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'main',
  `options` varchar(256) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `stat_views` int(11) NOT NULL DEFAULT '0',
  `stat_topics` int(11) NOT NULL DEFAULT '0',
  `stat_com` int(11) NOT NULL DEFAULT '0',
  `speclevel` tinyint(4) NOT NULL DEFAULT '0',
  `secret` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `persona` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `icon` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ordered` smallint(6) NOT NULL DEFAULT '0',
  `sitemap` tinyint(1) NOT NULL DEFAULT '1',
  `status` enum('active','archived') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  KEY `isblog` (`isblog`),
  KEY `parent` (`parent`),
  KEY `isforum` (`isforum`),
  KEY `mods_only` (`mods_only`),
  KEY `persona` (`persona`),
  KEY `module` (`module`),
  KEY `forums` (`parent`,`module`,`id`),
  KEY `lang` (`lang`),
  KEY `textid_lang` (`textid`,`lang`),
  KEY `id_lang` (`id`,`lang`),
  KEY `textid` (`textid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=2125 ;

-- --------------------------------------------------------

--
-- Table structure for table `cat_ignore`
--

CREATE TABLE IF NOT EXISTS `cat_ignore` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `category_id` mediumint(9) NOT NULL DEFAULT '0',
  `user_id` mediumint(9) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `user_id` (`user_id`),
  KEY `user_category` (`user_id`,`category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1908 ;

-- --------------------------------------------------------

--
-- Table structure for table `cat_moderators`
--

CREATE TABLE IF NOT EXISTS `cat_moderators` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `category_id` mediumint(9) NOT NULL DEFAULT '0',
  `user_id` mediumint(9) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1080 ;

-- --------------------------------------------------------

--
-- Table structure for table `city`
--

CREATE TABLE IF NOT EXISTS `city` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `title` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1000 ;

-- --------------------------------------------------------

--
-- Table structure for table `clans`
--

CREATE TABLE IF NOT EXISTS `clans` (
  `id` smallint(4) NOT NULL AUTO_INCREMENT,
  `category_id` tinyint(3) NOT NULL DEFAULT '1',
  `title` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `text` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `avatar` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'none.png',
  `date_created` int(10) NOT NULL,
  `date_modified` int(10) NOT NULL,
  `owner` mediumint(6) NOT NULL,
  `owner_seenposts` mediumint(6) NOT NULL DEFAULT '0',
  `posts` mediumint(6) NOT NULL DEFAULT '0',
  `members` smallint(4) NOT NULL DEFAULT '0',
  `paid` tinyint(1) NOT NULL DEFAULT '0',
  `archived` tinyint(1) NOT NULL DEFAULT '0',
  `auto_approve` tinyint(1) NOT NULL DEFAULT '1',
  `public` tinyint(1) NOT NULL DEFAULT '1',
  `list` tinyint(1) NOT NULL DEFAULT '1',
  `lang` tinyint(4) NOT NULL DEFAULT '1',
  `hide_intro` tinyint(1) NOT NULL DEFAULT '0',
  `interest_id` smallint(6) NOT NULL DEFAULT '0',
  `disable_adsense` tinyint(1) NOT NULL DEFAULT '0',
  `top_ad` text COLLATE utf8_unicode_ci NOT NULL,
  `strid` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `persona` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `disable_vote` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `owner` (`owner`),
  KEY `category_id` (`category_id`),
  KEY `members` (`members`),
  KEY `posts` (`posts`),
  KEY `owner_2` (`owner`,`title`),
  KEY `interest_id` (`interest_id`),
  KEY `lang` (`lang`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=561 ;

-- --------------------------------------------------------

--
-- Table structure for table `clans_categories`
--

CREATE TABLE IF NOT EXISTS `clans_categories` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `importance` smallint(6) NOT NULL DEFAULT '50',
  PRIMARY KEY (`id`),
  KEY `importance` (`importance`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Table structure for table `clans_members`
--

CREATE TABLE IF NOT EXISTS `clans_members` (
  `id` mediumint(6) NOT NULL AUTO_INCREMENT,
  `user` mediumint(6) NOT NULL,
  `clan` smallint(4) NOT NULL,
  `approve` tinyint(1) NOT NULL DEFAULT '0',
  `date_added` int(10) NOT NULL DEFAULT '0',
  `moderator` tinyint(1) NOT NULL DEFAULT '0',
  `seenposts` mediumint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `clan` (`clan`),
  KEY `user` (`user`),
  KEY `date_added` (`date_added`),
  KEY `user_clan` (`user`,`clan`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=63832 ;

-- --------------------------------------------------------

--
-- Table structure for table `clans_paid`
--

CREATE TABLE IF NOT EXISTS `clans_paid` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `clan_id` mediumint(9) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `clan_id` (`clan_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=624 ;

-- --------------------------------------------------------

--
-- Table structure for table `clans_tabs`
--

CREATE TABLE IF NOT EXISTS `clans_tabs` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `slug` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `clan_id` mediumint(9) NOT NULL DEFAULT '0',
  `title` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `text` longtext COLLATE utf8_unicode_ci NOT NULL,
  `date_modified` int(11) NOT NULL DEFAULT '0',
  `public` tinyint(1) NOT NULL DEFAULT '0',
  `module` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `share` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `slug` (`slug`),
  KEY `clan_id` (`clan_id`),
  KEY `public` (`public`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=685 ;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `pid` mediumint(9) NOT NULL,
  `replies` smallint(6) NOT NULL DEFAULT '0',
  `parent` mediumint(9) NOT NULL DEFAULT '0',
  `author` mediumint(9) NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `vote_value` smallint(6) NOT NULL DEFAULT '0',
  `vote_users` text COLLATE utf8_unicode_ci NOT NULL,
  `mail` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `anon_nick` varchar(18) COLLATE utf8_unicode_ci NOT NULL,
  `edit_time` int(11) NOT NULL DEFAULT '0',
  `edit_user` mediumint(9) NOT NULL DEFAULT '0',
  `edit_times` smallint(6) NOT NULL DEFAULT '0',
  `removed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `author` (`author`),
  KEY `parent` (`parent`),
  KEY `pid_parent` (`pid`,`parent`,`removed`),
  KEY `author_removed` (`author`,`removed`),
  KEY `vote_value` (`vote_value`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=751108 ;

-- --------------------------------------------------------

--
-- Table structure for table `counter_ip`
--

CREATE TABLE IF NOT EXISTS `counter_ip` (
  `ip_addr` char(30) COLLATE utf8_unicode_ci NOT NULL,
  `last_hit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `site_id` tinyint(4) NOT NULL DEFAULT '1',
  UNIQUE KEY `ip_addr_site_id` (`ip_addr`,`site_id`),
  KEY `site_id` (`site_id`),
  KEY `ip_addr` (`ip_addr`),
  KEY `last_hit` (`last_hit`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dateks_offers`
--

CREATE TABLE IF NOT EXISTS `dateks_offers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `img` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `price` decimal(7,2) NOT NULL DEFAULT '0.00',
  `link` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `params` text COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `desas`
--

CREATE TABLE IF NOT EXISTS `desas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_1` mediumint(9) NOT NULL DEFAULT '0',
  `user_2` mediumint(9) NOT NULL DEFAULT '0',
  `data` text COLLATE utf8_unicode_ci NOT NULL,
  `winner` tinyint(4) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `waiting_for` tinyint(4) NOT NULL DEFAULT '1',
  `loser_seen` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_2` (`user_2`),
  KEY `status` (`status`),
  KEY `user_1` (`user_1`),
  KEY `winner` (`winner`),
  KEY `user_1_winner` (`user_1`,`winner`),
  KEY `user_2_winner` (`user_2`,`winner`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=49115 ;

-- --------------------------------------------------------

--
-- Table structure for table `desas_moves`
--

CREATE TABLE IF NOT EXISTS `desas_moves` (
  `user_id` mediumint(9) NOT NULL DEFAULT '0',
  `timestamp` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dofollow_sites`
--

CREATE TABLE IF NOT EXISTS `dofollow_sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=202 ;

-- --------------------------------------------------------

--
-- Table structure for table `drafts`
--

CREATE TABLE IF NOT EXISTS `drafts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `text` longtext COLLATE utf8_unicode_ci NOT NULL,
  `category_id` int(11) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2110 ;

-- --------------------------------------------------------

--
-- Table structure for table `draugiem_followers`
--

CREATE TABLE IF NOT EXISTS `draugiem_followers` (
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_blacklist`
--

CREATE TABLE IF NOT EXISTS `email_blacklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `domain` (`domain`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=731 ;

-- --------------------------------------------------------

--
-- Table structure for table `facts`
--

CREATE TABLE IF NOT EXISTS `facts` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=300 ;

-- --------------------------------------------------------

--
-- Table structure for table `failed_logins`
--

CREATE TABLE IF NOT EXISTS `failed_logins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `username` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=56334 ;

-- --------------------------------------------------------

--
-- Table structure for table `flash_games`
--

CREATE TABLE IF NOT EXISTS `flash_games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `thb_local` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `launch_date` date NOT NULL,
  `category` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `category_slug` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `flash_file` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `width` smallint(6) NOT NULL,
  `height` smallint(6) NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `description_en` text COLLATE utf8_unicode_ci NOT NULL,
  `instructions` text COLLATE utf8_unicode_ci NOT NULL,
  `instructions_en` text COLLATE utf8_unicode_ci NOT NULL,
  `developer_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `gameplays` mediumint(9) NOT NULL,
  `rating` float NOT NULL,
  `rating_count` mediumint(9) NOT NULL DEFAULT '1',
  `rating_users` longtext COLLATE utf8_unicode_ci NOT NULL,
  `lang` tinyint(4) NOT NULL DEFAULT '1',
  `translated` tinyint(1) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_slug` (`category_slug`),
  KEY `rating` (`rating`,`rating_count`),
  KEY `category` (`category`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=187757 ;

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE IF NOT EXISTS `friends` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `friend1` mediumint(9) NOT NULL,
  `friend2` mediumint(9) NOT NULL,
  `date` datetime NOT NULL,
  `date_confirmed` datetime NOT NULL,
  `confirmed` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `friend1` (`friend1`),
  KEY `friend2` (`friend2`),
  KEY `confirmed` (`confirmed`),
  KEY `date_confirmed` (`date_confirmed`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=FIXED AUTO_INCREMENT=59560 ;

-- --------------------------------------------------------

--
-- Table structure for table `galcom`
--

CREATE TABLE IF NOT EXISTS `galcom` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `bid` mediumint(9) NOT NULL,
  `author` mediumint(9) NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `ip` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `vote_value` smallint(6) NOT NULL DEFAULT '0',
  `vote_users` text COLLATE utf8_unicode_ci NOT NULL,
  `edit_time` int(11) NOT NULL DEFAULT '0',
  `edit_user` mediumint(9) NOT NULL DEFAULT '0',
  `edit_times` smallint(6) NOT NULL DEFAULT '0',
  `removed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `bid` (`bid`),
  KEY `author` (`author`),
  KEY `bid_existing` (`bid`,`removed`),
  KEY `author_removed` (`author`,`removed`),
  KEY `vote_value` (`vote_value`),
  KEY `author_vote` (`author`,`vote_value`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=542017 ;

-- --------------------------------------------------------

--
-- Table structure for table `gamescore`
--

CREATE TABLE IF NOT EXISTS `gamescore` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) NOT NULL DEFAULT '0',
  `game` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'snake',
  `score` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=246 ;

-- --------------------------------------------------------

--
-- Table structure for table `https_sites`
--

CREATE TABLE IF NOT EXISTS `https_sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=160 ;

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE IF NOT EXISTS `images` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `groupid` smallint(6) NOT NULL DEFAULT '0',
  `uid` mediumint(9) NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `thb` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `bump` datetime NOT NULL,
  `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `posts` mediumint(9) NOT NULL DEFAULT '0',
  `closed` tinyint(1) NOT NULL DEFAULT '0',
  `views` mediumint(9) NOT NULL,
  `rating` float NOT NULL DEFAULT '0',
  `rating_count` mediumint(9) NOT NULL DEFAULT '0',
  `rating_users` longtext COLLATE utf8_unicode_ci NOT NULL,
  `readby` longtext COLLATE utf8_unicode_ci NOT NULL,
  `lang` tinyint(4) NOT NULL DEFAULT '1',
  `interest_id` smallint(6) NOT NULL DEFAULT '0',
  `youtube_video` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `private` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `date` (`date`),
  KEY `groupid` (`groupid`),
  KEY `bump` (`bump`),
  KEY `interest_id` (`interest_id`),
  KEY `lang` (`lang`),
  KEY `uid_lang` (`uid`,`lang`),
  KEY `lang_interest` (`lang`,`interest_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=65740 ;

-- --------------------------------------------------------

--
-- Table structure for table `image_sites`
--

CREATE TABLE IF NOT EXISTS `image_sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=38 ;

-- --------------------------------------------------------

--
-- Table structure for table `imgupload`
--

CREATE TABLE IF NOT EXISTS `imgupload` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'img',
  `user_id` mediumint(9) NOT NULL DEFAULT '0',
  `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `created` datetime NOT NULL,
  `file` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `path` (`path`,`file`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=46066 ;

-- --------------------------------------------------------

--
-- Table structure for table `interests`
--

CREATE TABLE IF NOT EXISTS `interests` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `title` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `title_long` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `default` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Table structure for table `junk`
--

CREATE TABLE IF NOT EXISTS `junk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author` int(11) NOT NULL DEFAULT '0',
  `approved_by` int(11) NOT NULL DEFAULT '0',
  `image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `thb` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `bump` int(11) NOT NULL DEFAULT '0',
  `posts` int(11) NOT NULL DEFAULT '0',
  `closed` tinyint(1) NOT NULL DEFAULT '0',
  `closed_by` int(11) NOT NULL DEFAULT '0',
  `close_reason` text COLLATE utf8_unicode_ci NOT NULL,
  `edit_time` int(11) NOT NULL DEFAULT '0',
  `edit_user` int(11) NOT NULL DEFAULT '0',
  `edit_times` int(11) NOT NULL DEFAULT '0',
  `lang` tinyint(4) NOT NULL DEFAULT '1',
  `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `removed` tinyint(1) NOT NULL DEFAULT '0',
  `source` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `link` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `author` (`author`),
  KEY `bump` (`bump`),
  KEY `date` (`date`),
  KEY `posts` (`posts`),
  KEY `source` (`source`),
  KEY `removed` (`removed`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=15210 ;

-- --------------------------------------------------------

--
-- Table structure for table `junk_queue`
--

CREATE TABLE IF NOT EXISTS `junk_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image` varchar(512) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `title` text COLLATE utf8_unicode_ci NOT NULL,
  `link` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `source` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `approved` tinyint(2) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_id` mediumint(9) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `image` (`image`(333)),
  KEY `source` (`source`),
  KEY `approved` (`approved`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=83090 ;

-- --------------------------------------------------------

--
-- Table structure for table `junk_votes`
--

CREATE TABLE IF NOT EXISTS `junk_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `junk_id` mediumint(9) NOT NULL DEFAULT '0',
  `user_id` mediumint(9) NOT NULL DEFAULT '0',
  `value` tinyint(4) NOT NULL,
  `created` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `junk_id` (`junk_id`),
  KEY `user_id` (`user_id`),
  KEY `created` (`created`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=81483 ;

-- --------------------------------------------------------

--
-- Table structure for table `lastfm_tracks`
--

CREATE TABLE IF NOT EXISTS `lastfm_tracks` (
  `user_id` mediumint(9) DEFAULT NULL,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `mbid` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `date` int(11) DEFAULT NULL,
  `artist_name` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `artist_mbid` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `album_name` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `album_mbid` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `images_small` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `images_medium` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `images_large` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `created` datetime DEFAULT NULL,
  KEY `user_id` (`user_id`),
  KEY `date` (`date`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `action` text COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `foreign_table` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `foreign_key` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `created` (`created`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=254933 ;

-- --------------------------------------------------------

--
-- Table structure for table `lol_players`
--

CREATE TABLE IF NOT EXISTS `lol_players` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `lol_nick` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `server` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `errors` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=220 ;

-- --------------------------------------------------------

--
-- Table structure for table `lol_tracking`
--

CREATE TABLE IF NOT EXISTS `lol_tracking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) NOT NULL DEFAULT '0',
  `date` date NOT NULL,
  `lks` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `player_id` (`player_id`),
  KEY `date` (`date`),
  KEY `player_id_date` (`player_id`,`date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=31282 ;

-- --------------------------------------------------------

--
-- Table structure for table `lostmaps`
--

CREATE TABLE IF NOT EXISTS `lostmaps` (
  `title` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `hits` int(11) NOT NULL DEFAULT '0',
  `game` varchar(16) CHARACTER SET latin1 NOT NULL DEFAULT 'cs',
  `gt` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`title`),
  KEY `hits` (`hits`),
  KEY `game` (`game`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mc_users`
--

CREATE TABLE IF NOT EXISTS `mc_users` (
  `id` int(11) NOT NULL,
  `mc_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mc_id` (`mc_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `miniblog`
--

CREATE TABLE IF NOT EXISTS `miniblog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupid` smallint(4) NOT NULL DEFAULT '0',
  `author` mediumint(9) NOT NULL,
  `date` datetime NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `parent` int(11) NOT NULL DEFAULT '0',
  `type` enum('miniblog','junk') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'miniblog',
  `reply_to` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `removed` tinyint(1) NOT NULL DEFAULT '0',
  `bump` int(11) NOT NULL DEFAULT '0',
  `posts` smallint(4) NOT NULL DEFAULT '0',
  `closed` tinyint(1) NOT NULL DEFAULT '0',
  `closed_by` mediumint(9) NOT NULL DEFAULT '0',
  `close_reason` text COLLATE utf8_unicode_ci NOT NULL,
  `twitterid` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `twitteruser` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `vote_value` smallint(4) NOT NULL DEFAULT '0',
  `vote_users` text COLLATE utf8_unicode_ci NOT NULL,
  `edit_time` int(10) NOT NULL DEFAULT '0',
  `edit_user` mediumint(6) NOT NULL DEFAULT '0',
  `edit_times` smallint(6) NOT NULL DEFAULT '0',
  `lang` tinyint(4) NOT NULL DEFAULT '1',
  `private` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `parent` (`parent`),
  KEY `groupid` (`groupid`),
  KEY `twitterid` (`twitterid`),
  KEY `bump` (`bump`),
  KEY `author` (`author`),
  KEY `date` (`date`),
  KEY `parent_2` (`parent`,`bump`),
  KEY `lang` (`lang`),
  KEY `exists` (`parent`,`removed`,`id`),
  KEY `count_pager` (`author`,`groupid`,`removed`,`parent`),
  KEY `type` (`type`),
  KEY `author_vote` (`author`,`vote_value`) COMMENT 'for karma update',
  KEY `author_removed` (`author`,`removed`) COMMENT 'Karma update',
  KEY `author_removed_posts` (`author`,`removed`,`posts`) COMMENT 'Karma update',
  KEY `miniblog_list` (`parent`,`groupid`,`removed`,`lang`,`bump`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4142564 ;

-- --------------------------------------------------------

--
-- Table structure for table `movie_data`
--

CREATE TABLE IF NOT EXISTS `movie_data` (
  `page_id` int(11) NOT NULL,
  `title_lv` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `imdb_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `year` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `runtime` int(11) NOT NULL DEFAULT '0',
  `rating` float NOT NULL DEFAULT '0',
  `exs_likes` mediumint(9) NOT NULL DEFAULT '0',
  `exs_dislikes` mediumint(9) NOT NULL DEFAULT '0',
  `type` enum('movie','series','documentary','animation') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'movie',
  PRIMARY KEY (`page_id`),
  KEY `type` (`type`),
  KEY `exs_likes` (`exs_likes`),
  KEY `exs_dislikes` (`exs_dislikes`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `movie_genres`
--

CREATE TABLE IF NOT EXISTS `movie_genres` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` mediumint(9) NOT NULL DEFAULT '0',
  `genre` varchar(24) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_id_genre` (`page_id`,`genre`),
  KEY `page_id` (`page_id`),
  KEY `genre` (`genre`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1829 ;

-- --------------------------------------------------------

--
-- Table structure for table `movie_images`
--

CREATE TABLE IF NOT EXISTS `movie_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `main` tinyint(1) NOT NULL DEFAULT '0',
  `image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `thb` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`),
  KEY `main` (`main`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=676 ;

-- --------------------------------------------------------

--
-- Table structure for table `movie_ratings`
--

CREATE TABLE IF NOT EXISTS `movie_ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `rating` tinyint(4) NOT NULL DEFAULT '0',
  `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`),
  KEY `user_id` (`user_id`),
  KEY `rating` (`rating`),
  KEY `page_user` (`page_id`,`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=11161 ;

-- --------------------------------------------------------

--
-- Table structure for table `nick_history`
--

CREATE TABLE IF NOT EXISTS `nick_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) NOT NULL DEFAULT '0',
  `nick` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `changed` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=396 ;

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE IF NOT EXISTS `notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `weight` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2302 ;

-- --------------------------------------------------------

--
-- Table structure for table `notify`
--

CREATE TABLE IF NOT EXISTS `notify` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `foreign_key` int(11) NOT NULL DEFAULT '0',
  `bump` datetime NOT NULL,
  `url` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `info` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `lang` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `type` (`type`),
  KEY `foreign_key` (`foreign_key`),
  KEY `bump` (`bump`),
  KEY `user_id_2` (`user_id`,`bump`),
  KEY `lang` (`lang`),
  KEY `user_id_type` (`user_id`,`type`) COMMENT 'Karma update'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=914271 ;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `strid` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `textid` varchar(19) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Netiek izmantots. Priekš sadaerības ar vecajiem linkiem.',
  `category` smallint(6) NOT NULL,
  `text` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `intro` text COLLATE utf8_unicode_ci NOT NULL,
  `title` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `author` mediumint(6) NOT NULL,
  `date` datetime NOT NULL,
  `bump` datetime NOT NULL,
  `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sm_avatar` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `posts` mediumint(9) NOT NULL DEFAULT '0',
  `closed` tinyint(4) NOT NULL DEFAULT '0',
  `disable_close` tinyint(1) NOT NULL DEFAULT '0',
  `attach` tinyint(1) NOT NULL DEFAULT '0',
  `views` mediumint(9) NOT NULL DEFAULT '0',
  `rating` float NOT NULL DEFAULT '0',
  `rating_count` smallint(5) NOT NULL DEFAULT '0',
  `rating_users` longtext COLLATE utf8_unicode_ci NOT NULL,
  `edit_time` int(11) NOT NULL DEFAULT '0',
  `edit_user` mediumint(6) NOT NULL DEFAULT '0',
  `edit_times` smallint(6) NOT NULL DEFAULT '0',
  `readby` longtext COLLATE utf8_unicode_ci NOT NULL,
  `redirect` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `is_wide` tinyint(1) NOT NULL DEFAULT '0',
  `custom_ad` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `lang` tinyint(4) NOT NULL DEFAULT '1',
  `disable_emotions` tinyint(1) NOT NULL DEFAULT '0',
  `upd` tinyint(1) NOT NULL DEFAULT '0',
  `private` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `textid` (`textid`),
  UNIQUE KEY `strid` (`strid`),
  KEY `author` (`author`),
  KEY `category` (`category`),
  KEY `attach` (`attach`),
  KEY `bump` (`bump`),
  KEY `date` (`date`),
  KEY `views` (`views`),
  KEY `posts` (`posts`),
  KEY `quest_chapter` (`is_wide`),
  KEY `category_bump` (`category`,`bump`),
  KEY `lang` (`lang`),
  KEY `rating_count` (`rating_count`),
  KEY `author_rating` (`author`,`rating_count`) COMMENT 'Karma update',
  KEY `author_category` (`author`,`category`) COMMENT 'Karma update',
  KEY `category_id` (`category`,`id`),
  KEY `lang_bump` (`lang`,`bump`),
  KEY `movies_index` (`category`,`lang`,`date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT AUTO_INCREMENT=66112 ;

-- --------------------------------------------------------

--
-- Table structure for table `pages_ver`
--

CREATE TABLE IF NOT EXISTS `pages_ver` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` mediumint(9) NOT NULL,
  `time` int(11) NOT NULL,
  `title` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `text` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `nextmod` mediumint(9) NOT NULL DEFAULT '0',
  `is_wide` tinyint(1) NOT NULL DEFAULT '0',
  `category` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=44514 ;

-- --------------------------------------------------------

--
-- Table structure for table `players_online`
--

CREATE TABLE IF NOT EXISTS `players_online` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` datetime NOT NULL,
  `count` int(11) NOT NULL DEFAULT '0',
  `game` varchar(12) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'mta',
  PRIMARY KEY (`id`),
  KEY `time` (`time`),
  KEY `game` (`game`),
  KEY `game_2` (`game`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=64341 ;

-- --------------------------------------------------------

--
-- Table structure for table `player_likes`
--

CREATE TABLE IF NOT EXISTS `player_likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `video_id` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` mediumint(9) NOT NULL DEFAULT '0',
  `archived` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `playlist` mediumint(9) NOT NULL DEFAULT '1865',
  PRIMARY KEY (`id`),
  KEY `video_id` (`video_id`),
  KEY `user_id` (`user_id`),
  KEY `created` (`created`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8295 ;

-- --------------------------------------------------------

--
-- Table structure for table `pm`
--

CREATE TABLE IF NOT EXISTS `pm` (
  `id` mediumint(6) NOT NULL AUTO_INCREMENT,
  `from_uid` mediumint(6) NOT NULL,
  `to_uid` mediumint(6) NOT NULL,
  `date` datetime NOT NULL,
  `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `title` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `important` tinyint(1) NOT NULL DEFAULT '0',
  `imap_uid` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `imap_account` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `imap_name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `imap_email` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `to_uid` (`to_uid`),
  KEY `from_uid` (`from_uid`),
  KEY `date` (`date`),
  KEY `is_read` (`is_read`),
  KEY `to_uid_2` (`to_uid`,`date`),
  KEY `from_uid_2` (`from_uid`,`date`),
  KEY `to_uid_is_read` (`to_uid`,`is_read`),
  KEY `from_to` (`from_uid`,`to_uid`,`date`) COMMENT 'Sarakstes vēsturei'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=646432 ;

-- --------------------------------------------------------

--
-- Table structure for table `poll`
--

CREATE TABLE IF NOT EXISTS `poll` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `topic` mediumint(9) NOT NULL DEFAULT '0',
  `group` mediumint(9) NOT NULL DEFAULT '0',
  `lang` smallint(6) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `group` (`group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1143 ;

-- --------------------------------------------------------

--
-- Table structure for table `qgame_answers`
--

CREATE TABLE IF NOT EXISTS `qgame_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` mediumint(9) NOT NULL DEFAULT '0',
  `user_id` mediumint(9) NOT NULL DEFAULT '0',
  `answer` tinyint(1) NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `question_id` (`question_id`),
  KEY `answer` (`answer`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=146781 ;

-- --------------------------------------------------------

--
-- Table structure for table `qgame_questions`
--

CREATE TABLE IF NOT EXISTS `qgame_questions` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `slug` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` mediumint(9) NOT NULL DEFAULT '0',
  `question` text COLLATE utf8_unicode_ci NOT NULL,
  `answ0` text COLLATE utf8_unicode_ci NOT NULL,
  `answ1` text COLLATE utf8_unicode_ci NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1215 ;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE IF NOT EXISTS `questions` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `pid` int(4) NOT NULL,
  `question` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=4520 ;

-- --------------------------------------------------------

--
-- Table structure for table `random_texts`
--

CREATE TABLE IF NOT EXISTS `random_texts` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=275 ;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE IF NOT EXISTS `reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `removed` tinyint(1) NOT NULL DEFAULT '0',
  `site_id` tinyint(1) NOT NULL DEFAULT '1',
  `type` int(11) NOT NULL DEFAULT '0',
  `archived` tinyint(1) NOT NULL DEFAULT '0',
  `entry_id` int(11) NOT NULL DEFAULT '0',
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  `reported_content` text COLLATE utf8_unicode_ci NOT NULL,
  `created_by` int(11) NOT NULL DEFAULT '0',
  `updated_by` int(11) NOT NULL DEFAULT '0',
  `deleted_by` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `archived` (`archived`),
  KEY `deleted_by` (`deleted_by`),
  KEY `site_id` (`site_id`),
  KEY `entry_id` (`entry_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1320 ;

-- --------------------------------------------------------

--
-- Table structure for table `responses`
--

CREATE TABLE IF NOT EXISTS `responses` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `qid` int(8) NOT NULL,
  `user_id` mediumint(9) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `qid` (`qid`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=76949 ;

-- --------------------------------------------------------

--
-- Table structure for table `rs_facts`
--

CREATE TABLE IF NOT EXISTS `rs_facts` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `is_short` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_by` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `deleted_by` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `is_short` (`is_short`),
  KEY `deleted_by` (`deleted_by`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=237 ;

-- --------------------------------------------------------

--
-- Table structure for table `rs_mods`
--

CREATE TABLE IF NOT EXISTS `rs_mods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `user_nick` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `is_deleted` (`is_deleted`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `rs_news`
--

CREATE TABLE IF NOT EXISTS `rs_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hash_value` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `mb_id` int(11) NOT NULL DEFAULT '0',
  `news_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `news_date` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `news_category` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `news_description` text COLLATE utf8_unicode_ci NOT NULL,
  `news_link` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `has_image` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `deleted_by` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `hash_value` (`hash_value`),
  KEY `deleted_by` (`deleted_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=402 ;

-- --------------------------------------------------------

--
-- Table structure for table `rs_pages`
--

CREATE TABLE IF NOT EXISTS `rs_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_hidden` tinyint(4) NOT NULL DEFAULT '0',
  `is_old` tinyint(1) NOT NULL DEFAULT '0',
  `page_id` int(11) NOT NULL DEFAULT '0',
  `cat_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `members_only` tinyint(4) NOT NULL DEFAULT '0',
  `safe` tinyint(1) NOT NULL DEFAULT '1',
  `difficulty` smallint(6) NOT NULL DEFAULT '0',
  `length` smallint(6) NOT NULL DEFAULT '0',
  `age` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `voice_acted` tinyint(1) NOT NULL DEFAULT '0',
  `hero` int(11) NOT NULL DEFAULT '0',
  `image` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `starting_point` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `skills` text COLLATE utf8_unicode_ci NOT NULL,
  `quests` text COLLATE utf8_unicode_ci NOT NULL,
  `extra` text COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `date` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `year` int(11) NOT NULL DEFAULT '2001',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_by` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `deleted_by` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `is_hidden` (`is_hidden`),
  KEY `page_id` (`page_id`),
  KEY `deleted_by` (`deleted_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=337 ;

-- --------------------------------------------------------

--
-- Table structure for table `rs_series`
--

CREATE TABLE IF NOT EXISTS `rs_series` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'not set',
  `img` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `ordered_by` int(11) NOT NULL DEFAULT '0',
  `category` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `klase` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `info` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `members_only` tinyint(1) NOT NULL DEFAULT '0',
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cat` (`category`),
  KEY `order` (`ordered_by`),
  KEY `cat_order` (`category`,`ordered_by`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=127 ;

-- --------------------------------------------------------

--
-- Table structure for table `rs_series_quests`
--

CREATE TABLE IF NOT EXISTS `rs_series_quests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `series_id` int(11) NOT NULL DEFAULT '0',
  `rspages_id` int(11) NOT NULL DEFAULT '0',
  `ordered_by` int(11) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_by` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `deleted_by` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `series_id` (`series_id`,`rspages_id`,`deleted_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=178 ;

-- --------------------------------------------------------

--
-- Table structure for table `rs_skills`
--

CREATE TABLE IF NOT EXISTS `rs_skills` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_special` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `level` smallint(3) NOT NULL,
  `page_id` int(11) NOT NULL DEFAULT '0',
  `page_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=31 ;

-- --------------------------------------------------------

--
-- Table structure for table `serverlist`
--

CREATE TABLE IF NOT EXISTS `serverlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `port` int(11) NOT NULL DEFAULT '0',
  `status` text COLLATE utf8_unicode_ci NOT NULL,
  `updated` int(11) NOT NULL DEFAULT '0',
  `hits` int(11) NOT NULL DEFAULT '0',
  `type` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `online` tinyint(1) NOT NULL DEFAULT '0',
  `last_online` datetime NOT NULL,
  `fails` int(11) NOT NULL DEFAULT '0',
  `players` int(11) NOT NULL DEFAULT '0',
  `maxplayers` int(11) NOT NULL DEFAULT '20',
  `map` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `weight` int(11) NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`),
  KEY `players` (`players`),
  KEY `fails` (`fails`),
  KEY `weight` (`weight`),
  KEY `map` (`map`),
  KEY `online` (`online`),
  KEY `updated` (`updated`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1959 ;

-- --------------------------------------------------------

--
-- Table structure for table `serverlist_log`
--

CREATE TABLE IF NOT EXISTS `serverlist_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `server_id` mediumint(9) NOT NULL DEFAULT '0',
  `map` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `players` int(11) NOT NULL,
  `online` tinyint(1) NOT NULL DEFAULT '1',
  `when` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `server_id` (`server_id`),
  KEY `when` (`when`),
  KEY `map` (`map`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3793107 ;

-- --------------------------------------------------------

--
-- Table structure for table `sidelinks`
--

CREATE TABLE IF NOT EXISTS `sidelinks` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `category` smallint(6) NOT NULL,
  `title` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category` (`category`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=256 ;

-- --------------------------------------------------------

--
-- Table structure for table `site_admins`
--

CREATE TABLE IF NOT EXISTS `site_admins` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) NOT NULL,
  `site_id` smallint(6) NOT NULL,
  `level` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Site specific user access level' AUTO_INCREMENT=47 ;

-- --------------------------------------------------------

--
-- Table structure for table `site_storage`
--

CREATE TABLE IF NOT EXISTS `site_storage` (
  `key` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `lang` tinyint(1) NOT NULL DEFAULT '1',
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  KEY `key` (`key`),
  KEY `lang` (`lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sms`
--

CREATE TABLE IF NOT EXISTS `sms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` int(11) NOT NULL,
  `message` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `sender` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `message_id` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `data` longtext COLLATE utf8_unicode_ci NOT NULL,
  `suspended` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `message_id` (`message_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1144 ;

-- --------------------------------------------------------

--
-- Table structure for table `steam_player_info`
--

CREATE TABLE IF NOT EXISTS `steam_player_info` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `steamid` varchar(50) DEFAULT NULL,
  `communityvisibilitystate` int(11) DEFAULT NULL,
  `profilestate` int(11) DEFAULT NULL,
  `personaname` varchar(50) DEFAULT NULL,
  `lastlogoff` varchar(50) DEFAULT NULL,
  `profileurl` varchar(250) DEFAULT NULL,
  `avatar` text,
  `personastate` int(11) DEFAULT NULL,
  `realname` varchar(250) DEFAULT NULL,
  `primaryclanid` varchar(50) DEFAULT NULL,
  `timecreated` varchar(50) DEFAULT NULL,
  `personastateflags` int(11) DEFAULT NULL,
  `gameextrainfo` text,
  `gameid` varchar(50) DEFAULT NULL,
  `loccountrycode` varchar(10) DEFAULT NULL,
  `locstatecode` varchar(10) DEFAULT NULL,
  `loccityid` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `taged`
--

CREATE TABLE IF NOT EXISTS `taged` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `tag_id` mediumint(9) NOT NULL,
  `page_id` int(11) NOT NULL DEFAULT '0',
  `type` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0-page,1-image,2-miniblog,3-group',
  `lang` smallint(6) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`),
  KEY `tag_id` (`tag_id`),
  KEY `type` (`type`),
  KEY `page_id_type` (`page_id`,`type`),
  KEY `lang` (`lang`),
  KEY `tag_id_lang` (`tag_id`,`lang`) COMMENT 'Random makona generesanai'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=FIXED AUTO_INCREMENT=354669 ;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `special` tinyint(1) NOT NULL DEFAULT '0',
  `slug` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=53601 ;

-- --------------------------------------------------------

--
-- Table structure for table `twitter_followers`
--

CREATE TABLE IF NOT EXISTS `twitter_followers` (
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `userlogs`
--

CREATE TABLE IF NOT EXISTS `userlogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` int(11) NOT NULL DEFAULT '0',
  `user` mediumint(9) NOT NULL,
  `avatar` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `action` text COLLATE utf8_unicode_ci NOT NULL,
  `multi` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `lang` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `multi` (`multi`),
  KEY `lang` (`lang`),
  KEY `time` (`time`),
  KEY `user_lang` (`user`,`lang`),
  KEY `lang_time` (`lang`,`time`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4714140 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `nick` varchar(26) CHARACTER SET utf8 COLLATE utf8_latvian_ci NOT NULL,
  `password` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `pwd` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `mail` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `mail_confirmed` datetime DEFAULT NULL,
  `date` datetime NOT NULL,
  `lastseen` datetime NOT NULL,
  `city` mediumint(9) NOT NULL DEFAULT '0',
  `avatar` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `av_alt` tinyint(1) NOT NULL DEFAULT '0',
  `av_lock` tinyint(1) NOT NULL DEFAULT '0',
  `level` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0-user,1-admin,2-mod,3-writer,4-gamemaster',
  `posts` mediumint(9) NOT NULL DEFAULT '0',
  `karma` int(11) NOT NULL DEFAULT '0',
  `karma_bonus` smallint(6) NOT NULL DEFAULT '0',
  `lastip` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `birthday` date NOT NULL,
  `skin` tinyint(4) NOT NULL DEFAULT '3',
  `signature` text COLLATE utf8_unicode_ci NOT NULL,
  `allow_signature` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Vai lietotājam atļaut izmantot parakstu un par mani lapu',
  `rte` tinyint(1) NOT NULL DEFAULT '1',
  `showsig` tinyint(1) NOT NULL DEFAULT '1',
  `skype` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `web` varchar(127) COLLATE utf8_unicode_ci NOT NULL,
  `donated` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `about` text COLLATE utf8_unicode_ci NOT NULL,
  `rs_nick` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `ig_done` int(11) NOT NULL DEFAULT '0',
  `ig_points` int(11) NOT NULL DEFAULT '0',
  `custom_title` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `custom_title_paid` tinyint(1) NOT NULL DEFAULT '0',
  `yt_name` varchar(127) COLLATE utf8_unicode_ci NOT NULL,
  `yt_updated` int(11) NOT NULL,
  `twitter` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `credit` int(11) NOT NULL DEFAULT '0',
  `today` smallint(6) NOT NULL DEFAULT '0',
  `daily_first` smallint(4) NOT NULL DEFAULT '0',
  `daily_hangman` mediumint(9) NOT NULL DEFAULT '0',
  `last_action` varchar(255) CHARACTER SET utf8 COLLATE utf8_latvian_ci NOT NULL,
  `maximg` smallint(6) NOT NULL DEFAULT '200',
  `vote_others` mediumint(9) NOT NULL DEFAULT '0',
  `vote_total` mediumint(9) NOT NULL DEFAULT '0',
  `vote_today` smallint(6) NOT NULL DEFAULT '0',
  `seen_today` tinyint(1) NOT NULL DEFAULT '0',
  `days_in_row` mediumint(9) NOT NULL DEFAULT '0',
  `max_in_row` int(11) NOT NULL DEFAULT '0',
  `user_agent` text CHARACTER SET utf8 COLLATE utf8_latvian_ci NOT NULL,
  `draugiem_id` int(11) NOT NULL DEFAULT '0',
  `facebook_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `twitter_id` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `steam_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastfm_username` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastfm_sessionkey` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastfm_subscriber` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastfm_token` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastfm_updated` int(11) DEFAULT NULL,
  `lastfm_onlyfriends` tinyint(1) NOT NULL DEFAULT '1',
  `block_cs` tinyint(1) NOT NULL DEFAULT '0',
  `show_code` tinyint(1) NOT NULL DEFAULT '0',
  `show_lol` tinyint(1) NOT NULL DEFAULT '0',
  `show_rp` tinyint(1) NOT NULL DEFAULT '0',
  `show_rs` tinyint(1) NOT NULL DEFAULT '0',
  `persona` varchar(127) CHARACTER SET utf8 COLLATE utf8_latvian_ci NOT NULL,
  `gender` tinyint(1) NOT NULL DEFAULT '0',
  `mobile` tinyint(1) NOT NULL DEFAULT '0',
  `mobile_seen` tinyint(1) NOT NULL DEFAULT '0',
  `warn_count` mediumint(9) NOT NULL DEFAULT '0',
  `source_site` tinyint(4) NOT NULL DEFAULT '1',
  `decos` text COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `interest_quiz` tinyint(1) NOT NULL DEFAULT '0',
  `year_first` tinyint(1) NOT NULL DEFAULT '0',
  `rating` int(11) NOT NULL DEFAULT '0',
  `reset_token` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `reset_time` datetime DEFAULT NULL,
  `email_new` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_token` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_time` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nick` (`nick`),
  KEY `yt_name` (`yt_name`),
  KEY `today` (`today`),
  KEY `lastseen` (`lastseen`),
  KEY `karma` (`karma`),
  KEY `lastip` (`lastip`),
  KEY `draugiem_id` (`draugiem_id`),
  KEY `source_site` (`source_site`),
  KEY `token` (`token`),
  KEY `mail` (`mail`),
  KEY `lastfm_username` (`lastfm_username`),
  KEY `steam_id` (`steam_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT AUTO_INCREMENT=36340 ;

-- --------------------------------------------------------

--
-- Table structure for table `users_groups`
--

CREATE TABLE IF NOT EXISTS `users_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '`users_groups`.`id`',
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `created_by` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `deleted_by` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `parent_id` (`parent_id`),
  KEY `deleted_by` (`deleted_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=178 ;

-- --------------------------------------------------------

--
-- Table structure for table `users_tmp`
--

CREATE TABLE IF NOT EXISTS `users_tmp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nick` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `mail` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `hash` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nick` (`nick`),
  UNIQUE KEY `hash` (`hash`),
  KEY `mail` (`mail`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3145 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_interests`
--

CREATE TABLE IF NOT EXISTS `user_interests` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `interest_id` smallint(6) NOT NULL DEFAULT '0',
  KEY `user_id` (`user_id`),
  KEY `interest_id` (`interest_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `viewprofile`
--

CREATE TABLE IF NOT EXISTS `viewprofile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profile` mediumint(9) NOT NULL,
  `viewer` mediumint(9) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `profile` (`profile`),
  KEY `time` (`time`),
  KEY `viewer` (`viewer`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=FIXED AUTO_INCREMENT=1478677 ;

-- --------------------------------------------------------

--
-- Table structure for table `visits`
--

CREATE TABLE IF NOT EXISTS `visits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) NOT NULL DEFAULT '0',
  `site_id` tinyint(4) NOT NULL DEFAULT '1',
  `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `lastseen` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `site_id` (`site_id`),
  KEY `ip` (`ip`),
  KEY `user_lookup` (`user_id`,`ip`,`site_id`),
  KEY `lastseen` (`lastseen`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=319892 ;

-- --------------------------------------------------------

--
-- Table structure for table `votes13`
--

CREATE TABLE IF NOT EXISTS `votes13` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL,
  `ip` varchar(100) CHARACTER SET latin1 NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `age` int(11) NOT NULL DEFAULT '10',
  `length` int(11) NOT NULL DEFAULT '0',
  `maxcost` int(11) NOT NULL,
  `paybycard` int(11) NOT NULL DEFAULT '0',
  `distance` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=96 ;

-- --------------------------------------------------------

--
-- Table structure for table `votes13_dates`
--

CREATE TABLE IF NOT EXISTS `votes13_dates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL,
  `choice` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=198 ;

-- --------------------------------------------------------

--
-- Table structure for table `wallpapers`
--

CREATE TABLE IF NOT EXISTS `wallpapers` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `image` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `author` mediumint(9) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `date` (`date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1170 ;

-- --------------------------------------------------------

--
-- Table structure for table `warns`
--

CREATE TABLE IF NOT EXISTS `warns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) NOT NULL DEFAULT '0',
  `created_by` mediumint(9) NOT NULL DEFAULT '1',
  `edited_by` mediumint(9) NOT NULL DEFAULT '0',
  `removed_by` mediumint(9) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `removed` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `reason` text COLLATE utf8_unicode_ci NOT NULL,
  `remove_reason` text COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `site_id` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `active` (`active`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9444 ;

-- --------------------------------------------------------

--
-- Table structure for table `wg_games`
--

CREATE TABLE IF NOT EXISTS `wg_games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word_id` mediumint(9) NOT NULL,
  `correct` text COLLATE utf8_unicode_ci NOT NULL,
  `wrong` text COLLATE utf8_unicode_ci NOT NULL,
  `user_id` mediumint(9) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=124293 ;

-- --------------------------------------------------------

--
-- Table structure for table `wg_results`
--

CREATE TABLE IF NOT EXISTS `wg_results` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) NOT NULL,
  `date` date NOT NULL,
  `points` mediumint(9) NOT NULL,
  `games` mediumint(9) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `date` (`date`),
  KEY `points` (`points`),
  KEY `games` (`games`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4657 ;

-- --------------------------------------------------------

--
-- Table structure for table `wg_words`
--

CREATE TABLE IF NOT EXISTS `wg_words` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `word` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `hint` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=419 ;

-- --------------------------------------------------------

--
-- Table structure for table `ytlocal`
--

CREATE TABLE IF NOT EXISTS `ytlocal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `yt_id` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `yt_title` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  `yt_description` text COLLATE utf8_unicode_ci NOT NULL,
  `yt_restricted` tinyint(1) NOT NULL DEFAULT '0',
  `yt_time` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `yt_id` (`yt_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=65194 ;

-- --------------------------------------------------------

--
-- Table structure for table `ytrss`
--

CREATE TABLE IF NOT EXISTS `ytrss` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) NOT NULL,
  `url` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`),
  KEY `user_id` (`user_id`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3335 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
