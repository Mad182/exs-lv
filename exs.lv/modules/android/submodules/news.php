<?php

/**
 *  Android rakstu apakšmodulis
 *
 *  Rakstu lasīšana, komentēšana, vērtēšana
 */

// nebūs iespējams skatīt failu pa tiešo
!isset($sub_include) and die('Error loading page!');


/**
 *  Norādīts kāda, iespējams, eksistējoša raksta id
 *  
 *  Šajā blokā notiek raksta lasīšana, komentēšana un 
 *  komentāru vērtēšana
 */
if (isset($_GET['var1'])) {

	// atlasa norādīta raksta informāciju
	$article = $db->get_row("
		SELECT 
			`pages`.`id`        AS `id`,
			`pages`.`strid`     AS `strid`,
			`pages`.`title`     AS `title`, 
			`pages`.`text`      AS `text`,
			`pages`.`author`    AS `author`,
			`pages`.`date`      AS `date`,
			`pages`.`closed`    AS `closed`,
			`pages`.`posts`     AS `posts`,
			`pages`.`views`     AS `views`,
			`cat`.`title`       AS `category`
		FROM `pages`
			JOIN `cat` ON `pages`.`category` = `cat`.`id`
		WHERE 
			`pages`.`id` = '".(int)$_GET['var1']."' AND
			(`pages`.`lang` = '$api_lang' OR `pages`.`lang` = 0)
	");
	
	if (!$article) {
		api_error('Raksts nav atrasts');
	} else {
	
		// ieraksta vērtēšana
		if (isset($_GET['var2']) && isset($_GET['var3']) &&
			in_array($_GET['var2'], array('plus', 'minus'))) {

			if ($_GET['var2'] == 'plus') {
				api_rate_comment((int)$_GET['var3'], 'article', true);
			} else {
				api_rate_comment((int)$_GET['var3'], 'article', false);
			}
		}
		
		// jauna komentāra pievienošana
		else if (isset($_POST['comment'])) {
			api_add_article_comment($article);
		}
		
		// raksta satura skatīšana
		else {
		
			$author = get_user($article->author);
			$key = substr(md5($article->id . $remote_salt . $auth->id), 0, 5);

			// dati par pašu rakstu
			$about_article = array(
				'id'        => (int)$article->id,
				'title'     => $article->title,
				'text'      => $article->text,
				'date'      => display_time(strtotime($article->date)),
				'category'  => $article->category,
				'author'    => api_fetch_user($author->id, $author->nick,
											$author->level),
				'closed'    => (bool)$article->closed,
				'safe'      => $key
			);
		
			// atlasa raksta komentārus, ja tādi maz ir
			$comments = $db->get_results("
				SELECT 
					`comments`.`id`         AS `id`,
					`comments`.`text`       AS `text`,
					`comments`.`date`       AS `date`,
					`comments`.`replies`    AS `replies`,
					`comments`.`author`     AS `author`,
					`comments`.`vote_value` AS `vote`,
					`comments`.`vote_users` AS `voted_by`
				FROM 
					`comments`
				WHERE 
					`comments`.`pid` = '".(int)$_GET['var1']."' AND 
					`comments`.`parent` = 0 AND 
					`comments`.`removed` = 0 
				ORDER BY `comments`.`id` ASC
			");
			
			// komentāru datu masīvs
			$arr_comments  = array();
			
			if ($comments) {
				foreach ($comments as $comment) {
					
					$author = get_user($comment->author);
					$key = substr(md5($comment->id . $remote_salt . $auth->id),
								  0, 5);
					
					// noskaidro, vai šo ierakstu lietotājs jau ir vērtējis
					$voters = array();
					$voted = true;
					if (!empty($comment->voted_by)) {
						$voters = unserialize($comment->voted_by);
					}
					if (!in_array($auth->id, $voters)) {
						$voted = false;
					}
					
					$arr_comments[] = array(
						'id'        => (int)$comment->id,
						'text'      => $comment->text,
						'date'      => display_time(strtotime($comment->date)),
						'replies'   => (int)$comment->replies,
						'vote'      => (int)$comment->vote,
						'voted'     => (bool)$voted,
						'author'    => api_fetch_user($author->id, $author->nick,
													$author->level),
						'avatar'    => api_get_user_avatar($author, 's'),
						'safe'      => $key
					);
				}
			}
			
			// atgriežamais saturs
			$json_page = array(
				'content'   => $about_article,
				'comments'  => $arr_comments
			);        
		}
		
	}
}


/**
 *  Visos pārējos gadījumos atgriezīs sarakstu ar jaunākajiem rakstiem
 */
else {    
	$json_page = api_get_news();
}
