<?php

/**
 * cron_steam.php
 * cron skripts priekš datu ievākšanas no steam
 * 
 * Izpildās ar 3 minūšu intervālu
 * \/3 * * * * exs php /home/www/exs.lv/cron_steam.php
 */
if (PHP_SAPI !== 'cli') {
    echo 'CLI only!';
    exit;
}

echo 'cron_steam.php started' . "\n";

chdir(__DIR__);
ini_set('memory_limit', '128M');
error_reporting(0);
ini_set('display_errors', 'Off');

require('configdb.php');
require(CORE_PATH . '/includes/class.mdb.php');
require(CORE_PATH . '/includes/functions.core.php');

//mysql konekcija
$db = new mdb($username, $password, $database, $hostname);
unset($password);

//memcached konekcija
$m = new Memcache;
$m->connect($mc_host, $mc_port);



$_apiURL = 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=' . $steam_api_key . '&steamids='; //Steam API url for players data
$_limit = 100;  //steam has limit 100 users for request
$steamIDs = array();


$users = $db->get_results("SELECT `id`, `nick`, `steam_id`  FROM `users` WHERE `steam_id` IS NOT NULL");
$user_id_array = array();

//array to later get exs_id from steam_id without additional query and store steam id's in separate array for curl request
foreach ($users as $user) {
    $user_id_array[$user->steam_id] = $user->id;
    $steamIDs[] = $user->steam_id;
}

//split arrays in chunks to don't reach steam limit
$chunks = array_chunk($steamIDs, $_limit);

foreach ($chunks as $chunk) {
    $ids_string = implode(',', $chunk);

    //do curl request
    $url = $_apiURL . $ids_string;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($ch, CURLOPT_TIMEOUT, 4);

    $output = curl_exec($ch);

    //json object from steam with players data
    $response = json_decode($output);

    foreach ($response as $players) {
        foreach ($players as $player) {
            foreach ($player as $user) {

                //check if entry already exists
                $is_in_table = $db->get_results("SELECT `id`, `steamid` FROM `steam_player_info` WHERE `steamid` = " . $user->steamid . " LIMIT 1;");

                //get id if entry exists
                foreach ($is_in_table as $exs_user) {
                    $id = $exs_user->id;
                }

                //update if entry exists in table
                if (!empty($is_in_table)) {
                    $db->update('steam_player_info', $id, array(
                        'communityvisibilitystate' => $user->communityvisibilitystate,
                        'profilestate'             => $user->profilestate,
                        'personaname'              => $user->personaname,
                        'lastlogoff'               => $user->lastlogoff,
                        'profileurl'               => $user->profileurl,
                        'avatar'                   => $user->avatar,
                        'personastate'             => $user->personastate,
                        'realname'                 => $user->realname,
                        'primaryclanid'            => $user->primaryclanid,
                        'timecreated'              => $user->timecreated,
                        'personastateflags'        => $user->personastateflags,
                        'gameextrainfo'            => $user->gameextrainfo,
                        'gameid'                   => $user->gameid,
                        'loccountrycode'           => $user->loccountrycode,
                        'locstatecode'             => $user->locstatecode,
                        'loccityid'                => $user->loccityid
                    ));
                } else {

                    //do first entry if user doesn't exist in table
                    $db->insert('steam_player_info', array(
                        'user_id'                  => $user_id_array[$user->steamid],
                        'steamid'                  => $user->steamid,
                        'communityvisibilitystate' => $user->communityvisibilitystate,
                        'profilestate'             => $user->profilestate,
                        'personaname'              => $user->personaname,
                        'lastlogoff'               => $user->lastlogoff,
                        'profileurl'               => $user->profileurl,
                        'avatar'                   => $user->avatar,
                        'personastate'             => $user->personastate,
                        'realname'                 => $user->realname,
                        'primaryclanid'            => $user->primaryclanid,
                        'timecreated'              => $user->timecreated,
                        'personastateflags'        => $user->personastateflags,
                        'gameextrainfo'            => $user->gameextrainfo,
                        'gameid'                   => $user->gameid,
                        'loccountrycode'           => $user->loccountrycode,
                        'locstatecode'             => $user->locstatecode,
                        'loccityid'                => $user->loccityid
                    ));
                }

            }
        }


        //empty dynamic values for next loop
        unset($ids_string);
        unset($steamIDs);
    }
}

echo "\n\nfinished!\n";

