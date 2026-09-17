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
 * Lejupielādē attēlu no attālās adreses ar cURL un Wayback Machine rezerves variantu.
 */
function fetch_remote_image($url) {
	$url = html_entity_decode($url, ENT_QUOTES, 'UTF-8');
	if (strpos($url, '//') === 0) {
		$url = 'https:' . $url;
	}

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
	curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');

	$data = curl_exec($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	if ($http_code == 200 && !empty($data) && strlen($data) > 50) {
		$finfo = new finfo(FILEINFO_MIME_TYPE);
		$detected_mime = $finfo->buffer($data);
		if (strpos($detected_mime, 'image/') === 0) {
			return [
				'ok' => true,
				'data' => $data,
				'mime' => $detected_mime,
			];
		}
	}

	// Ja tiešais pieprasījums neizdevās, mēģinām caur Wayback Machine (ja vien tas jau nav archive.org)
	if (strpos($url, 'web.archive.org') === false) {
		$wb_url = 'https://web.archive.org/web/0id_/' . $url;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $wb_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');

		$data = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($http_code == 200 && !empty($data) && strlen($data) > 50) {
			$finfo = new finfo(FILEINFO_MIME_TYPE);
			$detected_mime = $finfo->buffer($data);
			if (strpos($detected_mime, 'image/') === 0) {
				return [
					'ok' => true,
					'data' => $data,
					'mime' => $detected_mime,
				];
			}
		}
	}

	return ['ok' => false];
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

	// Atrodam visus img tagus un to src atribūtus
	preg_match_all('/<img\b[^>]*?\bsrc\s*=\s*(["\']?)([^"\'\s>]+)\1[^>]*>/i', $combined, $matches);
	if (empty($matches[2])) {
		rehost_log("Article #{$article->id}: No <img> tags found in content.");
		return ['status' => 'success', 'count' => 0];
	}

	$raw_urls = array_unique($matches[2]);
	$urls_to_rehost = [];
	foreach ($raw_urls as $raw_url) {
		$clean_url = html_entity_decode(trim($raw_url), ENT_QUOTES, 'UTF-8');
		if (is_external_image_url($clean_url)) {
			$urls_to_rehost[] = [
				'raw' => $raw_url,
				'clean' => $clean_url
			];
		}
	}

	if (empty($urls_to_rehost)) {
		rehost_log("Article #{$article->id}: All " . count($raw_urls) . " <img> URLs are internal. Nothing to rehost.");
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

