<?php

$page_title = 'Desas (Tic-Tac-Toe) — Spēle 2 spēlētājiem pārlūkā';
$meta_description = 'Spēlē klasisko Desas (Tic-Tac-Toe) spēli 2 spēlētājiem tiešsaistē pārlūkā bez lejupielādes! Pārspēj pretinieku 3x3 laukumā un uzlabo savu uzvaru statistiku.';
$opengraph_meta['description'] = $meta_description;

if ($auth->ok) {
	$tpl->newBlock('desas');
} else {
	$tpl->newBlock('error-nologin');
}
