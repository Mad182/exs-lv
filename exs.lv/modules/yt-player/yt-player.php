<?php

if(!$auth->ok) {
	set_flash("Pagaidām tikai reģistrētajiem lietotājiem!");
	redirect();
}

$pagepath = '';
$add_css = ',player.css';


//get shared playlist in json format
if(isset($_GET['var1']) && $_GET['var1'] === 'list') {

	$list = array(
		'state' => 'success',
		'songs' => player_get_list()
	);

	die(json_encode($list));
}


//get active users playlist in json format
if(isset($_GET['var1']) && $_GET['var1'] === 'mylist') {

	$list = array(
		'state' => 'success',
		'songs' => player_get_mylist($auth->id)
	);

	die(json_encode($list));
}


//get next song
if(isset($_GET['var1']) && $_GET['var1'] === 'getnext') {

	$now_playing = player_now_playing();

	$song = array(
		'state' => 'success',
		'position' => $now_playing['position'],
		'duration' => $now_playing['duration'],
		'html' => '<iframe width="560" height="315" src="//www.youtube.com/embed/'.$now_playing['id'].'?start='.$now_playing['position'].'&amp;autoplay=1" frameborder="0" allowfullscreen></iframe><a style="float:right;" class="player-resubmit" href="/player/add/'.$now_playing['id'].'?_=1">+1 pievienot sarakstam</a><div class="clear"></div>'
	);

	die(json_encode($song));
}


//add song
if(isset($_GET['var1']) && $_GET['var1'] === 'add') {

	$video = get_youtube($_GET['var2']);

	if(!empty($video->yt_time) && $video->yt_time != '0:00' && yt_time_to_seconds($video->yt_time) < 480) {

		$check_like = $db->get_var("SELECT count(*) FROM player_likes WHERE video_id = '".sanitize($video->yt_id)."'
			AND user_id = '$auth->id' AND archived = 0");

		if(empty($check_like)) {
			$db->query("INSERT INTO player_likes (video_id, user_id, archived, created) 
						VALUES ('".sanitize($video->yt_id)."', '$auth->id', 0, NOW())");
			$like = 'ok';
		} else {
			$like = 'error';
		}

		$return = array(
			'title' => $video->yt_title,
			'id' => $video->yt_id,
			'duration' => $video->yt_time,
			'state' => 'success',
			'like' => $like 
		);

	} else {

		$return = array('error' => '1', 'state' => 'failure');
	}

	die(json_encode($return));
}


$now_playing = player_now_playing();
$tpl->assignGlobal(array(
	'player-now-id' => $now_playing['id'],
	'player-now-position' => $now_playing['position'],
	'player-now-duration' => $now_playing['duration'],
));