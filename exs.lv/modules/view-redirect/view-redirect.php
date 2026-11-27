<?php

if (isset($_GET['var1']) && $_GET['var1'] == 'Flash-speles' && !empty($_GET['var2'])) {
	redirect('/flash-speles/' . strtolower($_GET['var2']), true);
}

$cat = isset($_GET['var1']) ? get_cat($_GET['var1']) : false;

if (!empty($cat)) {
	redirect('/' . $cat->textid, true);
}

redirect('/', true);

