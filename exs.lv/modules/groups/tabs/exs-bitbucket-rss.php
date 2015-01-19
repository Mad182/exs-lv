<?php

if ($auth->ok) {

	if ($file = simplexml_load_file('https://bitbucket.org/mad182/exs-lv/rss?token=a74189d9ab66cb0535a36fe89869a012')) {
		$module_content = '<ul style="padding: 20px 50px;margin: 0;">';
		foreach ($file->channel->item as $item) {
			$module_content .= '<li class="mbox">' . date('Y-m-d', strtotime($item->pubDate)) . ' <a href="' . $item->link . '">' . $item->title . '</a></li>';
		}
		$module_content .= '</ul>';
	}
} else {
	$module_content = 'Log in';
}

