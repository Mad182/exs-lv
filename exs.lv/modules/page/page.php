<?php

if(!empty($_GET['var1'])) {
	$pagevars = explode('-', $_GET['var1']);
	$pageid = intval($pagevars[0]);
	
	if($pageid) {
		$page = $db->get_row("SELECT * FROM `pages` WHERE `id` = $pageid");
		if($page) {
			redirect(get_protocol($page->lang) . $config_domains[$page->lang]['domain'] . '/read/' . $page->strid, true);
		}
	}

}

redirect();

