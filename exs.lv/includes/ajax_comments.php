<?php

function comments_block($parent = 'null', $ajax = false) {
	global $db, $auth;

	$tpl_com = new TemplatePower(CORE_PATH . '/modules/core/comments-ajax.tpl');
	$tpl_com->prepare();

	if ($auth->ok && isset($_POST['new-c-text']) && !empty($_POST['new-c-text'])) {

		if (!isset($_SESSION['antiflood']) or $_SESSION['antiflood'] < time() - 2) {
			$_SESSION["antiflood"] = time();
			$text = post2db($_POST['new-c-text']);
			$parent = sanitize($parent);
			$db->query("INSERT INTO ajax_comments (user_id,parent,time,ip,text) VALUES ('$auth->id','$parent','" . time() . "','$auth->ip','$text')");
		} else {
			die('<small>Floods! Pagaidi 2 sekundes, pirms pievieno jaunu komentāru.</small>');
		}
	}

	if (!$ajax) {
		$comments = $db->get_results("SELECT
      `ajax_comments`.`id` AS `id`,
      `ajax_comments`.`text` AS `text`,
      `ajax_comments`.`time` AS `time`,
      `ajax_comments`.`user_id` AS `user_id`,
      `users`.`nick` AS `nick`,
      `users`.`avatar` AS `avatar`,
      `users`.`av_alt` AS `av_alt`,
      `users`.`level` AS `level`
  	FROM
  		`ajax_comments`,
  		`users`
  	WHERE
  		`ajax_comments`.`parent` = '$parent' AND
  		`users`.`id` = `ajax_comments`.`user_id`
  	ORDER BY
  		`ajax_comments`.`time` DESC LIMIT 50");

		$tpl_com->newBlock('comments-ajax-list');

		if ($comments) {
			foreach ($comments as $comment) {

				$avatar = get_avatar($comment, 's');

				$tpl_com->newBlock('comments-ajax-node');
				$tpl_com->assign(array(
					'nick' => $comment->nick,
					'text' => add_smile($comment->text),
					'avatar' => $avatar,
					'date' => display_time($comment->time),
				));
			}
		} else {
			$tpl_com->newBlock('comments-ajax-empty');
		}

		$tpl_com->newBlock('comments-ajax-list-end');

		if ($auth->ok) {
			$tpl_com->newBlock('comments-ajax-form');
		}
	} else {
		$comments = $db->get_results("SELECT
      `ajax_comments`.`id` AS `id`,
      `ajax_comments`.`text` AS `text`,
      `ajax_comments`.`time` AS `time`,
      `ajax_comments`.`user_id` AS `user_id`,
      `users`.`nick` AS `nick`,
      `users`.`avatar` AS `avatar`,
      `users`.`av_alt` AS `av_alt`,
      `users`.`level` AS `level`
  	FROM
  		`ajax_comments`,
  		`users`
  	WHERE
  		`ajax_comments`.`parent` = '$parent' AND
  		`ajax_comments`.`id` > '" . intval($ajax) . "' AND
  		`users`.`id` = `ajax_comments`.`user_id`
  	ORDER BY
  		`ajax_comments`.`time` ASC");

		$json = array();
		if ($comments) {
			foreach ($comments as $comment) {

				$avatar = get_avatar($comment, 's');

				$json['comment'][] = '<img src="' . $avatar . '" class="av" /><p class="comment-author"><strong>' . $comment->nick . '</strong> ' . display_time($comment->time) . '</p>' . add_smile($comment->text);
			}
			$json['id'] = $comment->id;
		}
		header("Content-type: application/json");
		echo json_encode($json);
		exit;
	}

	return $tpl_com->getOutputContent();
}
