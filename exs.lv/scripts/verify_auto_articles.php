<?php

if (php_sapi_name() !== 'cli') {
    die("CLI only.\n");
}

chdir(__DIR__ . '/..');
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['HTTP_HOST'] = 'exs.lv';
require_once('configdb.php');
require_once('includes/class.mdb.php');
require_once('includes/functions.core.php');

$db = new mdb($username, $password, $database, $hostname);

$rows = $db->get_results("SELECT id, strid, title, text, intro, image FROM pages WHERE category = 611 ORDER BY id ASC");
$escapingIssues = 0;
$imgUrls = [];

echo "Found " . count($rows) . " articles in category 611.\n\n";

foreach ($rows as $r) {
    $hasIssue = false;
    if (strpos($r->text, '\n') !== false || strpos($r->text, '\"') !== false || strpos($r->text, "\\'") !== false) {
        echo "[ESCAPING ISSUE] ID {$r->id} ({$r->strid}): Contains literal escaped characters!\n";
        $escapingIssues++;
        $hasIssue = true;
    }
    
    preg_match_all('/<img[^>]+src=[\'"]([^\'"]+)[\'"]/i', $r->text, $matches);
    if (!empty($matches[1])) {
        foreach ($matches[1] as $src) {
            $imgUrls[$src][] = $r->strid;
        }
    }
}

echo "\n--- Summary ---\n";
echo "Articles with literal escaping issues: {$escapingIssues}\n";
echo "Total unique inline images: " . count($imgUrls) . "\n\n";

echo "--- Checking Image HTTP Statuses ---\n";
foreach ($imgUrls as $url => $articleSlugs) {
    $fullUrl = $url;
    if (strpos($fullUrl, 'http') !== 0) {
        if (strpos($fullUrl, '//') === 0) {
            $fullUrl = 'https:' . $fullUrl;
        } else {
            $fullUrl = 'https://exs.lv' . $fullUrl;
        }
    }
    
    $ch = curl_init($fullUrl);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "HTTP {$httpCode} - {$url} (Articles: " . implode(', ', array_slice($articleSlugs, 0, 3)) . ")\n";
}
