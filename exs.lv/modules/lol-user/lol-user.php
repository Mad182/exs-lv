<?php

if (isset($_GET['var1'])) {
	$userid = (int) $_GET['var1'];
	$inprofile = get_user($userid);
} else {
	redirect('/');
}

if ($inprofile && $auth->id == 1) {

	if (isset($_POST['url'])) {
		$url = $_POST['url'];
		$data = curl_get($url);
		if ($data) {

			$nick = get_between($data, 'statistics and LolKing score for ', ', a summoner on');

			if (!empty($nick)) {
				$s1 = str_replace('http://www.lolking.net/summoner/', '', $url);
				$sparts = explode('/', $s1);
				$server = $sparts[0];
				$db->query("INSERT INTO `lol_players` (`user_id`,`lol_nick`,`server`,`url`,`created`) VALUES ('$inprofile->id','$nick', '$server', '$url', NOW())");
				set_flash('Veiksmīgi pievienots!', 'success');
				redirect('/' . $category->textid . '/' . $inprofile->id);
			}
		}
	}

	$tpl->newBlock('lol-user');

	$profiles = $db->get_results("SELECT * FROM `lol_players` WHERE `user_id` = '$inprofile->id'");

	if (!empty($profiles)) {
		foreach ($profiles as $profile) {
			$tpl->newBlock('lol-user-existing');
			$tpl->assignAll($profile);
		}
	}
}

