<?php

/**
 * auto_approve_everything.php
 *
 * Approves all pending restored articles:
 *  - Categorizes all runescape.exs.lv articles into "RS rakstu arhīvs" (category ID 346)
 *  - Categorizes articles with unknown/non-existing/inactive categories into "Galvenā sadaļa" (category ID 232)
 *  - Approves all pending articles (`pages.needs_review = 0`, `pages.private = 0`, `restored_articles_review.status = 'approved'`)
 *  - Recalculates stats for affected categories and parents
 *  - Flushes Memcached cache and forum caches
 *
 * Usage:
 *   php exs.lv/scripts/auto_approve_everything.php [--dry-run] [--verbose]
 *   php exs.lv/scripts/auto_approve_everything.php --stats
 */

if (php_sapi_name() !== 'cli') {
    die("CLI only.\n");
}

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
    echo "Usage: php auto_approve_everything.php [options]\n";
    echo "Options:\n";
    echo "  --dry-run   Simulate execution without modifying the database\n";
    echo "  --verbose   Output detailed progress per chunk\n";
    echo "  --stats     Show statistics of current pending articles and categories\n";
    echo "  --help      Show this help message\n";
    exit(0);
}

$dryRun = isset($options['dry-run']);
$verbose = isset($options['verbose']);
$showStats = isset($options['stats']);

$catOfftopicId = 232;
$catOfftopicTitle = (string)$db->get_var("SELECT title FROM cat WHERE id = {$catOfftopicId}");
if (empty($catOfftopicTitle)) {
    die("Category 232 ('Galvenā sadaļa') not found!\n");
}

$catRsArchiveId = 346;
$catRsArchiveTitle = (string)$db->get_var("SELECT title FROM cat WHERE id = {$catRsArchiveId}");
if (empty($catRsArchiveTitle)) {
    die("Category 346 ('RS Rakstu arhīvs') not found!\n");
}

// Identify RuneScape article category IDs:
// lang = 9 or module = rshelp or parent in (1863, 1903, 4, 102) or id in (346, 1874, 91, 1903)
// excluding personal user blogs (isblog > 0) and forum categories (661, 1871, isforum = 1)
$rsCatRows = $db->get_results("
    SELECT id, title, module, parent, lang
    FROM cat
    WHERE (lang = 9 OR module = 'rshelp' OR parent IN (1863, 1903, 4, 102) OR id IN (346, 1874, 91, 1903))
      AND isblog = 0
      AND id NOT IN (661, 1871)
      AND isforum = 0
");

$rsCatMap = [];
foreach ($rsCatRows as $row) {
    $rsCatMap[(int)$row->id] = $row->title;
}

// Fetch all active categories into map for existence check
$allActiveCats = $db->get_results("SELECT id, title, status FROM cat WHERE status = 'active' AND TRIM(title) != ''");
$activeCatMap = [];
foreach ($allActiveCats as $c) {
    $activeCatMap[(int)$c->id] = $c->title;
}

if ($showStats) {
    $totalPending = (int)$db->get_var("SELECT count(*) FROM restored_articles_review WHERE status = 'pending'");
    $totalPagesNeedsReview = (int)$db->get_var("SELECT count(*) FROM pages WHERE needs_review = 1");
    $totalApproved = (int)$db->get_var("SELECT count(*) FROM restored_articles_review WHERE status = 'approved'");

    echo "=== Auto-Approve Statistics ===\n";
    echo "Pending review records:       {$totalPending}\n";
    echo "Pages with needs_review = 1:  {$totalPagesNeedsReview}\n";
    echo "Already approved reviews:     {$totalApproved}\n";
    echo "Target Offtopic Category:     [{$catOfftopicId}] {$catOfftopicTitle}\n";
    echo "Target RS Archive Category:   [{$catRsArchiveId}] {$catRsArchiveTitle}\n";
    echo "Identified RS categories:     " . count($rsCatMap) . " categories\n";
    exit(0);
}

// 1. Fetch all pending review items
$pendingReviews = $db->get_results("
    SELECT r.id as review_id, r.page_id, r.category_id, r.title, p.category as page_category
    FROM restored_articles_review r
    LEFT JOIN pages p ON p.id = r.page_id
    WHERE r.status = 'pending'
    ORDER BY r.id ASC
");

// 2. Also check any pages with needs_review = 1 not in review table
$orphanPages = $db->get_results("
    SELECT p.id as page_id, p.category as page_category, p.title
    FROM pages p
    LEFT JOIN restored_articles_review r ON r.page_id = p.id
    WHERE p.needs_review = 1 AND r.id IS NULL
");

$totalPending = count($pendingReviews);
$totalOrphans = count($orphanPages);

echo "Found {$totalPending} pending review articles and {$totalOrphans} unlinked pages with needs_review = 1" . ($dryRun ? " [DRY-RUN]" : "") . ".\n";

// Also check existing approved RuneScape articles not yet in category 346
$rsIdsList = implode(',', array_keys($rsCatMap));
$approvedRsPages = $db->get_results("
    SELECT p.id as page_id, p.category as page_category, r.id as review_id
    FROM pages p
    LEFT JOIN restored_articles_review r ON r.page_id = p.id
    WHERE p.category IN ({$rsIdsList})
      AND p.category != {$catRsArchiveId}
      AND p.needs_review = 0
");
$totalApprovedRsToMove = count($approvedRsPages);
echo "Found {$totalApprovedRsToMove} already approved RuneScape articles in legacy categories to move to [{$catRsArchiveId}] {$catRsArchiveTitle}.\n";

// Plan destinations for pending reviews
$toRsArchivePageIds = [];
$toRsArchiveReviewIds = [];

$toOfftopicPageIds = [];
$toOfftopicReviewIds = [];

$toKeepPageIds = [];
$toKeepReviewIds = [];

$affectedCategoryIds = [];
$affectedCategoryIds[$catOfftopicId] = true;
$affectedCategoryIds[$catRsArchiveId] = true;

foreach ($pendingReviews as $rev) {
    $pId = (int)$rev->page_id;
    $rId = (int)$rev->review_id;
    $curCat = (int)($rev->page_category ?: $rev->category_id);
    $affectedCategoryIds[$curCat] = true;

    // Check if it's a RuneScape article
    if (isset($rsCatMap[$curCat])) {
        $toRsArchivePageIds[] = $pId;
        $toRsArchiveReviewIds[] = $rId;
    } elseif (!isset($activeCatMap[$curCat])) {
        // Unknown or non-existing or inactive category
        $toOfftopicPageIds[] = $pId;
        $toOfftopicReviewIds[] = $rId;
    } else {
        // Valid existing category, keep it
        $toKeepPageIds[] = $pId;
        $toKeepReviewIds[] = $rId;
    }
}

// Process orphan pages
foreach ($orphanPages as $orp) {
    $pId = (int)$orp->page_id;
    $curCat = (int)$orp->page_category;
    $affectedCategoryIds[$curCat] = true;

    if (isset($rsCatMap[$curCat])) {
        $toRsArchivePageIds[] = $pId;
    } elseif (!isset($activeCatMap[$curCat])) {
        $toOfftopicPageIds[] = $pId;
    } else {
        $toKeepPageIds[] = $pId;
    }
}

// Add legacy approved RuneScape articles to RS move list
$approvedRsPageIds = [];
$approvedRsReviewIds = [];
foreach ($approvedRsPages as $arp) {
    $pId = (int)$arp->page_id;
    $curCat = (int)$arp->page_category;
    $affectedCategoryIds[$curCat] = true;
    $approvedRsPageIds[] = $pId;
    if (!empty($arp->review_id)) {
        $approvedRsReviewIds[] = (int)$arp->review_id;
    }
}

echo "\nPlan Summary:\n";
echo " - Pending articles to move to [{$catRsArchiveId}] {$catRsArchiveTitle}: " . count($toRsArchivePageIds) . "\n";
echo " - Pending articles with unknown category to move to [{$catOfftopicId}] {$catOfftopicTitle}: " . count($toOfftopicPageIds) . "\n";
echo " - Pending articles keeping their current valid category: " . count($toKeepPageIds) . "\n";
echo " - Approved RS articles to move to [{$catRsArchiveId}] {$catRsArchiveTitle}: " . count($approvedRsPageIds) . "\n";

if ($dryRun) {
    echo "\n[DRY-RUN] No changes were made to the database.\n";
    exit(0);
}

echo "\nExecuting database updates...\n";
$startTime = microtime(true);
$chunkSize = 500;

// Helper to chunk-update
function updateChunked($db, $pageIds, $reviewIds, $setCategory, $catTitle, $verbose, $label) {
    global $chunkSize;
    if (empty($pageIds)) {
        return;
    }
    $pageChunks = array_chunk($pageIds, $chunkSize);
    $reviewChunks = !empty($reviewIds) ? array_chunk($reviewIds, $chunkSize) : [];

    for ($i = 0; $i < count($pageChunks); $i++) {
        $pIdsStr = implode(',', $pageChunks[$i]);
        if ($setCategory !== null) {
            $db->query("UPDATE pages SET category = {$setCategory}, needs_review = 0, private = 0 WHERE id IN ({$pIdsStr})");
        } else {
            $db->query("UPDATE pages SET needs_review = 0, private = 0 WHERE id IN ({$pIdsStr})");
        }

        if (isset($reviewChunks[$i]) && !empty($reviewChunks[$i])) {
            $rIdsStr = implode(',', $reviewChunks[$i]);
            if ($setCategory !== null && $catTitle !== null) {
                $db->query("UPDATE restored_articles_review SET category_id = {$setCategory}, category_name = '" . sanitize($catTitle) . "', status = 'approved', reviewed_at = NOW(), reviewed_by = 1 WHERE id IN ({$rIdsStr})");
            } else {
                $db->query("UPDATE restored_articles_review SET status = 'approved', reviewed_at = NOW(), reviewed_by = 1 WHERE id IN ({$rIdsStr})");
            }
        }
        if ($verbose) {
            echo "   - {$label}: chunk " . ($i + 1) . "/" . count($pageChunks) . " done (" . count($pageChunks[$i]) . " items)\n";
        }
    }
}

// Populate empty intro / meta_description from text for pending pages
$db->query("
    UPDATE pages
    SET intro = IF(CHAR_LENGTH(TRIM(REPLACE(REPLACE(REPLACE(text, '<br>', ' '), '<p>', ' '), '</p>', ' '))) > 280,
                   CONCAT(SUBSTRING(TRIM(REPLACE(REPLACE(REPLACE(text, '<br>', ' '), '<p>', ' '), '</p>', ' ')), 1, 280), '...'),
                   TRIM(REPLACE(REPLACE(REPLACE(text, '<br>', ' '), '<p>', ' '), '</p>', ' '))),
        meta_description = SUBSTRING(TRIM(REPLACE(REPLACE(REPLACE(text, '<br>', ' '), '<p>', ' '), '</p>', ' ')), 1, 160)
    WHERE needs_review = 1 AND (intro IS NULL OR intro = '' OR meta_description IS NULL OR meta_description = '')
");

// 1. Move & approve RuneScape pending articles
echo "1. Moving & approving RuneScape pending articles...\n";
updateChunked($db, $toRsArchivePageIds, $toRsArchiveReviewIds, $catRsArchiveId, $catRsArchiveTitle, $verbose, "RS Pending");

// 2. Move & approve unknown/non-existing category pending articles
echo "2. Moving & approving unknown category pending articles...\n";
updateChunked($db, $toOfftopicPageIds, $toOfftopicReviewIds, $catOfftopicId, $catOfftopicTitle, $verbose, "Unknown/Offtopic");

// 3. Approve remaining pending articles keeping their valid categories
echo "3. Approving valid category pending articles...\n";
updateChunked($db, $toKeepPageIds, $toKeepReviewIds, null, null, $verbose, "Valid Pending");

// 4. Move approved legacy RuneScape articles into 346
echo "4. Moving legacy approved RuneScape articles to RS archive...\n";
if (!empty($approvedRsPageIds)) {
    $approvedPageChunks = array_chunk($approvedRsPageIds, $chunkSize);
    $approvedReviewChunks = !empty($approvedRsReviewIds) ? array_chunk($approvedRsReviewIds, $chunkSize) : [];

    for ($i = 0; $i < count($approvedPageChunks); $i++) {
        $pIdsStr = implode(',', $approvedPageChunks[$i]);
        $db->query("UPDATE pages SET category = {$catRsArchiveId} WHERE id IN ({$pIdsStr})");

        if (isset($approvedReviewChunks[$i]) && !empty($approvedReviewChunks[$i])) {
            $rIdsStr = implode(',', $approvedReviewChunks[$i]);
            $db->query("UPDATE restored_articles_review SET category_id = {$catRsArchiveId}, category_name = '" . sanitize($catRsArchiveTitle) . "' WHERE id IN ({$rIdsStr})");
        }
        if ($verbose) {
            echo "   - Approved RS Move: chunk " . ($i + 1) . "/" . count($approvedPageChunks) . " done\n";
        }
    }
}

// Ensure any rogue needs_review = 1 is cleaned up
$db->query("UPDATE pages SET needs_review = 0, private = 0 WHERE needs_review = 1");

echo "\nDatabase records updated. Recalculating category statistics...\n";

// Update stats for all affected categories
foreach (array_keys($affectedCategoryIds) as $cid) {
    if ($cid > 0) {
        update_stats($cid);
    }
}

// Also update main parent categories
$parentsToUpdate = [1863, 1903, 4, 102, 110, 247, 81, 663, 101];
foreach ($parentsToUpdate as $pid) {
    update_stats($pid);
}

// Flush Memcached cache and forum caches
echo "Flushing Memcached and site caches...\n";
$m->flush();
clear_forum_cache();
clear_latest_posts_cache();

$elapsed = round(microtime(true) - $startTime, 2);
echo "\nSUCCESS! All articles approved and categorized in {$elapsed} seconds.\n";
