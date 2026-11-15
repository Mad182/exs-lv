<?php

/**
 * Meklēšana izmnatojot google CSE
 * https://www.google.com/cse/
 * 
 * Meklētāja id atbilstoši domēnam iegūst no datubāzes (`cat`.`content`)
 * 
 */
$robotstag = ['noindex', 'follow'];
$meta_description = 'Meklēšana EXS.LV spēļu un izklaides portālā — atrodi rakstus, spēļu apskatus, foruma tēmas un diskusijas.';

$tpl->newBlock('search-results');
$tpl->assign('cx', $category->content);
