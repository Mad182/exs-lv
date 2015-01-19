<?php

$out = '';
if (isset($_GET['var1']) && $_GET['var1'] == 'csgo') {
	$out = get_game_monitor('http://csgo.exs.lv/monitor/index.php', true);
} elseif (isset($_GET['var1']) && $_GET['var1'] == 'ut2004') {
	$out = get_game_monitor('http://csgo.exs.lv/monitor/ut.php', true);
}

if (isset($_GET['_'])) {
	die($out);
}

$tpl->assignGlobal('monit', $out);

