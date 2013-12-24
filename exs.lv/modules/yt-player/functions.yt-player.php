<?php

function player_get_list() {
	global $db, $category;

	//top
	$list = $db->get_results("
			SELECT
				COUNT(player_likes.id) as likes,
				ytlocal.yt_title as title,
				ytlocal.yt_time as duration,
				ytlocal.yt_id as id
			FROM
				player_likes,
				ytlocal
			WHERE
				player_likes.video_id = ytlocal.yt_id AND
				player_likes.archived = 0 AND
				player_likes.playlist = ".$category->id."
			GROUP BY
				player_likes.video_id
			ORDER BY
				likes DESC
			LIMIT 5
		");

	$return = array();
	$ids = array();

	foreach($list as $item) {

		$ids[] = "'".$item->id."'";

		$out['likes'] = intval($item->likes);
		$out['title'] = htmlspecialchars($item->title);
		$out['duration'] = $item->duration;
		$out['id'] = $item->id;

		$out['likers'] = '';


		$likers = $db->get_results("SELECT user_id FROM player_likes WHERE video_id = '$item->id' AND archived = 0");
		if(!empty($likers)) {

			foreach($likers as $liker) {
				$user = get_user($liker->user_id);
				$avatar = get_avatar($user, 's');
				$out['likers'] .= '<a style="float:left;margin: 0 3px 0 0;width:26px;height:26px;" title="'.htmlspecialchars($user->nick).'" href="/user/'.$user->id.'" target="_blank"><img src="'.$avatar.'" alt="" /></a>';
			}

		}

		$return[] = $out;

	}

	//random > 0
	$list = $db->get_results("
			SELECT
				COUNT(player_likes.id) as likes,
				ytlocal.yt_title as title,
				ytlocal.yt_time as duration,
				ytlocal.yt_id as id
			FROM
				player_likes,
				ytlocal
			WHERE
				player_likes.video_id = ytlocal.yt_id AND
				player_likes.archived = 0 AND
				player_likes.video_id NOT IN(".implode(',',$ids).") AND
				player_likes.playlist = ".$category->id."
			GROUP BY
				player_likes.video_id
			ORDER BY
				RAND()
			LIMIT 20
		");

	if(!empty($list)) {

		foreach($list as $item) {

			$out['likes'] = intval($item->likes);
			$out['title'] = htmlspecialchars($item->title);
			$out['duration'] = $item->duration;
			$out['id'] = $item->id;

			$out['likers'] = '';


			$likers = $db->get_results("SELECT user_id FROM player_likes WHERE video_id = '$item->id' AND archived = 0");
			if(!empty($likers)) {

				foreach($likers as $liker) {
					$user = get_user($liker->user_id);
					$avatar = get_avatar($user, 's');
					$out['likers'] .= '<a style="float:left;margin: 0 3px 0 0;width:26px;height:26px;" title="'.htmlspecialchars($user->nick).'" href="/user/'.$user->id.'" target="_blank"><img src="'.$avatar.'" alt="" /></a>';
				}

			}

			$return[] = $out;

		}
	}
	return $return;

}


function player_get_mylist($user = 0) {
	global $db, $category;

	$list = $db->get_results("
			SELECT
				COUNT(player_likes.id) as likes,
				player_likes.archived as archived,
				ytlocal.yt_title as title,
				ytlocal.yt_time as duration,
				ytlocal.yt_id as id
			FROM
				player_likes,
				ytlocal
			WHERE
				player_likes.video_id = ytlocal.yt_id AND
				player_likes.user_id = ".intval($user)." AND
				player_likes.archived = 0 AND
				player_likes.playlist = ".$category->id."

			GROUP BY
				player_likes.video_id
			ORDER BY
				likes DESC
			LIMIT 60
		");

	$active = count($list);

	if($active < 60) {

		$ids = array();

		foreach($list as $item) {
			$ids[] = "'".$item->id."'";
		}

		$list2 = $db->get_results("
				SELECT
					COUNT(player_likes.id) as likes,
					player_likes.archived as archived,
					ytlocal.yt_title as title,
					ytlocal.yt_time as duration,
					ytlocal.yt_id as id
				FROM
					player_likes,
					ytlocal
				WHERE
					player_likes.video_id = ytlocal.yt_id AND
					player_likes.user_id = ".intval($user)." AND
					player_likes.archived = 1 AND
					player_likes.video_id NOT IN(".implode(',',$ids).")

				GROUP BY
					player_likes.video_id
				ORDER BY
					likes DESC
				LIMIT ".(60-$active));

		if(empty($list)) {
			$list = array();
		}
		if(empty($list2)) {
			$list2 = array();
		}

		$list = array_merge($list, $list2);

	}


	return $list;

}


function player_now_playing() {
	global $ss, $db, $category;

	$playing_song = $ss->get('player_songid_'.$category->id);
	$playing_started = $ss->get('player_started_'.$category->id);
	$video = get_youtube($playing_song);

	$duration = yt_time_to_seconds($video->yt_time);

	if(empty($playing_song) || $playing_started < time()-$duration) {

		$new_song = $db->get_row("SELECT COUNT(id) AS likes, video_id FROM player_likes WHERE archived = 0 AND playlist = $category->id GROUP BY video_id ORDER BY likes DESC limit 1");

		if(empty($new_song)) {
			$new_song = $db->get_row("SELECT video_id FROM player_likes WHERE playlist =$category->id ORDER BY rand() LIMIT 1");
		}

		$playing_song = $new_song->video_id;
		$playing_started = time();

		$db->query("UPDATE player_likes SET archived = 1 WHERE video_id = '$playing_song' AND playlist = $category->id");

		$new_song = get_youtube($playing_song);

		$duration = yt_time_to_seconds($new_song->yt_time);

		$ss->set('player_songid_'.$category->id, $playing_song);
		$ss->set('player_started_'.$category->id, $playing_started);

	}

	return array(
		'id' => $playing_song,
		'position' => time()-$playing_started,
		'duration' => $duration
		);

}


function yt_time_to_seconds($str_time) {
	sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
	return isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;
}