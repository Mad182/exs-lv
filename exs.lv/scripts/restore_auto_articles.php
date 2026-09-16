<?php

/**
 * restore_auto_articles.php
 *
 * Restores historical articles from auto.exs.lv preserved in Wayback Machine
 * into https://exs.lv/auto-jaunumi (pages category 611).
 *
 * Features:
 *  - Preserves original publication dates, view counts, and authors.
 *  - Maps author username to existing EXS user; if not found, falls back to user ID 1.
 *  - Generates clean SEF slugs using mkslug_newpage().
 *  - Downloads & rehosts intro images and inline upload images to img.exs.lv/auto/.
 *  - Cleans HTML body of Wayback artifacts and standardizes formatting.
 *  - Updates category statistics via update_stats(611).
 *
 * Usage:
 *   php restore_auto_articles.php [--dry-run] [--verbose]
 *   php restore_auto_articles.php --page=<old_id> [--dry-run]
 *   php restore_auto_articles.php --stats
 */

if (php_sapi_name() !== 'cli') {
    die("CLI only.\n");
}

// Setup environment and paths
chdir(__DIR__ . '/..');
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_URI'] = '/';
require_once('configdb.php');

if (!defined('IMG_PATH')) {
    define('IMG_PATH', ROOT_PATH . '/img.exs.lv');
}

require_once(CORE_PATH . '/includes/class.mdb.php');
require_once(CORE_PATH . '/includes/functions.core.php');
$db = new mdb($username, $password, $database, $hostname);

// Parse CLI options
$options = getopt('', [
    'dry-run',
    'verbose',
    'page:',
    'limit:',
    'stats',
    'help'
]);

$dryRun = isset($options['dry-run']);
$verbose = isset($options['verbose']);
$filterPage = isset($options['page']) ? (int)$options['page'] : null;
$limit = isset($options['limit']) ? (int)$options['limit'] : 0;
$showStats = isset($options['stats']);

if (isset($options['help'])) {
    echo "Usage:\n";
    echo "  php restore_auto_articles.php [--dry-run] [--verbose]\n";
    echo "  php restore_auto_articles.php --page=<old_id> [--dry-run]\n";
    echo "  php restore_auto_articles.php --stats\n";
    exit(0);
}

const TARGET_CATEGORY_ID = 611; // 'auto-jaunumi'
const DEFAULT_AUTHOR_ID = 1;

// Load data file
$dataFile = __DIR__ . '/data/auto_articles.json';
if (!file_exists($dataFile)) {
    die("Error: Data file {$dataFile} not found.\n");
}

$rawArticles = json_decode(file_get_contents($dataFile), true);
if (!is_array($rawArticles)) {
    die("Error: Invalid JSON in {$dataFile}.\n");
}

// Filter out invalid / 404 articles
$articles = [];
foreach ($rawArticles as $item) {
    $title = trim($item['title'] ?? '');
    if (empty($title) || strpos($title, 'Pieprasītais raksts netika atrasts') !== false) {
        continue;
    }
    if ($filterPage !== null && (int)$item['old_id'] !== $filterPage) {
        continue;
    }
    $articles[] = $item;
}

if ($limit > 0) {
    $articles = array_slice($articles, 0, $limit);
}

// Build user map from database
$userMap = [];
$usersRes = $db->get_results("SELECT id, LOWER(nick) AS lnick FROM users");
if ($usersRes) {
    foreach ($usersRes as $u) {
        $userMap[$u->lnick] = (int)$u->id;
    }
}

if ($showStats) {
    $currentCount = (int)$db->get_var("SELECT COUNT(*) FROM pages WHERE category = " . TARGET_CATEGORY_ID);
    echo "=== Auto Articles Stats ===\n";
    echo "Available in archive dataset: " . count($rawArticles) . "\n";
    echo "Valid articles to restore:    " . count($articles) . "\n";
    echo "Current in DB (cat 611):      " . $currentCount . "\n";
    exit(0);
}

echo "=== Restoring auto.exs.lv Articles to exs.lv/auto-jaunumi ===" . ($dryRun ? " [DRY-RUN]" : "") . "\n";
echo "Total articles to process: " . count($articles) . "\n\n";

$imgDir = IMG_PATH . '/auto';
if (!is_dir($imgDir) && !$dryRun) {
    mkdir($imgDir, 0755, true);
}

// Known intro snapshots from Wayback Machine
$introSnapshots = [
    1  => 'http://web.archive.org/web/20120119154418id_/http://auto.exs.lv/upload/intro/1.png',
    2  => 'http://web.archive.org/web/20120119154417id_/http://auto.exs.lv/upload/intro/2.png',
    3  => 'http://web.archive.org/web/20120119154417id_/http://auto.exs.lv/upload/intro/3.png',
    4  => 'http://web.archive.org/web/20120119154415id_/http://auto.exs.lv/upload/intro/4.png',
    5  => 'http://web.archive.org/web/20120119154416id_/http://auto.exs.lv/upload/intro/5.png',
    7  => 'http://web.archive.org/web/20120119154415id_/http://auto.exs.lv/upload/intro/7.png',
    8  => 'http://web.archive.org/web/20120119154414id_/http://auto.exs.lv/upload/intro/8.png',
    10 => 'http://web.archive.org/web/20120119154414id_/http://auto.exs.lv/upload/intro/10.png',
];

/**
 * Download file from URL and save locally
 */
function downloadAsset($url, $destPath) {
    $ctx = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) EXS-LV-Bot/1.0\r\n",
            'timeout' => 15,
            'follow_location' => 1
        ]
    ]);
    $data = @file_get_contents($url, false, $ctx);
    if ($data !== false && strlen($data) > 100) {
        return @file_put_contents($destPath, $data) !== false;
    }
    return false;
}

/**
 * Clean and rehost images inside article HTML
 */
function cleanArticleBody($bodyHtml, $dryRun, $imgDir) {
    // Remove Wayback Machine comments, banners, and scripts
    $bodyHtml = preg_replace('/<!--\s*BEGIN WAYBACK TOOLBAR INSERT\s*-->.*?<!--\s*END WAYBACK TOOLBAR INSERT\s*-->/is', '', $bodyHtml);
    $bodyHtml = preg_replace('/<!--\s*FILE ARCHIVED ON\s*.*?-->/is', '', $bodyHtml);
    $bodyHtml = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $bodyHtml);

    // Rewrite relative article links href="/raksts/(\d+)/([^"]*)" -> "/read/"
    $bodyHtml = preg_replace_callback('/href=([\'"])(?:http:\/\/auto\.exs\.lv)?\/raksts\/(\d+)\/([^\'"]*)\1/i', function ($matches) {
        $q = $matches[1];
        $slug = $matches[3];
        $cleanSlug = strtolower(trim(preg_replace('/[^a-z0-9\-]+/i', '-', $slug), '-'));
        return "href={$q}/read/{$cleanSlug}{$q}";
    }, $bodyHtml);

    // Download & rewrite images hosted under auto.exs.lv/upload/... or wayback captures
    $bodyHtml = preg_replace_callback('/<img\b([^>]*?)src=([\'"])([^\s\'">]+)\2([^>]*?)>/i', function ($matches) use ($dryRun, $imgDir) {
        $before = $matches[1];
        $src = $matches[3];
        $after = $matches[4];

        // Clean wayback prefix from src if present
        if (preg_match('/web\.archive\.org\/web\/\d+(?:id_)?\/(https?:\/\/.*)/i', $src, $wbMatch)) {
            $src = $wbMatch[1];
        }

        // If local upload path
        if (preg_match('/(?:\/upload\/|auto\.exs\.lv\/upload\/)(.*)/i', $src, $uMatch)) {
            $subPath = $uMatch[1];
            $localFilename = preg_replace('/[^a-zA-Z0-9_\.\-]/', '_', basename($subPath));
            $destFile = $imgDir . '/' . $localFilename;
            $publicUrl = 'https://img.exs.lv/auto/' . $localFilename;

            if (!file_exists($destFile) && !$dryRun) {
                // Try to download via Wayback Machine
                $wbSrc = 'http://web.archive.org/web/20120119154418id_/http://auto.exs.lv/upload/' . $subPath;
                downloadAsset($wbSrc, $destFile);
            }

            if (file_exists($destFile) || $dryRun) {
                return "<img{$before}src=\"{$publicUrl}\"{$after}>";
            }
        }

        // Fix protocol relative or http image hotlinks
        return "<img{$before}src=\"{$src}\"{$after}>";
    }, $bodyHtml);

    // Standardize align=justify
    $bodyHtml = str_replace('<p align="justify">', '<p style="text-align:justify;">', $bodyHtml);

    return trim($bodyHtml);
}

$insertedCount = 0;
$skippedCount = 0;
$updatedCount = 0;

foreach ($articles as $art) {
    $oldId = (int)$art['old_id'];
    $title = trim($art['title']);
    $date = trim($art['date']);
    $rawAuthorNick = trim($art['author_nick'] ?? '');
    $authorId = DEFAULT_AUTHOR_ID;
    $views = (int)($art['views'] ?? 0);
    $intro = trim($art['intro'] ?? '');
    $bodyHtml = trim($art['body_html'] ?? '');

    if (empty($date)) {
        $date = date('Y-m-d H:i:s');
    }

    // Resolve author
    if (!empty($rawAuthorNick)) {
        $lnick = strtolower($rawAuthorNick);
        if (isset($userMap[$lnick])) {
            $authorId = $userMap[$lnick];
        }
    }

    // Generate unique slug
    $baseSlug = mkslug_newpage($title);
    if (empty($baseSlug)) {
        $baseSlug = 'auto-' . $oldId;
    }
    $strid = $baseSlug;

    // Check if article already exists (by title or slug)
    $existing = $db->get_row("SELECT id, strid, title FROM pages WHERE category = " . TARGET_CATEGORY_ID . " AND (title = '" . $db->escape($title) . "' OR strid = '" . $db->escape($strid) . "')");
    if ($existing) {
        if ($verbose) {
            echo "[SKIPPED] ID {$oldId}: '{$title}' already exists (page #{$existing->id}, slug: {$existing->strid})\n";
        }
        $skippedCount++;
        continue;
    }

    // Ensure unique strid globally in pages table
    $slugCollision = (int)$db->get_var("SELECT COUNT(*) FROM pages WHERE strid = '" . $db->escape($strid) . "'");
    if ($slugCollision > 0) {
        $strid = $baseSlug . '-' . $oldId;
    }

    // Intro teaser image handling
    $imageField = '';
    if (isset($introSnapshots[$oldId])) {
        $introImgName = 'intro_' . $oldId . '.png';
        $introDest = $imgDir . '/' . $introImgName;
        if (!file_exists($introDest) && !$dryRun) {
            downloadAsset($introSnapshots[$oldId], $introDest);
        }
        if (file_exists($introDest) || $dryRun) {
            $imageField = 'auto/' . $introImgName;
        }
    }

    // Process & clean HTML
    $cleanBody = cleanArticleBody($bodyHtml, $dryRun, $imgDir);
    if (empty($intro) && !empty($cleanBody)) {
        $intro = mb_substr(strip_tags($cleanBody), 0, 300);
    }

    $titleDb = title2db($title);
    $bodyDb = htmlpost2db($cleanBody);
    $introDb = $db->escape($intro);
    $textid = date('YmdHis', strtotime($date));
    $imageDb = $db->escape($imageField);

    if ($verbose || $dryRun) {
        echo "[IMPORT] ID {$oldId}: '{$title}'\n";
        echo "  Date:     {$date}\n";
        echo "  Author:   {$rawAuthorNick} -> User ID {$authorId}\n";
        echo "  Slug:     {$strid}\n";
        echo "  Views:    {$views}\n";
        echo "  Image:    " . ($imageField ?: 'none') . "\n";
        echo "  Body Len: " . strlen($cleanBody) . " chars\n";
    }

    if (!$dryRun) {
        $query = "INSERT INTO pages (
            strid, textid, category, text, intro, title, author,
            date, bump, updated, ip, lang, views, is_wide, posts, image
        ) VALUES (
            '" . $db->escape($strid) . "',
            '" . $db->escape($textid) . "',
            " . TARGET_CATEGORY_ID . ",
            '" . $db->escape($bodyDb) . "',
            '" . $introDb . "',
            '" . $db->escape($titleDb) . "',
            " . $authorId . ",
            '" . $db->escape($date) . "',
            '" . $db->escape($date) . "',
            '" . $db->escape($date) . "',
            '127.0.0.1',
            1,
            " . $views . ",
            0,
            0,
            '" . $imageDb . "'
        )";

        $res = $db->query($query);
        if ($res) {
            $insertedId = $db->insert_id;
            $insertedCount++;
            if ($verbose) {
                echo "  => Inserted page #{$insertedId}\n\n";
            }
        } else {
            echo "  [ERROR] Database insert failed for ID {$oldId}: '{$title}'\n";
        }
    } else {
        $insertedCount++;
        if ($verbose) {
            echo "  => [DRY-RUN] Insert simulated\n\n";
        }
    }
}

// Update category stats
if (!$dryRun && $insertedCount > 0) {
    if (function_exists('update_stats')) {
        update_stats(TARGET_CATEGORY_ID);
    }
    echo "Updated stats for category " . TARGET_CATEGORY_ID . ".\n";
}

echo "\n=== Migration Summary ===\n";
echo "Total Processed: " . count($articles) . "\n";
echo "Inserted:        " . $insertedCount . "\n";
echo "Skipped/Exists:  " . $skippedCount . "\n";
echo "Done!\n";
