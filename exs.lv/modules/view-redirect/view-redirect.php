<?php

if($_GET['var1'] == 'Flash-speles' && !empty($_GET['var2'])) {
	redirect('/flash-speles/'.strtolower($_GET['var2']), true);
}

$cat = get_cat($_GET['var1']);

redirect('/'.$cat->textid, true);

