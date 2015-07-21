<?php

function can_edit_page($article) {
	global $lang, $auth, $min_page_edit, $category, $page_edit_time;

	if (!$auth->ok || $lang != $article->lang) {
		return false;
	}

	if (im_mod() || im_cat_mod()) {
		return true;
	}

	if (im_rs_mod() && $auth->id == $article->author) {
		return true;
	}

	if ($category->isblog == $auth->id) {
		return true;
	}

	if ($auth->id == $article->author) {
		if ($auth->level == 3) {
			return true;
		}
		if ($auth->karma >= $min_page_edit) {
			if ($page_edit_time == 0) {
				return true;
			}
			if ($page_edit_time >= time() - strtotime($article->date)) {
				return true;
			}
		}
	}

	return false;
}

function get_page_categories($current = null, $force = false) {
	global $db, $m, $lang, $debug;

	if ($debug || $force || !($cats = $m->get('cat_list_' . $lang))) {
		$cats = $db->get_results("SELECT `lang`,`parent`,`module`,`persona`,`isblog`,`isforum`,`id`,`title`,`status` FROM `cat` WHERE `module` IN('list','wall','rshelp','movies') AND `lang` = '$lang' ORDER BY `title` ASC");
		$m->set('cat_list_' . $lang, $cats, false, 900);
	}

	$return = array();
	foreach ($cats as $cat) {
		if ((im_mod() || im_cat_mod($cat->id) || $cat->id == $current || $current == 'all') && $cat->status == 'active') {

			if ($cat->isforum) {
				$return['Forums'][$cat->id] = $cat->title . ' forums';
			} elseif ($cat->isblog) {
				$return['Blogi'][$cat->id] = $cat->title;
			} elseif ($cat->persona == 'runescape.jpg') {
				$return['Runescape'][$cat->id] = $cat->title;
			} else {
				$return['Main'][$cat->id] = $cat->title;
			}
		}
	}
	return $return;
}
