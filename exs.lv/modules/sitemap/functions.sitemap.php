<?php

/**
 * Rekursīvi parāda lapas karti
 */
function create_sitemap($parent = 0, $sitemap_modules = 'list') {
	global $db, $lang, $auth;

	$cats = $db->get_results("SELECT `id`,`textid`,`title`,`content`,`isforum` FROM `cat` WHERE `parent` = '$parent' AND (`lang` = '$lang' OR `lang` = 0) AND `mods_only` = 0 AND `sitemap` = 1 AND `module` IN(" . $sitemap_modules . ") ORDER BY `ordered` ASC");

	$out = '';
	if ($cats) {
		$out .= '<ul>';
		foreach ($cats as $cat) {
			$out .= '<li>';

			$description = $cat->title;
			if (!empty($cat->isforum)) {
				$description = strip_tags($cat->content);
			}

			if ($auth->level == 1) {
				$out .= ' <a style="float: right;padding: 0 2px;margin: 0;border: 0" href="?moveup=' . $cat->id . '">&#8593;</a> ';
				$out .= ' <a style="float: right;padding: 0 2px;margin: 0;border: 0" href="?movedown=' . $cat->id . '">&#8595;</a> ';
			}

			$out .= '<a href="/' . $cat->textid . '" title="' . htmlspecialchars($description) . '">' . $cat->title . '</a>';

			$out .= create_sitemap($cat->id, $sitemap_modules);
			$out .= '</li>';
		}

		$out .= '</ul>';
	}
	return $out;
}

