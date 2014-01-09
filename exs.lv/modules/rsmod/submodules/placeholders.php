<?php

/**
 * 	RuneScape pamācību placeholderu pārvaldība.
 *
 *  Ļauj RS pamācību sadaļām pievienot placeholderus iztrūkstošajiem rakstiem.
 *
 * 	Moduļa adrese: runescape.exs.lv/rsph
 */
if (!isset($sub_include)) {
	set_flash('No hacking, pls.');
	redirect();
}

if ($_GET['var1'] == 'ph') {
	exit;

	$cat_ids = array(99, 100, 160, 193, 792, 80, 95, 96, 97, 101);
	$cats = array(
		array(99, 'F2P kvesti'),
		array(100, 'P2P kvesti'),
		array(193, 'Minikvesti'),
		array(160, 'Minispēles'),
		array(792, 'Distractions & Diversions'),
		array(80, 'Ceļveži: Citas vietas'),
		array(95, 'Ceļveži: Wilderness'),
		array(96, 'Ceļveži: Pilsētas'),
		array(97, 'Ceļveži: Salas'),
		array(101, 'Ceļveži: Pazemes')
	);

	// ievieto datubāzē jaunu placeholder
	if (isset($_POST['submit'])) {
		if (title2db($_POST['title']) != '' && in_array((int) $_POST['cat'], $cat_ids)) {
			$db->query("INSERT INTO `rs_placeholders` (cat,title,url,url2) VALUES (
			  '" . (int) $_POST['cat'] . "',
			  '" . title2db($_POST['title']) . "',
			  '" . sanitize($_POST['url']) . "',
			  '" . sanitize($_POST['url2']) . "'
			)");
		}
	}
	// izdzēš no datubāzes jau esošu placeholder
	else if (isset($_GET['delete'])) {
		$id = (int) $_GET['delete'];
		$db->query("DELETE FROM `rs_placeholders` WHERE `id` = '$id' LIMIT 1");
		header("Location: /" . $_GET['viewcat'] . "/ph");
	}
	/* izvada visus pievienotos rakstus */
	$tpl->newBlock('rsmod-placeholders');
	$tpl->newBlock('rsmod-ph-addnew');

	foreach ($cats as $cat) {
		$get_ph = $db->get_results("SELECT * FROM `rs_placeholders` WHERE `cat` = '" . $cat[0] . "' ORDER BY `cat` ASC, `title` ASC");
		if ($get_ph) {
			$tpl->newBlock('rsmod-phtable');
			$tpl->assign('cat-title', $cat[1]);
			foreach ($get_ph as $ph) {
				$tpl->newBlock('rsmod-ph-listitem');
				$tpl->assignAll($ph);
				$link1 = ($ph->url == '') ? 'delete' : 'tick';
				$link2 = ($ph->url2 == '') ? 'delete' : 'tick';
				$tpl->assign(array(
					'link1' => $link1,
					'link2' => $link2
				));
			}
		}
	}
}