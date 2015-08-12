<?php

if ($file = simplexml_load_file('https://bitbucket.org/mad182/exs-lv/rss?token=11b75e3f7580de2e7a01fadb958012a0')) {
	$module_content = '<ul style="padding: 20px 50px;margin: 0">';
	foreach ($file->channel->item as $item) {
		$module_content .= '<li class="mbox">' . date('Y-m-d', strtotime($item->pubDate)) . ' <a href="' . $item->link . '">' . h($item->title) . '</a></li>';
	}
	$module_content .= '</ul>';
}

