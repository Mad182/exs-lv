<?php

/**
 * Lietotāja izveidoto tēmu saraksts
 */

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

	if ($inprofile->private && !$auth->ok) {
		$robotstag = ['noindex', 'nofollow'];
		$tpl->newBlock('user-usertopics-private');
	} else {
		if ($skip) {
			$page_title = $page_title . ' - lapa ' . ($skip / $end + 1);
		}

		$tpl->newBlock('user-usertopics');

	$total = $db->get_var("SELECT count(*) FROM `pages` WHERE `author` = '" . $inprofile->id . "' AND `lang` = '$lang' AND `category` != '6'");
	$articles = $db->get_results("SELECT p.*, c.title AS cat_title FROM `pages` p LEFT JOIN `cat` c ON p.category = c.id WHERE p.author = '" . $inprofile->id . "' AND p.lang = '$lang' AND p.category != '6' ORDER BY p.date DESC LIMIT $skip,$end");
	if ($articles) {
		$tpl->newBlock('user-usertopics-list');

		if ($total > $end) {
			$pager = pager($total, $skip, $end, '/topics/' . $inprofile->id . '?skip=', false);
			$tpl->newBlock('user-usertopics-pager-top');
			$tpl->assign([
				'pager-next' => $pager['next'],
				'pager-prev' => $pager['prev'],
				'pager-numeric' => $pager['pages']
			]);
		}

		foreach ($articles as $article) {
			$tpl->newBlock('user-usertopics-node');
			$posts = (int) $article->posts;
			$views = (int) $article->views;
			$timestamp = strtotime($article->date);

			$tpl->assign([
				'articles-node-id' => $article->id,
				'node-url' => '/read/' . $article->strid,
				'articles-node-title' => $article->title,
				'articles-node-date' => display_time($timestamp, false),
				'articles-node-datetime' => substr($article->date, 0, 16),
				'articles-node-posts' => $posts,
				'replies-class' => $posts > 0 ? 'has-replies' : '',
			]);

			if (!empty($article->closed)) {
				$tpl->newBlock('user-usertopics-closed');
			}

			if (!empty($article->cat_title)) {
				$tpl->newBlock('user-usertopics-cat');
				$tpl->assign('category-title', $article->cat_title);
			}

			if ($views > 0) {
				$tpl->newBlock('user-usertopics-views');
				$tpl->assign('articles-node-views', number_format($views, 0, '', ' '));
			}
		}

		if ($total > $end) {
			$tpl->newBlock('user-usertopics-pager-bottom');
			$tpl->assign([
				'pager-next' => $pager['next'],
				'pager-prev' => $pager['prev'],
				'pager-numeric' => $pager['pages']
			]);
		}
	} else {
		$robotstag = ['noindex', 'follow'];
		$tpl->newBlock('empty-usertopics');
	}
	}
	$pagepath = '';
} else {
	set_flash('Šāds lietotājs netika atrasts, iespējams kļūdains links!', 'error');
	redirect();
}


