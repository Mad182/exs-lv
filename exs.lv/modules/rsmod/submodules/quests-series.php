<?php

/**
 * 	RuneScape questu sēriju pārvaldība.
 *
 *  Jaunu sēriju pievienošana sistēmai, esošo izmaiņas;
 *  sēriju secība kvestu sadaļā, kvestu secība sērijā.
 *
 * 	Moduļa adrese: runescape.exs.lv/series
 */
if (!isset($sub_include)) {
	die('No hacking, pls.');
}
$tpl->assign('content-title', 'Kvestu sēriju pārvaldība');


// iesniegti sēriju secības atjaunošanas dati
if (isset($_POST['submit'])) {

	$series = $db->get_results("SELECT `id` FROM `rs_classes` WHERE `category` = 'series' ");

	if ($series) {
		foreach ($series as $single) {
			if (isset($_POST['order_' . $single->id]) && isset($_POST['title_' . $single->id])) {

				$order = (int) $_POST['order_' . $single->id];
				$title = strip_tags(trim($_POST['title_' . $single->id]));
				$title = sanitize(substr($title, 0, 50));

				$db->query("UPDATE `rs_classes` SET `ordered` = '$order', `title` = '$title' WHERE `id` = '$single->id' LIMIT 1");
			}
		}
	}
	set_flash('Sēriju secība un nosaukumi veiksmīgi atjaunoti!');
	redirect('/' . $_GET['viewcat']);
}

// formu drukāšana lapā
else {

	/**
	 *  izdrukās visas pievienotās storylines
	 *  ar iespēju mainīt to secību kvestu pamācību sadaļā
	 */
	$series = $db->get_results("
        SELECT `id`, `title`, `ordered` FROM `rs_classes` 
        WHERE `category` = 'series' ORDER BY `ordered` ASC 
    ");
	if ($series) {

		$counter = 0;
		$series_count = count($series);

		// skaits, aiz kura sarakstu pārdalīt uz pusēm
		$col_split = floor($series_count / 2);

		$tpl->newBlock('series-form');

		foreach ($series as $single) {

			// izveido jaunu saraksta kolonnu
			if ($counter == 0 || $counter == $col_split) {
				$tpl->newBlock('series-column');
			}

			$tpl->newBlock('single-series');
			$tpl->assignAll($single);

			// katrai sērijai ir izvēlne ar kārtas numuriem
			for ($i = 1; $i <= $series_count; $i++) {
				$selected = ($i == $single->ordered) ? ' selected="selected"' : '';
				$tpl->newBlock('selection-option');
				$tpl->assign(array(
					'order' => $i,
					'selected' => $selected
				));
			}
			$counter++;
		}
	}

	$tpl->gotoBlock('_ROOT');
	/**
	 *  izdrukās visas pievienotās storylines
	 *  un katrai sērijai piesaistītos rakstus un to secību
	 */
	/* $series = $db->get_results("
	  SELECT
	  `rs_classes`.`id` AS `story_id`,
	  `rs_classes`.`title`,
	  `rs_classes`.`ordered`,
	  IFNULL(`rs_help`.`title`, 0) AS `help_title`,
	  IFNULL(`pages`.`title`, 0) AS `pages_title`,
	  IFNULL(`pages`.`strid`, 0) AS `pages_strid`
	  FROM `rs_classes`
	  LEFT JOIN `rs_help` ON `rs_classes`.`id` = `rs_help`.`storyline`
	  LEFT JOIN `pages` ON `rs_help`.`page_id` = `pages`.`id`
	  WHERE
	  `rs_classes`.`category` = 'series'
	  ORDER BY
	  `rs_classes`.`ordered` ASC
	  ");
	  if ($series) {
	  echo '<br><br><br><br>1';
	  $counter    = 0;
	  $story_id   = 0;

	  foreach ($series as $single) {
	  echo 2;
	  //
	  if ($single->pages_title != '0') {

	  // izveido jaunu storyline
	  if ($single->story_id != $story_id) {
	  $tpl->newBlock('rsmod-quests-order');
	  $tpl->assign(array(
	  'title' => $single->page_title,
	  'story' => $single->id
	  ));
	  if ($counter % 4 == 0) {
	  $tpl->assign('clearleft', 'clear:left');
	  }
	  $story_id = $single->story_id;
	  }

	  $tpl->newBlock('order-quest');
	  $tpl->assign(array(
	  'quest-title' => $single->pages_title,
	  'strid' => $single->pages_strid,
	  'qid' => 0
	  ));
	  $counter++;
	  }
	  }
	  } */
}
