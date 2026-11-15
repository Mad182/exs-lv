<?php
/**
 * CLI Sitemap Generator for EXS.LV, CODING.LV, RUNESCAPE.EXS.LV, and LOL.EXS.LV
 * Generates per-domain sitemaps in /sitemaps/:
 *   - /sitemaps/sitemap-{domain}.xml (Sitemap Index served at https://{domain}/sitemap.xml)
 *   - /sitemaps/{domain}-categories.xml (Homepage, Games, Categories, Forums, Public Groups)
 *   - /sitemaps/{domain}-pages-*.xml (Public Articles & Forum Topics)
 *   - /sitemaps/{domain}-miniblogs-*.xml (Indexable Public Miniblog Discussions)
 */

if (PHP_SAPI !== 'cli') {
	http_response_code(403);
	exit("CLI only.\n");
}

$root_dir = dirname(__DIR__);
chdir($root_dir);
ini_set('memory_limit', '512M');
$_SERVER['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'exs.lv';
$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '/';

if (!isset($db) || !is_object($db)) {
	require_once $root_dir . '/configdb.php';
	require_once CORE_PATH . '/includes/class.mdb.php';
	require_once $root_dir . '/includes/functions.core.php';
	$db = new mdb($username, $password, $database, $hostname);
	unset($password);
}

$sitemap_dir = $root_dir . '/sitemaps';
if (!is_dir($sitemap_dir)) {
	mkdir($sitemap_dir, 0775, true);
}

$now_iso = date('c');

$sites = [
	1 => 'exs.lv',
	3 => 'coding.lv',
	7 => 'lol.exs.lv',
	9 => 'runescape.exs.lv',
];

/**
 * Helper to write a standard urlset XML file atomically
 */
function write_urlset_sitemap($filepath, array $urls) {
	$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
	foreach ($urls as $u) {
		$xml .= "\t<url>\n";
		$xml .= "\t\t<loc>" . htmlspecialchars($u['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</loc>\n";
		if (!empty($u['lastmod'])) {
			$xml .= "\t\t<lastmod>" . htmlspecialchars($u['lastmod'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</lastmod>\n";
		}
		if (!empty($u['changefreq'])) {
			$xml .= "\t\t<changefreq>" . $u['changefreq'] . "</changefreq>\n";
		}
		if (!empty($u['priority'])) {
			$xml .= "\t\t<priority>" . $u['priority'] . "</priority>\n";
		}
		$xml .= "\t</url>\n";
	}
	$xml .= "</urlset>\n";

	$tmp = $filepath . '.tmp';
	file_put_contents($tmp, $xml);
	@chmod($tmp, 0664);
	rename($tmp, $filepath);
}

$excluded_modules = [
	'error403', 'error404', 'redirect', 'search', 'blogadmin', 'wg-admin',
	'finduser', 'bookmarks', 'extend-gallery', 'group', 'pm', 'user',
	'login', 'logout', 'register', 'write', 'approve', 'banned', 'crows',
	'reports', 'findby', 'log', 'polladmin', 'wallpaper_admin', 'racontest',
	'user_decos', 'custom_awards', 'review-restored', 'myblog', 'piezimes',
	'notifications', 'subscribe', 'interests', 'mb-latest', 'dailytop',
	'rate-comment', 'ajax'
];
$excluded_slugs = [
	'home', 'index', 'search', 'login', 'logout', 'register', 'error404', 'error403'
];

$game_modules = [
	'speles', 'snake', 'tetris', 'minu-mekletajs', 'wordle', '2048', 'flappy',
	'sudoku', 'memory', 'rulete', 'augsup', 'vardes', 'invaders', 'karatavas',
	'runner', 'tornis', 'arkanoid', 'rezonanse', 'desas', 'tanki', 'pacman',
	'wg-play', 'ut99'
];

$chunk_size = 25000;

foreach ($sites as $site_lang => $domain) {
	$base_url = 'https://' . $domain;
	$generated_sitemaps = [];

	// =========================================================================
	// 1. Categories, Games & Public Groups ({domain}-categories.xml)
	// =========================================================================
	$cat_urls = [];
	$seen_locs = [];

	$static_entries = [
		['loc' => $base_url . '/', 'lastmod' => $now_iso, 'changefreq' => 'hourly', 'priority' => '1.0'],
	];
	if ($site_lang === 1) {
		$static_entries[] = ['loc' => $base_url . '/latvian-games/', 'lastmod' => $now_iso, 'changefreq' => 'monthly', 'priority' => '0.7'];
	}
	foreach ($static_entries as $entry) {
		$cat_urls[] = $entry;
		$seen_locs[$entry['loc']] = true;
	}

	$cats = $db->get_results("
		SELECT `textid`, `module`, `isblog`, `isforum`, `stat_topics`, `parent`
		FROM `cat`
		WHERE `lang` = $site_lang
		  AND `status` = 'active'
		  AND `noindex` = 0
		  AND `private` = 0
		  AND `mods_only` = 0
		  AND (`sitemap` = 1 OR `module` = 'ut99')
		ORDER BY `id` ASC
	");

	if ($cats) {
		foreach ($cats as $c) {
			$slug = trim($c->textid);
			if ($slug === '' || in_array($slug, $excluded_slugs, true)) {
				continue;
			}
			if (in_array($c->module, $excluded_modules, true)) {
				continue;
			}
			if ((int) $c->isblog !== 0 && (int) $c->stat_topics < 1) {
				continue;
			}
			$loc = $base_url . '/' . $slug;
			if (isset($seen_locs[$loc])) {
				continue;
			}
			$seen_locs[$loc] = true;

			$is_game = in_array($c->module, $game_modules, true) || in_array($slug, $game_modules, true) || (int) $c->parent === 2516;
			$cat_urls[] = [
				'loc' => $loc,
				'lastmod' => $now_iso,
				'changefreq' => $is_game ? 'weekly' : 'daily',
				'priority' => $is_game ? '0.8' : '0.7',
			];
		}
	}

	// Public interest groups / clans
	$groups = $db->get_results("
		SELECT `id`, `strid`, `last_activity`
		FROM `clans`
		WHERE `lang` = $site_lang
		  AND `public` = 1
		  AND `noindex` = 0
		  AND `archived` = 0
		ORDER BY `posts` DESC
	");

	if ($groups) {
		foreach ($groups as $g) {
			$g_slug = !empty($g->strid) ? '/' . trim($g->strid) : '/group/' . (int) $g->id;
			$loc = $base_url . $g_slug;
			if (isset($seen_locs[$loc])) {
				continue;
			}
			$seen_locs[$loc] = true;

			$ts = !empty($g->last_activity) ? strtotime($g->last_activity) : false;
			$lastmod = ($ts && $ts > 0) ? date('c', $ts) : $now_iso;

			$cat_urls[] = [
				'loc' => $loc,
				'lastmod' => $lastmod,
				'changefreq' => 'weekly',
				'priority' => '0.6',
			];
		}
	}

	$cat_filename = $domain . '-categories.xml';
	write_urlset_sitemap($sitemap_dir . '/' . $cat_filename, $cat_urls);
	$generated_sitemaps[] = [
		'loc' => $base_url . '/sitemaps/' . $cat_filename,
		'lastmod' => $now_iso,
	];
	unset($cat_urls);

	// =========================================================================
	// 2. Public Articles & Forum Topics ({domain}-pages-*.xml)
	// =========================================================================
	$page_offset = 0;
	$page_part = 1;

	while (true) {
		$rows = $db->get_results("
			SELECT `p`.`strid`, `p`.`date`, `p`.`updated`, `p`.`edit_time`
			FROM `pages` AS `p`
			LEFT JOIN `cat` AS `c` ON `c`.`id` = `p`.`category`
			WHERE `p`.`lang` = $site_lang
			  AND `p`.`private` = 0
			  AND `p`.`needs_review` = 0
			  AND `p`.`strid` != ''
			  AND (`p`.`redirect` = '' OR `p`.`redirect` IS NULL)
			  AND (`c`.`mods_only` IS NULL OR `c`.`mods_only` = 0)
			  AND (`c`.`private` IS NULL OR `c`.`private` = 0)
			  AND (`c`.`noindex` IS NULL OR `c`.`noindex` = 0)
			ORDER BY `p`.`id` DESC
			LIMIT $page_offset, $chunk_size
		");

		if (!$rows) {
			break;
		}

		$page_urls = [];
		$latest_ts = 0;
		foreach ($rows as $r) {
			$loc = $base_url . '/read/' . $r->strid;
			$ts = 0;
			if (!empty($r->updated) && $r->updated !== '0000-00-00 00:00:00') {
				$ts = strtotime($r->updated);
			}
			if ((!$ts || $ts <= 0) && !empty($r->edit_time)) {
				$ts = (int) $r->edit_time;
			}
			if ((!$ts || $ts <= 0) && !empty($r->date)) {
				$ts = strtotime($r->date);
			}
			if ($ts > $latest_ts) {
				$latest_ts = $ts;
			}

			$page_urls[] = [
				'loc' => $loc,
				'lastmod' => ($ts && $ts > 0) ? date('c', $ts) : $now_iso,
				'changefreq' => 'monthly',
				'priority' => '0.6',
			];
		}

		$filename = $domain . '-pages-' . $page_part . '.xml';
		write_urlset_sitemap($sitemap_dir . '/' . $filename, $page_urls);
		$generated_sitemaps[] = [
			'loc' => $base_url . '/sitemaps/' . $filename,
			'lastmod' => ($latest_ts > 0) ? date('c', $latest_ts) : $now_iso,
		];

		if (count($rows) < $chunk_size) {
			break;
		}
		$page_offset += $chunk_size;
		$page_part++;
	}

	// =========================================================================
	// 3. Indexable Public Miniblog Threads ({domain}-miniblogs-*.xml)
	// =========================================================================
	$mb_last_id = PHP_INT_MAX;
	$mb_part = 1;

	while (true) {
		$mb_rows = $db->get_results("
			SELECT `id`, `author`, `date`, `bump`, `text`
			FROM `miniblog`
			WHERE `lang` = $site_lang
			  AND `private` = 0
			  AND `removed` = 0
			  AND `hidden` = 0
			  AND `noindex` = 0
			  AND `parent` = 0
			  AND `groupid` = 0
			  AND `type` = 'miniblog'
			  AND (`posts` >= 3 OR CHAR_LENGTH(`text`) >= 100)
			  AND `id` < $mb_last_id
			ORDER BY `id` DESC
			LIMIT $chunk_size
		");

		if (!$mb_rows) {
			break;
		}

		$mb_urls = [];
		$latest_mb_ts = 0;
		foreach ($mb_rows as $mb) {
			$mb_last_id = (int) $mb->id;
			$slug = mkslug(textlimit(mb_get_title($mb->text), 36, ''));
			$loc = $base_url . '/say/' . (int) $mb->author . '/' . (int) $mb->id . ($slug !== '' ? '-' . $slug : '');

			$ts = !empty($mb->bump) ? (int) $mb->bump : strtotime($mb->date);
			if ($ts > $latest_mb_ts) {
				$latest_mb_ts = $ts;
			}

			$mb_urls[] = [
				'loc' => $loc,
				'lastmod' => ($ts && $ts > 0) ? date('c', $ts) : $now_iso,
				'changefreq' => 'monthly',
				'priority' => '0.5',
			];
		}

		$filename = $domain . '-miniblogs-' . $mb_part . '.xml';
		write_urlset_sitemap($sitemap_dir . '/' . $filename, $mb_urls);
		$generated_sitemaps[] = [
			'loc' => $base_url . '/sitemaps/' . $filename,
			'lastmod' => ($latest_mb_ts > 0) ? date('c', $latest_mb_ts) : $now_iso,
		];

		if (count($mb_rows) < $chunk_size) {
			break;
		}
		$mb_part++;
	}

	// =========================================================================
	// 4. Write Domain Sitemap Index (/sitemaps/sitemap-{domain}.xml)
	// =========================================================================
	$index_xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	$index_xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
	foreach ($generated_sitemaps as $sm) {
		$index_xml .= "\t<sitemap>\n";
		$index_xml .= "\t\t<loc>" . htmlspecialchars($sm['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</loc>\n";
		$index_xml .= "\t\t<lastmod>" . htmlspecialchars($sm['lastmod'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</lastmod>\n";
		$index_xml .= "\t</sitemap>\n";
	}
	$index_xml .= "</sitemapindex>\n";

	$domain_index_path = $sitemap_dir . '/sitemap-' . $domain . '.xml';
	$tmp_index = $domain_index_path . '.tmp';
	file_put_contents($tmp_index, $index_xml);
	@chmod($tmp_index, 0664);
	rename($tmp_index, $domain_index_path);

	echo $domain . ': generated ' . count($generated_sitemaps) . ' sitemaps in /sitemaps/sitemap-' . $domain . ".xml\n";
}

// Remove legacy root /sitemap.xml if present so per-domain routing handles /sitemap.xml cleanly
if (file_exists($root_dir . '/sitemap.xml')) {
	@unlink($root_dir . '/sitemap.xml');
}
