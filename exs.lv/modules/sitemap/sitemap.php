<?php

/**
 * Lapas kartes skats
 */
$add_css[] = 'sitemap.css';

$sitemap_modules = "'list','forums','rshelp','index','flash-games','raksti','groups','imgupload','items-db','listsub','miniblogs','polls','register','search','servers','snake','statistics','text','cs_monitor','kasnotiek','say','gifav'";

$tpl->newBlock('sitemap-body');

//sadaļu pārkārtošana
if ($auth->level == 1 && !empty($_GET['moveup'])) {
	move_cat($_GET['moveup'], 'up');
} elseif ($auth->level == 1 && !empty($_GET['movedown'])) {
	move_cat($_GET['movedown'], 'down');
}

$out = '';
$tpl->assign('out', create_sitemap(0, $sitemap_modules));

