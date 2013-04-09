<?php

if ($auth->ok) {
	$tpl->newBlock('desas');
} else {
	$tpl->newBlock('error-nologin');
}
