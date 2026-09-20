<?php

/**
 * fix_post_avatars.php
 *
 * Generates and sets missing post avatars (avatar and sm_avatar)
 * for articles in Spēļu apskati (category 81) and Filmu apskati (category 80).
 *
 * Usage:
 *   php fix_post_avatars.php [--dry-run] [--verbose]
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
require_once(LIB_PATH . '/verot/src/class.upload.php');

$db = new mdb($username, $password, $database, $hostname);

$m = new Memcached;
$m->addServer($mc_host, $mc_port);

$options = getopt('', ['dry-run', 'verbose', 'help']);
$dryRun = isset($options['dry-run']);
$verbose = isset($options['verbose']);

echo "=== EXS.LV Post Avatar Generator ===\n";
echo "Mode: " . ($dryRun ? "DRY-RUN (no files or DB modified)" : "LIVE EXECUTION") . "\n\n";

$avatari_dir = CORE_PATH . '/dati/bildes/avatari/';
$av_sm_dir = CORE_PATH . '/dati/bildes/av_sm/';
$topic_av_dir = CORE_PATH . '/dati/bildes/topic-av/';

if (!is_dir($avatari_dir) && !$dryRun) {
    mkdir($avatari_dir, 0777, true);
}
if (!is_dir($av_sm_dir) && !$dryRun) {
    mkdir($av_sm_dir, 0777, true);
}

/**
 * Helper to process an avatar into dati/bildes/avatari/<id>.jpg (max 17800 pixels)
 * and dati/bildes/av_sm/<id>.jpg (75x75 crop).
 */
function process_avatars($source_path, $article_id, $dryRun, $verbose, &$avatari_dir, &$av_sm_dir, &$topic_av_dir) {
    if (!file_exists($source_path)) {
        echo "  [ERROR] Source file does not exist: $source_path\n";
        return false;
    }

    if ($dryRun) {
        if ($verbose) {
            echo "  [DRY-RUN] Would process $source_path into avatars for article $article_id\n";
        }
        return true;
    }

    // 1. Large avatar (standard 17800 total pixels ratio)
    $foo = new Upload($source_path);
    $foo->image_max_pixels = 200000000;
    $foo->file_new_name_body = $article_id;
    $foo->image_resize = true;
    $foo->image_convert = 'jpg';
    $foo->allowed = ['image/*'];
    $foo->image_ratio = true;
    $foo->image_ratio_pixels = 17800;
    $foo->jpeg_quality = 98;
    $foo->image_ratio_no_zoom_in = true;
    $foo->file_auto_rename = false;
    $foo->file_overwrite = true;
    $foo->process($avatari_dir);

    if (!$foo->processed) {
        echo "  [ERROR] Failed to process avatar: " . $foo->error . "\n";
        return false;
    }

    // 2. Small avatar (75x75 crop)
    $foo = new Upload($source_path);
    $foo->image_max_pixels = 200000000;
    $foo->file_new_name_body = $article_id;
    $foo->image_resize = true;
    $foo->image_convert = 'jpg';
    $foo->image_x = 75;
    $foo->image_y = 75;
    $foo->allowed = ['image/*'];
    $foo->image_ratio_crop = true;
    $foo->jpeg_quality = 98;
    $foo->file_auto_rename = false;
    $foo->file_overwrite = true;
    $foo->process($av_sm_dir);

    if (!$foo->processed) {
        echo "  [ERROR] Failed to process sm_avatar: " . $foo->error . "\n";
        return false;
    }

    // Remove old topic avatar cache if present
    $cached_topic_av = $topic_av_dir . $article_id . '.jpg';
    if (file_exists($cached_topic_av)) {
        @unlink($cached_topic_av);
    }

    return true;
}

/**
 * Helper to generate sm_avatar (75x75 crop) when large avatar already exists.
 */
function process_sm_avatar_only($source_path, $article_id, $dryRun, $verbose, &$av_sm_dir, &$topic_av_dir) {
    if (!file_exists($source_path)) {
        echo "  [ERROR] Source file does not exist: $source_path\n";
        return false;
    }

    if ($dryRun) {
        if ($verbose) {
            echo "  [DRY-RUN] Would process $source_path into sm_avatar for article $article_id\n";
        }
        return true;
    }

    $foo = new Upload($source_path);
    $foo->image_max_pixels = 200000000;
    $foo->file_new_name_body = $article_id;
    $foo->image_resize = true;
    $foo->image_convert = 'jpg';
    $foo->image_x = 75;
    $foo->image_y = 75;
    $foo->allowed = ['image/*'];
    $foo->image_ratio_crop = true;
    $foo->jpeg_quality = 98;
    $foo->file_auto_rename = false;
    $foo->file_overwrite = true;
    $foo->process($av_sm_dir);

    if (!$foo->processed) {
        echo "  [ERROR] Failed to process sm_avatar: " . $foo->error . "\n";
        return false;
    }

    $cached_topic_av = $topic_av_dir . $article_id . '.jpg';
    if (file_exists($cached_topic_av)) {
        @unlink($cached_topic_av);
    }

    return true;
}

// ==========================================
// 1. FILMU APSKATI (Category 80)
// ==========================================
echo "--- Processing Filmu Apskati (Category 80) ---\n";

$movies_no_avatar = $db->get_results("
    SELECT p.id, p.title, p.strid, mi.image, mi.thb
    FROM pages p
    LEFT JOIN movie_images mi ON mi.page_id = p.id AND mi.main = 1
    WHERE p.category = 80 AND (p.avatar IS NULL OR p.avatar = '')
    ORDER BY p.id ASC
");

$count_movies_fixed = 0;
echo "Found " . count($movies_no_avatar) . " movie articles missing avatar.\n";

foreach ($movies_no_avatar as $m_article) {
    $src = '';
    if (!empty($m_article->image) && file_exists(IMG_PATH . $m_article->image)) {
        $src = IMG_PATH . $m_article->image;
    } elseif (!empty($m_article->thb) && file_exists(IMG_PATH . $m_article->thb)) {
        $src = IMG_PATH . $m_article->thb;
    } elseif (!empty($m_article->image) && file_exists(CORE_PATH . $m_article->image)) {
        $src = CORE_PATH . $m_article->image;
    }

    if (empty($src)) {
        echo "  [WARNING] No image source for movie {$m_article->id} ({$m_article->title})\n";
        continue;
    }

    if ($verbose) {
        echo "Processing movie {$m_article->id} ({$m_article->title}) from $src...\n";
    }

    $ok = process_avatars($src, $m_article->id, $dryRun, $verbose, $avatari_dir, $av_sm_dir, $topic_av_dir);
    if ($ok) {
        $av = 'dati/bildes/avatari/' . $m_article->id . '.jpg';
        $sm_av = 'dati/bildes/av_sm/' . $m_article->id . '.jpg';
        if (!$dryRun) {
            $db->query("UPDATE pages SET avatar = '$av', sm_avatar = '$sm_av' WHERE id = {$m_article->id} LIMIT 1");
        }
        $count_movies_fixed++;
    }
}
echo "Fixed $count_movies_fixed / " . count($movies_no_avatar) . " movie articles missing avatar.\n";

// Also fix movies that have avatar, but missing sm_avatar
$movies_no_sm = $db->get_results("
    SELECT id, title, avatar
    FROM pages
    WHERE category = 80 AND (avatar IS NOT NULL AND avatar != '') AND (sm_avatar IS NULL OR sm_avatar = '')
    ORDER BY id ASC
");

$count_movies_sm_fixed = 0;
echo "Found " . count($movies_no_sm) . " movie articles with avatar but missing sm_avatar.\n";
foreach ($movies_no_sm as $m_article) {
    $src = CORE_PATH . '/' . $m_article->avatar;
    if (!file_exists($src)) {
        $src = IMG_PATH . '/' . $m_article->avatar;
    }
    if (!file_exists($src)) {
        echo "  [WARNING] Avatar file not found for movie {$m_article->id}: {$m_article->avatar}\n";
        continue;
    }

    $ok = process_sm_avatar_only($src, $m_article->id, $dryRun, $verbose, $av_sm_dir, $topic_av_dir);
    if ($ok) {
        $sm_av = 'dati/bildes/av_sm/' . $m_article->id . '.jpg';
        if (!$dryRun) {
            $db->query("UPDATE pages SET sm_avatar = '$sm_av' WHERE id = {$m_article->id} LIMIT 1");
        }
        $count_movies_sm_fixed++;
    }
}
echo "Fixed $count_movies_sm_fixed / " . count($movies_no_sm) . " movie articles missing sm_avatar.\n\n";

// ==========================================
// 2. SPĒĻU APSKATI (Category 81)
// ==========================================
echo "--- Processing Spēļu Apskati (Category 81) ---\n";

$games_no_avatar = $db->get_results("
    SELECT id, title, strid, avatar, sm_avatar
    FROM pages
    WHERE category = 81 AND (avatar IS NULL OR avatar = '')
    ORDER BY id ASC
");

echo "Found " . count($games_no_avatar) . " game articles missing avatar.\n";
$count_games_fixed = 0;

foreach ($games_no_avatar as $g_article) {
    $src = null;

    if ($g_article->id == 61607) {
        // Super Meat Boy
        $candidate = IMG_PATH . '/s/h/shevijs/a_6.jpg';
        if (file_exists($candidate)) {
            $src = $candidate;
        } else {
            $candidate2 = IMG_PATH . '/shevijs/1_6.jpg';
            if (file_exists($candidate2)) {
                $src = $candidate2;
            }
        }
    } elseif ($g_article->id == 11641) {
        // Return to the Castle Wolfenstein: Enemy Territory
        $temp_wet = '/tmp/wet_' . uniqid() . '.jpg';
        $download_urls = [
            'https://small-games.info/s/l/w/Wolfenstein_Enemy_Territory_1.jpg',
            'https://upload.wikimedia.org/wikipedia/en/2/23/Wolfenstein_Enemy_Territory_cover.jpg'
        ];
        foreach ($download_urls as $d_url) {
            $cmd = "curl -s -L -A 'Mozilla/5.0' --connect-timeout 10 " . escapeshellarg($d_url) . " -o " . escapeshellarg($temp_wet);
            exec($cmd);
            if (file_exists($temp_wet) && filesize($temp_wet) > 1000) {
                $src = $temp_wet;
                break;
            }
        }
    } elseif ($g_article->id == 47788) {
        // Drīzumā gaidāmās spēles (1. daļa) - Batman Arkham City trailer thumbnail from the article
        $temp_batman = '/tmp/batman_' . uniqid() . '.jpg';
        $cmd = "curl -s -L --connect-timeout 10 'https://i.ytimg.com/vi/yXqj0rpyD2c/hqdefault.jpg' -o " . escapeshellarg($temp_batman);
        exec($cmd);
        if (file_exists($temp_batman) && filesize($temp_batman) > 1000) {
            $src = $temp_batman;
        }
    }

    if (empty($src) || !file_exists($src)) {
        echo "  [WARNING] No source image found for game {$g_article->id} ({$g_article->title})\n";
        continue;
    }

    echo "Processing game {$g_article->id} ({$g_article->title}) from $src...\n";
    $ok = process_avatars($src, $g_article->id, $dryRun, $verbose, $avatari_dir, $av_sm_dir, $topic_av_dir);

    // Clean up temporary downloaded files
    if (strpos($src, '/tmp/') === 0 && file_exists($src)) {
        @unlink($src);
    }

    if ($ok) {
        $av = 'dati/bildes/avatari/' . $g_article->id . '.jpg';
        $sm_av = 'dati/bildes/av_sm/' . $g_article->id . '.jpg';
        if (!$dryRun) {
            $db->query("UPDATE pages SET avatar = '$av', sm_avatar = '$sm_av' WHERE id = {$g_article->id} LIMIT 1");
        }
        $count_games_fixed++;
    }
}
echo "Fixed $count_games_fixed / " . count($games_no_avatar) . " game articles missing avatar.\n";

// Fix game articles that have avatar but missing sm_avatar
$games_no_sm = $db->get_results("
    SELECT id, title, avatar
    FROM pages
    WHERE category = 81 AND (avatar IS NOT NULL AND avatar != '') AND (sm_avatar IS NULL OR sm_avatar = '')
    ORDER BY id ASC
");

$count_games_sm_fixed = 0;
echo "Found " . count($games_no_sm) . " game articles with avatar but missing sm_avatar.\n";
foreach ($games_no_sm as $g_article) {
    $src = CORE_PATH . '/' . $g_article->avatar;
    if (!file_exists($src)) {
        $src = IMG_PATH . '/' . $g_article->avatar;
    }
    if (!file_exists($src)) {
        echo "  [WARNING] Avatar file not found for game {$g_article->id}: {$g_article->avatar}\n";
        continue;
    }

    $ok = process_sm_avatar_only($src, $g_article->id, $dryRun, $verbose, $av_sm_dir, $topic_av_dir);
    if ($ok) {
        $sm_av = 'dati/bildes/av_sm/' . $g_article->id . '.jpg';
        if (!$dryRun) {
            $db->query("UPDATE pages SET sm_avatar = '$sm_av' WHERE id = {$g_article->id} LIMIT 1");
        }
        $count_games_sm_fixed++;
    }
}
echo "Fixed $count_games_sm_fixed / " . count($games_no_sm) . " game articles missing sm_avatar.\n\n";

// ==========================================
// 3. CACHE CLEAR & FINAL STATS
// ==========================================
if (!$dryRun) {
    echo "Flushing Memcached cache...\n";
    $m->flush();
}

$speles_remaining_no_av = $db->get_var("SELECT count(*) FROM pages WHERE category = 81 AND (avatar IS NULL OR avatar = '')");
$speles_remaining_no_sm = $db->get_var("SELECT count(*) FROM pages WHERE category = 81 AND (sm_avatar IS NULL OR sm_avatar = '')");

$filmas_remaining_no_av = $db->get_var("SELECT count(*) FROM pages WHERE category = 80 AND (avatar IS NULL OR avatar = '')");
$filmas_remaining_no_sm = $db->get_var("SELECT count(*) FROM pages WHERE category = 80 AND (sm_avatar IS NULL OR sm_avatar = '')");

echo "=== Final Status ===\n";
echo "Spēļu apskati (cat 81): Total 222 | Missing avatar: $speles_remaining_no_av | Missing sm_avatar: $speles_remaining_no_sm\n";
echo "Filmu apskati (cat 80): Total 405 | Missing avatar: $filmas_remaining_no_av | Missing sm_avatar: $filmas_remaining_no_sm\n";
echo "Done!\n";
