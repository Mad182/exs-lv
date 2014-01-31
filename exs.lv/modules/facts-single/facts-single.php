<?php

if (!empty($_GET['var1']) && $_GET['var1'] == 'rs') {
	echo $db->get_var("SELECT `text` FROM `rs_facts` WHERE `deleted_by` = 0 ORDER BY rand() LIMIT 1");
	echo ' <a href="/fact/rs" class="moar">Citu &raquo;</a>';
} else {
	echo $db->get_var("SELECT `text` FROM `facts` ORDER BY rand() LIMIT 1");
	echo ' <a href="/fact" class="moar">Citu &raquo;</a>';
}
exit;
