<?php

/**
 *
 * index.php
 *
 * Ielādē kopīgos failus, sagatavo globālos mainīgos, ielādē moduli
 *
 */
require('configdb.php');

/* ielādē kopīgos failus */

require(ROOT_PATH  . '/vendor/autoload.php');

require(CORE_PATH . '/includes/class.mdb.php');
require(CORE_PATH . '/includes/class.auth.php');
require(CORE_PATH . '/includes/functions.core.php');
require(CORE_PATH . '/includes/class.templatepower.php');
require(CORE_PATH . '/includes/class.site_storage.php');
require(CORE_PATH . '/includes/class.cookie.tracking.php');

/* nosaka, kuru lapu rādīt (exs.lv, coding.lv, etc) */
require(CORE_PATH . '/includes/site_loader.php');

//rewrite hack
if(!empty($_GET['fakeurl'])) {
	$parts = explode('/', $_GET['fakeurl']);
	$_GET['viewcat'] = $parts[0];
	if(!empty($parts[1])) {
		$_GET['var1'] = $parts[1];
	}
	if(!empty($parts[2])) {
		$_GET['var2'] = $parts[2];
	}
	if(!empty($parts[3])) {
		$_GET['var3'] = $parts[3];
	}
	if(!empty($parts[4])) {
		$_GET['var4'] = $parts[4];
	}

	if($_GET['viewcat'] === 'say') {
		$_GET['m'] = $parts[1];
		if(!empty($parts[2])) {
			$mbid = explode('-', $parts[2]);
			if($mbid[0] === 'skip') {
				$_GET['skip'] = $mbid[1];
			} else {
				$_GET['single'] = $mbid[0];
			}
		}
	}

}

if(empty($_GET['viewcat'])) {
	$_GET['viewcat'] = null;
}

session_start();

//mysql konekcija
$db = new mdb($username, $password, $database, $hostname);
unset($password);

//memcached konekcija
$m = new Memcached;
$m->addServer($mc_host, $mc_port);

//lapas settingu/datu glabāšana
$ss = new SiteStorage;

$requested_json = (substr($_SERVER['REQUEST_URI'], -5) === '.json' || (isset($_GET['var1']) && $_GET['var1'] == 'json'));

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
	redirect('https://exs.lv/read/' . $redirect->strid, true);
} elseif (isset($_GET['id']) && !isset($_GET['viewcat'])) {
	//kategorijas
	$category = get_cat($_GET['id']);
	redirect('https://exs.lv/' . $category->textid, true);
}

$site_access = get_site_access();

//izveido aktīvā lietotāja objektu
$auth = new Auth();

//login
if (isset($_POST['niks']) && isset($_POST['parole']) && isset($_POST['xsrf_token'])) {
	$auth->login($_POST['niks'], $_POST['parole'], $_POST['xsrf_token']);

	if ($auth->error === 1) {
		set_flash('Nepareizs niks un/vai parole! Mēģini vēlreiz, vai izmanto "<a href="/forgot-password">Aizmirsu paroli</a>".', 'error');
	}
	if ($auth->ok === true || $auth->error === 3) {
		update_karma($auth->id);
		$cookies = new cookieTracker('_steam', 'T3vN3bu5MusC4k4r3T!!!1', $db);
		$cookies->setCookie();
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

		if (isset($_GET['_'])) {
			$tpl = new TemplatePower(CORE_PATH . '/modules/' . $category->module . '/' . $category->module . '.tpl');
			$tpl->prepare();
		} else {

			$tpl->assignInclude('module-currrent', CORE_PATH . '/modules/' . $category->module . '/' . $category->module . '.tpl');
			//iekešojam sadaļas templeitu. mazliet apgrūtina .tpl failu labošanu, toties -20% ielādes laikam
			if (($tpl2 = $m->get('tpl_' . $lang . '_' . $category->module)) === false || $debug === true) {
				$tpl->prepare();
				$m->set('tpl_' . $lang . '_' . $category->module, $tpl, 3600);
			} else {
				$tpl = $tpl2;
				unset($tpl2);
			}
		}

		$pagepath = $category->title;

		/* ielādē moduļa funkcijas */
		if (file_exists(CORE_PATH . '/modules/' . $category->module . '/functions.' . $category->module . '.php')) {
			require(CORE_PATH . '/modules/' . $category->module . '/functions.' . $category->module . '.php');
		}

		/* ielādē moduli */
        require(CORE_PATH . '/modules/' . $category->module . '/' . $category->module . '.php');

		/* ajax pieprasījumus te arī izbeidzam */
		if (isset($_GET['_'])) {
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

//lietotājam specifiskās fīčas
if ($skin === 'main') {
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

$persona = '';
if (!empty($inprofile) && !empty($inprofile->persona)) {
	$persona = ' style="background:url(\'//exs.lv/bildes/personas/' . $inprofile->persona . '\') repeat-x 0 0;background-size:cover;"';
} elseif (!empty($ingroup) && !empty($ingroup->persona)) {
	$persona = ' style="background:url(\'//exs.lv/bildes/personas/' . $ingroup->persona . '\') repeat-x 0 0;background-size:cover;"';
} elseif (!empty($auth->persona)) {
	$persona = ' style="background:url(\'//exs.lv/bildes/personas/' . $auth->persona . '\') repeat-x 0 0;background-size:cover;"';
} elseif (!empty($category->persona)) {
	$persona = ' style="background:url(\'//exs.lv/bildes/personas/' . $category->persona . '\') repeat-x 0 0;background-size:cover;"';
} elseif ($lang == 3) {
	$persona = ' style="background:url(\'//exs.lv/bildes/personas/gear.png\') repeat-x 0 0;background-size:cover;"';
} else {
	$persona = ' style="background:url(\'//exs.lv/bildes/personas/gaming.jpg\') repeat-x 0 0;background-size:cover;"';
}

//Latvijas valsts svētki
if (in_array(date('m-d'), ['01-20', '05-01', '05-04', '11-11', '11-18'])) {
	$persona = ' style="height:157px;background:url(\'//exs.lv/bildes/personas/lielvardes_josta.jpg\') repeat-x 50% -25px;background-size:cover;"';
}

$in_level = 0;
$in_gender = 0;
if (!empty($inprofile)) {
	$in_level = $inprofile->level;
	$in_gender = $inprofile->gender;
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
if (strlen($page_title) < 55 && $lang != 4) {
	if (!empty($page_title)) {
		$page_title .= ' - ';
	}
	$page_title .= $config_domains[$lang]['domain'];
}

$login_url = h($_SERVER['REQUEST_URI']);
if (!empty($secure_login)) {
	$login_url = h('https://secure.exs.lv/');
}

if ($auth->skin == 1 && $lang == 1) {
	//$add_css[] = 'dark.css';
}

// noteiks vēl nearhivēto sūdzību skaitu mod izvēlnei
if (im_mod()) {
	$new_reports_count = $db->get_var("
		SELECT count(*) FROM `reports`
		WHERE `archived` = 0 AND `site_id` = $lang AND `removed` = 0
	");
	$new_reports_count = ' (<span class="r">' . $new_reports_count . '</span>)';
} else {
	$new_reports_count = 0;
}

//links uz openidea.lv, aktīvs tikai sākumlapās
$openidea = 'SIA Open Idea';
if($_SERVER['REQUEST_URI'] === '/' || $category->textid === 'html-pamati' || $category->textid === 'css-pamati') {
	$openidea = '<a href="https://openidea.lv/" title="Mājas lapas izstrāde un uzturēšana" rel="nofollow">SIA Open Idea</a>';
}

//assigno visur izmantotas vērtības
$tpl->assignGlobal([
	'page-title' => hide_spoilers($page_title),
	'page-loginurl' => $login_url,
	'page-time' => time(),
	'page-url' => h($_SERVER['REQUEST_URI']),
	'page-domain' => $_SERVER['HTTP_HOST'],
	'category-url' => $category->textid,
	'currentuser-nick' => h($auth->nick),
	'inprofile-level' => $in_level,
	'inprofile-gender' => $in_gender,
	'new-messages' => $new_msg_html,
	'new-messages-count' => (int) $new_msg_string,
	'new-approve' => $new_ap_string,
	'reports-count' => $new_reports_count,
	'layout-options' => $tpl_options,
	'currentuser-id' => $auth->id,
	'current-date' => $today_date,
	'page-onlinetotal' => $auth->hosts_online,
	'page-persona' => $persona,
	'page-onlineusers' => get_online_list(),
	'current-year' => date('Y'),
	'mb-refresh-limit' => $mb_refresh_limit,
	'footer-mb' => get_footer_mb(),
	'footer-topics' => get_footer_topics(),
	'static-server' => $static_server,
	'facebook-app-id' => $fb_api_id,
	'img-server' => $img_server,
	'logout-hash' => $auth->logout_hash,
	'openidea' => $openidea
]);

if (!empty($add_css)) {
	$tpl->newBlock('additional-css');
	$tpl->assign('filename', implode(',', $add_css));
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
		if ($topcat->parent) {
			$tpl->assignGlobal([
				'cat-sel-' . $topcat->parent => ' class="selected active"',
			]);
		}
	}
}

//kreisā kolonna
if ($tpl_options != 'no-left' && $tpl_options != 'no-left-right') {
	include(CORE_PATH . '/includes/left_' . $lang . '.php');
}

//labā kolonna
if ($tpl_options != 'no-right' && $tpl_options != 'no-left-right') {
	include(CORE_PATH . '/includes/right_' . $lang . '.php');
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
			DATE(`clans`.`last_activity`) >= DATE(NOW() - INTERVAL 12 MONTH)
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

/* robots meta taga pievienošana */
if (!empty($robotstag)) {
	$robotstag = array_unique($robotstag);
	$tpl->newBlock('robots');
	$tpl->assign('value', implode(',', $robotstag));
}

/* opengraph meta tagi */
if(!empty($opengraph_meta)) {
	foreach($opengraph_meta as $key => $val) {
		$tpl->newBlock('og-meta');
		$tpl->assign([
			'key' => $key,
			'val' => $val
		]);
	}
}

/* twitter meta tagi */
if(!empty($twitter_meta)) {
	foreach($twitter_meta as $key => $val) {
		$tpl->newBlock('twitter-meta');
		$tpl->assign([
			'key' => $key,
			'val' => $val
		]);
	}
}


/* flash error or success message */
if (!empty($_SESSION['flash_message'])) {
	$tpl->newBlock('flash-message');
	$tpl->assign([
		'message' => add_smile($_SESSION['flash_message']['message']),
		'class' => $_SESSION['flash_message']['class']
	]);
	$_SESSION['flash_message'] = '';
}

//aizveram konekciju lai nekarājas, ja satura sūtīšana ieilgst
$db->close();

if (isset($_GET['vc'])) {
	die('');
}

$tpl->printToScreen();

if ($debug && !$requested_json) {
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
}

