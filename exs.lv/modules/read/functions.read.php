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
 * Veic faila lejupielādi ar cURL.
 */
function curl_download_file($url, $timeout = 15, $connect_timeout = 6) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
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
 * Sagatavo iespējamās Archive.org meklēšanas URL variācijas.
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
	$other_scheme = ($scheme === 'https') ? 'http' : 'https';

	// Mainām shēmu
	$candidates[] = "{$other_scheme}://{$host}{$path}{$query}";

	// Ja ir vaicājuma parametri (query string), pievienojam variantu bez tiem
	if ($query !== '') {
		$candidates[] = "{$scheme}://{$host}{$path}";
		$candidates[] = "{$other_scheme}://{$host}{$path}";
	}

	// www. un bez-www variācijas
	if (stripos($host, 'www.') === 0) {
		$non_www = substr($host, 4);
		$candidates[] = "{$scheme}://{$non_www}{$path}{$query}";
		$candidates[] = "{$other_scheme}://{$non_www}{$path}{$query}";
		if ($query !== '') {
			$candidates[] = "{$scheme}://{$non_www}{$path}";
			$candidates[] = "{$other_scheme}://{$non_www}{$path}";
		}
	} else {
		$www = 'www.' . $host;
		$candidates[] = "{$scheme}://{$www}{$path}{$query}";
		$candidates[] = "{$other_scheme}://{$www}{$path}{$query}";
		if ($query !== '') {
			$candidates[] = "{$scheme}://{$www}{$path}";
			$candidates[] = "{$other_scheme}://{$www}{$path}";
		}
	}

	return array_values(array_unique($candidates));
}

/**
 * Meklē un lejupielādē attēlu no Archive.org (Wayback Machine).
 */
function fetch_from_archive_org($url) {
	$candidates = get_archive_org_url_candidates($url);

	foreach ($candidates as $candidate) {
		// 1. Mēģinām tiešo Wayback 0id_ saiti
		$wb_url = 'https://web.archive.org/web/0id_/' . $candidate;
		$res = curl_download_file($wb_url, 15, 6);
		if ($res['ok'] && strpos($res['mime'], 'image/') === 0) {
			$res['source'] = 'archive.org';
			$res['archive_url'] = $res['effective_url'] ?? $wb_url;
			return $res;
		}

		// 2. Ja 0id_ neatrada, meklējam snapshotu caur Wayback CDX API
		$cdx_url = 'https://web.archive.org/cdx/search/cdx?url=' . urlencode($candidate) . '&limit=1&output=json';
		$cdx_res = curl_download_file($cdx_url, 8, 4);
		if ($cdx_res['ok']) {
			$cdx_data = json_decode($cdx_res['data'], true);
			if (is_array($cdx_data) && count($cdx_data) >= 2 && !empty($cdx_data[1][1]) && !empty($cdx_data[1][2])) {
				$timestamp = $cdx_data[1][1];
				$orig_url = $cdx_data[1][2];
				$exact_wb = "https://web.archive.org/web/{$timestamp}id_/{$orig_url}";
				$exact_res = curl_download_file($exact_wb, 15, 6);
				if ($exact_res['ok'] && strpos($exact_res['mime'], 'image/') === 0) {
					$exact_res['source'] = 'archive.org';
					$exact_res['archive_url'] = $exact_res['effective_url'] ?? $exact_wb;
					return $exact_res;
				}
			}
		}

		usleep(100000); // 100ms pauze starp mēģinājumiem
	}

	return ['ok' => false];
}

/**
 * Lejupielādē attēlu no attālās adreses. Ja sākotnējā adrese nav sasniedzama vai atgriež 404/kļūdu,
 * veic meklēšanu un lejupielādi no Archive.org (Wayback Machine).
 */
function fetch_remote_image($url) {
	$clean_url = clean_remote_url($url);
	if (strpos($clean_url, '//') === 0) {
		$clean_url = 'https:' . $clean_url;
	}

	// 1. Mēģinām tiešo lejupielādi no oriģinālā avota
	$direct_res = curl_download_file($clean_url, 10, 5);
	if ($direct_res['ok'] && strpos($direct_res['mime'], 'image/') === 0) {
		$direct_res['source'] = 'direct';
		return $direct_res;
	}

	// 2. Ja tiešais pieprasījums nav sasniedzams vai ir 404/kļūda, meklējam Archive.org
	$fail_info = !empty($direct_res['error']) ? $direct_res['error'] : ('HTTP ' . ($direct_res['http_code'] ?: '0'));
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

	foreach ($urls_to_rehost as $item) {
		$raw_url = $item['raw'];
		$clean_url = $item['clean'];

		rehost_log("Article #{$article->id}: Fetching: {$clean_url}");
		$res = fetch_remote_image($clean_url);
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
		return ['status' => 'success', 'count' => $rehosted_count];
	}

	rehost_log("Article #{$article->id}: No images could be verified on disk. Original article left unchanged.");
	return ['status' => 'error', 'message' => 'Neizdevās lejupielādēt vai saglabāt nevienu no ārējiem attēliem. Raksta saturs netika mainīts.'];
}

