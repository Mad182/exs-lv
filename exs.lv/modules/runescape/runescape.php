<?php
/** 
 *  RuneScape apakšprojekta sākumlapas modulis.
 *
 *  Šeit nonāk arī atsevišķi vienkārši pieprasījumi,
 *  kuriem netiek veidoti atsevišķi moduļi.
 */

if ($auth->ok) {
    set_action('sākumlapu');
}

// mod opšns
if ($auth->id == 115) {    
    if (isset($_GET['magic']) && $_GET['magic'] == 'readrss') {
        read_rss(true);
    }   
    if (isset($_GET['magic']) && $_GET['magic'] == 'recreate') {
        create_news('rs3');
        create_news('oldschool');
    }
}

if ($auth->ok) {
    // fona noformējuma iestatījumi
    if (isset($_GET['bg'])) {
        if ($_GET['bg'] === 'goats') {
            $db->update('users', $auth->id, ['rs_bg' => 1]);
        } else if ($_GET['bg'] === 'map') {
            $db->update('users', $auth->id, ['rs_bg' => 2]);
        } else {
            $db->update('users', $auth->id, ['rs_bg' => 0]);
        }
        $auth->reset();
        redirect();
    }
    // izklājuma iestatījumi
    if (isset($_GET['layout'])) {
        if ($_GET['layout'] === 'sticky') {
            $db->update('users', $auth->id, ['rs_layout' => 0]);
        } else {
            $db->update('users', $auth->id, ['rs_layout' => 1]);
        }
        $auth->reset();
        redirect();
    }
}


// sākumlapā rādīs ierakstus no runescape.com RSS feed
// (izvēle starp OSRS un RuneScape 3 versiju)
$news_type = 'rs3';
if (isset($_COOKIE['last-rsnews-tab']) &&
    $_COOKIE['last-rsnews-tab'] === 'oldschool') {
    $news_type = 'oldschool';
}
read_rss(); // iekšēji funkcija nolasīs tikai reizi x minūtēs

$tpl->newBlock('news-tabs');
$tpl->assign($news_type.'-selected', 'active '); 
$tpl->assign('selected-news', fetch_news($news_type));

// $tpl->newBlock('articles-container');
// jaunais bloks ar jaunāko RuneScape ziņu virsrakstiem
// $tpl->newBlock('rsnews-container');
/*$all_news = $db->get_results("
    SELECT
        `rs_news`.`id`,
        `rs_news`.`mb_id`,
        `rs_news`.`has_image`,
        `rs_news`.`news_title`          AS `title`,
        `rs_news`.`news_description`    AS `description`,
        `rs_news`.`news_date`           AS `date`,
        `rs_news`.`news_category`       AS `category`,
        `rs_news`.`news_link`           AS `link`,
        `miniblog`.`removed`,
        `miniblog`.`text`
    FROM `rs_news`
        JOIN `miniblog` ON `rs_news`.`mb_id` = `miniblog`.`id`
    WHERE
        `rs_news`.`deleted_by` = 0 AND
        `rs_news`.`is_oldschool` = 0
    ORDER BY `rs_news`.`id` DESC LIMIT 5
");

if ($all_news) {
    foreach ($all_news as $news) {
        $tpl->newBlock('rsnews-single');
        $tpl->assign('title', $news->title);
    }
}*/
