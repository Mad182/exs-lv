<?php

if ($auth->ok) {

	if ($file = simplexml_load_file('http://showrss.karmorra.info/feeds/24.rss')) {
		$module_content = '<ul style="padding: 10px 0;margin: 0;">';
		foreach ($file->channel->item as $item) {
			$module_content .= '<li>' . date('Y-m-d', strtotime($item->pubDate)) . ' <a href="' . $item->link . '">' . $item->title . '</a></li>';
		}
		$module_content .= '</ul>';
	}
} else {
	$module_content = 'Log in';
}
