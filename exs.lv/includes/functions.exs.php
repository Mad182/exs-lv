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
				$html .= '<a href="/read/' . $bloglate->strid . '">' . $bloglate->title . '&nbsp;[' . $bloglate->posts . ']</a><br>';
			}
			$html .= '</p></div>';
		} else {
			$html = '';
		}
		if ($auth->ok === true) {
			if ($sidelinks = $db->get_results("SELECT title,url FROM sidelinks WHERE category = '" . $category_id . "' ORDER BY id DESC")) {
				$html .= '<h3>Manas saites</h3><div class="box"><p>';
				foreach ($sidelinks as $sidelink) {
					$html .= '<a href="' . $sidelink->url . '" rel="nofollow">' . $sidelink->title . '</a><br>';
				}
				$html .= '</p></div>';
			}
		}
		$m->set('blog_latest_' . $category_id . '_' . $auth->ok, $html, 100);
	}
	return $html;
}

/**
 * Movie genres
 */
function translate_genres($en) {
	$genres = [
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
	];

	if (!empty($genres[$en])) {
		return $genres[$en];
	}
	return $en;
}

/**
 * Atgriež dienas labākā komentāra masīvu
 */
function get_todays_top_comment($date = null) {
	global $db, $m;
	
	if(!empty($date)) {
		$time = strtotime($date);
	} else {
		$time = time();
	}

	if (($out = $m->get('todays_top_comment_' . date('Y-m-d', $time))) === false) {

		$out = [];

		$best = $db->get_row("SELECT
						`id`, `author`, `text`, `parent`, `vote_value`
					FROM
						`miniblog`
					WHERE
						`date` BETWEEN '" . date('Y-m-d 00:00:00', $time) . "' AND '" . date('Y-m-d 23:59:59', $time) . "' AND
						`removed` = 0 AND
						`groupid` = 0 AND
						`type` = 'miniblog' AND
						`lang` = 1
					ORDER BY
						`vote_value` DESC LIMIT 1");

		if (!empty($best)) {

			$user = get_user($best->author);

			if ($best->parent > 0) {
				// Ja ir parent, tad tā ir atbilde uz MB, ja nav, tad tas ir pats MB ieraksts.
				$parent = $db->get_row("SELECT `text`, `author` FROM `miniblog` WHERE `id` = $best->parent");
				$strid = mb_get_strid(mb_get_title($parent->text), $best->parent);
				$url = '/say/' . $parent->author . '/' . $best->parent . '-' . $strid . '#m' . $best->id;
			} else {
				$strid = mb_get_strid(mb_get_title($best->text), $best->author);
				$url = '/say/' . $best->author . '/' . $best->id . '-' . $strid;
			}

			$avatar = get_avatar($user, 's');

			$content = strip_tags($best->text);
			if (strlen($content) > 100) {
				$content = textlimit($content, 120, '') . '...';
			}

			$out = [
				'best-link' => $url,
				'best-avatar' => $avatar,
				'best-nick' => $user->nick,
				'best-rating' => $best->vote_value,
				'best-comment' => $content
			];
		}

		$m->set('todays_top_comment_' . date('Y-m-d', $time), $out, 20);
	}

	return $out;
}
