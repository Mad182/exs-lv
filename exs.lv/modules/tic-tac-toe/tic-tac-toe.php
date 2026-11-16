<?php

$page_title = 'Desas (Tic-Tac-Toe) — Tiešsaistes loģikas spēle';
$meta_description = 'Spēlē klasisko Desas (Tic-Tac-Toe) spēli tiešsaistē pārlūkā bez lejupielādes! Pārbaudi loģisko domāšanu un uzveic pretinieku 3x3 spēles laukumā.';
$opengraph_meta['description'] = $meta_description;

if ($auth->ok) {
	$tpl->newBlock('desas');
}
