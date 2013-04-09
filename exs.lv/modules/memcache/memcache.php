<?php

if (!im_mod()) {
	redirect();
}

$file = '';
if (!empty($_GET['var1'])) {
	$file = $_GET['var1'];
}

ksort($_GET);
$get = '';
$i = 0;
foreach ($_GET as $k => $v) {
	if (!empty($k) && !in_array($k, array('viewcat', 'var1'))) {
		if ($i == 0) {
			$get .= '?' . $k . '=' . $v;
		} else {
			$get .= '&' . $k . '=' . $v;
		}
		$i++;
	}
}

$contents = file_get_contents('http://exs.lv/modules/memcache/' . $file . $get);

$tpl->assignGlobal('api-content', str_replace('modules/memcache/index.php?&op', 'Memcached/?op', $contents));
