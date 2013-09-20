<?php

class getWallpapers {
	function reddit() {
		$data = file_get_contents('http://www.reddit.com/r/wallpaper+wallpapers/top/.json?sort=top&t=week');
		$wallpapers = json_decode($data);
		$files = array();

		foreach ($wallpapers->data->children as $data) {
			$file = false;
			$thumb = $data->data->thumbnail;
			if (in_array(substr($data->data->url, -3), array('jpg', 'png'))) {
				$file = $data->data->url;
			} elseif (stristr($data->data->url, 'http://imgur.com/')) {
				$addr = str_replace('http://imgur.com/', 'http://i.imgur.com/', $data->data->url);

				if (@fsockopen($addr . '.jpg', 80, $errno, $errstr, 5)) {
					$file = $addr . '.jpg';
				} elseif (@fsockopen($addr . '.png', 80, $errno, $errstr, 5)) {
					$file = $addr . '.png';
				}

			}
			if (strpos($thumb, 'http') === 0 && $file) {
				$files[] = array(
					'thumb'=>$thumb,
					'file'=>$file
				);
			}
		}
		return $files;
	}
}


