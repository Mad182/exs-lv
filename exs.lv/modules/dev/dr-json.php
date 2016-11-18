<?php

$str = 'json';

$str = json_decode($str);

foreach ($str as $usr) {
	if (stristr($usr->url, '/user/')) {
		$id = str_replace(['/user/', '/'], '', $usr->url);
	} else {
		//pr($usr);
		$id = get_between($usr->image, '/i_', '.jpg');
	}
	if ($id > 1000) {
		if (!$db->get_var("SELECT count(*) FROM draugiem_followers WHERE id = '$id'")) {
			$db->query("INSERT INTO draugiem_followers (id) VALUES ('$id')");
		}
	}
}

exit;
$twfollowers = file_get_contents('https://api.twitter.com/1/followers/ids.json?cursor=-1&screen_name=exs_lv');

$twfollowers = json_decode($twfollowers);


foreach ($twfollowers->ids as $id) {
	if (!$db->get_var("SELECT count(*) FROM twitter_followers WHERE id = '$id'")) {
		$db->query("INSERT INTO twitter_followers (id) VALUES ('$id')");
	}
}


exit;
