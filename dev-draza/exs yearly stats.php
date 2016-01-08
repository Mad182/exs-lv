<?php

/*
|--------------------------------------------------------------------------
|   Saistīti raksti/miniblogi (ļoti iespējams, ka kaut kas ir izlaists!)
|--------------------------------------------------------------------------
*/

NOMINĀCIJAS
	2014. gads
		https://exs.lv/lazy-girls/forum/2gbvv
		https://exs.lv/read/2014-gada-nominacijas
	2013. gads
		https://exs.lv/read/exs-lv-gada-nominacijas-2013-rezultati
	2012. gads
		https://exs.lv/read/2012-gada-exs-lv-nominacijas-2

STATISTIKA
	2015. gads
		https://exs.lv/lazy-girls/forum/2q8x6
		Styrnuča ieteikumi
		https://exs.lv/lazy-girls/forum/2ps0v#m4580088
	2014. gads
		Apkopota statistika
		https://exs.lv/lazy-girls/forum/2gt5g
	2013. gads
		Viesty infografiks
		https://exs.lv/read/exs-lv-2013-gada-infografiks
	
/*
|--------------------------------------------------------------------------
|   Raksti/blograksti/foruma raksti/rakstu komentāri
|--------------------------------------------------------------------------
*/

rakstu skaits ------------------------------------------------------------- 31

	SELECT count(*) FROM `pages`
		JOIN `cat` ON `pages`.`category` = `cat`.`id`
	WHERE
		`pages`.`category` NOT IN(6, 244, 1133, 1904, 1972) AND
		`pages`.`date` > '2014-12-31 23:59:59' AND
		`pages`.`date` < '2016-01-01 00:00:00' AND
		`cat`.`isblog` = 0 AND
		`cat`.`isforum` = 0 AND
		`pages`.`lang` IN(0, 1)
	// sadaļas ir dažādu apakšprojektu musari
	
	// visvairāk sarakstījuši:
	SELECT `users`.`nick`, count(*) AS `articles_written` FROM `pages`
		JOIN `cat` ON `pages`.`category` = `cat`.`id`
		JOIN `users` ON `pages`.`author` = `users`.`id`
	WHERE
		`pages`.`category` NOT IN(6, 244, 1133, 1904, 1972) AND
		`pages`.`date` > '2014-12-31 23:59:59' AND
		`pages`.`date` < '2016-01-01 00:00:00' AND
		`cat`.`isblog` = 0 AND
		`cat`.`isforum` = 0 AND
		`pages`.`lang` IN(0, 1)
	GROUP BY `pages`.`author` ORDER BY count(*) DESC LIMIT 10
	
	filmas: `cat`.`id` = 80													4
	spēles: `cat`.`id` = 81													7
	mūzika: `cat`.`id` = 323												1

blograkstu skaits --------------------------------------------------------- 1

	SELECT count(*) FROM `pages`
		JOIN `cat` ON `pages`.`category` = `cat`.`id`
	WHERE
		`pages`.`category` NOT IN(6, 244, 1133, 1904, 1972) AND
		`pages`.`date` > '2014-12-31 23:59:59' AND
		`pages`.`date` < '2016-01-01 00:00:00' AND
		`cat`.`isblog` = 1 AND
		`cat`.`isforum` = 0 AND
		`pages`.`lang` IN(0, 1)
	// sadaļas ir dažādu apakšprojektu musari
	
	// visvairāk sarakstījuši:
	SELECT `users`.`nick`, count(*) AS `blogs_written` FROM `pages`
		JOIN `cat` ON `pages`.`category` = `cat`.`id`
		JOIN `users` ON `pages`.`author` = `users`.`id`
	WHERE
		`pages`.`category` NOT IN(6, 244, 1133, 1904, 1972) AND
		`pages`.`date` > '2014-12-31 23:59:59' AND
		`pages`.`date` < '2016-01-01 00:00:00' AND
		`cat`.`isblog` = 1 AND
		`cat`.`isforum` = 0 AND
		`pages`.`lang` IN(0, 1)
	GROUP BY `pages`.`author` ORDER BY count(*) DESC LIMIT 10

foruma tēmas -------------------------------------------------------------- 852

	SELECT count(*) FROM `pages`
		JOIN `cat` ON `pages`.`category` = `cat`.`id`
	WHERE
		`pages`.`category` NOT IN(6, 244, 1133, 1904, 1972) AND
		`pages`.`date` > '2014-12-31 23:59:59' AND
		`pages`.`date` < '2016-01-01 00:00:00' AND
		`cat`.`isblog` = 0 AND
		`cat`.`isforum` = 1 AND
		`pages`.`lang` IN(0, 1)
	// sadaļas ir dažādu apakšprojektu musari
	
	// visvairāk sarakstījuši:
	SELECT `users`.`nick`, count(*) AS `topics_written` FROM `pages`
		JOIN `cat` ON `pages`.`category` = `cat`.`id`
		JOIN `users` ON `pages`.`author` = `users`.`id`
	WHERE
		`pages`.`category` NOT IN(6, 244, 1133, 1904, 1972) AND
		`pages`.`date` > '2014-12-31 23:59:59' AND
		`pages`.`date` < '2016-01-01 00:00:00' AND
		`cat`.`isblog` = 0 AND
		`cat`.`isforum` = 1 AND
		`pages`.`lang` IN(0, 1)
	GROUP BY `pages`.`author` ORDER BY count(*) DESC LIMIT 10

komentāri rakstos (tai skaitā forumā un blogos) --------------------------- 11321

	SELECT count(*) FROM `comments`
		JOIN `pages` ON `comments`.`pid` = `pages`.`id`
	WHERE
		`comments`.`date` > '2014-12-31 23:59:59' AND
		`comments`.`date` < '2016-01-01 00:00:00' AND
		`pages`.`category` NOT IN(6, 244, 1133, 1904, 1972) AND
		`pages`.`lang` IN(0, 1)
	// sadaļas ir dažādu apakšprojektu musari
	
	// visvairāk sarakstījuši:
	SELECT `users`.`nick`, count(*) AS `comments_written` FROM `comments`
		JOIN `pages` ON `comments`.`pid` = `pages`.`id`
		JOIN `users` ON `comments`.`author` = `users`.`id`
	WHERE
		`comments`.`date` > '2014-12-31 23:59:59' AND
		`comments`.`date` < '2016-01-01 00:00:00' AND
		`pages`.`category` NOT IN(6, 244, 1133, 1904, 1972) AND
		`pages`.`lang` IN(0, 1)
	GROUP BY `comments`.`author` ORDER BY count(*) DESC LIMIT 10


/*
|--------------------------------------------------------------------------
|   Galerijas/attēli/attēlu komentāri
|--------------------------------------------------------------------------
*/
	
attēli galerijās ---------------------------------------------------------- 163

	SELECT count(*) FROM `images`
	WHERE
		`images`.`date` > '2014-12-31 23:59:59' AND
		`images`.`date` < '2016-01-01 00:00:00' AND
		`images`.`lang` IN(0, 1)
		
	// visvairāk
	SELECT `users`.`nick`, count(*) AS `images_uploaded` FROM `images`
		JOIN `users` ON `images`.`uid` = `users`.`id`
	WHERE
		`images`.`date` > '2014-12-31 23:59:59' AND
		`images`.`date` < '2016-01-01 00:00:00' AND
		`images`.`lang` IN(0, 1)
	GROUP BY `images`.`uid` ORDER BY count(*) DESC LIMIT 10

komentāri pie attēliem ---------------------------------------------------- 1406

	SELECT count(*) FROM `galcom`
		JOIN `images` ON `galcom`.`bid` = `images`.`id`
	WHERE
		`galcom`.`date` > '2014-12-31 23:59:59' AND
		`galcom`.`date` < '2016-01-01 00:00:00' AND
		`galcom`.`removed` = 0 AND
		`images`.`lang` IN(0, 1)
		
	// visvairāk
	SELECT `users`.`nick`, count(*) AS `gallery_comments` FROM `galcom`
		JOIN `images` ON `galcom`.`bid` = `images`.`id`
		JOIN `users` ON `galcom`.`author` = `users`.`id`
	WHERE
		`galcom`.`date` > '2014-12-31 23:59:59' AND
		`galcom`.`date` < '2016-01-01 00:00:00' AND
		`galcom`.`removed` = 0 AND
		`images`.`lang` IN(0, 1)
	GROUP BY `galcom`.`author` ORDER BY count(*) DESC LIMIT 10

		
/*
|--------------------------------------------------------------------------
|   Pastkastīte/vēstules
|--------------------------------------------------------------------------
*/

nosūtītās vēstules -------------------------------------------------------- 56869

	SELECT count(*) FROM `pm`
	WHERE
		`pm`.`date` > '2014-12-31 23:59:59' AND
		`pm`.`date` < '2016-01-01 00:00:00'
		
	// visvairāk
	SELECT `users`.`nick`, count(*) AS `msg_sent` FROM `pm`
		JOIN `users` ON `pm`.`from_uid` = `users`.`id`
	WHERE
		`pm`.`date` > '2014-12-31 23:59:59' AND
		`pm`.`date` < '2016-01-01 00:00:00'
	GROUP BY `pm`.`from_uid` ORDER BY count(*) DESC LIMIT 10

neizlasītās vēstules ------------------------------------------------------ 871

	SELECT count(*) FROM `pm`
	WHERE
		`pm`.`date` > '2014-12-31 23:59:59' AND
		`pm`.`date` < '2016-01-01 00:00:00' AND
		`pm`.`is_read` = 0
		
	// visvairāk
	SELECT `users`.`nick`, count(*) AS `unread_count` FROM `pm`
		JOIN `users` ON `pm`.`to_uid` = `users`.`id`
	WHERE
		`pm`.`date` > '2014-12-31 23:59:59' AND
		`pm`.`date` < '2016-01-01 00:00:00' AND
		`pm`.`is_read` = 0
	GROUP BY `pm`.`to_uid` ORDER BY count(*) DESC LIMIT 10


/*
|--------------------------------------------------------------------------
|   Junk attēli/komentāri
|--------------------------------------------------------------------------
*/

attēli junk sadaļā -------------------------------------------------------- 2109

	SELECT count(*) FROM `junk`
	WHERE
		`junk`.`date` > '2014-12-31 23:59:59' AND
		`junk`.`date` < '2016-01-01 00:00:00' AND
		`junk`.`removed` = 0 AND
		`junk`.`lang` IN(0, 1)
		
	// visvairāk
	SELECT `users`.`nick`, count(*) AS `junk_images` FROM `junk`
		JOIN `users` ON `junk`.`author` = `users`.`id`
	WHERE
		`junk`.`date` > '2014-12-31 23:59:59' AND
		`junk`.`date` < '2016-01-01 00:00:00' AND
		`junk`.`removed` = 0 AND
		`junk`.`lang` IN(0, 1)
	GROUP BY `junk`.`author` ORDER BY count(*) DESC LIMIT 10

komentāri junk sadaļā ----------------------------------------------------- 7001

	SELECT count(*) FROM `miniblog`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`type` = 'junk' AND
		`miniblog`.`parent` != 0
		
	// visvairāk
	SELECT `users`.`nick`, count(*) AS `junk_comments` FROM `miniblog`
		JOIN `users` ON `miniblog`.`author` = `users`.`id`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`type` = 'junk' AND
		`miniblog`.`parent` != 0
	GROUP BY `miniblog`.`author` ORDER BY count(*) DESC LIMIT 10


/*
|--------------------------------------------------------------------------
|   Miniblogi
|--------------------------------------------------------------------------
*/

miniblogi kopā ------------------------------------------------------------ 27342

	SELECT count(*) FROM `miniblog`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`lang` IN(0, 1) AND
		`miniblog`.`type` = 'miniblog' AND
		`miniblog`.`parent` = 0
		
	// visvairāk
	SELECT `users`.`nick`, count(*) FROM `miniblog`
		JOIN `users` ON `miniblog`.`author` = `users`.`id`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`lang` IN(0, 1) AND
		`miniblog`.`type` = 'miniblog' AND
		`miniblog`.`parent` = 0
	GROUP BY `miniblog`.`author` ORDER BY count(*) DESC LIMIT 10
	

miniblogu komentāri kopā -------------------------------------------------- 369878

	SELECT count(*) FROM `miniblog`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`lang` IN(0, 1) AND
		`miniblog`.`type` = 'miniblog' AND
		`miniblog`.`parent` != 0
		
	// visvairāk
	SELECT `users`.`nick`, count(*) FROM `miniblog`
		JOIN `users` ON `miniblog`.`author` = `users`.`id`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`lang` IN(0, 1) AND
		`miniblog`.`type` = 'miniblog' AND
		`miniblog`.`parent` != 0
	GROUP BY `miniblog`.`author` ORDER BY count(*) DESC LIMIT 10



miniblogi grupās ---------------------------------------------------------- 10679

	SELECT count(*) FROM `miniblog`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`lang` IN(0, 1) AND
		`miniblog`.`type` = 'miniblog' AND
		`miniblog`.`parent` = 0 AND
		`miniblog`.`groupid` != 0

	// visvairāk
	SELECT `users`.`nick`, count(*) FROM `miniblog`
		JOIN `users` ON `miniblog`.`author` = `users`.`id`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`lang` IN(0, 1) AND
		`miniblog`.`type` = 'miniblog' AND
		`miniblog`.`parent` = 0 AND
		`miniblog`.`groupid` != 0
	GROUP BY `miniblog`.`author` ORDER BY count(*) DESC LIMIT 10
		
miniblogu komentāri grupās ------------------------------------------------ 131639

	SELECT count(*) FROM `miniblog`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`lang` IN(0, 1) AND
		`miniblog`.`type` = 'miniblog' AND
		`miniblog`.`parent` != 0 AND
		`miniblog`.`groupid` != 0
		
	// visvairāk
	SELECT `users`.`nick`, count(*) FROM `miniblog`
		JOIN `users` ON `miniblog`.`author` = `users`.`id`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`lang` IN(0, 1) AND
		`miniblog`.`type` = 'miniblog' AND
		`miniblog`.`parent` != 0 AND
		`miniblog`.`groupid` != 0
	GROUP BY `miniblog`.`author` ORDER BY count(*) DESC LIMIT 10



miniblogi RS apakšprojektā ------------------------------------------------ 2263

	SELECT count(*) FROM `miniblog`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`lang` = 9 AND
		`miniblog`.`type` = 'miniblog' AND
		`miniblog`.`parent` = 0
		
	// visvairāk
	SELECT `users`.`nick`, count(*) FROM `miniblog`
		JOIN `users` ON `miniblog`.`author` = `users`.`id`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`lang` = 9 AND
		`miniblog`.`type` = 'miniblog' AND
		`miniblog`.`parent` = 0
	GROUP BY `miniblog`.`author` ORDER BY count(*) DESC LIMIT 10

miniblogu komentāri RS apakšprojektā -------------------------------------- 14211

	SELECT count(*) FROM `miniblog`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`lang` = 9 AND
		`miniblog`.`type` = 'miniblog' AND
		`miniblog`.`parent` != 0
		
	// visvairāk
	SELECT `users`.`nick`, count(*) FROM `miniblog`
		JOIN `users` ON `miniblog`.`author` = `users`.`id`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`lang` = 9 AND
		`miniblog`.`type` = 'miniblog' AND
		`miniblog`.`parent` != 0
	GROUP BY `miniblog`.`author` ORDER BY count(*) DESC LIMIT 10

miniblogi LOL apakšprojektā ----------------------------------------------- 2152

	SELECT count(*) FROM `miniblog`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`lang` = 7 AND
		`miniblog`.`type` = 'miniblog' AND
		`miniblog`.`parent` = 0
		
	SELECT `users`.`nick`, count(*) FROM `miniblog`
		JOIN `users` ON `miniblog`.`author` = `users`.`id`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`lang` = 7 AND
		`miniblog`.`type` = 'miniblog' AND
		`miniblog`.`parent` = 0
	GROUP BY `miniblog`.`author` ORDER BY count(*) DESC LIMIT 10

miniblogu komentāri LOL apakšprojektā ------------------------------------- 21720

	SELECT count(*) FROM `miniblog`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`lang` = 7 AND
		`miniblog`.`type` = 'miniblog' AND
		`miniblog`.`parent` != 0
		
	// visvairāk
	SELECT `users`.`nick`, count(*) FROM `miniblog`
		JOIN `users` ON `miniblog`.`author` = `users`.`id`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`lang` = 7 AND
		`miniblog`.`type` = 'miniblog' AND
		`miniblog`.`parent` != 0
	GROUP BY `miniblog`.`author` ORDER BY count(*) DESC LIMIT 10



vidējais rakstzīmju skaits miniblogos ------------------------------------- 101.8044

	SELECT avg(length(`miniblog`.`text`)) AS `average_length` FROM `miniblog`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`lang` IN(0, 1) AND
		`miniblog`.`type` = 'miniblog'


/*
|--------------------------------------------------------------------------
|   coding.lv
|--------------------------------------------------------------------------
*/

miniblogi coding.lv ------------------------------------------------------- 367

	SELECT count(*) FROM `miniblog`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`lang` = 3 AND
		`miniblog`.`type` = 'miniblog' AND
		`miniblog`.`parent` = 0
		
	// visvairāk
	SELECT `users`.`nick`, count(*) FROM `miniblog`
		JOIN `users` ON `miniblog`.`author` = `users`.`id`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`lang` = 3 AND
		`miniblog`.`type` = 'miniblog' AND
		`miniblog`.`parent` = 0
	GROUP BY `miniblog`.`author` ORDER BY count(*) DESC LIMIT 10

miniblogu komentāri coding.lv --------------------------------------------- 2413

	SELECT count(*) FROM `miniblog`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`lang` = 3 AND
		`miniblog`.`type` = 'miniblog' AND
		`miniblog`.`parent` != 0
		
	// visvairāk
	SELECT `users`.`nick`, count(*) FROM `miniblog`
		JOIN `users` ON `miniblog`.`author` = `users`.`id`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`lang` = 3 AND
		`miniblog`.`type` = 'miniblog' AND
		`miniblog`.`parent` != 0
	GROUP BY `miniblog`.`author` ORDER BY count(*) DESC LIMIT 10

raksti (tai skaitā tēmas forumā) coding.lv forumā ------------------------- 45

	SELECT count(*) FROM `pages`
		JOIN `cat` ON `pages`.`category` = `cat`.`id`
	WHERE
		`pages`.`category` NOT IN(6, 244, 1133, 1904, 1972) AND
		`pages`.`date` > '2014-12-31 23:59:59' AND
		`pages`.`date` < '2016-01-01 00:00:00' AND
		`cat`.`isblog` = 0 AND
		`pages`.`lang` = 3
	// sadaļas ir dažādu apakšprojektu musari
	
	// visvairāk
	SELECT `users`.`nick`, count(*) FROM `pages`
		JOIN `users` ON `pages`.`author` = `users`.`id`
		JOIN `cat` ON `pages`.`category` = `cat`.`id`
	WHERE
		`pages`.`category` NOT IN(6, 244, 1133, 1904, 1972) AND
		`pages`.`date` > '2014-12-31 23:59:59' AND
		`pages`.`date` < '2016-01-01 00:00:00' AND
		`cat`.`isblog` = 0 AND
		`pages`.`lang` = 3
	GROUP BY `pages`.`author` ORDER BY count(*) DESC LIMIT 10

rakstu (tai skaitā foruma tēmu) komentāri coding.lv forumā ---------------- 334

	SELECT count(*) FROM `comments`
		JOIN `pages` ON `comments`.`pid` = `pages`.`id`
		JOIN `cat` ON `pages`.`category` = `cat`.`id`
	WHERE
		`comments`.`date` > '2014-12-31 23:59:59' AND
		`comments`.`date` < '2016-01-01 00:00:00' AND
		`pages`.`category` NOT IN(6, 244, 1133, 1904, 1972) AND
		`pages`.`lang` = 3 AND
		`cat`.`isblog` = 0
	// sadaļas ir dažādu apakšprojektu musari
	
	// visvairāk
	SELECT `users`.`nick`, count(*) FROM `comments`
		JOIN `pages` ON `comments`.`pid` = `pages`.`id`
		JOIN `cat` ON `pages`.`category` = `cat`.`id`
		JOIN `users` ON `comments`.`author` = `users`.`id`
	WHERE
		`comments`.`date` > '2014-12-31 23:59:59' AND
		`comments`.`date` < '2016-01-01 00:00:00' AND
		`pages`.`category` NOT IN(6, 244, 1133, 1904, 1972) AND
		`pages`.`lang` = 3 AND
		`cat`.`isblog` = 0
	GROUP BY `comments`.`author` ORDER BY count(*) DESC LIMIT 10


/*
|--------------------------------------------------------------------------
|   exs.lv
|--------------------------------------------------------------------------
*/


gada konkurss -------------------------------------------------------------

	Rakstu konkurss: https://exs.lv/read/rakstu-konkurss-balvu-fonds-350


eXs publisko pasākumu skaits ---------------------------------------------- 0?

cilvēku skaits, kas apmeklēja eXs pasākumus ------------------------------- 0?



jauni reģistrētie lietotāji ----------------------------------------------- 2534

	SELECT count(*) FROM `users`
	WHERE
		`users`.`date` > '2014-12-31 23:59:59' AND
		`users`.`date` < '2016-01-01 00:00:00' AND
		`users`.`deleted` = 0


/*
|--------------------------------------------------------------------------
|   medaļas
|--------------------------------------------------------------------------
*/


lietotājiem automātiski piešķirtās medaļas -------------------------------- 8468

	SELECT count(*) FROM `autoawards`
	WHERE
		`autoawards`.`created` > '2014-12-31 23:59:59' AND
		`autoawards`.`created` < '2016-01-01 00:00:00'
		
	--- kuram visvairāk medaļu
		
	SELECT `users`.`nick`, count(*) AS `award_count` FROM `autoawards`
		JOIN `users` ON `autoawards`.`user_id` = `users`.`id`
	WHERE
		`autoawards`.`created` > '2014-12-31 23:59:59' AND
		`autoawards`.`created` < '2016-01-01 00:00:00'
	GROUP BY `autoawards`.`user_id`
	ORDER BY count(*) DESC
	LIMIT 0, 10

lietotājiem piešķirtās profila medaļas ------------------------------------ 0


/*
|--------------------------------------------------------------------------
|   visvairāk komentētais
|--------------------------------------------------------------------------
*/

visvairāk komentētais miniblogs -------------------------------------------
	(ir daudz tādu, kas slēgti pie 500 komentāriem, tāpēc nav interesanti)

	SELECT
		`miniblog`.`id`,
		`miniblog`.`groupid`,
		`miniblog`.`parent`,
		`miniblog`.`text`,
		`miniblog`.`posts`,
		`miniblog`.`author`,
		`users`.`nick`
	FROM `miniblog`
		JOIN `users` ON `miniblog`.`author` = `users`.`id`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`lang` IN(0, 1) AND
		`miniblog`.`type` = 'miniblog' AND
		`miniblog`.`parent` = 0 AND
		`miniblog`.`posts` < 500
	ORDER BY `posts` DESC
	LIMIT 5

visvairāk komentētā bilde ------------------------------------------------

	https://exs.lv/gallery/23678/65988

	SELECT
		`images`.`id`,
		`images`.`url`,
		`images`.`thb`,
		`images`.`uid`,
		`users`.`nick`
	FROM `images`
		JOIN `users` ON `images`.`uid` = `users`.`id`
	WHERE
		`images`.`date` > '2014-12-31 23:59:59' AND
		`images`.`date` < '2016-01-01 00:00:00' AND
		`images`.`lang` IN(0, 1)
	ORDER BY `images`.`posts` DESC
	LIMIT 5

visvairāk komentētā junk bilde -------------------------------------------

	https://exs.lv/junk/16181

	SELECT
		`junk`.`id`,
		`junk`.`image`,
		`junk`.`thb`,
		`junk`.`author`,
		`users`.`nick`
	FROM `junk`
		JOIN `users` ON `junk`.`author` = `users`.`id`
	WHERE
		`junk`.`date` > '2014-12-31 23:59:59' AND
		`junk`.`date` < '2016-01-01 00:00:00' AND
		`junk`.`lang` IN(0, 1) AND
		`junk`.`removed` = 0
	ORDER BY `junk`.`posts` DESC
	LIMIT 5


/*
|--------------------------------------------------------------------------
|   plusiņi/mīnusiņi
|--------------------------------------------------------------------------
*/

mīnusotākais komentārs ----------------------------------------------------

	https://exs.lv/say/16111/4392975-iesakiet-kadu-seksa-komediju#m4393178

	SELECT
		`miniblog`.`id`,
		`miniblog`.`groupid`,
		`miniblog`.`parent`,
		`miniblog`.`reply_to`,
		`miniblog`.`author`,
		`miniblog`.`text`,
		`miniblog`.`vote_value`
	FROM `miniblog`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`lang` IN(0, 1) AND
		`miniblog`.`type` = 'miniblog'
	ORDER BY `miniblog`.`vote_value` ASC
	LIMIT 5

plusotākais komentārs -----------------------------------------------------

	https://exs.lv/say/13034/4432441-www-straightouttasomewhere-com#m4432485

	SELECT
		`miniblog`.`id`,
		`miniblog`.`groupid`,
		`miniblog`.`parent`,
		`miniblog`.`reply_to`,
		`miniblog`.`author`,
		`miniblog`.`text`,
		`miniblog`.`vote_value`
	FROM `miniblog`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`lang` IN(0, 1) AND
		`miniblog`.`type` = 'miniblog'
	ORDER BY `miniblog`.`vote_value` DESC
	LIMIT 5

plusu summa --------------------------------------------------------------- 249289

	SELECT
		SUM(`vote_value`) AS `pluses`
	FROM `miniblog`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`lang` IN(0, 1) AND
		`miniblog`.`type` = 'miniblog' AND
		`miniblog`.`vote_value` > 0
		
	// visvairāk
	SELECT
		`users`.`nick`,
		SUM(`vote_value`) AS `pluses`
	FROM `miniblog`
		JOIN `users` ON `miniblog`.`author` = `users`.`id`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`lang` IN(0, 1) AND
		`miniblog`.`type` = 'miniblog' AND
		`miniblog`.`vote_value` > 0
	GROUP BY `miniblog`.`author` ORDER BY SUM(`vote_value`) DESC LIMIT 10

mīnusu summa -------------------------------------------------------------- -88416

	SELECT
		SUM(`vote_value`) AS `minuses`
	FROM `miniblog`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`lang` IN(0, 1) AND
		`miniblog`.`type` = 'miniblog' AND
		`miniblog`.`vote_value` < 0
		
	// vismazāk
	SELECT
		`users`.`nick`,
		SUM(`vote_value`) AS `minuses`
	FROM `miniblog`
		JOIN `users` ON `miniblog`.`author` = `users`.`id`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`lang` IN(0, 1) AND
		`miniblog`.`type` = 'miniblog' AND
		`miniblog`.`vote_value` < 0
	GROUP BY `miniblog`.`author` ORDER BY SUM(`vote_value`) ASC LIMIT 10

vērtējumu summa gada laikā ------------------------------------------------ 160873

	SELECT
		SUM(`vote_value`) AS `total_value`
	FROM `miniblog`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`lang` IN(0, 1) AND
		`miniblog`.`type` = 'miniblog'
		
	// visvairāk
	SELECT
		`users`.`nick`,
		SUM(`vote_value`) AS `total_value`
	FROM `miniblog`
		JOIN `users` ON `miniblog`.`author` = `users`.`id`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`lang` IN(0, 1) AND
		`miniblog`.`type` = 'miniblog'
	GROUP BY `miniblog`.`author` ORDER BY SUM(`vote_value`) DESC LIMIT 10



/*
|--------------------------------------------------------------------------
|   sūdzības/brīdinājumi/liegumi
|--------------------------------------------------------------------------
*/

iesniegto ziņojumu/sūdzību skaits ----------------------------------------- 604

	http://www.epochconverter.com/

	SELECT count(*) FROM `reports`
	WHERE
		`reports`.`created_at` > '1420070399' AND
		`reports`.`created_at` < '1451606400' AND
		`reports`.`removed` = 0 AND
		`reports`.`site_id` IN(0, 1)
		
	// visvairāk
	SELECT `users`.`nick`, count(*) FROM `reports`
		JOIN `users` ON `reports`.`created_by` = `users`.`id`
	WHERE
		`reports`.`created_at` > '1420070399' AND
		`reports`.`created_at` < '1451606400' AND
		`reports`.`removed` = 0 AND
		`reports`.`site_id` IN(0, 1)
	GROUP BY `reports`.`created_by` ORDER BY count(*) DESC LIMIT 10

uzlikto brīdinājumu skaits ------------------------------------------------ 1493

	SELECT count(*) FROM `warns`
	WHERE
		`warns`.`created` > '2014-12-31 23:59:59' AND
		`warns`.`created` < '2016-01-01 00:00:00' AND
		`warns`.`site_id` IN(0, 1)
	
	// visvairāk "kam"
	SELECT `users`.`nick`, count(*) FROM `warns`
		JOIN `users` ON `warns`.`user_id` = `users`.`id`
	WHERE
		`warns`.`created` > '2014-12-31 23:59:59' AND
		`warns`.`created` < '2016-01-01 00:00:00' AND
		`warns`.`site_id` IN(0, 1)
	GROUP BY `warns`.`user_id` ORDER BY count(*) DESC LIMIT 10


piemēroto liegumu skaits -------------------------------------------------- 666

	http://www.epochconverter.com/

	SELECT count(*) FROM `banned`
	WHERE
		`banned`.`time` > '1420070399' AND
		`banned`.`time` < '1451606400' AND
		`banned`.`lang` IN(0, 1)
		
	// visvairāk "kam"
	SELECT
		`users`.`nick`,
		count(*) AS `times_banned`
	FROM `banned`
		JOIN `users` ON `banned`.`user_id` = `users`.`id`
	WHERE
		`banned`.`time` > '1420070399' AND
		`banned`.`time` < '1451606400' AND
		`banned`.`lang` IN(0, 1)
	GROUP BY `banned`.`user_id` ORDER BY count(*) DESC LIMIT 10


/*
|--------------------------------------------------------------------------
|   random
|--------------------------------------------------------------------------
*/

izveidoto grupu skaits ---------------------------------------------------- 22

	http://www.epochconverter.com/

	SELECT count(*) FROM `clans`
	WHERE
		`clans`.`date_created` > '1420070399' AND
		`clans`.`date_created` < '1451606400' AND
		`clans`.`lang` IN(0, 1)

izspēlēto desas partiju skaits -------------------------------------------- 152

	http://www.epochconverter.com/

	SELECT count(*) FROM `desas`
	WHERE
		`desas`.`created` > '2014-12-31 23:59:59' AND
		`desas`.`created` < '2016-01-01 00:00:00'

pieci aktīvākie lietotāji (miniblogu un to komentāru skaita ziņā) ---------

	* Lukijs 9204
	* pj sk1ll 7822
	* pankijs 7006
	* MGP 5840
	* Kāvējs 5673
	* ...

	SELECT
		`users`.`nick`,
		count(*) AS `comments`
	FROM `miniblog`
		JOIN `users` ON `miniblog`.`author` = `users`.`id`
	WHERE
		`miniblog`.`date` > '2014-12-31 23:59:59' AND
		`miniblog`.`date` < '2016-01-01 00:00:00' AND
		`miniblog`.`removed` = 0 AND
		`miniblog`.`lang` IN(0, 1) AND
		`miniblog`.`type` = 'miniblog'
	GROUP BY `miniblog`.`author`
	ORDER BY count(*) DESC
	LIMIT 10


/*
|--------------------------------------------------------------------------
|   Administrācijas lietas
|--------------------------------------------------------------------------
*/

aktīvākais banotājs --------------------------------------------------------

	alberts00 - 216

	SELECT
		`users`.`nick`,
		count(*) AS `times_banned`
	FROM `banned`
		JOIN `users` ON `banned`.`author` = `users`.`id`
	WHERE
		`banned`.`time` > '1420070399' AND
		`banned`.`time` < '1451606400' AND
		`banned`.`lang` IN(0, 1)
	GROUP BY `banned`.`author`
	ORDER BY count(*) DESC
	LIMIT 10

aktīvākais brīdinātājs -----------------------------------------------------

	Styrnucis - 279

	SELECT 
		`users`.`nick`,
		count(*) AS `times_warned`
	FROM `warns`
		JOIN `users` ON `warns`.`created_by` = `users`.`id`
	WHERE
		`warns`.`created` > '2014-12-31 23:59:59' AND
		`warns`.`created` < '2016-01-01 00:00:00' AND
		`warns`.`site_id` IN(0, 1)
	GROUP BY `warns`.`created_by`
	ORDER BY count(*) DESC
	LIMIT 10

aktīvākais sūdzību izskatītājs ---------------------------------------------

	krabz - 281

	SELECT
		`users`.`nick`,
		count(*) AS `times_archived`
	FROM `reports`
		JOIN `users` ON `reports`.`deleted_by` = `users`.`id`
	WHERE
		`reports`.`created_at` > '1420070399' AND
		`reports`.`created_at` < '1451606400' AND
		`reports`.`removed` = 0 AND
		`reports`.`site_id` IN(0, 1)
	GROUP BY `reports`.`deleted_by`
	ORDER BY count(*) DESC
	LIMIT 10

