<?php

/**
 * Last.fm profila autorizācija,
 * lai varētu ievākt lietotāja klausītās dziesmas   
 */
if ($auth->ok === true) {
	require_once(LIB_PATH . '/phplastfm/lastfmapi/lastfmapi.php');

	//apstrādā no last.fm atgriezto autorizāciju
	if (!empty($_GET['token'])) {

		$vars = [
			'apiKey' => $lastfm_apikey,
			'secret' => $lastfm_secret,
			'token' => $_GET['token']
		];

		$lastfm_auth = new lastfmApiAuth('getsession', $vars);

		if (!empty($lastfm_auth->username)) {
			$db->update('users', $auth->id, [
				'lastfm_token' => $lastfm_auth->token,
				'lastfm_username' => $lastfm_auth->username,
				'lastfm_sessionkey' => $lastfm_auth->sessionKey,
				'lastfm_subscriber' => $lastfm_auth->subscriber
			]);
		}
		get_user($auth->id, true);

		redirect('/lastfm');
	}

	if (!empty($auth->lastfm_username)) {

		//saglabā iestatījumus
		if (isset($_POST['friends-do'])) {

			$db->update('users', $auth->id, [
				'lastfm_onlyfriends' => (bool) $_POST['friends']
			]);
			get_user($auth->id, true);

			die('ok');
		}

		//profils ir savienots ar lastfm, updatojam dziesmas un rādam ka viss ok
		lastfm_update_tracks($auth->id);
		$tpl->newBlock('lastfm-success');

		//mark checkbox
		$friendsmark = '';
		if ($auth->lastfm_onlyfriends) {
			$friendsmark = ' checked="checked"';
		}
		$tpl->assign('friendsmark', $friendsmark);

	} else {

		//profils NAV savinots ar last.fm, rādam pogu
		$tpl->newBlock('lastfm-auth');
		$tpl->assign('key', $lastfm_apikey);
	}
} else {

	//login logs
	$tpl->newBlock('error-nologin');
}

