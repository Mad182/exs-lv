<?php

/**
 * Meklēšana izmnatojot google CSE
 * https://www.google.com/cse/
 * 
 * Meklētāja id atbilstoši domēnam iegūst no datubāzes (`cat`.`content`)
 * 
 */
$tpl->newBlock('search-results');
$tpl->assign('cx', $category->content);
