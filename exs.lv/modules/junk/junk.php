<?php

/**
 * exs.lv/junk sadaļas apskate un attēlu iesniegšana
 */
$add_css[] = 'junk.css';

if (isset($_GET['var1']) && $_GET['var1'] == 'top') {
	echo 'top';
} elseif (isset($_GET['var1']) && $_GET['var1'] == 'add') {
	if ($auth->ok) {
		$page_title = 'Pievienot attēlu';
		$tpl->newBlock('junk-add');

		if (isset($_FILES['new-image'])) {

			ini_set('memory_limit', '200M');

			$title = sanitize(nl2br(h($_POST['title'])));
			require_once(CORE_PATH . '/includes/class.upload.php');

			$foo = new Upload($_FILES['new-image']);
			$foo->image_max_pixels = 200000000;
			$foo->mime_check = true;
			$foo->no_script = true;
			$foo->file_max_size = '8M';
			$foo->allowed = array('image/*');

			if ($foo->image_src_type == 'bmp') {
				$foo->image_convert = 'png';
			}

			//saglabā gifus kustīgus
			if ($foo->image_src_type != 'gif') {
				$foo->image_resize = true;
				$foo->image_ratio = true;
				$foo->image_y = 1800;
				$foo->image_x = 900;
				$foo->image_ratio_no_zoom_in = true;
			}

			$foo->process('/home/www/exs.lv/tmp/');

			if ($foo->processed) {

				$db->query("INSERT INTO `junk_queue`
					(image,title,source,created,ip,user_id) VALUES
					('https://exs.lv/tmp/" . sanitize($foo->file_dst_name) . "','$title','user',NOW(),'$auth->ip','$auth->id')
				");
				set_flash('Attēls pievienots! Modi apskatīsies un ja būs OK, apstiprinās ;)', 'success');
				redirect('/junk/add');
			} else {
				set_flash('Kļūda: ' . $foo->error, 'error');
			}
		}
	} else {
		set_flash('Jāielogojas, lai pievienotu attēlu!', 'error');
		redirect('/junk');
	}
} elseif (isset($_GET['var1']) && $_GET['var1'] == 'random') {
	redirect('/' . $category->textid . '/' . $db->get_var("SELECT `id` FROM `junk`  WHERE `removed` = 0 ORDER BY rand() LIMIT 1") . '#content');
} elseif (isset($_GET['var1']) && $_GET['var1'] == 'commented') {

	if (isset($_GET['skip'])) {
		$skip = (int) $_GET['skip'];
	} else {
		$skip = 0;
	}

	$junks = $db->get_results("SELECT * FROM `junk` WHERE `removed` = 0 AND `posts` > 0 ORDER BY `bump` DESC LIMIT $skip,120");

	if (!empty($junks)) {
		$date = null;
		$tpl->newBlock('junk');
		foreach ($junks as $junk) {
			$thisdate = display_date_simple($junk->bump);
			if ($date != $thisdate) {
				$tpl->newBlock('junk-item-date');
				$tpl->assign(array(
					'date' => $thisdate
				));
				$date = $thisdate;
			}

			$tpl->newBlock('junk-item');
			$tpl->assignAll($junk);
		}
	}

	$total = $db->get_var("SELECT count(*) FROM `junk` WHERE `removed` = 0 AND `posts` > 0");
	$pager = pager($total, $skip, 120, '/junk/commented/?skip=');
	$tpl->assignGlobal(array(
		'pager-next' => $pager['next'],
		'pager-prev' => $pager['prev'],
		'pager-numeric' => $pager['pages']
	));
} elseif (isset($_GET['var1'])) {
	$id = (int) $_GET['var1'];
	$pic = $db->get_row("SELECT * FROM `junk` WHERE `removed` = 0 AND `id` = '$id'");
	if (!empty($pic)) {

		if ($auth->ok &&
				isset($_GET['var2']) &&
				($_GET['var2'] == 'upvote' || $_GET['var2'] == 'downvote') &&
				substr(md5($remote_salt . '-' . $auth->id . '-' . $pic->id), 0, 6) == $_GET['check']
		) {

			if (!$db->get_var("SELECT count(*) FROM `junk_votes` WHERE `junk_id` = $pic->id AND `user_id` = $auth->id")) {
				if ($_GET['var2'] == 'upvote') {
					$add = 1;
				} else {
					$add = -1;
				}
				$db->query("INSERT INTO `junk_votes` (junk_id,user_id,value,created) VALUES  ('$pic->id','$auth->id','$add',NOW())");
			}
			echo junk_vote($pic->id, $auth->id);
			exit;
		}


		$tpl->newBlock('junk-view');
		$add = '';
		if (im_mod()) {
			$add = ' [<a href="/junk-edit/' . $pic->id . '">labot</a>]';
		}

		if(substr($pic->image, -4) === 'gifv') {
			$html = '<iframe class="embedded-iframe" src="'.$pic->image.'#embed" ';
			$html .= 'allowfullscreen="" frameborder="0" scrolling="no" ';
			$html .= 'width="100%" style="background:transparent" height="400"></iframe>';
		} else {
			$html = '<p style="text-align:center"><img src="//img.exs.lv'.$pic->image.'" class="av" style="height:auto;width:auto;float:none" alt="'.h(strip_tags($pic->title)).'" title="'.h(strip_tags($pic->title)).'" /></p>';
		}

		$tpl->assign(array(
			'voter' => junk_vote($pic->id, $auth->id),
			'title' => $pic->title . $add,
			'title-html' => h($pic->title),
			'image' => $html,
			'id' => $pic->id,
		));

		if ($pic->author) {
			$author = get_user($pic->author);
			$tpl->newBlock('junk-view-author');
			$tpl->assign(array(
				'nick' => usercolor($author->nick, $author->level, false, $author->id),
				'id' => $author->id
			));
		} else {
			if(empty($pic->edit_user) && $pic->posts < 10) {
				$robotstag = array('noindex', 'follow');
			}
		}

		$page_title = h($pic->title);

		$next = $db->get_var("SELECT `id` FROM `junk` WHERE `removed` = 0 AND `id` > '$id' ORDER BY `id` ASC LIMIT 1");
		if ($next) {
			$tpl->newBlock('junk-next');
			$tpl->assign(array(
				'id' => $next
			));
		}

		$prev = $db->get_var("SELECT `id` FROM `junk` WHERE `removed` = 0 AND `id` < '$id' ORDER BY `id` DESC LIMIT 1");
		if ($prev) {
			$tpl->newBlock('junk-prev');
			$tpl->assign(array(
				'id' => $prev
			));
		}

		$url = '/junk/' . $pic->id;

		if ($auth->ok === true && isset($_POST['responseminiblog']) && !empty($_POST['responseminiblog'])) {

			$to = (int) $_POST['response-to'];

			if (!isset($_POST['token']) || $_POST['token'] != md5('mb' . intval($_GET['var1']) . $remote_salt . $auth->nick)) {
				set_flash('Kļūdains pieprasījums! Hacking around?');
				redirect();
			}

			if (get_mb_level($to) > 1 && $auth->level != 1) {
				die('Too deep ;(');
			}

			$reply_to = $db->get_row("SELECT * FROM `miniblog` WHERE `id` = '$to' AND `type` = 'junk' AND `removed` = '0' AND `groupid` = '0'");

			$reply_to_id = 0;
			if ($reply_to) {
				$reply_to_id = $reply_to->id;
			}

			$body = post2db($_POST['responseminiblog']);

			if (!isset($_SESSION['antiflood']) or $_SESSION['antiflood'] < time() - 5) {
				$_SESSION["antiflood"] = time();

				$newid = post_mb(array(
					'text' => $body,
					'parent' => $pic->id,
					'type' => 'junk',
					'reply_to' => $reply_to_id
				));

				push('Komentēja attēlu <a href="' . $url . '#m' . $newid . '">&quot;' . textlimit($pic->title, 32, '...') . '&quot;</a>', '', 'junk-answ-' . $id);


				$newpost = $db->get_row("SELECT * FROM `miniblog` WHERE id = '$newid'");
				$newpost->text = mention($newpost->text, $url, 'junk', $pic->id);
				$db->query("UPDATE `miniblog` SET `text` = '" . sanitize($newpost->text) . "' WHERE id = '$newpost->id'");



				if (isset($_GET['postcomment'])) {
					die('ok');
				}
				redirect($url);
			} else {
				die('err: flood');
			}

			if (isset($_GET['postcomment'])) {
				die('err: wrong params');
			}
		}

		if ($auth->ok) {
			$tpl->newBlock('mb-reply-main');
		}

		if ($pic->posts) {

			$responses = $db->get_results("
	SELECT
		`miniblog`.`text` AS `text`,
		`miniblog`.`vote_value` AS `vote_value`,
		`miniblog`.`vote_users` AS `vote_users`,
		`miniblog`.`date` AS `date`,
		`miniblog`.`author` AS `author`,
 		`miniblog`.`groupid` AS `groupid`,
		`miniblog`.`posts` AS `posts`,
		`miniblog`.`reply_to` AS `reply_to`,
		`miniblog`.`id` AS `id`,
		`miniblog`.`removed` AS `mb_removed`,
		`miniblog`.`hidden` AS `hidden`,
		`users`.`nick` AS `nick`,
		`users`.`decos` AS `decos`,
		`users`.`avatar` AS `avatar`,
		`users`.`av_alt` AS `av_alt`,
		`users`.`level` AS `level`,
		`users`.`deleted` AS `user_deleted`
	FROM
		`miniblog`,
		`users`
	WHERE
		`miniblog`.`parent` = '" . $pic->id . "' AND
		`miniblog`.`type` = 'junk' AND
		`users`.`id` = `miniblog`.`author`
	ORDER BY
		`miniblog`.`id`
	ASC");

			if ($responses) {
				$json = array();
				foreach ($responses as $response) {
					$json[$response->reply_to][] = $response;
				}
				$tpl->newBlock('miniblog-posts');
				$tpl->assign('mbout', mb_recursive($json, 0, 0, 0, 3, $pic->closed));
			}
		} else {
			$tpl->newBlock('miniblog-no');
		}


		if ($auth->ok && !$pic->closed) {
			$tpl->newBlock('user-miniblog-resp');
			$tpl->assign(array(
				'id' => $pic->id,
				'token' => md5('mb' . $pic->id . $remote_salt . $auth->nick)
			));

			$tpl->newBlock('mb-head');
			$tpl->assign(array(
				'mbid' => $pic->id,
				'usrid' => $auth->id,
				'edit_time' => time(),
				'type' => 'junk',
				'lastid' => (int) $db->get_var("SELECT `id` FROM `miniblog` WHERE `parent` = '$pic->id' AND `removed` = '0' AND `type` = 'junk' ORDER BY `id` DESC LIMIT 1")
			));
		}
	} else {
		redirect('/' . $category->textid);
	}
} else {

	if (isset($_GET['skip'])) {
		$skip = (int) $_GET['skip'];
	} else {
		$skip = 0;
	}

	$junks = $db->get_results("SELECT * FROM `junk` WHERE `removed` = 0 ORDER BY `id` DESC LIMIT $skip,120");

	if (!empty($junks)) {
		$date = null;
		$tpl->newBlock('junk');
		foreach ($junks as $junk) {
			$thisdate = display_date_simple(strtotime($junk->date));
			if ($date != $thisdate) {
				$tpl->newBlock('junk-item-date');
				$tpl->assign(array(
					'date' => $thisdate
				));
				$date = $thisdate;
			}

			$tpl->newBlock('junk-item');
			$tpl->assignAll($junk);
		}
	}



	$total = $db->get_var("SELECT count(*) FROM `junk` WHERE `removed` = 0");
	$pager = pager($total, $skip, 120, '/junk/?skip=');
	$tpl->assignGlobal(array(
		'pager-next' => $pager['next'],
		'pager-prev' => $pager['prev'],
		'pager-numeric' => $pager['pages']
	));
}

$pagepath = '';

