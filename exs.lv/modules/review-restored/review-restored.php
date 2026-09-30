<?php
/**
 * Review Restored Articles Module
 *
 * Allows administrators and moderators to review, edit, approve, or reject
 * historical articles and comments recovered from archive.org.
 */

if (!$auth->ok || ($auth->level != 1 && $auth->level != 2)) {
	set_flash('Piekļuve liegta! Šī sadaļa pieejama tikai administratoriem.', 'error');
	redirect('/');
}

$tpl->assignInclude('module-head', CORE_PATH . '/modules/review-restored/head.tpl');

$page_title = 'Atjaunoto rakstu pārbaude | EXS.LV Administrācija';
$robotstag[] = 'noindex';

// -------------------------------------------------------------
// POST Actions: Approve, Save, Reject
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
	if (!check_token('review_action', $_POST['xsrf_token'])) {
		set_flash('Nederīgs drošības tokens. Lūdzu, mēģini vēlreiz.', 'error');
		redirect('/review-restored');
	}

	$action = sanitize($_POST['action']);
	$reviewId = (int)$_POST['review_id'];
	$pageId = (int)$_POST['page_id'];

	$revRow = $db->get_row("SELECT * FROM restored_articles_review WHERE id = {$reviewId} LIMIT 1");
	if (!$revRow) {
		set_flash('Pārbaudes ieraksts netika atrasts!', 'error');
		redirect('/review-restored');
	}

	if ($action === 'approve') {
		$title = sanitize(trim($_POST['title']));
		$category = (int)$_POST['category'];
		$text = htmlpost2db(trim($_POST['text']));
		$date = sanitize(trim($_POST['date']));
		$introText = trim(strip_tags($_POST['text']));
		$intro = (mb_strlen($introText) > 280) ? mb_substr($introText, 0, 280) . '...' : $introText;
		$metaDesc = mb_substr($introText, 0, 160);

		// Get category title
		$catTitle = (string)$db->get_var("SELECT title FROM cat WHERE id = {$category}");

		// 1. Update page in `pages` table
		$db->query("UPDATE pages SET
			title = '" . sanitize($title) . "',
			category = {$category},
			text = '" . sanitize($text) . "',
			intro = '" . sanitize($intro) . "',
			meta_description = '" . sanitize($metaDesc) . "',
			date = '" . sanitize($date) . "',
			needs_review = 0,
			private = 0
		WHERE id = {$pageId} LIMIT 1");

		// 2. Update status in `restored_articles_review`
		$db->query("UPDATE restored_articles_review SET
			title = '" . sanitize($title) . "',
			category_id = {$category},
			category_name = '" . sanitize($catTitle) . "',
			status = 'approved',
			reviewed_at = NOW(),
			reviewed_by = {$auth->id}
		WHERE id = {$reviewId} LIMIT 1");

		// 3. Update category stats & flush Memcached
		update_stats($category);
		$m->flush();

		set_flash('Raksts &quot;' . h($title) . '&quot; ir veiksmīgi apstiprināts un publicēts!', 'success');

		// Find next pending article in comment count order
		$currentComments = (int)$revRow->comment_count;
		$nextPending = $db->get_var("
			SELECT id FROM restored_articles_review
			WHERE status = 'pending'
			  AND (comment_count < {$currentComments} OR (comment_count = {$currentComments} AND id > {$reviewId}))
			ORDER BY comment_count DESC, id ASC
			LIMIT 1
		");
		if (!$nextPending) {
			$nextPending = $db->get_var("SELECT id FROM restored_articles_review WHERE status = 'pending' AND id != {$reviewId} ORDER BY comment_count DESC, id ASC LIMIT 1");
		}
		if ($nextPending) {
			redirect('/review-restored?review=' . $nextPending);
		} else {
			redirect('/review-restored');
		}

	} elseif ($action === 'save') {
		$title = sanitize(trim($_POST['title']));
		$category = (int)$_POST['category'];
		$text = htmlpost2db(trim($_POST['text']));
		$date = sanitize(trim($_POST['date']));
		$introText = trim(strip_tags($_POST['text']));
		$intro = (mb_strlen($introText) > 280) ? mb_substr($introText, 0, 280) . '...' : $introText;
		$metaDesc = mb_substr($introText, 0, 160);

		$catTitle = (string)$db->get_var("SELECT title FROM cat WHERE id = {$category}");

		$db->query("UPDATE pages SET
			title = '" . sanitize($title) . "',
			category = {$category},
			text = '" . sanitize($text) . "',
			intro = '" . sanitize($intro) . "',
			meta_description = '" . sanitize($metaDesc) . "',
			date = '" . sanitize($date) . "'
		WHERE id = {$pageId} LIMIT 1");

		$db->query("UPDATE restored_articles_review SET
			title = '" . sanitize($title) . "',
			category_id = {$category},
			category_name = '" . sanitize($catTitle) . "'
		WHERE id = {$reviewId} LIMIT 1");

		set_flash('Izmaiņas veiksmīgi saglabātas!', 'success');
		redirect('/review-restored?review=' . $reviewId);

	} elseif ($action === 'reject') {
		// Mark as rejected in review table
		$db->query("UPDATE restored_articles_review SET
			status = 'rejected',
			reviewed_at = NOW(),
			reviewed_by = {$auth->id}
		WHERE id = {$reviewId} LIMIT 1");

		// Delete from pages and comments, or keep private & closed
		$db->query("DELETE FROM comments WHERE pid = {$pageId}");
		$db->query("DELETE FROM pages WHERE id = {$pageId} LIMIT 1");

		$m->flush();
		set_flash('Raksts tika noraidīts un izdzēsts.', 'notice');

		$currentComments = (int)$revRow->comment_count;
		$nextPending = $db->get_var("
			SELECT id FROM restored_articles_review
			WHERE status = 'pending'
			  AND (comment_count < {$currentComments} OR (comment_count = {$currentComments} AND id > {$reviewId}))
			ORDER BY comment_count DESC, id ASC
			LIMIT 1
		");
		if (!$nextPending) {
			$nextPending = $db->get_var("SELECT id FROM restored_articles_review WHERE status = 'pending' AND id != {$reviewId} ORDER BY comment_count DESC, id ASC LIMIT 1");
		}
		if ($nextPending) {
			redirect('/review-restored?review=' . $nextPending);
		} else {
			redirect('/review-restored');
		}
	}
}

// -------------------------------------------------------------
// GET: Single Article Review
// -------------------------------------------------------------
if (isset($_GET['review'])) {
	$reviewId = (int)$_GET['review'];
	$rev = $db->get_row("SELECT * FROM restored_articles_review WHERE id = {$reviewId} LIMIT 1");

	if (!$rev) {
		set_flash('Pieprasītais pārbaudes ieraksts netika atrasts!', 'error');
		redirect('/review-restored');
	}

	$page = $db->get_row("SELECT * FROM pages WHERE id = {$rev->page_id} LIMIT 1");
	if (!$page) {
		set_flash('Saistītais raksta ieraksts `pages` tabulā netika atrasts!', 'error');
		redirect('/review-restored');
	}

	$tpl->newBlock('review-single');

	// Status label
	$statusLabels = [
		'pending' => 'Gaida pārbaudi',
		'approved' => 'Apstiprināts',
		'rejected' => 'Noraidīts'
	];
	$statusClass = $rev->status;

	$tpl->assign([
		'article-review-id' => $rev->id,
		'article-page-id' => $page->id,
		'article-title' => h($page->title),
		'article-slug' => h($page->strid),
		'article-date' => $page->date,
		'article-views' => number_format($page->views, 0, '', ' '),
		'article-rating' => $page->rating,
		'article-rating-count' => $page->rating_count,
		'article-comments-count' => $page->posts,
		'article-author-id' => $page->author,
		'article-author-nick' => !empty($rev->author_nick) ? h($rev->author_nick) : 'Lietotājs #' . $page->author,
		'article-raw-body' => h($page->text),
		'article-rendered-body' => add_smile($page->text, 1, 0),
		'wayback-url' => h($rev->wayback_url),
		'current-status-class' => $statusClass,
		'current-status-label' => $statusLabels[$rev->status] ?? $rev->status,
		'xsrf-token' => make_token('review_action')
	]);

	// Categories options - exclude groups, personal blogs, games, redirects, and system modules
	$catRows = $db->get_results("
		SELECT id, title, module, isforum, parent, lang
		FROM cat
		WHERE status = 'active'
		  AND isblog = 0
		  AND module IN ('list', 'movies', 'wall', 'rshelp')
		  AND parent != 2516
		  AND TRIM(title) != ''
		  AND (lang = 1 OR module = 'rshelp')
		ORDER BY title ASC
	");

	$catsGrouped = [
		'Forums' => [],
		'Raksti un apskati' => [],
		'RuneScape' => []
	];

	$includedCatIds = [];

	if ($catRows) {
		foreach ($catRows as $catOpt) {
			$includedCatIds[] = (int)$catOpt->id;
			$displayTitle = $catOpt->title;
			if ($catOpt->id == 335) {
				$displayTitle = 'Minecraft (apskati)';
			} elseif ($catOpt->id == 336) {
				$displayTitle = 'Minecraft (forums)';
			}

			if ($catOpt->module === 'rshelp' || $catOpt->lang == 9) {
				$catsGrouped['RuneScape'][] = (object)[
					'id' => $catOpt->id,
					'title' => $displayTitle
				];
			} elseif ($catOpt->isforum) {
				$catsGrouped['Forums'][] = (object)[
					'id' => $catOpt->id,
					'title' => $displayTitle
				];
			} else {
				$catsGrouped['Raksti un apskati'][] = (object)[
					'id' => $catOpt->id,
					'title' => $displayTitle
				];
			}
		}
	}

	// If article's current category is outside standard categories, keep it in a separate group so it's not lost
	if (!empty($page->category) && !in_array((int)$page->category, $includedCatIds)) {
		$curCat = $db->get_row("SELECT id, title FROM cat WHERE id = " . (int)$page->category . " LIMIT 1");
		if ($curCat && trim($curCat->title) !== '') {
			$catsGrouped['Pašreizējā sadaļa'] = [
				(object)[
					'id' => $curCat->id,
					'title' => $curCat->title . ' (pašreizējā)'
				]
			];
		}
	}

	foreach ($catsGrouped as $groupName => $groupCats) {
		if (empty($groupCats)) {
			continue;
		}
		$tpl->newBlock('cat-group');
		$tpl->assign('group-label', $groupName);

		foreach ($groupCats as $catOpt) {
			$tpl->newBlock('cat-option');
			$tpl->assign([
				'cat-id' => $catOpt->id,
				'cat-title' => h($catOpt->title),
				'cat-selected' => ($catOpt->id == $page->category) ? 'selected="selected"' : ''
			]);
		}
	}

	// Queue position among pending articles (ordered by comment_count DESC, id ASC)
	$pendingIds = $db->get_col("SELECT id FROM restored_articles_review WHERE status = 'pending' ORDER BY comment_count DESC, id ASC");
	$prevId = null;
	$nextId = null;

	if ($pendingIds && in_array($rev->id, $pendingIds)) {
		$currentPosIndex = array_search($rev->id, $pendingIds);
		$currentPos = $currentPosIndex + 1;
		$tpl->newBlock('queue-position');
		$tpl->assign([
			'pos-current' => $currentPos,
			'pos-total' => count($pendingIds)
		]);

		if ($currentPosIndex > 0) {
			$prevId = $pendingIds[$currentPosIndex - 1];
		}
		if ($currentPosIndex < count($pendingIds) - 1) {
			$nextId = $pendingIds[$currentPosIndex + 1];
		}
	} else {
		// If current article is not pending (e.g. already approved/rejected), navigate among all articles in same order
		$allIds = $db->get_col("SELECT id FROM restored_articles_review ORDER BY comment_count DESC, id ASC");
		if ($allIds && in_array($rev->id, $allIds)) {
			$currentPosIndex = array_search($rev->id, $allIds);
			if ($currentPosIndex > 0) {
				$prevId = $allIds[$currentPosIndex - 1];
			}
			if ($currentPosIndex < count($allIds) - 1) {
				$nextId = $allIds[$currentPosIndex + 1];
			}
		}
	}

	if ($prevId) {
		$tpl->newBlock('prev-btn');
		$tpl->assign('prev-id', $prevId);
		$tpl->newBlock('prev-link-js');
		$tpl->assign('prev-id', $prevId);
	}

	if ($nextId) {
		$tpl->newBlock('next-btn');
		$tpl->assign('next-id', $nextId);
		$tpl->newBlock('next-link-js');
		$tpl->assign('next-id', $nextId);
	}

	// Comments list
	$comments = $db->get_results("SELECT * FROM comments WHERE pid = {$page->id} ORDER BY parent ASC, id ASC");
	if (!empty($comments)) {
		$usersCache = [];
		foreach ($comments as $c) {
			$tpl->newBlock('review-comment');

			$authorNick = 'Lietotājs #' . $c->author;
			if ($c->author > 0) {
				if (!isset($usersCache[$c->author])) {
					$uRow = $db->get_row("SELECT nick, level FROM users WHERE id = {$c->author} LIMIT 1");
					$usersCache[$c->author] = $uRow;
				}
				if (!empty($usersCache[$c->author])) {
					$authorNick = usercolor($usersCache[$c->author]->nick, $usersCache[$c->author]->level, false, $c->author);
				}
			}

			$tpl->assign([
				'comment-id' => $c->id,
				'comment-author' => $authorNick,
				'comment-date' => $c->date,
				'comment-vote' => ($c->vote_value > 0 ? '+' : '') . $c->vote_value,
				'comment-text' => add_smile($c->text, 1, 0),
				'comment-reply-class' => ($c->parent > 0) ? 'reply' : ''
			]);
		}
	} else {
		$tpl->newBlock('comments-empty');
	}

	return;
}

// -------------------------------------------------------------
// GET: Dashboard / List View
// -------------------------------------------------------------
$tpl->newBlock('review-list');

// Compute statistics
$statPending = (int)$db->get_var("SELECT count(*) FROM restored_articles_review WHERE status = 'pending'");
$statApproved = (int)$db->get_var("SELECT count(*) FROM restored_articles_review WHERE status = 'approved'");
$statRejected = (int)$db->get_var("SELECT count(*) FROM restored_articles_review WHERE status = 'rejected'");
$statComments = (int)$db->get_var("SELECT coalesce(sum(comment_count), 0) FROM restored_articles_review");

$tpl->assign([
	'stat-pending' => number_format($statPending, 0, '', ' '),
	'stat-approved' => number_format($statApproved, 0, '', ' '),
	'stat-rejected' => number_format($statRejected, 0, '', ' '),
	'stat-comments' => number_format($statComments, 0, '', ' ')
]);

// Start Reviewing button (pending with most comments first)
$firstPending = $db->get_var("SELECT id FROM restored_articles_review WHERE status = 'pending' ORDER BY comment_count DESC, id ASC LIMIT 1");
if ($firstPending) {
	$tpl->newBlock('start-review-btn');
	$tpl->assign('first-pending-id', $firstPending);
}

// Filter and Search
$currentStatus = isset($_GET['status']) ? sanitize($_GET['status']) : 'pending';
$searchQ = isset($_GET['q']) ? trim($_GET['q']) : '';

$tpl->assign([
	'current-status' => $currentStatus,
	'search-q' => h($searchQ),
	'tab-pending-active' => ($currentStatus === 'pending') ? 'active' : '',
	'tab-approved-active' => ($currentStatus === 'approved') ? 'active' : '',
	'tab-rejected-active' => ($currentStatus === 'rejected') ? 'active' : '',
	'tab-all-active' => ($currentStatus === 'all') ? 'active' : ''
]);

$whereClauses = [];
if ($currentStatus !== 'all') {
	$whereClauses[] = "status = '" . sanitize($currentStatus) . "'";
}
if (!empty($searchQ)) {
	$sanQ = sanitize($searchQ);
	$whereClauses[] = "(title LIKE '%{$sanQ}%' OR slug LIKE '%{$sanQ}%' OR author_nick LIKE '%{$sanQ}%')";
}

$whereSql = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

// Pagination
$perPage = 30;
$pageNum = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$offset = ($pageNum - 1) * $perPage;

$totalRows = (int)$db->get_var("SELECT count(*) FROM restored_articles_review {$whereSql}");
$rows = $db->get_results("SELECT * FROM restored_articles_review {$whereSql} ORDER BY comment_count DESC, id ASC LIMIT {$offset}, {$perPage}");

$statusLabels = [
	'pending' => 'Gaida',
	'approved' => 'Apstiprināts',
	'rejected' => 'Noraidīts'
];

if (!empty($rows)) {
	foreach ($rows as $r) {
		$tpl->newBlock('review-row');
		$tpl->assign([
			'row-id' => $r->id,
			'row-page-id' => $r->page_id,
			'row-title' => h($r->title),
			'row-slug' => h($r->slug),
			'row-category' => !empty($r->category_name) ? h($r->category_name) : 'Sadaļa #' . $r->category_id,
			'row-author' => !empty($r->author_nick) ? h($r->author_nick) : 'Lietotājs #' . $r->author_id,
			'row-comments' => $r->comment_count,
			'row-date' => date('d.m.Y H:i', strtotime($r->article_date ?: $r->created_at)),
			'row-status-class' => $r->status,
			'row-status-label' => $statusLabels[$r->status] ?? $r->status
		]);
	}

	// Pagination controls
	$totalPages = ceil($totalRows / $perPage);
	if ($totalPages > 1) {
		$tpl->newBlock('review-pagination');
		$paginationHtml = '<div class="rr-tabs">';
		for ($p = 1; $p <= $totalPages; $p++) {
			$active = ($p == $pageNum) ? 'active' : '';
			$url = '/review-restored?status=' . urlencode($currentStatus) . '&p=' . $p . (!empty($searchQ) ? '&q=' . urlencode($searchQ) : '');
			$paginationHtml .= '<a href="' . $url . '" class="rr-tab ' . $active . '">' . $p . '</a>';
		}
		$paginationHtml .= '</div>';
		$tpl->assign('pagination-html', $paginationHtml);
	}
} else {
	$tpl->newBlock('review-empty');
}
