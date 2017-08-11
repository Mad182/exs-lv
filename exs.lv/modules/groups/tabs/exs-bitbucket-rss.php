<?php

if ($file = simplexml_load_file('https://bitbucket.org/mad182/exs-lv/rss?token=f606e4e6353d84e32532d632668f3c9a')) {
	$module_content = '<ul style="padding:20px;margin:0;list-style:none">';
	foreach ($file->channel->item as $item) {
		$module_content .= '<li class="mbox">' . date('Y-m-d', strtotime($item->pubDate)) . ' <a href="' . $item->link . '">' . h($item->title) . '</a></li>';
	}
	$module_content .= '</ul>';
}

