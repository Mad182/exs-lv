<?php

$auth->logout();
if ($_SERVER['HTTP_REFERER'] == "") {
	$urla = "/";
} else {
	$urla = $_SERVER['HTTP_REFERER'];
}
redirect($urla);
