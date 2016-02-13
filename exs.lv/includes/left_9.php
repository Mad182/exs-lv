<?php
/**
 *  RuneScape apakšprojekta kreisā kolonna un tās saturs
 *  (lietotāja profila attēls, miniblogi, jaunākās grupas u.tml.).
 */

$tpl->newBlock('main-layout-left');


// kreisajā kolonnā redzama informācija par lietotāju, 
// ar kuru saistīta skatāmā informācija
if (isset($category) && $category->isblog != 0 && empty($inprofile)) {
    $inprofile = get_user($category->isblog);
}
if (!empty($inprofile)) {

    $avatar = get_avatar($inprofile, 'l');

    $tpl->newBlock('profile-box');
    $tpl->assignGlobal(array(
        'url'                   => '/user/' . $inprofile->id,
        'profile-nick'          => h($inprofile->nick),
        'profile-slug'          => mkslug($inprofile->nick),
        'profile-id'            => $inprofile->id,
        'avatar'                => $avatar,
        'profile-top-awards'    => get_top_awards($inprofile->id)
    ));

    if (!empty($inprofile->custom_title)) {
        $tpl->assign(array(
            'custom_title' => ' <span style="font-size:11px">(' . $inprofile->custom_title . ')</span>'
        ));
    }

    // autorizētiem lietotājiem redzama iespēja nosūtīt lietotājam vēstuli
    if ($auth->ok === true && $auth->id != $inprofile->id) {
        $tpl->newBlock('profilebox-pm-link');
    }
    
    $isblog = get_blog_by_user($inprofile->id);
    if ($isblog) {
        $blog = get_cat($isblog);
        $count = $db->get_var("SELECT count(*) FROM `pages` WHERE `category` = '" . $isblog . "' AND `lang` = '".(int)$lang."' ");
        $tpl->newBlock('profilebox-blog-link');
        $tpl->assign(array(
            'url' => '/' . $blog->textid,
            'count' => $count
        ));
    }

    // administrācijai un pašam lietotājam redzams brīdinājumu skaits
    if ($auth->ok === true
            && ($auth->id == $inprofile->id
            || im_mod())
            && !in_array($inprofile->level, array(1, 2))) {
        $tpl->newBlock('profilebox-warn');
        if ($inprofile->warn_count > 0) {
            $tpl->assign(array(
                'profile-warns' => '&nbsp;(' . $inprofile->warn_count . ')',
                'class' => ' class="active"'
            ));
        }
    }

    // adrese uz twitter profilu
    if (!empty($inprofile->twitter)) {
        $tpl->newBlock('profilebox-twitter-link');
        $tpl->assign(array(
            'twitter' => $inprofile->twitter
        ));
    }

    // adrese uz youtube profilu
    if (!empty($inprofile->yt_name)) {
        $tpl->newBlock('profilebox-yt-link');
        $tpl->assign(array(
            'yt-name' => $inprofile->yt_name,
            'yt-slug' => mkslug($inprofile->yt_name)
        ));
    }
}


// jaunākais lietotāju albumos
// - tikai index lapā, jo citviet attēlus rādīs otrā kolonnā
if ($category->textid === 'index' && empty($inprofile)) {
    $tpl->newBlock('latest-images');
    $tpl->assign('latest-images', get_latest_images());
}


// saraksts ar jaunākajiem miniblogiem;
// atkarībā no tā, kura cilne norādīta, redzami grupu vai ārpus tām esošie ieraksti;
// friends šajā gadījumā nozīmē, ka skatīti tiek grupu ieraksti
$tpl->newBlock('mb-box');
$sel = 'all';
if ($auth->ok && !empty($_COOKIE['last-mbs-tab']) && $_COOKIE['last-mbs-tab'] == 'friends') {
    $mbs = get_latest_mbs('friends');
    $sel = 'friends';
} else {
    $mbs = get_latest_mbs('all');
}
$tpl->assign('out', $mbs);

// ciļņu izvēlne redzama tikai autorizētiem lietotājiem;
// neautorizēts lietotājs vienkārši grupās nav
if($auth->ok) {
    $tpl->newBlock('mb-tabs');
    $tpl->assign(array(
        $sel . '-selected' => 'active '
    ));
}

// poga uz jauna ieraksta pievienošanu
if ($auth->ok === true) {
    $tpl->assignGlobal(
        'miniblog-add',
        '&nbsp;<a href="/say/' . $auth->id . '#content" class="mb-create" title="Pievienot jaunu ierakstu">izveidot</a>');
}


// jaunāko izveidoto RuneScape grupu saraksts
if ($groups = get_latest_groups()) {

    $tpl->newBlock('groups-l-list');
    
    foreach ($groups as $group) {
    
        $tpl->newBlock('groups-l-node');

        if(!empty($group->strid)) {
            $group->link = '/'.$group->strid;
        } else {
            $group->link = '/group/'.$group->id;
        }

        $tpl->assign(array(
            'title'     => $group->title,
            'link'      => $group->link,
            'avatar'    => $group->avatar
        ));
    }
    unset($groups);
}


// nejauši atlasīts RuneScape fakts
$tpl->newBlock('runescape-facts-box');
if ($rsfacts = $db->get_row("SELECT `text` FROM `rs_facts` WHERE `deleted_by` = 0 ORDER BY RAND() LIMIT 1")) {
    $tpl->assign('random-fact', $rsfacts->text);
}
