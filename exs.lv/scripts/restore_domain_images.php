<?php

/**
 * restore_domain_images.php
 *
 * Scans articles (pages), microblogs (miniblog), and comments (comments)
 * for broken image URLs hosted on external domains (e.g. socawlege.com, usvsth3m.com),
 * attempts to recover them from the Wayback Machine (Internet Archive),
 * rehosts recovered images to img.exs.lv/<domain_slug>/...,
 * and updates the database records.
 *
 * Usage:
 *   php restore_domain_images.php --domain=socawlege.com,usvsth3m.com [--dry-run] [--verbose]
 *   php restore_domain_images.php --domain=socawlege.com --table=pages
 *   php restore_domain_images.php --domain=usvsth3m.com --stats
 *   php restore_domain_images.php --domain=usvsth3m.com --reset-404
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
    'domain:',    // Required: one or more domains comma-separated (e.g. socawlege.com,usvsth3m.com)
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

if (isset($options['help']) || empty($options['domain'])) {
    echo "Usage:\n";
    echo "  php restore_domain_images.php --domain=<domain1,domain2> [--table=pages|miniblog|comments|all] [--dry-run] [--verbose]\n";
    echo "  php restore_domain_images.php --domain=<domain> --id=<id> [--table=pages|miniblog|comments]\n";
    echo "  php restore_domain_images.php --domain=<domain> --stats\n";
    echo "  php restore_domain_images.php --domain=<domain> --reset-404\n";
    exit(0);
}

$raw_domains = array_filter(array_map('trim', explode(',', $options['domain'])));
if (empty($raw_domains)) {
    die("Error: No valid domains specified.\n");
}

$is_dry_run = isset($options['dry-run']);
$is_verbose = isset($options['verbose']);
$table_filter = $options['table'] ?? 'all';
$record_id = isset($options['id']) ? (int) $options['id'] : null;
$limit = isset($options['limit']) ? (int) $options['limit'] : null;
$delay_ms = isset($options['delay']) ? (int) $options['delay'] : 300;

function domain_to_slug($domain) {
    $clean = preg_replace('#^https?://#i', '', $domain);
    $clean = preg_replace('#^www\.#i', '', $clean);
    $clean = explode('/', $clean)[0];
    $clean = preg_replace('#\.[a-z]{2,8}$#i', '', $clean); // strip tld
    return strtolower(preg_replace('/[^a-z0-9_-]/i', '_', $clean));
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

/**
 * Clean & normalize a raw extracted URL from text.
 */
function clean_remote_url($raw_url) {
    $url = html_entity_decode($raw_url, ENT_QUOTES, 'UTF-8');
    $url = preg_replace('/(%5C|%C2%A0|\xc2\xa0)+$/i', '', $url);
    $url = trim($url, " \t\n\r\0\x0B.,;:!?)'\"[]<>\\");
    return $url;
}

/**
 * Parse remote target and determine destination path & probe URLs.
 */
function parse_domain_target($url, $domain_slug) {
    $parsed = parse_url($url);
    if (!$parsed || empty($parsed['host'])) {
        return null;
    }

    $host = $parsed['host'];
    $path = $parsed['path'] ?? '';
    $path = trim($path, " \t\n\r.,;:!?)'\"[]<>\\");
    if (empty($path)) {
        return null;
    }

    // Only process URLs that look like image files
    $ext = pathinfo($path, PATHINFO_EXTENSION);
    $is_image_ext = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'ico', 'svg']);

    // Also consider paths containing uploads/ or images/ even if no extension
    if (!$is_image_ext && !preg_match('#/(uploads|images|media|img)/#i', $path)) {
        return null;
    }

    $subpath = ltrim($path, '/');
    $clean_host = preg_replace('#^www\.#i', '', $host);

    $variants = [
        $url,
        "https://{$clean_host}/{$subpath}",
        "http://{$clean_host}/{$subpath}",
        "https://www.{$clean_host}/{$subpath}",
        "http://www.{$clean_host}/{$subpath}",
    ];

    return [
        'has_ext' => !empty($ext),
        'subpath' => $subpath,
        'probe_urls' => array_values(array_unique($variants))
    ];
}

// Global counters
$total_stats = [
    'records_total' => 0,
    'records_updated' => 0,
    'urls_found' => 0,
    'urls_recovered' => 0,
    'urls_cached_ok' => 0,
    'urls_cached_fail' => 0,
    'urls_failed' => 0,
];

// Process each domain
foreach ($raw_domains as $domain) {
    $slug = domain_to_slug($domain);
    $state_dir = __DIR__ . '/data';
    if (!is_dir($state_dir)) {
        @mkdir($state_dir, 0775, true);
    }
    $state_file = $options['state-file'] ?? ($state_dir . '/' . $slug . '_state.json');

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
        echo "[$domain] Reset complete. Removed " . ($before_count - $after_count) . " not_found entries. Remaining: $after_count\n";
        continue;
    }

    if (isset($options['stats'])) {
        echo "=== Domain: $domain ($slug) State Summary ===\n";
        echo "State File: $state_file\n";
        echo "Total URLs Tracked: " . count($state) . "\n";
        $counts = array_count_values(array_column($state, 'status'));
        foreach ($counts as $st => $c) {
            echo "  - $st: $c\n";
        }
        echo "\n";
        continue;
    }

    $storage_base = IMG_PATH . '/' . $slug;
    $target_url_base = 'https://img.exs.lv/' . $slug;

    if (!is_dir($storage_base) && !$is_dry_run) {
        @mkdir($storage_base, 0775, true);
    }

    echo "========================================\n";
    echo "Processing Domain: $domain (slug: $slug)\n";
    echo "Mode: " . ($is_dry_run ? "DRY RUN (no modifications)" : "LIVE EXECUTION") . "\n";
    echo "Storage Base: $storage_base\n";
    echo "Target Base URL: $target_url_base\n";
    echo "State File: $state_file (" . count($state) . " cached URLs)\n";
    echo "========================================\n\n";

    $state_dirty = false;
    $batch_save_counter = 0;

    $tables_config = [
        'pages' => [
            'id_col' => 'id',
            'title_col' => 'title',
            'content_cols' => ['intro', 'text'],
            'condition' => "`text` LIKE '%$domain%' OR `intro` LIKE '%$domain%'"
        ],
        'miniblog' => [
            'id_col' => 'id',
            'title_col' => 'id',
            'content_cols' => ['text'],
            'condition' => "`text` LIKE '%$domain%'"
        ],
        'comments' => [
            'id_col' => 'id',
            'title_col' => 'id',
            'content_cols' => ['text'],
            'condition' => "`text` LIKE '%$domain%'"
        ]
    ];

    foreach ($tables_config as $tbl_name => $tbl_cfg) {
        if ($table_filter !== 'all' && $table_filter !== $tbl_name) {
            continue;
        }

        echo "--- Scanning table `$tbl_name` for $domain ---\n";
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
        $total_stats['records_total'] += count($rows);

        foreach ($rows as $row) {
            $rec_id = $row->{$tbl_cfg['id_col']};
            $rec_title = $row->{$tbl_cfg['title_col']};

            $combined_content = '';
            foreach ($tbl_cfg['content_cols'] as $c_col) {
                $combined_content .= ' ' . ($row->$c_col ?? '');
            }

            $escaped_d = preg_quote($domain, '#');
            if (!preg_match_all('#https?://[^\s"\'<>\[\]()]*' . $escaped_d . '[^\s"\'<>\[\]()]*#i', $combined_content, $matches)) {
                continue;
            }

            $raw_urls = array_unique($matches[0]);
            $replacements = [];

            if ($is_verbose) {
                echo "[$tbl_name #$rec_id] \"$rec_title\": found " . count($raw_urls) . " $domain URL(s)\n";
            }

            foreach ($raw_urls as $raw_url) {
                $clean_url = clean_remote_url($raw_url);
                $target_info = parse_domain_target($clean_url, $slug);

                if (!$target_info) {
                    if ($is_verbose) {
                        echo "  [SKIP] Non-image or unsupported format: $clean_url\n";
                    }
                    continue;
                }

                $total_stats['urls_found']++;

                // Check cache in state
                if (isset($state[$clean_url]) && in_array($state[$clean_url]['status'], ['recovered', 'exists'])) {
                    $dest_file = $storage_base . '/' . $state[$clean_url]['rel_path'];
                    if (file_exists($dest_file) && filesize($dest_file) > 100) {
                        $total_stats['urls_cached_ok']++;
                        $replacements[$clean_url] = $state[$clean_url]['new_url'];
                        continue;
                    }
                }

                // Check if known 404
                if (isset($state[$clean_url]) && $state[$clean_url]['status'] === 'not_found') {
                    $total_stats['urls_cached_fail']++;
                    if ($is_verbose) {
                        echo "  [PREVIOUS 404] $clean_url\n";
                    }
                    continue;
                }

                // Check if local file exists
                $rel_path = $target_info['subpath'];
                $dest_file = $storage_base . '/' . $rel_path;
                $new_url = $target_url_base . '/' . $rel_path;
                if ($target_info['has_ext'] && file_exists($dest_file) && filesize($dest_file) > 100) {
                    $total_stats['urls_cached_ok']++;
                    $replacements[$clean_url] = $new_url;
                    $state[$clean_url] = [
                        'status' => 'exists',
                        'rel_path' => $rel_path,
                        'new_url' => $new_url,
                        'time' => date('Y-m-d H:i:s')
                    ];
                    $state_dirty = true;
                    continue;
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

                        if (!$target_info['has_ext']) {
                            $ext = mime_to_ext($res['mime']);
                            $rel_path = rtrim($target_info['subpath'], '/') . $ext;
                            $dest_file = $storage_base . '/' . $rel_path;
                            $new_url = $target_url_base . '/' . $rel_path;
                        }

                        if (!$is_dry_run) {
                            $dir = dirname($dest_file);
                            if (!is_dir($dir)) {
                                @mkdir($dir, 0775, true);
                            }
                            file_put_contents($dest_file, $res['data']);
                            @chmod($dest_file, 0664);
                        }

                        $total_stats['urls_recovered']++;
                        $replacements[$clean_url] = $new_url;

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
                        $total_stats['urls_failed']++;
                        $state[$clean_url] = [
                            'status' => 'not_found',
                            'time' => date('Y-m-d H:i:s')
                        ];
                        $state_dirty = true;
                    } else {
                        echo "    -> TEMPORARY ERROR (HTTP $last_http_code) in Wayback Machine (will retry on next run)\n";
                        $total_stats['urls_failed']++;
                    }
                }

                $batch_save_counter++;
                if ($batch_save_counter % 10 === 0 && $state_dirty) {
                    file_put_contents($state_file, json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
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
                    $total_stats['records_updated']++;
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

    if ($state_dirty) {
        file_put_contents($state_file, json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}

if (!isset($options['stats']) && !isset($options['reset-404'])) {
    echo "=== Overall Migration Summary ===\n";
    echo "Records Processed: {$total_stats['records_total']}\n";
    echo "Records Updated:   {$total_stats['records_updated']}\n";
    echo "URLs Found:        {$total_stats['urls_found']}\n";
    echo "URLs Recovered:    {$total_stats['urls_recovered']}\n";
    echo "URLs Reused Local: {$total_stats['urls_cached_ok']}\n";
    echo "URLs Cached 404:   {$total_stats['urls_cached_fail']}\n";
    echo "URLs Failed (404): {$total_stats['urls_failed']}\n";
    echo "Done!\n";
}
