<?php
/**
 *  RuneScape apakšprojekta labā kolonna.
 *  Aplūkojama tikai šaurākajās lapas sadaļās, kur redzamas abas kolonnas.
 */
$tpl->newBlock('main-layout-right');

// jaunākais galerijās
$tpl->assign(array(
	'latest-noscript'   => get_latest_images(),
	'pages-selected'    => 'active ',
));


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