<?php

/**
 * restore_missing_user_blogs.php
 *
 * Auto-restores all imported articles from blog categories whose authors
 * are non-existing (deleted/purged) users and moves them to "Galvenā sadaļa"
 * forum (category ID 232).
 *
 * Actions performed:
 *  - Updates `pages` table: `category = 232`, `needs_review = 0`, `private = 0`
 *  - Updates `restored_articles_review`: `category_id = 232`, `category_name = 'Galvenā sadaļa'`,
 *    `status = 'approved'`, `reviewed_at = NOW()`, `reviewed_by = 1`
 *  - Recalculates stats for all source blog categories and category 232 (plus forum parents)
 *  - Flushes Memcached cache and refreshes forum caches
 *
 * Usage:
 *   php exs.lv/scripts/restore_missing_user_blogs.php [--dry-run] [--verbose]
 *   php exs.lv/scripts/restore_missing_user_blogs.php --stats
 */

if (php_sapi_name() !== 'cli') {
    die("CLI only.\n");
}

// Setup environment and paths
chdir(__DIR__ . '/..');
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['HTTP_HOST'] = 'exs.lv';
require_once('configdb.php');

require_once(CORE_PATH . '/includes/class.mdb.php');
require_once(CORE_PATH . '/includes/functions.core.php');
$db = new mdb($username, $password, $database, $hostname);

$m = new Memcached;
if (defined('Memcached::HAVE_IGBINARY') && Memcached::HAVE_IGBINARY) {
    $m->setOption(Memcached::OPT_SERIALIZER, Memcached::SERIALIZER_IGBINARY);
}
$m->addServer($mc_host, $mc_port);

$options = getopt('', [
    'dry-run',
    'verbose',
    'stats',
    'help'
]);

if (isset($options['help'])) {
    echo "Usage: php restore_missing_user_blogs.php [options]\n";
    echo "Options:\n";
    echo "  --dry-run   Simulate restoration without making any database changes\n";
    echo "  --verbose   Output details for each article/category being updated\n";
    echo "  --stats     Show statistics of pending blog articles with missing vs existing users\n";
    echo "  --help      Show this help message\n";
    exit(0);
}

$dryRun = isset($options['dry-run']);
$verbose = isset($options['verbose']);
$showStats = isset($options['stats']);

$targetCategoryId = 232; // "Galvenā sadaļa"
$targetCategoryTitle = (string)$db->get_var("SELECT title FROM cat WHERE id = {$targetCategoryId}");

if (empty($targetCategoryTitle)) {
    die("Target category ID {$targetCategoryId} ('Galvenā sadaļa') not found!\n");
}

$blogWhere = "(c.isblog != 0 OR c.parent = 110 OR c.module = 'blogs' OR c.id IN (110, 748))";

if ($showStats) {
    $missingCount = (int)$db->get_var("
        SELECT COUNT(r.id)
        FROM restored_articles_review r
        JOIN cat c ON c.id = r.category_id
        LEFT JOIN users u ON u.id = r.author_id
        WHERE r.status = 'pending' AND {$blogWhere} AND u.id IS NULL
    ");

    $existingCount = (int)$db->get_var("
        SELECT COUNT(r.id)
        FROM restored_articles_review r
        JOIN cat c ON c.id = r.category_id
        LEFT JOIN users u ON u.id = r.author_id
        WHERE r.status = 'pending' AND {$blogWhere} AND u.id IS NOT NULL
    ");

    echo "=== Blog Articles by Author Existence ===\n";
    echo "Pending Blog Articles (Non-existing users): {$missingCount}\n";
    echo "Pending Blog Articles (Existing users):     {$existingCount}\n";
    echo "Target Forum:                              [{$targetCategoryId}] {$targetCategoryTitle}\n";
    echo "------------------------------------------\n";

    echo "Breakdown of articles with non-existing users by original category:\n";
    $breakdown = $db->get_results("
        SELECT c.id, c.title, COUNT(r.id) as cnt
        FROM restored_articles_review r
        JOIN cat c ON c.id = r.category_id
        LEFT JOIN users u ON u.id = r.author_id
        WHERE r.status = 'pending' AND {$blogWhere} AND u.id IS NULL
        GROUP BY c.id
        ORDER BY cnt DESC
    ");
    foreach ($breakdown as $b) {
        echo sprintf(" - [%4d] %-32s: %d articles\n", $b->id, $b->title, $b->cnt);
    }
    exit(0);
}

// Fetch all pending blog articles whose author does not exist in users
$pendingRows = $db->get_results("
    SELECT r.id as review_id, r.page_id, r.category_id, r.title, r.author_id, r.author_nick, c.title as cat_title
    FROM restored_articles_review r
    JOIN cat c ON c.id = r.category_id
    LEFT JOIN users u ON u.id = r.author_id
    WHERE r.status = 'pending' AND {$blogWhere} AND u.id IS NULL
    ORDER BY r.id ASC
");

$totalCount = count($pendingRows);
echo "Found {$totalCount} pending blog articles of non-existing users to restore into '{$targetCategoryTitle}' [ID: {$targetCategoryId}]" . ($dryRun ? " [DRY-RUN]" : "") . ".\n";

if ($totalCount === 0) {
    echo "No pending blog articles of non-existing users found.\n";
    exit(0);
}

$pageIds = [];
$reviewIds = [];
$sourceCatIds = [];
$catCounts = [];

foreach ($pendingRows as $row) {
    $pageIds[] = (int)$row->page_id;
    $reviewIds[] = (int)$row->review_id;
    $origCid = (int)$row->category_id;
    $sourceCatIds[$origCid] = true;

    if (!isset($catCounts[$origCid])) {
        $catCounts[$origCid] = [
            'title' => $row->cat_title,
            'count' => 0
        ];
    }
    $catCounts[$origCid]['count']++;
}

echo "Source category breakdown:\n";
foreach ($catCounts as $cid => $info) {
    echo sprintf(" - [%4d] %-32s: %d articles\n", $cid, $info['title'], $info['count']);
}

if ($dryRun) {
    echo "\n[DRY-RUN] No database modifications were performed.\n";
    exit(0);
}

echo "\nUpdating articles in database...\n";
$startTime = microtime(true);

// Process in chunks of 500
$chunkSize = 500;
$pageChunks = array_chunk($pageIds, $chunkSize);
$reviewChunks = array_chunk($reviewIds, $chunkSize);

for ($i = 0; $i < count($pageChunks); $i++) {
    $pChunk = $pageChunks[$i];
    $rChunk = $reviewChunks[$i];

    $pIdsStr = implode(',', $pChunk);
    $rIdsStr = implode(',', $rChunk);

    $db->query("UPDATE pages SET category = {$targetCategoryId}, needs_review = 0, private = 0 WHERE id IN ({$pIdsStr})");
    $db->query("UPDATE restored_articles_review SET category_id = {$targetCategoryId}, category_name = '" . sanitize($targetCategoryTitle) . "', status = 'approved', reviewed_at = NOW(), reviewed_by = 1 WHERE id IN ({$rIdsStr})");

    if ($verbose) {
        echo " - Chunk " . ($i + 1) . "/" . count($pageChunks) . " updated (" . count($pChunk) . " items)\n";
    }
}

echo "Database records updated successfully.\n";

// Recompute stats for affected source categories and target category hierarchy
echo "Updating category statistics...\n";

// Update all source categories
foreach (array_keys($sourceCatIds) as $srcCid) {
    update_stats($srcCid);
}
// Update parent blog category
update_stats(110);

// Update target forum category & parents
update_stats($targetCategoryId); // 232: Galvenā sadaļa
update_stats(663);               // 663: Main
update_stats(101);               // 101: Forums

// Flush Memcached cache
echo "Flushing Memcached cache...\n";
$m->flush();
clear_forum_cache();
clear_latest_posts_cache();

$elapsed = round(microtime(true) - $startTime, 2);
echo "\nDONE! Successfully restored {$totalCount} blog articles into '{$targetCategoryTitle}' in {$elapsed} seconds.\n";
