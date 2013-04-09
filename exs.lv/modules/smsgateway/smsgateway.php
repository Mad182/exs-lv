<?php

if ($auth->ok) {


	$tpl = new TemplatePower('modules/smsgateway/smsgateway.tpl');
	$tpl->prepare();

	if (isset($_GET['lang']) && $_GET['lang'] == 'uk') {
		$tpl->newBlock('fortumo-uk');
	} elseif (isset($_GET['lang']) && $_GET['lang'] == 'ie') {
		$tpl->newBlock('fortumo-ie');
	} elseif (isset($_GET['lang']) && $_GET['lang'] == 'no') {
		$tpl->newBlock('fortumo-no');
	} else {
		$tpl->newBlock('fortumo-lv');
	}

	$tpl->assign(array(
		'user-id' => $auth->id
	));

	$tpl->printToScreen();
	exit;
}
