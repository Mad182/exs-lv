<?php

$file = '';
if (!empty($_GET['var1'])) {
	$file = $_GET['var1'];
}

ksort($_GET);
$data = array();
foreach ($_GET as $k => $v) {
	if (!in_array($k, array('viewcat', 'var1'))) {
		$data[$k] = $v;
	}
}

ksort($_POST);
$data['ea-post'] = array();
foreach ($_POST as $k => $v) {
	if (!in_array($k, array('niks', 'parole', 'login-submit'))) {
		$data['ea-post'][$k] = $v;
	}
}

ksort($_SESSION);
$data['ea-session'] = array();
foreach ($_GET as $k => $v) {
	$data['ea-session'][$k] = $v;
}

ksort($_COOKIE);
$data['ea-session'] = array();
foreach ($_COOKIE as $k => $v) {
	$data['ea-cookie'][$k] = $v;
}

if (empty($auth->avatar)) {
	$av = 'none.png';
} else {
	$av = $auth->avatar;
}

$data['ea-user'] = array(
	'id' => $auth->id,
	'nick' => $auth->nick,
	'av' => $av,
	'type' => $auth->level,
	'ip' => $auth->ip
);

$data['ea-sig'] = md5(serialize($data) . $category->secret);
$send = strtr(base64_encode(addslashes(gzcompress(serialize($data), 9))), '+/=', '-_,');

if ($category->textid == 'psychostats') {

	redirect('http://exs.lv/');

	/*$cache_file = 'cache/psychostats/' . md5(serialize($_GET));
	if (file_exists($cache_file) && time() - filemtime($cache_file) < 3600) {
		$contents = file_get_contents($cache_file);
	} else {
		$contents = str_replace(array('	', "\r", "\n"), '', file_get_contents($category->content . $file . '?ea-data=' . $send));
		$contents = get_between($contents, '<html xmlns="http://www.w3.org/1999/xhtml" lang="en">', '</html>');
		$handle = fopen($cache_file, 'wb');
		fwrite($handle, $contents);
		fclose($handle);
	}*/

} elseif ($category->textid == 'unreal') {
	$cache_file = 'cache/unreal/' . md5(serialize($_GET) . '|' . serialize($_POST));
	if (file_exists($cache_file) && time() - filemtime($cache_file) < 3600) {
		$contents = file_get_contents($cache_file);
	} else {
		$contents = str_replace(array('	', "\r", "\n"), '', file_get_contents($category->content . $file . '?ea-data=' . $send));
		$handle = fopen($cache_file, 'wb');
		fwrite($handle, $contents);
		fclose($handle);
	}
} else {
	$contents = file_get_contents($category->content . $file . '?ea-data=' . $send);
}

if ($category->textid == 'bans') {
	//header('HTTP/1.1 503 Service Temporarily Unavailable');
//	header('Location: http://exs.lv/');
	include('modules/api/inc/amxbans.php');
}
if ($category->textid == 'psychostats') {
	include('modules/api/inc/psychostats.php');
}
if ($category->textid == 'unreal') {
	include('modules/api/inc/unreal.php');
}

if (!empty($http_response_header)) {
	foreach ($http_response_header as $header) {
		if (substr($header, 0, 9) == 'Location:' || substr($header, 0, 9) == 'location:') {
			if ($category->textid == 'bans') {
				$header = str_replace('Location: ', 'Location: /bans/', $header);
			}
			header($header);
			exit;
		}
	}
}

$tpl->assignGlobal('api-title', $category->title);
$tpl->assignGlobal('api-content', $contents);

$disable_f_ad = true;
?>
