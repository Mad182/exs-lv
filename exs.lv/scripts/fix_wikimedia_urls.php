<?php

/**
 * fix_wikimedia_urls.php
 *
 * Scans articles (pages table) for broken upload.wikimedia.org thumbnail URLs
 * (which now return HTTP 400 "Use thumbnail sizes listed on https://w.wiki/GHai").
 *
 * Resolves each URL by:
 *   1. Checking candidate standard Wikimedia sizes (20, 40, 60, 120, 250, 330, 500, 960, 1280).
 *   2. Checking Wikimedia Commons if file was moved from wikipedia/en to wikipedia/commons.
 *   3. Falling back to Wayback Machine (web.archive.org) and rehosting to img.exs.lv/wikimedia/...
 *
 * Usage:
 *   php fix_wikimedia_urls.php [--dry-run] [--verbose]
 *   php fix_wikimedia_urls.php --page=61336 [--dry-run]
 *   php fix_wikimedia_urls.php --stats
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
$db = new mdb($username, $password, $database, $hostname);

// Parse CLI options
$options = getopt('', [
    'page:',      // Specific page ID
    'limit:',     // Process first N pages
    'dry-run',    // Preview mode
    'verbose',    // Verbose debug logging
    'help'        // Show help
]);

if (isset($options['help'])) {
    echo "Usage:\n";
    echo "  php fix_wikimedia_urls.php [--dry-run] [--verbose]\n";
    echo "  php fix_wikimedia_urls.php --page=<id> [--dry-run] [--verbose]\n";
    echo "  php fix_wikimedia_urls.php --limit=<n>\n";
    exit(0);
}

$is_dry_run = isset($options['dry-run']);
$is_verbose = isset($options['verbose']);
$single_page_id = isset($options['page']) ? (int) $options['page'] : null;
$limit = isset($options['limit']) ? (int) $options['limit'] : null;

// Wikimedia allowed standard steps
$standard_steps = [20, 40, 60, 120, 250, 330, 500, 960, 1280, 1920, 3840];

$storage_base = IMG_PATH . '/wikimedia';
$target_url_base = 'https://img.exs.lv/wikimedia';

if (!is_dir($storage_base) && !$is_dry_run) {
    @mkdir($storage_base, 0775, true);
}

echo "=== Wikimedia Thumbnail URL Restorer ===\n";
echo "Mode: " . ($is_dry_run ? "DRY RUN (no modifications)" : "LIVE EXECUTION") . "\n";
echo "Storage Base (for archived): $storage_base\n\n";

function get_curl_head_handle() {
    static $ch = null;
    if ($ch === null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) EXS-LV-Bot/1.0");
        curl_setopt($ch, CURLOPT_TCP_KEEPALIVE, 1);
    }
    return $ch;
}

function check_url_status($url) {
    $ch = get_curl_head_handle();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_exec($ch);
    return curl_getinfo($ch, CURLINFO_HTTP_CODE);
}

function fetch_wayback_image($url) {
    $wayback_url = "https://web.archive.org/web/0id_/" . $url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $wayback_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64)");
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $effective_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);

    if ($code == 200 && !empty($body)) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($body);
        if (strpos($mime, 'image/') === 0) {
            return [
                'ok' => true,
                'data' => $body,
                'mime' => $mime,
                'size' => strlen($body),
                'wayback_url' => $effective_url
            ];
        }
    }
    return ['ok' => false];
}

function get_candidate_sizes($orig_size, $standard_steps) {
    $candidates = $standard_steps;
    usort($candidates, function($a, $b) use ($orig_size) {
        return abs($a - $orig_size) <=> abs($b - $orig_size);
    });
    return $candidates;
}

function clean_wikimedia_url($raw_url) {
    $url = html_entity_decode($raw_url, ENT_QUOTES, 'UTF-8');
    $url = preg_replace('/(%5C|%C2%A0|\xc2\xa0)+$/i', '', $url);
    $url = trim($url, " \t\n\r\0\x0B.,;:!?)'\"[]<>\\");
    return $url;
}

// Query pages
if ($single_page_id) {
    $pages = $db->get_results("SELECT id, title, intro, text FROM `pages` WHERE `id` = $single_page_id");
} else {
    $limit_sql = $limit ? "LIMIT $limit" : "";
    $pages = $db->get_results("
        SELECT id, title, intro, text 
        FROM `pages` 
        WHERE `text` LIKE '%upload.wikimedia.org%' OR `intro` LIKE '%upload.wikimedia.org%' 
        ORDER BY `id` ASC 
        $limit_sql
    ");
}

if (empty($pages)) {
    echo "No matching pages found.\n";
    exit(0);
}

echo "Found " . count($pages) . " page(s) with upload.wikimedia.org to inspect.\n\n";

// URL resolution cache
$url_cache = []; // [old_url => new_url]

$stats = [
    'pages_total' => count($pages),
    'pages_updated' => 0,
    'urls_found' => 0,
    'urls_already_ok' => 0,
    'urls_resized' => 0,
    'urls_moved_commons' => 0,
    'urls_archived' => 0,
    'urls_failed' => 0,
];

foreach ($pages as $p_idx => $page) {
    $page_num = $p_idx + 1;
    $combined = $page->intro . ' ' . $page->text;

    if (!preg_match_all('#https?://upload\.wikimedia\.org/[^\s"\'<>\[\]()]+#i', $combined, $matches)) {
        continue;
    }

    $raw_urls = array_unique($matches[0]);
    $replacements = [];

    if ($is_verbose) {
        echo "[$page_num/{$stats['pages_total']}] Page #{$page->id} \"{$page->title}\": " . count($raw_urls) . " URL(s)\n";
    }

    foreach ($raw_urls as $raw_url) {
        $clean_url = clean_wikimedia_url($raw_url);
        $stats['urls_found']++;

        if (isset($url_cache[$clean_url])) {
            if ($url_cache[$clean_url] !== false && $url_cache[$clean_url] !== $clean_url) {
                $replacements[$clean_url] = $url_cache[$clean_url];
            }
            continue;
        }

        echo "  [PROBING] $clean_url ...\n";

        // 1. Check if current URL is already working
        $current_code = check_url_status($clean_url);
        if ($current_code == 200) {
            echo "    -> Already HTTP 200 (OK)\n";
            $stats['urls_already_ok']++;
            $url_cache[$clean_url] = $clean_url;
            continue;
        }

        $resolved_url = null;

        // 2. If it's a thumbnail, try standard sizes
        if (preg_match('#/(\d+)px-([^/]+)$#i', $clean_url, $sm)) {
            $orig_size = (int)$sm[1];
            $candidates = get_candidate_sizes($orig_size, $standard_steps);

            foreach ($candidates as $cand_size) {
                $candidate_url = preg_replace('#/\d+px-([^/]+)$#i', "/{$cand_size}px-$1", $clean_url);
                if (check_url_status($candidate_url) == 200) {
                    $resolved_url = $candidate_url;
                    echo "    -> FIXED SIZE: {$orig_size}px -> {$cand_size}px\n";
                    echo "       $resolved_url\n";
                    $stats['urls_resized']++;
                    break;
                }
                usleep(30000);
            }

            // 3. Try Wikimedia Commons if wikipedia/en/
            if (!$resolved_url && strpos($clean_url, '/wikipedia/en/thumb/') !== false) {
                $commons_base = str_replace('/wikipedia/en/thumb/', '/wikipedia/commons/thumb/', $clean_url);
                foreach ($candidates as $cand_size) {
                    $candidate_url = preg_replace('#/\d+px-([^/]+)$#i', "/{$cand_size}px-$1", $commons_base);
                    if (check_url_status($candidate_url) == 200) {
                        $resolved_url = $candidate_url;
                        echo "    -> MOVED TO COMMONS: {$cand_size}px\n";
                        echo "       $resolved_url\n";
                        $stats['urls_moved_commons']++;
                        break;
                    }
                    usleep(30000);
                }
            }
        }

        // 4. If still unresolved, try Wayback Machine
        if (!$resolved_url) {
            echo "    -> Querying Wayback Machine...\n";
            $wb_candidates = [$clean_url];

            // If thumbnail, also try original unthumbnailed URL
            // e.g. /wikipedia/en/thumb/0/09/file.jpg/600px-file.jpg -> /wikipedia/en/0/09/file.jpg
            if (preg_match('#(https?://upload\.wikimedia\.org/wikipedia/[^/]+)/thumb/(.+)/\d+px-[^/]+$#i', $clean_url, $um)) {
                $wb_candidates[] = $um[1] . '/' . $um[2];
            }

            foreach ($wb_candidates as $probe_wb) {
                $wb_res = fetch_wayback_image($probe_wb);
                if ($wb_res['ok']) {
                    $filename = basename(parse_url($probe_wb, PHP_URL_PATH));
                    $rel_path = preg_replace('#^https?://upload\.wikimedia\.org/wikipedia/#i', '', $probe_wb);
                    // sanitize rel path
                    $rel_path = ltrim(preg_replace('#/thumb/#i', '/', $rel_path), '/');
                    $dest_file = $storage_base . '/' . $rel_path;
                    $rehosted_url = $target_url_base . '/' . $rel_path;

                    if (!$is_dry_run) {
                        $dir = dirname($dest_file);
                        if (!is_dir($dir)) {
                            @mkdir($dir, 0775, true);
                        }
                        file_put_contents($dest_file, $wb_res['data']);
                        @chmod($dest_file, 0664);
                    }

                    $resolved_url = $rehosted_url;
                    echo "    -> RECOVERED via Wayback Machine (" . round($wb_res['size'] / 1024, 1) . " KB)\n";
                    echo "       $resolved_url\n";
                    $stats['urls_archived']++;
                    break;
                }
            }
        }

        if ($resolved_url) {
            $url_cache[$clean_url] = $resolved_url;
            $replacements[$clean_url] = $resolved_url;
        } else {
            echo "    -> FAILED to resolve URL\n";
            $stats['urls_failed']++;
            $url_cache[$clean_url] = false;
        }
    }

    if (!empty($replacements)) {
        $updated_intro = $page->intro;
        $updated_text = $page->text;

        foreach ($replacements as $old_url => $new_url) {
            $updated_intro = str_replace($old_url, $new_url, $updated_intro);
            $updated_text = str_replace($old_url, $new_url, $updated_text);

            $escaped_old = preg_quote($old_url, '#');
            $updated_intro = preg_replace('#' . $escaped_old . '(%5C|%C2%A0|\xc2\xa0)?#i', $new_url, $updated_intro);
            $updated_text = preg_replace('#' . $escaped_old . '(%5C|%C2%A0|\xc2\xa0)?#i', $new_url, $updated_text);
        }

        if ($updated_intro !== $page->intro || $updated_text !== $page->text) {
            $stats['pages_updated']++;
            echo "  => [UPDATE PAGE #{$page->id}] Replaced " . count($replacements) . " URL(s) in \"{$page->title}\"\n";

            if (!$is_dry_run) {
                $clean_text = $db->real_escape_string($updated_text);
                $clean_intro = $db->real_escape_string($updated_intro);
                $db->query("
                    UPDATE `pages` 
                    SET `text` = '$clean_text', `intro` = '$clean_intro' 
                    WHERE `id` = {$page->id}
                    LIMIT 1
                ");
            }
        }
    }
    echo "\n";
}

echo "=== Migration Summary ===\n";
echo "Pages Processed:      {$stats['pages_total']}\n";
echo "Pages Updated:        {$stats['pages_updated']}\n";
echo "URLs Inspected:       {$stats['urls_found']}\n";
echo "URLs Already OK:      {$stats['urls_already_ok']}\n";
echo "URLs Size Adjusted:   {$stats['urls_resized']}\n";
echo "URLs Moved to Commons:{$stats['urls_moved_commons']}\n";
echo "URLs Rehosted (WB):   {$stats['urls_archived']}\n";
echo "URLs Unresolved:      {$stats['urls_failed']}\n";
echo "Done!\n";
