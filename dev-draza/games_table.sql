-- EXS.LV Games Table & Initial Seed Data

CREATE TABLE IF NOT EXISTS `games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(64) NOT NULL,
  `title` varchar(128) NOT NULL,
  `url` varchar(128) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `badge` varchar(64) NOT NULL DEFAULT '',
  `badge_class` varchar(64) NOT NULL DEFAULT '',
  `desc` text NOT NULL,
  `game_code` varchar(64) NOT NULL,
  `vote_value` smallint(6) NOT NULL DEFAULT 0,
  `vote_users` text NOT NULL DEFAULT '',
  `votes_up` int(11) NOT NULL DEFAULT 0,
  `votes_down` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','hidden') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `vote_value` (`vote_value`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `games` (`slug`, `title`, `url`, `icon`, `badge`, `badge_class`, `desc`, `game_code`, `vote_value`, `vote_users`, `votes_up`, `votes_down`, `status`) VALUES
('tetris', 'Tetris', '/tetris', '/bildes/icons/games/tetris.png', '', '', 'Klasiskais Tetris ar SRS griešanos, līmeņiem un tiešsaistes topos.', 'tetris', 0, '', 0, 0, 'active'),
('snake', 'Čūska', '/snake', '/bildes/icons/games/snake.png', '', '', 'Izsalkusī čūska ar 6 sarežģītības līmeņiem un šķēršļu sienām.', 'snake', 0, '', 0, 0, 'active'),
('karatavas', 'Karātavas', '/karatavas', '/bildes/icons/games/karatavas.png', '', '', 'Interaktīva vārdu minēšanas spēle ar tūkstošiem latviešu valodas vārdu.', 'karatavas', 0, '', 0, 0, 'active'),
('memory', 'Atmiņas spēle', '/memory', '/bildes/icons/games/memory.png', '', '', 'Atrodi vienādos EXS.LV lietotāju avatarus! Maināmi 4x4, 6x4 un 6x6 tīkli.', 'memory', 0, '', 0, 0, 'active'),
('2048', '2048', '/2048-spele', '/bildes/icons/games/2048.png', '', '', 'Bīdi un apvieno vienādos skaitļu lauciņus, lai sasniegtu 2048 flīzi un uzstādītu rekordu!', '2048', 0, '', 0, 0, 'active'),
('minu-mekletajs', 'Mīnu Meklētājs', '/minu-mekletajs', '/bildes/icons/games/minu-mekletajs.png', '', '', 'Klasiskā Minesweeper spēle ar 3 grūtības līmeņiem un ātruma rekordu topu.', 'minu-mekletajs', 0, '', 0, 0, 'active'),
('sudoku', 'Sudoku', '/sudoku', '/bildes/icons/games/sudoku.png', '', '', 'Klasiskā Sudoku mīkla ar 3 sarežģītības līmeņiem, zīmuļa piezīmēm un mājieniem.', 'sudoku', 0, '', 0, 0, 'active'),
('wordle', 'Wordle', '/wordle', '/bildes/icons/games/wordle.png', '', '', 'Populārā 5 burtu vārdu minēšanas spēle latviešu valodā ar dienas vārdu un treniņu režīmu.', 'wordle', 0, '', 0, 0, 'active'),
('rulete', 'Rulete', '/rulete', '/bildes/icons/games/rulete.png', '', '', 'Klasiskā Eiropas kazino rulete ar 100 zelta sākuma kapitālu un ikdienas bilances atjaunošanu.', 'rulete', 0, '', 0, 0, 'active'),
('desas', 'Desas', '/desas', '/bildes/icons/games/desas.png', '', '', 'Klasiskā desu (Tic-Tac-Toe) spēle.', 'desas', 0, '', 0, 0, 'active'),
('flappy', 'Lidojošais Eksis', '/flappy', '/bildes/icons/games/flappy.png', '', '', 'Vadā savu pārlūka avatāru cauri šķēršļiem, vāc punktus un uzstādi jaunu rekordu!', 'flappy', 0, '', 0, 0, 'active'),
('invaders', 'Space Invaders', '/invaders', '/bildes/icons/games/invaders.png', '', '', 'Klasiskā kosmosa iebrucēju spēle bezgalīgā režīmā. Aizstāvi Zemi, vāc punktus un uzstādi jaunu rekordu!', 'invaders', 0, '', 0, 0, 'active'),
('augsup', 'Augšup', '/augsup', '/bildes/icons/games/augsup.png', '', '', 'Lēkā pa platformām ar savu avatāru, sasniedz mākoņus un uzstādi jaunu augstuma rekordu!', 'augsup', 0, '', 0, 0, 'active'),
('vardes', 'Vardes', '/vardes', '/bildes/icons/games/vardes.png', '', '', 'Šķērso bīstamo šoseju un upi ar baļķiem, lai sasniegtu liliju lapas un uzstādītu rekordu!', 'vardes', 0, '', 0, 0, 'active'),
('runner', 'Runner', '/runner', '/bildes/icons/games/runner.png', 'Jaunums', 'label-success', 'Bēdz no šķēršļiem un citu lietotāju avatariem, vāc zvaigznes un uzstādi jaunu rekordu!', 'runner', 0, '', 0, 0, 'active'),
('tornis', 'Tornis', '/tornis', '/bildes/icons/games/tornis.png', 'Jaunums', 'label-success', 'Būvē augstāko debesskrāpi! Liec 3D blokus vienu virs otra, veido combo sērijas un uzstādi rekordu!', 'tornis', 0, '', 0, 0, 'active'),
('arkanoid', 'Arkanoid', '/arkanoid', '/bildes/icons/games/arkanoid.png', 'Jaunums', 'label-success', 'Klasiskā arkādes spēle! Vadi Vaus, atsit bumbu, sašķaidi blokus un ķer leģendāros kapsulu bonusus.', 'arkanoid', 0, '', 0, 0, 'active')
ON DUPLICATE KEY UPDATE
  `title` = VALUES(`title`),
  `url` = VALUES(`url`),
  `icon` = VALUES(`icon`),
  `badge` = VALUES(`badge`),
  `badge_class` = VALUES(`badge_class`),
  `desc` = VALUES(`desc`),
  `game_code` = VALUES(`game_code`);
