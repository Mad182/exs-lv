<?php

if($auth->level != 1) {
	redirect();
}

$tpl->newBlock('link-manager');

$active = 'dofollow';
$title = 'Draudzīgās lapas bez nofollow';
if(isset($_GET['var1']) && $_GET['var1'] == 'blacklisted') {
	$active = 'blacklisted';
	$title = 'Bloķētās lapas';
}
	
$tpl->assign(array(
	'active-'.$active =>  'active',
	'title' => $title
));

if(isset($_GET['var2']) && $_GET['var2'] == 'delete' && isset($_GET['var3'])) {
	$delete = (int)$_GET['var3'];
	if($delete) {
		$db->query("DELETE FROM `".$active."_sites` WHERE `id` = $delete LIMIT 1");
	}

	$m->delete($active.'_sites');
	redirect('/'.$category->textid.'/'.$active);
}

if(isset($_POST['submit-domain']) && !empty($_POST['domain']) && strlen(trim($_POST['domain'])) > 3) {
	$domain = sanitize(htmlspecialchars(trim($_POST['domain'])));
	$db->query("INSERT INTO `".$active."_sites` (`id`, `url`) VALUES (NULL, '$domain')");
	$m->delete($active.'_sites');
}

$domains = $db->get_results("SELECT * FROM `".$active."_sites` ORDER BY `id` DESC");

if(!empty($domains)) {
	foreach($domains as $domain) {
		$tpl->newBlock('link-manager-item');
		$tpl->assign(array(
			'domain' => htmlspecialchars($domain->url),
			'id' => $domain->id,
			'type' => $active
		));
	}
}

