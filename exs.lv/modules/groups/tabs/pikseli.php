<?php

$module_content = '<table class="main-table">';

$files = array();
if ($handle = opendir('/home/www/img.exs.lv/pikseli-atbildes')) {
	while (false !== ($file = readdir($handle))) {
		if ($file != "." && $file != "..") {
			$files[] = $file;
		}
	}
	closedir($handle);
}

sort($files, SORT_NUMERIC);

$ti = 0;
foreach ($files as $file) {
	if ($ti == 0) {
		$module_content .= '<tr>';
	}

	$title = str_replace('.jpg', '', $file);
	list($id, $title) = explode('-', $title);

	$module_content .= '<td>';
	$module_content .= '<img src="http://img' . alternator(1, 2, 0) . '.exs.lv/pikseli-atbildes/' . $file . '" alt="' . $title . '" title="' . $title . '" style="width:240px;" />';
	$module_content .= '<p style="text-align: center;"><strong>' . ucfirst($title) . '</strong></p>';
	$module_content .= '</td>';
	if ($ti == 2) {
		$module_content .= '</tr>';
	}
	$ti++;
	if ($ti > 2) {
		$ti = 0;
	}
}

$module_content .= '</table>';
