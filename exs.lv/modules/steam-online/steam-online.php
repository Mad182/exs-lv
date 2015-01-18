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
$chunked_grouped = partition($grouped, 3);

$tpl->newBlock('steam-game-wrapper');

//izvadam lietotājus caur loopiem
foreach ($chunked_grouped as $row) {
    $tpl->newBlock('steam-game-col');

    foreach ($row as $game) {

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
}


/**
 * Splits array into equal parts
 *
 * @param Array $list
 * @param int $p
 * @return multitype:multitype:
 * @link http://www.php.net/manual/en/function.array-chunk.php#75022
 */
function partition(Array $list, $p)
{
    $listlen = count($list);
    $partlen = floor($listlen / $p);
    $partrem = $listlen % $p;
    $partition = array();
    $mark = 0;
    for ($px = 0; $px < $p; $px++) {
        $incr = ($px < $partrem) ? $partlen + 1 : $partlen;
        $partition[$px] = array_slice($list, $mark, $incr);
        $mark += $incr;
    }
    return $partition;
}