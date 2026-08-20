-- EXS.LV Local Development Minimal Seed Data
-- Standard password for all users is: password123

SET NAMES utf8mb4;
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
(1, 'laipni-lugti-jaunaja-exs-lv', 'p1', 1, 'Laipni lūgti jaunajā EXS.LV portālā!', 'Sveicieni visiem exs.lv biedriem un apmeklētājiem!', 'Sveiki visiem! EXS.LV ir atjaunināts ar jaunu vizuālo tēlu, ātrāku datubāzi un ērtāku mobilo versiju. Paldies ikvienam, kas piedalās mūsu kopienas veidošanā un uzturēšanā.', 1, NOW(), NOW(), '127.0.0.1', 4, 150, '', '', '', '', ''),
(2, 'labakas-2026-gada-speles', 'p2', 81, 'Labākās 2026. gada spēles un jaunumi', 'Kopsavilkums par šī gada spilgtākajām spēlēm un izlaidumiem.', 'Šogad spēļu industrija mūs priecē ar neparasti daudziem augstas kvalitātes izlaidumiem. No iespaidīgiem atvērtās pasaules RPG līdz aizraujošām indiju spēlēm – katrs atradīs sev ko piemērotu.', 4, NOW(), NOW(), '127.0.0.1', 3, 98, '', '', '', '', ''),
(3, 'ka-optimizet-datora-veiktspeju', 'p3', 89, 'Kā optimizēt datora veiktspēju spēlēm', 'Daži praktiski padomi un triki datora ātrdarbības uzlabošanai.', 'Ja jūsu dators sāk aizturēt kadrus jaunākajās spēlēs, pirms jaunas aparatūras iegādes vērts veikt fona procesu tīrīšanu, draiveru atjaunināšanu un operatīvās atmiņas optimizāciju.', 9, NOW(), NOW(), '127.0.0.1', 2, 74, '', '', '', '', ''),
(4, 'augsup-speles-rekordu-konkurss', 'p4', 2516, 'Spēles Augšup jaunais rekordu konkurss', 'Aicinām visus izmēģināt spēku jaunajā spēlē Augšup!', 'Sveiki visi! Esam atjauninājuši spēli Augšup. Izmēģiniet atlekt no brūnajām platformām un uzstādiet jaunus rekordus!', 7, NOW(), NOW(), '127.0.0.1', 5, 210, '', '', '', '', ''),
(5, 'geforce-rtx-5090-pazinojums', 'p5', 89, 'Nvidia izziņo GeForce RTX 5090 videokarti', 'Nvidia oficiāli prezentējusi jauno Flagmaņu sērijas videokarti RTX 5090 ar Blackwell arhitektūru.', 'Jaunā RTX 5090 videokarte nodrošina līdz pat 70% veiktspējas pieaugumu salīdzinājumā ar iepriekšējo paaudzi. Ar 32 GB GDDR7 atmiņu un DLSS 4 atbalstu, spēlētāji varēs baudīt 4K izšķirtspēju ar maksimāliem stariņu izsekošanas iestatījumiem un augstu kadru skaits sekundē.', 9, NOW(), NOW(), '127.0.0.1', 2, 340, '', '', '', '', ''),
(6, 'gta-6-jaunais-treileris-un-izdosanas-datums', 'p6', 1, 'GTA 6 saņem jaunu gameplay treileri un oficiālo izdošanas datumu', 'Rockstar Games demonstrē jaunu spēles gaitas video un apstiprina izdošanas datumu rudenī.', 'Rockstar Games šodien publiskoja ilgi gaidīto GTA 6 spēles gaitas treileri. Video atklāj plašās Vice City ielas, uzlabotu fizikas dzinēju, reālistiskas pūļa reakcijas un jaunu dinamisku laika apstākļu sistēmu. Spēle būs pieejama uz PS5 un Xbox Series X/S jau šā gada rudenī.', 4, NOW(), NOW(), '127.0.0.1', 2, 510, '', '', '', '', ''),
(7, 'counter-strike-2-liela-atjauninajuma-zinas', 'p7', 1, 'CS2 lielais atjauninājums: atgriežas klasiskās kartes un antireitinga sistēma', 'Valve izlaidusi vērienīgu Counter-Strike 2 atjauninājumu ar jaunām kartēm un pretkrāpšanas uzlabojumiem.', 'Valve atjauninājusi CS2 ar vairākām populārām kopienas kartēm, tostarp Cache un Train atjaunotajām versijām. Tāpat ir uzlabots Sub-Tick tīkla kods, samazinot aizturi un padarot šaušanas mehāniku precīzāku un atsaucīgāku.', 2, NOW(), NOW(), '127.0.0.1', 2, 290, '', '', '', '', ''),
(8, 'latvijas-esporta-liga-sakas-jauna-sezona', 'p8', 1, 'Latvijas e-sporta līga uzsāk jauno 2026. gada sezonu', 'Sākas pieteikšanās Latvijas lielākajam CS2 un League of Legends turnīram ar 10,000 EUR balvu fondu.', 'Latvijas e-sporta asociācija paziņo par 2026. gada pavasara sezonas atklāšanu. Komandas no visas Latvijas var pieteikties tiešsaistes kvalifikācijas posmiem. Fināla spēles norisināsies klātienē Rīgā, kur labākās komandas cīnīesies par čempiona titulu.', 7, NOW(), NOW(), '127.0.0.1', 2, 180, '', '', '', '', ''),
(9, 'nakamas-paaudzes-ai-spelu-izstrade', 'p9', 1, 'Mākslīgais intelekts un spēļu izstrādes nākotne 2026. gadā', 'Pārskats par to, kā AI rīki un procedurālā ģenerēšana maina mūsdienu video spēļu izstrādi.', 'Mākslīgais intelekts 2026. gadā kļuvis par neatņemamu spēļu izstrādes sastāvdaļu. No gudrākiem NPC tēliem līdz dinamiskai pasaules ģenerēšanai reāllaikā – izstrādātāji spēj radīt daudz plašākas un dzīvākas pasaules īsākā laikā. Rakstā aplūkojam spilgtākos piemērus un nākotnes tendences.', 1, NOW(), NOW(), '127.0.0.1', 1, 420, '', '', '', '', ''),
(10, 'cyberpunk-2077-turpinajums-project-orion', 'p10', 80, 'CD Projekt Red atklāj pirmās detaļas par Cyberpunk 2077 turpinājumu', 'Kompānija uzsākusi aktīvu izstrādes fāzi projekta "Orion" nākamajai spēlei.', 'CD Projekt Red komanda paziņojusi, ka nākamā Cyberpunk spēle tiks veidota uz Unreal Engine 5 dzinēja. Izstrādātāji sola vēl dziļāku lomu spēles elementu integrāciju, uzlabotu stāstījumu un jaunus reģionus ārpus Night City robām. Spēles koncepti solās pārsteigt pat rūdītākos žanra cienītājus.', 4, NOW(), NOW(), '127.0.0.1', 1, 380, '', '', '', '', ''),
(11, 'elektriskie-sporta-auto-un-simulaciju-speles', 'p11', 1, 'Jaunākie auto simulāciju uzlabojumi un elektroauto sports', 'Kā mūsdienu autosporta simulācijas sasniedz nepārspētu reālismu ar jaunajiem fizikas dzinējiem.', 'Sacīkšu simulāciju spēles sniedz neticamu precizitāti un reālismu, ļaujot spēlētājiem izjust jaunāko hibrīda un elektrisko sacīkšu automašīnu uzvedību trasē. Rakstā pētām jauno riepu nodiluma fiziku, aerodinamikas modeļus un labākos stūru komplektus mājas lietošanai.', 2, NOW(), NOW(), '127.0.0.1', 1, 260, '', '', '', '', '')
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
(9, 4, 5, 'Šodien uzstādīju savu personīgo rekordu 4500m!', NOW(), '127.0.0.1', ''),
(10, 5, 8, '32GB VRAM izskatās iespaidīgi! Laiks krāt naudu.', NOW(), '127.0.0.1', ''),
(11, 5, 9, 'Fascinējošs lēciens veiktspējā. Gaidu testus reālās spēlēs.', NOW(), '127.0.0.1', ''),
(12, 6, 1, 'Vice City atmosfēra izskatās neaprakstāmi labi!', NOW(), '127.0.0.1', ''),
(13, 6, 3, 'Nevaru sagaidīt izdošanas dienu rudenī.', NOW(), '127.0.0.1', ''),
(14, 7, 4, 'Beidzot atgriežas Train karte! Paldies Valve.', NOW(), '127.0.0.1', ''),
(15, 7, 2, 'Sub-tick uzlabojumi tiešām bija ļoti nepieciešami.', NOW(), '127.0.0.1', ''),
(16, 8, 7, 'Pieteicu mūsu komandu kvalifikācijai! Tiekamies spēlē.', NOW(), '127.0.0.1', ''),
(17, 8, 10, 'Lai veicas visiem turnīra dalībniekiem!', NOW(), '127.0.0.1', ''),
(18, 9, 3, 'Ļoti aizraujošs raksts! AI NPC uzvedība patiešām ir sperts liels solis uz priekšu.', NOW(), '127.0.0.1', ''),
(19, 10, 5, 'Unreal Engine 5 sniegs neticamu grafiku. Ļoti gaidu kadrus no spēles!', NOW(), '127.0.0.1', ''),
(20, 11, 8, 'Autosporta simulācijas pēdējos gados ir kļuvušas neticami reālistiskas.', NOW(), '127.0.0.1', '')
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
