<?php

function strip_selected_tags($text, $tags = array()) {

	$args = func_get_args();
	$text = array_shift($args);
	$tags = func_num_args() > 2 ? array_diff($args,array($text))  : (array)$tags;
	foreach ($tags as $tag){
		if(preg_match_all('/<'.$tag.'[^>]*>(.*)<\/'.$tag.'>/iU', $text, $found)){
			$text = str_replace($found[0],$found[1],$text);
		}
	}

	return $text;
}

