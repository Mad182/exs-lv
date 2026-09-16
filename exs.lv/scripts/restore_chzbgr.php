<?php

/**
 * restore_chzbgr.php
 *
 * Scans articles (pages), microblogs (miniblog), and comments (comments)
 * for broken i.chzbgr.com image URLs, attempts to recover them from the
 * Wayback Machine (Internet Archive), rehosts recovered images to img.exs.lv,
 * and updates the database records.
 *
 * Usage:
 *   php restore_chzbgr.php --table=pages [--dry-run] [--verbose]
 *   php restore_chzbgr.php --table=all [--dry-run] [--delay=300]
 *   php restore_chzbgr.php --id=67516 --table=pages
 *   php restore_chzbgr.php --stats
 *   php restore_chzbgr.php --reset-404
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
    'table:',     // Table to process: pages, miniblog, comments, or all (default: all)
    'id:',        // Specific record ID
    'limit:',     // Limit records per table
    'dry-run',    // Do not download images or update DB
    'delay:',     // Milliseconds delay between Wayback requests (default: 300)
    'state-file:',// Custom path to state JSON file
    'verbose',    // Verbose debug logging
    'stats',      // Display stats from state file and exit
    'reset-404',  // Clear all cached not_found entries to re-evaluate them
    'help'        // Show help
]);

if (isset($options['help'])) {
    echo "Usage:\n";
    echo "  php restore_chzbgr.php [--table=pages|miniblog|comments|all] [--dry-run] [--verbose]\n";
    echo "  php restore_chzbgr.php --id=<id> [--table=pages|miniblog|comments]\n";
    echo "  php restore_chzbgr.php --limit=<n>\n";
    echo "  php restore_chzbgr.php --delay=<ms>\n";
    echo "  php restore_chzbgr.php --stats\n";
    echo "  php restore_chzbgr.php --reset-404\n";
    exit(0);
}

$is_dry_run = isset($options['dry-run']);
$is_verbose = isset($options['verbose']);
$table_filter = $options['table'] ?? 'all';
$record_id = isset($options['id']) ? (int) $options['id'] : null;
$limit = isset($options['limit']) ? (int) $options['limit'] : null;
$delay_ms = isset($options['delay']) ? (int) $options['delay'] : 300;

// State tracking file
$state_dir = __DIR__ . '/data';
if (!is_dir($state_dir)) {
    @mkdir($state_dir, 0775, true);
}
$state_file = $options['state-file'] ?? ($state_dir . '/chzbgr_state.json');

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
    echo "=== Chzbgr State Summary ===\n";
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
$storage_base = IMG_PATH . '/chzbgr';
$target_url_base = 'https://img.exs.lv/chzbgr';

if (!is_dir($storage_base) && !$is_dry_run) {
    @mkdir($storage_base, 0775, true);
}

echo "=== i.chzbgr.com Image Restorer ===\n";
echo "Mode: " . ($is_dry_run ? "DRY RUN (no modifications)" : "LIVE EXECUTION") . "\n";
echo "Storage Base: $storage_base\n";
echo "Target Base URL: $target_url_base\n";
echo "Target Table(s): $table_filter\n";
echo "State File: $state_file (" . count($state) . " cached URLs)\n\n";

/**
 * Clean & normalize a raw extracted URL from text.
 */
function clean_chzbgr_url($raw_url) {
    $url = html_entity_decode($raw_url, ENT_QUOTES, 'UTF-8');
    // If concatenated by comma (e.g. #step2_...,https://i.chzbgr.com/...)
    if (preg_match('#(https?://i\.chzbgr\.com/[^\s"\'<>\[\]()]+)#i', $url, $m)) {
        $url = $m[1];
    }
    $url = preg_replace('/(%5C|%C2%A0|\xc2\xa0)+$/i', '', $url);
    $url = trim($url, " \t\n\r\0\x0B.,;:!?)'\"[]<>\\");
    return $url;
}

/**
 * Determine probe URLs and relative destination path for i.chzbgr.com URL.
 */
function parse_chzbgr_target($url) {
    $parsed = parse_url($url);
    if (!$parsed || empty($parsed['host'])) {
        return null;
    }
    
    $path = $parsed['path'] ?? '';
    $path = trim($path, " \t\n\r.,;:!?)'\"[]<>\\");
    if (empty($path)) {
        return null;
    }

    // 1. Direct file path with known extension (/completestore/... or /imagestore/...)
    if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $path, $m)) {
        $subpath = ltrim($path, '/');
        $variants = [
            $url,
            "https://i.chzbgr.com/" . $subpath,
            "http://i.chzbgr.com/" . $subpath,
            "http://images.cheezburger.com/" . $subpath,
            "http://images.icanhascheezburger.com/" . $subpath
        ];

        return [
            'type' => 'direct',
            'rel_path' => $subpath,
            'probe_urls' => array_values(array_unique($variants))
        ];
    }

    // 2. Hash-based path without extension (e.g. /maxW500/7761904128/hDC187F3C/ or /full/8396061696/h0F000527/)
    if (preg_match('#^(maxW500|full|\d+)/(.+)$#i', ltrim($path, '/'), $m)) {
        $prefix = $m[1];
        $sub = trim($m[2], '/');
        $variants = [
            "https://i.chzbgr.com/{$prefix}/{$sub}/",
            "http://i.chzbgr.com/{$prefix}/{$sub}/",
            "https://i.chzbgr.com/{$prefix}/{$sub}",
            "http://i.chzbgr.com/{$prefix}/{$sub}"
        ];

        return [
            'type' => 'no_ext',
            'rel_base' => "{$prefix}/{$sub}",
            'probe_urls' => array_values(array_unique($variants))
        ];
    }

    return null;
}

function get_curl_handle($reset = false) {
    static $ch = null;
    if ($reset && $ch !== null) {
        @curl_close($ch);
        $ch = null;
    }
    if ($ch === null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36");
        curl_setopt($ch, CURLOPT_TCP_KEEPALIVE, 1);
        curl_setopt($ch, CURLOPT_TCP_KEEPIDLE, 120);
        curl_setopt($ch, CURLOPT_TCP_KEEPINTVL, 30);
    }
    return $ch;
}

/**
 * Fetch image bytes from Wayback Machine with persistent connection & backoff.
 */
function fetch_from_wayback($url, $delay_ms) {
    $wayback_url = "https://web.archive.org/web/0id_/" . $url;
    $max_retries = 3;
    $http_code = 0;
    $content_type = '';
    $effective_url = '';
    $curl_err = '';

    for ($attempt = 1; $attempt <= $max_retries; $attempt++) {
        $ch = get_curl_handle();
        curl_setopt($ch, CURLOPT_URL, $wayback_url);
        
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
        
        // Rate limit (429) or temporary server error (500-504) or connection reset (0)
        if ($http_code == 429 || ($http_code >= 500 && $http_code <= 504) || $http_code == 0) {
            $backoff_sec = $attempt * 2;
            echo "    [WAIT] Wayback HTTP $http_code (" . ($curl_err ?: 'throttled') . "). Reconnecting in {$backoff_sec}s (attempt $attempt/$max_retries)...\n";
            if ($http_code == 0) {
                get_curl_handle(true);
            }
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

function mime_to_ext($mime) {
    switch ($mime) {
        case 'image/jpeg':
            return '.jpg';
        case 'image/png':
            return '.png';
        case 'image/gif':
            return '.gif';
        case 'image/webp':
            return '.webp';
        default:
            return '.jpg';
    }
}

/**
 * Resolve or download a chzbgr URL.
 * Returns array with replacement info or null if failed.
 */
function resolve_chzbgr_url($clean_url, &$state, &$state_dirty, $storage_base, $target_url_base, $delay_ms, $is_dry_run, $is_verbose, &$stats) {
    $target_info = parse_chzbgr_target($clean_url);
    if (!$target_info) {
        if ($is_verbose) {
            echo "  [SKIP] Unsupported format: $clean_url\n";
        }
        return null;
    }

    $stats['urls_found']++;

    // Check if already in state as recovered or exists
    if (isset($state[$clean_url]) && in_array($state[$clean_url]['status'], ['recovered', 'exists'])) {
        $dest_file = $storage_base . '/' . $state[$clean_url]['rel_path'];
        if (file_exists($dest_file) && filesize($dest_file) > 100) {
            $stats['urls_cached_ok']++;
            return [
                'new_url' => $state[$clean_url]['new_url'],
                'clean_url' => $clean_url
            ];
        }
    }

    // Check if known 404
    if (isset($state[$clean_url]) && $state[$clean_url]['status'] === 'not_found') {
        $stats['urls_cached_fail']++;
        if ($is_verbose) {
            echo "  [PREVIOUS 404] $clean_url\n";
        }
        return null;
    }

    // Direct check if local file already exists for direct type
    if ($target_info['type'] === 'direct') {
        $dest_file = $storage_base . '/' . $target_info['rel_path'];
        $new_url = $target_url_base . '/' . $target_info['rel_path'];
        if (file_exists($dest_file) && filesize($dest_file) > 100) {
            $stats['urls_cached_ok']++;
            $state[$clean_url] = [
                'status' => 'exists',
                'rel_path' => $target_info['rel_path'],
                'new_url' => $new_url,
                'time' => date('Y-m-d H:i:s')
            ];
            $state_dirty = true;
            return [
                'new_url' => $new_url,
                'clean_url' => $clean_url
            ];
        }
    }

    // Query Wayback Machine
    $success = false;
    $last_http_code = 0;
    echo "  [QUERYING WAYBACK] $clean_url ...\n";

    foreach ($target_info['probe_urls'] as $probe_url) {
        $res = fetch_from_wayback($probe_url, $delay_ms);
        $last_http_code = $res['http_code'] ?? 0;
        if ($res['ok']) {
            $success = true;
            echo "    -> RECOVERED via {$res['wayback_url']} ({$res['mime']}, " . round($res['size'] / 1024, 1) . " KB)\n";

            if ($target_info['type'] === 'direct') {
                $rel_path = $target_info['rel_path'];
            } else {
                $ext = mime_to_ext($res['mime']);
                $rel_path = $target_info['rel_base'] . $ext;
            }

            $dest_file = $storage_base . '/' . $rel_path;
            $new_url = $target_url_base . '/' . $rel_path;

            if (!$is_dry_run) {
                $dir = dirname($dest_file);
                if (!is_dir($dir)) {
                    @mkdir($dir, 0775, true);
                }
                file_put_contents($dest_file, $res['data']);
                @chmod($dest_file, 0664);
            }

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

            return [
                'new_url' => $new_url,
                'clean_url' => $clean_url
            ];
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

    return null;
}

// Processing counters
$stats = [
    'records_total' => 0,
    'records_updated' => 0,
    'urls_found' => 0,
    'urls_recovered' => 0,
    'urls_cached_ok' => 0,
    'urls_cached_fail' => 0,
    'urls_failed' => 0,
];

$state_dirty = false;
$batch_save_counter = 0;

// Tables definition
$tables_config = [
    'pages' => [
        'id_col' => 'id',
        'title_col' => 'title',
        'content_cols' => ['intro', 'text'],
        'condition' => "`text` LIKE '%i.chzbgr.com%' OR `intro` LIKE '%i.chzbgr.com%'"
    ],
    'miniblog' => [
        'id_col' => 'id',
        'title_col' => 'id',
        'content_cols' => ['text'],
        'condition' => "`text` LIKE '%i.chzbgr.com%'"
    ],
    'comments' => [
        'id_col' => 'id',
        'title_col' => 'id',
        'content_cols' => ['text'],
        'condition' => "`text` LIKE '%i.chzbgr.com%'"
    ]
];

foreach ($tables_config as $tbl_name => $tbl_cfg) {
    if ($table_filter !== 'all' && $table_filter !== $tbl_name) {
        continue;
    }

    echo "--- Scanning table `$tbl_name` ---\n";
    $where = $tbl_cfg['condition'];
    if ($record_id) {
        $where = "`{$tbl_cfg['id_col']}` = $record_id";
    }

    $limit_sql = $limit ? "LIMIT $limit" : "";
    $cols_sql = implode(', ', array_unique(array_merge([$tbl_cfg['id_col']], [$tbl_cfg['title_col']], $tbl_cfg['content_cols'])));
    $rows = $db->get_results("SELECT $cols_sql FROM `$tbl_name` WHERE $where ORDER BY `{$tbl_cfg['id_col']}` ASC $limit_sql");

    if (empty($rows)) {
        echo "No matching records found in `$tbl_name`.\n\n";
        continue;
    }

    echo "Found " . count($rows) . " record(s) in `$tbl_name` to process.\n";
    $stats['records_total'] += count($rows);

    foreach ($rows as $r_idx => $row) {
        $rec_id = $row->{$tbl_cfg['id_col']};
        $rec_title = $row->{$tbl_cfg['title_col']};

        $combined_content = '';
        foreach ($tbl_cfg['content_cols'] as $c_col) {
            $combined_content .= ' ' . ($row->$c_col ?? '');
        }

        if (!preg_match_all('#https?://i\.chzbgr\.com/[^\s"\'<>\[\]()]+#i', $combined_content, $matches)) {
            continue;
        }

        $raw_urls = array_unique($matches[0]);
        $replacements = []; // [old_url => new_url]

        if ($is_verbose) {
            echo "[$tbl_name #$rec_id] \"$rec_title\": found " . count($raw_urls) . " chzbgr URL(s)\n";
        }

        foreach ($raw_urls as $raw_url) {
            $clean_url = clean_chzbgr_url($raw_url);
            $res = resolve_chzbgr_url(
                $clean_url,
                $state,
                $state_dirty,
                $storage_base,
                $target_url_base,
                $delay_ms,
                $is_dry_run,
                $is_verbose,
                $stats
            );

            if ($res) {
                $replacements[$clean_url] = $res['new_url'];
            }

            $batch_save_counter++;
            if ($batch_save_counter % 10 === 0 && $state_dirty) {
                save_state($state_file, $state);
                $state_dirty = false;
            }
        }

        if (!empty($replacements)) {
            $updates = [];
            $record_changed = false;

            foreach ($tbl_cfg['content_cols'] as $c_col) {
                $old_val = $row->$c_col ?? '';
                $new_val = $old_val;

                foreach ($replacements as $old_url => $new_url) {
                    $new_val = str_replace($old_url, $new_url, $new_val);

                    // Also handle variations without trailing slash if old_url had trailing slash
                    $trimmed_old = rtrim($old_url, '/');
                    if ($trimmed_old !== $old_url) {
                        $new_val = str_replace($trimmed_old, $new_url, $new_val);
                    } else {
                        // Or with trailing slash
                        $new_val = str_replace($old_url . '/', $new_url, $new_val);
                    }

                    // Handle noise suffixes
                    $escaped_old = preg_quote($old_url, '#');
                    $new_val = preg_replace('#' . $escaped_old . '(%5C|%C2%A0|\xc2\xa0)?#i', $new_url, $new_val);
                }

                if ($new_val !== $old_val) {
                    $record_changed = true;
                    $updates[$c_col] = $new_val;
                }
            }

            if ($record_changed) {
                $stats['records_updated']++;
                echo "  => [UPDATE $tbl_name #$rec_id] Replaced " . count($replacements) . " URL(s) in \"$rec_title\"\n";

                if (!$is_dry_run) {
                    $set_clauses = [];
                    foreach ($updates as $col => $val) {
                        $escaped = $db->real_escape_string($val);
                        $set_clauses[] = "`$col` = '$escaped'";
                    }
                    $sql = "UPDATE `$tbl_name` SET " . implode(', ', $set_clauses) . " WHERE `{$tbl_cfg['id_col']}` = $rec_id LIMIT 1";
                    $db->query($sql);
                }
            }
        }
    }
    echo "\n";
}

// Final state save
if ($state_dirty) {
    save_state($state_file, $state);
}

echo "=== Migration Summary ===\n";
echo "Records Processed: {$stats['records_total']}\n";
echo "Records Updated:   {$stats['records_updated']}\n";
echo "URLs Found:        {$stats['urls_found']}\n";
echo "URLs Recovered:    {$stats['urls_recovered']}\n";
echo "URLs Reused Local: {$stats['urls_cached_ok']}\n";
echo "URLs Cached 404:   {$stats['urls_cached_fail']}\n";
echo "URLs Failed (404): {$stats['urls_failed']}\n";
echo "Done!\n";
