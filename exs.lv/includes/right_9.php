<?php
/**
 *  RuneScape apakšprojekta labā kolonna.
 *  Aplūkojama tikai šaurākajās lapas sadaļās, kur redzamas abas kolonnas.
 */
$tpl->newBlock('main-layout-right');

// jaunākais galerijās
$sel = 'pages';
if (!empty($_COOKIE['last-sidebar-tab']) && $_COOKIE['last-sidebar-tab'] == 'gallery') {
	$out = get_latest_images();
	$sel = 'gallery';
} else {
	$out = get_latest_posts();
}
$tpl->assign(array(
	'latest-noscript'   => $out,
	$sel . '-selected'  => 'active '
));
unset($out);


// aptaujas
include(CORE_PATH . '/modules/core/poll.php');


//  jaunāko izveidoto RuneScape grupu saraksts
if ($groups = get_latest_groups()) {

	$tpl->newBlock('groups-l-list');
    
	foreach ($groups as $group) {
    
		$tpl->newBlock('groups-l-node');

		if(!empty($group->strid)) {
			$group->link = '/'.$group->strid;
		} else {
			$group->link = '/group/'.$group->id;
		}

		$tpl->assign(array(
			'title'     => $group->title,
			'link'      => $group->link,
            'avatar'    => $group->avatar
		));
	}
	unset($groups);
}