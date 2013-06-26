<?php


/**
 * Unshorten links
 *
 * @param  string $text
 * @return string
 */
function unshorten_links($text) {
	$pattern  = '#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#';
	$callback = create_function('$matches', '
		$url = array_shift($matches);
		$url_parts = parse_url($url);

		$text = parse_url($url, PHP_URL_HOST) . parse_url($url, PHP_URL_PATH);

		$ch = curl_init($text);
		curl_setopt_array($ch, array(
			CURLOPT_FOLLOWLOCATION => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_SSL_VERIFYHOST => FALSE, // suppress certain SSL errors
			CURLOPT_SSL_VERIFYPEER => FALSE,
		));
		curl_exec($ch);
		$text = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		curl_close($ch);

		return $text;
	');

	return preg_replace_callback($pattern, $callback, $text);
}


$username = 'ezgif_com';
$cache_file = 'cache/twitter-' . md5($username);
$cache_created = filemtime($cache_file);

if (false && (!$cache_created || (time() - $cache_created) > 420)) {

	$xml = simplexml_load_file('https://api.twitter.com/1/statuses/user_timeline/' . $username . '.xml?count=10');
	if ($xml) {

		$out = '<ul class="tweet_list">';
		$i = 0;
		foreach ($xml->status as $tweet) {
			if ($i++ >= 5) {
				break;
			}

			$out .= '<li><a class="tweet_avatar" href="http://twitter.com/ezgif_com"><img src="http://ezgif.com/bildes/ezgif/twitter-av.png" height="50" width="50" alt="ezgif_com avatar" title="ezgif_com avatar" /></a><span class="tweet_time"><a href="http://twitter.com/ezgif_com/status/' . $tweet->id . '">' . date('M j', strtotime($tweet->created_at)) . '</a></span><span class="tweet_join"> we said,</span>' . add_smile(stripslashes(htmlpost2db(unshorten_links(

str_replace("\n",'',nl2br('<p class="tweet_text">'.$tweet->text.'</p>'))

)))) . '</li>';
		}
		$out .= '</ul>';

		$cache_handle = fopen($cache_file, 'wb');
		fwrite($cache_handle, $out);
		fclose($cache_handle);
	} else {

		if ($cache_created) {

			$out = file_get_contents($cache_file);
		} else {

			$out = '<!-- twitter error -->';
		}
	}
} else {
	$out = file_get_contents($cache_file);
}

$tpl->assignGlobal('twitter-posts', $out);

$tpl->newBlock('meta-description');
$tpl->assign('description', 'Crop, resize, optimize and split animated gifs online with ease');

