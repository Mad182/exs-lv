<?php

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

if ($inprofile) {

	if ($auth->ok) {
		set_action($inprofile->nick . ' rakstus');
	}

	$tpl->newBlock('profile-menu');
	$tpl->assign('user-menu-add', ' raksti');

	$page_title = $inprofile->nick . ' | raksti';
	if ($skip) {
		$page_title = $page_title . ' - lapa ' . ($skip / $end + 1);
	}
	$tpl->assignGlobal(array(
		'user-id' => $inprofile->id,
		'user-nick' => htmlspecialchars($inprofile->nick),
		'active-tab-usertopics' => 'active'
	));
	$tpl->newBlock('user-usertopics');

	$total = $db->get_var("SELECT count(*) FROM `pages` WHERE `author` = '" . $inprofile->id . "' AND `lang` = '$lang' AND `category` != '6'");
	$articles = $db->get_results("SELECT * FROM `pages` WHERE `author` = '" . $inprofile->id . "' AND `lang` = '$lang' AND `category` != '6' ORDER BY `date` DESC LIMIT $skip,$end");
	if ($articles) {
		$tpl->newBlock('user-usertopics-list');
		foreach ($articles as $article) {
			$tpl->newBlock('user-usertopics-node');
			$tpl->assign(array(
				'articles-node-id' => $article->id,
				'node-url' => '/read/' . $article->strid,
				'articles-node-title' => $article->title,
				'articles-node-date' => substr($article->date, 0, 10),
				'articles-node-posts' => $article->posts
			));
		}
	}

	if ($total) {
		$pager = pager($total, $skip, $end, '/topics/' . $inprofile->id . '?skip=', true);
		$tpl->assignGlobal(array(
			'pager-next' => $pager['next'],
			'pager-prev' => $pager['prev'],
			'pager-numeric' => $pager['pages']
		));
	}
} else {
	set_flash('Šāds lietotājs netika atrasts, iespējams kļūdains links!', 'error');
	redirect();
}

$pagepath = '';
