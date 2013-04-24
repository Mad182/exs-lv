<?php

if (isset($_GET['var1']) && $_GET['var1'] == 'autosave') {
	$body = $_POST['new-topic-body'];
	$title = $_POST['new-topic-title'];
	$title = title2db($title);
	$body = htmlpost2db($body);
	$category_id = intval($_POST['new-topic-category']);
	$draft_id = $db->get_var("SELECT id FROM drafts WHERE user_id = '$auth->id'");

	if ($draft_id) {
		if ($db->query("UPDATE drafts SET title = '$title', text = '$body', category_id = '$category_id', modified = NOW() WHERE id = '$draft_id'")) {
			die('<p class="success">Melnraksts saglabāts ' . date('Y-m-d H:i:s') . '</p>');
		}
	} else {
		if ($db->query("INSERT INTO drafts (user_id,title,text,category_id,modified) VALUES ('$auth->id','$title','$body','$category_id',NOW())")) {
			die('<p class="success">Melnraksts izveidots ' . date('Y-m-d H:i:s') . '</p>');
		}
	}
	die('<p class="error">Kļūda saglabājot melnrakstu!</p>');
}

if (isset($_POST['new-topic-body'])) {
	$body = trim($_POST['new-topic-body']);
	$title = trim($_POST['new-topic-title']);
	if (empty($title)) {
		$title = 'Nosaukums nav norādīts';
	}
	$newcat = (int) $_POST['new-topic-category'];
	if ($body && $title) {

		$title = title2db($title);
		$body = htmlpost2db($body);

		$date = date('Y-m-d H:i:s');
		$textid = date('YmdHis');
		$strid = mkslug_newpage($title);

		if ($auth->ok && ($auth->level == 3 or $auth->level == 2 or $auth->level == 1)) {
			$insert = $db->query("INSERT INTO pages (strid,textid,category,text,title,author,date,bump,ip,lang)
									VALUES ('$strid','$textid','$newcat','$body','$title','$auth->id','$date','$date','$auth->ip','$lang')");

			$topicid = $db->insert_id;

			update_stats($newcat);

			if ($insert) {
				$db->query("DELETE FROM drafts WHERE user_id = '$auth->id'");
			}

			if (isset($_FILES['edit-avatar']) && !empty($_FILES['edit-avatar'])) {
				require_once('includes/class.upload.php');
				$foo = new Upload($_FILES['edit-avatar']);
				$foo->file_new_name_body = $topicid;
				$foo->image_resize = true;
				$foo->image_convert = 'jpg';
				$foo->allowed = array('image/*');
				$foo->image_ratio = true;
				$foo->image_ratio_pixels = 17800;
				$foo->jpeg_quality = 96;
				$foo->image_ratio_no_zoom_in = true;
				$foo->file_auto_rename = false;
				$foo->file_overwrite = true;
				$foo->process('dati/bildes/avatari/');
				if ($foo->processed) {
					$foo->file_new_name_body = $topicid;
					$foo->image_resize = true;
					$foo->image_convert = 'jpg';
					$foo->image_x = 75;
					$foo->image_y = 75;
					$foo->allowed = array('image/*');
					$foo->image_ratio_crop = true;
					$foo->jpeg_quality = 96;
					$foo->file_auto_rename = false;
					$foo->file_overwrite = true;
					$foo->process('dati/bildes/av_sm/');
					$foo->clean();
					$avatar = 'dati/bildes/avatari/' . $topicid . '.jpg';
					$sm_avatar = 'dati/bildes/av_sm/' . $topicid . '.jpg';
					$db->query("UPDATE pages SET avatar = ('$avatar'), sm_avatar = ('$sm_avatar') WHERE id = '$topicid'");
				}
			}

			build_latest();
			update_karma($auth->id);
			redirect('/read/' . $strid);
		} else {

			$insert = $db->query("INSERT INTO approve (category,text,title,author,date,ip,lang)
									VALUES ('$newcat','$body','$title','$auth->id','$date','$auth->ip','$lang')");
			$topicid = $db->insert_id;

			if ($insert) {
				$db->query("DELETE FROM drafts WHERE user_id = '$auth->id'");
			}

			if (isset($_FILES['edit-avatar']) && !empty($_FILES['edit-avatar'])) {
				require_once('includes/class.upload.php');
				$foo = new Upload($_FILES['edit-avatar']);
				$foo->file_new_name_body = $topicid;
				$foo->image_resize = true;
				$foo->image_convert = 'jpg';
				$foo->allowed = array('image/*');
				$foo->image_ratio = true;
				$foo->image_ratio_pixels = 17800;
				$foo->jpeg_quality = 96;
				$foo->image_ratio_no_zoom_in = true;
				$foo->file_auto_rename = false;
				$foo->file_overwrite = true;
				$foo->process('modules/approve/av_l/');
				if ($foo->processed) {
					$foo->file_new_name_body = $topicid;
					$foo->image_resize = true;
					$foo->image_convert = 'jpg';
					$foo->image_x = 75;
					$foo->image_y = 75;
					$foo->allowed = array('image/*');
					$foo->image_ratio_crop = true;
					$foo->jpeg_quality = 96;
					$foo->file_auto_rename = false;
					$foo->file_overwrite = true;
					$foo->process('modules/approve/av_sm/');
					$foo->clean();
				}
			}

			redirect('/write/list');
		}
	}
}

if ($auth->ok) {

	if (im_mod() && isset($_GET['var1']) && $_GET['var1'] == 'delete') {
		$delete = (int) $_GET['var2'];
		$db->query("UPDATE `approve` SET `removed` = 1 WHERE `id` = '$delete'");
		@unlink('modules/approve/av_l/' . $delete . '.jpg');
		@unlink('modules/approve/av_sm/' . $delete . '.jpg');
		redirect('/write/list');
	}

	$tpl->newBlock('approve-body');
	if (isset($_GET['var1']) && $_GET['var1'] == 'edit' && ($auth->level == 1 or $auth->level == 2 || $auth->id == '115')) {

		$tpl->assign('edit-active', 'active');

		$edit = (int) $_GET['var2'];

		if (isset($_POST['ap-topic-title']) && isset($_POST['ap-topic-body'])) {
			$body = trim($_POST['ap-topic-body']);
			$title = trim($_POST['ap-topic-title']);
			if (!empty($body) && !empty($title)) {

				$title = title2db($title);
				$body = htmlpost2db($body);
				$author = (int) $_POST['ap-topic-author'];
				$added = sanitize($_POST['ap-topic-date']);
				$ip = sanitize($_POST['ap-topic-ip']);
				$category = (int) $_POST['ap-topic-category'];
				$date = date('Y-m-d H:i:s');
				$textid = date('YmdHis');
				$strid = mkslug_newpage($title);
				$db->query("INSERT INTO pages (strid,textid,category,text,title,author,date,bump,ip,lang) VALUES ('$strid','$textid','$category','$body','$title','$author','$added','$date','$ip','$lang')");
				$topicid = $db->insert_id;
				update_stats($category);

				if (file_exists('modules/approve/av_l/' . $edit . '.jpg')) {
					rename('modules/approve/av_l/' . $edit . '.jpg', 'dati/bildes/avatari/' . $topicid . '.jpg');
					rename('modules/approve/av_sm/' . $edit . '.jpg', 'dati/bildes/av_sm/' . $topicid . '.jpg');
					$avatar = 'dati/bildes/avatari/' . $topicid . '.jpg';
					$sm_avatar = 'dati/bildes/av_sm/' . $topicid . '.jpg';
					$db->query("UPDATE pages SET avatar = ('$avatar'), sm_avatar = ('$sm_avatar') WHERE id = '$topicid'");
				}

				$db->query("UPDATE `approve` SET `removed` = 1 WHERE `id` = '$edit'");
				build_latest();
				update_karma($author, true);
				redirect('/read/' . $strid);
			}
		}

		$article = $db->get_row("SELECT * FROM approve WHERE id = '$edit' AND `removed` = 0");
		$author = get_user($article->author);

		$tpl->newBlock('approve-edit');

		$tpl->assign(array(
			'article-showtitle' => $article->title,
			'article-title' => $article->title,
			'article-text' => htmlspecialchars($article->text),
			'article-id' => $article->id,
			'article-ip' => $article->ip,
			'article-author' => $article->author,
			'article-author-nick' => usercolor($author->nick, $author->level, false, $article->author),
			'aurl' => mkurl('user', $article->author, $author->nick),
			'article-date' => $article->date,
		));

		if (file_exists('modules/approve/av_l/' . $article->id . '.jpg')) {
			$tpl->assign(array(
				'article-avatar' => '<strong>Avatars:</strong><br/ ><img src="/modules/approve/av_l/' . $article->id . '.jpg" alt="" />',
			));
		}

		$categorys = $db->get_results("SELECT id,title FROM `cat` WHERE (module = 'list' OR module = 'movies' OR module = 'index' OR module = 'rshelp') AND isblog = '0' AND mods_only = '0' AND (`lang` = '$lang' OR `lang` = '0')");
		if ($categorys) {
			foreach ($categorys as $category_l) {
				$tpl->newBlock('select-apcategory');
				$sel = '';
				if ($category_l->id == $article->category) {
					$sel = ' selected="selected"';
				}
				$tpl->assign(array(
					'category-title' => $category_l->title,
					'category-id' => $category_l->id,
					'category-sel' => $sel,
				));
			}
		}

		$tpl->newBlock('tinymce-enabled');
	} elseif (isset($_GET['var1']) && $_GET['var1'] == 'list') {

		$tpl->assign('edit-active', 'active');
		$tpl->newBlock('approve-view');

		$articles = $db->get_results("SELECT id,title FROM `approve` WHERE `author` = '" . $auth->id . "' AND `lang` = '$lang' AND `removed` = 0");
		if ($articles) {
			$tpl->newBlock('approve-list');
			foreach ($articles as $article) {
				$tpl->newBlock('approve-list-node');
				$tpl->assign(array(
					'approve-list-title' => $article->title,
					'approve-list-id' => $article->id,
				));
			}
		}

		if (im_mod()) {
			$tpl->newBlock('approveadm-view');
			$articles = $db->get_results("SELECT id,title FROM `approve` WHERE `lang` = '$lang' AND `removed` = 0 ORDER BY `date` ASC");
			if ($articles) {
				$tpl->newBlock('approveadm-list');
				foreach ($articles as $article) {
					$tpl->newBlock('approveadm-list-node');
					$tpl->assign(array(
						'approve-list-title' => $article->title,
						'approve-list-id' => $article->id,
					));
				}
			}
		}
	} else {
		$tpl->assign('new-active', 'active');
		$tpl->newBlock('approve-new');

		$draft = $db->get_row("SELECT * FROM drafts WHERE user_id = '$auth->id'");
		if ($draft) {
			$tpl->assign(array(
				'draft-title' => $draft->title,
				'draft-text' => $draft->text
			));
		}

		$categorys = $db->get_results("SELECT id,title FROM `cat` WHERE `isforum` = '0' AND (module = 'list' OR module = 'movies' OR module = 'index' OR module = 'rshelp') AND isblog = '0' AND mods_only = '0' AND (`lang` = '$lang' OR `lang` = '0')");
		if ($categorys) {
			foreach ($categorys as $category_l) {
				$tpl->newBlock('select-category');
				$tpl->assign(array(
					'category-title' => $category_l->title,
					'category-id' => $category_l->id,
				));
			}
		}

		$tpl->newBlock('tinymce-enabled');
	}
} else {
	$tpl->newBlock('error-nologin');
}
