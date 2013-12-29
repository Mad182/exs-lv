<?php
/**
 *	RuneScape kvestiem nepieciešamo prasmju pārvaldība.
 *
 *  Ļauj norādīt, kādi līmeņi nepieciešami, lai spēlētājs
 *  varētu izpildīt visus RuneScape kvestus.
 *
 *	Moduļa adrese: runescape.exs.lv/qskills
 */
 
if ( !isset($sub_include) ) {
    set_flash('No hacking, pls.');
    redirect();
}

if ($_GET['var1'] == 'qskills') {
    exit;
    
	$tpl->newBlock('rsmod-quests-skills');
	if (isset($_POST['submit'])) {
		$get = $db->get_results("SELECT `id` FROM `rs_qskills`");
		foreach ($get as $data) {
			if (isset($_POST[$data->id . '_level']) && isset($_POST[$data->id . '_quest'])) {
				$db->query("UPDATE `rs_qskills` SET `level` = '" . (int) $_POST[$data->id . '_level'] . "', `quest` = '" . sanitize($_POST[$data->id . '_quest']) . "' WHERE `id` = '" . $data->id . "' ");
			}
		}
	}
	$skills = $db->get_results("SELECT * FROM `rs_qskills` ORDER BY `skill` ASC");
	if ($skills) {
		$tpl->newBlock('skills-col');
		$skaits = 0;
		foreach ($skills as $data) {
			$tpl->newBlock('level');
			$tpl->assignAll($data);
			$skaits++;
			if ($skaits == 13) {
				$tpl->newBlock('skills-col');
			}
		}
	}
}