<?php
/**
 *  RuneScape apakšprojektā izmantotās funkcijas.
 *
 *  Autors: Edgars P. 
 */

/**
 *  Testēšanas nolūkiem, lai pārbaudītu Models (un ne tikai) rezultātus kā json
 *  @see https://github.com/callumlocke/json-formatter
 */
function as_json($content) {
	header('Content-Type: application/json');
	
	if (is_string($content) || is_integer($content)) {
		$content = array($content);
	}
	
	echo json_encode($content);
	exit;
}

/**
 *  Atgriež objektu ar template faila saturu
 *
 *  @param string $file     faila nosaukums
 *  @param bool $add_path   vai pievienot pilno ceļu uz atvērto moduli
 *  @return bool            "false", ja fails neeksistē
 *  @return TemplatePower   template objekts
 */
function get_tpl($file = '', $add_path = true) {
	global $category;
	
	if ($file == '') {
		return false;
	}

	if ($add_path) {
		$file = CORE_PATH.'/modules/'.$category->module.'/'.$file.'.tpl';
	}
	
	if (!file_exists($file)) {
		return false;
	}

	$tpl = new TemplatePower($file);
	$tpl->prepare();
	
	return $tpl;
}

/**
 *  RuneScape kategoriju saraksts.
 *
 *  Funkcija tiek izsaukta sadaļās, kurās iespējams mainīt raksta kategoriju
 *  (read, write, blogadmin).
 */
function get_rs_page_categories($current = null, $force = false) {
	global $db, $m, $lang, $debug;

	if ($debug || $force || !($cats = $m->get('cat_list_' . $lang))) {
		$cats = $db->get_results("SELECT `lang`,`parent`,`module`,`persona`,`isblog`,`isforum`,`id`,`title`,`status` FROM `cat` WHERE `module` IN('list','index','rshelp') AND `lang` = '$lang' ORDER BY `title` ASC");
		$m->set('cat_list_' . $lang, $cats, false, 900);
	}

	$return = array();
	foreach ($cats as $cat) {

		if ( in_array($cat->id, array(102, 4)) ) { // kvestu parent, prasmju parent
			continue;
		}
 
		// pāris sadaļu kategorijas redzamas vienmēr
		if ($cat->parent == 4) {
			$return['Prasmes'][$cat->id] = $cat->title;
		} elseif ($cat->parent == 1903) {
			$return['Arhīvs'][$cat->id] = $cat->title;
		} elseif ($cat->parent == 102) {
			$return['Kvesti'][$cat->id] = $cat->title;
		} elseif ($cat->module == 'rshelp') {
			$return['Runescape'][$cat->id] = $cat->title;
		} elseif ($cat->id == 599) { // rs ziņas
			$return['Main'][$cat->id] = $cat->title;
		}         
		// blogi, atkritne un vēl atsevišķas sadaļas redzamas tikai moderatoriem
		else if ( im_mod() || im_cat_mod($cat->id) ) {
			if (!$cat->isblog && $cat->isforum) {
				$return['Main'][$cat->id] = $cat->title . ' forums';
			} elseif ($cat->isblog) {
				$return['Blogi'][$cat->id] = $cat->title;
			} else {
				$return['Main'][$cat->id] = $cat->title;
			}
		}
	}
	return $return;
}
