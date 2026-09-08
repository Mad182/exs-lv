<?php

$meta_description = 'Spēlē klasisko Desas (Tic-Tac-Toe) spēli tiešsaistē par brīvu! Pārbaudi savu loģisko domāšanu, uzveic pretinieku 3x3 laukumā un uzstādi labāko rezultātu.';
$opengraph_meta['description'] = $meta_description;

if ($auth->ok) {
	$tpl->newBlock('desas');
} else {
	$tpl->newBlock('error-nologin');
}
