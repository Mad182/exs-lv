<?php
/**
 *	RuneScape ceļvežu pārvaldība.
 *
 *  Ceļvežu raksti tiek sakārtoti specifiskā sarakstā ar attēliem.
 *
 *	Moduļa adrese: runescape.exs.lv/areas
 */
 
if ( !isset($sub_include) ) {
    set_flash('No hacking, pls.');
    redirect();
}

// sadaļas rediģēšanas lapa
if ($_GET['var1'] == 'places' && isset($_GET['edit'])) {
    exit;
	$page_id = (int) $_GET['edit'];
	$page = $db->get_row("SELECT `id`,`title`,`strid`,`rsclass` FROM `pages` WHERE `id` = '$page_id' AND `category` = '195' LIMIT 1");
	if ($page) {
		$tpl->newBlock('rsmod-cities-edit');
		$tpl->assignAll($page);
		// izvēlne ar kategorijām
		$cats = $db->get_results("SELECT `id`,`title` FROM `rs_classes` WHERE `cat` = 'areas' ORDER BY `order` ASC");
		if ($cats) {
			foreach ($cats as $cat) {
				$tpl->newBlock('rsmod-cities-cat');
				$tpl->assign(array(
					'nr' => $cat->id,
					'cat' => $cat->title
				));
				if ($cat->id == $page->quest_chapter) {
					$tpl->assign('selected', ' selected="selected"');
				}
			}
		}
	}
	
} else if ($_GET['var1'] == 'places' && isset($_GET['delete']) && $auth->id == 115) {
    exit;
	$page_id = (int) $_GET['delete'];
	//$page = $db->get_var("SELECT count(*) FROM `rs_help` WHERE `page_id` = '$page_id'");
	$del = $db->query("DELETE FROM `rs_help` WHERE `page_id` = '$page_id' ");
	header("Location: /" . $_GET['viewcat'] . "/places");
}


// updeito sadaļu
else if ($_GET['var1'] == 'places' && isset($_GET['var2'])) {
    exit;
	$page_id = (int) $_GET['var2'];
	$check = $db->get_var("SELECT count(*) FROM `pages` WHERE `id` = '" . $page_id . "' AND `category` = '195' ");
	if ($check == 1 && isset($_POST['cat'])) {
		$cat = (int) $_POST['cat'];
		$update = $db->query("UPDATE `pages` SET `rsclass` = '" . $cat . "' WHERE `id` = '" . $page_id . "' LIMIT 1");
		$up2 = $db->query("UPDATE `rs_help` SET `ready` = '1' WHERE `page_id` = '$page_id' ");
		// pārbaude, vai iekš rs_help tāds ir?
	}
	header("Location: /" . $_GET['viewcat'] . "/places");
}


// saraksts ar ceļvežiem pa sadaļām
else if ($_GET['var1'] == 'places') {
    exit;
	$tpl->newBlock('rsmod-cities');

	// ceļvežu rediģējamais saraksts
	$pages = $db->get_results("SELECT `id`,`strid`,`title`,`author`,`rsclass` FROM `pages` WHERE `category` = '195' ORDER BY `title` ASC ");
	if ($pages) {
		foreach ($pages as $page) {
		
			if ($user = get_user($page->author)) {
				$page->author = '<a style="font-size:11px;" href="' . mkurl('user', $user->id, $user->nick) . '">' . usercolor($user->nick, $user->level) . '</a>';
			}
			$tpl->newBlock('city');
			if ($c_title = $db->get_row("SELECT `title` FROM `rs_classes` WHERE `cat` = 'areas' AND `id` = '" . $page->rsclass . "' LIMIT 1")) {
				$tpl->assign('c-title', $c_title->title);
			}

			$tpl->assignAll($page);
			if ($auth->id == '115') {
				$tpl->newBlock('city-delete');
				$tpl->assign('id', $page->id);
			}
		}
	}
}