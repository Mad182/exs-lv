<?php

class getWallpapers {

	function reddit() {
		$data = curl_get('http://www.reddit.com/r/wallpaper+wallpapers/top/.json?sort=top&t=week');
		$wallpapers = json_decode($data);
		$files = [];

		if (!empty($wallpapers->data->children) && (is_array($wallpapers->data->children) || is_object($wallpapers->data->children))) {
			foreach ($wallpapers->data->children as $data) {
				$file = false;
				$thumb = isset($data->data->thumbnail) ? $data->data->thumbnail : '';
				if (isset($data->data->url) && in_array(substr($data->data->url, -3), ['jpg', 'png'])) {
					$file = $data->data->url;
				}
				if (isset($data->data->url) && stristr($data->data->url, 'imgur.com')) {
					$addr = str_replace('http://imgur.com/', 'http://i.imgur.com/', $data->data->url);

					if (@fsockopen($addr . '.jpg', 80, $errno, $errstr, 5)) {
						$file = $addr . '.jpg';
					} elseif (@fsockopen($addr . '.png', 80, $errno, $errstr, 5)) {
						$file = $addr . '.png';
					}

					$thumb = preg_replace('/(\.[jpg|png])/', 's\1', $file);
				}
				if ($thumb && strpos($thumb, 'http') === 0 && $file) {
					$files[] = [
						'thumb' => $thumb,
						'file' => $file
					];
				}
			}
		}
		return $files;
	}

	function imgur() {
		$oauth_base = 'https://api.imgur.com/oauth2/';
		$api_base = 'https://api.imgur.com/3/';

		// get fresh token
		$params = [
			'client_id' => 'fe55f5989f0576e',
			'refresh_token' => '308c25d24f84347065b4d0040d346d384bfbdce4',
			'client_secret' => 'a1712852c1dbde73f0d4a8ec3d070afe47436f68',
			'grant_type' => 'refresh_token'
		];

		$auth = '';
		foreach ($params as $k => $v) {
			$auth .= $k . '=' . urlencode($v) . '&';
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $oauth_base . 'token/?' . $auth);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

		$response = curl_exec($ch);
		if (PHP_VERSION_ID < 80500) {
			@curl_close($ch);
		}
		$response = json_decode($response);
		if (empty($response->access_token)) {
			return [];
		}
		$token = $response->access_token;

		// search for wallpapers
		$headers = [
			'Authorization: Bearer ' . $token
		];

		// todo: cleanup, copypasta
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $api_base . 'gallery/search/viral?q=wallpaper');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

		$response = curl_exec($ch);
		if (PHP_VERSION_ID < 80500) {
			@curl_close($ch);
		}
		$response = json_decode($response);
		$files = [];

		if (!empty($response->data) && (is_array($response->data) || is_object($response->data))) {
			foreach ($response->data as $wallpaper) {
				if (!empty($wallpaper->nsfw) || !empty($wallpaper->is_album)) {
					continue;
				}

				$files[] = [
					'thumb' => 'http://i.imgur.com/' . $wallpaper->id . 's.jpg',
					'file' => $wallpaper->link
				];
			}
		}

		return $files;
	}

}
