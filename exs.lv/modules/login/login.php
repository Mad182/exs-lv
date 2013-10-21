<?php

if($auth->ok) {

	$allowed_domains = array();
	foreach($config_domains as $domain) {
		if($domain['domain'] !== 'secure.exs.lv') {
			$allowed_domains[] = $domain['domain'];
			$allowed_domains[] = 'm.' . $domain['domain'];
		}
	}
	
	$host = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
	if(!in_array($host, $allowed_domains)) {
		$redirect = 'http://exs.lv/';
	} else {
		$redirect = $_SERVER['HTTP_REFERER'];
	}
	
	redirect($redirect);
	
}
