<?php

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

//sadalam chunkos, lai glītāks layout
$chunked_grouped = array_chunk($grouped, 3);

//izvadam lietotājus caur loopiem
foreach ($chunked_grouped as $row) {
    $tpl->newBlock('steam-game-row');

    foreach ($row as $game) {

        $tpl->newBlock('steam-game');
        $tpl->assign('game-id', $game[0]->gameid);

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
}
