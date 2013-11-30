-- phpMyAdmin SQL Dump
-- version 4.0.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 30, 2013 at 12:54 PM
-- Server version: 5.5.34-0ubuntu0.12.04.1
-- PHP Version: 5.3.10-1ubuntu3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=11501 ;

-- --------------------------------------------------------

--
-- Table structure for table `animations`
--

CREATE TABLE IF NOT EXISTS `animations` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) NOT NULL DEFAULT '0',
  `image` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `image` (`image`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=149 ;

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
  `lang` tinyint(4) NOT NULL DEFAULT '1',
  `removed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `lang` (`lang`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=2300 ;

-- --------------------------------------------------------

--
-- Table structure for table `async_ip`
--

CREATE TABLE IF NOT EXISTS `async_ip` (
  `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `action` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  KEY `ip` (`ip`),
  KEY `action` (`action`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=60821 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4789 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=88 ;

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
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `ip` (`ip`),
  KEY `time` (`time`),
  KEY `length` (`length`),
  KEY `lang` (`lang`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=8139 ;

-- --------------------------------------------------------

--
-- Table structure for table `blacklisted_sites`
--

CREATE TABLE IF NOT EXISTS `blacklisted_sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=101 ;

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
  KEY `pageid` (`pageid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=FIXED AUTO_INCREMENT=6212 ;

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
  `newlink` tinyint(1) NOT NULL DEFAULT '1',
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1848 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1722 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1079 ;

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
  PRIMARY KEY (`id`),
  KEY `owner` (`owner`),
  KEY `category_id` (`category_id`),
  KEY `members` (`members`),
  KEY `posts` (`posts`),
  KEY `owner_2` (`owner`,`title`),
  KEY `interest_id` (`interest_id`),
  KEY `lang` (`lang`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=515 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=55496 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=618 ;

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
  KEY `clan_id` (`clan_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=609 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=679313 ;

-- --------------------------------------------------------

--
-- Table structure for table `counter_ip`
--

CREATE TABLE IF NOT EXISTS `counter_ip` (
  `ip_addr` char(30) COLLATE utf8_unicode_ci NOT NULL,
  `last_hit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `site_id` tinyint(4) NOT NULL DEFAULT '1',
  KEY `site_id` (`site_id`),
  KEY `ip_addr` (`ip_addr`),
  KEY `last_hit` (`last_hit`),
  KEY `ip_addr_site_id` (`ip_addr`,`site_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=45244 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=87 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=510 ;

-- --------------------------------------------------------

--
-- Table structure for table `facts`
--

CREATE TABLE IF NOT EXISTS `facts` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=297 ;

-- --------------------------------------------------------

--
-- Table structure for table `facts_rs`
--

CREATE TABLE IF NOT EXISTS `facts_rs` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=218 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=27793 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=FIXED AUTO_INCREMENT=53199 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=533042 ;

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
-- Table structure for table `idb`
--

CREATE TABLE IF NOT EXISTS `idb` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `itemid` mediumint(6) NOT NULL,
  `strid` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `itemimg` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `oldimg` varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  `img` varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  `olddb` tinyint(1) NOT NULL DEFAULT '0',
  `oldrs` tinyint(1) NOT NULL DEFAULT '0',
  `evo` tinyint(1) NOT NULL DEFAULT '0',
  `upd` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `views` int(11) NOT NULL DEFAULT '0',
  `toP` tinyint(1) NOT NULL DEFAULT '0',
  `tolv` tinyint(1) NOT NULL DEFAULT '0',
  `asg` tinyint(1) NOT NULL DEFAULT '0',
  `atime` datetime NOT NULL,
  `auser` mediumint(6) NOT NULL,
  `etime` datetime NOT NULL,
  `euser` mediumint(6) NOT NULL,
  `ecount` smallint(3) NOT NULL,
  `apptime` datetime NOT NULL,
  `appuser` mediumint(6) NOT NULL,
  `examine` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `quest` tinyint(1) NOT NULL DEFAULT '0',
  `trade` tinyint(1) NOT NULL DEFAULT '0',
  `stacks` tinyint(1) NOT NULL DEFAULT '0',
  `equips` tinyint(1) NOT NULL DEFAULT '0',
  `members` tinyint(1) NOT NULL DEFAULT '0',
  `weight` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `alchlow` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `alchhigh` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `heals` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `lvlocation` text COLLATE utf8_unicode_ci NOT NULL,
  `lvuses` text COLLATE utf8_unicode_ci NOT NULL,
  `lvnotes` text COLLATE utf8_unicode_ci NOT NULL,
  `location` text COLLATE utf8_unicode_ci NOT NULL,
  `shops` text COLLATE utf8_unicode_ci NOT NULL,
  `madeby` text COLLATE utf8_unicode_ci NOT NULL,
  `usedin` text COLLATE utf8_unicode_ci NOT NULL,
  `uses` text COLLATE utf8_unicode_ci NOT NULL,
  `notes` text COLLATE utf8_unicode_ci NOT NULL,
  `droppedby` text COLLATE utf8_unicode_ci NOT NULL,
  `links` text COLLATE utf8_unicode_ci NOT NULL,
  `tags` text COLLATE utf8_unicode_ci NOT NULL,
  `bonuses` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `dmg` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `level` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `accuracy` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `armour` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `lifeb` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `prayb` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `style` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `speed` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `ammo` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `cmelee` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `cmage` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `crange` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `slot` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `ragil` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `ratt` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `rconst` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `rdef` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `rmage` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `rpray` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `rrange` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `rstr` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `rsumm` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item` (`item`),
  KEY `strid` (`strid`),
  KEY `olddb` (`olddb`),
  KEY `oldrs` (`oldrs`),
  KEY `asg` (`asg`),
  KEY `tolv` (`tolv`),
  KEY `auser` (`auser`),
  KEY `ecount` (`ecount`),
  KEY `atime` (`atime`),
  KEY `appuser` (`appuser`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10615 ;

-- --------------------------------------------------------

--
-- Table structure for table `idb_approve`
--

CREATE TABLE IF NOT EXISTS `idb_approve` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reset` tinyint(1) NOT NULL DEFAULT '0',
  `auser` mediumint(6) NOT NULL,
  `atime` datetime NOT NULL,
  `etime` datetime NOT NULL,
  `ecount` mediumint(9) NOT NULL,
  `app` tinyint(1) NOT NULL DEFAULT '0',
  `apptime` datetime NOT NULL,
  `appuser` mediumint(9) NOT NULL,
  `itemid` mediumint(6) NOT NULL,
  `lvlocation` text COLLATE utf8_unicode_ci NOT NULL,
  `lvuses` text COLLATE utf8_unicode_ci NOT NULL,
  `lvnotes` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1581 ;

-- --------------------------------------------------------

--
-- Table structure for table `idb_old_values`
--

CREATE TABLE IF NOT EXISTS `idb_old_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemid` mediumint(6) NOT NULL,
  `stab_att` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `stab_def` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `slash_att` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `slash_def` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `crush_att` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `crush_def` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `mage_att` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `mage_def` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `range_att` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `range_def` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `pray` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `summ` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `str` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `abs_melee` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `abs_range` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `abs_mage` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `mage_dmg` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `range_str` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2955 ;

-- --------------------------------------------------------

--
-- Table structure for table `idb_users`
--

CREATE TABLE IF NOT EXISTS `idb_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` mediumint(6) NOT NULL,
  `nick` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `mod` tinyint(1) NOT NULL DEFAULT '0',
  `allowed` tinyint(1) NOT NULL DEFAULT '1',
  `app` tinyint(1) NOT NULL DEFAULT '0',
  `auser` mediumint(6) NOT NULL DEFAULT '0',
  `atime` datetime NOT NULL,
  `items` mediumint(6) NOT NULL DEFAULT '0',
  `thisweek` datetime NOT NULL,
  `lastweek` datetime NOT NULL,
  `tcount` mediumint(9) NOT NULL DEFAULT '0',
  `lcount` mediumint(9) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=61 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=63852 ;

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
  `accessed` datetime NOT NULL,
  `views` int(11) NOT NULL DEFAULT '0',
  `file` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `path` (`path`,`file`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=32449 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10736 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=51568 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=50026 ;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `action` text COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `foreign_table` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `foreign_key` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=132575 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=206 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=26640 ;

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
  KEY `miniblog_list` (`parent`,`groupid`,`removed`,`lang`,`id`),
  KEY `author_vote` (`author`,`vote_value`) COMMENT 'for karma update',
  KEY `author_removed` (`author`,`removed`) COMMENT 'Karma update',
  KEY `author_removed_posts` (`author`,`removed`,`posts`) COMMENT 'Karma update'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3260541 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1715 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=633 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9572 ;

-- --------------------------------------------------------

--
-- Table structure for table `mta_chart`
--

CREATE TABLE IF NOT EXISTS `mta_chart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` datetime NOT NULL,
  `count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `time` (`time`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2885 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=302 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1714 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=636646 ;

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
  `rsclass` tinyint(1) NOT NULL DEFAULT '0',
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
  KEY `quest_chapter` (`rsclass`),
  KEY `category_bump` (`category`,`bump`),
  KEY `lang` (`lang`),
  KEY `rating_count` (`rating_count`),
  KEY `author_rating` (`author`,`rating_count`) COMMENT 'Karma update',
  KEY `author_category` (`author`,`category`) COMMENT 'Karma update',
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=61498 ;

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
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=37243 ;

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
  KEY `to_uid_is_read` (`to_uid`,`is_read`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=543479 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=944 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=146631 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=3800 ;

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
  KEY `deleted_by` (`deleted_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=202 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=63985 ;

-- --------------------------------------------------------

--
-- Table structure for table `rs_classes`
--

CREATE TABLE IF NOT EXISTS `rs_classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'not set',
  `img` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `order` smallint(3) NOT NULL DEFAULT '0',
  `cat` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '2',
  `klase` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `info` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `members` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cat` (`cat`),
  KEY `order` (`order`),
  KEY `cat_order` (`cat`,`order`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=113 ;

-- --------------------------------------------------------

--
-- Table structure for table `rs_help`
--

CREATE TABLE IF NOT EXISTS `rs_help` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `old` tinyint(1) NOT NULL DEFAULT '0',
  `page_id` mediumint(9) NOT NULL DEFAULT '0',
  `cat` smallint(6) NOT NULL,
  `title` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `strid` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `auth` mediumint(5) NOT NULL DEFAULT '0',
  `img` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `large_img` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(700) COLLATE utf8_unicode_ci NOT NULL,
  `location` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `skills` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `quests` varchar(400) COLLATE utf8_unicode_ci NOT NULL,
  `extra` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `date` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `year` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `p2p` tinyint(1) NOT NULL DEFAULT '1',
  `difficulty` tinyint(1) NOT NULL DEFAULT '0',
  `length` tinyint(1) NOT NULL,
  `storyline` int(11) NOT NULL DEFAULT '0',
  `order` tinyint(2) NOT NULL DEFAULT '0',
  `ready` tinyint(1) NOT NULL DEFAULT '0',
  `edit_user` mediumint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_id` (`page_id`),
  KEY `cat` (`cat`),
  KEY `year` (`year`),
  KEY `difficulty` (`difficulty`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=669 ;

-- --------------------------------------------------------

--
-- Table structure for table `rs_placeholders`
--

CREATE TABLE IF NOT EXISTS `rs_placeholders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cat` smallint(3) NOT NULL DEFAULT '0',
  `title` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `url2` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `author` tinyint(5) NOT NULL DEFAULT '115',
  KEY `id` (`id`),
  KEY `cat` (`cat`),
  KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=193 ;

-- --------------------------------------------------------

--
-- Table structure for table `rs_qskills`
--

CREATE TABLE IF NOT EXISTS `rs_qskills` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `skill` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `level` smallint(3) NOT NULL,
  `quest` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=152 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3786220 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=254 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Site specific user access level' AUTO_INCREMENT=33 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1034 ;

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
  KEY `lang` (`lang`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=FIXED AUTO_INCREMENT=350520 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=51694 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3700078 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `nick` varchar(26) CHARACTER SET utf8 COLLATE utf8_latvian_ci NOT NULL,
  `pwd` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `mail` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
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
  `rte` tinyint(1) NOT NULL DEFAULT '1',
  `showsig` tinyint(1) NOT NULL DEFAULT '1',
  `skype` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `web` varchar(127) COLLATE utf8_unicode_ci NOT NULL,
  `donated` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `about` text COLLATE utf8_unicode_ci NOT NULL,
  `rs_nick` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `ig_done` int(11) NOT NULL DEFAULT '0',
  `ig_points` int(11) NOT NULL DEFAULT '0',
  `custom_title` varchar(127) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `custom_title_paid` tinyint(1) NOT NULL DEFAULT '0',
  `yt_name` varchar(127) COLLATE utf8_unicode_ci NOT NULL,
  `yt_updated` int(11) NOT NULL,
  `twitter` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `credit` int(11) NOT NULL DEFAULT '0',
  `today` smallint(6) NOT NULL DEFAULT '0',
  `daily_first` smallint(4) NOT NULL DEFAULT '0',
  `daily_hangman` mediumint(9) NOT NULL DEFAULT '0',
  `last_action` varchar(255) CHARACTER SET utf8 COLLATE utf8_latvian_ci NOT NULL,
  `maximg` smallint(6) NOT NULL DEFAULT '100',
  `vote_others` mediumint(9) NOT NULL DEFAULT '0',
  `vote_total` mediumint(9) NOT NULL DEFAULT '0',
  `vote_today` smallint(6) NOT NULL DEFAULT '0',
  `seen_today` tinyint(1) NOT NULL DEFAULT '0',
  `days_in_row` mediumint(9) NOT NULL DEFAULT '0',
  `max_in_row` int(11) NOT NULL DEFAULT '0',
  `user_agent` text CHARACTER SET utf8 COLLATE utf8_latvian_ci NOT NULL,
  `draugiem_id` int(11) NOT NULL DEFAULT '0',
  `facebook_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `block_cs` tinyint(1) NOT NULL DEFAULT '0',
  `show_code` tinyint(1) NOT NULL DEFAULT '0',
  `show_lol` tinyint(1) NOT NULL DEFAULT '0',
  `show_rp` tinyint(1) NOT NULL DEFAULT '0',
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
  `reset_token` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `reset_time` datetime NOT NULL,
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
  KEY `pwd` (`pwd`),
  KEY `token` (`token`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT AUTO_INCREMENT=32468 ;

-- --------------------------------------------------------

--
-- Table structure for table `users_tmp`
--

CREATE TABLE IF NOT EXISTS `users_tmp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nick` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `mail` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `hash` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nick` (`nick`),
  UNIQUE KEY `hash` (`hash`),
  KEY `mail` (`mail`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=11822 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=FIXED AUTO_INCREMENT=1251023 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=171629 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=198 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1050 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6366 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=105819 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4249 ;

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `yt_id` (`yt_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=74080 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4637 ;

