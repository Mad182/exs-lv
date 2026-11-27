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

$_apiURL = 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=' . $steam_api_key . '&steamids='; //Steam API url for players data
$_limit = 100;  //steam has limit 100 users for request
$steamIDs = [];


$users = $db->get_results("SELECT `id`, `nick`, `steam_id`  FROM `users` WHERE `steam_id` IS NOT NULL");
$user_id_array = [];

//array to later get exs_id from steam_id without additional query and store steam id's in separate array for curl request
if (!empty($users)) {
    foreach ($users as $user) {
        $user_id_array[$user->steam_id] = $user->id;
        $steamIDs[] = $user->steam_id;
    }
}

//split arrays in chunks to don't reach steam limit
$chunks = array_chunk($steamIDs, $_limit);

foreach ($chunks as $chunk) {
    $ids_string = implode(',', $chunk);

    //do curl request
    $url = $_apiURL . $ids_string;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($ch, CURLOPT_TIMEOUT, 4);

    $output = curl_exec($ch);

    //json object from steam with players data
    $response = is_string($output) ? json_decode($output) : null;
    if (empty($response->response->players) || !is_array($response->response->players)) {
        continue;
    }

    foreach ($response->response->players as $user) {
        if (empty($user->steamid)) {
            continue;
        }
        $steamid_clean = sanitize($user->steamid);
        $fields = [
            'communityvisibilitystate' => intval($user->communityvisibilitystate ?? 0),
            'profilestate'             => intval($user->profilestate ?? 0),
            'personaname'              => sanitize($user->personaname ?? ''),
            'lastlogoff'               => intval($user->lastlogoff ?? 0),
            'profileurl'               => sanitize($user->profileurl ?? ''),
            'avatar'                   => sanitize($user->avatar ?? ''),
            'personastate'             => intval($user->personastate ?? 0),
            'realname'                 => sanitize($user->realname ?? ''),
            'primaryclanid'            => sanitize($user->primaryclanid ?? ''),
            'timecreated'              => intval($user->timecreated ?? 0),
            'personastateflags'        => intval($user->personastateflags ?? 0),
            'gameextrainfo'            => sanitize($user->gameextrainfo ?? ''),
            'gameid'                   => sanitize($user->gameid ?? ''),
            'loccountrycode'           => sanitize($user->loccountrycode ?? ''),
            'locstatecode'             => sanitize($user->locstatecode ?? ''),
            'loccityid'                => intval($user->loccityid ?? 0)
        ];

        //check if entry already exists
        $id = $db->get_var("SELECT `id` FROM `steam_player_info` WHERE `steamid` = '" . $steamid_clean . "' LIMIT 1");

        //update if entry exists in table
        if (!empty($id)) {
            $db->update('steam_player_info', $id, $fields);
        } elseif (isset($user_id_array[$user->steamid])) {
            //do first entry if user doesn't exist in table
            $db->insert('steam_player_info', array_merge([
                'user_id' => intval($user_id_array[$user->steamid]),
                'steamid' => $steamid_clean,
            ], $fields));
        }
    }
}

echo "\n\nfinished!\n";

