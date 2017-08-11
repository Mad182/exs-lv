<?php
/**
 *  Kaudze ar SQL pieprasījumiem, kas  atgriež statistiku par
 *  norādīto gadu (pēc noklusējuma - iepriekšējo).
 *
 *  Autors: Edgars, 2017.g. janvāris
 */

/*
|--------------------------------------------------------------------------
|   Saistīti raksti/miniblogi (ļoti iespējams, ka kaut kas ir izlaists!)
|--------------------------------------------------------------------------
*/

/*
NOMINĀCIJAS
	2014. gads
		https://exs.lv/lazy-girls/forum/2gbvv
		https://exs.lv/read/2014-gada-nominacijas
	2013. gads
		https://exs.lv/read/exs-lv-gada-nominacijas-2013-rezultati
	2012. gads
		https://exs.lv/read/2012-gada-exs-lv-nominacijas-2

STATISTIKA
    2016. gads
        https://exs.lv/read/exs-lv-2016-gads-skaitlos
	2015. gads
        https://exs.lv/read/exs-2015-gads-skaitlos
		https://exs.lv/lazy-girls/forum/2q8x6
		Styrnuča ieteikumi
		https://exs.lv/lazy-girls/forum/2ps0v#m4580088
	2014. gads
		Apkopota statistika
		https://exs.lv/lazy-girls/forum/2gt5g
	2013. gads
		Viesty infografiks
		https://exs.lv/read/exs-lv-2013-gada-infografiks
*/

/*
|--------------------------------------------------------------------------
|   Iestatījumi.
|--------------------------------------------------------------------------
*/
 
// jābūt norādītam parent failā
if (!isset($include_check)) die('Savaldi savus zirgus!');

// pieprasījumi ir daudz un prasīgi, tāpēc neļausim tos izpildīt
// kuram katram; (1 - Madars, 115 - Edgars)
if ((int)$auth->id !== 1 && (int)$auth->id !== 115) {
    redirect('/');
    exit;
}

ini_set('max_execution_time', 300);
ini_set('memory_limit', '256M');

// iepriekšējais gads pēc noklusējuma
$year = (isset($_GET['var2'])) ? (int)$_GET['var2'] : (int) date('Y') - 1;
if ($year < 2010 || $year > (int) date('Y')) {
    $year = (int) date('Y') - 1;
}

$year_start = ($year - 1).'-12-31 23:59:59';
$year_end = ($year + 1).'-01-01 00:00:00';

// visu apakšprojektu musaru sadaļas
$bin_categories = '6, 244, 1133, 1904, 1972';
$lang_main = '0, 1';
$lang_exs = 1;
$lang_lol = 7;
$lang_rs = 9;
$lang_codinglv = 3;

$id_movie_cat = 80; // filmu sadaļas id main projektā
$id_games_cat = 81; // spēļu sadaļas id main projektā
$id_music_cat = 323; // mūzikas sadaļas id main projektā
$id_history_cat = 565; // vēstures sadaļas id main projektā

/*
|--------------------------------------------------------------------------
|   Funkcijas.
|--------------------------------------------------------------------------
*/

/**
 *  SQL pieprasījuma rezultātu atgriež noformētas HTML tabulas veidā.
 */
function sqlToTable($query_data) {
    if (empty($query_data)) return '<br>&mdash;';
    
    $html = '<table style="border:1px solid #dde;border-collapse:collapse;margin:10px 0 0 15px;font-family:arial;">';
    $html .= '<tr>';
    
    // tabulas heading
    $th_style = 'text-align:left;font-size:12px;background:#e7e7fd;padding:4px;color:#252627';
    $html .= '<td style="'.$th_style.';text-align:center">#</td>';
    foreach ($query_data[0] as $field_name => $value) {
        $html .= '<td style="'.$th_style.'">';
        $html .= $field_name.'</td>';
    }
    $html .= '</tr>';
    
    // pārējās tabulas rindas
    $td_style = 'font-size:12px;color:#555;padding:2px 4px;border:1px solid #dde;padding:4px 8px;';
    $row_counter = 1;
    foreach ($query_data as $row) {
        $html .= '<tr>';
        $html .= '<td style="'.$td_style.'">'.($row_counter++).'</td>';
        foreach ($row as $field_name => $value) {
            $html .= '<td style="'.$td_style.'">';
            $html .= htmlspecialchars($value).'</td>';
        }
        $html .= '</tr>';
    }
    $html .= '</table>';
    
    return $html;
}

/*
|--------------------------------------------------------------------------
|   Masīvs ar statistikas pieprasījumiem.
|--------------------------------------------------------------------------
*/

// types: count, table
$arr_stats = [

    /*-------------------------------------------------------------
    // Raksti (-blograksti, -foruma ieraksti) un to komentāri
    //------------------------------------------------------------*/
    'Raksti (-blogi, -forumi)' => [
    
        // rakstu skaits
        // --
        ['about' => 'Rakstu skaits main.exs',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `pages`
            JOIN `cat` ON `pages`.`category` = `cat`.`id` WHERE
            `pages`.`category` NOT IN('.$bin_categories.') AND
            `pages`.`date` > \''.$year_start.'\' AND
            `pages`.`date` < \''.$year_end.'\' AND
            `pages`.`lang` IN('.$lang_main.') AND
            `cat`.`isblog` = 0 AND
            `cat`.`isforum` = 0'],
          
        // --
        ['about' => 'Rakstu skaits main.exs filmu sadaļā',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `pages` WHERE
            `pages`.`category` = '.$id_movie_cat.' AND
            `pages`.`date` > \''.$year_start.'\' AND
            `pages`.`date` < \''.$year_end.'\''],
            
        // --
        ['about' => 'Rakstu skaits main.exs spēļu sadaļā',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `pages` WHERE
            `pages`.`category` = '.$id_games_cat.' AND
            `pages`.`date` > \''.$year_start.'\' AND
            `pages`.`date` < \''.$year_end.'\''],
            
        // --
        ['about' => 'Rakstu skaits main.exs mūzikas sadaļā',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `pages` WHERE
            `pages`.`category` = '.$id_music_cat.' AND
            `pages`.`date` > \''.$year_start.'\' AND
            `pages`.`date` < \''.$year_end.'\''],
            
        // --
        ['about' => 'Rakstu skaits main.exs vēstures sadaļā',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `pages` WHERE
            `pages`.`category` = '.$id_history_cat.' AND
            `pages`.`date` > \''.$year_start.'\' AND
            `pages`.`date` < \''.$year_end.'\''],
            
        // --
        ['about' => 'Rakstu skaits lol.exs',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `pages`
            JOIN `cat` ON `pages`.`category` = `cat`.`id` WHERE
            `pages`.`category` NOT IN('.$bin_categories.') AND
            `pages`.`date` > \''.$year_start.'\' AND
            `pages`.`date` < \''.$year_end.'\' AND
            `pages`.`lang` = '.$lang_lol.' AND
            `cat`.`isblog` = 0 AND
            `cat`.`isforum` = 0'],
            
        // --
        ['about' => 'Rakstu skaits rs.exs',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `pages`
            JOIN `cat` ON `pages`.`category` = `cat`.`id` WHERE
            `pages`.`category` NOT IN('.$bin_categories.') AND
            `pages`.`date` > \''.$year_start.'\' AND
            `pages`.`date` < \''.$year_end.'\' AND
            `pages`.`lang` = '.$lang_rs.' AND
            `cat`.`isblog` = 0 AND
            `cat`.`isforum` = 0'],
        
        // aktīvākie rakstu autori
        // --
        ['about' => 'Aktīvākie main.exs rakstu autori',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `articles`
            FROM `pages` JOIN `cat` ON `pages`.`category` = `cat`.`id`
            JOIN `users` ON `pages`.`author` = `users`.`id` WHERE
            `pages`.`category` NOT IN('.$bin_categories.') AND
            `pages`.`date` > \''.$year_start.'\' AND
            `pages`.`date` < \''.$year_end.'\' AND
            `pages`.`lang` IN('.$lang_main.') AND
            `cat`.`isblog` = 0 AND
            `cat`.`isforum` = 0
            GROUP BY `pages`.`author` ORDER BY count(*) DESC LIMIT 5'],
          
        // --
        ['about' => 'Aktīvākie lol.exs rakstu autori',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `articles`
            FROM `pages` JOIN `cat` ON `pages`.`category` = `cat`.`id`
            JOIN `users` ON `pages`.`author` = `users`.`id` WHERE
            `pages`.`category` NOT IN('.$bin_categories.') AND
            `pages`.`date` > \''.$year_start.'\' AND
            `pages`.`date` < \''.$year_end.'\' AND
            `pages`.`lang` = '.$lang_lol.' AND
            `cat`.`isblog` = 0 AND
            `cat`.`isforum` = 0
            GROUP BY `pages`.`author` ORDER BY count(*) DESC LIMIT 5'],
          
        // --
        ['about' => 'Aktīvākie rs.exs rakstu autori',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `articles`
            FROM `pages` JOIN `cat` ON `pages`.`category` = `cat`.`id`
            JOIN `users` ON `pages`.`author` = `users`.`id` WHERE
            `pages`.`category` NOT IN('.$bin_categories.') AND
            `pages`.`date` > \''.$year_start.'\' AND
            `pages`.`date` < \''.$year_end.'\' AND
            `pages`.`lang` = '.$lang_rs.' AND
            `cat`.`isblog` = 0 AND
            `cat`.`isforum` = 0
            GROUP BY `pages`.`author` ORDER BY count(*) DESC LIMIT 5'],
          
        // komentāru skaits rakstos
        // --
        ['about' => 'Rakstu komentāri main.exs',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `comments`
            JOIN `pages` ON `comments`.`pid` = `pages`.`id`
            JOIN `cat` ON `pages`.`category` = `cat`.`id` WHERE
            `comments`.`date` > \''.$year_start.'\' AND
            `comments`.`date` < \''.$year_end.'\' AND
            `comments`.`removed` = 0 AND
            `pages`.`category` NOT IN('.$bin_categories.') AND
            `pages`.`lang` IN('.$lang_main.') AND
            `cat`.`isforum` = 0 AND
            `cat`.`isblog` = 0'],
            
        // --
        ['about' => 'Rakstu komentāri lol.exs',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `comments`
            JOIN `pages` ON `comments`.`pid` = `pages`.`id`
            JOIN `cat` ON `pages`.`category` = `cat`.`id` WHERE
            `comments`.`date` > \''.$year_start.'\' AND
            `comments`.`date` < \''.$year_end.'\' AND
            `comments`.`removed` = 0 AND
            `pages`.`category` NOT IN('.$bin_categories.') AND
            `pages`.`lang` = '.$lang_lol.' AND
            `cat`.`isforum` = 0 AND
            `cat`.`isblog` = 0'],
            
        // --
        ['about' => 'Rakstu komentāri rs.exs',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `comments`
            JOIN `pages` ON `comments`.`pid` = `pages`.`id`
            JOIN `cat` ON `pages`.`category` = `cat`.`id` WHERE
            `comments`.`date` > \''.$year_start.'\' AND
            `comments`.`date` < \''.$year_end.'\' AND
            `comments`.`removed` = 0 AND
            `pages`.`category` NOT IN('.$bin_categories.') AND
            `pages`.`lang` = '.$lang_rs.' AND
            `cat`.`isforum` = 0 AND
            `cat`.`isblog` = 0'],
            
        // aktīvākie rakstu komentētāji
        // --
        ['about' => 'Aktīvākie main.exs rakstu komentētāji',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `comments`
            FROM `comments` JOIN `pages` ON `comments`.`pid` = `pages`.`id`
            JOIN `users` ON `comments`.`author` = `users`.`id`
            JOIN `cat` ON `pages`.`category` = `cat`.`id` WHERE
            `comments`.`date` > \''.$year_start.'\' AND
            `comments`.`date` < \''.$year_end.'\' AND
            `comments`.`removed` = 0 AND
            `pages`.`category` NOT IN('.$bin_categories.') AND
            `pages`.`lang` IN('.$lang_main.') AND
            `cat`.`isforum` = 0 AND
            `cat`.`isblog` = 0
            GROUP BY `comments`.`author` ORDER BY count(*) DESC LIMIT 5'],
            
        // --
        ['about' => 'Aktīvākie lol.exs rakstu komentētāji',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `comments`
            FROM `comments` JOIN `pages` ON `comments`.`pid` = `pages`.`id`
            JOIN `users` ON `comments`.`author` = `users`.`id`
            JOIN `cat` ON `pages`.`category` = `cat`.`id` WHERE
            `comments`.`date` > \''.$year_start.'\' AND
            `comments`.`date` < \''.$year_end.'\' AND
            `comments`.`removed` = 0 AND
            `pages`.`category` NOT IN('.$bin_categories.') AND
            `pages`.`lang` = '.$lang_lol.' AND
            `cat`.`isforum` = 0 AND
            `cat`.`isblog` = 0
            GROUP BY `comments`.`author` ORDER BY count(*) DESC LIMIT 5'],
            
        // --
        ['about' => 'Aktīvākie rs.exs rakstu komentētāji',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `comments`
            FROM `comments` JOIN `pages` ON `comments`.`pid` = `pages`.`id`
            JOIN `users` ON `comments`.`author` = `users`.`id`
            JOIN `cat` ON `pages`.`category` = `cat`.`id` WHERE
            `comments`.`date` > \''.$year_start.'\' AND
            `comments`.`date` < \''.$year_end.'\' AND
            `comments`.`removed` = 0 AND
            `pages`.`category` NOT IN('.$bin_categories.') AND
            `pages`.`lang` = '.$lang_rs.' AND
            `cat`.`isforum` = 0 AND
            `cat`.`isblog` = 0
            GROUP BY `comments`.`author` ORDER BY count(*) DESC LIMIT 5']
    ],
    
    /*-------------------------------------------------------------
    // Lietotāju personīgie blogi
    //------------------------------------------------------------*/
    'Blogi' => [
        
        // blogu skaits
        // --
        ['about' => 'Blogu skaits main.exs',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `pages`
            JOIN `cat` ON `pages`.`category` = `cat`.`id` WHERE
            `pages`.`date` > \''.$year_start.'\' AND
            `pages`.`date` < \''.$year_end.'\' AND
            `pages`.`lang` IN('.$lang_main.') AND
            `cat`.`isblog` > 0'],
            
        // --
        ['about' => 'Blogu skaits rs.exs',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `pages`
            JOIN `cat` ON `pages`.`category` = `cat`.`id` WHERE
            `pages`.`date` > \''.$year_start.'\' AND
            `pages`.`date` < \''.$year_end.'\' AND
            `pages`.`lang` = '.$lang_rs.' AND
            `cat`.`isblog` > 0'],
            
        // aktīvākie blogeri
        // --
        ['about' => 'Aktīvākie main.exs blogeri',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `new blogs` FROM `pages`
            JOIN `cat` ON `pages`.`category` = `cat`.`id`
            JOIN `users` ON `pages`.`author` = `users`.`id` WHERE
            `pages`.`date` > \''.$year_start.'\' AND
            `pages`.`date` < \''.$year_end.'\' AND
            `pages`.`lang` IN('.$lang_main.') AND
            `cat`.`isblog` > 0
            GROUP BY `pages`.`author` ORDER BY count(*) DESC LIMIT 5'],
            
        // --
        ['about' => 'Aktīvākie rs.exs blogeri',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `new blogs` FROM `pages`
            JOIN `cat` ON `pages`.`category` = `cat`.`id`
            JOIN `users` ON `pages`.`author` = `users`.`id` WHERE
            `pages`.`date` > \''.$year_start.'\' AND
            `pages`.`date` < \''.$year_end.'\' AND
            `pages`.`lang` = '.$lang_rs.' AND
            `cat`.`isblog` > 0
            GROUP BY `pages`.`author` ORDER BY count(*) DESC LIMIT 5'],
        
        // komentāru skaits blogos
        // --
        ['about' => 'Blogu komentāri main.exs',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `comments`
            JOIN `pages` ON `comments`.`pid` = `pages`.`id`
            JOIN `cat` ON `pages`.`category` = `cat`.`id` WHERE
            `comments`.`date` > \''.$year_start.'\' AND
            `comments`.`date` < \''.$year_end.'\' AND
            `comments`.`removed` = 0 AND
            `pages`.`lang` IN('.$lang_main.') AND
            `cat`.`isblog` > 0'],
            
        // --
        ['about' => 'Blogu komentāri rs.exs',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `comments`
            JOIN `pages` ON `comments`.`pid` = `pages`.`id`
            JOIN `cat` ON `pages`.`category` = `cat`.`id` WHERE
            `comments`.`date` > \''.$year_start.'\' AND
            `comments`.`date` < \''.$year_end.'\' AND
            `comments`.`removed` = 0 AND
            `pages`.`lang` = '.$lang_rs.' AND
            `cat`.`isblog` > 0'],
            
        // aktīvākie blogu komentētāji
        // --
        ['about' => 'Aktīvākie main.exs blogu komentētāji',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `comments`
            FROM `comments` JOIN `pages` ON `comments`.`pid` = `pages`.`id`
            JOIN `users` ON `comments`.`author` = `users`.`id`
            JOIN `cat` ON `pages`.`category` = `cat`.`id` WHERE
            `comments`.`date` > \''.$year_start.'\' AND
            `comments`.`date` < \''.$year_end.'\' AND
            `comments`.`removed` = 0 AND
            `pages`.`lang` IN('.$lang_main.') AND
            `cat`.`isblog` > 0
            GROUP BY `comments`.`author` ORDER BY count(*) DESC LIMIT 5'],
            
        // --
        ['about' => 'Aktīvākie rs.exs blogu komentētāji',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `comments`
            FROM `comments` JOIN `pages` ON `comments`.`pid` = `pages`.`id`
            JOIN `users` ON `comments`.`author` = `users`.`id`
            JOIN `cat` ON `pages`.`category` = `cat`.`id` WHERE
            `comments`.`date` > \''.$year_start.'\' AND
            `comments`.`date` < \''.$year_end.'\' AND
            `comments`.`removed` = 0 AND
            `pages`.`lang` = '.$lang_rs.' AND
            `cat`.`isblog` > 0
            GROUP BY `comments`.`author` ORDER BY count(*) DESC LIMIT 5']
    ],
    
    /*-------------------------------------------------------------
    // Forumi
    //------------------------------------------------------------*/
    'Forumi' => [
        
        // foruma tēmu skaits
        // --
        ['about' => 'Foruma tēmas main.exs',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `pages`
            JOIN `cat` ON `pages`.`category` = `cat`.`id` WHERE
            `pages`.`date` > \''.$year_start.'\' AND
            `pages`.`date` < \''.$year_end.'\' AND
            `pages`.`lang` IN('.$lang_main.') AND
            `cat`.`isforum` = 1'],
            
        // --
        ['about' => 'Foruma tēmas lol.exs',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `pages`
            JOIN `cat` ON `pages`.`category` = `cat`.`id` WHERE
            `pages`.`date` > \''.$year_start.'\' AND
            `pages`.`date` < \''.$year_end.'\' AND
            `pages`.`lang` = '.$lang_lol.' AND
            `cat`.`isforum` = 1'],
            
        // --
        ['about' => 'Foruma tēmas rs.exs',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `pages`
            JOIN `cat` ON `pages`.`category` = `cat`.`id` WHERE
            `pages`.`date` > \''.$year_start.'\' AND
            `pages`.`date` < \''.$year_end.'\' AND
            `pages`.`lang` = '.$lang_rs.' AND
            `cat`.`isforum` = 1'],
            
        // aktīvākie foruma tēmu veidotāji
        // --
        ['about' => 'Aktīvākie foruma tēmu veidotāji main.exs',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `topics`
            FROM `pages` JOIN `cat` ON `pages`.`category` = `cat`.`id`
            JOIN `users` ON `pages`.`author` = `users`.`id` WHERE
            `pages`.`date` > \''.$year_start.'\' AND
            `pages`.`date` < \''.$year_end.'\' AND
            `pages`.`lang` IN('.$lang_main.') AND
            `cat`.`isforum` = 1
            GROUP BY `pages`.`author` ORDER BY count(*) DESC LIMIT 5'],
            
        // --
        ['about' => 'Aktīvākie foruma tēmu veidotāji lol.exs',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `topics`
            FROM `pages` JOIN `cat` ON `pages`.`category` = `cat`.`id`
            JOIN `users` ON `pages`.`author` = `users`.`id` WHERE
            `pages`.`date` > \''.$year_start.'\' AND
            `pages`.`date` < \''.$year_end.'\' AND            
            `pages`.`lang` = '.$lang_lol.' AND
            `cat`.`isforum` = 1
            GROUP BY `pages`.`author` ORDER BY count(*) DESC LIMIT 5'],
            
        // --
        ['about' => 'Aktīvākie foruma tēmu veidotāji rs.exs',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `topics`
            FROM `pages` JOIN `cat` ON `pages`.`category` = `cat`.`id`
            JOIN `users` ON `pages`.`author` = `users`.`id` WHERE
            `pages`.`date` > \''.$year_start.'\' AND
            `pages`.`date` < \''.$year_end.'\' AND            
            `pages`.`lang` = '.$lang_rs.' AND
            `cat`.`isforum` = 1
            GROUP BY `pages`.`author` ORDER BY count(*) DESC LIMIT 5'],
            
        // komentāru skaits
        // --
        ['about' => 'Foruma tēmu komentāri main.exs',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `comments`
            JOIN `pages` ON `comments`.`pid` = `pages`.`id`
            JOIN `cat` ON `pages`.`category` = `cat`.`id` WHERE
            `comments`.`date` > \''.$year_start.'\' AND
            `comments`.`date` < \''.$year_end.'\' AND
            `comments`.`removed` = 0 AND
            `pages`.`lang` IN('.$lang_main.') AND
            `cat`.`isforum` = 1'],
            
        // --
        ['about' => 'Foruma tēmu komentāri lol.exs',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `comments`
            JOIN `pages` ON `comments`.`pid` = `pages`.`id`
            JOIN `cat` ON `pages`.`category` = `cat`.`id` WHERE
            `comments`.`date` > \''.$year_start.'\' AND
            `comments`.`date` < \''.$year_end.'\' AND
            `comments`.`removed` = 0 AND
            `pages`.`lang` = '.$lang_lol.' AND
            `cat`.`isforum` = 1'],
            
        // --
        ['about' => 'Foruma tēmu komentāri rs.exs',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `comments`
            JOIN `pages` ON `comments`.`pid` = `pages`.`id`
            JOIN `cat` ON `pages`.`category` = `cat`.`id` WHERE
            `comments`.`date` > \''.$year_start.'\' AND
            `comments`.`date` < \''.$year_end.'\' AND
            `comments`.`removed` = 0 AND
            `pages`.`lang` = '.$lang_rs.' AND
            `cat`.`isforum` = 1'],
            
        // aktīvākie foruma tēmu komentētāji
        // --
        ['about' => 'Aktīvākie foruma tēmu komentētāji main.exs',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `comments`
            FROM `comments` JOIN `users` ON `comments`.`author` = `users`.`id`
            JOIN `pages` ON `comments`.`pid` = `pages`.`id`
            JOIN `cat` ON `pages`.`category` = `cat`.`id` WHERE
            `comments`.`date` > \''.$year_start.'\' AND
            `comments`.`date` < \''.$year_end.'\' AND
            `comments`.`removed` = 0 AND
            `pages`.`lang` IN('.$lang_main.') AND
            `cat`.`isforum` = 1
            GROUP BY `comments`.`author` ORDER BY count(*) DESC LIMIT 5'],
            
        // --
        ['about' => 'Aktīvākie foruma tēmu komentētāji lol.exs',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `comments`
            FROM `comments` JOIN `users` ON `comments`.`author` = `users`.`id`
            JOIN `pages` ON `comments`.`pid` = `pages`.`id`
            JOIN `cat` ON `pages`.`category` = `cat`.`id` WHERE
            `comments`.`date` > \''.$year_start.'\' AND
            `comments`.`date` < \''.$year_end.'\' AND
            `comments`.`removed` = 0 AND
            `pages`.`lang` = '.$lang_lol.' AND
            `cat`.`isforum` = 1
            GROUP BY `comments`.`author` ORDER BY count(*) DESC LIMIT 5'],
            
        // --
        ['about' => 'Aktīvākie foruma tēmu komentētāji rs.exs',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `comments`
            FROM `comments` JOIN `users` ON `comments`.`author` = `users`.`id`
            JOIN `pages` ON `comments`.`pid` = `pages`.`id`
            JOIN `cat` ON `pages`.`category` = `cat`.`id` WHERE
            `comments`.`date` > \''.$year_start.'\' AND
            `comments`.`date` < \''.$year_end.'\' AND
            `comments`.`removed` = 0 AND
            `pages`.`lang` = '.$lang_rs.' AND
            `cat`.`isforum` = 1
            GROUP BY `comments`.`author` ORDER BY count(*) DESC LIMIT 5']
    ],

    /*-------------------------------------------------------------
    // Galerijas, attēli, attēlu komentāri
    //------------------------------------------------------------*/
    'Galerijas' => [
        
        // attēlu skaits galerijās
        // --
        ['about' => 'Attēlu skaits galerijās main.exs',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `images` WHERE
            `images`.`date` > \''.$year_start.'\' AND
            `images`.`date` < \''.$year_end.'\' AND
            `images`.`lang` = '.$lang_exs],
            
        // --
        ['about' => 'Attēlu skaits galerijās lol.exs',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `images` WHERE
            `images`.`date` > \''.$year_start.'\' AND
            `images`.`date` < \''.$year_end.'\' AND
            `images`.`lang` = '.$lang_lol],
            
        // --
        ['about' => 'Attēlu skaits galerijās rs.exs',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `images` WHERE
            `images`.`date` > \''.$year_start.'\' AND
            `images`.`date` < \''.$year_end.'\' AND
            `images`.`lang` = '.$lang_rs],
            
        // aktīvākie attēlu pievienotāji
        // --
        ['about' => 'Aktīvākie attēlu pievienotāji main.exs',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `uploads`
            FROM `images` JOIN `users` ON `images`.`uid` = `users`.`id` WHERE
            `images`.`date` > \''.$year_start.'\' AND
            `images`.`date` < \''.$year_end.'\' AND
            `images`.`lang` = '.$lang_exs.'
            GROUP BY `images`.`uid` ORDER BY count(*) DESC LIMIT 5'],
            
        // --
        ['about' => 'Aktīvākie attēlu pievienotāji lol.exs',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `uploads`
            FROM `images` JOIN `users` ON `images`.`uid` = `users`.`id` WHERE
            `images`.`date` > \''.$year_start.'\' AND
            `images`.`date` < \''.$year_end.'\' AND
            `images`.`lang` = '.$lang_lol.'
            GROUP BY `images`.`uid` ORDER BY count(*) DESC LIMIT 5'],
            
        // --
        ['about' => 'Aktīvākie attēlu pievienotāji rs.exs',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `uploads`
            FROM `images` JOIN `users` ON `images`.`uid` = `users`.`id` WHERE
            `images`.`date` > \''.$year_start.'\' AND
            `images`.`date` < \''.$year_end.'\' AND
            `images`.`lang` = '.$lang_rs.'
            GROUP BY `images`.`uid` ORDER BY count(*) DESC LIMIT 5'],

        // komentāru skaits pie attēliem
        // --
        ['about' => 'Attēlu komentāru skaits galerijās main.exs',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `galcom`
            JOIN `images` ON `galcom`.`bid` = `images`.`id` WHERE
            `galcom`.`date` > \''.$year_start.'\' AND
            `galcom`.`date` < \''.$year_end.'\' AND
            `galcom`.`removed` = 0 AND
            `images`.`lang` = '.$lang_exs],
            
        // --
        ['about' => 'Attēlu komentāru skaits galerijās lol.exs',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `galcom`
            JOIN `images` ON `galcom`.`bid` = `images`.`id` WHERE
            `galcom`.`date` > \''.$year_start.'\' AND
            `galcom`.`date` < \''.$year_end.'\' AND
            `galcom`.`removed` = 0 AND
            `images`.`lang` = '.$lang_lol],
            
        // --
        ['about' => 'Attēlu komentāru skaits galerijās rs.exs',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `galcom`
            JOIN `images` ON `galcom`.`bid` = `images`.`id` WHERE
            `galcom`.`date` > \''.$year_start.'\' AND
            `galcom`.`date` < \''.$year_end.'\' AND
            `galcom`.`removed` = 0 AND
            `images`.`lang` = '.$lang_rs],
            
        // aktīvākie attēlu komentētāji
        // --
        ['about' => 'Aktīvākie attēlu komentētāji main.exs',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `comments`
            FROM `galcom` JOIN `images` ON `galcom`.`bid` = `images`.`id`
            JOIN `users` ON `galcom`.`author` = `users`.`id` WHERE
            `galcom`.`date` > \''.$year_start.'\' AND
            `galcom`.`date` < \''.$year_end.'\' AND
            `galcom`.`removed` = 0 AND
            `images`.`lang` = '.$lang_exs.'
            GROUP BY `galcom`.`author` ORDER BY count(*) DESC LIMIT 5'],
            
        // --
        ['about' => 'Aktīvākie attēlu komentētāji lol.exs',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `comments`
            FROM `galcom` JOIN `images` ON `galcom`.`bid` = `images`.`id`
            JOIN `users` ON `galcom`.`author` = `users`.`id` WHERE
            `galcom`.`date` > \''.$year_start.'\' AND
            `galcom`.`date` < \''.$year_end.'\' AND
            `galcom`.`removed` = 0 AND
            `images`.`lang` = '.$lang_lol.'
            GROUP BY `galcom`.`author` ORDER BY count(*) DESC LIMIT 5'],
            
        // --
        ['about' => 'Aktīvākie attēlu komentētāji rs.exs',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `comments`
            FROM `galcom` JOIN `images` ON `galcom`.`bid` = `images`.`id`
            JOIN `users` ON `galcom`.`author` = `users`.`id` WHERE
            `galcom`.`date` > \''.$year_start.'\' AND
            `galcom`.`date` < \''.$year_end.'\' AND
            `galcom`.`removed` = 0 AND
            `images`.`lang` = '.$lang_rs.'
            GROUP BY `galcom`.`author` ORDER BY count(*) DESC LIMIT 5'],
            
        // --
        ['about' => 'Visvairāk komentētie attēli',
         'type' => 'table',
         'sql' => 'SELECT `images`.`id`, `images`.`url`, `images`.`thb`,
            `images`.`uid`, `users`.`nick` AS `username` FROM `images`
            JOIN `users` ON `images`.`uid` = `users`.`id` WHERE
            `images`.`date` > \''.$year_start.'\' AND
            `images`.`date` < \''.$year_end.'\' AND
            `images`.`lang` = '.$lang_exs.'
            ORDER BY `images`.`posts` DESC LIMIT 5']],

    /*-------------------------------------------------------------
    // Junk attēli, junk komentāri
    //------------------------------------------------------------*/
    'Junk' => [
    
        // --
        ['about' => 'junk attēlu skaits',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `junk` WHERE
            `junk`.`date` > \''.$year_start.'\' AND
            `junk`.`date` < \''.$year_end.'\' AND
            `junk`.`removed` = 0 AND
            `junk`.`lang` = '.$lang_exs],
            
        // --
        ['about' => 'Aktīvākie junk bilžu pievienotāji',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `junk_images`
            FROM `junk` LEFT JOIN `users` ON `junk`.`author` = `users`.`id` WHERE
            `junk`.`date` > \''.$year_start.'\' AND
            `junk`.`date` < \''.$year_end.'\' AND
            `junk`.`removed` = 0 AND
            `junk`.`lang` = '.$lang_exs.'
            GROUP BY `junk`.`author` ORDER BY count(*) DESC LIMIT 5'],
        
        // --
        ['about' => 'Komentāru skaits junk sadaļā',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `miniblog` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`type` = \'junk\''],
        
        // --
        ['about' => 'Aktīvākie junk bilžu komentētāji',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `comments`
            FROM `miniblog` JOIN `users` ON `miniblog`.`author` = `users`.`id` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`type` = \'junk\'
            GROUP BY `miniblog`.`author` ORDER BY count(*) DESC LIMIT 5'],
            
        // --
        ['about' => 'Visvairāk komentētie junk attēli',
         'type' => 'table',
         'sql' => 'SELECT `junk`.`id`, `junk`.`image`, `junk`.`thb`,
            `junk`.`author`, `users`.`nick` AS `username` FROM `junk`
            JOIN `users` ON `junk`.`author` = `users`.`id` WHERE
            `junk`.`date` > \''.$year_start.'\' AND
            `junk`.`date` < \''.$year_end.'\' AND
            `junk`.`lang` = '.$lang_exs.' AND
            `junk`.`removed` = 0
            ORDER BY `junk`.`posts` DESC LIMIT 5'],
    ],
    
    /*-------------------------------------------------------------
    // Pastkastīte, vēstules
    //------------------------------------------------------------*/
    'Vēstules' => [
    
        // --
        ['about' => 'Nosūtīto vēstuļu skaits',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `pm` WHERE
            `date` > \''.$year_start.'\' AND
            `date` < \''.$year_end.'\''],
            
        // --
        ['about' => 'Neizlasīto vēstuļu skaits',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `pm` WHERE
            `date` > \''.$year_start.'\' AND
            `date` < \''.$year_end.'\' AND
            `is_read` = 0'],
            
        // --
        ['about' => 'Visaktīvākie vēstuļu sūtītāji',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `msg_sent`
            FROM `pm` JOIN `users` ON `pm`.`from_uid` = `users`.`id` WHERE
            `pm`.`date` > \''.$year_start.'\' AND
            `pm`.`date` < \''.$year_end.'\'
            GROUP BY `pm`.`from_uid` ORDER BY count(*) DESC LIMIT 5'],
            
        // --
        ['about' => 'Visvairāk vēstuļu saņēmuši',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `msg_received`
            FROM `pm` JOIN `users` ON `pm`.`to_uid` = `users`.`id` WHERE
            `pm`.`date` > \''.$year_start.'\' AND
            `pm`.`date` < \''.$year_end.'\'
            GROUP BY `pm`.`to_uid` ORDER BY count(*) DESC LIMIT 5'],
            
        // --
        ['about' => 'Visvairāk neizlasīto vēstuļu',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `msg_unread`
            FROM `pm` JOIN `users` ON `pm`.`to_uid` = `users`.`id` WHERE
            `pm`.`date` > \''.$year_start.'\' AND
            `pm`.`date` < \''.$year_end.'\' AND
            `pm`.`is_read` = 0
            GROUP BY `pm`.`to_uid` ORDER BY count(*) DESC LIMIT 5']
    ],

    /*-------------------------------------------------------------
    // Miniblogi (arī grupās)
    //------------------------------------------------------------*/
    'Miniblogi (+grupas)' => [
    
        // miniblogi
        // --
        ['about' => 'Miniblogi (ārpus grupām, main.exs)',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `miniblog` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` IN('.$lang_main.') AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`parent` = 0 AND
            `miniblog`.`groupid` = 0'],
        // --
        ['about' => 'Miniblogi (grupās, main.exs)',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `miniblog` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` IN('.$lang_main.') AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`parent` = 0 AND
            `miniblog`.`groupid` != 0'],
        // --
        ['about' => 'Miniblogi (lol.exs)',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `miniblog` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_lol.' AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`parent` = 0'],
        // --
        ['about' => 'Miniblogi (ārpus grupām, rs.exs)',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `miniblog` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_rs.' AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`parent` = 0 AND
            `miniblog`.`groupid` = 0'],
        // --
        ['about' => 'Miniblogi (grupās, rs.exs)',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `miniblog` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_rs.' AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`parent` = 0 AND
            `miniblog`.`groupid` != 0'],
            
        // miniblogu komentāri
        // --
        ['about' => 'Miniblogu komentāri (ārpus grupām, main.exs)',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `miniblog` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` IN('.$lang_main.') AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`parent` != 0 AND
            `miniblog`.`groupid` = 0'],            
        // --
        ['about' => 'Miniblogu komentāri (grupās, main.exs)',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `miniblog` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` IN('.$lang_main.') AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`parent` != 0 AND
            `miniblog`.`groupid` != 0'],    
        // --
        ['about' => 'Miniblogu komentāri (lol.exs)',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `miniblog` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_lol.' AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`parent` != 0'],    
        // --
        ['about' => 'Miniblogu komentāri (ārpus grupām, rs.exs)',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `miniblog` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_rs.' AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`parent` != 0 AND
            `miniblog`.`groupid` = 0'],    
        // --
        ['about' => 'Miniblogu komentāri (grupās, rs.exs)',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `miniblog` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_rs.' AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`parent` != 0 AND
            `miniblog`.`groupid` != 0'],
            
        // aktīvākie jaunu miniblogu veidotāji
        // --
        ['about' => 'Aktīvākie miniblogu veidotāji (ārpus grupām, main.exs)',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `miniblogs`
            FROM `miniblog` JOIN `users` ON `miniblog`.`author` = `users`.`id` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` IN('.$lang_main.') AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`parent` = 0 AND
            `miniblog`.`groupid` = 0
            GROUP BY `miniblog`.`author` ORDER BY count(*) DESC LIMIT 5'],
        // --
        ['about' => 'Aktīvākie miniblogu veidotāji (grupās, main.exs)',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `miniblogs`
            FROM `miniblog` JOIN `users` ON `miniblog`.`author` = `users`.`id` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` IN('.$lang_main.') AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`parent` = 0 AND
            `miniblog`.`groupid` != 0
            GROUP BY `miniblog`.`author` ORDER BY count(*) DESC LIMIT 5'],    
        // --
        ['about' => 'Aktīvākie miniblogu veidotāji (lol.exs)',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `miniblogs`
            FROM `miniblog` JOIN `users` ON `miniblog`.`author` = `users`.`id` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_lol.' AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`parent` = 0
            GROUP BY `miniblog`.`author` ORDER BY count(*) DESC LIMIT 5'],    
        // --
        ['about' => 'Aktīvākie miniblogu veidotāji (ārpus grupām, rs.exs)',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `miniblogs`
            FROM `miniblog` JOIN `users` ON `miniblog`.`author` = `users`.`id` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_rs.' AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`parent` = 0 AND
            `miniblog`.`groupid` = 0
            GROUP BY `miniblog`.`author` ORDER BY count(*) DESC LIMIT 5'],    
        // --
        ['about' => 'Aktīvākie miniblogu veidotāji (grupās, rs.exs)',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `miniblogs`
            FROM `miniblog` JOIN `users` ON `miniblog`.`author` = `users`.`id` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_rs.' AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`parent` = 0 AND
            `miniblog`.`groupid` != 0
            GROUP BY `miniblog`.`author` ORDER BY count(*) DESC LIMIT 5'],
            
        // aktīvākie miniblogu komentētāji
        // --
        ['about' => 'Aktīvākie miniblogu komentētāji (ārpus grupām, main.exs)',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `comments`
            FROM `miniblog` JOIN `users` ON `miniblog`.`author` = `users`.`id` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` IN('.$lang_main.') AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`parent` != 0 AND
            `miniblog`.`groupid` = 0
            GROUP BY `miniblog`.`author` ORDER BY count(*) DESC LIMIT 5'],    
        // --
        ['about' => 'Aktīvākie miniblogu komentētāji (grupās, main.exs)',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `comments`
            FROM `miniblog` JOIN `users` ON `miniblog`.`author` = `users`.`id` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` IN('.$lang_main.') AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`parent` != 0 AND
            `miniblog`.`groupid` != 0
            GROUP BY `miniblog`.`author` ORDER BY count(*) DESC LIMIT 5'],    
        // --
        ['about' => 'Aktīvākie miniblogu komentētāji (lol.exs)',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `comments`
            FROM `miniblog` JOIN `users` ON `miniblog`.`author` = `users`.`id` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_lol.' AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`parent` != 0
            GROUP BY `miniblog`.`author` ORDER BY count(*) DESC LIMIT 5'],    
        // --
        ['about' => 'Aktīvākie miniblogu komentētāji (ārpus grupām, rs.exs)',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `comments`
            FROM `miniblog` JOIN `users` ON `miniblog`.`author` = `users`.`id` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_rs.' AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`parent` != 0 AND
            `miniblog`.`groupid` = 0
            GROUP BY `miniblog`.`author` ORDER BY count(*) DESC LIMIT 5'],    
        // --
        ['about' => 'Aktīvākie miniblogu komentētāji (grupās, rs.exs)',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `comments`
            FROM `miniblog` JOIN `users` ON `miniblog`.`author` = `users`.`id` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_rs.' AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`parent` != 0 AND
            `miniblog`.`groupid` != 0
            GROUP BY `miniblog`.`author` ORDER BY count(*) DESC LIMIT 5'],
            
        // avg rakstzīmju skaits
        // --
        ['about' => 'Vidējais rakstzīmju skaits miniblogos (main.exs, +grupas, +komentāri)',
         'type' => 'table',
         'sql' => 'SELECT avg(length(`miniblog`.`text`)) AS `average_length`
            FROM `miniblog` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` IN('.$lang_main.') AND
            `miniblog`.`type` = \'miniblog\''],
        // --
        ['about' => 'Vidējais rakstzīmju skaits miniblogos (lol.exs, +komentāri)',
         'type' => 'table',
         'sql' => 'SELECT avg(length(`miniblog`.`text`)) AS `average_length`
            FROM `miniblog` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_lol.' AND
            `miniblog`.`type` = \'miniblog\''],
        // --
        ['about' => 'Vidējais rakstzīmju skaits miniblogos (rs.exs, +grupas, +komentāri)',
         'type' => 'table',
         'sql' => 'SELECT avg(length(`miniblog`.`text`)) AS `average_length`
            FROM `miniblog` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_rs.' AND
            `miniblog`.`type` = \'miniblog\''],
            
        // visvairāk komentētie miniblogi
        // --
        ['about' => 'Visvairāk komentētie miniblogi (main.exs, +grupas)',
         'type' => 'table',
         'sql' => 'SELECT `miniblog`.`id`, `miniblog`.`groupid`, `miniblog`.`parent`,
            `miniblog`.`text`, `miniblog`.`posts`, `miniblog`.`author`, `users`.`nick` AS `username`
            FROM `miniblog` JOIN `users` ON `miniblog`.`author` = `users`.`id` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` IN('.$lang_main.') AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`parent` = 0
            ORDER BY `posts` DESC LIMIT 15'],
        // --
        ['about' => 'Visvairāk komentētie miniblogi (lol.exs)',
         'type' => 'table',
         'sql' => 'SELECT `miniblog`.`id`, `miniblog`.`groupid`, `miniblog`.`parent`,
            `miniblog`.`text`, `miniblog`.`posts`, `miniblog`.`author`, `users`.`nick` AS `username`
            FROM `miniblog` JOIN `users` ON `miniblog`.`author` = `users`.`id` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_lol.' AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`parent` = 0
            ORDER BY `posts` DESC LIMIT 10'],
        // --
        ['about' => 'Visvairāk komentētie miniblogi (rs.exs, +grupas)',
         'type' => 'table',
         'sql' => 'SELECT `miniblog`.`id`, `miniblog`.`groupid`, `miniblog`.`parent`,
            `miniblog`.`text`, `miniblog`.`posts`, `miniblog`.`author`, `users`.`nick` AS `username`
            FROM `miniblog` JOIN `users` ON `miniblog`.`author` = `users`.`id` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_rs.' AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`parent` = 0
            ORDER BY `posts` DESC LIMIT 10'],
            
        // aktīvākie lietotāji lapā (miniblogu + komentāru ziņā)
        // --
        ['about' => 'Visaktīvākie miniblogotāji (main.exs, +grupas, +komentāri)',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `comments`
            FROM `miniblog` JOIN `users` ON `miniblog`.`author` = `users`.`id` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` IN('.$lang_main.') AND
            `miniblog`.`type` = \'miniblog\'
            GROUP BY `miniblog`.`author` ORDER BY count(*) DESC LIMIT 5'],
        // --
        ['about' => 'Visaktīvākie miniblogotāji (lol.exs, +komentāri)',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `comments`
            FROM `miniblog` JOIN `users` ON `miniblog`.`author` = `users`.`id` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_lol.' AND
            `miniblog`.`type` = \'miniblog\'
            GROUP BY `miniblog`.`author` ORDER BY count(*) DESC LIMIT 5'],
        // --
        ['about' => 'Visaktīvākie miniblogotāji (rs.exs, +grupas, +komentāri)',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `comments`
            FROM `miniblog` JOIN `users` ON `miniblog`.`author` = `users`.`id` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_rs.' AND
            `miniblog`.`type` = \'miniblog\'
            GROUP BY `miniblog`.`author` ORDER BY count(*) DESC LIMIT 5']
    ],
    
    /*-------------------------------------------------------------
    // Grupas 
    //------------------------------------------------------------*/
    'Grupas' => [
    
        // --
        ['about' => 'Jaunas domubiedru grupas',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `clans` WHERE
            `clans`.`date_created` > \''.strtotime($year_start).'\' AND
            `clans`.`date_created` < \''.strtotime($year_end).'\''],
            
        // --
        ['about' => 'Populārākās no jaunajām grupām lietotāju skaita ziņā',
         'type' => 'table',
         'sql' => 'SELECT `clans`.`title`, count(*) AS `clan_members` FROM `clans`
            JOIN `clans_members` ON `clans`.`id` = `clans_members`.`clan` WHERE
            `clans`.`date_created` > \''.strtotime($year_start).'\' AND
            `clans`.`date_created` < \''.strtotime($year_end).'\' AND
            `clans_members`.`date_added` > \''.strtotime($year_start).'\' AND
            `clans_members`.`date_added` < \''.strtotime($year_end).'\' AND
            `clans_members`.`approve` = 1
            GROUP BY `clans_members`.`clan` ORDER BY count(*) DESC LIMIT 5'],
            
        // --
        ['about' => 'Populārākās no jaunajām grupām miniblogu (+komentāru) skaita ziņā',
         'type' => 'table',
         'sql' => 'SELECT `clans`.`title`, count(*) AS `posts` FROM `miniblog`
            JOIN `clans` ON `miniblog`.`groupid` = `clans`.`id` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`groupid` != 0 AND
            `clans`.`date_created` > \''.strtotime($year_start).'\' AND
            `clans`.`date_created` < \''.strtotime($year_end).'\'
            GROUP BY `miniblog`.`groupid` ORDER BY count(*) DESC LIMIT 10'],
            
        // --
        ['about' => 'Populārākās no grupām miniblogu (+komentāru) skaita ziņā',
         'type' => 'table',
         'sql' => 'SELECT `clans`.`title`, count(*) AS `posts` FROM `miniblog`
            JOIN `clans` ON `miniblog`.`groupid` = `clans`.`id` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`groupid` != 0
            GROUP BY `miniblog`.`groupid` ORDER BY count(*) DESC LIMIT 10'],
            
        // --
        ['about' => 'Visvairāk lietotāju pievienojās šīm grupām',
         'type' => 'table',
         'sql' => 'SELECT `clans`.`id`, `clans`.`title`, count(*) AS `members` FROM `clans`
            JOIN `clans_members` ON `clans`.`id` = `clans_members`.`clan` WHERE
            `clans_members`.`date_added` > \''.strtotime($year_start).'\' AND
            `clans_members`.`date_added` < \''.strtotime($year_end).'\' AND
            `clans_members`.`approve` = 1 AND
            `clans`.`date_created` > \''.strtotime($year_start).'\' AND
            `clans`.`date_created` < \''.strtotime($year_end).'\'
            GROUP BY `clans`.`id` ORDER BY count(*) DESC LIMIT 10']
    ],
    
    /*-------------------------------------------------------------
    // Vērtējumi: plusiņi/mīnusiņi
    //------------------------------------------------------------*/
    'Vērtējumi' => [
    
        // --
        ['about' => 'Mīnusotākie komentāri main.exs miniblogos',
         'type' => 'table',
         'sql' => 'SELECT `miniblog`.`id`, `miniblog`.`groupid`, `miniblog`.`parent`, 
         `miniblog`.`reply_to`, `miniblog`.`author`, `miniblog`.`text`,
         `miniblog`.`vote_value` FROM `miniblog` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_exs.' AND
            `miniblog`.`type` = \'miniblog\'
        ORDER BY `miniblog`.`vote_value` LIMIT 10'],

        // --
        ['about' => 'Mīnusotākie komentāri lol.exs miniblogos',
         'type' => 'table',
         'sql' => 'SELECT `miniblog`.`id`, `miniblog`.`groupid`, `miniblog`.`parent`, 
         `miniblog`.`reply_to`, `miniblog`.`author`, `miniblog`.`text`,
         `miniblog`.`vote_value` FROM `miniblog` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_lol.' AND
            `miniblog`.`type` = \'miniblog\'
        ORDER BY `miniblog`.`vote_value` LIMIT 10'],

        // --
        ['about' => 'Mīnusotākie komentāri rs.exs miniblogos',
         'type' => 'table',
         'sql' => 'SELECT `miniblog`.`id`, `miniblog`.`groupid`, `miniblog`.`parent`, 
         `miniblog`.`reply_to`, `miniblog`.`author`, `miniblog`.`text`,
         `miniblog`.`vote_value` FROM `miniblog` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_rs.' AND
            `miniblog`.`type` = \'miniblog\'
        ORDER BY `miniblog`.`vote_value` LIMIT 10'],
        
        // --
        ['about' => 'Plusotākie komentāri main.exs miniblogos',
         'type' => 'table',
         'sql' => 'SELECT `miniblog`.`id`, `miniblog`.`groupid`, `miniblog`.`parent`, 
         `miniblog`.`reply_to`, `miniblog`.`author`, `miniblog`.`text`,
         `miniblog`.`vote_value` FROM `miniblog` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_exs.' AND
            `miniblog`.`type` = \'miniblog\'
        ORDER BY `miniblog`.`vote_value` DESC LIMIT 10'],
        
        // --
        ['about' => 'Plusotākie komentāri lol.exs miniblogos',
         'type' => 'table',
         'sql' => 'SELECT `miniblog`.`id`, `miniblog`.`groupid`, `miniblog`.`parent`, 
         `miniblog`.`reply_to`, `miniblog`.`author`, `miniblog`.`text`,
         `miniblog`.`vote_value` FROM `miniblog` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_lol.' AND
            `miniblog`.`type` = \'miniblog\'
        ORDER BY `miniblog`.`vote_value` DESC LIMIT 10'],
        
        // --
        ['about' => 'Plusotākie komentāri rs.exs miniblogos',
         'type' => 'table',
         'sql' => 'SELECT `miniblog`.`id`, `miniblog`.`groupid`, `miniblog`.`parent`, 
         `miniblog`.`reply_to`, `miniblog`.`author`, `miniblog`.`text`,
         `miniblog`.`vote_value` FROM `miniblog` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_rs.' AND
            `miniblog`.`type` = \'miniblog\'
        ORDER BY `miniblog`.`vote_value` DESC LIMIT 10'],
        
        // --
        ['about' => 'Plusu summa (main.exs)',
         'type' => 'count',
         'sql' => 'SELECT SUM(`vote_value`) AS `pluses` FROM `miniblog` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_exs.' AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`vote_value` > 0'],
            
        // --
        ['about' => 'Mīnusu summa (main.exs)',
         'type' => 'count',
         'sql' => 'SELECT SUM(`vote_value`) AS `minuses` FROM `miniblog` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_exs.' AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`vote_value` < 0'],
            
        // --
        ['about' => 'Visu miniblogu (+komentāru, +grupās) vērtējumu (pozitīvu un negatīvu) summa',
         'type' => 'count',
         'sql' => 'SELECT SUM(`vote_value`) AS `total_value` FROM `miniblog` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_exs.' AND
            `miniblog`.`type` = \'miniblog\''],
            
        // --
        ['about' => 'Lietotāji ar vislielāko summu to pozitīvajiem vērtējumiem',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, SUM(`vote_value`) AS `pluses`
            FROM `miniblog` JOIN `users` ON `miniblog`.`author` = `users`.`id` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_exs.' AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`vote_value` > 0
            GROUP BY `miniblog`.`author` ORDER BY SUM(`vote_value`) DESC LIMIT 10'],
            
        // --
        ['about' => 'Lietotāji ar vislielāko summu to negatīvajiem vērtējumiem',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, SUM(`vote_value`) AS `minuses` FROM `miniblog`
            JOIN `users` ON `miniblog`.`author` = `users`.`id` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_exs.' AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`vote_value` < 0
            GROUP BY `miniblog`.`author` ORDER BY SUM(`vote_value`) ASC LIMIT 10'],
            
        // --
        ['about' => 'Lietotāji ar vislielāko vērtējumu summu',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`,
            SUM(`vote_value`) AS `total_value` FROM `miniblog`
            JOIN `users` ON `miniblog`.`author` = `users`.`id` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_exs.' AND
            `miniblog`.`type` = \'miniblog\'
            GROUP BY `miniblog`.`author` ORDER BY SUM(`vote_value`) DESC LIMIT 10']
    ],
    
    /*-------------------------------------------------------------
    // Sūdzības, sodi, brīdinājumi, liegumi
    //------------------------------------------------------------*/
    'Sūdzības, sodi, brīdinājumi, liegumi' => [
    
        // --
        ['about' => 'Iesniegtās sūdzības (main.exs)',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `reports` WHERE
            `reports`.`created_at` > \''.strtotime($year_start).'\' AND
            `reports`.`created_at` < \''.strtotime($year_end).'\' AND
            `reports`.`removed` = 0 AND
            `reports`.`site_id` = '.$lang_exs],
            
        // --
        ['about' => 'Visvairāk sūdzību iesnieguši (main.exs)',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `reports`
            FROM `reports` JOIN `users` ON `reports`.`created_by` = `users`.`id` WHERE
            `reports`.`created_at` > \''.strtotime($year_start).'\' AND
            `reports`.`created_at` < \''.strtotime($year_end).'\' AND
            `reports`.`removed` = 0 AND
            `reports`.`site_id` = '.$lang_exs.'
            GROUP BY `reports`.`created_by` ORDER BY count(*) DESC LIMIT 5'],
            
        // --
        ['about' => 'Visvairāk sūdzību izskatījuši (main.exs)',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `reports`
            FROM `reports` JOIN `users` ON `reports`.`deleted_by` = `users`.`id` WHERE
            `reports`.`created_at` > \''.strtotime($year_start).'\' AND
            `reports`.`created_at` < \''.strtotime($year_end).'\' AND
            `reports`.`removed` = 0 AND
            `reports`.`site_id` = '.$lang_exs.'
            GROUP BY `reports`.`deleted_by` ORDER BY count(*) DESC LIMIT 5'],
            
        // --
        ['about' => 'Atbrīvotās vārnas (main.exs)',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `warns` WHERE
            `warns`.`created` > \''.$year_start.'\' AND
            `warns`.`created` < \''.$year_end.'\' AND
            `warns`.`site_id` = '.$lang_exs],
            
        // --
        ['about' => 'Atbrīvotās vārnas (lol.exs)',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `warns` WHERE
            `warns`.`created` > \''.$year_start.'\' AND
            `warns`.`created` < \''.$year_end.'\' AND
            `warns`.`site_id` = '.$lang_lol],
            
        // --
        ['about' => 'Atbrīvotās vārnas (rs.exs)',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `warns` WHERE
            `warns`.`created` > \''.$year_start.'\' AND
            `warns`.`created` < \''.$year_end.'\' AND
            `warns`.`site_id` = '.$lang_rs],
            
        // --
        ['about' => 'Visvairāk vārnas notvēris (main.exs)',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `warns`
            FROM `warns` JOIN `users` ON `warns`.`user_id` = `users`.`id` WHERE
            `warns`.`created` > \''.$year_start.'\' AND
            `warns`.`created` < \''.$year_end.'\' AND
            `warns`.`site_id` = '.$lang_exs.'
            GROUP BY `warns`.`user_id` ORDER BY count(*) DESC LIMIT 5'],
            
        // --
        ['about' => 'Visvairāk vārnas notvēris (lol.exs)',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `warns`
            FROM `warns` JOIN `users` ON `warns`.`user_id` = `users`.`id` WHERE
            `warns`.`created` > \''.$year_start.'\' AND
            `warns`.`created` < \''.$year_end.'\' AND
            `warns`.`site_id` = '.$lang_lol.'
            GROUP BY `warns`.`user_id` ORDER BY count(*) DESC LIMIT 5'],
            
        // --
        ['about' => 'Visvairāk vārnas notvēris (rs.exs)',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `warns`
            FROM `warns` JOIN `users` ON `warns`.`user_id` = `users`.`id` WHERE
            `warns`.`created` > \''.$year_start.'\' AND
            `warns`.`created` < \''.$year_end.'\' AND
            `warns`.`site_id` = '.$lang_rs.'
            GROUP BY `warns`.`user_id` ORDER BY count(*) DESC LIMIT 5'],
            
        // --
        ['about' => 'Piešķirtie liegumi (globālie)',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `banned` WHERE
            `banned`.`time` > \''.strtotime($year_start).'\' AND
            `banned`.`time` < \''.strtotime($year_end).'\' AND
            `banned`.`lang` = 0'],
            
        // --
        ['about' => 'Piešķirtie liegumi (main.exs)',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `banned` WHERE
            `banned`.`time` > \''.strtotime($year_start).'\' AND
            `banned`.`time` < \''.strtotime($year_end).'\' AND
            `banned`.`lang` = '.$lang_exs],
            
        // --
        ['about' => 'Piešķirtie liegumi (lol.exs)',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `banned` WHERE
            `banned`.`time` > \''.strtotime($year_start).'\' AND
            `banned`.`time` < \''.strtotime($year_end).'\' AND
            `banned`.`lang` = '.$lang_lol],
            
        // --
        ['about' => 'Piešķirtie liegumi (rs.exs)',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `banned` WHERE
            `banned`.`time` > \''.strtotime($year_start).'\' AND
            `banned`.`time` < \''.strtotime($year_end).'\' AND
            `banned`.`lang` = '.$lang_rs],
            
        // --
        ['about' => 'Visvairāk liegumus nopelnījuši (pa visiem projektiem)',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `times`
            FROM `banned` JOIN `users` ON `banned`.`user_id` = `users`.`id` WHERE
            `banned`.`time` > \''.strtotime($year_start).'\' AND
            `banned`.`time` < \''.strtotime($year_end).'\'
            GROUP BY `banned`.`user_id` ORDER BY count(*) DESC LIMIT 10'],
            
        // --
        ['about' => 'Visaktīvākie liegumu piešķīrēji (pa visiem projektiem)',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `times`
            FROM `banned` JOIN `users` ON `banned`.`author` = `users`.`id` WHERE
            `banned`.`time` > \''.strtotime($year_start).'\' AND
            `banned`.`time` < \''.strtotime($year_end).'\'
            GROUP BY `banned`.`author` ORDER BY count(*) DESC LIMIT 10'],
            
        // --
        ['about' => 'Visaktīvākie vārnu atbrīvotāji (pa visiem projektiem)',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `times`
            FROM `warns` JOIN `users` ON `warns`.`created_by` = `users`.`id` WHERE
            `warns`.`created` > \''.$year_start.'\' AND
            `warns`.`created` < \''.$year_end.'\'
            GROUP BY `warns`.`created_by` ORDER BY count(*) DESC LIMIT 10'],
            
        // --
        ['about' => 'Visaktīvākie sūdzību izskatītāji (pa visiem projektiem)',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick`, count(*) AS `times` FROM `reports`
            JOIN `users` ON `reports`.`deleted_by` = `users`.`id` WHERE
            `reports`.`created_at` > \''.strtotime($year_start).'\' AND
            `reports`.`created_at` < \''.strtotime($year_end).'\' AND
            `reports`.`removed` = 0
            GROUP BY `reports`.`deleted_by` ORDER BY count(*) DESC LIMIT 10']
    ],

    /*-------------------------------------------------------------
    // coding.lv
    //------------------------------------------------------------*/
    'coding.lv' => [
    
        // --
        ['about' => 'Miniblogu skaits',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `miniblog` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_codinglv.' AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`parent` = 0'],
            
        // --
        ['about' => 'Aktīvākie jaunu miniblogu autori',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `miniblogs`
            FROM `miniblog` JOIN `users` ON `miniblog`.`author` = `users`.`id` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_codinglv.' AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`parent` = 0
            GROUP BY `miniblog`.`author` ORDER BY count(*) DESC LIMIT 5'],
            
        // --
        ['about' => 'Miniblogu komentāru skaits',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `miniblog` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_codinglv.' AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`parent` != 0'],
        
        // --
        ['about' => 'Aktīvākie miniblogu komentētāji',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `comments`
            FROM `miniblog` JOIN `users` ON `miniblog`.`author` = `users`.`id` WHERE
            `miniblog`.`date` > \''.$year_start.'\' AND
            `miniblog`.`date` < \''.$year_end.'\' AND
            `miniblog`.`removed` = 0 AND
            `miniblog`.`lang` = '.$lang_codinglv.' AND
            `miniblog`.`type` = \'miniblog\' AND
            `miniblog`.`parent` != 0
            GROUP BY `miniblog`.`author` ORDER BY count(*) DESC LIMIT 5'],
        
        // --
        ['about' => 'Raksti (tai skaitā tēmas forumā)',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `pages`
            JOIN `cat` ON `pages`.`category` = `cat`.`id` WHERE
            `pages`.`date` > \''.$year_start.'\' AND
            `pages`.`date` < \''.$year_end.'\' AND
            `pages`.`lang` = '.$lang_codinglv.' AND
            `cat`.`isblog` = 0'],
            
        // --
        ['about' => 'Aktīvākie rakstu (un foruma tēmu) komentētāji',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `comments`
            FROM `pages` JOIN `users` ON `pages`.`author` = `users`.`id`
            JOIN `cat` ON `pages`.`category` = `cat`.`id` WHERE
            `pages`.`category` NOT IN('.$bin_categories.') AND
            `pages`.`date` > \''.$year_start.'\' AND
            `pages`.`date` < \''.$year_end.'\' AND
            `pages`.`lang` = '.$lang_codinglv.' AND
            `cat`.`isblog` = 0
            GROUP BY `pages`.`author` ORDER BY count(*) DESC LIMIT 5']
    ],

    /*-------------------------------------------------------------
    // Dažādi
    //------------------------------------------------------------*/
    'Dažādi' => [
    
        // --
        ['about' => 'Reģistrējās jauni lietotāji',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `users` WHERE
            `users`.`date` > \''.$year_start.'\' AND
            `users`.`date` < \''.$year_end.'\' AND
            `users`.`deleted` = 0'],
            
        // --
        ['about' => 'Automātiski piešķirtas medaļas',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `autoawards` WHERE
            `autoawards`.`created` > \''.$year_start.'\' AND
            `autoawards`.`created` < \''.$year_end.'\''],
            
        // --
        ['about' => 'Visvairāk jaunas medaļas šajā gadā saņēmuši',
         'type' => 'table',
         'sql' => 'SELECT `users`.`nick` AS `username`, count(*) AS `award_count` FROM `autoawards`
            JOIN `users` ON `autoawards`.`user_id` = `users`.`id` WHERE
            `autoawards`.`created` > \''.$year_start.'\' AND
            `autoawards`.`created` < \''.$year_end.'\'
            GROUP BY `autoawards`.`user_id` ORDER BY count(*) DESC LIMIT 5'],
            
        // --
        ['about' => 'Izspēlēto desas partiju skaits',
         'type' => 'count',
         'sql' => 'SELECT count(*) FROM `desas` WHERE
            `desas`.`created` > \''.$year_start.'\' AND
            `desas`.`created` < \''.$year_end.'\''],
    ]
];

// adreses parametrā var norādīt atsevišķu SQL pieprasījumu
// grupu, lai izpildītu pieprasījumus tikai tai
$sql_group = -1; // pēc noklusējuma (-1) neizpildīs neko
if (isset($_GET['group']) && is_numeric($_GET['group'])) {
    $sql_group = (int) $_GET['group'];
    // 0 - izpilda uzreiz visām grupām
    if ($sql_group < 0 || $sql_group > count($arr_stats)) {
        $sql_group = -1; // neizpildīs neko
    }
}

/*
|--------------------------------------------------------------------------
|   Statistikas izvade lapā.
|--------------------------------------------------------------------------
*/

$sql_counter = 1;
$group_counter = 1;

echo '<br>Gads: '.$year.'<br>';

foreach ($arr_stats as $st_cat => $arr_queries) {
    
    echo '<a style="display:block;background:#f1f1f1;color:#5a5a5a;';
    echo 'padding:10px 20px;text-decoration:none;margin:10px 0;"';
    echo ' href="/'.$category->textid.'/detailed/'.$year.'?group='.$group_counter.'">';
    echo '#'.($group_counter++).' '.$st_cat.'</a>';
    
    // pārbaude, vai pieprasīts izpildīt šo sql pieprasījumu grupu
    if ($sql_group !== 0 && $sql_group !== $group_counter - 1) {
        continue;
    }
    
    foreach ($arr_queries as $single_sql) {
        
        echo '<p style="color:#424242">'.
             '<span style="color:#64add2">'.($sql_counter++).'.</span> '.
             $single_sql['about'].':&nbsp;';
        
        // sql query atgriež skaitli
        if ($single_sql['type'] === 'count') {
            
            $sql = $db->get_var($single_sql['sql']);
            echo '<i>'.$sql.'</i>';
            
        // sql query atgriež rindas ar datiem
        } else if ($single_sql['type'] === 'table') {
            
            $sql = $db->get_results($single_sql['sql']);
            if (!$sql) {
                echo '&mdash;';
            } else {
                echo sqlToTable($sql);
            }            
        }

        echo '</p>';
    }
}
exit;
