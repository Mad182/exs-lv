<?php

if (!im_mod()) {
	redirect();
}

$tpl->assignGlobal(array(
	'st-uptime' => nl2br(`uptime`),
	'st-df' => nl2br(`df -h`),
	'st-top' => nl2br(`top -b -n 1 | grep exs`)
));
