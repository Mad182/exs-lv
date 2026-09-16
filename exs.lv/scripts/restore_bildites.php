<?php

/**
 * restore_bildites.php
 *
 * Scans articles (pages table) for broken bildites.lv image URLs,
 * attempts to recover them from the Wayback Machine (Internet Archive),
 * rehosts recovered images to img.exs.lv, and updates the database records.
 *
 * Usage:
 *   php restore_bildites.php --page=69295 [--dry-run] [--verbose]
 *   php restore_bildites.php --limit=10 [--dry-run] [--verbose]
 *   php restore_bildites.php --all [--dry-run] [--delay=250]
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
    'all',        // Process all pages
    'dry-run',    // Do not download images or update DB
    'delay:',     // Milliseconds delay between Wayback requests (default: 400)
    'state-file:',// Custom path to state JSON file
    'verbose',    // Verbose debug logging
    'stats',      // Display stats from state file and exit
    'reset-404',  // Clear all cached not_found entries to re-evaluate them
    'help'        // Show help
]);

if (isset($options['help'])) {
    echo "Usage:\n";
    echo "  php restore_bildites.php --page=<id> [--dry-run] [--verbose]\n";
    echo "  php restore_bildites.php --limit=<n> [--dry-run] [--verbose]\n";
    echo "  php restore_bildites.php --all [--dry-run] [--delay=400]\n";
    echo "  php restore_bildites.php --stats\n";
    echo "  php restore_bildites.php --reset-404\n";
    exit(0);
}

$is_dry_run = isset($options['dry-run']);
$is_verbose = isset($options['verbose']);
$single_page_id = isset($options['page']) ? (int) $options['page'] : null;
$limit = isset($options['limit']) ? (int) $options['limit'] : null;
$delay_ms = isset($options['delay']) ? (int) $options['delay'] : 400;

// State tracking file
$state_dir = __DIR__ . '/data';
if (!is_dir($state_dir)) {
    @mkdir($state_dir, 0775, true);
}
$state_file = $options['state-file'] ?? ($state_dir . '/bildites_state.json');

$state = [];
if (file_exists($state_file)) {
    $raw_state = file_get_contents($state_file);
    if ($raw_state) {
        $decoded = json_decode($raw_state, true);
        if (is_array($decoded)) {
            $state = $decoded;
        }
    }
}

if (isset($options['reset-404'])) {
    $before_count = count($state);
    foreach ($state as $k => $v) {
        if (($v['status'] ?? '') === 'not_found') {
            unset($state[$k]);
        }
    }
    file_put_contents($state_file, json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    $after_count = count($state);
    echo "Reset complete. Removed " . ($before_count - $after_count) . " not_found entries. Remaining: $after_count\n";
    exit(0);
}

if (isset($options['stats'])) {
    echo "=== Bildites State Summary ===\n";
    echo "State File: $state_file\n";
    echo "Total URLs Tracked: " . count($state) . "\n";
    $counts = array_count_values(array_column($state, 'status'));
    foreach ($counts as $st => $c) {
        echo "  - $st: $c\n";
    }
    exit(0);
}

function save_state($file, &$state) {
    file_put_contents($file, json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

// Storage target configuration
$storage_base = IMG_PATH . '/bildites';
$target_url_base = 'https://img.exs.lv/bildites';

echo "=== Bildites.lv Image Restorer ===\n";
echo "Mode: " . ($is_dry_run ? "DRY RUN (no modifications)" : "LIVE EXECUTION") . "\n";
echo "Storage Base: $storage_base\n";
echo "Target Base URL: $target_url_base\n";
echo "State File: $state_file (" . count($state) . " cached URLs)\n\n";

// Query articles to process
if ($single_page_id) {
    $pages = $db->get_results("SELECT id, title, intro, text FROM `pages` WHERE `id` = $single_page_id");
} else {
    $limit_sql = $limit ? "LIMIT $limit" : "";
    $pages = $db->get_results("
        SELECT id, title, intro, text 
        FROM `pages` 
        WHERE `text` LIKE '%bildites.lv%' OR `intro` LIKE '%bildites.lv%' 
        ORDER BY `id` ASC 
        $limit_sql
    ");
}

if (empty($pages)) {
    echo "No matching pages found.\n";
    exit(0);
}

echo "Found " . count($pages) . " page(s) to inspect.\n";

/**
 * Clean & normalize a raw extracted URL from text.
 */
function clean_bildites_url($raw_url) {
    // Decode HTML entities
    $url = html_entity_decode($raw_url, ENT_QUOTES, 'UTF-8');
    // Strip common trailing punctuation/noise: . , ; : ! ? ) ] \ " ' \xc2\xa0 %5C %C2%A0
    $url = preg_replace('/(%5C|%C2%A0|\xc2\xa0)+$/i', '', $url);
    $url = trim($url, " \t\n\r\0\x0B.,;:!?)'\"[]<>\\");
    return $url;
}

/**
 * Extract path and determine destination relative filename.
 * Supports:
 *   /images/<filename.ext>
 *   /images/<hash>/<id>/<filename.ext>
 *   /viewer.php?file=<filename.ext>
 */
function parse_bildites_target($url) {
    $parsed = parse_url($url);
    if (!$parsed || empty($parsed['host'])) {
        return null;
    }
    
    $path = $parsed['path'] ?? '';
    
    // Check if it's viewer.php?file=...
    if (strpos($path, 'viewer.php') !== false) {
        parse_str($parsed['query'] ?? '', $query);
        if (!empty($query['file'])) {
            $file = basename($query['file']);
            // strip noise from file param
            $file = preg_replace('/(%5C|%C2%A0|\xc2\xa0)+$/i', '', $file);
            $file = trim($file, " \t\n\r.,;:!?)'\"[]<>\\");
            if (preg_match('/\.(jpg|jpeg|png|gif|bmp|webp)$/i', $file)) {
                return [
                    'type' => 'viewer',
                    'rel_path' => 'images/' . $file,
                    'probe_urls' => [
                        "https://bildites.lv/images/$file",
                        "http://bildites.lv/images/$file",
                    ]
                ];
            }
        }
    }
    
    // Direct image URL (/images/...)
    if (preg_match('#^/images/(.+)$#i', $path, $m)) {
        $subpath = $m[1];
        // strip noise
        $subpath = preg_replace('/(%5C|%C2%A0|\xc2\xa0)+$/i', '', $subpath);
        $subpath = trim($subpath, " \t\n\r.,;:!?)'\"[]<>\\");
        if (preg_match('/\.(jpg|jpeg|png|gif|bmp|webp)$/i', $subpath)) {
            $variants = [$url];
            $apex_url = "http://bildites.lv/images/" . $subpath;
            if ($apex_url !== $url) {
                $variants[] = $apex_url;
            }
            // If thumb, also check if full exists
            if (strpos($subpath, '_thumb.') !== false) {
                $non_thumb = str_replace('_thumb.', '.', $subpath);
                $variants[] = "https://bildites.lv/images/" . $non_thumb;
            }

            return [
                'type' => 'image',
                'rel_path' => 'images/' . $subpath,
                'probe_urls' => array_values(array_unique($variants))
            ];
        }
    }
    
    return null;
}

/**
 * Fetch image bytes from Wayback Machine with backoff & retry.
 */
function fetch_from_wayback($url, $delay_ms) {
    $wayback_url = "https://web.archive.org/web/0id_/" . $url;
    $max_retries = 3;
    $http_code = 0;
    $content_type = '';
    $effective_url = '';
    $curl_err = '';

    for ($attempt = 1; $attempt <= $max_retries; $attempt++) {
        $ch = curl_init($wayback_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 25);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36");
        
        $body = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $effective_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $curl_err = curl_error($ch);
        
        if ($delay_ms > 0) {
            usleep($delay_ms * 1000);
        }
        
        // Success case
        if ($http_code == 200 && !empty($body)) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $detected_mime = $finfo->buffer($body);
            if (strpos($detected_mime, 'image/') === 0) {
                return [
                    'ok' => true,
                    'data' => $body,
                    'mime' => $detected_mime,
                    'size' => strlen($body),
                    'wayback_url' => $effective_url,
                    'http_code' => 200
                ];
            }
        }
        
        // Rate limit (429) or temporary server error (500-504) or timeout/reset (0)
        if ($http_code == 429 || ($http_code >= 500 && $http_code <= 504) || $http_code == 0) {
            $backoff_sec = $attempt * 4;
            echo "    [WAIT] Wayback returned HTTP $http_code (" . ($curl_err ?: 'throttled') . "). Backing off {$backoff_sec}s (attempt $attempt/$max_retries)...\n";
            sleep($backoff_sec);
            continue;
        }
        
        // Definite 404 or other 4xx: do not retry this URL
        break;
    }
    
    return [
        'ok' => false,
        'http_code' => $http_code,
        'mime' => $content_type,
        'error' => $curl_err
    ];
}

// Processing counters
$stats = [
    'pages_total' => count($pages),
    'pages_updated' => 0,
    'urls_found' => 0,
    'urls_recovered' => 0,
    'urls_cached_ok' => 0,
    'urls_cached_fail' => 0,
    'urls_failed' => 0,
];

$state_dirty = false;
$batch_save_counter = 0;

foreach ($pages as $p_idx => $page) {
    $page_num = $p_idx + 1;
    $content_combined = $page->intro . ' ' . $page->text;
    
    // Find all bildites.lv URLs
    if (!preg_match_all('#https?://[^\s"\'<>\[\]()]*bildites\.lv[^\s"\'<>\[\]()]*#i', $content_combined, $matches)) {
        continue;
    }
    
    $raw_urls = array_unique($matches[0]);
    $replacements = []; // [clean_url => new_url]
    
    if ($is_verbose) {
        echo "[$page_num/{$stats['pages_total']}] Page #{$page->id} \"{$page->title}\": found " . count($raw_urls) . " raw bildites URL(s)\n";
    }
    
    foreach ($raw_urls as $raw_url) {
        $clean_url = clean_bildites_url($raw_url);
        $target_info = parse_bildites_target($clean_url);
        
        if (!$target_info) {
            if ($is_verbose) {
                echo "  [SKIP] Unsupported format: $clean_url\n";
            }
            continue;
        }
        
        $stats['urls_found']++;
        $rel_path = $target_info['rel_path'];
        $dest_file = $storage_base . '/' . $rel_path;
        $new_url = $target_url_base . '/' . $rel_path;
        
        // 1. Check if the local file already exists and is non-empty
        if (file_exists($dest_file) && filesize($dest_file) > 100) {
            $replacements[$clean_url] = $new_url;
            $stats['urls_cached_ok']++;
            $state[$clean_url] = [
                'status' => 'exists',
                'rel_path' => $rel_path,
                'new_url' => $new_url,
                'time' => date('Y-m-d H:i:s')
            ];
            $state_dirty = true;
            if ($is_verbose) {
                echo "  [FOUND LOCALLY] $clean_url -> $new_url\n";
            }
            continue;
        }
        
        // 2. Check if we have already recorded a 404/not_found in our state
        if (isset($state[$clean_url]) && $state[$clean_url]['status'] === 'not_found') {
            $stats['urls_cached_fail']++;
            if ($is_verbose) {
                echo "  [PREVIOUS 404] $clean_url\n";
            }
            continue;
        }
        
        // 3. Query Wayback Machine with probe URLs
        $success = false;
        $last_http_code = 0;
        echo "  [QUERYING WAYBACK] $clean_url ...\n";
        
        foreach ($target_info['probe_urls'] as $probe_url) {
            $res = fetch_from_wayback($probe_url, $delay_ms);
            $last_http_code = $res['http_code'] ?? 0;
            if ($res['ok']) {
                $success = true;
                echo "    -> RECOVERED via {$res['wayback_url']} ({$res['mime']}, " . round($res['size'] / 1024, 1) . " KB)\n";
                
                if (!$is_dry_run) {
                    $dir = dirname($dest_file);
                    if (!is_dir($dir)) {
                        @mkdir($dir, 0775, true);
                    }
                    file_put_contents($dest_file, $res['data']);
                    @chmod($dest_file, 0664);
                }
                
                $replacements[$clean_url] = $new_url;
                $stats['urls_recovered']++;
                
                $state[$clean_url] = [
                    'status' => 'recovered',
                    'rel_path' => $rel_path,
                    'new_url' => $new_url,
                    'wayback_url' => $res['wayback_url'],
                    'size' => $res['size'],
                    'mime' => $res['mime'],
                    'time' => date('Y-m-d H:i:s')
                ];
                $state_dirty = true;
                break;
            }
        }
        
        if (!$success) {
            if ($last_http_code == 404) {
                echo "    -> NOT FOUND (404) in Wayback Machine\n";
                $stats['urls_failed']++;
                $state[$clean_url] = [
                    'status' => 'not_found',
                    'time' => date('Y-m-d H:i:s')
                ];
                $state_dirty = true;
            } else {
                echo "    -> TEMPORARY ERROR (HTTP $last_http_code) in Wayback Machine (will retry on next run)\n";
                $stats['urls_failed']++;
            }
        }
        
        $batch_save_counter++;
        if ($batch_save_counter % 10 === 0 && $state_dirty) {
            save_state($state_file, $state);
            $state_dirty = false;
        }
    }
    
    // If we have replacements for this page, update it
    if (!empty($replacements)) {
        $updated_intro = $page->intro;
        $updated_text = $page->text;
        
        foreach ($replacements as $old_url => $new_url) {
            // Replace exact URL
            $updated_intro = str_replace($old_url, $new_url, $updated_intro);
            $updated_text = str_replace($old_url, $new_url, $updated_text);
            
            // Also handle variants if clean_url had trailing noise in source
            $escaped_old = preg_quote($old_url, '#');
            $updated_intro = preg_replace('#' . $escaped_old . '(%5C|%C2%A0|\xc2\xa0)?#i', $new_url, $updated_intro);
            $updated_text = preg_replace('#' . $escaped_old . '(%5C|%C2%A0|\xc2\xa0)?#i', $new_url, $updated_text);
        }
        
        $changed = ($updated_intro !== $page->intro || $updated_text !== $page->text);
        
        if ($changed) {
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
}

// Final state save
if ($state_dirty) {
    save_state($state_file, $state);
}

echo "\n=== Migration Summary ===\n";
echo "Pages Processed:   {$stats['pages_total']}\n";
echo "Pages Updated:     {$stats['pages_updated']}\n";
echo "URLs Found:        {$stats['urls_found']}\n";
echo "URLs Recovered:    {$stats['urls_recovered']}\n";
echo "URLs Reused Local: {$stats['urls_cached_ok']}\n";
echo "URLs Cached 404:   {$stats['urls_cached_fail']}\n";
echo "URLs Failed (404): {$stats['urls_failed']}\n";
echo "Done!\n";
