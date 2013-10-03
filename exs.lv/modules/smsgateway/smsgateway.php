<?php

if ($auth->ok) {

	$tpl = new TemplatePower('modules/smsgateway/smsgateway.tpl');
	$tpl->prepare();

	if (isset($_GET['var1']) && $_GET['var1'] == 'uk') {
		$tpl->newBlock('fortumo-uk');
	} elseif (isset($_GET['var1']) && $_GET['var1'] == 'ie') {
		$tpl->newBlock('fortumo-ie');
	} elseif (isset($_GET['var1']) && $_GET['var1'] == 'no') {
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

