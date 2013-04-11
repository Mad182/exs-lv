<?php

if (!im_mod()) {
	redirect();
}

$ftype = (isset($_GET['type']) && $_GET['type'] == 'rs') ? 'facts_rs' : 'facts';
$flink = (isset($_GET['type']) && $_GET['type'] == 'rs') ? '&amp;type=rs' : '&amp;type=gaming';

$tpl->newBlock('facts_admin-tabs');

if (isset($_GET['delete']) && isset($_GET['type'])) {
	$delete = (int) $_GET['delete'];
	$db->query("DELETE FROM `" . $ftype . "` WHERE id = '$delete' LIMIT 1");
	$flink2 = (isset($_GET['type']) && $_GET['type'] == 'rs') ? 'rs' : 'gaming';
	redirect('/' . $category->textid . '?type=' . $flink2);
}

if (isset($_GET['edit']) && isset($_GET['type'])) {
	$edit = (int) $_GET['edit'];
	$fact = $db->get_row("SELECT * FROM `" . $ftype . "` WHERE id = '$edit'");
	if ($fact) {
		if (isset($_POST['edit-fact'])) {
			$editfact = sanitize(trim($_POST['edit-fact']));
			if ($db->query("UPDATE `" . $ftype . "` SET text = ('$editfact') WHERE id = $edit")) {
				$tpl->newBlock("facts_admin-successupd");
				$fact->text = $_POST['edit-fact'];
			}
		}
		$tpl->newBlock("facts_admin-edit");
		$tpl->assign(array(
			'id' => $fact->id,
			'text' => stripslashes($fact->text),
			'fact-type' => $flink
		));
	}
}

$tpl->newBlock("facts_admin-add");
$tpl->assign('fact-type', $flink);
if (isset($_POST['new-fact']) && isset($_GET['type'])) {
	$newfact = sanitize(trim($_POST['new-fact']));
	if ($db->query("INSERT INTO `" . $ftype . "` (text) VALUES ('$newfact')")) {
		$tpl->newBlock("facts_admin-success");
	}
}

$facts = $db->get_results("SELECT * FROM `" . $ftype . "` ORDER BY id DESC");
if ($facts) {
	$facts_title = (isset($_GET['type']) && $_GET['type'] == 'rs') ? 'RuneScape fakti' : 'Gaming fakti';
	$tpl->newBlock("facts_admin-list");
	$tpl->assign('facts-title', $facts_title);
	foreach ($facts as $fact) {
		$tpl->newBlock("facts_admin-list-node");
		$tpl->assign(array(
			'id' => $fact->id,
			'text' => stripslashes($fact->text),
			'fact-type' => $flink
		));
	}
}

