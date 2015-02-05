<?php

/**
 * Lietotāja piezīmju blociņš
 */
if ($auth->ok) {

	$pagepath = '';
	$tpl->newBlock('notepad');

	if (isset($_GET['var1']) && $_GET['var1'] == 'delete' && isset($_GET['var2']) && check_token('delnote', $_GET['token'])) {
		$db->query("DELETE FROM `notes` WHERE user_id = '$auth->id' AND id = '" . intval($_GET['var2']) . "'");
		redirect('/' . $category->textid);
	} elseif (isset($_GET['var1']) && ($_GET['var1'] == 'read' || $_GET['var1'] == 'edit') && isset($_GET['var2'])) {
		$note = $db->get_row("SELECT * FROM notes WHERE user_id = '$auth->id' AND id = '" . intval($_GET['var2']) . "'");
	} elseif (!isset($_GET['var1']) || $_GET['var1'] != 'new') {
		$note = $db->get_row("SELECT * FROM notes WHERE user_id = '$auth->id' ORDER BY weight ASC LIMIT 1");
	} else {
		$note = false;
	}

	$pages = $db->get_results("SELECT `id`,`title` FROM `notes` WHERE `user_id` = '$auth->id' ORDER BY `weight` ASC");
	if (!empty($pages)) {
		foreach ($pages as $page) {
			$tpl->newBlock('np-menu-node');
			$sel = '';
			if (!empty($note) && $note->id == $page->id) {
				$sel = 'active';
			}
			$tpl->assign(array(
				'id' => $page->id,
				'sel' => $sel,
				'title' => $page->title,
			));
		}
	}

	if (isset($_GET['var1']) && ($_GET['var1'] === 'edit' || $_GET['var1'] === 'new')) {
		$tpl->newBlock('notepad-edit');
		if ($note) {
			$tpl->assign('content', h($note->content));
			$tpl->assign('id', $note->id);
		} else {
			$tpl->newBlock('notepad-title');
		}

		//iezīmē aktīvo tabu "+"
		if ($_GET['var1'] === 'new') {
			$tpl->assignGlobal('active-tab-new', 'active');
		}

		$tpl->newBlock('tinymce-enabled');

		if (isset($_POST['note-text'])) {
			$body = htmlpost2db($_POST['note-text']);
			$title = 'Piezīmes';
			if (!empty($_POST['title'])) {
				$title = title2db($_POST['title']);
			}
			if ($note) {
				$db->query("UPDATE notes SET content = '$body', modified = NOW() WHERE id = '$note->id'");
				$id = $note->id;
			} else {
				$db->query("INSERT INTO notes (user_id,title,content,created,modified)
				    VALUES ('$auth->id','$title','$body',NOW(),NOW())");
				$id = $db->insert_id;
			}

			redirect('/' . $category->textid . '/read/' . $id);
		}
	} else {
		if (!empty($note)) {
			$tpl->newBlock('notepad-view');
			$tpl->assign(array(
				'content' => add_smile($note->content, 1),
				'id' => $note->id,
				'token' => make_token('delnote')
			));
		}
	}
} else {
	$tpl->newBlock('error-nologin');
}
