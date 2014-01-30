<?php

/**
 * EXS.LV specifiskās funkcijas
 * Šis fails netiek ielādēts apakšprojektos
 */

/**
 * Juanākie lietotāja bloga ieraksti
 */
function get_blog_latest($category_id, $force = false) {
	global $auth, $db, $m;
	if ($force || !($html = $m->get('blog_latest_' . $category_id . '_' . $auth->ok))) {
		if ($bloglatest = $db->get_results("SELECT strid,title,posts FROM pages WHERE category = '" . $category_id . "' ORDER BY bump DESC LIMIT 5")) {
			$html = '<h3>Jaunākais blogā</h3><div class="box"><p>';
			foreach ($bloglatest as $bloglate) {
				$html .= '<a href="/read/' . $bloglate->strid . '">' . $bloglate->title . '&nbsp;[' . $bloglate->posts . ']</a><br />';
			}
			$html .= '</p></div>';
		} else {
			$html = '';
		}
		if ($auth->ok === true) {
			if ($sidelinks = $db->get_results("SELECT title,url FROM sidelinks WHERE category = '" . $category_id . "' ORDER BY id DESC")) {
				$html .= '<h3>Manas saites</h3><div class="box"><p>';
				foreach ($sidelinks as $sidelink) {
					$html .= '<a href="' . $sidelink->url . '" rel="nofollow">' . $sidelink->title . '</a><br />';
				}
				$html .= '</p></div>';
			}
		}
		$m->set('blog_latest_' . $category_id . '_' . $auth->ok, $html, false, 100);
	}
	return $html;
}

/**
 * Movie genres
 */
function translate_genres($en) {
	$genres = array(
		'Action' => 'Asa sižeta',
		'Adventure' => 'Piedzīvojumi',
		'Animation' => 'Animācijas',
		'Biography' => 'Biogrāfija',
		'Comedy' => 'Komēdija',
		'Crime' => 'Noziegumu',
		'Drama' => 'Drāma',
		'Documentary' => 'Dokumentāla',
		'Family' => 'Ģimenes',
		'Fantasy' => 'Fantāzija',
		'History' => 'Vēsturiskas',
		'Horror' => 'Šausmu',
		'Music' => 'Muzikāla',
		'Mystery' => 'Mistērija',
		'Reality-TV' => 'Realitātes TV',
		'Romance' => 'Romantika',
		'Sci-Fi' => 'Zinātniskā fantastika',
		'Sport' => 'Sports',
		'Thriller' => 'Trilleris',
		'War' => 'Karš',
		'Western' => 'Vesterns'
	);

	if (!empty($genres[$en])) {
		return $genres[$en];
	}
	return $en;
}
