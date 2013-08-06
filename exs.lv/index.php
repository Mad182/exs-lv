<?php

require('configdb.php');

/* ielādē kopīgos failus */
require(CORE_PATH . '/includes/class.mdb.php');
require(CORE_PATH . '/includes/class.auth.php');
require(CORE_PATH . '/includes/functions.core.php');
require(CORE_PATH . '/includes/class.templatepower.php');
require(CORE_PATH . '/includes/class.site_storage.php');

/* nosaka, kuru lapu rādīt (exs.lv, coding.lv, etc) */
require(CORE_PATH . '/includes/site_loader.php');

session_start();

//mysql konekcija
$db = new mdb($username, $password, $database, $hostname);
unset($password);

//memcached konekcija
$m = new Memcache;
$m->connect($mc_host, $mc_port);

//lapas settingu/datu glabāšana
$ss = new SiteStorage;

if ($debug) {
	echo '<div style="color:#eee;background:#222;font-size:9px;padding:0;margin:0;width:100%;"><div style="padding:2px 0;margin:0 auto;width:960px;">';
}

//redirektē vecos runescape.ex.lv linkus uz pareizu jauno linku
if (isset($_GET['kategorija']) && isset($_GET['id'])) {
	//raksti
	$redirect = $db->get_row("SELECT `strid` FROM `pages` WHERE `textid` = '" . sanitize($_GET['id']) . "'");
	redirect('http://exs.lv/read/' . $redirect->strid, true);
} elseif (isset($_GET['id']) && !isset($_GET['viewcat'])) {
	//kategorijas
	$category = get_cat($_GET['id']);
	redirect('http://exs.lv/' . $category->textid, true);
}

//izveido aktīvā lietotāja objektu
$auth = new Auth();

//login
if (isset($_POST['niks']) && isset($_POST['parole']) && isset($_POST['xsrf_token'])) {
	$auth->login($_POST['niks'], $_POST['parole'], $_POST['xsrf_token']);
	if ($auth->ok === true) {
		update_karma($auth->id);
	}
}

if ($auth->ok && $lang == 1 && (!isset($_GET['viewcat']) || $_GET['viewcat'] != 'interests') && empty($_POST) && !isset($_GET['_']) && !$db->get_var("SELECT `interest_quiz` FROM `users` WHERE `id` = '$auth->id'")) {
	redirect('/interests');
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
	$data = array();
	if (isset($_GET['loadpm'])) {
		$data['pm-count'] = $new_msg_string;
	}
	if (isset($_GET['loadgallery'])) {
		$data['in-tabs'] = get_latest_images();
	} elseif (isset($_GET['loadposts'])) {
		$data['in-tabs'] = get_latest_posts();
	}
	if (isset($_GET['loadcs'])) {
		$data['cs-content'] = file_get_contents(CORE_PATH . '/cache/cs_monitor.html');
	}
	if (isset($_GET['loadmc'])) {
		$data['mc-content'] = file_get_contents(CORE_PATH . '/cache/mc_monitor.html');
	}
	if (isset($_GET['loadmta'])) {
		$data['mta-content'] = file_get_contents(CORE_PATH . '/cache/mta_monitor.html');
	}
	if (isset($_GET['loadmb'])) {
		$data['mb-latest'] = get_latest_mbs(!empty($_GET['friendmb']));
	}
	if (isset($_GET['loadindex'])) {
		$data['index-events'] = get_index_events();
	}
	header("Content-Type: application/json");
	echo json_encode($data);
	exit;
}

//laicīgi novēršam enkodinga gļukus stulbos pārlūkos
header('Content-Type: text/html; charset=UTF-8');

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
	}
} else {

	/*
	  atrod moduli (cat tabula) ko rādīt
	 */
	if (isset($_GET['viewcat'])) {
		$category = get_cat($_GET['viewcat']);
		$cat = $category->id;
	} else {
		if (isset($_GET['c'])) {
			$cat = (int) $_GET['c'];
		}
		$category = get_cat($cat);
	}
	if ($category->tmpl) {
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
} elseif (isset($_GET['r'])) {
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
				$m->set('tpl_' . $lang . '_' . $category->module, $tpl, false, 3600);
			} else {
				$tpl = $tpl2;
				unset($tpl2);
			}
		}

		$pagepath = $category->title;

		/* ielade moduļa funkcijas */
		if(file_exists(CORE_PATH . '/modules/' . $category->module . '/functions.' . $category->module . '.php')) {
			require(CORE_PATH . '/modules/' . $category->module . '/functions.' . $category->module . '.php');
		}

		/* ielade moduli */
		require(CORE_PATH . '/modules/' . $category->module . '/' . $category->module . '.php');

		/* ajax pieprasijumus te ari izbeidzam */
		if (isset($_GET['_'])) {
			$tpl->printToScreen();
			exit;
		}

	} else {
		//404
		set_flash('Pieprasītā lapa netika atrasta!', 'error');
		redirect();
	}
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
		$tpl->assign('xsrf', $auth->xsrf);
		if ($auth->error === 1) {
			set_flash('Nepareizs niks un/vai parole! Mēģini vēlreiz, vai izmanto "<a href="/forgot-password">Aizmirsu paroli</a>".', 'error');
			$tpl->newBlock('login-form-error1');
		}
	} else {
		$tpl->newBlock('user-menu');

		if (im_mod()) {
			$tpl->newBlock('user-modlink');
			$tpl->newBlock('user-approvelink');
			$new_approve = $db->get_var("SELECT count(*) FROM `approve` WHERE `removed` = 0");
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
	$persona = ' style="background:url(\'http://exs.lv/bildes/personas/' . $inprofile->persona . '\') repeat-x 0 0"';
} elseif (!empty($auth->persona)) {
	$persona = ' style="background:url(\'http://exs.lv/bildes/personas/' . $auth->persona . '\') repeat-x 0 0"';
} elseif (!empty($category->persona)) {
	$persona = ' style="background:url(\'http://exs.lv/bildes/personas/' . $category->persona . '\') repeat-x 0 0"';
} elseif ($lang == 3) {
	$persona = ' style="background:url(\'http://exs.lv/bildes/personas/gear.png\') repeat-x 0 0"';
} else {
	$persona = ' style="background:url(\'http://exs.lv/bildes/personas/gaming.jpg\') repeat-x 0 0"';
}

$in_level = 0;
$in_gender = 0;
if (!empty($inprofile)) {
	$in_level = $inprofile->level;
	$in_gender = $inprofile->gender;
}

/* if ((im_mod() || $auth->level == 3) && $auth->id != '8872') {
  $idb_count = get_itemsdb_action();
  } */

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

if (!$auth->ok && $lang == 3) {
	$auth->skin = 4;
}

if ($auth->skin == 0) {
	$tinymce_skin_variant = 'silver';
} elseif ($auth->skin == 1) {
	$tinymce_skin_variant = 'black';
}

//reklāmas

$ads_type = '_adsense';
if(!empty($disable_adsense)) {
	$ads_type = '';
}

if ($auth->hosts_online > $ss->get('most_online')) {
	$ss->set('most_online', $auth->hosts_online);
	$ss->set('most_online_time', time());
}

$page_title = hide_spoilers($page_title);
if(strlen($page_title) < 55 && $lang != 4) {
	$page_title .= ' - ' . $config_domains[$lang]['domain'];
}

//assigno visur izmantotas vērtības
$tpl->assignGlobal(array(
	'page-title' => hide_spoilers($page_title),
	'page-url' => htmlspecialchars($_SERVER['REQUEST_URI']),
	'page-domain' => $_SERVER['HTTP_HOST'],
	'page-skinid' => $auth->skin,
	'category-url' => $category->textid,
	'currentuser-nick' => htmlspecialchars($auth->nick),
	'inprofile-level' => $in_level,
	'inprofile-gender' => $in_gender,
	'new-messages' => $new_msg_html,
	'new-messages-count' => (int) $new_msg_string,
	'new-approve' => $new_ap_string,
	'layout-options' => $tpl_options,
	'currentuser-id' => $auth->id,
	'current-date' => $today_date,
	'page-onlinetotal' => $auth->hosts_online,
	'page-persona' => $persona,
	'page-onlineusers' => get_online_list(),
	'mb-refresh-limit' => $mb_refresh_limit,
	'footer-mb' => get_footer_mb(),
	'footer-topics' => get_footer_topics(),
	'add-css' => $add_css,
	'tinymce_skin_variant' => $tinymce_skin_variant,
	'ad-468' => file_get_contents(CORE_PATH . '/tmpl/ads/' . $lang . '_468' . $ads_type . '.tpl'),
	'ad-728' => file_get_contents(CORE_PATH . '/tmpl/ads/' . $lang . '_728' . $ads_type . '.tpl'),
	'ad-top' => file_get_contents(CORE_PATH . '/tmpl/ads/' . $lang . '_top' . $ads_type . '.tpl'),
	'static-server' => $static_server
));
// 'idb-count' => $idb_count,

if (!empty($pagepath) && $skin === 'main') {
	$tpl->newBlock('page-path');
	$tpl->assign('page-path', $pagepath);
}

//lai var iezīmēt aktīvo menuci
if (isset($category) && !isset($_GET['u']) && !isset($_GET['g']) && !isset($_GET['m'])) {

	$tpl->assignGlobal(array(
		'cat-sel-' . $category->id => ' class="selected"',
		'cat-sel-' . $category->parent => ' class="selected"',
	));
	if ($category->parent) {
		$topcat = get_cat($category->parent);
		if ($topcat->parent) {
			$tpl->assignGlobal(array(
				'cat-sel-' . $topcat->parent => ' class="selected"',
			));
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
	if (empty($tpl_options)) {
		$tpl->newBlock('ads-google');
	} elseif (empty($disable_f_ad)) {
		$tpl->newBlock('ads-google-wide');
	}

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
  		WHERE `clans_members`.`user` = '$auth->id' AND `clans_members`.`approve` = '1' AND `clans`.`id` = `clans_members`.`clan` AND `clans`.`lang` = '$lang' ORDER BY `clans_members`.`moderator` DESC, `clans_members`.`date_added` ASC");

		if ($g_owners or $g_members) {
			$tpl->newBlock('mygroups');
			if ($g_owners) {
				foreach ($g_owners as $g_owner) {
					$tpl->newBlock('myg-node');
					$class = 'l-gadmin';
					$newposts = $g_owner->posts - $g_owner->owner_seenposts;
					$add = '';
					if ($newposts > 0) {
						$add = ' [<span class="r">' . $newposts . '</span>]';
					}
					if (empty($g_owner->avatar)) {
						$g_owner->avatar = 'none.png';
					}
					$tpl->assign(array(
						'id' => $g_owner->id,
						'class' => $class,
						'title' => $g_owner->title,
						'avatar' => $g_owner->avatar,
						'add' => $add,
					));
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
					$add = '';
					if ($newposts > 0) {
						$add = ' [<span class="r">' . $newposts . '</span>]';
					}
					if (empty($g_member->avatar)) {
						$g_member->avatar = 'none.png';
					}
					$tpl->assign(array(
						'id' => $g_member->clan,
						'class' => $class,
						'add' => $add,
						'title' => $g_member->title,
						'avatar' => $g_member->avatar,
					));
				}
			}
		}
	}

	/* pec ielades izsauc lapu, kura ir wos counteri */
	/*if(!$db->get_var("SELECT count(*) FROM `async_ip` WHERE `ip` = '$auth->ip'")) {
		$tpl->newBlock('async-call');
	}*/

}

/* robots meta taga pievienošana */
if(!empty($robotstag)) {
	$tpl->newBlock('robots');
	$tpl->assign('value', implode(',',$robotstag));
}

/* flash error or success message */
if (!empty($_SESSION['flash_message'])) {
	$tpl->newBlock('flash-message');
	$tpl->assign(array(
		'message' => add_smile($_SESSION['flash_message']['message']),
		'class' => $_SESSION['flash_message']['class']
	));
	$_SESSION['flash_message'] = '';
}

if ($debug) {
	echo '<div><a id="debug-details-trigger" href="#" style="float:right;color: #ccf;">detaļas &raquo;</a>atmiņa: ' . round((memory_get_usage() / 1024 / 1024), 3) . ' mb';
	echo ' | peak atmiņa: ' . round((memory_get_peak_usage() / 1024 / 1024), 3) . ' mb';
	echo ' | ielāde: ' . round(microtime(true) - $start_time, 5) . ' s';
	echo ' | mysql: ' . $db->num_queries . ' q';
	echo ' | load avg: ' . $load[0];
	if (!empty($category->id)) {
		echo ' | cat_id:' . $category->id . ' (textid:' . $category->textid . ', module:' . $category->module . ')';
	}
	echo '</div><div id="debug-details" style="display:none"><strong>$_GET</strong><br />';
	pr($_GET);
	echo '<strong>$_POST</strong><br />';
	pr($_POST);
	echo '<strong>$auth</strong><br />';
	pr($auth);
	echo '<strong>$_SERVER</strong><br />';
	pr($_SERVER);
	echo '</div></div></div>';
}

//aizveram konekciju lai nekarājas, ja satura sūtīšana ieilgst
$db->close();

if (isset($_GET['vc'])) {
	die('');
}

$tpl->printToScreen();
