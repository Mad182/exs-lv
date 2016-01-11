<h2>Konkursam iesniegtie raksti</h2>

<ul class="blockhref mb-col" style="list-style:none;padding:20px">
<?php

$c_pages = $db->get_results("SELECT `id`, `strid`, `title`, `text`, `sm_avatar`, `category` FROM `pages` WHERE `custom_param` = 'vote-2015' ORDER BY `title` ASC");
foreach($c_pages as $c_pg) {
	$c_cat = get_cat($c_pg->category);
	echo '<li style="padding: 4px 20px;margin:4px 20px">';
	echo '<a href="/read/' . $c_pg->strid . '"><img class="av" style="width:75px;height:75px;" src="//img.exs.lv/'.$c_pg->sm_avatar.'"><h3 style="font-size:110%;margin-top:0;padding-top:2px">' . $c_pg->title . ' <span style="color:#ccc">('.str_replace('Spēļu portāls', 'Jaunumi', $c_cat->title).')</span></h3><p>' . textlimit(trim(strip_tags($c_pg->text)), 220) . '</p></a><div class="c"></div>';
	echo '</li>';
}
?>
</ul>

<?php
	if(date('Y-m-d H:i') < '2016-01-11 12:00') {
?>

	<h2 id="cvote">Balsošana</h2>

	<?php

	if(!$auth->ok) {
		echo '<div class="form"><p class="notice">Lai nobalsotu, nepieciešams ielogoties!</p></div>';
	} elseif($auth->posts < 100 || $auth->date > '2015-11-06 17:51:50') {
		echo '<div class="form"><p class="notice">Balsošanā var piedalīties tikai lietotāji, kuri reģistrējušies pirms konkursa sākuma un pievienojuši 100 ierakstus!</p></div>';
	} else {

		echo '<div style="border-radius: 3px;padding: 20px 80px;margin: 20px auto;width:70%;background:url(\'https://img.exs.lv/m/a/mad/bg.jpg\');background-size:cover" class="mbox">';
		echo '<form style="width:230px;margin: auto;" action="/read/'.$article->strid.'#cvote" method="post" class="form">';
		echo '<input type="hidden" name="ctoken" id="ctoken" value="'.make_token('cvote').'" />';

		if(isset($_POST['cvote-submit']) && check_token('cvote', $_POST['ctoken'])) {
			if(count($_POST['cvote']) > 3) {
				echo '<p class="error">Pārāk daudz izvēlēto variantu!<p>';
			} else {
				for($i = 1; $i <= 3; $i++) {
					if(!empty($_POST['cvote'][$i])) {

						$for = intval($_POST['cvote'][$i]);

						//pārbauda, vai raksts eksistē nu par to var balsot
						if($for && $db->get_var("SELECT count(*) FROM `pages` WHERE `id` = $for AND `custom_param` = 'vote-2015'")) {

							//pārbauda, vai lietotājs jau nav nobalsojis par šo vietu vai rakstu
							if(!$db->get_var("SELECT count(*) FROM `topic_votes` WHERE `user_id` = $auth->id AND (`points` = ".(4-$i)." OR `page_id` = $for)")) {

								$db->query("INSERT INTO `topic_votes` (`user_id`, `ip`, `user_agent`, `page_id`, `points`, `created`) VALUES ($auth->id, '$auth->ip','" . sanitize($_SERVER['HTTP_USER_AGENT']) . "', $for, ".(4-$i).", NOW())");
								$auth->log("Nobalsoja par rakstu $for, $i. vieta");

							}

						}
					}
				}

			}
		}

		$votes = array();
		$votes[1] = $db->get_row("SELECT * FROM `topic_votes` WHERE `user_id` = $auth->id AND `points` = 3");
		$votes[2] = $db->get_row("SELECT * FROM `topic_votes` WHERE `user_id` = $auth->id AND `points` = 2");
		$votes[3] = $db->get_row("SELECT * FROM `topic_votes` WHERE `user_id` = $auth->id AND `points` = 1");

		for($i = 1; $i <= 3; $i++) {
			echo '<h3 style="color:#fafafa">' . $i . '. vieta (' . (4-$i) . ' ' . lv_dsk((4-$i), 'punkts', 'punkti') . ')</h3>';
			if(empty($votes[$i])) {

				echo '<select name="cvote['.$i.']">';
				echo '<option value="0"></option>';
				foreach($c_pages as $c_pg) {
					if($votes[1]->page_id != $c_pg->id && $votes[2]->page_id != $c_pg->id && $votes[1]->page_id != $c_pg->id) {
						echo '<option value="'.$c_pg->id.'">'.$c_pg->title.'</option>';
					}
				}

				echo '<select>';
			} else {

				echo '<select name="cvote['.$i.']" disabled="disabled">';
				foreach($c_pages as $c_pg) {
					if($votes[$i]->page_id === $c_pg->id) {
						echo '<option value="0">'.$c_pg->title.'</option>';
					}
				}

				echo '<select>';
			}
		}

		if(empty($votes[1]) OR empty($votes[2]) OR  empty($votes[3])) {

			echo '<p><input style="padding:8px 20px;font-size:15px !important;" type="submit" name="cvote-submit" value="Balsot!" class="button submit primary large"></p>';
		} else {
			echo '<p class="success">Paldies par balsojumu!</p>';	
		}
		echo '</form>';
		echo '</div>';
	}

} else {
	echo '<p><strong>Balsošana noslēgusies!</strong></p>';
}

