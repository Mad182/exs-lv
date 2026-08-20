/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.18-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: exs
-- ------------------------------------------------------
-- Server version	10.11.18-MariaDB-0+deb12u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `animations`
--

DROP TABLE IF EXISTS `animations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `animations` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) NOT NULL DEFAULT 0,
  `image` varchar(64) NOT NULL,
  `added_by` mediumint(9) NOT NULL DEFAULT 1,
  `ip` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `image` (`image`)
) ENGINE=MyISAM AUTO_INCREMENT=280 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `api_logs`
--

DROP TABLE IF EXISTS `api_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `api_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `api_type` smallint(1) NOT NULL DEFAULT 0 COMMENT '0 - android; 1 - ios',
  `message` text NOT NULL,
  `url` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `created_ip` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=90528 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `approve`
--

DROP TABLE IF EXISTS `approve`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `approve` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `category` smallint(6) NOT NULL,
  `text` text NOT NULL,
  `title` varchar(128) NOT NULL,
  `author` mediumint(9) NOT NULL,
  `date` datetime NOT NULL,
  `ip` varchar(45) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `is_wide` tinyint(1) NOT NULL DEFAULT 0,
  `lang` tinyint(4) NOT NULL DEFAULT 1,
  `removed` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `lang` (`lang`),
  KEY `removed` (`removed`),
  KEY `author` (`author`)
) ENGINE=MyISAM AUTO_INCREMENT=3395 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `apps_countries`
--

DROP TABLE IF EXISTS `apps_countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `apps_countries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_code` varchar(2) NOT NULL DEFAULT '',
  `country_name` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=247 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `autoawards`
--

DROP TABLE IF EXISTS `autoawards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `autoawards` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) NOT NULL,
  `award` varchar(64) NOT NULL,
  `title` varchar(128) NOT NULL,
  `importance` mediumint(9) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_3` (`user_id`,`award`),
  KEY `user_id` (`user_id`),
  KEY `importance` (`importance`),
  KEY `user_id_2` (`user_id`,`importance`)
) ENGINE=MyISAM AUTO_INCREMENT=100228 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `autoawards_custom`
--

DROP TABLE IF EXISTS `autoawards_custom`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `autoawards_custom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `award_title` varchar(255) NOT NULL,
  `img_title` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `awards`
--

DROP TABLE IF EXISTS `awards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `awards` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `user` mediumint(9) NOT NULL,
  `title` varchar(128) NOT NULL,
  `icon` varchar(32) NOT NULL,
  `date` datetime NOT NULL,
  `link` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `date` (`date`),
  KEY `user_date` (`user`,`date`)
) ENGINE=MyISAM AUTO_INCREMENT=96 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bad_passwords`
--

DROP TABLE IF EXISTS `bad_passwords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bad_passwords` (
  `shit` varchar(16) DEFAULT NULL,
  KEY `shit` (`shit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `banned`
--

DROP TABLE IF EXISTS `banned`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `banned` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) NOT NULL,
  `reason` text NOT NULL,
  `time` int(11) NOT NULL DEFAULT 0,
  `length` int(11) NOT NULL DEFAULT 1,
  `author` mediumint(9) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `lang` smallint(6) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `ip` (`ip`),
  KEY `time` (`time`),
  KEY `length` (`length`),
  KEY `lang` (`lang`),
  KEY `active` (`active`)
) ENGINE=MyISAM AUTO_INCREMENT=11991 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blacklisted_sites`
--

DROP TABLE IF EXISTS `blacklisted_sites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `blacklisted_sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=MyISAM AUTO_INCREMENT=306 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bookmarks`
--

DROP TABLE IF EXISTS `bookmarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookmarks` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `userid` mediumint(9) NOT NULL,
  `pageid` mediumint(9) NOT NULL,
  `foreign_table` varchar(64) NOT NULL DEFAULT 'pages',
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `pageid` (`pageid`),
  KEY `foreign_table` (`foreign_table`)
) ENGINE=MyISAM AUTO_INCREMENT=6984 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=FIXED;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat`
--

DROP TABLE IF EXISTS `cat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cat` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `textid` varchar(46) NOT NULL,
  `lang` tinyint(4) NOT NULL DEFAULT 1,
  `title` varchar(64) NOT NULL,
  `intro` tinyint(1) NOT NULL DEFAULT 1,
  `module` varchar(32) NOT NULL DEFAULT 'list',
  `showall` tinyint(1) NOT NULL DEFAULT 0,
  `isblog` mediumint(9) NOT NULL DEFAULT 0,
  `isforum` tinyint(1) NOT NULL DEFAULT 0,
  `mods_only` tinyint(1) NOT NULL DEFAULT 0,
  `mods_only_post` tinyint(1) NOT NULL DEFAULT 0,
  `alphabetical` tinyint(1) NOT NULL DEFAULT 0,
  `parent` mediumint(9) NOT NULL DEFAULT 0,
  `content` longtext NOT NULL,
  `tmpl` varchar(12) NOT NULL DEFAULT 'main',
  `options` varchar(256) NOT NULL DEFAULT '',
  `stat_views` int(11) NOT NULL DEFAULT 0,
  `stat_topics` int(11) NOT NULL DEFAULT 0,
  `stat_com` int(11) NOT NULL DEFAULT 0,
  `speclevel` tinyint(4) NOT NULL DEFAULT 0,
  `secret` varchar(256) NOT NULL,
  `persona` varchar(64) NOT NULL DEFAULT '',
  `icon` varchar(255) NOT NULL,
  `ordered` smallint(6) NOT NULL DEFAULT 0,
  `sitemap` tinyint(1) NOT NULL DEFAULT 1,
  `status` enum('active','archived') NOT NULL DEFAULT 'active',
  `private` tinyint(1) NOT NULL DEFAULT 0,
  `noindex` tinyint(1) NOT NULL DEFAULT 0,
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
) ENGINE=MyISAM AUTO_INCREMENT=2524 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_ignore`
--

DROP TABLE IF EXISTS `cat_ignore`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cat_ignore` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `category_id` mediumint(9) NOT NULL DEFAULT 0,
  `user_id` mediumint(9) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `user_id` (`user_id`),
  KEY `user_category` (`user_id`,`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2232 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_moderators`
--

DROP TABLE IF EXISTS `cat_moderators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cat_moderators` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `category_id` mediumint(9) NOT NULL DEFAULT 0,
  `user_id` mediumint(9) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq` (`category_id`,`user_id`),
  KEY `category_id` (`category_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1087 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clans`
--

DROP TABLE IF EXISTS `clans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `clans` (
  `id` smallint(4) NOT NULL AUTO_INCREMENT,
  `category_id` tinyint(3) NOT NULL DEFAULT 1,
  `title` varchar(64) DEFAULT NULL,
  `title_form` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `text` mediumtext NOT NULL,
  `avatar` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'none.png',
  `firstpage_module` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `date_created` int(10) NOT NULL,
  `date_modified` int(10) NOT NULL,
  `last_activity` datetime DEFAULT NULL,
  `owner` mediumint(6) NOT NULL,
  `owner_seenposts` mediumint(6) NOT NULL DEFAULT 0,
  `posts` mediumint(6) NOT NULL DEFAULT 0,
  `members` smallint(4) NOT NULL DEFAULT 0,
  `archived` tinyint(1) NOT NULL DEFAULT 0,
  `auto_approve` tinyint(1) NOT NULL DEFAULT 1,
  `public` tinyint(1) NOT NULL DEFAULT 1,
  `list` tinyint(1) NOT NULL DEFAULT 1,
  `lang` tinyint(4) NOT NULL DEFAULT 1,
  `hide_intro` tinyint(1) NOT NULL DEFAULT 0,
  `interest_id` smallint(6) NOT NULL DEFAULT 0,
  `strid` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `persona` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `disable_vote` tinyint(1) NOT NULL DEFAULT 0,
  `noindex` tinyint(1) DEFAULT 1,
  `posts_today` smallint(6) NOT NULL DEFAULT 0,
  `paginator` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`),
  KEY `owner` (`owner`),
  KEY `category_id` (`category_id`),
  KEY `members` (`members`),
  KEY `posts` (`posts`),
  KEY `owner_2` (`owner`,`title`),
  KEY `interest_id` (`interest_id`),
  KEY `lang` (`lang`),
  KEY `posts_today` (`posts_today`),
  KEY `strid` (`strid`),
  KEY `last_activity` (`last_activity`)
) ENGINE=MyISAM AUTO_INCREMENT=670 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clans_categories`
--

DROP TABLE IF EXISTS `clans_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `clans_categories` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `importance` smallint(6) NOT NULL DEFAULT 50,
  PRIMARY KEY (`id`),
  KEY `importance` (`importance`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clans_members`
--

DROP TABLE IF EXISTS `clans_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `clans_members` (
  `id` mediumint(6) NOT NULL AUTO_INCREMENT,
  `user` mediumint(6) NOT NULL,
  `clan` smallint(4) NOT NULL,
  `approve` tinyint(1) NOT NULL DEFAULT 0,
  `date_added` int(10) NOT NULL DEFAULT 0,
  `moderator` tinyint(1) NOT NULL DEFAULT 0,
  `posts` mediumint(6) NOT NULL DEFAULT 0,
  `seenposts` mediumint(6) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_clan` (`user`,`clan`),
  KEY `clan` (`clan`),
  KEY `user` (`user`),
  KEY `date_added` (`date_added`),
  KEY `posts` (`posts`)
) ENGINE=MyISAM AUTO_INCREMENT=79720 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clans_tabs`
--

DROP TABLE IF EXISTS `clans_tabs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `clans_tabs` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `slug` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `clan_id` mediumint(9) NOT NULL DEFAULT 0,
  `title` varchar(32) DEFAULT NULL,
  `text` longtext DEFAULT NULL,
  `date_modified` int(11) NOT NULL DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `public` tinyint(1) NOT NULL DEFAULT 0,
  `module` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `module_data` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `pic_heavy` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `slug` (`slug`),
  KEY `clan_id` (`clan_id`),
  KEY `public` (`public`)
) ENGINE=MyISAM AUTO_INCREMENT=836 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clans_ver`
--

DROP TABLE IF EXISTS `clans_ver`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `clans_ver` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) DEFAULT NULL,
  `text` text DEFAULT NULL,
  `avatar` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `title` varchar(128) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `ip` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`),
  KEY `modified` (`modified`)
) ENGINE=InnoDB AUTO_INCREMENT=702 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `comments` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `pid` mediumint(9) NOT NULL,
  `replies` smallint(6) NOT NULL DEFAULT 0,
  `parent` mediumint(9) NOT NULL DEFAULT 0,
  `author` mediumint(9) NOT NULL,
  `text` text NOT NULL,
  `date` datetime NOT NULL,
  `ip` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `vote_value` smallint(6) NOT NULL DEFAULT 0,
  `vote_users` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `edit_time` int(11) NOT NULL DEFAULT 0,
  `edit_user` mediumint(9) NOT NULL DEFAULT 0,
  `edit_times` smallint(6) NOT NULL DEFAULT 0,
  `removed` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `author` (`author`),
  KEY `parent` (`parent`),
  KEY `pid_parent` (`pid`,`parent`,`removed`),
  KEY `author_removed` (`author`,`removed`),
  KEY `vote_value` (`vote_value`)
) ENGINE=MyISAM AUTO_INCREMENT=785140 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `counter_ip`
--

DROP TABLE IF EXISTS `counter_ip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `counter_ip` (
  `ip_addr` char(6) DEFAULT NULL,
  `last_hit` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `site_id` tinyint(4) NOT NULL DEFAULT 1,
  UNIQUE KEY `ip_addr_site_id` (`ip_addr`,`site_id`),
  KEY `site_id` (`site_id`),
  KEY `ip_addr` (`ip_addr`),
  KEY `last_hit` (`last_hit`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `countries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) DEFAULT NULL,
  `code` varchar(2) DEFAULT NULL,
  `in_europe` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `desas`
--

DROP TABLE IF EXISTS `desas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `desas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_1` mediumint(9) NOT NULL DEFAULT 0,
  `user_2` mediumint(9) NOT NULL DEFAULT 0,
  `data` text NOT NULL,
  `winner` tinyint(4) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `waiting_for` tinyint(4) NOT NULL DEFAULT 1,
  `loser_seen` tinyint(1) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_2` (`user_2`),
  KEY `status` (`status`),
  KEY `user_1` (`user_1`),
  KEY `winner` (`winner`),
  KEY `user_1_winner` (`user_1`,`winner`),
  KEY `user_2_winner` (`user_2`,`winner`)
) ENGINE=MyISAM AUTO_INCREMENT=52968 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `desas_moves`
--

DROP TABLE IF EXISTS `desas_moves`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `desas_moves` (
  `user_id` mediumint(9) NOT NULL DEFAULT 0,
  `timestamp` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dofollow_sites`
--

DROP TABLE IF EXISTS `dofollow_sites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `dofollow_sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB AUTO_INCREMENT=398 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `drafts`
--

DROP TABLE IF EXISTS `drafts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `drafts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `title` varchar(256) NOT NULL,
  `text` longtext NOT NULL,
  `category_id` int(11) NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2110 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_blacklist`
--

DROP TABLE IF EXISTS `email_blacklist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_blacklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `domain` (`domain`)
) ENGINE=MyISAM AUTO_INCREMENT=945 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `facts`
--

DROP TABLE IF EXISTS `facts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `facts` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=301 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `failed_logins`
--

DROP TABLE IF EXISTS `failed_logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_logins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `username` varchar(64) NOT NULL,
  `ip` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=139788 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `files` (
  `tmp_name` varchar(32) NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '',
  `created` datetime DEFAULT NULL,
  `size` varchar(16) DEFAULT NULL,
  `psize` varchar(32) DEFAULT NULL,
  `pnum` int(11) NOT NULL DEFAULT 1,
  `type` varchar(4) NOT NULL DEFAULT 'pdf',
  `ip` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`tmp_name`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `friends`
--

DROP TABLE IF EXISTS `friends`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `friends` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `friend1` mediumint(9) NOT NULL,
  `friend2` mediumint(9) NOT NULL,
  `date` datetime NOT NULL,
  `date_confirmed` datetime NOT NULL,
  `confirmed` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `friend1_2` (`friend1`,`friend2`),
  KEY `friend1` (`friend1`),
  KEY `friend2` (`friend2`),
  KEY `confirmed` (`confirmed`),
  KEY `date_confirmed` (`date_confirmed`)
) ENGINE=MyISAM AUTO_INCREMENT=64566 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=FIXED;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `galcom`
--

DROP TABLE IF EXISTS `galcom`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `galcom` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `bid` mediumint(9) NOT NULL,
  `author` mediumint(9) NOT NULL,
  `text` text NOT NULL,
  `date` datetime NOT NULL,
  `ip` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `vote_value` smallint(6) NOT NULL DEFAULT 0,
  `vote_users` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `edit_time` int(11) NOT NULL DEFAULT 0,
  `edit_user` mediumint(9) NOT NULL DEFAULT 0,
  `edit_times` smallint(6) NOT NULL DEFAULT 0,
  `removed` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `bid` (`bid`),
  KEY `author` (`author`),
  KEY `bid_existing` (`bid`,`removed`),
  KEY `author_removed` (`author`,`removed`),
  KEY `vote_value` (`vote_value`),
  KEY `author_vote` (`author`,`vote_value`)
) ENGINE=MyISAM AUTO_INCREMENT=546933 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gamescore`
--

DROP TABLE IF EXISTS `gamescore`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gamescore` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) NOT NULL DEFAULT 0,
  `game` varchar(32) NOT NULL DEFAULT 'snake',
  `score` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=399 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `https_sites`
--

DROP TABLE IF EXISTS `https_sites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `https_sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB AUTO_INCREMENT=3020 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `images` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `uid` mediumint(9) NOT NULL,
  `url` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `thb` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `text` text NOT NULL,
  `date` datetime NOT NULL,
  `bump` datetime NOT NULL,
  `ip` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `posts` mediumint(9) NOT NULL DEFAULT 0,
  `closed` tinyint(1) NOT NULL DEFAULT 0,
  `views` mediumint(9) NOT NULL,
  `rating` float NOT NULL DEFAULT 0,
  `rating_count` mediumint(9) NOT NULL DEFAULT 0,
  `rating_users` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `readby` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `lang` tinyint(4) NOT NULL DEFAULT 1,
  `interest_id` smallint(6) NOT NULL DEFAULT 0,
  `youtube_video` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `private` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `date` (`date`),
  KEY `bump` (`bump`),
  KEY `interest_id` (`interest_id`),
  KEY `lang` (`lang`),
  KEY `uid_lang` (`uid`,`lang`),
  KEY `lang_interest` (`lang`,`interest_id`),
  KEY `private` (`private`),
  KEY `url` (`url`),
  KEY `thb` (`thb`)
) ENGINE=MyISAM AUTO_INCREMENT=66940 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `imgupload`
--

DROP TABLE IF EXISTS `imgupload`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `imgupload` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(128) NOT NULL DEFAULT 'img',
  `user_id` mediumint(9) NOT NULL DEFAULT 0,
  `ip` varchar(45) NOT NULL DEFAULT '',
  `created` datetime NOT NULL,
  `file` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `path` (`path`,`file`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=61054 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `interests`
--

DROP TABLE IF EXISTS `interests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `interests` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  `title_long` varchar(255) NOT NULL,
  `default` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `junk`
--

DROP TABLE IF EXISTS `junk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `junk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author` int(11) NOT NULL DEFAULT 0,
  `approved_by` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `thb` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `text` text NOT NULL,
  `date` datetime DEFAULT NULL,
  `bump` int(11) NOT NULL DEFAULT 0,
  `posts` int(11) NOT NULL DEFAULT 0,
  `closed` tinyint(1) NOT NULL DEFAULT 0,
  `closed_by` int(11) NOT NULL DEFAULT 0,
  `close_reason` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `edit_time` int(11) NOT NULL DEFAULT 0,
  `edit_user` int(11) NOT NULL DEFAULT 0,
  `edit_times` int(11) NOT NULL DEFAULT 0,
  `lang` tinyint(4) NOT NULL DEFAULT 1,
  `ip` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `removed` tinyint(1) NOT NULL DEFAULT 0,
  `source` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `link` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `author` (`author`),
  KEY `bump` (`bump`),
  KEY `date` (`date`),
  KEY `posts` (`posts`),
  KEY `source` (`source`),
  KEY `removed` (`removed`),
  KEY `thb` (`thb`),
  KEY `image` (`image`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `junk_queue`
--

DROP TABLE IF EXISTS `junk_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `junk_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image` varchar(512) NOT NULL DEFAULT '',
  `title` text NOT NULL,
  `link` varchar(255) NOT NULL,
  `source` varchar(255) NOT NULL DEFAULT '',
  `approved` tinyint(2) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL,
  `ip` varchar(45) NOT NULL DEFAULT '',
  `user_id` mediumint(9) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `image` (`image`(333)),
  KEY `source` (`source`),
  KEY `approved` (`approved`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `junk_votes`
--

DROP TABLE IF EXISTS `junk_votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `junk_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `junk_id` mediumint(9) NOT NULL DEFAULT 0,
  `user_id` mediumint(9) NOT NULL DEFAULT 0,
  `value` tinyint(4) NOT NULL,
  `created` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `junk_id` (`junk_id`),
  KEY `user_id` (`user_id`),
  KEY `created` (`created`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lastfm_tracks`
--

DROP TABLE IF EXISTS `lastfm_tracks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lastfm_tracks` (
  `user_id` mediumint(9) DEFAULT NULL,
  `name` varchar(128) NOT NULL DEFAULT '',
  `mbid` varchar(128) NOT NULL DEFAULT '',
  `url` varchar(128) NOT NULL DEFAULT '',
  `date` int(11) DEFAULT NULL,
  `artist_name` varchar(128) NOT NULL DEFAULT '',
  `artist_mbid` varchar(128) NOT NULL DEFAULT '',
  `album_name` varchar(128) NOT NULL DEFAULT '',
  `album_mbid` varchar(128) NOT NULL DEFAULT '',
  `images_medium` varchar(128) NOT NULL DEFAULT '',
  `created` datetime DEFAULT NULL,
  KEY `user_id` (`user_id`),
  KEY `date` (`date`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `action` text NOT NULL,
  `created` datetime NOT NULL,
  `ip` varchar(45) NOT NULL,
  `foreign_table` varchar(64) NOT NULL,
  `foreign_key` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `created` (`created`)
) ENGINE=MyISAM AUTO_INCREMENT=537409 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lostmaps`
--

DROP TABLE IF EXISTS `lostmaps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lostmaps` (
  `title` varchar(256) NOT NULL,
  `hits` int(11) NOT NULL DEFAULT 0,
  `game` varchar(16) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'cs',
  `gt` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`title`),
  KEY `hits` (`hits`),
  KEY `game` (`game`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `miniblog`
--

DROP TABLE IF EXISTS `miniblog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `miniblog` (
  `pic_heavy` tinyint(1) NOT NULL DEFAULT 0,
  `hidden` tinyint(1) NOT NULL DEFAULT 0,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupid` smallint(4) NOT NULL DEFAULT 0,
  `author` mediumint(9) NOT NULL,
  `date` datetime NOT NULL,
  `text` text NOT NULL,
  `parent` int(11) NOT NULL DEFAULT 0,
  `type` enum('miniblog','junk') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'miniblog',
  `reply_to` int(11) NOT NULL DEFAULT 0,
  `ip` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `removed` tinyint(1) NOT NULL DEFAULT 0,
  `bump` int(11) NOT NULL DEFAULT 0,
  `posts` smallint(4) NOT NULL DEFAULT 0,
  `closed` tinyint(1) NOT NULL DEFAULT 0,
  `closed_by` mediumint(9) NOT NULL DEFAULT 0,
  `close_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `vote_value` smallint(4) NOT NULL DEFAULT 0,
  `vote_users` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `edit_time` int(10) NOT NULL DEFAULT 0,
  `edit_user` mediumint(6) NOT NULL DEFAULT 0,
  `edit_times` smallint(6) NOT NULL DEFAULT 0,
  `lang` tinyint(4) NOT NULL DEFAULT 1,
  `private` tinyint(1) NOT NULL DEFAULT 0,
  `noindex` tinyint(1) NOT NULL DEFAULT 0,
  `device` smallint(6) NOT NULL DEFAULT 0 COMMENT '0-web, 1-m.exs.lv, 2-android, 3-ios',
  PRIMARY KEY (`id`),
  KEY `parent` (`parent`),
  KEY `groupid` (`groupid`),
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
) ENGINE=InnoDB AUTO_INCREMENT=5215598 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `miniblog_ver`
--

DROP TABLE IF EXISTS `miniblog_ver`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `miniblog_ver` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mbid` int(11) DEFAULT NULL,
  `text` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mbid` (`mbid`),
  KEY `user_id` (`user_id`),
  KEY `modified` (`modified`)
) ENGINE=InnoDB AUTO_INCREMENT=80872 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `movie_data`
--

DROP TABLE IF EXISTS `movie_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `movie_data` (
  `page_id` int(11) NOT NULL,
  `title_lv` varchar(255) NOT NULL DEFAULT '',
  `imdb_id` varchar(255) NOT NULL DEFAULT '',
  `year` varchar(4) NOT NULL,
  `runtime` int(11) NOT NULL DEFAULT 0,
  `rating` float NOT NULL DEFAULT 0,
  `exs_likes` mediumint(9) NOT NULL DEFAULT 0,
  `exs_dislikes` mediumint(9) NOT NULL DEFAULT 0,
  `type` enum('movie','series','documentary','animation') NOT NULL DEFAULT 'movie',
  PRIMARY KEY (`page_id`),
  KEY `type` (`type`),
  KEY `exs_likes` (`exs_likes`),
  KEY `exs_dislikes` (`exs_dislikes`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `movie_genres`
--

DROP TABLE IF EXISTS `movie_genres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `movie_genres` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` mediumint(9) NOT NULL DEFAULT 0,
  `genre` varchar(24) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_id_genre` (`page_id`,`genre`),
  KEY `page_id` (`page_id`),
  KEY `genre` (`genre`)
) ENGINE=MyISAM AUTO_INCREMENT=1863 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `movie_images`
--

DROP TABLE IF EXISTS `movie_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `movie_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `main` tinyint(1) NOT NULL DEFAULT 0,
  `image` varchar(255) NOT NULL,
  `thb` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`),
  KEY `main` (`main`)
) ENGINE=MyISAM AUTO_INCREMENT=687 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `movie_ratings`
--

DROP TABLE IF EXISTS `movie_ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `movie_ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `rating` tinyint(4) NOT NULL DEFAULT 0,
  `ip` varchar(45) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`),
  KEY `user_id` (`user_id`),
  KEY `rating` (`rating`),
  KEY `page_user` (`page_id`,`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12271 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nick_history`
--

DROP TABLE IF EXISTS `nick_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `nick_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) NOT NULL DEFAULT 0,
  `nick` varchar(100) NOT NULL DEFAULT '',
  `changed` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=544 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS `notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `title` varchar(32) NOT NULL,
  `content` longtext NOT NULL,
  `ip` varchar(45) NOT NULL DEFAULT '',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `weight` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `weight` (`weight`)
) ENGINE=MyISAM AUTO_INCREMENT=3017 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notify`
--

DROP TABLE IF EXISTS `notify`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notify` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) NOT NULL DEFAULT 0,
  `type` tinyint(4) NOT NULL DEFAULT 0,
  `foreign_key` int(11) NOT NULL DEFAULT 0,
  `bump` datetime DEFAULT NULL,
  `url` varchar(256) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `info` varchar(128) DEFAULT NULL,
  `lang` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `type` (`type`),
  KEY `foreign_key` (`foreign_key`),
  KEY `bump` (`bump`),
  KEY `user_id_2` (`user_id`,`bump`),
  KEY `lang` (`lang`),
  KEY `user_id_type` (`user_id`,`type`) COMMENT 'Karma update'
) ENGINE=MyISAM AUTO_INCREMENT=1261774 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `strid` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `textid` varchar(19) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'Netiek izmantots. Priekš sadaerības ar vecajiem linkiem.',
  `category` smallint(6) NOT NULL,
  `text` text NOT NULL,
  `intro` text NOT NULL,
  `title` tinytext NOT NULL,
  `meta_description` tinytext NOT NULL DEFAULT '\'\'',
  `custom_include` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `custom_param` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `author` mediumint(6) NOT NULL,
  `date` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  `bump` datetime NOT NULL,
  `ip` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `sm_avatar` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `posts` mediumint(9) NOT NULL DEFAULT 0,
  `closed` tinyint(4) NOT NULL DEFAULT 0,
  `disable_close` tinyint(1) NOT NULL DEFAULT 0,
  `attach` tinyint(1) NOT NULL DEFAULT 0,
  `views` mediumint(9) NOT NULL DEFAULT 0,
  `rating` float NOT NULL DEFAULT 0,
  `rating_count` smallint(5) NOT NULL DEFAULT 0,
  `rating_users` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `edit_time` int(11) NOT NULL DEFAULT 0,
  `edit_user` mediumint(6) NOT NULL DEFAULT 0,
  `edit_times` smallint(6) NOT NULL DEFAULT 0,
  `readby` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `redirect` varchar(256) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `is_wide` tinyint(1) NOT NULL DEFAULT 0,
  `lang` tinyint(4) NOT NULL DEFAULT 1,
  `disable_emotions` tinyint(1) NOT NULL DEFAULT 0,
  `upd` tinyint(1) NOT NULL DEFAULT 0,
  `private` tinyint(1) NOT NULL DEFAULT 0,
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
  KEY `movies_index` (`category`,`lang`,`date`),
  KEY `custom_param` (`custom_param`)
) ENGINE=MyISAM AUTO_INCREMENT=69761 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pages_ver`
--

DROP TABLE IF EXISTS `pages_ver`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages_ver` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` mediumint(9) NOT NULL,
  `time` int(11) NOT NULL,
  `title` varchar(256) NOT NULL,
  `text` mediumtext NOT NULL,
  `nextmod` mediumint(9) NOT NULL DEFAULT 0,
  `is_wide` tinyint(1) NOT NULL DEFAULT 0,
  `category` int(11) NOT NULL DEFAULT 0,
  `ip` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `time` (`time`)
) ENGINE=MyISAM AUTO_INCREMENT=49663 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pm`
--

DROP TABLE IF EXISTS `pm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pm` (
  `id` mediumint(6) NOT NULL AUTO_INCREMENT,
  `from_uid` mediumint(6) NOT NULL,
  `to_uid` mediumint(6) NOT NULL,
  `date` datetime NOT NULL,
  `ip` varchar(45) NOT NULL DEFAULT '',
  `title` tinytext NOT NULL,
  `text` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `device` smallint(6) NOT NULL DEFAULT 0 COMMENT '0-web, 1-m.exs.lv, 2-android, 3-ios',
  `important` tinyint(1) NOT NULL DEFAULT 0,
  `imap_uid` varchar(128) NOT NULL,
  `imap_account` varchar(128) NOT NULL,
  `imap_name` varchar(128) NOT NULL,
  `imap_email` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `to_uid` (`to_uid`),
  KEY `from_uid` (`from_uid`),
  KEY `date` (`date`),
  KEY `is_read` (`is_read`),
  KEY `to_uid_2` (`to_uid`,`date`),
  KEY `from_uid_2` (`from_uid`,`date`),
  KEY `to_uid_is_read` (`to_uid`,`is_read`),
  KEY `from_to` (`from_uid`,`to_uid`,`date`) COMMENT 'Sarakstes vēsturei'
) ENGINE=MyISAM AUTO_INCREMENT=809653 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `poll`
--

DROP TABLE IF EXISTS `poll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `poll` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `topic` mediumint(9) NOT NULL DEFAULT 0,
  `group` mediumint(9) NOT NULL DEFAULT 0,
  `lang` smallint(6) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `group` (`group`)
) ENGINE=MyISAM AUTO_INCREMENT=1289 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `questions`
--

DROP TABLE IF EXISTS `questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `questions` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `pid` int(4) NOT NULL,
  `question` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=5217 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `removed` tinyint(1) NOT NULL DEFAULT 0,
  `site_id` tinyint(1) NOT NULL DEFAULT 1,
  `type` int(11) NOT NULL DEFAULT 0,
  `archived` tinyint(1) NOT NULL DEFAULT 0,
  `entry_id` int(11) NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `reported_content` text NOT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `updated_by` int(11) NOT NULL DEFAULT 0,
  `deleted_by` int(11) NOT NULL DEFAULT 0,
  `created_at` int(11) NOT NULL DEFAULT 0,
  `updated_at` int(11) NOT NULL DEFAULT 0,
  `deleted_at` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `archived` (`archived`),
  KEY `deleted_by` (`deleted_by`),
  KEY `site_id` (`site_id`),
  KEY `entry_id` (`entry_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2765 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `responses`
--

DROP TABLE IF EXISTS `responses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `responses` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `qid` int(8) NOT NULL,
  `user_id` mediumint(9) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `qid` (`qid`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=93559 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `roulette_balance`
--

DROP TABLE IF EXISTS `roulette_balance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `roulette_balance` (
  `user_id` int(11) NOT NULL,
  `gold` int(11) NOT NULL DEFAULT 100,
  `max_gold` int(11) NOT NULL DEFAULT 100,
  `last_reset_date` date NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `gold` (`gold`),
  KEY `max_gold` (`max_gold`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rs_facts`
--

DROP TABLE IF EXISTS `rs_facts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rs_facts` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  `is_short` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` int(11) NOT NULL DEFAULT 0,
  `updated_by` int(11) NOT NULL DEFAULT 0,
  `updated_at` int(11) NOT NULL DEFAULT 0,
  `deleted_by` int(11) NOT NULL DEFAULT 0,
  `deleted_at` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `is_short` (`is_short`),
  KEY `deleted_by` (`deleted_by`)
) ENGINE=MyISAM AUTO_INCREMENT=238 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rs_mods`
--

DROP TABLE IF EXISTS `rs_mods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rs_mods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `user_nick` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `is_deleted` (`is_deleted`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rs_news`
--

DROP TABLE IF EXISTS `rs_news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rs_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hash_value` varchar(32) NOT NULL,
  `mb_id` int(11) NOT NULL DEFAULT 0,
  `is_oldschool` tinyint(1) NOT NULL DEFAULT 0,
  `news_title` varchar(255) NOT NULL,
  `news_date` varchar(255) NOT NULL,
  `news_category` varchar(255) NOT NULL,
  `news_description` text NOT NULL,
  `news_link` varchar(255) NOT NULL,
  `has_image` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` int(11) NOT NULL DEFAULT 0,
  `deleted_by` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `hash_value` (`hash_value`),
  KEY `deleted_by` (`deleted_by`)
) ENGINE=InnoDB AUTO_INCREMENT=1846 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rs_pages`
--

DROP TABLE IF EXISTS `rs_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rs_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_hidden` tinyint(4) NOT NULL DEFAULT 0,
  `is_old` tinyint(1) NOT NULL DEFAULT 0,
  `page_id` int(11) NOT NULL DEFAULT 0,
  `cat_id` int(11) NOT NULL DEFAULT 0,
  `title` varchar(256) NOT NULL,
  `members_only` tinyint(4) NOT NULL DEFAULT 0,
  `safe` tinyint(1) NOT NULL DEFAULT 1,
  `difficulty` smallint(6) NOT NULL DEFAULT 0,
  `length` smallint(6) NOT NULL DEFAULT 0,
  `age` varchar(256) NOT NULL,
  `voice_acted` tinyint(1) NOT NULL DEFAULT 0,
  `hero` int(11) NOT NULL DEFAULT 0,
  `image` varchar(256) NOT NULL,
  `starting_point` varchar(256) NOT NULL,
  `skills` text NOT NULL,
  `quests` text NOT NULL,
  `extra` text NOT NULL,
  `description` text NOT NULL,
  `date` varchar(256) NOT NULL,
  `year` int(11) NOT NULL DEFAULT 2001,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` int(11) NOT NULL DEFAULT 0,
  `updated_by` int(11) NOT NULL DEFAULT 0,
  `updated_at` int(11) NOT NULL DEFAULT 0,
  `deleted_by` int(11) NOT NULL DEFAULT 0,
  `deleted_at` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `is_hidden` (`is_hidden`),
  KEY `page_id` (`page_id`),
  KEY `deleted_by` (`deleted_by`)
) ENGINE=InnoDB AUTO_INCREMENT=346 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rs_series`
--

DROP TABLE IF EXISTS `rs_series`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rs_series` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT 'not set',
  `img` varchar(50) NOT NULL,
  `ordered_by` int(11) NOT NULL DEFAULT 0,
  `category` varchar(15) NOT NULL,
  `klase` varchar(10) NOT NULL,
  `info` varchar(300) NOT NULL,
  `members_only` tinyint(1) NOT NULL DEFAULT 0,
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `cat` (`category`),
  KEY `order` (`ordered_by`),
  KEY `cat_order` (`category`,`ordered_by`)
) ENGINE=MyISAM AUTO_INCREMENT=128 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rs_series_quests`
--

DROP TABLE IF EXISTS `rs_series_quests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rs_series_quests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `series_id` int(11) NOT NULL DEFAULT 0,
  `rspages_id` int(11) NOT NULL DEFAULT 0,
  `ordered_by` int(11) NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` int(11) NOT NULL DEFAULT 0,
  `updated_by` int(11) NOT NULL DEFAULT 0,
  `updated_at` int(11) NOT NULL DEFAULT 0,
  `deleted_by` int(11) NOT NULL DEFAULT 0,
  `deleted_at` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `series_id` (`series_id`,`rspages_id`,`deleted_by`)
) ENGINE=InnoDB AUTO_INCREMENT=178 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rs_skills`
--

DROP TABLE IF EXISTS `rs_skills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rs_skills` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_special` tinyint(1) NOT NULL DEFAULT 0,
  `title` varchar(256) NOT NULL,
  `level` smallint(3) NOT NULL,
  `page_id` int(11) NOT NULL DEFAULT 0,
  `page_title` varchar(255) NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT 0,
  `updated_at` int(11) NOT NULL DEFAULT 0,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `serverlist`
--

DROP TABLE IF EXISTS `serverlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `serverlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(128) NOT NULL,
  `address` varchar(64) NOT NULL,
  `port` int(11) NOT NULL DEFAULT 0,
  `status` text NOT NULL,
  `updated` int(11) NOT NULL DEFAULT 0,
  `hits` int(11) NOT NULL DEFAULT 0,
  `type` varchar(64) NOT NULL,
  `online` tinyint(1) NOT NULL DEFAULT 0,
  `last_online` datetime NOT NULL,
  `fails` int(11) NOT NULL DEFAULT 0,
  `players` int(11) NOT NULL DEFAULT 0,
  `maxplayers` int(11) NOT NULL DEFAULT 20,
  `map` varchar(32) NOT NULL,
  `title` varchar(128) NOT NULL,
  `weight` int(11) NOT NULL DEFAULT 100,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`),
  KEY `players` (`players`),
  KEY `fails` (`fails`),
  KEY `weight` (`weight`),
  KEY `map` (`map`),
  KEY `online` (`online`),
  KEY `updated` (`updated`)
) ENGINE=MyISAM AUTO_INCREMENT=1959 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sidelinks`
--

DROP TABLE IF EXISTS `sidelinks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sidelinks` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `category` smallint(6) NOT NULL,
  `title` varchar(64) NOT NULL,
  `url` varchar(256) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category` (`category`)
) ENGINE=MyISAM AUTO_INCREMENT=258 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `site_admins`
--

DROP TABLE IF EXISTS `site_admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_admins` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) NOT NULL,
  `site_id` smallint(6) NOT NULL,
  `level` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci COMMENT='Site specific user access level';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `site_storage`
--

DROP TABLE IF EXISTS `site_storage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_storage` (
  `key` varchar(64) NOT NULL DEFAULT '',
  `lang` tinyint(1) NOT NULL DEFAULT 1,
  `value` text NOT NULL,
  KEY `key` (`key`),
  KEY `lang` (`lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sms`
--

DROP TABLE IF EXISTS `sms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` int(11) NOT NULL,
  `message` varchar(128) NOT NULL,
  `sender` varchar(128) NOT NULL,
  `message_id` varchar(128) NOT NULL,
  `data` longtext NOT NULL,
  `suspended` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `message_id` (`message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `steam_player_info`
--

DROP TABLE IF EXISTS `steam_player_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `steam_player_info` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `steamid` varchar(50) DEFAULT NULL,
  `communityvisibilitystate` int(11) DEFAULT NULL,
  `profilestate` int(11) DEFAULT NULL,
  `personaname` varchar(50) DEFAULT NULL,
  `lastlogoff` varchar(50) DEFAULT NULL,
  `profileurl` varchar(250) DEFAULT NULL,
  `avatar` text DEFAULT NULL,
  `personastate` int(11) DEFAULT NULL,
  `realname` varchar(250) DEFAULT NULL,
  `primaryclanid` varchar(50) DEFAULT NULL,
  `timecreated` varchar(50) DEFAULT NULL,
  `personastateflags` int(11) DEFAULT NULL,
  `gameextrainfo` text DEFAULT NULL,
  `gameid` varchar(50) DEFAULT NULL,
  `loccountrycode` varchar(10) DEFAULT NULL,
  `locstatecode` varchar(10) DEFAULT NULL,
  `loccityid` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=350 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taged`
--

DROP TABLE IF EXISTS `taged`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `taged` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `tag_id` mediumint(9) NOT NULL,
  `page_id` int(11) NOT NULL DEFAULT 0,
  `type` tinyint(2) NOT NULL DEFAULT 0 COMMENT '0-page,1-image,2-miniblog,3-group',
  `lang` smallint(6) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`),
  KEY `tag_id` (`tag_id`),
  KEY `type` (`type`),
  KEY `page_id_type` (`page_id`,`type`),
  KEY `lang` (`lang`),
  KEY `tag_id_lang` (`tag_id`,`lang`) COMMENT 'Random makona generesanai'
) ENGINE=MyISAM AUTO_INCREMENT=358413 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=FIXED;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tags` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `special` tinyint(1) NOT NULL DEFAULT 0,
  `slug` varchar(64) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=55627 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tfa_whitelist`
--

DROP TABLE IF EXISTS `tfa_whitelist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tfa_whitelist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `ip` varchar(64) DEFAULT NULL,
  `cookie` varchar(64) NOT NULL,
  `token` varchar(64) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=600 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `topic_votes`
--

DROP TABLE IF EXISTS `topic_votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `topic_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` text NOT NULL,
  `page_id` mediumint(9) DEFAULT NULL,
  `points` smallint(6) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=525 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_interests`
--

DROP TABLE IF EXISTS `user_interests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_interests` (
  `user_id` int(11) NOT NULL DEFAULT 0,
  `interest_id` smallint(6) NOT NULL DEFAULT 0,
  KEY `user_id` (`user_id`),
  KEY `interest_id` (`interest_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `userlogs`
--

DROP TABLE IF EXISTS `userlogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `userlogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` int(11) NOT NULL DEFAULT 0,
  `user` mediumint(9) NOT NULL,
  `avatar` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `action` text NOT NULL,
  `multi` varchar(16) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '0',
  `lang` tinyint(4) NOT NULL DEFAULT 1,
  `private` tinyint(1) NOT NULL DEFAULT 0,
  `group_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `multi` (`multi`),
  KEY `lang` (`lang`),
  KEY `time` (`time`),
  KEY `user_lang` (`user`,`lang`),
  KEY `lang_time` (`lang`,`time`),
  KEY `private` (`private`)
) ENGINE=InnoDB AUTO_INCREMENT=5908818 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `nick` varchar(26) NOT NULL,
  `password` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `auth_secret` varchar(16) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `auth_2fa` tinyint(1) NOT NULL DEFAULT 0,
  `mail` varchar(128) DEFAULT NULL,
  `mail_confirmed` datetime DEFAULT NULL,
  `date` datetime NOT NULL,
  `lastseen` datetime NOT NULL,
  `avatar` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `av_alt` tinyint(1) NOT NULL DEFAULT 0,
  `av_lock` tinyint(1) NOT NULL DEFAULT 0,
  `level` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0-user,1-admin,2-mod,3-writer,4-gamemaster',
  `posts` mediumint(9) NOT NULL DEFAULT 0,
  `bookmarks` smallint(6) NOT NULL DEFAULT 0,
  `karma` int(11) NOT NULL DEFAULT 0,
  `karma_bonus` smallint(6) NOT NULL DEFAULT 0,
  `lastip` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `skin` tinyint(4) NOT NULL DEFAULT 3,
  `firstpage` set('news','wall') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'news',
  `signature` text DEFAULT NULL,
  `allow_signature` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Vai lietotājam atļaut izmantot parakstu un par mani lapu',
  `showsig` tinyint(1) NOT NULL DEFAULT 1,
  `skype` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `web` varchar(127) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `about` text DEFAULT NULL,
  `rs_bg` int(11) NOT NULL DEFAULT 0,
  `rs_layout` int(11) NOT NULL DEFAULT 0,
  `ig_done` int(11) NOT NULL DEFAULT 0,
  `ig_points` int(11) NOT NULL DEFAULT 0,
  `custom_title` varchar(32) DEFAULT NULL,
  `custom_title_paid` tinyint(1) NOT NULL DEFAULT 0,
  `yt_name` varchar(127) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `yt_updated` int(11) NOT NULL,
  `twitter` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `credit` int(11) NOT NULL DEFAULT 0,
  `today` smallint(6) NOT NULL DEFAULT 0,
  `daily_first` smallint(4) NOT NULL DEFAULT 0,
  `daily_hangman` smallint(6) NOT NULL DEFAULT 0,
  `daily_best_comment` smallint(6) NOT NULL DEFAULT 0,
  `last_action` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_latvian_ci NOT NULL,
  `maximg` smallint(6) NOT NULL DEFAULT 200,
  `vote_others` mediumint(9) NOT NULL DEFAULT 0,
  `vote_total` mediumint(9) NOT NULL DEFAULT 0,
  `vote_today` smallint(6) NOT NULL DEFAULT 0,
  `seen_today` tinyint(1) NOT NULL DEFAULT 0,
  `days_in_row` mediumint(9) NOT NULL DEFAULT 0,
  `max_in_row` int(11) NOT NULL DEFAULT 0,
  `user_agent` text CHARACTER SET utf8mb3 COLLATE utf8mb3_latvian_ci NOT NULL,
  `draugiem_id` int(11) NOT NULL DEFAULT 0,
  `facebook_id` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '0',
  `twitter_id` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `steam_id` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `lastfm_username` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `lastfm_sessionkey` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `lastfm_subscriber` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `lastfm_token` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `lastfm_updated` int(11) DEFAULT NULL,
  `lastfm_onlyfriends` tinyint(1) NOT NULL DEFAULT 1,
  `show_code` tinyint(1) NOT NULL DEFAULT 0,
  `show_lol` tinyint(1) NOT NULL DEFAULT 0,
  `show_rs` tinyint(1) NOT NULL DEFAULT 0,
  `persona` varchar(127) CHARACTER SET utf8mb3 COLLATE utf8mb3_latvian_ci NOT NULL,
  `mobile` tinyint(1) NOT NULL DEFAULT 0,
  `mobile_seen` tinyint(1) NOT NULL DEFAULT 0,
  `android` tinyint(1) NOT NULL DEFAULT 0,
  `android_seen` tinyint(1) NOT NULL DEFAULT 0,
  `ios` tinyint(1) NOT NULL DEFAULT 0,
  `ios_seen` tinyint(1) NOT NULL DEFAULT 0,
  `pm_notify_email` tinyint(4) NOT NULL DEFAULT 1,
  `warn_count` mediumint(9) NOT NULL DEFAULT 0,
  `source_site` tinyint(4) NOT NULL DEFAULT 1,
  `decos` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `year_first` tinyint(1) NOT NULL DEFAULT 0,
  `rating` int(11) NOT NULL DEFAULT 0,
  `reset_token` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `reset_time` datetime DEFAULT NULL,
  `email_new` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email_token` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email_time` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `private` tinyint(1) NOT NULL DEFAULT 0,
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
  KEY `steam_id` (`steam_id`),
  KEY `facebook_id` (`facebook_id`),
  KEY `twitter_id` (`twitter_id`),
  KEY `deleted` (`deleted`),
  KEY `avatar` (`avatar`)
) ENGINE=InnoDB AUTO_INCREMENT=43065 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_groups`
--

DROP TABLE IF EXISTS `users_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `parent_id` int(11) NOT NULL DEFAULT 0 COMMENT '`users_groups`.`id`',
  `description` text NOT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` int(11) NOT NULL DEFAULT 0,
  `deleted_by` int(11) NOT NULL DEFAULT 0,
  `deleted_at` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `parent_id` (`parent_id`),
  KEY `deleted_by` (`deleted_by`)
) ENGINE=InnoDB AUTO_INCREMENT=595 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_tmp`
--

DROP TABLE IF EXISTS `users_tmp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_tmp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nick` varchar(128) NOT NULL,
  `password` varchar(60) NOT NULL,
  `mail` varchar(128) NOT NULL,
  `created` datetime NOT NULL,
  `hash` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nick` (`nick`),
  UNIQUE KEY `hash` (`hash`),
  KEY `mail` (`mail`)
) ENGINE=MyISAM AUTO_INCREMENT=9216 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `viewprofile`
--

DROP TABLE IF EXISTS `viewprofile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `viewprofile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profile` mediumint(9) NOT NULL,
  `viewer` mediumint(9) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `profile` (`profile`),
  KEY `time` (`time`),
  KEY `viewer` (`viewer`)
) ENGINE=MyISAM AUTO_INCREMENT=1825353 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=FIXED;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `visits`
--

DROP TABLE IF EXISTS `visits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `visits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) NOT NULL DEFAULT 0,
  `site_id` tinyint(4) NOT NULL DEFAULT 1,
  `ip` varchar(45) NOT NULL DEFAULT '',
  `lastseen` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `site_id` (`site_id`),
  KEY `ip` (`ip`),
  KEY `user_lookup` (`user_id`,`ip`,`site_id`),
  KEY `lastseen` (`lastseen`)
) ENGINE=MyISAM AUTO_INCREMENT=714758 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `votes13`
--

DROP TABLE IF EXISTS `votes13`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `votes13` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL DEFAULT 0,
  `date` datetime NOT NULL,
  `ip` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `name` varchar(100) NOT NULL,
  `age` int(11) NOT NULL DEFAULT 10,
  `length` int(11) NOT NULL DEFAULT 0,
  `maxcost` int(11) NOT NULL,
  `paybycard` int(11) NOT NULL DEFAULT 0,
  `distance` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `votes13_dates`
--

DROP TABLE IF EXISTS `votes13_dates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `votes13_dates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL DEFAULT 0,
  `date` datetime NOT NULL,
  `choice` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=198 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `warns`
--

DROP TABLE IF EXISTS `warns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `warns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) NOT NULL DEFAULT 0,
  `created_by` mediumint(9) NOT NULL DEFAULT 1,
  `edited_by` mediumint(9) NOT NULL DEFAULT 0,
  `removed_by` mediumint(9) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL,
  `removed` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `reason` text NOT NULL,
  `remove_reason` text NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `site_id` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `active` (`active`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14864 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wg_games`
--

DROP TABLE IF EXISTS `wg_games`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wg_games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word_id` mediumint(9) NOT NULL,
  `correct` text NOT NULL,
  `wrong` text NOT NULL,
  `user_id` mediumint(9) NOT NULL,
  `created_at` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=154716 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wg_results`
--

DROP TABLE IF EXISTS `wg_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wg_results` (
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
) ENGINE=MyISAM AUTO_INCREMENT=5167 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wg_words`
--

DROP TABLE IF EXISTS `wg_words`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wg_words` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `word` varchar(32) NOT NULL,
  `hint` varchar(512) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=421 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ytlocal`
--

DROP TABLE IF EXISTS `ytlocal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ytlocal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `yt_id` varchar(16) NOT NULL,
  `yt_title` varchar(512) NOT NULL,
  `yt_description` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `yt_id` (`yt_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed
