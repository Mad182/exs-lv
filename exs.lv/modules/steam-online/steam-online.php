<?php

//load css
$add_css[] = 'steam.css';

$grouped = array();

//lietotāji, kas šobrīd kaut ko spēlē
$players_online = $db->get_results("
    SELECT *
    FROM `steam_player_info`
    WHERE `gameid`
    ORDER BY `gameextrainfo` ASC
");


$tpl->newBlock('steam');

//ja lietotājs nav piesaistījis steam kontu, tad parādam linku uz steam login lapu
if (empty($auth->steam_id)) {
    $tpl->newBlock('steam-login');
}


//sagrupējam lietotājus pēc spēles id
foreach ($players_online as $player) {
    if ($player->gameid) {
        $grouped[$player->gameid][] = $player;
    }
}

$tpl->newBlock('steam-game-wrapper');

foreach ($grouped as $game) {
    $tpl->newBlock('steam-game');
    $tpl->assign('game-id', $game[0]->gameid);
    $tpl->assign('game-name', $game[0]->gameextrainfo);

    foreach ($game as $user) {
        $exs_user = get_user($user->user_id);
        $tpl->newBlock('steam-player');
        $tpl->assign(array(
            'profile-url' => $user->profileurl,
            'id'          => $exs_user->id,
            'nick'        => $exs_user->nick
        ));
    }
}
