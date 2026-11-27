<?php

$allowed_domains = [];
foreach ($config_domains as $domain) {
	if ($domain['domain'] !== 'secure.exs.lv') {
		$allowed_domains[] = $domain['domain'];
		$allowed_domains[] = 'm.' . $domain['domain'];
	}
}

$referer = $_SERVER['HTTP_REFERER'] ?? '';
$host = $referer !== '' ? parse_url($referer, PHP_URL_HOST) : null;
if (!in_array($host, $allowed_domains, true)) {
	$redirect = 'https://exs.lv/';
} else {
	$redirect = $referer;
}

redirect($redirect);
exit;

