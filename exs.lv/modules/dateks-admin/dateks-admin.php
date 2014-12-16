<?php

/**
 *  Dateks reklāmas piedāvājumi
 *
 *  Adrese: /offers_admin
 */
$robotstag[] = 'noindex';

if (!im_mod()) {
	redirect();
}

$fields = array('title', 'link', 'img', 'price', 'params');

// pievienotā fakta dzēšana
if (isset($_GET['delete'])) {
	$delete = (int) $_GET['delete'];
	$db->query("DELETE FROM `dateks_offers` WHERE `id` = '$delete' LIMIT 1");
	redirect('/' . $category->textid . $offer_link);
}

// rediģēšana
if (isset($_GET['edit'])) {

	$offer_id = (int) $_GET['edit'];
	$offer = $db->get_row("SELECT * FROM `dateks_offers` WHERE `id` = $offer_id ");

	// tukšu lapu nav vērts rādīt, tāpēc pārvirzām uz faktu sarakstu
	if (!$offer) {
		redirect('/' . $category->textid);
	}

	// rediģēšanas forma
	$tpl->newBlock("offer-edit");
	$tpl->assignAll($offer, true);

	// informācijas atjaunošana datubāzē
	if (isset($_POST['offer-title'])) {

		$update = '';
		foreach($fields as $field) {
			$update .= "`".$field."` = '".sanitize(trim($_POST['offer-'.$field]))."',";
		}

		if ($db->query("UPDATE `dateks_offers` SET  " . $update . " `modified` = NOW() WHERE `id` = $offer_id ")) {
			set_flash("Izmaiņas saglabātas!", "success");
		}
		redirect('/' . $category->textid);
	}
} else {

	//jauns piedāvājums
	$tpl->newBlock("offer-edit");
	if (isset($_POST['offer-title'])) {

		$insert = '';
		foreach($fields as $field) {
			$insert .= "'".sanitize(trim($_POST['offer-'.$field]))."',";
		}

		if ($db->query("INSERT INTO `dateks_offers` 
		(`".implode('`,`',$fields)."`,`created`,`modified`) 
		VALUES (".$insert."NOW(),NOW())")) {
			set_flash("Piedāvājums pievienots!", "success");
			redirect('/' . $category->textid);
		}
	}
	
	//saraksts
	$offers = $db->get_results("SELECT * FROM `dateks_offers` ORDER BY `id` DESC");
	if ($offers) {

		$tpl->newBlock("offers-list");

		foreach ($offers as $offer) {
			$tpl->newBlock("offers-list-node");
			$tpl->assignAll($offer, true);
		}
	}
	
}

