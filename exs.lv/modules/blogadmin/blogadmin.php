<?php

if (!$auth->ok) {
	$tpl->newBlock('error-nologin');
} elseif (!get_blog_by_user($auth->id)) {
	$credit = $db->get_var("SELECT credit FROM users WHERE id = '$auth->id'");
	$pay = '';
	if ($credit >= 5) {

		if (isset($_GET['act']) && $_GET['act'] == 'submitpay') {
			$nick = sanitize($auth->nick);
			$db->query("UPDATE users SET credit = credit-'5' WHERE id = '$auth->id'");
			$db->query("INSERT INTO cat (textid,title,isblog,parent) VALUES ('" . strtolower(mkslug($auth->nick)) . "','$nick blogs','$auth->id','110')");
			push('Izveidoja sev <a href="/' . mkslug($auth->nick) . '">blogu</a>');
			$m->delete('isb_' . $auth->id);
			redirect('/'.$category->textid);
		}

		$pay = '<p><a href="/?c=111&amp;act=submitpay"><strong>Izveidot blogu</strong></a></p>';
	}
	$tpl->newBlock('blogadmin-setup');
	$tpl->assign(array(
		'credit' => $credit,
		'user-id' => $auth->id,
		'pay' => $pay
	));
} else {
	$inprofile = get_user($auth->id);
	$tpl->newBlock('blogadmin-body');

	if (isset($_POST['new-topic-title']) && isset($_POST['new-topic-body'])) {

		if (!isset($_POST['token']) or $_POST['token'] != md5('lol' . $category->title . $remote_salt . $auth->id)) {
			set_flash('Kļūda, tēma netika izveidota! Hacking around?');
			redirect();
		}

		$body = trim($_POST['new-topic-body']);
		$title = trim($_POST['new-topic-title']);
		if ($body && $title) {

			$title = title2db($title);
			$body = htmlpost2db($body);
			$blogid = get_blog_by_user($auth->id, true);
			$textid = date('YmdHis');
			$strid = mkslug_newpage($title);

			$db->query("INSERT INTO pages (strid,textid,category,text,title,author,date,bump,ip,lang) VALUES ('$strid','$textid','$blogid','$body','$title','$auth->id',NOW(),NOW(),'$auth->ip','$lang')");
			$ins = $db->insert_id;
			userlog($auth->id, 'Izveidoja rakstu blogā &quot;<a href="/read/' . $strid . '">' . $title . '</a>&quot;');
			update_stats($blogid);

			$avatar = '';
			if (isset($_FILES['new-avatar'])) {
				require_once('includes/class.upload.php');
				$foo = new Upload($_FILES['new-avatar']);
				$foo->file_new_name_body = $ins;
				$foo->image_resize = true;
				$foo->image_convert = 'jpg';
				$foo->allowed = array('image/*');
				$foo->image_ratio = true;
				$foo->image_ratio_pixels = 17200;
				$foo->jpeg_quality = 93;
				$foo->image_ratio_no_zoom_in = true;
				$foo->file_auto_rename = false;
				$foo->file_overwrite = true;
				$foo->process('dati/bildes/avatari/');
				if ($foo->processed) {

					$foo->file_new_name_body = $ins;
					$foo->image_resize = true;
					$foo->image_convert = 'jpg';
					$foo->image_x = 75;
					$foo->image_y = 75;
					$foo->allowed = array('image/*');
					$foo->image_ratio_crop = true;
					$foo->jpeg_quality = 93;
					$foo->process('dati/bildes/av_sm/');
					$foo->clean();

					$sm_avatar = 'dati/bildes/av_sm/' . $ins . '.jpg';
					$avatar = 'dati/bildes/avatari/' . $ins . '.jpg';
					$db->query("UPDATE `pages` SET `avatar` = '$avatar', `sm_avatar` = '$sm_avatar' WHERE `id` = '$ins'");
				}
			}

			if (empty($avatar)) {
				$curuser_avatar = $db->get_var("SELECT avatar FROM users WHERE id = '$auth->id'");

				if (!empty($curuser_avatar)) {
					require_once('includes/class.upload.php');
					$foo = new Upload('dati/bildes/useravatar/' . $curuser_avatar);
					$foo->file_new_name_body = $ins;
					$foo->image_resize = true;
					$foo->image_convert = 'jpg';
					$foo->image_x = 75;
					$foo->image_y = 75;
					$foo->allowed = array('image/*');
					$foo->image_ratio_crop = true;
					$foo->jpeg_quality = 95;
					$foo->process('dati/bildes/av_sm/');
					if ($foo->processed) {
						$sm_avatar = 'dati/bildes/av_sm/' . $ins . '.jpg';
						$db->query("UPDATE `pages` SET `sm_avatar` = ('$sm_avatar') WHERE `id` = '$ins'");
					}
				}
			}

			build_latest();
			redirect('/read/' . $strid);

		}
	}

	if (isset($_GET['act']) && $_GET['act'] == 'edit') {
		$tpl->assign('edit-active', ' active');
		$articles = $db->get_results("SELECT `title`,`strid` FROM `pages` WHERE `category` = '" . get_blog_by_user($auth->id) . "' ORDER BY date DESC");
		if ($articles) {
			$tpl->newBlock('user-usertopics-list');
			foreach ($articles as $article) {
				$tpl->newBlock('user-usertopics-node');
				$tpl->assign(array(
					'strid' => $article->strid,
					'title' => $article->title
				));
			}
		}
	} elseif (isset($_GET['act']) && $_GET['act'] == 'links') {
		$tpl->assign('links-active', ' active');
		$tpl->newBlock('blogadmin-links');

		if (isset($_GET['delete'])) {
			$delete = (int) $_GET['delete'];
			$db->query("DELETE FROM sidelinks WHERE id = ('$delete') AND category = ('" . get_blog_by_user($auth->id) . "') LIMIT 1");
			redirect('/'.$category->textid.'/?act=links');
		}

		if (!isset($_GET['edit'])) {
			$tpl->newBlock('sidelinks-new');

			if (isset($_POST['new-sidelink-title'])) {
				if (!isset($_POST['new-sidelink-url']) or $_POST['new-sidelink-url'] == 'http://') {
					$tpl->newBlock('error-nolink');
				} else {
					$title = sanitize(substr(htmlspecialchars(trim($_POST['new-sidelink-title'])), 0, 64));
					$url = sanitize(substr(htmlspecialchars(trim($_POST['new-sidelink-url'])), 0, 256));
					$blogid = get_blog_by_user($auth->id);
					$db->query("INSERT INTO sidelinks (category,title,url) VALUES ('$blogid','$title','$url')");
				}
			}

			$sidelinks = $db->get_results("SELECT * FROM sidelinks WHERE category = ('" . get_blog_by_user($auth->id) . "') ORDER BY id DESC");
			if ($sidelinks) {
				$tpl->newBlock('user-sidelinks-list');
				foreach ($sidelinks as $sidelink) {
					$tpl->newBlock('user-sidelinks-node');
					$tpl->assign(array(
						'sidelink-id' => $sidelink->id,
						'sidelink-title' => $sidelink->title,
						'sidelink-url' => $sidelink->url,
					));
				}
			}
		} else {

			$editid = (int) $_GET['edit'];
			$blogid = get_blog_by_user($auth->id);

			$edit = $db->get_row("SELECT * FROM sidelinks WHERE category = '" . $blogid . "' AND id = '$editid'");

			if ($edit) {

				$tpl->newBlock('sidelinks-edit');

				if (isset($_POST['edit-sidelink-title'])) {
					if (!isset($_POST['edit-sidelink-url']) or $_POST['edit-sidelink-url'] == 'http://') {
						$tpl->newBlock('error-nolink2');
					} else {
						$title = sanitize(substr(htmlspecialchars(trim($_POST['edit-sidelink-title'])), 0, 64));
						$url = sanitize(substr(htmlspecialchars(trim($_POST['edit-sidelink-url'])), 0, 256));
						$blogid = get_blog_by_user($auth->id);
						$db->query("UPDATE sidelinks SET title = ('$title'), url = ('$url') WHERE id = '$editid' AND category = '$blogid'");
						$edit->title = $title;
						$edit->url = $url;
					}
				}

				$tpl->assign(array(
					'title' => $edit->title,
					'url' => $edit->url
				));
			} else {
				$tpl->newBlock('error-noedit');
			}
		}
	} else {
		$tpl->assign('new-active', ' active');
		$tpl->newBlock('tinymce-enabled');
		$tpl->newBlock('blogadmin-new');
		$tpl->assign('blog-check', md5('lol' . $category->title . $remote_salt . $auth->id));
	}
}
