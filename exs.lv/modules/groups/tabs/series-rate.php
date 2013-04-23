<?php

$series = $db->get_results("SELECT
				`movie_data`.`year`,
				`movie_data`.`runtime`,
				`movie_data`.`rating`,
				`movie_data`.`title_lv`,
				`pages`.`title`,
				`pages`.`id`,
				`pages`.`strid`,
				`movie_images`.`thb`
			FROM
				`movie_data`,
				`pages`,
				`movie_images`
			WHERE
				`movie_data`.`type` = 'series' AND
				`pages`.`id` = `movie_data`.`page_id` AND
				`movie_images`.`page_id` = `movie_data`.`page_id` AND
				`movie_images`.`main` = 1
			ORDER BY
				`movie_data`.`exs_likes` DESC,
				`movie_data`.`rating` DESC
			");

if (!empty($series)) {

	$module_content .= '<h2>Exs.lv seriālu tops</h2>';
	$module_content .= '<p style="font-size:90%;">Lūdzu nobalso par seriāliem, kurus esi skatījies. Ja Tev seriāls patika un iesaki to citiem, spied "Man patīk", bet ja skatījies un uzskati, ka nav vērts tērēt laiku, spied "Man nepatīk". Balsot var sasnidzot 100. karmas līmeni. Tavi vērtējumi būs redzami citiem.</p>';
	$module_content .= '<table class="main-table" id="series-ratings-ingroup">';
	$module_content .= '	<tr>';
	$module_content .= '		<th></th>';
	$module_content .= '		<th>Nosaukums</th>';
	$module_content .= '		<th><span class="rautors">Patīk</span></th>';
	$module_content .= '		<th><span class="admins">Nepatīk</span></th>';
	$module_content .= '	</tr>';

	foreach ($series as $s) {
		$module_content .= '
	<tr>
		<td>
			<a href="/read/' . $s->strid . '">
				<img style="width:145px;height:215px;" src="http://img.exs.lv' . $s->thb . '" alt="' . htmlspecialchars($s->title) . '" />
			</a>
		</td>
		<td style="vertical-align:top;padding: 20px 10px">
			<strong>' . $s->title . '</strong>
			<br />' . $s->title_lv . '
			<br />
			<br />Gads: ' . $s->year . '
			<br />IMDB: ' . $s->rating . '
		</td>';

		/* vertejuma pievienošana */
		$rate_like = '';
		$rate_dislike = '';
		if ($auth->ok && $auth->karma >= 100 && !$db->get_var("SELECT count(*) FROM `movie_ratings` WHERE `page_id` = '$s->id' AND `user_id` = '$auth->id'")) {

			$token = md5($auth->id . '-' . $s->id . '-' . $remote_salt);

			if (isset($_GET['movie']) && isset($_GET['check']) && $_GET['check'] == $token) {
				$rating = 1;
				if (isset($_GET['dislike'])) {
					$rating = -1;
				}
				$db->query("INSERT INTO `movie_ratings` (`page_id`, `user_id`, `rating`, `created`, `ip`) VALUES ('$s->id', '$auth->id', '$rating', NOW(), '$auth->ip')");
				$db->query("UPDATE `movie_data` SET `exs_likes` = (SELECT count(*) FROM `movie_ratings` WHERE `page_id` = $s->id AND `rating` = 1) WHERE `page_id` = $s->id");

				//ajax
				if (isset($_GET['_'])) {
					die('Balsojums pieņemts!');

					//nav ajax, redirektejam
				} else {
					set_flash('Balsojums pieņemts!', 'success');
					redirect('/group/' . $group->id . '/tab/' . $tab->slug);
				}
			}

			$rate_like = '<p style="padding: 20px 0 0" class="movie-liker"><a href="?movie=' . $s->id . '&amp;check=' . $token . '&amp;like" class="rautors">Man patīk</a></p>';
			$rate_dislike = '<p style="padding: 20px 0 0" class="movie-liker"><a href="?movie=' . $s->id . '&amp;check=' . $token . '&amp;dislike" class="admins">Man nepatīk</a></p>';
		}

		$ratings = $db->get_results("SELECT
						`movie_ratings`.`user_id`,
						`movie_ratings`.`rating`,
						`users`.`nick`,
						`users`.`level`
					FROM
						`movie_ratings`,
						`users`
					WHERE
						`movie_ratings`.`page_id` = '$s->id' AND
						`users`.`id` = `movie_ratings`.`user_id`
					ORDER BY
					`movie_ratings`.`id` ASC");

		$likes = array();
		$dislikes = array();
		if (!empty($ratings)) {
			foreach ($ratings as $rating) {
				if ($rating->rating == 1) {
					$likes[] = usercolor($rating->nick, $rating->level, false, $rating->user_id);
				} elseif ($rating->rating == -1) {
					$dislikes[] = usercolor($rating->nick, $rating->level, false, $rating->user_id);
				}
			}
		}


		$module_content .= '		<td>' . implode(', ', $likes) . $rate_like . '</td>';
		$module_content .= '		<td>' . implode(', ', $dislikes) . $rate_dislike . '</td>';


		$module_content .= '	</tr>';
	}


	$module_content .= '</table>';
}
