<?php

/**
 * exs.lv labais sidebar
 */
$tpl->newBlock('main-layout-right');

//profile box
if (isset($category) && $category->isblog != 0 && empty($inprofile)) {
	$inprofile = get_user($category->isblog);
}

if (!empty($inprofile) && !$inprofile->deleted) {
	$avatar = get_avatar($inprofile, 'l');

	$tpl->newBlock('profile-box');
	$tpl->assignGlobal(array(
		'url' => '/user/' . $inprofile->id,
		'profile-nick' => h($inprofile->nick),
		'profile-slug' => mkslug($inprofile->nick),
		'profile-id' => $inprofile->id,
		'avatar' => $avatar,
		'profile-top-awards' => get_top_awards($inprofile->id)
	));

	if (!empty($inprofile->custom_title)) {
		$tpl->assign(array(
			'custom_title' => ' <small>(' . $inprofile->custom_title . ')</small>'
		));
	}

	//pm links
	if ($auth->ok === true && $auth->id != $inprofile->id) {
		$tpl->newBlock('profilebox-pm-link');
	}

	//avatara maiņas links, ja nav avatara
	if ($auth->id === $inprofile->id && empty($auth->avatar)) {
		$tpl->newBlock('profilebox-updateavatar');
	}

	//warnu links un skaits
	if ($auth->ok === true && ($auth->id == $inprofile->id || im_mod()) && !in_array($inprofile->level, array(1))) {
		$tpl->newBlock('profilebox-warn');
		if ($inprofile->warn_count > 0) {
			$tpl->assign(array(
				'profile-warns' => '&nbsp;(' . $inprofile->warn_count . ')',
				'class' => ' class="active"'
			));
		}
	}

	if (!empty($inprofile->twitter)) {
		$tpl->newBlock('profilebox-twitter-link');
		$tpl->assign(array(
			'twitter' => $inprofile->twitter
		));
	}

	if (!empty($inprofile->yt_name)) {
		$tpl->newBlock('profilebox-yt-link');
		$tpl->assign(array(
			'yt-name' => $inprofile->yt_name,
			'yt-slug' => mkslug($inprofile->yt_name)
		));
	}

	if (!empty($inprofile->lastfm_username)) {
		$tpl->newBlock('profilebox-lastfm-link');
		$tpl->assign(array(
			'name' => $inprofile->lastfm_username
		));
	}

	$isblog = get_blog_by_user($inprofile->id);
	if ($isblog) {
		$blog = get_cat($isblog);
		$count = $db->get_var("SELECT count(*) FROM `pages` WHERE `category` = '" . $isblog . "' AND `lang` = " . intval($lang));
		$tpl->newBlock('profilebox-blog-link');
		$tpl->assign(array(
			'url' => '/' . $blog->textid,
			'count' => $count
		));

		$tpl->newBlock('blog-latest-list');
		$tpl->assign('html', get_blog_latest($isblog));
	}
} elseif (!empty($ingroup)) {
	$tpl->newBlock('group-box');
	$ingroup->av_alt = 1;
	$av = get_avatar($ingroup, 'l');
	$tpl->assign(array(
		'group-id' => $ingroup->id,
		'group-title' => $ingroup->title,
		'group-av' => $av
	));
}

$wallpaper = $db->get_var("SELECT `image` FROM `wallpapers` WHERE `date` <= '" . date('Y-m-d') . "' ORDER BY `date` DESC LIMIT 1");
if ($wallpaper) {
	$tpl->newBlock('daily-wallpaper');
	$tpl->assignGlobal('wallpaper-image', $wallpaper);
	unset($wallpaper);
}

//jaunākās junk bildes
$junks = $db->get_results("SELECT `id`, `thb`, `title`, `posts` FROM `junk` WHERE `removed` = 0 ORDER BY `bump` DESC LIMIT 4");
if ($junks) {
	$tpl->newBlock('side-junk');
	foreach ($junks as $junk) {
		$tpl->newBlock('side-junk-node');
		$tpl->assign(array(
			'id' => $junk->id,
			'thb' => $junk->thb,
			'title' => h($junk->title),
			'posts' => $junk->posts
		));
	}
	unset($junks);
}

include(CORE_PATH . '/modules/core/poll.php');

$tpl->newBlock('mb-box');
$sel = 'all';
if (!empty($_COOKIE['last-mbs-tab']) && $_COOKIE['last-mbs-tab'] === 'friends') {
	$mbs = get_latest_mbs('friends');
	$sel = 'friends';
} elseif (!empty($_COOKIE['last-mbs-tab']) && $_COOKIE['last-mbs-tab'] === 'music') {
	$mbs = get_latest_mbs('music');
	$sel = 'music';
} else {
	$mbs = get_latest_mbs('all');
}

$tpl->assign('out', $mbs);

if ($auth->ok) {
	$tpl->newBlock('mb-tabs');
}
$tpl->assignGlobal(array(
	$sel . '-selected' => 'active '
));


// Dienas labākā komentāra bloks
$responses = $db->get_results("SELECT `id`,`author`,`text`,`parent`,`vote_value`,`lang` FROM `miniblog` WHERE
                                `removed` = 0 AND DATE(`date`) = CURDATE() AND vote_value > 0 AND groupid = 0
                                AND `type` = 'miniblog' ");
if($responses){
    $tpl->newBlock('daily-best');
    $maxkey = $responses[0];
    // Dabū komentāru ar lielāko plusiņu skaitu
    foreach($responses as $row => $column){
        if( $column->vote_value > $maxkey->vote_value ) $maxkey = $responses[$row];
    }
    $user = get_user($maxkey->author);
    $parent = $maxkey->parent;

    if($parent > 0){
        // Ja ir parent, tad tā ir atbilde uz MB, ja nav, tad tas ir pats MB ieraksts.
        $body = $db->get_row("SELECT text,author FROM miniblog WHERE id = $parent");
        $title = mb_get_title(stripslashes($body->text));
        $check = $body->author;
        $strid = mb_get_strid($title, $maxkey->parent);
    }else{
        $title = mb_get_title(stripslashes($maxkey->text));
        $check = $maxkey->author;
        $strid = mb_get_strid($title, $check);
        $parent = $maxkey->id;
    }

    $url = '/say/' . $check . '/' . $parent . '-' . $strid. '#m' .$maxkey->id;
    $avatar = get_avatar($user, 's');
    $nick = $user->nick;
    $rating = '+ '.$maxkey->vote_value;
    $content = strip_tags($maxkey->text);
    if(strlen($content)>100) $content = substr($content, 0, 100).'...';

    $tpl->assign(array(
           'best-link' => $url,
           'best-avatar' => $avatar,
           'best-nick' => $nick,
           'best-rating' => $rating,
           'best-comment' => $content
        ));
}


if (im_mod()) {
	$newimgs = $db->get_var("SELECT count(*) FROM `junk_queue` WHERE `approved` = 0");
	$iappstr = '';
	if ($newimgs) {
		$iappstr = '&nbsp;(<span class="r">' . $newimgs . '</span>)';
	}
	$tpl->newBlock('junk-info');
	$tpl->assign(array(
		'count' => $iappstr
	));
}

if ($auth->ok === true) {
	$tpl->assignGlobal('miniblog-add', '&nbsp;<a href="/say/' . $auth->id . '#content" class="mb-create" title="Pievienot jaunu ierakstu">Izveidot</a>');
}

if ($auth->skin == 1) {
	$tpl->assignGlobal('twitter-theme', ' data-theme="dark"');
}
