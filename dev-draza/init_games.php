<?php

/**
 * Initializes or updates games table and router categories for local development.
 */

$host = getenv('DB_HOST') ?: 'db';
$user = getenv('DB_USER') ?: 'exs';
$pass = getenv('DB_PASS') ?: 'dev';
$name = getenv('DB_NAME') ?: 'exs';
$sqlFile = __DIR__ . '/games_table.sql';

if (!file_exists($sqlFile)) {
    echo "Games SQL file not found: $sqlFile\n";
    exit(0);
}

$mysqli = @new mysqli($host, $user, $pass, $name);
if ($mysqli->connect_errno) {
    echo "Notice: Could not connect to DB ($host/$name): " . $mysqli->connect_error . "\n";
    exit(0);
}

// 1. Execute games_table.sql
$sql = file_get_contents($sqlFile);
if ($mysqli->multi_query($sql)) {
    do {
        if ($res = $mysqli->store_result()) {
            $res->free();
        }
    } while ($mysqli->more_results() && $mysqli->next_result());
}

// 2. Ensure router categories in `cat` table for all games
$game_cats = [
    'vardes' => ['id' => 2525, 'title' => 'Vardes', 'module' => 'vardes', 'content' => 'Šķērso šoseju un upi, lai uzstādītu jaunu rekordu!'],
    'runner' => ['id' => 2528, 'title' => 'Runner', 'module' => 'runner', 'content' => 'Spēlē Runner tiešsaistē EXS.LV! Bēdz no lietotāju avatāru šķēršļiem un lidojošiem droīdiem, vāc zelta zvaigznes, cīnies par vietu topā un pārspēj rekordus.'],
    'tornis' => ['id' => 2581, 'title' => 'Tornis', 'module' => 'tornis', 'content' => 'Būvē augstāko debesskrāpi! Liec 3D blokus vienu virs otra, veido combo sērijas un uzstādi rekordu!'],
    'arkanoid' => ['id' => 2583, 'title' => 'Arkanoid', 'module' => 'arkanoid', 'content' => 'Klasiskā Arkanoid arkādes spēle ar līmeņiem un kapsulu bonusiem']
];

foreach ($game_cats as $slug => $c) {
    $slugEsc = $mysqli->real_escape_string($slug);
    $titleEsc = $mysqli->real_escape_string($c['title']);
    $modEsc = $mysqli->real_escape_string($c['module']);
    $contentEsc = $mysqli->real_escape_string($c['content']);
    $cid = (int)$c['id'];

    $check = $mysqli->query("SELECT id FROM `cat` WHERE `textid` = '$slugEsc'");
    if ($check && $check->num_rows == 0) {
        $mysqli->query("INSERT INTO `cat` (`id`, `textid`, `lang`, `title`, `intro`, `module`, `parent`, `content`, `tmpl`, `status`, `sitemap`) VALUES ($cid, '$slugEsc', 1, '$titleEsc', 1, '$modEsc', 2516, '$contentEsc', 'main', 'active', 1) ON DUPLICATE KEY UPDATE `textid` = VALUES(`textid`), `module` = VALUES(`module`), `title` = VALUES(`title`)");
    }
}

echo "Games table and category routes verified successfully.\n";
