<?php

require('../exs.lv/configdb.php');

/* load cammon libraries */
require(ROOT_PATH  . '/vendor/autoload.php');

require(CORE_PATH . '/includes/class.mdb.php');
require('includes/class.auth.php');
require(CORE_PATH . '/includes/functions.core.php');
require(CORE_PATH . '/includes/class.templatepower.php');

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

session_start();

$cat = 'index';
if ($lang === 9 || $lang === 7) $cat = 'wall';
$bootstrap_cache_key = '';

//mysql konekcija
$db = new mdb($username, $password, $database, $hostname);
unset($password);

//memcached konekcija
$m = new Memcached;
$m->addServer($mc_host, $mc_port);

header('Content-Type: text/html; charset=utf-8');

$site_access = get_site_access();

$auth = new Auth();

//login
if (isset($_POST['niks']) && isset($_POST['parole']) && isset($_POST['xsrf_token'])) {
	$auth->login($_POST['niks'], $_POST['parole'], $_POST['xsrf_token']);

	if ($auth->error === 1) {
		set_flash('Nepareizs niks vai parole! Mēģini vēlreiz, vai izmanto "<a href="/forgot-password">Aizmirsu paroli</a>".', 'error');
	}

	if ($auth->ok === true) {
		update_karma($auth->id);
	}

}

if (!$auth->ok && (!isset($_GET['viewcat']) || ($_GET['viewcat'] != 'mav' && $_GET['viewcat'] != 'forgot-password' && $_GET['viewcat'] != 'fb-login' && $_GET['viewcat'] != 'twitter-login'))) {
	$tpl = new TemplatePower('tmpl/login.tpl');
	$tpl->prepare();

	$login_url = h($_SERVER['REQUEST_URI']);
	if (!empty($secure_login)) {
		$login_url = h('https://secure.exs.lv/');
	}

	$tpl->assignGlobal([
		'xsrf' => $auth->xsrf,
	]);

} else {

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
	$tpl->assignInclude('module-core-error', CORE_PATH . '/modules/core/error.tpl');
	$tpl->prepare();

	if (isset($_GET['p'])) {
		$id = (int) $_GET['p'];
		$article = $db->get_row("SELECT * FROM `pages` WHERE `id` = " . $id . "");
		if ($article) {
			redirect('/read/' . $article->strid);
		}
	} elseif (isset($_GET['u'])) {
		include(CORE_PATH . '/modules/core/user.php');
	} elseif (isset($_GET['m'])) {
		include(CORE_PATH . '/modules/core/miniblog.php');
	} elseif (isset($_GET['r'])) {
		include(CORE_PATH . '/modules/core/usertopics.php');
	} elseif (isset($_GET['y'])) {
		include('modules/core/youtube.php');
	} else {
		if (!empty($category)) {
			$page_title = $category->title;

			if (isset($_GET['_'])) {

				if (file_exists('modules/' . $category->module . '/' . $category->module . '.php')) {
					$tpl = new TemplatePower('modules/' . $category->module . '/' . $category->module . '.tpl');
				} else {
					$tpl = new TemplatePower(CORE_PATH . '/modules/' . $category->module . '/' . $category->module . '.tpl');
				}
			} else {

				if (file_exists('modules/' . $category->module . '/' . $category->module . '.php')) {
					$tpl->assignInclude('module-currrent', 'modules/' . $category->module . '/' . $category->module . '.tpl');
				} else {
					$tpl->assignInclude('module-currrent', CORE_PATH . '/modules/' . $category->module . '/' . $category->module . '.tpl');
				}
			}
			$tpl->prepare();

			if ($category->module != 'index') {
				$pagepath = $category->title;
			}

			/* ielade moduļa funkcijas */
			if (file_exists(CORE_PATH . '/modules/' . $category->module . '/functions.' . $category->module . '.php')) {
				require(CORE_PATH . '/modules/' . $category->module . '/functions.' . $category->module . '.php');
			}

			if (file_exists('modules/' . $category->module . '/' . $category->module . '.php')) {
				require('modules/' . $category->module . '/' . $category->module . '.php');
			} else {
				require(CORE_PATH . '/modules/' . $category->module . '/' . $category->module . '.php');
			}
		} else {
			$tpl->newBlock('error-nocat');
			$page_title = 'Kļūda: kategorija nav atrasta!';
		}

		if (isset($_GET['_'])) {
			$tpl->printToScreen();
			exit;
		}
	}

	$tpl->newBlock('user-menu');
	$tpl->assign([
		'url' => mkurl('user', $auth->id, $auth->nick),
	]);
	//unread messages count
	$new_messages = $db->get_var("SELECT count(*) FROM pm WHERE to_uid = '" . $auth->id . "' AND is_read = '0'");
	if ($new_messages) {
		$new_msg_string = '&nbsp;(<span class="red">' . $new_messages . '</span>)';
	}

	$tpl->assignGlobal([
		'currentuser-nick' => h($auth->nick),
		'new-messages' => $new_msg_string,
		'currentuser-id' => $auth->id,
		'currentuser-avatar' => $auth->avatar,
		'logout-hash' => $auth->logout_hash
	]);

	//profile box
	if (isset($category) && $category->isblog != 0) {
		$inprofile = get_user($category->isblog);
	}

	//blog link
	if ($inprofile && $lang == 1) {
		$isblog = get_blog_by_user($inprofile->id);
		if ($isblog) {
			$tpl->newBlock('profilebox-blog-link');
			$tpl->assign('profile-blogid', $isblog);
		}
	}
}

$tpl->assignGlobal([
	'server-name' => h(str_replace('m.', '', $_SERVER['HTTP_HOST'])),
	'page-title' => $page_title,
	'page-url' => h($_SERVER['REQUEST_URI']),
	'current-year' => date('Y'),
	'timestamp' => time(),
	'static-server' => $static_server,
	'img-server' => $img_server,
]);

/* flash error or success message */
if (!empty($_SESSION['flash_message'])) {
	$tpl->newBlock('flash-message');
	$tpl->assign([
		'message' => add_smile($_SESSION['flash_message']['message']),
		'class' => $_SESSION['flash_message']['class']
	]);
	$_SESSION['flash_message'] = '';
}

$tpl->printToScreen();
