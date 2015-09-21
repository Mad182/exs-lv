<?php
/**
 *  RuneScape apakšprojektam specifiskas funkcijas.
 *  Tiek ielādētas apakšprojekta konfigurācijas failā.
 */

/**
 *  Vairākām RuneScape sadaļām tiek izmantota MVC struktūra,
 *  lai to saturu foršāk nodalītu un ielādētu caur klasēm un funkcijām.
 *  
 *  Lai sadaļas modulī realizētā klase tiktu izsaukta,
 *  AIZ tās jāpievieno šāda rinda:
 *      init_mvc();
 */
function init_mvc() {
	global $category;
	if (empty($category)) die('Ooooops! Sadaļu ielādēt neizdevās. :(');
	$class_name = as_class_name($category->module);
	if (empty($class_name)) die('Ooooops! Sadaļu ielādēt neizdevās. :(');
	$controller = new $class_name();
	$controller->index();
}

/**
 *  Apstrādās saņemto simbolu virkni, pārveidojot (ja nepieciešams)
 *  to uz tādu, kāda drīkst būt PHP klases nosaukumā.
 *
 *  Tiek izmantota sadaļās ar MVC tipa arhitektūru.
 */
function as_class_name($name = '') {

	$name = trim($name);
	if (empty($name)) return '';

	// klašu nosaukumos nevar būt "-", tāpēc aizstājam ar pieņemamu atdalītāju
	$name = str_replace(array('-', ' '), '_', $name);

	$allowed = "/[^a-z0-9_]/i";
	$name = preg_replace($allowed, '', $name);
	if (empty($name)) return '';

	// katra daļa sāksies ar lielo sākumburtu, piemēram, "class Model_Users"
	$name = str_replace('_', ' ', $name);
	$name = ucwords($name);
	$name = str_replace(' ', '_', $name);

	return $name;
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
