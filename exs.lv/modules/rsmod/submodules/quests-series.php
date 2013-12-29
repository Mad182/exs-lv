<?php
/**
 *	RuneScape questu sēriju pārvaldība
 *
 *	Moduļa adrese: runescape.exs.lv/series
 */
 
if ( !isset($sub_include) ) {
    redirect();
}

$tpl->assign('page-content-title', 'Sēriju kvestu secība');
$tpl->newBlock('rsmod-menu');

// sēriju numerācija un nosaukumi tiek atjaunoti
if ( isset($_GET['var1']) && $_GET['var1'] == 'update' ) {

    $get_cats = $db->get_results("SELECT `id` FROM `rs_classes` WHERE `cat` = 'series' ");
    
    if ($get_cats) {
        foreach ($get_cats as $cat => $data) {
            if (isset($_POST['order_' . $data->id]) && isset($_POST['title_' . $data->id])) {
                $order = (int) $_POST['order_' . $data->id];
                $title = sanitize($_POST['title_' . $data->id]);
                $update = $db->query("UPDATE `rs_classes` SET `order` = '$order', `title` = '$title' WHERE `id` = '$data->id' LIMIT 1");
            }
        }
    }
    //redirect("/".$_GET['viewcat']."/st-order");
}

// izvada visas kvestu sērijas ar to numerāciju
else {

    $all_cats = $db->get_results("SELECT `id`,`title`,`order` FROM `rs_classes` WHERE `cat` = 'series' ORDER BY `order` ASC ");
    if ($all_cats) {
        $skaits = 0;
        $tpl->newBlock('rsmod-series');
        $tpl->newBlock('rsmod-series-col');
        foreach ($all_cats as $cat => $data) {
            $tpl->newBlock('series-single');
            $tpl->assignAll($data);

            $tpl->newBlock('single-ordering');
            $tpl->assign('id', $data->id);

            for ($a = 0; $a < sizeof($all_cats); $a++) {
                $selected = (($a + 1) == $data->order) ? ' selected="selected"' : '';
                $tpl->newBlock('single-order');
                $tpl->assign(array(
                    'order' => ($a + 1),
                    'selected' => $selected
                ));
            }
            $skaits++;
            if ($skaits == 10) {
                $tpl->newBlock('rsmod-series-col');
            }
        }
    }
}

/*
    if ($_GET['var1'] == 'order') {
    exit;

	// numerācija tiek atjaunota
	if (isset($_GET['var2'])) {
		$id = (int) $_GET['var2'];
		if ($story = $db->get_row("SELECT `id` FROM `rs_classes` WHERE `id` = '" . $id . "' LIMIT 1")) {
			$quests = $db->get_results("SELECT `id` FROM `rs_help` WHERE `storyline` = '" . $story->id . "' ");
			if ($quests) {
				foreach ($quests as $quest) {
					if (isset($_POST[$quest->id . '_order'])) {
						$db->query("UPDATE `rs_help` SET `order` = '" . (int) $_POST[$quest->id . '_order'] . "' WHERE `id` = '" . $quest->id . "' ");
					}
				}
			}
		}
		header("Location: /" . $_GET['viewcat'] . "/order");
	}
	// izvada sarakstu ar visām sērijām un tajos esošo questu numerāciju
	else {
		$all_series = $db->get_results("SELECT * FROM `rs_classes` WHERE `cat` = 'series' ORDER BY `order` ASC");
		if ($all_series) {
			$sk = 0;
			foreach ($all_series as $single) {
				$get_quests = $db->get_results("SELECT * FROM `rs_help` WHERE `storyline` = '" . $single->id . "' ORDER BY `order` ASC");
				if ($get_quests && count($get_quests) > 1) {
					$tpl->newBlock('rsmod-quests-order');
					$tpl->assign(array(
						'title' => $single->title,
						'story' => $single->id
					));
					if ($sk % 4 == 0) {
						$tpl->assign('clearleft', 'clear:left');
					}
					foreach ($get_quests as $quest) {
						$title = $db->get_row("SELECT `title`,`strid` FROM `pages` WHERE `id` = '" . $quest->page_id . "' AND `category` IN ('99','100') LIMIT 1");
						if ($title) {
							$tpl->newBlock('order-quest');
							$tpl->assign(array(
								'quest-title' => $title->title,
								'strid' => $title->strid,
								'qid' => $quest->id
							));
							for ($a = 0; $a < count($get_quests); $a++) {
								$selected = ($a + 1 == $quest->order) ? ' selected="selected"' : '';
								$tpl->newBlock('order-nr');
								$tpl->assign(array(
									'nr' => $a + 1,
									'selected' => $selected
								));
							}
						}
					}
					$sk++;
				}
			}
		}
	}
}
*/
