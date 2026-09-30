<?php

/**
 * approve_forum_topics.php
 *
 * Approves all imported historical topics from `restored_articles_review`
 * that belong to forum categories (`cat.isforum = 1` or `cat.module = 'forums'`
 * or legacy forum redirects `id IN (660, 238)`).
 *
 * Actions performed:
 *  - Updates `pages` table: `needs_review = 0`, `private = 0`
 *  - Updates `restored_articles_review` table: `status = 'approved'`, `reviewed_at = NOW()`, `reviewed_by = 1`
 *  - Recalculates stats for all affected categories and parent categories bottom-up
 *  - Flushes Memcached cache and refreshes forum caches
 *
 * Usage:
 *   php exs.lv/scripts/approve_forum_topics.php [--dry-run] [--verbose]
 *   php exs.lv/scripts/approve_forum_topics.php --stats
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
    echo "Usage: php approve_forum_topics.php [options]\n";
    echo "Options:\n";
    echo "  --dry-run   Simulate approval without making any database changes\n";
    echo "  --verbose   Output details for each category being updated\n";
    echo "  --stats     Show statistics of pending topics in forum vs non-forum categories\n";
    echo "  --help      Show this help message\n";
    exit(0);
}

$dryRun = isset($options['dry-run']);
$verbose = isset($options['verbose']);
$showStats = isset($options['stats']);

$forumWhere = "(c.isforum = 1 OR c.module = 'forums' OR c.id IN (660, 238))";

if ($showStats) {
    $pendingForum = (int)$db->get_var("
        SELECT COUNT(*)
        FROM restored_articles_review r
        JOIN cat c ON c.id = r.category_id
        WHERE r.status = 'pending' AND {$forumWhere}
    ");

    $pendingNonForum = (int)$db->get_var("
        SELECT COUNT(*)
        FROM restored_articles_review r
        JOIN cat c ON c.id = r.category_id
        WHERE r.status = 'pending' AND NOT {$forumWhere}
    ");

    $alreadyApproved = (int)$db->get_var("SELECT COUNT(*) FROM restored_articles_review WHERE status = 'approved'");
    $alreadyRejected = (int)$db->get_var("SELECT COUNT(*) FROM restored_articles_review WHERE status = 'rejected'");

    echo "=== Restored Articles Review Stats ===\n";
    echo "Pending Forum Topics:      {$pendingForum}\n";
    echo "Pending Non-Forum Topics:  {$pendingNonForum}\n";
    echo "Already Approved:          {$alreadyApproved}\n";
    echo "Already Rejected:          {$alreadyRejected}\n";
    echo "--------------------------------------\n";

    echo "Category Breakdown of Pending Forum Topics:\n";
    $breakdown = $db->get_results("
        SELECT c.id, c.title, c.isforum, c.module, COUNT(r.id) as cnt
        FROM restored_articles_review r
        JOIN cat c ON c.id = r.category_id
        WHERE r.status = 'pending' AND {$forumWhere}
        GROUP BY c.id
        ORDER BY cnt DESC
    ");
    foreach ($breakdown as $b) {
        echo sprintf(" - [%4d] %-30s (cnt: %d, isforum: %d, module: %s)\n", $b->id, $b->title, $b->cnt, $b->isforum, $b->module);
    }
    exit(0);
}

// Fetch all pending forum reviews
$pendingRows = $db->get_results("
    SELECT r.id as review_id, r.page_id, r.category_id, r.title, c.title as cat_title, c.parent
    FROM restored_articles_review r
    JOIN cat c ON c.id = r.category_id
    WHERE r.status = 'pending' AND {$forumWhere}
    ORDER BY r.id ASC
");

$totalPending = count($pendingRows);
echo "Found {$totalPending} pending forum topics to approve" . ($dryRun ? " [DRY-RUN]" : "") . ".\n";

if ($totalPending === 0) {
    echo "No pending forum topics found.\n";
    exit(0);
}

$pageIds = [];
$reviewIds = [];
$catCounts = [];
$affectedCatIds = [];

foreach ($pendingRows as $row) {
    $pageIds[] = (int)$row->page_id;
    $reviewIds[] = (int)$row->review_id;
    $catId = (int)$row->category_id;
    $affectedCatIds[$catId] = true;
    if (!isset($catCounts[$catId])) {
        $catCounts[$catId] = [
            'title' => $row->cat_title,
            'count' => 0
        ];
    }
    $catCounts[$catId]['count']++;
}

echo "Breakdown by category:\n";
foreach ($catCounts as $cid => $info) {
    echo sprintf(" - [%4d] %-32s: %d topics\n", $cid, $info['title'], $info['count']);
}

if ($dryRun) {
    echo "\n[DRY-RUN] No database modifications were performed.\n";
    exit(0);
}

echo "\nApproving topics in database...\n";
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

    $db->query("UPDATE pages SET needs_review = 0, private = 0 WHERE id IN ({$pIdsStr})");
    $db->query("UPDATE restored_articles_review SET status = 'approved', reviewed_at = NOW(), reviewed_by = 1 WHERE id IN ({$rIdsStr})");

    if ($verbose) {
        echo " - Chunk " . ($i + 1) . "/" . count($pageChunks) . " updated (" . count($pChunk) . " items)\n";
    }
}

echo "Database records updated successfully.\n";

// Recompute stats for affected categories bottom-up
echo "Updating category statistics...\n";

// Gather all parents and grandparents
$levels = [];
$levels[0] = array_keys($affectedCatIds); // Level 0: leaf categories

$currentLevelIds = $levels[0];
$visited = array_flip($currentLevelIds);

for ($lvl = 1; $lvl <= 5; $lvl++) {
    if (empty($currentLevelIds)) {
        break;
    }
    $idsStr = implode(',', $currentLevelIds);
    $parents = $db->get_col("SELECT DISTINCT parent FROM cat WHERE id IN ({$idsStr}) AND parent > 0");
    if (empty($parents)) {
        break;
    }
    $newParents = [];
    foreach ($parents as $p) {
        $p = (int)$p;
        if (!isset($visited[$p])) {
            $newParents[] = $p;
            $visited[$p] = true;
        }
    }
    if (empty($newParents)) {
        break;
    }
    $levels[$lvl] = $newParents;
    $currentLevelIds = $newParents;
}

// Update stats from level 0 upwards (leaves to root)
foreach ($levels as $lvlIndex => $catIdList) {
    if ($verbose) {
        echo "Updating stats for level {$lvlIndex} (" . count($catIdList) . " categories)...\n";
    }
    foreach ($catIdList as $cid) {
        update_stats($cid);
    }
}

// Flush Memcached cache
echo "Flushing Memcached cache...\n";
$m->flush();
clear_forum_cache();
clear_latest_posts_cache();

$elapsed = round(microtime(true) - $startTime, 2);
echo "\nDONE! Successfully approved {$totalPending} forum topics in {$elapsed} seconds.\n";
