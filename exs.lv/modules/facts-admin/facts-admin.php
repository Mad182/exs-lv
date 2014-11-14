<?php

/**
 *  Spēļu faktu pārvaldība
 *
 *  Adrese: /facts_admin
 */
$robotstag[] = 'noindex';

if (!im_mod()) {
	redirect();
}
$tpl->newBlock('facts_admin-tabs');


// faktiem ir divi veidi: rs un gaming; laicīgi jāfiksē, kurš veids tiek skatīts, 
// lai varētu izmantot pareizo datubāzes tabulu
$fact_type = 'facts';
$fact_link = '?type=gaming';

if (isset($_GET['type']) && $_GET['type'] == 'rs') {
	redirect('https://runescape.exs.lv/rsfacts');
	exit;
}

// pievienotā fakta dzēšana
if (isset($_GET['delete']) && isset($_GET['type'])) {

	$delete = (int) $_GET['delete'];
	$db->query("DELETE FROM `" . $fact_type . "` WHERE `id` = '$delete' LIMIT 1");

	redirect('/' . $category->textid . $fact_link);
}

// pievienotā fakta rediģēšana
if (isset($_GET['edit']) && isset($_GET['type'])) {

	$fact_id = (int) $_GET['edit'];
	$fact = $db->get_row("SELECT * FROM `$fact_type` WHERE `id` = $fact_id ");

	// tukšu lapu nav vērts rādīt, tāpēc pārvirzām uz faktu sarakstu
	if (!$fact) {
		redirect('/' . $category->textid);
	}

	// fakta informācijas atjaunošana datubāzē
	if (isset($_POST['edit-fact'])) {

		$fact_text = sanitize(trim($_POST['edit-fact']));

		if ($db->query("UPDATE `$fact_type` SET `text` = '$fact_text' WHERE `id` = $fact_id ")) {
			$tpl->newBlock("facts_admin-successupd");
			$fact->text = $_POST['edit-fact'];
		}
		redirect('/' . $category->textid . $fact_link);
	}

	// rediģēšanas forma
	$tpl->newBlock("facts_admin-edit");
	$tpl->assign(array(
		'id' => $fact->id,
		'text' => stripslashes($fact->text),
		'fact-type' => $fact_link
	));
}



// jauna fakta pievienošana
$tpl->newBlock("facts_admin-add");
$tpl->assign('fact-type', $fact_link);

// fakta ierakstīšana datubāzē
if (isset($_POST['new-fact']) && isset($_GET['type'])) {

	$newfact = sanitize(trim($_POST['new-fact']));

	if ($db->query("INSERT INTO `$fact_type` (text) VALUES ('$newfact')")) {
		$tpl->newBlock("facts_admin-success");
	}
}



// no datubāzes atlasa visus pievienotos konkrētā veida faktus un
// izvada tos saraksta veidā
$facts = $db->get_results("SELECT * FROM `$fact_type` ORDER BY `id` DESC");
if ($facts) {

	$facts_title = (isset($_GET['type']) && $_GET['type'] == 'rs') ?
			'RuneScape fakti' : 'Gaming fakti';

	$tpl->newBlock("facts_admin-list");
	$tpl->assign('facts-title', $facts_title);

	foreach ($facts as $fact) {

		$tpl->newBlock("facts_admin-list-node");
		$tpl->assign(array(
			'id' => $fact->id,
			'text' => stripslashes($fact->text),
			'fact-type' => $fact_link
		));
	}
}

