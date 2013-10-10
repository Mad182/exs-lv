<?php

function mkslug_itemsdb($string) {
	$bads = array('+', '/', ' ', 'ā', 'č', 'ē', 'ģ', 'ī', 'ķ', 'ļ', 'ņ', 'ŗ', 'š', 'ū', 'ž', 'Ā', 'Č', 'Ē', 'Ģ', 'Ī', 'Ķ', 'Ļ', 'Ņ', 'Ŗ', 'Š', 'Ū', 'Ž', '$', '&', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'ЫЬ', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'шщ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
	$good = array('-', '-', '-', 'a', 'c', 'e', 'g', 'i', 'k', 'l', 'n', 'r', 's', 'u', 'z', 'A', 'C', 'E', 'G', 'I', 'K', 'L', 'N', 'R', 'S', 'U', 'Z', 's', 'and', 'A', 'B', 'V', 'G', 'D', 'E', 'J', 'Z', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'C', 'S', 'S', 'T', 'T', 'E', 'Ju', 'Ja', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'z', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'c', 's', 't', 't', 'y', 'z', 'e', 'ju', 'ja');
	$string = str_replace($bads, $good, trim($string));
	$allowed = "/[^a-z0-9\\-\\_\\\\]/i";
	$string = preg_replace($allowed, '', $string);
	//$string = str_replace(array('---','--'),'-',$string);
	return $string;
}

// aizstāj [[ ]] ar adresi uz attiecīgo priekšmetu/kvestu/minispēli
function itemsdb_replace($item, $field = 0, $found = 1, $uses = 1, $notes = 1) {
	global $db;

	$lv = ($field == 1) ? 'lv' : '';

	$friendly_fields = array();
	if ($found == 1) {
		$friendly_fields[] = $lv.'location';
	}
	if ($uses == 1) {
		$friendly_fields[] = $lv.'uses';
	}
	if ($notes == 1) {
		$friendly_fields[] = $lv.'notes';
	}

	foreach ($friendly_fields as $single_field) {

		$matches = array();

		$item->$single_field = str_replace(' [[Treasure Trails]]', ' Treasure Trails', $item->$single_field);
		$item->$single_field = str_replace(' Treasure Trails', ' <a href="/page/32746-DandampD_Treasure_Trails_1">Treasure Trails</a>', $item->$single_field);

		if (preg_match_all("/\[\[[a-zA-ZāēūīšģķļžčņĀĒŪĪŠĢĶĻŽČŅ0-9- \(\)\+\,\.\%\!\'\:\|\/]{1,}\]\]/i", $item->$single_field, $matches)) {
			foreach ($matches as $match => $match_val) {
				foreach ($match_val as $name) {

					$item_name 	= substr($name, 2, -2);   // priekšmeta nosaukums bez kantainajām iekavām
					$parts 		= explode('|', $item_name);
					$item_name 	= trim($parts[0]);
					$name_slug 	= mkslug_itemsdb($item_name);
					$display 	= (count($parts) == 2) ? $parts[1] : $item_name;

					// pārmeklē starp citiem priekšmetiem
					if ($entry = $db->get_row("SELECT `strid` FROM `idb` WHERE `strid` = '".$name_slug."' LIMIT 1")) {
						$prefix = '<a href="/db/' . $entry->strid . '">';
					}
					//pārmeklē starp kvestiem un minikvestiem, un minispēlēm
					else if ($entry = $db->get_row("SELECT `id`,`strid` FROM `pages` WHERE `category` IN('100','99','160')  AND `title` = '". sanitize($item_name) ."'") ) {
						$prefix = "<a href=\"/page/" . $entry->id . "-" . $entry->strid . "\">";
					}

					$sufix = '</a>';
					if ($entry) {
						$item->$single_field = str_replace($name, $prefix . $display . $sufix, $item->$single_field);
					} else {
						$item->$single_field = str_replace($name, $display, $item->$single_field);
					}
				}
			}
		}
	}
	return $item;
}

// atjauno tulkotāja statistikas datus
function update_weekly($usr) {
	global $db;

	if ($user = $db->get_row("SELECT `user` FROM `idb_users` WHERE `user` = '".(int)$usr."' ")) {

		$week_start 		= date("Y-m-d 00:00:00",strtotime('this week',time()));
		$week_end 			= date("Y-m-d 23:59:59",strtotime('this week',time())+86400*6);
		$last_week_start 	= date("Y-m-d 00:00:00",strtotime('last week',time()));
		$last_week_end 		= date("Y-m-d 23:59:59",strtotime('last week',time())+86400*6);


		$icount = $db->get_var("SELECT count(*) FROM `idb` WHERE `auser` = '".$user->user."' AND `oldrs` = '0' AND `asg` = '0'");
		$tcount = $db->get_var("SELECT count(*) FROM `idb` WHERE `auser` = '".$user->user."' AND `atime` < '".sanitize($week_end)."' AND `atime` > '".sanitize($week_start)."' AND `oldrs` = '0' AND `asg` = '0' ");
		$lcount = $db->get_var("SELECT count(*) FROM `idb` WHERE `auser` = '".$user->user."' AND `atime` < '".sanitize($last_week_end)."' AND `atime` > '".sanitize($last_week_start)."' AND `oldrs` = '0' AND `asg` = '0' ");

		$upd = $db->query("UPDATE `idb_users` SET `items` = '".(int)$icount."', `tcount` = '".(int)$tcount."', `lcount` = '".(int)$lcount."' WHERE `user` = '".$user->user."' ");
	}
}

// monstru sarakstam aizvāc - -
function strip_monsters($string) {
	if (preg_match_all("/ -[a-zA-Z0-9- \+\.\%\!\'\:\|\/]{1,}-/i", $string, $matches)) {
	//if (preg_match_all("/-[0-9-\+\.\%\!\'\:\|\/]{1,}-/i", $string, $matches)) {
		foreach ($matches as $match => $value) {
			foreach ($value as $val) {

				$nval 		= substr(trim($val), 1, -1); // nostripo - -
				$nval 		= (is_numeric($nval)) ? '' : ' ('.$nval.')'; // ciparus aizvāc, vārdus ieliek iekavās
				$string		= str_replace($val,$nval,$string);
				$values 	= explode(", ",$string);
				$res 		= array_unique($values); // atstāj unique vērtības (aizvācot ciparus, paliek duplicates)
				$string 	= implode(', ',$res);
			}
		}
	}
	return $string;
}

// priekšmeta nosaukumam aizstāj - -
function strip_item($string) {
	if (preg_match_all("/ -[a-zA-Z0-9- \+\.\%\!\'\:\|\/]{1,}-/i", $string, $matches)) {
		foreach ($matches as $match => $value) {
			foreach ($value as $val) {

				$nval 		= substr(trim($val), 1, -1); // nostripo - -
				$nval 		= (is_numeric($nval)) ? '' : ' ('.$nval.')'; // ciparus aizvāc, vārdus ieliek iekavās
				$string		= str_replace($val,$nval,$string);
				//$values 	= explode(", ",$string);
				//$res 		= array_unique($values); // atstāj unique vērtības (aizvācot ciparus, paliek duplicates)
				//$string 	= implode(', ',$res);
			}
		}
	}
	return $string;
}

// izvada lapu sarakstu
function display_pages($current_page = 1, $page_count = 1, $sl = '', $get = '', $aclass = 'bottom-page') {

	if ($current_page <= $page_count) {

		$toLeft = -2;
		$toRight = 2;
		$difference = $page_count - $current_page;
		if ($page_count < 5) {
			$diff = $current_page - 1;
			$toLeft = ($diff > 2) ? -3 : -$diff;
			$toRight = ($difference > 2) ? 3 : $difference;
		} else if ($page_count >= 5) {
			if ($current_page < 4) {
				$toLeft = 1 - $current_page;
				$toRight = 5 - $current_page;
			} else if ($current_page > $page_count - 2) {
				$toLeft = $difference - 4;
				$toRight = $difference;
			}
		}
		// »» ««
		$all_pages = '';
		/*if ($current_page > 3 && $page_count > 5) {
			$all_pages .= '<li class="start"><a class="'.$aclass.'" href="'.$sl.'/1'.$get.'">1</a></li>';
		}*/
		if ($current_page > 1) {
			$all_pages .= '<li class="arrows"><a class="'.$aclass.'" href="'.$sl.'/' . ($current_page - 1) .$get. '"><img src="/modules/idb/images/arr-left.png" /></a></li>';
		}
		for ($a = ($current_page + $toLeft); $a <= ($current_page + $toRight); $a++) {
			$all_pages .= ($a == $current_page) ? '<li class="page-active">' . $a . '</li>' : '<li><a class="'.$aclass.'" href="'.$sl.'/' . $a .$get. '">' . $a . '</a></li>';
		}
		if ($current_page < $page_count) {
			$all_pages .= '<li class="arrows"><a class="'.$aclass.'" href="'.$sl.'/' . ($current_page + 1) . $get.'"><img src="/modules/idb/images/arr-right.png" /></a></li>';
		}
		/*if ($current_page < $page_count - 2 && $page_count > 5) {
			$all_pages .= '<li class="end"><a class="'.$aclass.'" href="'.$sl.'/' . $page_count . $get.'">' . $page_count . '</a></li>';
		}*/
		return $all_pages;

	} else {
		return '';
	}
}

