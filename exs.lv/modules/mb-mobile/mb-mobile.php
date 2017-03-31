<?php

$group = null;
if(!empty($_GET['group'])) {
	$group = (int)$_GET['group'];
}
if (!isset($_GET['tab'])) $_GET['tab'] = 'all';
echo get_latest_mbs($_GET['tab'], $group);
exit;

