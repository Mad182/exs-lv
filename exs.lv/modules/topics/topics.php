<?php

/**
 * Lietotāja izveidoto tēmu saraksts
 */
$robotstag = ['noindex', 'follow'];

if (isset($_GET['skip'])) {
	$skip = (int) $_GET['skip'];
} else {
	$skip = 0;
}
$end = 60;

if (isset($_GET['var1'])) {
	$userid = (int) $_GET['var1'];
	$inprofile = get_user($userid);
} elseif ($auth->ok) {
	$inprofile = get_user($auth->id);
}

if (!empty($inprofile) && empty($inprofile->deleted)) {

	profile_menu($inprofile, 'usertopics', 'raksti', 'rakstus');

	if ($skip) {
		$page_title = $page_title . ' - lapa ' . ($skip / $end + 1);
	}

	$tpl->newBlock('user-usertopics');

	$total = $db->get_var("SELECT count(*) FROM `pages` WHERE `author` = '" . $inprofile->id . "' AND `lang` = '$lang' AND `category` != '6'");
	$articles = $db->get_results("SELECT * FROM `pages` WHERE `author` = '" . $inprofile->id . "' AND `lang` = '$lang' AND `category` != '6' ORDER BY `date` DESC LIMIT $skip,$end");
	if ($articles) {
		$tpl->newBlock('user-usertopics-list');
		foreach ($articles as $article) {
			$tpl->newBlock('user-usertopics-node');
			$tpl->assign([
				'articles-node-id' => $article->id,
				'node-url' => '/read/' . $article->strid,
				'articles-node-title' => $article->title,
				'articles-node-date' => substr($article->date, 0, 10),
				'articles-node-posts' => $article->posts
			]);
		}
	}

	if ($total) {
		$pager = pager($total, $skip, $end, '/topics/' . $inprofile->id . '?skip=', true);
		$tpl->assignGlobal([
			'pager-next' => $pager['next'],
			'pager-prev' => $pager['prev'],
			'pager-numeric' => $pager['pages']
		]);
	}
	$pagepath = '';
} else {
	set_flash('Šāds lietotājs netika atrasts, iespējams kļūdains links!', 'error');
	redirect();
}


