<?php

/**
 * Atvelk bildes no ārējiem avotiem,
 * lai pēc tam ar apstiprinājumu ievietotu /junk
 */
class getImages {

	public function reddit() {
		return false;
		global $db;
		$data = curl_get('https://www.reddit.com/r/comics+funny+gifs+funnygifs+gaming_gifs+GamePhysics+highqualitygifs+holdmyredbull+EngineeringPorn.json');
		$junk = json_decode($data);
		foreach ($junk->data->children as $data) {
			$file = false;

			if (in_array(substr($data->data->url, -3), ['jpg', 'png', 'gif', 'peg', 'ifv'])) {

				if (stristr($data->data->url, 'imgur.com')) {
					$data->data->url = str_replace('.gif', '.gifv', $data->data->url);
					$data->data->url = str_replace('.gifvv', '.gifv', $data->data->url);
				}

				$addr = str_replace('//imgur.com/', '//i.imgur.com/', $data->data->url);
				$file = str_replace('http://i.imgur.com/', 'https://i.imgur.com/', $addr);

			} elseif (stristr($data->data->url, '//imgur.com/')) {

				$addr = str_replace('//imgur.com/', '//i.imgur.com/', $data->data->url);
				$addr = str_replace('http://i.imgur.com/', 'https://i.imgur.com/', $addr);
				if (file_get_contents($addr . '.jpg')) {
					$file = $addr . '.jpg';
				} elseif (file_get_contents($addr . '.png')) {
					$file = $addr . '.png';
				} elseif (file_get_contents($addr . '.gifv')) {
					$file = $addr . '.gifv';
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

	public function xkcd() {
		return false;
		global $db;
		$data = curl_get('https://xkcd.com/info.0.json');
		$junk = json_decode($data);

		if ($this->can_add($junk->img)) {
			$file = sanitize($junk->img);
			$title = sanitize(h($junk->title . ' (' . $junk->alt . ')'));
			$link = sanitize($junk->img);
			$db->query("INSERT INTO `junk_queue` (`image`,`title`,`link`,`source`,`created`) VALUES ('" . $file . "','" . $title . "','" . $link . "','xkcd',NOW())");
		}
	}

	private function can_add($url = null) {
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

