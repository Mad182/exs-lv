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

//include(CORE_PATH . '/modules/core/poll.php');

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

// nejauši atlasīts RuneScape fakts;
$tpl->newBlock('runescape-facts-box');
if ($rsfacts = $db->get_row("SELECT `text` FROM `rs_facts` WHERE `deleted_by` = 0 ORDER BY RAND() LIMIT 1")) {
    $tpl->assign('random-fact', $rsfacts->text);
}