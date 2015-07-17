<?php

$group = null;
if(!empty($_GET['group'])) {
	$group = (int)$_GET['group'];
}

echo get_latest_mbs($_GET['tab'], $group);
exit;

