<?php
/**
 *  RuneScape apakšprojekta labā kolonna.
 *
 *  Aplūkojama tikai šaurākajās lapas sadaļās,
 *	kur vienlaicīgiredzamas abas kolonnas.
 */

$tpl->newBlock('main-layout-right');


// jaunākais galerijās
$sel = 'pages';
if (!empty($_COOKIE['last-sidebar-tab']) && $_COOKIE['last-sidebar-tab'] == 'gallery') {
	$out = get_latest_images();
	$sel = 'gallery';
} else { // jaunākais rakstos/blogos
	$out = rs_get_latest_pages();
}
$tpl->assign(array(
	'latest-noscript'   => $out,
	$sel . '-selected'  => 'active '
));
unset($out);


// lietotāja notifikācijas
if ($auth->ok === true) {
	if ($html = get_notify($auth->id)) {
		$tpl->newBlock('notification-list');
		$tpl->assign('out', $html);
		unset($html);
	}
}


// aptaujas
include(CORE_PATH . '/modules/core/poll.php');
