<?php

function can_edit_page($article) {
	global $lang, $auth, $min_page_edit, $category, $page_edit_time;

	if (!$auth->ok || $lang != $article->lang) {
		return false;
	}

	if (im_mod() || im_cat_mod()) {
		return true;
	}

	if (im_rs_mod() && $auth->id == $article->author) {
		return true;
	}

	if ($category->isblog == $auth->id) {
		return true;
	}

	if ($auth->id == $article->author) {

		//manuāli norādīti lietotāji
		if ($auth->id == 34212 || $auth->id == 34198 || $auth->id == 27719 || $auth->id == 3962 || $auth->id == 1822) {
			return true;
		}

		//rakstu autori
		if ($auth->level == 3) {
			return true;
		}

		//pārējie, ja ļauj karma un izveides laiks
		if ($auth->karma >= $min_page_edit) {
			if ($page_edit_time == 0) {
				return true;
			}
			if ($page_edit_time >= time() - strtotime($article->date)) {
				return true;
			}
		}

	}

	return false;
}

function get_page_categories($current = null, $force = false) {
	global $db, $m, $lang, $debug;

	if ($debug || $force || !($cats = $m->get('cat_list_' . $lang))) {
		$cats = $db->get_results("SELECT `lang`,`parent`,`module`,`persona`,`isblog`,`isforum`,`id`,`title`,`status` FROM `cat` WHERE `module` IN('list','wall','rshelp','movies') AND `lang` = '$lang' ORDER BY `title` ASC");
		$m->set('cat_list_' . $lang, $cats, 900);
	}

	$return = [];
	foreach ($cats as $cat) {
		if ((im_mod() || im_cat_mod($cat->id) || $cat->id == $current || $current == 'all') && $cat->status == 'active') {

			if ($cat->isforum) {
				$return['Forums'][$cat->id] = $cat->title . ' forums';
			} elseif ($cat->isblog) {
				$return['Blogi'][$cat->id] = $cat->title;
			} elseif ($cat->persona == 'runescape.jpg') {
				$return['Runescape'][$cat->id] = $cat->title;
			} else {
				$return['Main'][$cat->id] = $cat->title;
			}
		}
	}
	return $return;
}

/**
 * Pārbauda, vai attēla URL ir ārējs (nav exs.lv, *.exs.lv, coding.lv, *.coding.lv).
 */
function is_external_image_url($url) {
	$url = trim(html_entity_decode($url, ENT_QUOTES, 'UTF-8'));
	if (empty($url) || strpos($url, 'data:') === 0 || strpos($url, 'blob:') === 0 || strpos($url, 'javascript:') === 0) {
		return false;
	}

	if (strpos($url, '//') === 0) {
		$url = 'https:' . $url;
	}

	$parsed = parse_url($url);
	if (empty($parsed['host'])) {
		return false; // Relatīvs ceļš uz vietas
	}

	$host = strtolower($parsed['host']);
	if (preg_match('/(^|\.)exs\.lv$/i', $host) || preg_match('/(^|\.)coding\.lv$/i', $host)) {
		return false;
	}

	return true;
}

/**
 * Konvertē MIME tipu uz atbilstošo faila paplašinājumu.
 */
function image_mime_to_extension($mime, $original_url = '') {
	switch ($mime) {
		case 'image/jpeg':
			return '.jpg';
		case 'image/png':
			return '.png';
		case 'image/gif':
			return '.gif';
		case 'image/webp':
			return '.webp';
		case 'image/svg+xml':
			return '.svg';
		case 'image/avif':
			return '.avif';
		case 'image/bmp':
		case 'image/x-ms-bmp':
			return '.bmp';
	}

	if (!empty($original_url)) {
		$path = parse_url($original_url, PHP_URL_PATH);
		if ($path) {
			$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
			if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'avif'])) {
				return '.' . ($ext === 'jpeg' ? 'jpg' : $ext);
			}
		}
	}

	return '.jpg';
}

/**
 * Tīra un normalizē attālo URL.
 */
function clean_remote_url($raw_url) {
	$url = html_entity_decode($raw_url, ENT_QUOTES, 'UTF-8');
	$url = preg_replace('/(%5C|%C2%A0|\xc2\xa0)+$/i', '', $url);
	$url = trim($url, " \t\n\r\0\x0B.,;:!?)'\"[]<>\\");
	return $url;
}

/**
 * Veic faila lejupielādi ar cURL ar stingriem noilgumiem.
 */
function curl_download_file($url, $timeout = 4, $connect_timeout = 2) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connect_timeout);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');

	$data = curl_exec($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$effective_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	$curl_err = curl_error($ch);

	if ($http_code == 200 && !empty($data) && strlen($data) > 50) {
		$finfo = new finfo(FILEINFO_MIME_TYPE);
		$detected_mime = $finfo->buffer($data);
		return [
			'ok' => true,
			'data' => $data,
			'mime' => $detected_mime,
			'http_code' => $http_code,
			'effective_url' => $effective_url,
			'size' => strlen($data)
		];
	}

	return [
		'ok' => false,
		'http_code' => $http_code,
		'error' => $curl_err,
		'effective_url' => $effective_url
	];
}

/**
 * Sagatavo iespējamās Archive.org meklēšanas URL variācijas (ierobežots līdz augstākās ticamības variantiem).
 */
function get_archive_org_url_candidates($url) {
	$candidates = [$url];
	$parsed = parse_url($url);
	if (!$parsed || empty($parsed['host'])) {
		return $candidates;
	}

	$scheme = strtolower($parsed['scheme'] ?? 'http');
	$host = $parsed['host'];
	$path = $parsed['path'] ?? '/';
	$query = isset($parsed['query']) ? '?' . $parsed['query'] : '';

	// Ja ir vaicājuma parametri (query string), primārais alternatīvais variants ir bez tiem
	if ($query !== '') {
		$candidates[] = "{$scheme}://{$host}{$path}";
	}

	// Shēmas maiņa (https -> http, jo vecie arhīvi gandrīz vienmēr ir ar http)
	if ($scheme === 'https') {
		$candidates[] = "http://{$host}{$path}";
	}

	// Ierobežojam līdz maksimums 2 kandidātiem, lai nepārsniegtu noilgumu
	return array_slice(array_values(array_unique($candidates)), 0, 2);
}

/**
 * Meklē un lejupielādē attēlu no Archive.org (Wayback Machine).
 */
function fetch_from_archive_org($url) {
	$candidates = get_archive_org_url_candidates($url);

	foreach ($candidates as $candidate) {
		// Mēģinām tiešo Wayback 0id_ saiti (ātrs noilgums: 3s / savienojums 2s)
		$wb_url = 'https://web.archive.org/web/0id_/' . $candidate;
		$res = curl_download_file($wb_url, 3, 2);
		if ($res['ok'] && strpos($res['mime'], 'image/') === 0) {
			$res['source'] = 'archive.org';
			$res['archive_url'] = $res['effective_url'] ?? $wb_url;
			return $res;
		}
	}

	return ['ok' => false];
}

/**
 * Lejupielādē attēlu no attālās adreses. Ja sākotnējā adrese nav sasniedzama vai atgriež 404/kļūdu,
 * veic meklēšanu un lejupielādi no Archive.org (Wayback Machine).
 */
function fetch_remote_image($url, &$failed_direct_hosts = []) {
	$clean_url = clean_remote_url($url);
	if (strpos($clean_url, '//') === 0) {
		$clean_url = 'https:' . $clean_url;
	}

	$parsed = parse_url($clean_url);
	$host = strtolower($parsed['host'] ?? '');

	// 1. Mēģinām tiešo lejupielādi no oriģinālā avota (ja vien šis domēns jau nav zināms kā miris)
	$should_try_direct = empty($host) || !isset($failed_direct_hosts[$host]);

	if ($should_try_direct) {
		// Ātrs tiešais pieprasījums: timeout 3s, connect 2s
		$direct_res = curl_download_file($clean_url, 3, 2);
		if ($direct_res['ok'] && strpos($direct_res['mime'], 'image/') === 0) {
			$direct_res['source'] = 'direct';
			return $direct_res;
		}

		$fail_info = !empty($direct_res['error']) ? $direct_res['error'] : ('HTTP ' . ($direct_res['http_code'] ?: '0'));

		// Ja saimniekdators neeksistē vai nevar savienoties, atzīmējam to, lai nākamajiem šī raksta attēliem negaidītu lieki
		if (!empty($host) && (
			!empty($direct_res['error']) && (
				stripos($direct_res['error'], 'Could not resolve host') !== false ||
				stripos($direct_res['error'], 'timed out') !== false ||
				stripos($direct_res['error'], 'Connection refused') !== false
			)
		)) {
			$failed_direct_hosts[$host] = true;
			rehost_log("Host {$host} marked as unreachable ({$direct_res['error']}). Subsequent images will skip direct fetch.");
		}
	} else {
		$fail_info = "Host {$host} previously unreachable (skipped direct fetch)";
	}

	// 2. Ja tiešais pieprasījums nav sasniedzams vai ir 404/kļūda, meklējam Archive.org
	rehost_log("Direct fetch failed for {$clean_url} ({$fail_info}). Looking up in Archive.org...");

	if (strpos($clean_url, 'web.archive.org') === false) {
		$archive_res = fetch_from_archive_org($clean_url);
		if ($archive_res['ok']) {
			return $archive_res;
		}
	}

	return ['ok' => false, 'direct_fail' => $fail_info];
}

/**
 * Rehost darbību žurnālieraksts (CORE_PATH . /tmp/rehost.log un error_log).
 */
function rehost_log($message) {
	$line = '[' . date('Y-m-d H:i:s') . '] ' . $message . "\n";
	$log_file = defined('CORE_PATH') ? CORE_PATH . '/tmp/rehost.log' : __DIR__ . '/rehost.log';
	@file_put_contents($log_file, $line, FILE_APPEND);
	error_log('[Rehost] ' . $message);
}

/**
 * Atrod visus ārējos img tagus rakstā, lejupielādē un pārceļ uz img.exs.lv, aizstājot saites.
 */
function rehost_article_images($article) {
	global $db, $auth, $lang;

	if (!$auth->ok || $auth->level != 1 || empty($article) || empty($article->id)) {
		rehost_log("Access denied or invalid article for ID: " . ($article->id ?? 'null'));
		return ['status' => 'error', 'message' => 'Nav administratora tiesību.'];
	}

	@set_time_limit(60);
	$start_time = microtime(true);
	$max_budget_seconds = 45; // Maksimālais kopējais izpildes laiks, lai nepārsniegtu web servera 504 Gateway Timeout

	if (!defined('IMG_PATH')) {
		define('IMG_PATH', ROOT_PATH . '/img.exs.lv');
	}

	$text = $article->text;
	$intro = $article->intro ?? '';
	$combined = $text . ' ' . $intro;

	rehost_log("Article #{$article->id} ({$article->title}): Starting rehost scan.");

	// Atrodam visus img tagus un to src atribūtus, kā arī iespējamos [img] bbcode tagus
	preg_match_all('/<img\b[^>]*?\bsrc\s*=\s*(["\']?)([^"\'\s>]+)\1[^>]*>/i', $combined, $img_matches);
	preg_match_all('/\[img\]\s*([^\[\]\s]+)\s*\[\/img\]/i', $combined, $bb_matches);

	$raw_urls = array_unique(array_merge($img_matches[2] ?? [], $bb_matches[1] ?? []));
	if (empty($raw_urls)) {
		rehost_log("Article #{$article->id}: No image tags found in content.");
		return ['status' => 'success', 'count' => 0];
	}

	$urls_to_rehost = [];
	foreach ($raw_urls as $raw_url) {
		$clean_url = clean_remote_url($raw_url);
		if (is_external_image_url($clean_url)) {
			$urls_to_rehost[] = [
				'raw' => $raw_url,
				'clean' => $clean_url
			];
		}
	}

	if (empty($urls_to_rehost)) {
		rehost_log("Article #{$article->id}: All " . count($raw_urls) . " image URLs are internal. Nothing to rehost.");
		return ['status' => 'success', 'count' => 0];
	}

	rehost_log("Article #{$article->id}: Found " . count($urls_to_rehost) . " external image URL(s) to process.");

	$storage_dir = IMG_PATH . '/rehost/' . (int)$article->id;
	if (!is_dir($storage_dir)) {
		if (!rmkdir($storage_dir, 0777)) {
			rehost_log("ERROR: Article #{$article->id}: Could not create directory {$storage_dir}");
			return ['status' => 'error', 'message' => 'Neizdevās izveidot mapi attēlu glabāšanai serverī.'];
		}
	}

	if (!is_writable($storage_dir)) {
		rehost_log("ERROR: Article #{$article->id}: Storage directory {$storage_dir} is not writable.");
		return ['status' => 'error', 'message' => 'Attēlu mape nav pieejama ierakstīšanai serverī.'];
	}

	$rehosted_count = 0;
	$replacements = [];
	$failed_direct_hosts = [];
	$budget_exceeded = false;

	foreach ($urls_to_rehost as $item) {
		// Pārbaudām kopējo laika limitu pirms katra attēla apstrādes
		if ((microtime(true) - $start_time) > $max_budget_seconds) {
			rehost_log("Article #{$article->id}: Time budget ({$max_budget_seconds}s) reached. Stopping further fetches.");
			$budget_exceeded = true;
			break;
		}

		$raw_url = $item['raw'];
		$clean_url = $item['clean'];

		rehost_log("Article #{$article->id}: Fetching: {$clean_url}");
		$res = fetch_remote_image($clean_url, $failed_direct_hosts);
		if (!$res['ok']) {
			rehost_log("Article #{$article->id}: Failed to fetch image (direct & archive): {$clean_url}");
			continue;
		}

		if (($res['source'] ?? '') === 'archive.org') {
			rehost_log("Article #{$article->id}: Recovered via Archive.org: {$clean_url} -> " . ($res['archive_url'] ?? 'archive'));
		} else {
			rehost_log("Article #{$article->id}: Downloaded directly: {$clean_url}");
		}

		$ext = image_mime_to_extension($res['mime'], $clean_url);
		$path_part = parse_url($clean_url, PHP_URL_PATH) ?? '';
		$base_name = mkslug(pathinfo($path_part, PATHINFO_FILENAME));
		$hash = substr(md5($clean_url), 0, 8);
		$filename = ($base_name !== '' ? substr($base_name, 0, 40) . '_' : 'img_') . $hash . $ext;

		$dest_file = $storage_dir . '/' . $filename;
		if (!file_exists($dest_file) || filesize($dest_file) === 0) {
			$bytes_written = @file_put_contents($dest_file, $res['data']);
			if ($bytes_written === false || $bytes_written === 0) {
				rehost_log("ERROR: Article #{$article->id}: file_put_contents failed for {$dest_file} ({$clean_url})");
				continue;
			}
			@chmod($dest_file, 0664);
		}

		// PĀRBAUDE: Pārliecināmies, ka attēla fails REĀLI eksistē uz diska un nav tukšs pirms linku aizstāšanas
		clearstatcache(true, $dest_file);
		if (!file_exists($dest_file) || filesize($dest_file) < 50) {
			rehost_log("ERROR: Article #{$article->id}: File {$dest_file} not verified on disk after write attempt for {$clean_url}");
			continue;
		}

		$public_url = 'https://img.exs.lv/rehost/' . (int)$article->id . '/' . $filename;
		$replacements[$raw_url] = $public_url;
		$replacements[$clean_url] = $public_url;
		$replacements[htmlspecialchars($clean_url, ENT_QUOTES, 'UTF-8')] = $public_url;
		$replacements[htmlentities($clean_url, ENT_QUOTES, 'UTF-8')] = $public_url;
		$replacements[str_replace('&', '&amp;', $clean_url)] = $public_url;
		$trimmed_raw = rtrim($raw_url, '/');
		if ($trimmed_raw !== $raw_url) {
			$replacements[$trimmed_raw] = $public_url;
		}

		$rehosted_count++;
		rehost_log("Article #{$article->id}: Verified on disk and queued replacement: {$clean_url} -> {$public_url} (" . filesize($dest_file) . " bytes)");
	}

	if ($rehosted_count > 0 && !empty($replacements)) {
		// Aizstājam visus pārbaudītos linkus saturā
		foreach ($replacements as $old_str => $new_str) {
			$text = str_replace($old_str, $new_str, $text);
			if (!empty($intro)) {
				$intro = str_replace($old_str, $new_str, $intro);
			}
		}

		// Saglabājam versiju vēsturē pirms ieraksta atjaunošanas
		$lastmod = $db->get_row("SELECT * FROM pages_ver WHERE pid = '" . (int)$article->id . "' ORDER BY id DESC LIMIT 1");
		$lastmodu = (!empty($lastmod)) ? $lastmod->nextmod : $article->author;
		$db->query("INSERT INTO pages_ver (pid,time,title,text,nextmod,category,is_wide,ip) VALUES (
			'" . (int)$article->id . "',
			'" . time() . "',
			'" . sanitize($article->title) . "',
			'" . sanitize($article->text) . "',
			'" . sanitize($lastmodu) . "',
			'" . (int)$article->category . "',
			'" . (int)$article->is_wide . "',
			'" . sanitize($auth->ip) . "'
		)");

		// Atjaunojam pages ierakstu
		$db->query("UPDATE `pages` SET `text` = ('" . sanitize($text) . "'), `intro` = ('" . sanitize($intro) . "') WHERE `id` = '" . (int)$article->id . "' LIMIT 1");

		if (is_object($auth) && method_exists($auth, 'log')) {
			$auth->log('Pārnesa raksta attēlus (' . $rehosted_count . ' attēli)', 'pages', $article->id);
		}
		clear_forum_cache($article->lang ?? $lang);

		rehost_log("Article #{$article->id}: Successfully rehosted {$rehosted_count} image(s) and updated article.");
		$result = ['status' => 'success', 'count' => $rehosted_count];
		if ($budget_exceeded) {
			$result['notice'] = 'Sasniegts laika limits pirms visu attēlu apstrādes. Noklikšķiniet vēlreiz uz "pārnest attēlus", lai turpinātu ar atlikušajiem.';
		}
		return $result;
	}

	if ($budget_exceeded) {
		return ['status' => 'error', 'message' => 'Noilgums: attēlu serveri neatbildēja pietiekami ātri. Lūdzu, mēģiniet vēlreiz.'];
	}

	rehost_log("Article #{$article->id}: No images could be verified on disk. Original article left unchanged.");
	return ['status' => 'error', 'message' => 'Neizdevās lejupielādēt vai saglabāt nevienu no ārējiem attēliem. Raksta saturs netika mainīts.'];
}

