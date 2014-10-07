<?php

$data = json_decode(curl_get('http://meme.exs.lv/list.php'));

if (isset($_GET['var1']) && in_array($_GET['var1'], $data)) {
	$tpl->newBlock('generator');
	$tpl->assign(array(
		'image' => $_GET['var1']
	));
}

foreach ($data as $img) {
	$tpl->newBlock('img');
	$tpl->assign(array(
		'file' => $img
	));
}

