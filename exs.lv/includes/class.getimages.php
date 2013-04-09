<?php

class getImages {

	function reddit() {
		global $db;
		$data = file_get_contents('http://www.reddit.com/r/comics+funny.json');
		$junk = json_decode($data);
		foreach ($junk->data->children as $data) {
			$file = false;
			if (in_array(substr($data->data->url, -3), array('jpg', 'png', 'gif'))) {
				$file = $data->data->url;
			} elseif (stristr($data->data->url, 'http://imgur.com/')) {
				$addr = str_replace('http://imgur.com/', 'http://i.imgur.com/', $data->data->url);
				if (file_get_contents($addr . '.jpg')) {
					$file = $addr . '.jpg';
				} elseif (file_get_contents($addr . '.png')) {
					$file = $addr . '.png';
				} elseif (file_get_contents($addr . '.gif')) {
					$file = $addr . '.gif';
				}
			}

			if ($this->can_add($file)) {
				$file = sanitize($file);
				$title = sanitize($data->data->title);
				$link = sanitize($data->data->permalink);
				$db->query("INSERT INTO `junk_queue` (`image`,`title`,`link`,`source`,`approved`,`created`) VALUES ('" . $file . "','" . $title . "','" . $link . "','reddit',0,NOW())");
			}
		}
	}

	function xkcd() {
		global $db;
		$data = file_get_contents('http://xkcd.com/info.0.json');
		$junk = json_decode($data);

		if ($this->can_add($junk->img)) {
			$file = sanitize($junk->img);
			$title = sanitize(htmlspecialchars($junk->title . ' (' . $junk->alt . ')'));
			$link = sanitize($junk->img);
			$db->query("INSERT INTO `junk_queue` (`image`,`title`,`link`,`source`,`created`) VALUES ('" . $file . "','" . $title . "','" . $link . "','xkcd',NOW())");
		}
	}

	function can_add($url = null) {
		global $db;
		if (empty($url)) {
			return false;
		}
		if ($db->get_var("SELECT count(*) FROM `junk_queue` WHERE `image` = '" . sanitize($url) . "'")) {
			return false;
		}
		return true;
	}

}
