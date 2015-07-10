<?php

/**
 * Visu miniblogu saraksts
 */
$robotstag = array('noindex', 'follow');

if (isset($_GET['skip'])) {
	$skip = (int) $_GET['skip'];
} else {
	$skip = 0;
}
$end = 20;

if (isset($_GET['var1'])) {
	$u = (int) $_GET['var1'];
	if ($user = get_user($u)) {
		redirect('/say/' . $user->id);
	}
}

$mbs = $db->get_results("SELECT
		`miniblog`.`id` AS `id`,
		`miniblog`.`text` AS `text`,
		`miniblog`.`date` AS `date`,
		`miniblog`.`author` AS `author`,
		`miniblog`.`posts` AS `posts`
	FROM
		`miniblog`
	WHERE
		`miniblog`.`parent` = '0' AND
		`miniblog`.`groupid` = '0' AND
		`miniblog`.`removed` = '0' AND
		`miniblog`.`lang` = '$lang'
	ORDER BY
		`miniblog`.`id`
	DESC LIMIT $skip,$end");

if ($mbs) {
	$tpl->newBlock('miniblog-list');
	foreach ($mbs as $mb) {

		$tpl->newBlock('miniblog-list-node');

		$usr = get_user($mb->author);
		$av = get_avatar($usr, 's');

		$url = mb_get_strid($mb->text, $mb->id);

		$time = time_ago(strtotime($mb->date));
		$tpl->assign(array(
			'id' => $mb->id,
			'author' => $mb->author,
			'text' => add_smile($mb->text),
			'nick' => $usr->nick,
			'time' => $time,
			'avatar' => $av,
			'resp' => $mb->posts,
			'url' => $url,
			'aurl' => '/user/' . $mb->author
		));
	}

	if ($lang == 1) {
		$total = 1000;
	} else {
		$total = $db->get_var("
			SELECT
				COUNT(*)
			FROM
				`miniblog`
			WHERE
				`miniblog`.`parent` = '0' AND
				`miniblog`.`groupid` = '0' AND
				`miniblog`.`removed` = '0' AND
				`miniblog`.`lang` = '$lang'
		");
	}

	$pager = pager($total, $skip, $end, '/say/?skip=');
	$tpl->assignGlobal(array(
		'pager-next' => $pager['next'],
		'pager-prev' => $pager['prev'],
		'pager-numeric' => $pager['pages']
	));
}

