<?php
$tpl->newBlock('blogs-body');

$articles = $db->get_results(
						"SELECT
								`pages`.`title` AS `title`,
								`pages`.`intro` AS `intro`,
								`pages`.`text` AS `text`,
								`pages`.`author` AS `authorid`,
								`users`.`avatar` AS `avatar`,
								`users`.`nick` AS `nick`,
								`pages`.`id` AS `id`
						FROM
								`pages`,
								`users`,
								`cat`
						WHERE
								`pages`.`category`=`cat`.`id` AND
								`cat`.`isblog`!='0' AND
								`users`.`id`=`pages`.`author`
		        ORDER BY
							`pages`.`date` DESC
						LIMIT 15");

if($articles) {
	foreach($articles as $article) {
		$tpl->newBlock('blogs-featured');
		if($article->avatar == '') {$article->avatar = $config['default_user_avatar'];}
    $article->avatar = '/dati/bildes/useravatar/' . $article->avatar;

		if(!empty($article->intro)) {
      $article->text = $article->intro;
		} else {
			$article->text = textlimit(strip_tags(trim(str_replace(array('&nbsp;','<br />'),' ',$article->text))),$config['article_intro_len']);
			$article->text = preg_replace("#(^|[\n ]|<a(.*?)>)http://(www\.)?youtube\.com/watch\?v=([a-zA-Z0-9\-_]+)(</a>)?#im",'<div class="video-thb"><a href="'.mkurl('page',$article->id,$article->title).'"><img src="http://img.youtube.com/vi/$4/2.jpg" alt="" /></a><p>Youtube video</p><div class="c"></div></div>', $article->text,1);
			$videoid = get_between($article->text,'img.youtube.com/vi/','/2.jpg"');
			if($videoid) {
				$contents = file_get_contents('http://gdata.youtube.com/feeds/api/videos/' . $videoid);
				$v_description = '<strong><a href="'.mkurl('page',$article->id,$article->title).'">'.get_between($contents,"<media:title type='plain'>",'</media:title>').'</a></strong> <br />';
				$v_description .= textlimit(get_between($contents,"<media:description type='plain'>",'</media:description>'),270);
				$article->text = str_replace('<p>Youtube video</p>','<p>'.$v_description.'</p>',$article->text);
			}
			$article->intro = sanitize($article->text);
			$db->query("UPDATE pages SET intro = '$article->intro' WHERE id = '$article->id' LIMIT 1");
		}


		$tpl->assign(array(
			'newest-title' => textlimit($article->title,52),
			'newest-text' => textlimit(strip_tags($article->text),140),
			'url' => mkurl('page',$article->id,$article->title),
		  'newest-author-id' => $article->authorid,
		  'newest-author-avatar' => $article->avatar,
		  'newest-author-title' => htmlspecialchars($article->nick),
		));
	}
}



?>