-- EXS.LV Shared Game Chat Table Schema

CREATE TABLE IF NOT EXISTS `game_chat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) NOT NULL,
  `game` varchar(64) NOT NULL DEFAULT '',
  `game_title` varchar(128) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `time` int(11) NOT NULL,
  `removed` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `time` (`time`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `game_chat_online` (
  `user_id` mediumint(9) NOT NULL,
  `game` varchar(64) NOT NULL DEFAULT '',
  `game_title` varchar(128) NOT NULL DEFAULT '',
  `last_seen` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`),
  KEY `last_seen` (`last_seen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
