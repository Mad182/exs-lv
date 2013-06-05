<?php

require('/home/www/exs.lv/configdb.php');

$debug = false;

/* load cammon libraries */
require(CORE_PATH . '/includes/class.mdb.php');
require('includes/class.auth.php');
require(CORE_PATH . '/includes/functions.core.php');
require(CORE_PATH . '/includes/class.templatepower.php');

/* nosaka, kuru lapu rādīt (exs.lv, coding.lv, etc) */
require('includes/site_loader.php');
session_start();
$cat = 'wall';

//mysql konekcija
$db = new mdb($username, $password, $database, $hostname);
unset($password);

//memcached konekcija
$m = new Memcache;
$m->connect($mc_host, $mc_port);

header('Content-Type: text/html; charset=utf-8');

$auth = new Auth();

if ($debug) {
	ini_set('display_errors', 'On');
	error_reporting(E_ALL);
	//$db->debug_all = true;
	echo '<div style="color:#eee;background:#222;font-size:9px;padding:0;margin:0;width:100%;"><div style="padding:2px 0;margin:0 auto;width:960px;">';
}

if (isset($_POST['niks']) && isset($_POST['parole']) && isset($_POST['xsrf_token'])) {
	$auth->login($_POST['niks'], $_POST['parole'], $_POST['xsrf_token']);
}

if (!$auth->ok && (!isset($_GET['viewcat']) || $_GET['viewcat'] != 'mav')) {
	$tpl = new TemplatePower('tmpl/login.tpl');
	$tpl->prepare();
	$tpl->assignGlobal(array(
		'server-name' => htmlspecialchars(str_replace('m.','',$_SERVER['SERVER_NAME'])),
		'page-title' => $page_title,
		'page-url' => htmlspecialchars($_SERVER['REQUEST_URI']),
		'xsrf' => $auth->xsrf
	));

} else {

	if (isset($_GET['do']) && $_GET['do'] == 'logout') {
		$auth->logout();
		redirect('/');
	}

	//banoto lietotāju saraksts
	$busers = get_banlist();

	//online lietotāji
	$online_users = get_online();

	if (isset($_GET['p'])) {
		$id = (int) $_GET['p'];
		$article = $db->get_row("SELECT * FROM `pages` WHERE `id` = " . $id . "");
		if ($article) {
			redirect('/read/' . $article->strid);
		}
	} else {
		if (isset($_GET['c'])) {
			$cat = (int) $_GET['c'];
		}

		if (isset($_GET['viewcat'])) {
			$category = get_cat($_GET['viewcat']);
			$cat = $category->id;
		} else {
			if (isset($_GET['c'])) {
				$cat = (int) $_GET['c'];
			}
			$category = get_cat($cat);
		}
	}

	$tpl = new TemplatePower('tmpl/main_' . $lang . '.tpl');
	$tpl->assignInclude('module-core-error', 'modules/core/error.tpl');
	$tpl->prepare();

	if (isset($_GET['p'])) {
		$id = (int) $_GET['p'];
		$article = $db->get_row("SELECT * FROM `pages` WHERE `id` = " . $id . "");
		if ($article) {
			redirect('/read/' . $article->strid);
		}
	} elseif (isset($_GET['u'])) {
		include(CORE_PATH . '/modules/core/user.php');
	} elseif (isset($_GET['f'])) {
		include('modules/core/friends.php');
	} elseif (isset($_GET['r'])) {
		include('modules/core/usertopics.php');
	} elseif (isset($_GET['b'])) {
		include('modules/core/bookmarks.php');
	} elseif (isset($_GET['g'])) {
		include('modules/core/gallery.php');
	} elseif (isset($_GET['m'])) {
		include('modules/core/miniblog.php');
	} elseif (isset($_GET['y'])) {
		include('modules/core/youtube.php');
	} elseif (isset($_GET['group'])) {
		include('modules/core/group.php');
	} else {
		if (!empty($category)) {
			$page_title = $category->title;
			if (file_exists('modules/' . $category->module . '/' . $category->module . '.php')) {
				$tpl->assignInclude('module-currrent', 'modules/' . $category->module . '/' . $category->module . '.tpl');
			} else {
				$tpl->assignInclude('module-currrent', CORE_PATH . '/modules/' . $category->module . '/' . $category->module . '.tpl');
			}
			$tpl->prepare();
			if ($category->module != 'index') {
				$pagepath = $category->title;
			}
			if (file_exists('modules/' . $category->module . '/' . $category->module . '.php')) {
				include('modules/' . $category->module . '/' . $category->module . '.php');
			} else {
				include(CORE_PATH . '/modules/' . $category->module . '/' . $category->module . '.php');
			}
		} else {
			$tpl->newBlock('error-nocat');
			$page_title = 'Kļūda: kategorija nav atrasta!';
		}
	}

	$tpl->newBlock('user-menu');
	$tpl->assign(array(
		'url' => mkurl('user', $auth->id, $auth->nick),
	));
	//unread messages count
	$new_messages = $db->get_var("SELECT count(*) FROM pm WHERE to_uid = '" . $auth->id . "' AND is_read = '0'");
	if ($new_messages) {
		$new_msg_string = '&nbsp;(<span class="unread">' . $new_messages . '</span>)';
	}

	$tpl->assignGlobal(array(
		'server-name' => htmlspecialchars(str_replace('m.','',$_SERVER['SERVER_NAME'])),
		'page-title' => $page_title,
		'page-url' => htmlspecialchars($_SERVER['REQUEST_URI']),
		'currentuser-nick' => htmlspecialchars($auth->nick),
		'new-messages' => $new_msg_string,
		'currentuser-id' => $auth->id,
		'currentuser-avatar' => $auth->avatar
	));

	//profile box
	if (isset($category) && $category->isblog != 0) {
		$inprofile = get_user($category->isblog);
	}
	if ($inprofile) {
		if ($inprofile->av_alt) {
			$u_large_path = 'u_large';
		} else {
			$u_large_path = 'useravatar';
		}
		if ($inprofile->avatar == '') {
			$inprofile->avatar = 'none.png';
			$u_large_path = 'u_large';
		}
		if ($lang == 1) {
			$isblog = get_blog_by_user($inprofile->id);
			if ($isblog) {
				$tpl->newBlock('profilebox-blog-link');
				$tpl->assign(array(
					'profile-blogid' => $isblog,
					'profile-blogcount' => $db->get_var("SELECT count(*) FROM `pages` WHERE `category` = '" . $isblog . "'")
				));
			}
		}
	}
}

if ($auth->ok === true && (isset($_GET['m']) || (!empty($_GET['viewcat']) && $_GET['viewcat'] == 'read') || isset($_GET['group']))) {
	$events = array();

	$articles = $db->get_results("
	SELECT
		`pages`.`id` AS `id`,
		`pages`.`title` AS `title`,
		`pages`.`strid` AS `strid`,
		`pages`.`date` AS `date`,
		`pages`.`author` AS `author`,
		`pages`.`posts` AS `posts`,
		`pages`.`bump` AS `bump`,
		`pages`.`avatar` AS `avatar`,
		`pages`.`sm_avatar` AS `sm_avatar`,
		`pages`.`intro` AS `intro`,
		`users`.`nick` AS `nick`,
		`users`.`avatar` AS `user_avatar`,
		`users`.`av_alt` AS `av_alt`,
		`cat`.`title` AS `ctitle`
	FROM
		`pages`,
		`cat`,
		`users`
	WHERE
		category != '83' AND category != '6' AND category != '305' AND category != '306' AND category != '307' AND category != '403' AND
		`users`.`id` = `pages`.`author` AND
		`cat`.`id` = `pages`.`category` AND
		`pages`.`bump` != '0000-00-00 00:00:00' AND
		`pages`.`lang` = '$lang'
	ORDER BY
		`pages`.`bump` DESC
	LIMIT
		5");

	foreach ($articles as $article) {

		if ($article->sm_avatar) {
			$article->avatar = 'http://exs.lv/' . $article->sm_avatar;
		} elseif ($article->user_avatar) {
			$article->avatar = '/av/' . $article->user_avatar;
		} else {
			$article->avatar = '/av/none.png';
		}

		$url = '/read/' . $article->strid;
		$time = time_ago_m(strtotime($article->bump));
		$article->title = textlimit($article->title, 125, '...');
		$where = ' <span class="where">#' . $article->ctitle . '</span>';

		$events[strtotime($article->bump) . '-' . $url] = array(
			'url' => $url,
			'author' => $article->nick,
			'title' => $article->title,
			'avatar' => $article->avatar,
			'time' => $time,
			'where' => $where,
			'posts' => $article->posts
		);
	}

	$usergroups = array("`miniblog`.`groupid` = '0'");
	if ($auth->ok) {
		$g_owners = $db->get_col("SELECT id FROM clans WHERE owner = '$auth->id'");
		if ($g_owners) {
			foreach ($g_owners as $g_owner) {
				$usergroups[] = "`miniblog`.`groupid` = '" . $g_owner . "'";
			}
		}
		$g_members = $db->get_col("SELECT clan FROM clans_members WHERE user = '$auth->id' AND approve = '1'");
		if ($g_members) {
			foreach ($g_members as $g_member) {
				$usergroups[] = "`miniblog`.`groupid` = '" . $g_member . "'";
			}
		}
	}
	$groupquery = implode(' OR ', $usergroups);

	$mbs = $db->get_results("SELECT
		`miniblog`.`id` AS `id`,
		`miniblog`.`text` AS `text`,
		`miniblog`.`date` AS `date`,
		`miniblog`.`bump` AS `bump`,
		`miniblog`.`author` AS `author`,
		`miniblog`.`posts` AS `posts`,
		`miniblog`.`groupid` AS `groupid`,
		`users`.`avatar` AS `avatar`,
		`users`.`av_alt` AS `av_alt`,
		`users`.`nick` AS `nick`
	FROM
		`miniblog`,
		`users`
	WHERE
		`miniblog`.`removed` = '0' AND
		`miniblog`.`parent` = '0' AND
		`miniblog`.`type` = 'miniblog' AND
		`miniblog`.`lang` = '$lang' AND
		(" . $groupquery . ") AND
		`users`.`id` = `miniblog`.`author`
	ORDER BY
		`miniblog`.`bump`
	DESC LIMIT 5");

	if ($mbs) {

		foreach ($mbs as $mb) {
			if ($mb->avatar == '') {
				$mb->avatar = '/av/none.png';
			} else {
				$mb->avatar = '/av/' . $mb->avatar;
			}

			$mb->text = preg_replace("#(^|[\n ]|<a(.*?)>)http://(www\.)?youtube\.com/watch\?v=([a-zA-Z0-9\-_]+)((.*?)</a>)?#ime", 'get_youtube_title("\\4") ', strip_tags(str_replace(array('<br/>', '<br>', '<br />', '<p>', '</p>', '&nbsp;', "\n", "\r"), ' ', $mb->text)));

			if ($mb->groupid != 0) {
				$group = $db->get_row("SELECT * FROM clans WHERE id = '$mb->groupid'");
				if (!empty($group->avatar)) {
					$mb->avatar = '/av/' . $group->avatar;
				}
				$url = '/group/' . $mb->groupid . '/forum/' . base_convert($mb->id, 10, 36);
			} else {
				$url = '/say/' . $mb->author . '/' . $mb->id . '-' . mb_get_strid($mb->text, $mb->id);
			}

			$mb->text = wordwrap($mb->text, 32, "\n", 1);
			if ($mb->groupid != 0) {
				$mb->text = textlimit($mb->text, 125, '...');
				$where = ' <span class="where">@' . $group->title . '</span>';
			} else {
				$mb->text = textlimit($mb->text, 125, '...');
				$where = '';
			}
			$time = time_ago_m($mb->bump);

			$events[$mb->bump . '-' . md5($url)] = array(
				'url' => $url,
				'author' => $mb->nick,
				'title' => $mb->text,
				'avatar' => $mb->avatar,
				'time' => $time,
				'where' => $where,
				'posts' => $mb->posts
			);
		}
	}

	ksort($events);
	$events = array_reverse($events);

	if (!empty($events)) {
		$tpl->newBlock('events');
		$i = 0;
		foreach ($events as $event) {
			if ($i++ >= 5) {
				break;
			}
			$tpl->newBlock('events-node');
			$tpl->assign(array(
				'url' => $event['url'],
				'author' => $event['author'],
				'title' => $event['title'],
				'avatar' => $event['avatar'],
				'time' => $event['time'],
				'where' => $event['where'],
				'posts' => $event['posts']
			));
		}
		$tpl->assignGlobal('events-title', '<h3>Notikumi</h3>');
	}
}

if ($debug) {
	echo '<div>Peak atmiņa: ' . round((memory_get_peak_usage() / 1024 / 1024), 3) . ' mb';
	echo ' | ielāde: ' . round(microtime(true) - $start_time, 5) . ' s';
	echo ' | mysql: ' . $db->num_queries . ' q';
	if (!empty($category->id)) {
		echo ' | cat_id:' . $category->id . ' (textid:' . $category->textid . ', module:' . $category->module . ')';
	}
	echo '</div></div></div>';
}

$out = $tpl->getOutputContent();

$out = str_replace('    ', ' ', $out);
$out = str_replace('   ', ' ', $out);
$out = str_replace('  ', ' ', $out);
$out = str_replace('	', '', $out);;
$out = str_replace("\r", "", $out);
$out = str_replace("\n\n", "\n", $out);
$out = str_replace("\n\n", "\n", $out);
$out = str_replace("\n\n", "\n", $out);

echo $out;
