<?php

$expires = 3600;
header('Pragma: public');
header('Cache-Control: max-age=' . $expires);
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');

$cache = true;
$cachedir = dirname(__FILE__) . '/cache/frontend';
$cssdir = dirname(__FILE__) . '/css';
$jsdir = dirname(__FILE__) . '/js';

// Determine the directory and type we should use
switch ($_GET['type']) {
	case 'css':
		$base = realpath($cssdir);
		break;
	case 'javascript':
		$base = realpath($jsdir);
		break;
	default:
		header("HTTP/1.0 503 Not Implemented");
		exit;
};

$type = $_GET['type'];
$elements = explode(',', $_GET['files']);

// Determine last modification date of the files
$lastmodified = 0;
while (list(, $element) = each($elements)) {
	$path = realpath($base . '/' . $element);

	if (($type == 'javascript' && substr($path, -3) != '.js') ||
			($type == 'css' && substr($path, -4) != '.css')) {
		header("HTTP/1.0 403 Forbidden");
		exit;
	}

	if (substr($path, 0, strlen($base)) != $base || !file_exists($path)) {
		header("HTTP/1.0 404 Not Found");
		exit;
	}

	$lastmodified = max($lastmodified, filemtime($path));
}

// Send Etag hash
$hash = $lastmodified . '-' . md5($_GET['files']);
header("Etag: \"" . $hash . "\"");

if (isset($_SERVER['HTTP_IF_NONE_MATCH']) &&
		stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) == '"' . $hash . '"') {
	// Return visit and no modifications, so do not send anything
	header("HTTP/1.0 304 Not Modified");
	header('Content-Length: 0');
} else {
	// First time visit or files were modified
	if ($cache) {

		// Determine supported compression method
		$encoding = false;
		if (!empty($_SERVER['HTTP_ACCEPT_ENCODING'])) {
			if(strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
				$encoding = 'gzip';
			}
		}

		// Check for buggy versions of Internet Explorer
		if (!empty($_SERVER['HTTP_USER_AGENT']) && !strstr($_SERVER['HTTP_USER_AGENT'], 'Opera') &&
				preg_match('/^Mozilla\/4\.0 \(compatible; MSIE ([0-9]\.[0-9])/i', $_SERVER['HTTP_USER_AGENT'], $matches)) {
			$version = floatval($matches[1]);

			if ($version < 6)
				$encoding = false;

			if ($version == 6 && !strstr($_SERVER['HTTP_USER_AGENT'], 'EV1'))
				$encoding = false;
		}

		// Try the cache first to see if the combined files were already generated
		$cachefile = 'cache-' . $hash . '.' . $type . ($encoding !== false ? '.' . $encoding : '');

		if (file_exists($cachedir . '/' . $cachefile)) {
			if ($fp = fopen($cachedir . '/' . $cachefile, 'rb')) {

				if (!empty($encoding)) {
					header("Content-Encoding: " . $encoding);
				}

				header("Content-Type: text/" . $type);
				header("Content-Length: " . filesize($cachedir . '/' . $cachefile));

				fpassthru($fp);
				fclose($fp);
				exit;
			}
		}
	}

	// Get contents of the files
	$contents = '';
	reset($elements);
	$i = 0;
	while (list(, $element) = each($elements)) {
		$path = realpath($base . '/' . $element);
		if ($i == 0) {
			$contents = file_get_contents($path);
		} else {
			$contents .= "\n\n" . file_get_contents($path);
		}
		$i++;
	}

	// Send Content-Type
	header("Content-Type: text/" . $type);

	if (!empty($encoding)) {
		// Send compressed contents
		$contents = gzencode($contents, 9, FORCE_GZIP);
		header("Content-Encoding: " . $encoding);
		header('Content-Length: ' . strlen($contents));
		echo $contents;
	} else {
		// Send regular contents
		header('Content-Length: ' . strlen($contents));
		echo $contents;
	}

	// Store cache
	if ($cache) {
		if ($fp = fopen($cachedir . '/' . $cachefile, 'wb')) {
			fwrite($fp, $contents);
			fclose($fp);
		}
	}
}
