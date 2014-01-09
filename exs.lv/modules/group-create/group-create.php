<?php

if (!in_array($lang, array(1, 5, 9))) {
	set_Flash('Šobrīd nav pieejams!', 'error');
	redirect('/grupas');
}

if ($auth->ok) {

	$pagepath = '<a href="/grupas">Exs grupas</a> / ' . $pagepath;
	$credit = $db->get_var("SELECT credit FROM users WHERE id = '$auth->id'");

	if (isset($_POST['new-title']) && !empty($_POST['new-title'])) {
		if ($credit < 3) {
			set_flash('Nepietiek exs punktu!', 'error');
		} else {
			$title = sanitize(htmlspecialchars(trim($_POST['new-title'])));
			$db->query("UPDATE users SET credit = credit-'3' WHERE id = ('" . $auth->id . "')");
			$db->query("INSERT INTO clans (title,date_created,owner,lang) VALUES ('$title','" . time() . "','$auth->id','$lang')");
			update_karma($auth->id, true);
			get_latest_groups(true);
			redirect('/group/' . $db->insert_id);
		}
	}

	$tpl->newBlock('group-create');
	$tpl->assign(array(
		'user-credit' => $credit,
		'user-id' => $auth->id
	));
} else {
	$tpl->newBlock('error-nologin');
}
