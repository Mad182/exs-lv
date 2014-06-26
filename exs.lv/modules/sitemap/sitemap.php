<?php

$add_css .= ',sitemap.css';

$sitemap_modules = "'list','forums','rshelp','index','flash-games','raksti','groups','imgupload','items-db','listsub','miniblogs','polls','register','search','servers','snake','statistics','text','cs_monitor','kasnotiek','say','gifav'";

$tpl->newBlock('sitemap-body');

if ($auth->level == 1 && isset($_GET['moveup'])) {
	$move = $db->get_row("SELECT * FROM `cat` WHERE `id` = '" . intval($_GET['moveup']) . "'");
	$upper = $db->get_row("SELECT * FROM `cat` WHERE `parent` = '$move->parent' AND (`lang` = '$lang' OR `lang` = 0) AND `mods_only` = 0 AND `module` IN(" . $sitemap_modules . ") AND `ordered` < '$move->ordered' ORDER BY `ordered` DESC LIMIT 1");
	if ($move && $upper) {
		$db->query("UPDATE `cat` SET `ordered` = '$move->ordered' WHERE `id` = '$upper->id' LIMIT 1");
		$db->query("UPDATE `cat` SET `ordered` = '$upper->ordered' WHERE `id` = '$move->id' LIMIT 1");
	}
}

if ($auth->level == 1 && isset($_GET['movedown'])) {
	$move = $db->get_row("SELECT * FROM `cat` WHERE `id` = '" . intval($_GET['movedown']) . "'");
	$upper = $db->get_row("SELECT * FROM `cat` WHERE `parent` = '$move->parent' AND (`lang` = '$lang' OR `lang` = 0) AND `mods_only` = 0 AND `module` IN(" . $sitemap_modules . ") AND `ordered` > '$move->ordered' ORDER BY `ordered` ASC LIMIT 1");
	if ($move && $upper) {
		$db->query("UPDATE `cat` SET `ordered` = '$move->ordered' WHERE `id` = '$upper->id' LIMIT 1");
		$db->query("UPDATE `cat` SET `ordered` = '$upper->ordered' WHERE `id` = '$move->id' LIMIT 1");
	}
}

$out = '';
$tpl->assign('out', sitemap(0, $sitemap_modules));

