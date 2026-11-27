<?php

/**
 *
 * index.php
 *
 * Ielādē kopīgos failus, sagatavo globālos mainīgos, ielādē moduli
 *
 */
require('configdb.php');

$has_yt = false;

/* ielādē kopīgos failus */

if (file_exists(ROOT_PATH . '/vendor/autoload.php')) {
	require(ROOT_PATH . '/vendor/autoload.php');
}

require(CORE_PATH . '/includes/class.mdb.php');
require(CORE_PATH . '/includes/class.auth.php');
require(CORE_PATH . '/includes/functions.core.php');
require(CORE_PATH . '/includes/class.templatepower.php');
require(CORE_PATH . '/includes/class.site_storage.php');

/* nosaka, kuru lapu rādīt (exs.lv, coding.lv, etc) */
require(CORE_PATH . '/includes/site_loader.php');

// Per-domain XML sitemap serving (/sitemap.xml)
if (!empty($_GET['fakeurl']) && $_GET['fakeurl'] === 'sitemap.xml') {
	$sitemap_domain = isset($config_domains[$lang]['domain']) ? $config_domains[$lang]['domain'] : 'exs.lv';
	$sitemap_file = CORE_PATH . '/sitemaps/sitemap-' . $sitemap_domain . '.xml';
	if (file_exists($sitemap_file)) {
		header('Content-Type: application/xml; charset=UTF-8');
		readfile($sitemap_file);
		exit;
	}
}

//rewrite hack
if (!empty($_GET['fakeurl'])) {
	$parts = explode('/', $_GET['fakeurl']);
	$_GET['viewcat'] = $parts[0];
	if (!empty($parts[1])) {
		$_GET['var1'] = $parts[1];
	}
	if (!empty($parts[2])) {
		$_GET['var2'] = $parts[2];
	}
	if (!empty($parts[3])) {
		$_GET['var3'] = $parts[3];
	}
	if (!empty($parts[4])) {
		$_GET['var4'] = $parts[4];
	}

	if ($_GET['viewcat'] === 'say') {
		if (!empty($parts[1])) {
			$_GET['m'] = $parts[1];
		}
		if (!empty($parts[2])) {
			$mbid = explode('-', $parts[2]);
			if ($mbid[0] === 'skip') {
				$_GET['skip'] = $mbid[1] ?? 0;
			} else {
				$_GET['single'] = $mbid[0];
			}
		}
	}
}

if (empty($_GET['viewcat'])) {
	$_GET['viewcat'] = null;
}

//izslēdz sesijas botiem, paātrina ielādi un nepiemēslo ar nevajadzīgiem sesiju failiem
if (!is_bot_ua()) {
	session_start();
}

//mysql konekcija
$db = new mdb($username, $password, $database, $hostname);
unset($password);

//memcached konekcija
$m = new Memcached;
if (defined('Memcached::HAVE_IGBINARY') && Memcached::HAVE_IGBINARY) {
	$m->setOption(Memcached::OPT_SERIALIZER, Memcached::SERIALIZER_IGBINARY);
}
$m->addServer($mc_host, $mc_port);

//lapas settingu/datu glabāšana
$ss = new SiteStorage;

$requested_json = (substr($_SERVER['REQUEST_URI'] ?? '', -5) === '.json' || (isset($_GET['var1']) && $_GET['var1'] == 'json'));

if ($requested_json) {
	header('Content-Type: application/json; charset=UTF-8');
} else {
	//laicīgi novēršam enkodinga gļukus stulbos pārlūkos
	header('Content-Type: text/html; charset=UTF-8');
}

//redirektē vecos runescape.ex.lv linkus uz pareizu jauno linku
if (isset($_GET['kategorija']) && isset($_GET['id'])) {
	//raksti
	$redirect = $db->get_row("SELECT `strid` FROM `pages` WHERE `textid` = '" . sanitize($_GET['id']) . "'");
	if ($redirect && !empty($redirect->strid)) {
		redirect('https://exs.lv/read/' . $redirect->strid, true);
	}
} elseif (isset($_GET['id']) && !isset($_GET['viewcat'])) {
	//kategorijas
	$category = get_cat($_GET['id']);
	if ($category && !empty($category->textid)) {
		redirect('https://exs.lv/' . $category->textid, true);
	}
}

$site_access = get_site_access();

//izveido aktīvā lietotāja objektu
$auth = new Auth();

//login
if (isset($_POST['niks']) && isset($_POST['parole']) && isset($_POST['xsrf_token'])) {
	$auth->login($_POST['niks'], $_POST['parole'], $_POST['xsrf_token']);

	if ($auth->error === 1) {
		set_flash('Nepareizs niks un/vai parole! Mēģini vēlreiz, vai izmanto "<a href="/forgot-password">Aizmirsu paroli</a>".', 'error');
	} else {
		redirect($_SERVER['REQUEST_URI']);
	}
}

//jaunu vēstuļu skaits, tiek izmantots pie vēstuļu linka un notifikācijās
if ($auth->ok === true) {
	if ($new_messages = $db->get_var("SELECT count(*) FROM `pm` WHERE `to_uid` = " . $auth->id . " AND `is_read` = 0")) {
		$new_msg_string = (int) $new_messages;
	}
}

//jaunās vēstules (html)
if ($new_msg_string > 0) {
	$new_msg_html = '&nbsp;(<span class="r" style="display:inline">' . $new_msg_string . '</span>)';
} else {
	$new_msg_html = '';
}

//atgriež visādus datus json formātā, ja pieprasījums bijis uz /get/updates.json
if (isset($_GET['viewcat']) && $_GET['viewcat'] === 'get' && isset($_GET['var1']) && $_GET['var1'] === 'updates.json') {
	$data = [];
	if (isset($_GET['loadpm'])) {
		$data['pm-count'] = $new_msg_string;
	}
	if (isset($_GET['loadgallery'])) {
		$data['in-tabs'] = get_latest_images();
	} elseif (isset($_GET['loadposts'])) {
		$data['in-tabs'] = get_latest_posts();
	}
	if (isset($_GET['loadmb'])) {
		if (!isset($_GET['tab'])) $_GET['tab'] = '';
		$data['mb-latest'] = get_latest_mbs($_GET['tab']);
	}
	echo json_encode($data);
	exit;
}

//banoto lietotāju saraksts
$busers = get_banlist();

//online lietotāji
$online_users = get_online();

//"cake day"
$cday_users = get_cakeday();

if (isset($_GET['p'])) {
	$id = (int) $_GET['p'];
	$article = $db->get_row("SELECT * FROM `pages` WHERE `id` = " . $id . "");
	if ($article) {
		redirect('/read/' . $article->strid, true);
	} else {
		error_404();
	}
} else {

	/*
	  atrod moduli (cat tabula) ko rādīt
	 */
	if (isset($_GET['viewcat'])) {
		$category = get_cat($_GET['viewcat']);
		$cat = (!empty($category)) ? $category->id : 0;
	} else {
		if (isset($_GET['c'])) {
			$cat = (int) $_GET['c'];
		}
		$category = get_cat($cat);
	}
	if (empty($category) && isset($_GET['m'])) {
		$category = get_cat('say');
	}
	if (!empty($category) && $category->tmpl) {
		$skin = $category->tmpl;
	}
}

//ielādē templeitu
$loadskin = $skin;
if ($lang !== 1 && $skin === 'main') {
	$loadskin = $skin . '_' . $lang;
}

$tpl = new TemplatePower(CORE_PATH . '/tmpl/' . $loadskin . '.tpl');
$tpl->assignInclude('module-core-error', CORE_PATH . '/modules/core/error.tpl');

//izdomā, ko tad īsti rādīsim :)
//redirekti no veco moduļu versijām
if (isset($_GET['u'])) {
	include(CORE_PATH . '/modules/core/user.php');
} elseif (isset($_GET['f'])) {
	redirect('/friends/' . intval($_GET['f']), true);
} elseif (isset($_GET['r']) && $_GET['viewcat'] !== 'ES_SPAMOJU_SUDUS') {
	include(CORE_PATH . '/modules/core/usertopics.php');
} elseif (isset($_GET['b'])) {
	redirect('/bookmarks/' . intval($_GET['b']), true);
} elseif (isset($_GET['g'])) {
	include(CORE_PATH . '/modules/core/gallery.php');
} elseif (isset($_GET['m'])) {
	include(CORE_PATH . '/modules/core/miniblog.php');
} elseif (isset($_GET['y'])) {
	include(CORE_PATH . '/modules/core/youtube.php');
} else {
	//"jauno" moduļu ielāde
	if (!empty($category->module)) {
		$page_title = strip_tags($category->title);

		$is_ajax = isset($_GET['_']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

		if ($is_ajax) {
			$tpl = new TemplatePower(CORE_PATH . '/modules/' . $category->module . '/' . $category->module . '.tpl');
			$tpl->prepare();
		} else {

			$tpl->assignInclude('module-currrent', CORE_PATH . '/modules/' . $category->module . '/' . $category->module . '.tpl');
			//iekešojam sadaļas templeitu. mazliet apgrūtina .tpl failu labošanu, toties -20% ielādes laikam
			if (($tpl2 = $m->get('t_' . $lang . '_' . $category->module)) === false || $debug === true) {
				$tpl->prepare();
				$m->set('t_' . $lang . '_' . $category->module, $tpl, 3600);
			} else {
				$tpl = $tpl2;
				unset($tpl2);
			}
		}

		$pagepath = $category->title;
		if (!empty($category->parent)) {
			$category2 = get_cat($category->parent);
			if ($category2) {
				$pagepath = '<a href="/' . $category2->textid . '">' . $category2->title . '</a> / ' . $pagepath;
				if (!empty($category2->parent)) {
					$category3 = get_cat($category2->parent);
					if ($category3) {
						$pagepath = '<a href="/' . $category3->textid . '">' . $category3->title . '</a> / ' . $pagepath;
					}
				}
			}
		}

		/* ielādē moduļa funkcijas */
		if (file_exists(CORE_PATH . '/modules/' . $category->module . '/functions.' . $category->module . '.php')) {
			require(CORE_PATH . '/modules/' . $category->module . '/functions.' . $category->module . '.php');
		}

		/* ielādē moduli */
		require(CORE_PATH . '/modules/' . $category->module . '/' . $category->module . '.php');

		/* ajax pieprasījumus te arī izbeidzam */
		if ($is_ajax) {
			$tpl->printToScreen();
			exit;
		}
	} else {

		//mēģinam apskatīties vai šāda sadaļa neeksistē citā domēnā, ja eksistē - redirekts
		if (isset($_GET['viewcat'])) {
			$cat = $db->get_row("SELECT `textid`, `lang` FROM `cat` WHERE `textid` = '" . sanitize($_GET['viewcat']) . "' ORDER BY `id` ASC LIMIT 1");
			if (!empty($cat)) {
				redirect(get_protocol($cat->lang) . $config_domains[$cat->lang]['domain'] . '/' . $cat->textid, true);
			}
		}

		// 404
		error_404();
	}
}

// #rs apakšprojekts ielādē failu, kas veic papildpārbaudes
if ($lang === 9) {
	include(CORE_PATH . '/modules/runescape/init.php');
}

//izdomā, ko darīt ar templeita opšeniem (rādīt vai nerādīt kreiso un labo kolonnu)
//noklusēti katrai kategorijai tas ir norādīts db vai lapas konfigā, bet var manuāli pārrakstīt modulī ar $tpl_options
if (empty($tpl_options) && isset($category) && !empty($category->options)) {
	$tpl_options = $category->options;
}

// Spēles un moduļi, kuriem nepieciešama pilnā JS bibliotēka (jQuery)
$jquery_modules = [
	'2048', 'arkanoid', 'augsup', 'crows', 'desas', 'flappy', 'invaders',
	'karatavas', 'memory', 'minu-mekletajs', 'pacman', 'register', 'rezonanse', 'rulete', 'runner', 'snake', 'speles',
	'steam-online', 'sudoku', 'tanki', 'tetris', 'tic-tac-toe', 'tornis', 'ut99', 'vardes', 'wg-play', 'wordle'
];
$is_game = (isset($category) && (
	(!empty($category->module) && in_array($category->module, $jquery_modules)) ||
	(!empty($category->textid) && in_array($category->textid, $jquery_modules)) ||
	(!empty($category->parent) && $category->parent == 2516)
));
if ($is_game) {
	$require_jquery = true;
}
$is_miniblog = (isset($_GET['m']) || (isset($category) && in_array($category->textid, ['say', 'miniblogs', 'miniblog'])) || (isset($category) && in_array($category->module, ['miniblogs', 'miniblog'])));
$is_read = (isset($_GET['viewcat']) && $_GET['viewcat'] === 'read');

$game_info_queried = false;
if ($is_game && isset($tpl)) {
	$game_info = $db->get_row("SELECT * FROM `games` WHERE `slug` = '" . sanitize($category->textid) . "' OR `slug` = '" . sanitize($category->module) . "' OR `game_code` = '" . sanitize($category->module) . "' LIMIT 1");
	$game_info_queried = true;
	if ($game_info) {
		$tpl->assignGlobal('game-rate', get_game_rate_html($game_info));
	}
}

$is_real_game = (isset($category) && (
	(!empty($category->parent) && $category->parent == 2516) ||
	(!empty($category->module) && in_array($category->module, ['speles', 'pacman', 'tanki', 'rezonanse', 'wg-play', 'karatavas', 'arkanoid', 'tornis', 'runner', 'flappy', 'invaders', 'tetris', 'snake', 'wordle', 'memory', '2048', 'minu-mekletajs', 'sudoku', 'rulete', 'desas', 'augsup', 'vardes', 'ut99'])) ||
	(!empty($category->textid) && in_array($category->textid, ['speles', 'pacman', 'tanki', 'rezonanse', 'wg-play', 'karatavas', 'arkanoid', 'tornis', 'runner', 'flappy', 'invaders', 'tetris', 'snake', 'wordle', 'memory', '2048', 'minu-mekletajs', 'sudoku', 'rulete', 'desas', 'augsup', 'vardes', 'ut99'])) ||
	!empty($game_info)
));

if ($is_real_game) {
	$require_jquery = true;
	$add_css[] = 'game-chat.1c37d7cd.min.css';
	$current_game_slug = 'speles';
	$current_game_title = 'Spēļu katalogs';
	if (!empty($game_info)) {
		$current_game_slug = $game_info->slug;
		$current_game_title = $game_info->title;
	} elseif (isset($category) && $category->module !== 'speles') {
		$current_game_slug = !empty($category->textid) ? $category->textid : $category->module;
		$current_game_title = $category->title;
	}
}

//lietotājam specifiskās fīčas
if ($skin === 'main') {
	if ($auth->ok !== true && empty($require_jquery) && empty($force_full_js)) {
		$tpl->newBlock('guest-js');
		$tpl->newBlock('login-form');
		$tpl->assignGlobal('xsrf', $auth->xsrf);
	} else {
		$tpl->newBlock('user-js');

		if ($auth->ok !== true) {
			$tpl->newBlock('login-form');
			$tpl->assignGlobal('xsrf', $auth->xsrf);
		} else {
			$tpl->newBlock('user-menu');

			if (im_mod()) {
				$tpl->newBlock('user-modlink');
				if (($auth->id == 1 || $auth->id == 115) && $lang == 1) {
					$tpl->newBlock('user-modlink-adm');
				}
				$tpl->newBlock('user-approvelink');
				$new_approve = $db->get_var("SELECT count(*) FROM `approve` WHERE `removed` = 0 AND `lang` = '$lang'");
				if ($new_approve) {
					$new_ap_string = '&nbsp;(<span class="r">' . $new_approve . '</span>)';
				}
			} else {
				$tpl->newBlock('user-write');
			}
		}
	}
}

$persona = '';
if (!empty($inprofile) && !empty($inprofile->persona)) {
	$persona = ' style="background:url(\'/bildes/personas/' . $inprofile->persona . '\') repeat-x 0 0;background-size:cover;"';
} elseif (!empty($ingroup) && !empty($ingroup->persona)) {
	$persona = ' style="background:url(\'/bildes/personas/' . $ingroup->persona . '\') repeat-x 0 0;background-size:cover;"';
} elseif (!empty($auth->persona)) {
	$persona = ' style="background:url(\'/bildes/personas/' . $auth->persona . '\') repeat-x 0 0;background-size:cover;"';
} elseif (!empty($category->persona)) {
	$persona = ' style="background:url(\'/bildes/personas/' . $category->persona . '\') repeat-x 0 0;background-size:cover;"';
} elseif ($lang == 3) {
	$persona = ' style="background:url(\'/bildes/personas/gear.png\') repeat-x 0 0;background-size:cover;"';
} else {
	$persona = ' style="background:url(\'/bildes/personas/gaming.jpg\') repeat-x 0 0;background-size:cover;"';
}

//Latvijas valsts svētki
if (in_array(date('m-d'), ['01-20', '05-01', '05-04', '11-11', '11-18', '06-03'])) {
	$persona = ' style="height:157px;background:url(\'/bildes/personas/lielvardes_josta.jpg\') repeat-x 50% -25px;background-size:cover;"';
}

$in_level = 0;
if (!empty($inprofile)) {
	$in_level = $inprofile->level;
}


$load = sys_getloadavg();
$mb_refresh_limit = '8000';
if ($load[0] > 5) {
	$mb_refresh_limit = '180000';
} elseif ($load[0] > 4) {
	$mb_refresh_limit = '120000';
} elseif ($load[0] > 3) {
	$mb_refresh_limit = '60000';
} elseif ($load[0] > 2) {
	$mb_refresh_limit = '30000';
} elseif ($load[0] > 1) {
	$mb_refresh_limit = '16000';
}

$today_date = date_lv('l, j. F', time());

if ($auth->hosts_online > $ss->get('most_online')) {
	$ss->set('most_online', $auth->hosts_online);
	$ss->set('most_online_time', time());
}

$page_title = hide_spoilers($page_title);
if (mb_strlen($page_title, 'UTF-8') < 55 && $lang != 4) {
	if (!empty($page_title)) {
		$page_title .= ' - ';
	}
	$page_title .= $config_domains[$lang]['domain'];
}

$login_url = h($_SERVER['REQUEST_URI']);
if (!empty($secure_login)) {
	$login_url = h('https://secure.exs.lv/');
}

if ($auth->skin == 1 && ($lang == 1 || $lang == 3)) {
	$add_css[] = 'manual-dark.b8242589.min.css';
} elseif ($auth->ok === true && $auth->skin == 0 && ($lang == 1 || $lang == 3)) {
	//light skin
} elseif ($lang === 1 || $lang === 3) {
	$add_css[] = 'auto-dark.26c2b05a.min.css';
}

// noteiks vēl nearhivēto sūdzību skaitu mod izvēlnei
if (im_mod()) {
	$new_reports_count = (int) $db->get_var("
		SELECT (
			(SELECT count(*) FROM `reports` JOIN `miniblog` ON `reports`.`entry_id` = `miniblog`.`id` WHERE `reports`.`type` = 0 AND `reports`.`archived` = 0 AND `reports`.`removed` = 0 AND `reports`.`site_id` = $lang) +
			(SELECT count(*) FROM `reports` JOIN `comments` ON `reports`.`entry_id` = `comments`.`id` WHERE `reports`.`type` = 1 AND `reports`.`archived` = 0 AND `reports`.`removed` = 0 AND `reports`.`site_id` = $lang) +
			(SELECT count(*) FROM `reports` JOIN `galcom` ON `reports`.`entry_id` = `galcom`.`id` WHERE `reports`.`type` = 2 AND `reports`.`archived` = 0 AND `reports`.`removed` = 0 AND `reports`.`site_id` = $lang)
		)
	");
	$new_reports_count = $new_reports_count ? ' (<span class="r">' . $new_reports_count . '</span>)' : '';

	$new_polls_count = (int) $db->get_var("
		SELECT count(*) FROM `poll`
		WHERE `approved` = 0 AND `lang` = '$lang'
	");
	$new_polls_count_str = $new_polls_count ? ' (<span class="r">' . $new_polls_count . '</span>)' : '';
} else {
	$new_reports_count = '';
	$new_polls_count_str = '';
}

//assigno visur izmantotas vērtības
$tpl->assignGlobal([
	'page-title' => $page_title,
	'page-loginurl' => $login_url,
	'page-time' => time(),
	'page-url' => h($_SERVER['REQUEST_URI']),
	'page-domain' => h($_SERVER['HTTP_HOST'] ?? 'exs.lv'),
	'category-url' => isset($category->textid) ? $category->textid : '',
	'category-module' => isset($category->module) ? $category->module : '',
	'is-game-page' => (!empty($is_game) ? 'is-game-page' : ''),
	'is-miniblog-page' => (!empty($is_miniblog) ? 'is-miniblog-page' : ''),
	'is-read-page' => (!empty($is_read) ? 'is-read-page page-read' : ''),
	'currentuser-nick' => h($auth->nick),
	'inprofile-level' => $in_level,
	'new-messages' => $new_msg_html,
	'new-messages-count' => (int) $new_msg_string,
	'new-approve' => $new_ap_string,
	'reports-count' => $new_reports_count,
	'new-polls-count' => $new_polls_count_str,
	'layout-options' => $tpl_options,
	'currentuser-id' => $auth->id,
	'current-date' => $today_date,
	'page-onlinetotal' => $auth->hosts_online,
	'page-persona' => $persona,
	'page-onlineusers' => get_online_list(),
	'current-year' => date('Y'),
	'mb-refresh-limit' => $mb_refresh_limit,
	'static-server' => $static_server,
	'img-server' => $img_server,
	'logout-hash' => $auth->logout_hash
]);

if (isset($category) && (!empty($category->textid) || !empty($category->module))) {
	$game_slug_candidates = array_unique(array_filter([$category->textid ?? '', $category->module ?? '']));
	foreach ($game_slug_candidates as $game_slug_candidate) {
		$game_img_file = CORE_PATH . '/bildes/speles/' . $game_slug_candidate . '.png';
		if (file_exists($game_img_file)) {
			$img_url = 'https://exs.lv/bildes/speles/' . $game_slug_candidate . '.png';
			$opengraph_meta['image'] = $img_url;
			$twitter_meta['card'] = 'summary_large_image';
			$twitter_meta['image'] = $img_url;
			break;
		}
	}
}

if ($lang !== 1 && $lang !== 3) {
	$tpl->assignGlobal([
		'footer-mb' => get_footer_mb(),
		'footer-topics' => get_footer_topics(),
	]);
}

if (!empty($add_css)) {
	foreach ($add_css as $css_file) {
		$tpl->newBlock('additional-css');
		$tpl->assign('filename', $css_file);
	}
}

if (!empty($pagepath) && $skin === 'main') {
	$tpl->newBlock('page-path');
	$tpl->assign('page-path', $pagepath);
}

//lai var iezīmēt aktīvo menuci
if (isset($category) && !isset($_GET['u']) && !isset($_GET['g']) && !isset($_GET['m'])) {

	$tpl->assignGlobal([
		'cat-sel-' . $category->id => ' class="selected active"',
		'cat-sel-' . $category->textid => ' class="selected active"',
		'cat-sel-' . $category->parent => ' class="selected active"',
	]);
	if ($category->parent) {
		$topcat = get_cat($category->parent);
		if ($topcat && $topcat->parent) {
			$tpl->assignGlobal([
				'cat-sel-' . $topcat->parent => ' class="selected active"',
			]);
		}
	}
}

//kreisā kolonna
if ($tpl_options != 'no-left' && $tpl_options != 'no-left-right') {
	$left_sidebar_file = CORE_PATH . '/includes/left_' . (int)$lang . '.php';
	if (is_file($left_sidebar_file)) {
		include($left_sidebar_file);
	}
}

//labā kolonna
if ($tpl_options != 'no-right' && $tpl_options != 'no-left-right') {
	$right_sidebar_file = CORE_PATH . '/includes/right_' . (int)$lang . '.php';
	if (is_file($right_sidebar_file)) {
		include($right_sidebar_file);
	}
}

if ($skin === 'main') {

	if ($auth->ok === true) {

		$g_owners = $db->get_results("SELECT title,id,avatar,owner_seenposts,posts FROM clans WHERE owner = '$auth->id' AND `lang` = '$lang' ORDER BY title ASC");
		$g_members = $db->get_results("SELECT
			`clans_members`.`clan` AS `clan`,
			`clans_members`.`moderator` AS `moderator`,
			`clans_members`.`seenposts` AS `seenposts`,
			`clans`.`posts` AS `posts`,
			`clans`.`avatar` AS `avatar`,
			`clans`.`title` AS `title`
		FROM
			`clans_members`,
			`clans`
		WHERE
			`clans_members`.`user` = '$auth->id' AND
			`clans_members`.`approve` = '1' AND
			`clans`.`id` = `clans_members`.`clan` AND
			`clans`.`lang` = '$lang' AND
			`clans`.`last_activity` >= NOW() - INTERVAL 12 MONTH
		ORDER BY
			`clans_members`.`moderator` DESC,
			`clans_members`.`date_added` ASC");

		if ($g_owners or $g_members) {
			$tpl->newBlock('mygroups');
			if ($g_owners) {
				foreach ($g_owners as $g_owner) {
					$tpl->newBlock('myg-node');
					$class = 'l-gadmin';
					$newposts = $g_owner->posts - $g_owner->owner_seenposts;
					$unread = '';
					$css_class = '';
					if ($newposts > 0) {
						$unread = '<span class="gm-unread">' . $newposts . '</span>';
						$css_class = ' class="is-unread"';
					}
					if (empty($g_owner->avatar)) {
						$g_owner->avatar = 'none.png';
					}
					$tpl->assign([
						'id' => $g_owner->id,
						'class' => $class,
						'title' => $g_owner->title,
						'avatar' => $g_owner->avatar,
						'unread' => $unread,
						'unread-class' => $css_class
					]);
				}
			}
			if ($g_members) {
				foreach ($g_members as $g_member) {
					$tpl->newBlock('myg-node');
					if ($g_member->moderator) {
						$class = 'l-gmod';
					} else {
						$class = 'l-gmember';
					}
					$newposts = $g_member->posts - $g_member->seenposts;
					$unread = '';
					$css_class = '';
					if ($newposts > 0) {
						$unread = '<span class="gm-unread">' . $newposts . '</span>';
						$css_class = ' class="is-unread"';
					}
					if (empty($g_member->avatar)) {
						$g_member->avatar = 'none.png';
					}
					$tpl->assign([
						'id' => $g_member->clan,
						'class' => $class,
						'unread' => $unread,
						'unread-class' => $css_class,
						'title' => $g_member->title,
						'avatar' => $g_member->avatar,
					]);
				}
			}
		}
	}
}


if ($auth->ok !== true) {

	if (!empty($category->noindex)) {
		$robotstag = ['noindex'];
	}

	//if($has_yt && $category->module !== 'wall') {
	//	$robotstag[] = 'noindex';
	//}

	/* robots meta taga pievienošana */
	if (!empty($robotstag)) {
		$robotstag = array_unique($robotstag);
		$tpl->newBlock('robots');
		$tpl->assign('value', implode(',', $robotstag));
	}
}

/* meta description */
if (empty($meta_description_added)) {
	$desc_val = '';
	if (!empty($meta_description)) {
		$desc_val = $meta_description;
	} elseif (isset($category) && !empty($category->content) && !in_array($category->module, ['group', 'search']) && !is_numeric(trim($category->content))) {
		$desc_val = $category->content;
	} elseif (isset($category) && !empty($category->intro) && is_string($category->intro) && strlen(trim($category->intro)) > 5 && !is_numeric(trim($category->intro))) {
		$desc_val = $category->intro;
	} elseif (!empty($opengraph_meta['description'])) {
		$desc_val = $opengraph_meta['description'];
	} else {
		$desc_val = 'EXS.LV — spēļu un izklaides portāls Latvijā. Bezmaksas tiešsaistes spēles pārlūkā, videospēļu un filmu apskati, jaunumi, aktīvs forums un miniblogi.';
	}

	$raw_desc = trim(preg_replace('/\s+/', ' ', strip_tags($desc_val)));
	if (mb_strlen($raw_desc, 'UTF-8') > 160) {
		$cut_desc = mb_substr($raw_desc, 0, 157, 'UTF-8');
		$last_space = mb_strrpos($cut_desc, ' ', 0, 'UTF-8');
		if ($last_space !== false && $last_space > 100) {
			$cut_desc = mb_substr($cut_desc, 0, $last_space, 'UTF-8');
		}
		$raw_desc = rtrim($cut_desc, " \t\n\r\0\x0B.,;:-") . '...';
	}
	$clean_desc = h($raw_desc);
	$tpl->newBlock('meta-description');
	$tpl->assign('description', $clean_desc);
	$meta_description_added = true;

	if (empty($opengraph_meta['description'])) {
		$opengraph_meta['description'] = $clean_desc;
	}
}

/* opengraph meta tagi */
if (!empty($is_real_game) && empty($opengraph_meta['title']) && !empty($page_title)) {
	$opengraph_meta['title'] = h($page_title);
}
if (!empty($opengraph_meta)) {
	foreach ($opengraph_meta as $key => $val) {
		$tpl->newBlock('og-meta');
		$tpl->assign([
			'key' => $key,
			'val' => $val
		]);
	}
}

/* twitter meta tagi */
if (!empty($twitter_meta)) {
	foreach ($twitter_meta as $key => $val) {
		$tpl->newBlock('twitter-meta');
		$tpl->assign([
			'key' => $key,
			'val' => $val
		]);
	}
}

/* canonical tag fallback */
$has_noindex = (!empty($robotstag) && in_array('noindex', $robotstag)) || (!empty($category->noindex));
if (empty($canonical) && isset($category) && !$has_noindex && http_response_code() !== 404) {
	$cat_slug = ($category->textid === 'home' || $category->textid === '' ? '' : $category->textid);
	$canonical = get_protocol($lang) . get_domain($lang) . '/' . $cat_slug;
}

if (!empty($canonical) && !$has_noindex && http_response_code() !== 404) {
	$tpl->newBlock('canonical');
	$tpl->assign('url', htmlspecialchars($canonical));
}

/* JSON-LD Structured Data */
$json_ld_items = [
	[
		'@context' => 'https://schema.org',
		'@type' => 'WebSite',
		'name' => 'exs.lv',
		'url' => 'https://exs.lv',
		'potentialAction' => [
			'@type' => 'SearchAction',
			'target' => 'https://exs.lv/search?q={search_term_string}',
			'query-input' => 'required name=search_term_string'
		]
	],
	[
		'@context' => 'https://schema.org',
		'@type' => 'Organization',
		'name' => 'EXS.LV',
		'url' => 'https://exs.lv',
		'logo' => 'https://img.exs.lv/bildes/logos/logo_exs_small.png'
	]
];

if (isset($category) && $category->module !== 'speles' && (
	in_array($category->module, ['snake', 'tetris', 'minu-mekletajs', 'wordle', '2048', 'flappy', 'sudoku', 'memory', 'rulete', 'augsup', 'vardes', 'invaders', 'karatavas', 'runner', 'tornis', 'arkanoid', 'rezonanse', 'desas', 'tic-tac-toe', 'tanki', 'pacman', 'wg-play', 'ut99']) ||
	in_array($category->textid, ['snake', 'tetris', 'minu-mekletajs', 'wordle', '2048', 'flappy', 'sudoku', 'memory', 'rulete', 'augsup', 'vardes', 'invaders', 'karatavas', 'runner', 'tornis', 'arkanoid', 'rezonanse', 'desas', 'tic-tac-toe', 'tanki', 'pacman', 'wg-play', 'ut99']) ||
	!empty($game_info)
)) {
	$game_names = [
		'snake' => 'Čūska', 'tetris' => 'Tetris', 'minu-mekletajs' => 'Mīnu Meklētājs',
		'wordle' => 'Wordle', '2048' => '2048', 'flappy' => 'Lidojošais Eksis',
		'sudoku' => 'Sudoku', 'memory' => 'Atmiņas spēle', 'rulete' => 'Rulete',
		'augsup' => 'Augšup', 'vardes' => 'Vardes', 'invaders' => 'Space Invaders',
		'karatavas' => 'Karātavas', 'wg-play' => 'Karātavas', 'runner' => 'Runner', 'tornis' => 'Tornis',
		'arkanoid' => 'Arkanoid', 'rezonanse' => 'Rezonanse', 'desas' => 'Desas', 'tic-tac-toe' => 'Desas',
		'tanki' => 'Tanki 1990', 'pacman' => 'Exs-Man', 'ut99' => 'Unreal Tournament 99'
	];
	$lookup_key = isset($game_names[$category->textid]) ? $category->textid : $category->module;
	$g_name = isset($game_names[$lookup_key]) ? $game_names[$lookup_key] : (!empty($game_info->title) ? $game_info->title : $category->title);
	$game_schema = [
		'@context' => 'https://schema.org',
		'@type' => 'VideoGame',
		'name' => $g_name,
		'gamePlatform' => ['Web Browser', 'Mobile Web Browser'],
		'applicationCategory' => 'Game',
		'operatingSystem' => 'Any',
		'inLanguage' => 'lv',
		'isAccessibleForFree' => true,
		'offers' => [
			'@type' => 'Offer',
			'price' => '0',
			'priceCurrency' => 'EUR'
		],
		'url' => 'https://exs.lv/' . $category->textid
	];
	if (!empty($meta_description)) {
		$game_schema['description'] = trim(preg_replace('/\s+/', ' ', strip_tags($meta_description)));
	} elseif (!empty($category->content) && !is_numeric(trim($category->content))) {
		$game_schema['description'] = trim(preg_replace('/\s+/', ' ', strip_tags($category->content)));
	}
	if (!empty($opengraph_meta['image'])) {
		$game_schema['image'] = $opengraph_meta['image'];
	} elseif (!empty($game_info->icon)) {
		$game_schema['image'] = 'https://exs.lv' . $game_info->icon;
	}
	if (empty($game_info) && empty($game_info_queried) && isset($db)) {
		$game_info = $db->get_row("SELECT * FROM `games` WHERE `slug` = '" . sanitize($category->textid) . "' OR `slug` = '" . sanitize($category->module) . "' OR `game_code` = '" . sanitize($category->module) . "' LIMIT 1");
	}
	if (!empty($game_info)) {
		$votes_up = max(0, (int) $game_info->votes_up);
		$votes_down = max(0, (int) $game_info->votes_down);
		$total_votes = $votes_up + $votes_down;
		if ($total_votes > 0) {
			$rating_value = round(1 + (4 * ($votes_up / $total_votes)), 2);
			$game_schema['aggregateRating'] = [
				'@type' => 'AggregateRating',
				'ratingValue' => number_format($rating_value, 2, '.', ''),
				'ratingCount' => $total_votes,
				'bestRating' => 5,
				'worstRating' => 1
			];
		}
	}
	$json_ld_items[] = $game_schema;
}

if (!empty($is_read) && isset($article) && !empty($article->title)) {
	$art_date = !empty($article->date) ? strtotime($article->date) : time();
	$art_updated = !empty($article->updated) ? strtotime($article->updated) : $art_date;
	$art_author_obj = !empty($article_author) && is_object($article_author) ? $article_author : (isset($author) && is_object($author) ? $author : null);
	$art_author_schema = [
		'@type' => 'Person',
		'name' => !empty($art_author_obj->nick) ? $art_author_obj->nick : 'EXS.LV Autors'
	];
	if (!empty($art_author_obj->id)) {
		$art_author_schema['url'] = 'https://exs.lv/user/' . (int) $art_author_obj->id;
	}

	$blog_posting = [
		'@context' => 'https://schema.org',
		'@type' => 'BlogPosting',
		'headline' => trim(strip_tags($article->title)),
		'datePublished' => date(DATE_ATOM, $art_date),
		'dateModified' => date(DATE_ATOM, $art_updated),
		'mainEntityOfPage' => 'https://exs.lv/read/' . (!empty($article->strid) ? $article->strid : ''),
		'author' => $art_author_schema,
		'publisher' => [
			'@type' => 'Organization',
			'name' => 'EXS.LV',
			'logo' => [
				'@type' => 'ImageObject',
				'url' => 'https://img.exs.lv/bildes/logos/logo_exs_small.png'
			]
		]
	];
	if (!empty($opengraph_meta['image'])) {
		$blog_posting['image'] = $opengraph_meta['image'];
	}
	$json_ld_items[] = $blog_posting;
}

$tpl->newBlock('json-ld');
$tpl->assign('json-ld-content', json_encode($json_ld_items, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));


/* flash error or success message */
if (!empty($_SESSION['flash_message'])) {
	$tpl->newBlock('flash-message');
	$tpl->assign([
		'message' => add_smile($_SESSION['flash_message']['message']),
		'class' => $_SESSION['flash_message']['class']
	]);
	$_SESSION['flash_message'] = '';
}

// Kopējā spēļu tērzētava
if (!empty($is_real_game) && isset($tpl)) {
	$tpl->newBlock('game-chat-section');
	$tpl->assign([
		'game-chat-slug' => h($current_game_slug),
		'game-chat-title' => h($current_game_title),
		'game-chat-html' => get_game_chat_html($current_game_slug, $current_game_title)
	]);
}

//aizveram konekciju lai nekarājas, ja satura sūtīšana ieilgst
$db->close();

if (isset($_GET['vc'])) {
	die('');
}

$tpl->printToScreen();

/*if ($debug && !$requested_json) {
	echo '<div style="color:#eee;background:#222;font-size:9px;padding:0;margin:0;width:100%;"><div style="padding:2px 0;margin:0 auto;width:960px;">';
	echo '<div>atmiņa: ' . round((memory_get_usage() / 1024 / 1024), 3) . ' mb';
	echo ' | peak atmiņa: ' . round((memory_get_peak_usage() / 1024 / 1024), 3) . ' mb';
	echo ' | ielāde: ' . round(microtime(true) - $start_time, 5) . ' s';
	echo ' | mysql: ' . $db->num_queries . ' q';
	echo ' | load avg: ' . $load[0];
	if (!empty($category->id)) {
		echo ' | cat_id:' . $category->id . ' (textid:' . $category->textid . ', module:' . $category->module . ')';
	}
	echo '</div></div></div>';
}*/