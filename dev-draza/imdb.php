<?php

if (PHP_SAPI !== 'cli') {
	echo 'CLI only!';
	exit;
}

echo 'imdb.php started' . "\n";

chdir(__DIR__);
ini_set('memory_limit', '256M');
error_reporting(0);
ini_set('display_errors', 'Off');
require('configdb.php');
require(CORE_PATH . '/includes/class.mdb.php');
require(CORE_PATH . '/includes/functions.core.php');
require(LIB_PATH . '/imdb-grabber/imdb.class.php');

$debug = true;

$lang = 1;

//mysql konekcija
$db = new mdb($username, $password, $database, $hostname);

function reverse_htmlentities($mixed) {
	$htmltable = get_html_translation_table(HTML_ENTITIES);
	foreach ($htmltable as $key => $value) {
		$mixed = ereg_replace(addslashes($value), $key, $mixed);
	}
	return $mixed;
}

//memcached konekcija
$m = new Memcache;
$m->connect($mc_host, $mc_port);

echo "imdb data collecting started...\n\n";

$movies = $db->get_results("SELECT `id`, `title` FROM `pages` WHERE `category` = 80 ORDER BY `edit_time` ASC ");

$i = 0;
$tot = 0;
foreach ($movies as $movie) {

	$oIMDB = new IMDB($movie->title);
	if ($oIMDB->isReady) {

		if ($year = $oIMDB->getYear()) {
			$db->query("UPDATE `movie_data` SET `year` = '$year' WHERE `page_id` = '$movie->id'");
		}

		if ($runtime = $oIMDB->getRuntime()) {
			$db->query("UPDATE `movie_data` SET `runtime` = '$runtime' WHERE `page_id` = '$movie->id'");
		}

		if ($rating = $oIMDB->getRating()) {
			$db->query("UPDATE `movie_data` SET `rating` = '$rating' WHERE `page_id` = '$movie->id'");
		}

		//pievieno žanrus
		if ($genres = $oIMDB->getGenre()) {
			$genres = explode('/', $genres);
			foreach ($genres as $genre) {
				$genre = sanitize(trim($genre));
				if (!empty($genre) && !$db->get_var("SELECT count(*) FROM `movie_genres` WHERE `page_id` = '$movie->id' AND `genre` = '$genre'")) {
					$db->query("INSERT INTO `movie_genres` (`page_id`, `genre`) VALUES ('$movie->id', '$genre')");
				}
			}
		}
		
		$db->query("UPDATE `pages` SET `edit_time` = '".time()."' WHERE `id` = '$movie->id' LIMIT 1");

		echo $movie->title . ": success...\n";

	} else {
	
		echo $movie->title . ": ERROR NO DATA...\n";
	}
	
	sleep(rand(2,7));

}

echo "\ndone!\n";
