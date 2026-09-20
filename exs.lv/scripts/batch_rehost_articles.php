<?php

/**
 * batch_rehost_articles.php
 *
 * Automatically runs "pārnest attēlus" (image rehosting) for all articles
 * in specified sections (default: 81 = Spēļu apskati, 80 = Filmas).
 *
 * Usage:
 *   php batch_rehost_articles.php [--dry-run] [--categories=80,81] [--limit=N] [--start-id=N] [--delay=0.1] [--verbose]
 */

if (php_sapi_name() !== 'cli') {
    die("CLI only.\n");
}

chdir(__DIR__ . '/..');
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['HTTP_HOST'] = 'exs.lv';

require_once('configdb.php');

if (!defined('IMG_PATH')) {
    define('IMG_PATH', ROOT_PATH . '/img.exs.lv');
}

require_once(CORE_PATH . '/includes/class.mdb.php');
require_once(CORE_PATH . '/includes/functions.core.php');
require_once(CORE_PATH . '/modules/read/functions.read.php');

$db = new mdb($username, $password, $database, $hostname);

$m = new Memcached;
$m->addServer($mc_host, $mc_port);

$lang = 1;

// Mock Admin Auth for CLI execution
class BatchRehostCliAuth {
    public $ok = true;
    public $id = 1;
    public $level = 1;
    public $ip = '127.0.0.1';

    public function log($action, $foreign_table = '', $foreign_key = 0) {
        global $db;
        if (isset($db) && is_object($db)) {
            $db->query("INSERT INTO `logs` (`user_id`,`action`,`created`,`ip`,`foreign_table`,`foreign_key`) VALUES ('{$this->id}','" . sanitize($action) . "',NOW(),'{$this->ip}','" . sanitize($foreign_table) . "','" . intval($foreign_key) . "')");
        }
    }
}
$auth = new BatchRehostCliAuth();

// Parse CLI options
$options = getopt('', [
    'dry-run',
    'categories:',
    'limit:',
    'start-id:',
    'delay:',
    'verbose',
    'help'
]);

if (isset($options['help'])) {
    echo "Usage: php batch_rehost_articles.php [options]\n";
    echo "Options:\n";
    echo "  --dry-run          Scan and report external images without downloading or updating DB\n";
    echo "  --categories=LIST  Comma-separated category IDs (default: 80,81)\n";
    echo "  --limit=N          Process at most N articles\n";
    echo "  --start-id=N       Process articles with ID >= N\n";
    echo "  --delay=SECONDS    Pause between articles (e.g. 0.2)\n";
    echo "  --verbose          Show detailed debug output\n";
    echo "  --help             Show this help message\n";
    exit(0);
}

$dryRun = isset($options['dry-run']);
$verbose = isset($options['verbose']);
$categoriesRaw = isset($options['categories']) ? (string)$options['categories'] : '80,81';
$categoryIds = array_filter(array_map('intval', explode(',', $categoriesRaw)));
if (empty($categoryIds)) {
    die("Error: No valid categories specified.\n");
}
$catSql = implode(',', $categoryIds);

$limit = isset($options['limit']) ? intval($options['limit']) : 0;
$startId = isset($options['start-id']) ? intval($options['start-id']) : 0;
$delay = isset($options['delay']) ? floatval($options['delay']) : 0.0;

echo "=======================================================\n";
echo "   EXS.LV Batch Article Image Rehost (\"Pārnest attēlus\")\n";
echo "=======================================================\n";
echo "Mode:       " . ($dryRun ? "DRY-RUN (no files or DB modified)" : "LIVE EXECUTION") . "\n";
echo "Categories: " . implode(', ', $categoryIds) . "\n";
if ($startId > 0) echo "Start ID:   >= {$startId}\n";
if ($limit > 0)   echo "Limit:      {$limit} articles\n";
if ($delay > 0)   echo "Delay:      {$delay}s between articles\n";
echo "Verbose:    " . ($verbose ? "YES" : "NO") . "\n";
echo "-------------------------------------------------------\n\n";

// Query category titles
$catTitles = [];
foreach ($categoryIds as $cid) {
    $title = $db->get_var("SELECT title FROM cat WHERE id = " . intval($cid));
    $catTitles[$cid] = $title ?: "Category {$cid}";
    echo "Category [{$cid}]: {$catTitles[$cid]}\n";
}
echo "\n";

// Query matching candidate articles
$whereClauses = [
    "category IN ({$catSql})",
    "(text LIKE '%<img%' OR text LIKE '%[img%' OR intro LIKE '%<img%' OR intro LIKE '%[img%')"
];
if ($startId > 0) {
    $whereClauses[] = "id >= {$startId}";
}
$whereSql = implode(' AND ', $whereClauses);
$orderSql = "ORDER BY id ASC";
$limitSql = ($limit > 0) ? "LIMIT {$limit}" : "";

$query = "SELECT * FROM pages WHERE {$whereSql} {$orderSql} {$limitSql}";
$articles = $db->get_results($query);

if (empty($articles)) {
    echo "No matching articles found in categories {$catSql}.\n";
    exit(0);
}

$totalCandidates = count($articles);
echo "Found {$totalCandidates} candidate articles containing image tags.\n";
echo "Starting scan and rehost process...\n\n";

$startTime = microtime(true);
$articlesProcessed = 0;
$articlesWithExt = 0;
$articlesRehosted = 0;
$totalImagesRehosted = 0;
$totalImagesFound = 0;
$articlesFailed = 0;

/**
 * Helper to inspect external image URLs in an article
 */
function get_article_external_images($article) {
    $combined = ($article->text ?? '') . ' ' . ($article->intro ?? '');
    preg_match_all('/<img\b[^>]*?\bsrc\s*=\s*(["\']?)([^"\'\s>]+)\1[^>]*>/i', $combined, $img_matches);
    preg_match_all('/\[img\]\s*([^\[\]\s]+)\s*\[\/img\]/i', $combined, $bb_matches);

    $raw_urls = array_unique(array_merge($img_matches[2] ?? [], $bb_matches[1] ?? []));
    if (empty($raw_urls)) {
        return [];
    }

    $external = [];
    foreach ($raw_urls as $raw) {
        $clean = clean_remote_url($raw);
        if (is_external_image_url($clean)) {
            $external[] = [
                'raw' => $raw,
                'clean' => $clean
            ];
        }
    }
    return $external;
}

/**
 * Fix permissions for created rehost directory and files
 */
function fix_rehost_permissions($article_id) {
    $storage_dir = IMG_PATH . '/rehost/' . (int)$article_id;
    if (!is_dir($storage_dir)) {
        return;
    }

    // Try chown/chgrp to exs:exs if user exists
    @chown($storage_dir, 'exs');
    @chgrp($storage_dir, 'exs');
    @chmod($storage_dir, 0775);

    $files = glob($storage_dir . '/*');
    if ($files) {
        foreach ($files as $f) {
            @chown($f, 'exs');
            @chgrp($f, 'exs');
            @chmod($f, 0664);
        }
    }
}

foreach ($articles as $idx => $article) {
    $articlesProcessed++;
    $catName = $catTitles[$article->category] ?? "Cat {$article->category}";
    $prefix = sprintf("[%d/%d] #%d \"%s\" (%s):", $articlesProcessed, $totalCandidates, $article->id, $article->title, $catName);

    $extImages = get_article_external_images($article);
    if (empty($extImages)) {
        if ($verbose) {
            echo "{$prefix} All images already internal. Skipping.\n";
        }
        continue;
    }

    $articlesWithExt++;
    $countExt = count($extImages);
    $totalImagesFound += $countExt;

    echo "{$prefix} Found {$countExt} external image(s).\n";

    if ($verbose || $dryRun) {
        foreach ($extImages as $img) {
            echo "   -> {$img['clean']}\n";
        }
    }

    if ($dryRun) {
        continue;
    }

    // Live Rehost: loop up to 5 rounds if budget is exceeded so large articles finish all images
    $round = 0;
    $articleRehostCount = 0;
    $currentArticle = $article;

    while ($round < 5) {
        $round++;
        $result = rehost_article_images($currentArticle);

        if ($result['status'] === 'success') {
            $rehostedInRound = $result['count'] ?? 0;
            $articleRehostCount += $rehostedInRound;

            if ($rehostedInRound > 0) {
                echo "   Round {$round}: Successfully rehosted {$rehostedInRound} image(s).\n";
            }

            // Check if time budget exceeded and there are more images to process
            if (!empty($result['notice'])) {
                echo "   Notice: Time budget reached. Refreshing article state and continuing...\n";
                // Re-fetch updated article from DB
                $refreshed = $db->get_row("SELECT * FROM pages WHERE id = " . (int)$article->id);
                if ($refreshed) {
                    $currentArticle = $refreshed;
                    $remainingExt = get_article_external_images($currentArticle);
                    if (!empty($remainingExt)) {
                        echo "   Remaining external images to process: " . count($remainingExt) . "\n";
                        continue;
                    }
                }
            }
            break;
        } else {
            // Error returned
            $errMsg = $result['message'] ?? 'Unknown error';
            echo "   Error: {$errMsg}\n";
            $articlesFailed++;
            break;
        }
    }

    if ($articleRehostCount > 0) {
        $articlesRehosted++;
        $totalImagesRehosted += $articleRehostCount;
        fix_rehost_permissions($article->id);
        echo "   -> Total rehosted for article: {$articleRehostCount} image(s).\n";
    }

    if ($delay > 0) {
        usleep((int)($delay * 1000000));
    }
}

// Flush Memcached
if (!$dryRun) {
    echo "\nFlushing Memcached cache...\n";
    $m->flush();
}

$elapsed = round(microtime(true) - $startTime, 2);

echo "\n=======================================================\n";
echo "                  BATCH REHOST COMPLETE                \n";
echo "=======================================================\n";
echo "Execution time:             {$elapsed}s\n";
echo "Articles scanned:           {$articlesProcessed}\n";
echo "Articles with ext images:   {$articlesWithExt}\n";
if ($dryRun) {
    echo "Total ext images found:     {$totalImagesFound}\n";
} else {
    echo "Articles updated:           {$articlesRehosted}\n";
    echo "Total images rehosted:      {$totalImagesRehosted}\n";
    echo "Articles with errors:       {$articlesFailed}\n";
}
echo "=======================================================\n";
