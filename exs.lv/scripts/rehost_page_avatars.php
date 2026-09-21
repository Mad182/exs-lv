<?php

/**
 * rehost_page_avatars.php
 *
 * Scans articles (pages table) for remote avatar URLs (e.g. http://... or https://...),
 * downloads the images and rehosts them locally as:
 *   - dati/bildes/avatari/<id>.jpg (max 17800 pixels ratio)
 *   - dati/bildes/av_sm/<id>.jpg (75x75 crop)
 *
 * If the remote URL is unreachable (even after checking Wayback Machine archive),
 * unsets the avatar (avatar = '', sm_avatar = '').
 *
 * Usage:
 *   php rehost_page_avatars.php [--dry-run] [--verbose]
 */

if (php_sapi_name() !== 'cli') {
    die("CLI only.\n");
}

@ini_set('output_buffering', 'off');
@ini_set('zlib.output_compression', false);
@ob_implicit_flush(true);
while (ob_get_level()) {
    @ob_end_flush();
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
if (defined('Memcached::HAVE_IGBINARY') && Memcached::HAVE_IGBINARY) {
    $m->setOption(Memcached::OPT_SERIALIZER, Memcached::SERIALIZER_IGBINARY);
}
$m->addServer($mc_host, $mc_port);

$options = getopt('', ['dry-run', 'verbose', 'help', 'id:']);
if (isset($options['help'])) {
    echo "Usage: php rehost_page_avatars.php [options]\n";
    echo "Options:\n";
    echo "  --dry-run      Inspect remote URLs without downloading or modifying DB\n";
    echo "  --verbose      Display detailed debug progress\n";
    echo "  --id=N         Process single page ID only\n";
    echo "  --help         Show this help message\n";
    exit(0);
}

$dryRun = isset($options['dry-run']);
$verbose = isset($options['verbose']);
$singleId = isset($options['id']) ? (int)$options['id'] : 0;

echo "=======================================================\n";
echo "   EXS.LV Page Remote Avatar Rehost & Cleanup\n";
echo "   Mode: " . ($dryRun ? "DRY-RUN (no files or DB changed)" : "LIVE EXECUTION") . "\n";
echo "=======================================================\n\n";

$avatari_dir = CORE_PATH . '/dati/bildes/avatari/';
$av_sm_dir = CORE_PATH . '/dati/bildes/av_sm/';
$topic_av_dir = CORE_PATH . '/dati/bildes/topic-av/';

if (!is_dir($avatari_dir) && !$dryRun) {
    @mkdir($avatari_dir, 0777, true);
}
if (!is_dir($av_sm_dir) && !$dryRun) {
    @mkdir($av_sm_dir, 0777, true);
}

/**
 * Downloads a URL to a local temporary file using cURL.
 */
function download_url_to_temp($url, $timeout = 10, $connect_timeout = 5) {
    $tmp_file = tempnam(sys_get_temp_dir(), 'exs_av_');
    $fp = fopen($tmp_file, 'wb');
    if (!$fp) {
        return ['ok' => false, 'error' => 'Cannot open temp file'];
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_FILE => $fp,
        CURLOPT_HEADER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 5,
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_CONNECTTIMEOUT => $connect_timeout,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36'
    ]);

    $exec_ok = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $mime = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    $curl_err = curl_error($ch);
    @curl_close($ch);
    fclose($fp);

    if (!$exec_ok || $http_code < 200 || $http_code >= 400 || filesize($tmp_file) < 100) {
        @unlink($tmp_file);
        return [
            'ok' => false,
            'http_code' => $http_code,
            'error' => $curl_err ?: ("HTTP $http_code")
        ];
    }

    // Verify it is actually an image
    $img_info = @getimagesize($tmp_file);
    if ($img_info === false) {
        @unlink($tmp_file);
        return [
            'ok' => false,
            'http_code' => $http_code,
            'error' => 'Downloaded file is not a valid image'
        ];
    }

    return [
        'ok' => true,
        'tmp_file' => $tmp_file,
        'mime' => $mime,
        'http_code' => $http_code,
        'img_info' => $img_info
    ];
}

/**
 * Attempts to retrieve an image directly or via Internet Archive Wayback Machine.
 */
function fetch_avatar_image($raw_url, $verbose = false) {
    $clean_url = trim($raw_url);
    if (strpos($clean_url, '//') === 0) {
        $clean_url = 'https:' . $clean_url;
    }

    // Check for nested original URL inside Google Image thumbnails (e.g. q=tbn:...:http...)
    $nested_url = null;
    if (preg_match('~:https?://[^\s&#]+~i', $clean_url, $m)) {
        $nested_url = substr($m[0], 1);
    }

    // 1. Try direct fetch
    if ($verbose) {
        echo "    Trying direct fetch: $clean_url\n";
    }
    $res = download_url_to_temp($clean_url, 8, 4);
    if ($res['ok']) {
        $res['source'] = 'direct';
        return $res;
    }

    // 2. If nested URL exists, try direct fetch of nested URL
    if (!empty($nested_url) && $nested_url !== $clean_url) {
        if ($verbose) {
            echo "    Trying nested direct fetch: $nested_url\n";
        }
        $res_nested = download_url_to_temp($nested_url, 8, 4);
        if ($res_nested['ok']) {
            $res_nested['source'] = 'nested-direct';
            return $res_nested;
        }
    }

    // 3. Try Wayback Machine for original URL
    if (strpos($clean_url, 'web.archive.org') === false) {
        $wb_url = 'https://web.archive.org/web/0id_/' . $clean_url;
        if ($verbose) {
            echo "    Direct failed ({$res['error']}). Trying Wayback: $wb_url\n";
        }
        $res_wb = download_url_to_temp($wb_url, 8, 4);
        if ($res_wb['ok']) {
            $res_wb['source'] = 'wayback';
            return $res_wb;
        }
    }

    // 4. Try Wayback Machine for nested URL
    if (!empty($nested_url) && $nested_url !== $clean_url) {
        $wb_nested = 'https://web.archive.org/web/0id_/' . $nested_url;
        if ($verbose) {
            echo "    Trying Wayback for nested URL: $wb_nested\n";
        }
        $res_wb_nested = download_url_to_temp($wb_nested, 8, 4);
        if ($res_wb_nested['ok']) {
            $res_wb_nested['source'] = 'nested-wayback';
            return $res_wb_nested;
        }
    }

    return [
        'ok' => false,
        'error' => $res['error'] ?? 'Unreachable'
    ];
}

/**
 * Processes a downloaded local image into avatars and updates disk.
 */
function process_and_save_avatars($source_tmp_path, $article_id, &$avatari_dir, &$av_sm_dir, &$topic_av_dir) {
    // 1. Large avatar (max 17800 total pixels ratio)
    $foo = new Upload($source_tmp_path);
    $foo->image_max_pixels = 200000000;
    $foo->file_new_name_body = (string)$article_id;
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
        echo "    [ERROR] Failed to process large avatar: " . $foo->error . "\n";
        return false;
    }

    // 2. Small avatar (75x75 crop)
    $foo = new Upload($source_tmp_path);
    $foo->image_max_pixels = 200000000;
    $foo->file_new_name_body = (string)$article_id;
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
        echo "    [ERROR] Failed to process sm_avatar: " . $foo->error . "\n";
        return false;
    }

    // Invalidate cached topic avatar if present
    $cached_topic_av = $topic_av_dir . $article_id . '.jpg';
    if (file_exists($cached_topic_av)) {
        @unlink($cached_topic_av);
    }

    // Set file permissions if exs user exists
    $target_large = $avatari_dir . $article_id . '.jpg';
    $target_small = $av_sm_dir . $article_id . '.jpg';
    @chown($target_large, 'exs');
    @chgrp($target_large, 'exs');
    @chmod($target_large, 0664);
    @chown($target_small, 'exs');
    @chgrp($target_small, 'exs');
    @chmod($target_small, 0664);

    return true;
}

// Build query
$whereSql = "(avatar LIKE '%://%' OR sm_avatar LIKE '%://%' OR avatar LIKE '//%' OR sm_avatar LIKE '//%')";
if ($singleId > 0) {
    $whereSql .= " AND id = $singleId";
}

$candidates = $db->get_results("
    SELECT id, title, strid, avatar, sm_avatar
    FROM pages
    WHERE $whereSql
    ORDER BY id ASC
");

$total = count($candidates ?: []);
echo "Found $total page(s) with remote avatar URLs.\n\n";

if ($total === 0) {
    echo "Nothing to process.\n";
    exit(0);
}

$rehosted_count = 0;
$unset_count = 0;
$failed_process_count = 0;

foreach ($candidates as $idx => $row) {
    $num = $idx + 1;
    $url = !empty($row->avatar) && (strpos($row->avatar, '://') !== false || strpos($row->avatar, '//') === 0)
        ? $row->avatar
        : $row->sm_avatar;

    echo "[$num/$total] #{$row->id} \"{$row->title}\"\n";
    echo "  Remote URL: $url\n";

    if ($dryRun) {
        // Quick probe in dry-run mode
        $probe = fetch_avatar_image($url, $verbose);
        if ($probe['ok']) {
            echo "  [DRY-RUN] -> REACHABLE ({$probe['source']}). Would rehost to dati/bildes/avatari/{$row->id}.jpg\n";
            @unlink($probe['tmp_file']);
            $rehosted_count++;
        } else {
            echo "  [DRY-RUN] -> UNREACHABLE ({$probe['error']}). Would UNSET avatar\n";
            $unset_count++;
        }
        continue;
    }

    // Live execution: Fetch image
    $fetch_res = fetch_avatar_image($url, $verbose);

    if ($fetch_res['ok']) {
        echo "  -> FETCH SUCCESS ({$fetch_res['source']})\n";
        $ok = process_and_save_avatars($fetch_res['tmp_file'], $row->id, $avatari_dir, $av_sm_dir, $topic_av_dir);
        @unlink($fetch_res['tmp_file']);

        if ($ok) {
            $new_av = 'dati/bildes/avatari/' . $row->id . '.jpg';
            $new_sm = 'dati/bildes/av_sm/' . $row->id . '.jpg';
            $db->query("UPDATE pages SET avatar = '$new_av', sm_avatar = '$new_sm' WHERE id = {$row->id} LIMIT 1");
            echo "  -> REHOSTED & UPDATED: avatar='$new_av', sm_avatar='$new_sm'\n";
            $rehosted_count++;
        } else {
            // Failed to process into avatars; unset to avoid broken state
            $db->query("UPDATE pages SET avatar = '', sm_avatar = '' WHERE id = {$row->id} LIMIT 1");
            echo "  -> PROCESSING FAILED. Avatar UNSET\n";
            $failed_process_count++;
        }
    } else {
        // Unreachable: leave avatar unset
        echo "  -> UNREACHABLE ({$fetch_res['error']}). Unsetting avatar...\n";
        $db->query("UPDATE pages SET avatar = '', sm_avatar = '' WHERE id = {$row->id} LIMIT 1");
        echo "  -> UPDATED: avatar='', sm_avatar=''\n";
        $unset_count++;
    }
}

if (!$dryRun) {
    echo "\nFlushing Memcached...\n";
    $m->flush();
}

echo "\n=======================================================\n";
echo "   Summary\n";
echo "=======================================================\n";
echo "Total processed:      $total\n";
echo "Successfully rehosted: $rehosted_count\n";
echo "Unset (unreachable):   $unset_count\n";
if ($failed_process_count > 0) {
    echo "Processing failed:     $failed_process_count\n";
}
echo "Done!\n";
