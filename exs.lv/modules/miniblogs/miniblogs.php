<?php

/**
 * Visu miniblogu saraksts
 */

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

		if (is_valid_user($usr)) {
			$author_avatar = '<a href="/user/' . $mb->author . '"><img class="av" src="' . $av . '" alt="' . h($usr->nick) . '" /></a>';
			$author_link = '<a href="/user/' . $mb->author . '">' . h($usr->nick) . '</a>';
			$nick = $usr->nick;
			$aurl = '/user/' . $mb->author;
		} else {
			$author_avatar = '<img class="av" src="' . $av . '" alt="" />';
			$u_nick = !empty($usr->nick) ? mb_strtolower(trim($usr->nick), 'UTF-8') : '';
			$nick_display = ($u_nick === 'nezināms' || $u_nick === 'nezinams') ? 'nezināms' : 'dzēsts';
			$author_link = '<em>' . $nick_display . '</em>';
			$nick = $nick_display;
			$aurl = '#';
		}

		$url = mb_get_strid($mb->text, $mb->id);

		$time = time_ago(strtotime($mb->date));
		$tpl->assign([
			'id' => $mb->id,
			'author' => $mb->author,
			'text' => add_smile($mb->text),
			'nick' => $nick,
			'author-avatar' => $author_avatar,
			'author-link' => $author_link,
			'time' => $time,
			'avatar' => $av,
			'resp' => $mb->posts,
			'url' => $url,
			'aurl' => $aurl
		]);
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

	$pager = pager($total, $skip, $end, '/say?skip=');
	$tpl->assignGlobal([
		'pager-next' => $pager['next'],
		'pager-prev' => $pager['prev'],
		'pager-numeric' => $pager['pages']
	]);
}

