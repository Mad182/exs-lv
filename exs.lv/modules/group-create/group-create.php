<?php

/**
 * Grupas izveide
 */
$robotstag[] = 'noindex';

if (!in_array($lang, [1, 5, 9])) {
	set_Flash('Šobrīd nav pieejams!', 'error');
	redirect('/grupas');
}

if ($auth->ok) {

	$pagepath = '<a href="/grupas">Exs grupas</a> / ' . $pagepath;
	$credit = $db->get_var("SELECT `credit` FROM `users` WHERE `id` = '$auth->id'");

	if (isset($_POST['new-title']) && !empty($_POST['new-title'])) {
		$title = sanitize(h(trim($_POST['new-title'])));

		if($db->get_var("SELECT count(*) FROM `clans` WHERE `title` = '".$title."' OR `title` LIKE '".$title."'")) {
			set_flash('Izvēlies citu nosaukumu, šāda grupa jau eksistē!', 'error');
			$auth->log('Neizdevās uztaisīt grupu (nosaukums sakrīt ar eksistējošu ' . h(serialize($_POST)) . ')');

		} elseif ($credit < 3) {
			set_flash('Nepietiek exs punktu!', 'error');

		} else {

			if($db->query("INSERT INTO clans (title,date_created,owner,lang) VALUES ('$title','" . time() . "','$auth->id','$lang')")) {
				$db->query("UPDATE users SET credit = credit-'3' WHERE id = ('" . $auth->id . "')");
			
				update_karma($auth->id, true);
				get_latest_groups(true);

				$auth->log('Izveidoja grupu (' . h(serialize($_POST)) . ')');
			}

			redirect('/group/' . $db->insert_id);
		}
	}

	$tpl->newBlock('group-create');
	$tpl->assign([
		'user-credit' => $credit,
		'user-id' => $auth->id
	]);
} else {
	$tpl->newBlock('error-nologin');
}

