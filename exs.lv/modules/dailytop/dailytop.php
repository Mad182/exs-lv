<?php

if(!empty($_GET['var1']) && $_GET['var1'] === 'groups') {
	echo group_top();
} else {
	echo user_top();
}

exit;

