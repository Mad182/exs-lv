<?php
session_start();

include('facebook.php');
include('../../config/exs-lv.php');

$facebook = new Facebook(array(
	'appId'  => '353222841436117',
	'secret' => 'f6ac0e495e8b5a09ff2ea463383dc57c',
	'cookie' => true,
));

$session = $facebook->getSession();
var_dump($_REQUEST);

var_dump($session);
exit;

header('Location: http://exs.lv/modules/fb-login/index.php');

