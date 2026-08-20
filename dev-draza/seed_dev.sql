-- EXS.LV Local Development Minimal Seed Data
-- Standard password for all users is: password123

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Fake Users (10 users)
INSERT INTO `users` (id, nick, password, mail, mail_confirmed, date, lastseen, avatar, level, posts, karma, about, signature, skype, web, yt_name, yt_updated, last_action, user_agent, persona, decos, token) VALUES
(1, 'Jānis', '$2y$12$cIBcNY.4CxAnrV/9WjJU7u0zGoM9PsjNsI.mXznNskwONI3Sai16O', 'janis@exs.lv', NOW(), NOW(), NOW(), 'none.png', 1, 42, 540, 'EXS.lv administrators un izstrādātājs.', 'Avis la vista!', '', '', '', 0, '', '', '', '', ''),
(2, 'Pēteris', '$2y$12$cIBcNY.4CxAnrV/9WjJU7u0zGoM9PsjNsI.mXznNskwONI3Sai16O', 'peteris@exs.lv', NOW(), NOW(), NOW(), 'none.png', 2, 28, 320, 'Moderators un kaislīgs spēlētājs.', 'Saglabājiet mieru!', '', '', '', 0, '', '', '', '', ''),
(3, 'Līga', '$2y$12$cIBcNY.4CxAnrV/9WjJU7u0zGoM9PsjNsI.mXznNskwONI3Sai16O', 'liga@exs.lv', NOW(), NOW(), NOW(), 'none.png', 0, 15, 150, 'Mūzikas un filmu entuziaste.', 'Dzīve ir skaista!', '', '', '', 0, '', '', '', '', ''),
(4, 'Māris', '$2y$12$cIBcNY.4CxAnrV/9WjJU7u0zGoM9PsjNsI.mXznNskwONI3Sai16O', 'maris@exs.lv', NOW(), NOW(), NOW(), 'none.png', 3, 33, 280, 'Rakstu autors un spēļu apskatnieks.', 'Rakstu par jaunākajām spēlēm.', '', '', '', 0, '', '', '', '', ''),
(5, 'Kārlis', '$2y$12$cIBcNY.4CxAnrV/9WjJU7u0zGoM9PsjNsI.mXznNskwONI3Sai16O', 'karlis@exs.lv', NOW(), NOW(), NOW(), 'none.png', 0, 19, 95, 'Retro spēļu faniņš.', 'Tetris ir mūžīgs.', '', '', '', 0, '', '', '', '', ''),
(6, 'Elīna', '$2y$12$cIBcNY.4CxAnrV/9WjJU7u0zGoM9PsjNsI.mXznNskwONI3Sai16O', 'elina@exs.lv', NOW(), NOW(), NOW(), 'none.png', 0, 24, 210, 'Dizainere un fotogrāfe.', 'Krāsas vada pasauli.', '', '', '', 0, '', '', '', '', ''),
(7, 'Artūrs', '$2y$12$cIBcNY.4CxAnrV/9WjJU7u0zGoM9PsjNsI.mXznNskwONI3Sai16O', 'arturs@exs.lv', NOW(), NOW(), NOW(), 'none.png', 4, 50, 410, 'Game Master & turnīru organizators.', 'Gatavs nākamajam turnīram!', '', '', '', 0, '', '', '', '', ''),
(8, 'Laura', '$2y$12$cIBcNY.4CxAnrV/9WjJU7u0zGoM9PsjNsI.mXznNskwONI3Sai16O', 'laura@exs.lv', NOW(), NOW(), NOW(), 'none.png', 0, 12, 85, 'Datorgrafikas un IT studente.', 'Coding non-stop.', '', '', '', 0, '', '', '', '', ''),
(9, 'Edgars', '$2y$12$cIBcNY.4CxAnrV/9WjJU7u0zGoM9PsjNsI.mXznNskwONI3Sai16O', 'edgars@exs.lv', NOW(), NOW(), NOW(), 'none.png', 0, 31, 175, 'Aparatūras eksperts un minidzinēju entuziasts.', 'Overclocked everything.', '', '', '', 0, '', '', '', '', ''),
(10, 'Zane', '$2y$12$cIBcNY.4CxAnrV/9WjJU7u0zGoM9PsjNsI.mXznNskwONI3Sai16O', 'zane@exs.lv', NOW(), NOW(), NOW(), 'none.png', 0, 18, 120, 'Casual gamer un grāmatu mīļotāja.', 'Spēlēju prieka pēc!', '', '', '', 0, '', '', '', '', '')
ON DUPLICATE KEY UPDATE
password=VALUES(password), level=VALUES(level), karma=VALUES(karma);

-- 2. Fake Topics / Articles (pages)
INSERT INTO `pages` (id, strid, textid, category, title, intro, text, author, date, bump, ip, posts, views, avatar, sm_avatar, readby, redirect, rating_users) VALUES
(1, 'laipni-lugti-jaunaja-exs-lv', 'p1', 232, 'Laipni lūgti jaunajā EXS.LV portalā!', 'Sveicieni visiem exs.lv biedriem un apmeklētājiem!', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam in dui mauris. Vivamus hendrerit arcu sed erat molestie vehicula. Sed semper risus id metus. Phasellus dapibus semper urna. Duis iaculis porttitor num.', 1, NOW(), NOW(), '127.0.0.1', 4, 150, '', '', '', '', ''),
(2, 'labakas-2026-gada-speles', 'p2', 98, 'Labākās 2026. gada spēles un jaunumi', 'Kopsavilkums par šī gada spilgtākajām spēlēm.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur sodales ligula in libero. Sed dignissim lacinia nunc. Curabitur tortor. Pellentesque nibh. Aenean quam. In scelerisque sem at multo.', 4, NOW(), NOW(), '127.0.0.1', 3, 98, '', '', '', '', ''),
(3, 'ka-optimizet-datora-veiktspeju', 'p3', 89, 'Kā optimizēt datora veiktspēju spēlēm', 'Daži praktiski padomi un triki datora ātrdarbības uzlabošanai.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero. Sed cursus ante dapibus diam. Sed nisi. Nulla quis sem at nibh elementum imperdiet.', 9, NOW(), NOW(), '127.0.0.1', 2, 74, '', '', '', '', ''),
(4, 'augsup-speles-rekordu-konkurss', 'p4', 2516, 'Spēles Augšup jaunais rekordu konkurss', 'Aicinām visus izmēģināt spēku jaunajā spēlē Augšup!', 'Sveiki visi! Esam atjauninājuši spēli Augšup. Izmēģiniet atlekt no brūnajām platformām un uzstādiet jaunus rekordus!', 7, NOW(), NOW(), '127.0.0.1', 5, 210, '', '', '', '', '')
ON DUPLICATE KEY UPDATE
title=VALUES(title), text=VALUES(text);

-- 3. Comments on Topics
INSERT INTO `comments` (id, pid, author, text, date, ip, vote_users) VALUES
(1, 1, 2, 'Sveiciens visiem! Lieliskas ziņas par jauno versiju.', NOW(), '127.0.0.1', ''),
(2, 1, 3, 'Malči! Izskatās ļoti labi un moderni.', NOW(), '127.0.0.1', ''),
(3, 1, 5, 'Super! Ļoti gaidīju šo atjauninājumu.', NOW(), '127.0.0.1', ''),
(4, 1, 6, 'Apsveicu ar jauno dizainu un funkcionalitāti!', NOW(), '127.0.0.1', ''),
(5, 2, 1, 'Paldies par pārskatu, ļoti interesants raksts.', NOW(), '127.0.0.1', ''),
(6, 2, 7, 'Gaidu nākamās spēles iznākšanu rudenī.', NOW(), '127.0.0.1', ''),
(7, 3, 2, 'Labs raksts, īpaši daļa par videokartes draiveriem.', NOW(), '127.0.0.1', ''),
(8, 4, 3, 'Man ļoti patīk brūnās platformas uzlabojums!', NOW(), '127.0.0.1', ''),
(9, 4, 5, 'Šodien uzstādīju savu personīgo rekordu 4500m!', NOW(), '127.0.0.1', '')
ON DUPLICATE KEY UPDATE
text=VALUES(text);

-- 4. Miniblog Entries
INSERT INTO `miniblog` (id, author, date, text, ip, parent, posts, close_reason, vote_users) VALUES
(1, 1, NOW(), 'Labrīt visiem! Kāds šodien plāns spēlēs?', '127.0.0.1', 0, 2, '', ''),
(2, 2, NOW(), 'Sveiciens! Šodien plānoju uzspēlēt Tetris un Piektdienas spēles.', '127.0.0.1', 1, 0, '', ''),
(3, 3, NOW(), 'Es tikko uzstādīju jaunu rekordu spēlē Lidojošais Eksis! 🏆', '127.0.0.1', 0, 1, '', ''),
(4, 4, NOW(), 'Apsveicu, Līga! Cik punktus ieguvi?', '127.0.0.1', 3, 0, '', ''),
(5, 5, NOW(), 'Vai kāds vēlas uzspēlēt Desas tiešsaistē?', '127.0.0.1', 0, 0, '', ''),
(6, 6, NOW(), 'Diena ir lieliska jaunai spēļu sesijai. Veiksmi visiem!', '127.0.0.1', 0, 0, '', ''),
(7, 7, NOW(), 'Sagatavots jaunais turnīru saraksts brīvdienām!', '127.0.0.1', 0, 0, '', ''),
(8, 8, NOW(), 'Kods uzrakstīts, tagad laiks pauzei un čūskas spēlei. 🐍', '127.0.0.1', 0, 0, '', '')
ON DUPLICATE KEY UPDATE
text=VALUES(text);

-- 5. Game Scores (gamescore)
INSERT INTO `gamescore` (user_id, game, score, time) VALUES
(1, 'augsup', 5280, 120),
(2, 'augsup', 4120, 95),
(5, 'augsup', 4500, 110),
(7, 'augsup', 6100, 140),
(3, 'flappy', 45, 60),
(1, 'flappy', 38, 50),
(5, 'tetris', 12400, 300),
(9, 'tetris', 9800, 240),
(8, 'snake', 320, 180),
(6, 'snake', 280, 150),
(4, 'invaders', 15400, 200),
(10, '2048', 8192, 450),
(2, 'minu-mekletajs', 45, 45),
(3, 'wordle', 3, 30);

-- 6. Friendships
INSERT INTO `friends` (friend1, friend2, date, date_confirmed, confirmed) VALUES
(1, 2, NOW(), NOW(), 1),
(1, 3, NOW(), NOW(), 1),
(1, 4, NOW(), NOW(), 1),
(2, 5, NOW(), NOW(), 1),
(3, 6, NOW(), NOW(), 1),
(4, 7, NOW(), NOW(), 1),
(5, 8, NOW(), NOW(), 1),
(6, 9, NOW(), NOW(), 1),
(7, 10, NOW(), NOW(), 1);

SET FOREIGN_KEY_CHECKS = 1;
