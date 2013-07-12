<?php

if($auth->id != 1) {
	redirect();
}

$out = '<table>';

if ($handle = opendir('/home/www/img.exs.lv/pikseli-atbildes')) {
	$ti = 0;
	while (false !== ($file = readdir($handle))) {
		if ($file != "." && $file != "..") {
			if($ti == 0) {
			$out .= '<tr>';
			}
			$out .= '<td><img src="http://img'.alternator(1,2,0).'.exs.lv/pikseli-atbildes/'.$file.'" alt="" style="width:240px;" /></td>';
			if($ti == 2) {
			$out .= '</tr>';
			}
			$ti++;
			if($ti > 2) {
				$ti = 0;
			}
		}
	}
	closedir($handle);
}

$out .= '</table>';

echo $out;
die();

