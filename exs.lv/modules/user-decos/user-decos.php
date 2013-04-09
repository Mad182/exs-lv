<?php

if (!im_mod()) {
	redirect();
}

if (isset($_POST['userid']) && isset($_POST['title']) && isset($_POST['icon'])) {
	$userid = (int) $_POST['userid'];
	$user = $db->get_row("SELECT * FROM users WHERE id = '$userid'");

	if (!empty($user)) {
		if (!$user->decos == '') {
			$decos = array();
		} else {
			$decos = unserialize($user->decos);
		}

		$decos[] = array(
			'title' => htmlspecialchars($_POST['title']),
			'icon' => htmlspecialchars($_POST['icon'])
		);

		$decos = serialize($decos);
		$db->query("UPDATE users SET decos = '" . sanitize($decos) . "' WHERE id = '$user->id'");
		header('Location: /' . $category->textid);
		exit;
	}
}

$listdecos = $db->get_results("SELECT * FROM users WHERE decos != ''");

foreach ($listdecos as $decos) {
	$images = unserialize($decos->decos);
	if (!empty($images)) {
		$new = array();
		foreach ($images as $key => $deco) {
			if (isset($_GET['uid']) && $_GET['uid'] == $decos->id && isset($_GET['remove']) && $_GET['remove'] != $key) {
				$new[$key] = array(
					'title' => $deco['title'],
					'icon' => $deco['icon'],
				);
			}

			$tpl->newBlock('existing-deco');
			$tpl->assign(array(
				'title' => $deco['title'],
				'icon' => $deco['icon'],
				'key' => $key,
				'userid' => $decos->id,
				'nick' => $decos->nick,
			));
		}

		if (isset($_GET['uid']) && $_GET['uid'] == $decos->id && isset($_GET['remove'])) {
			$new = serialize($new);
			$db->query("UPDATE users SET decos = '" . sanitize($new) . "' WHERE id = '$decos->id'");
			header('Location: /' . $category->textid);
			exit;
		}
	} else {
		$db->query("UPDATE users SET decos = '' WHERE id = '$decos->id'");
	}
}
