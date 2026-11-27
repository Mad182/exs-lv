<?php
/**
 * Bundle & Minify Site JS files at deploy/build time and update template/PHP inclusions
 * with single hashed static JS filenames (eliminating runtime combine.php minification).
 */
if (PHP_SAPI !== 'cli') {
	http_response_code(403);
	exit;
}

require_once __DIR__ . '/../includes/jsmin.php';

$js_dir = realpath(__DIR__ . '/../js');
if (!$js_dir || !is_dir($js_dir)) {
	die("JS directory not found: $js_dir\n");
}

// Clean up ONLY previously generated hashed bundles (*.<8-hex>.min.js), never source vendor *.min.js files
$existing_min_js = glob($js_dir . '/*.min.js') ?: [];
foreach ($existing_min_js as $f) {
	if (preg_match('/\.[a-f0-9]{8}\.min\.js$/', basename($f))) {
		unlink($f);
		echo "Removed old generated JS: " . basename($f) . "\n";
	}
}

/**
 * Minify a single source JS file unless it is already a pre-minified vendor library (*.min.js).
 */
function minify_js_source($src_path, $rel_name) {
	$code = file_get_contents($src_path);
	if ($code === false) {
		return '';
	}
	if (substr($rel_name, -7) === '.min.js') {
		return trim($code);
	}
	return trim(JSMin::minify($code));
}

// 1. Multi-file site JS bundles (replacing combine.php comma-separated URLs)
$js_bundles = [
	'main' => [
		'sources' => [
			'jquery-1.10.2.min.js',
			'html5.js',
			'bootstrap.min.js',
			'fluidvids.min.js',
			'jquery.sidr.min.js',
			'jquery.touchSwipe.min.js',
			'jquery.swipemenu.init.js',
			'swfobject.js',
			'tinycon.min.js',
			'jquery.cookie.js',
			'jquery.fancybox.js',
			'jquery.raty.min.js',
			'mcp.js',
			'j.js',
			'exs.js',
		],
		'targets' => [
			[
				'file' => realpath(__DIR__ . '/../tmpl/main.tpl'),
				'pattern' => '/(src="[^"]*\/js\/)(?:jquery-1\.10\.2\.min\.js,[^"]+|main\.[a-f0-9]{8}\.min\.js)(")/i',
			],
		],
	],
	'main_3' => [
		'sources' => [
			'jquery.min.js',
			'tinycon.min.js',
			'jquery.cookie.js',
			'jquery.fancybox.js',
			'jquery.raty.min.js',
			'mcp.js',
			'j.js',
			'prettify/prettify.js',
		],
		'targets' => [
			[
				'file' => realpath(__DIR__ . '/../tmpl/main_3.tpl'),
				'pattern' => '/(src="[^"]*\/js\/)(?:jquery\.min\.js,[^"]+|main_3\.[a-f0-9]{8}\.min\.js)(")/i',
			],
		],
	],
	'main_7' => [
		'sources' => [
			'jquery.min.js',
			'tinycon.min.js',
			'jquery.cookie.js',
			'jquery.fancybox.js',
			'jquery.raty.min.js',
			'jquery.cycle.js',
			'mcp.js',
			'j.js',
		],
		'targets' => [
			[
				'file' => realpath(__DIR__ . '/../tmpl/main_7.tpl'),
				'pattern' => '/(src="[^"]*\/js\/)(?:jquery\.min\.js,[^"]+|main_7\.[a-f0-9]{8}\.min\.js)(")/i',
			],
		],
	],
	'main_9' => [
		'sources' => [
			'jquery.min.js',
			'tinycon.min.js',
			'jquery.cookie.js',
			'jquery.fancybox.js',
			'jquery.raty.min.js',
			'runescape.js',
			'mcp.js',
			'j.js',
		],
		'targets' => [
			[
				'file' => realpath(__DIR__ . '/../tmpl/main_9.tpl'),
				'pattern' => '/(src="[^"]*\/js\/)(?:jquery\.min\.js,[^"]+|main_9\.[a-f0-9]{8}\.min\.js)(")/i',
			],
		],
	],
	'm_main_sidr' => [
		'sources' => [
			'jquery.min.js',
			'jquery.sidr.js',
			'mobile.js',
		],
		'targets' => [
			[
				'file' => realpath(__DIR__ . '/../../m.exs.lv/tmpl/main_1.tpl'),
				'pattern' => '/(src="[^"]*\/js\/)(?:jquery\.min\.js,[^"]+|m_main_sidr\.[a-f0-9]{8}\.min\.js)(")/i',
			],
			[
				'file' => realpath(__DIR__ . '/../../m.exs.lv/tmpl/main_3.tpl'),
				'pattern' => '/(src="[^"]*\/js\/)(?:jquery\.min\.js,[^"]+|m_main_sidr\.[a-f0-9]{8}\.min\.js)(")/i',
			],
		],
	],
	'm_main' => [
		'sources' => [
			'jquery.min.js',
			'mobile.js',
		],
		'targets' => [
			[
				'file' => realpath(__DIR__ . '/../../m.exs.lv/tmpl/main_7.tpl'),
				'pattern' => '/(src="[^"]*\/js\/)(?:jquery\.min\.js,[^"]+|m_main\.[a-f0-9]{8}\.min\.js)(")/i',
			],
			[
				'file' => realpath(__DIR__ . '/../../m.exs.lv/tmpl/main_9.tpl'),
				'pattern' => '/(src="[^"]*\/js\/)(?:jquery\.min\.js,[^"]+|m_main\.[a-f0-9]{8}\.min\.js)(")/i',
			],
		],
	],
];

foreach ($js_bundles as $bundle_name => $bundle) {
	$parts = [];
	$raw_bytes = 0;

	foreach ($bundle['sources'] as $rel_src) {
		$src_path = $js_dir . '/' . $rel_src;
		if (!is_file($src_path)) {
			echo "Source JS file not found: $src_path\n";
			continue;
		}
		$raw_bytes += filesize($src_path);
		try {
			$parts[] = minify_js_source($src_path, $rel_src);
		} catch (Exception $e) {
			echo "Error minifying $rel_src for bundle $bundle_name: " . $e->getMessage() . "\n";
			$parts[] = trim(file_get_contents($src_path));
		}
	}

	if (empty($parts)) {
		continue;
	}

	$bundled_code = implode(";\n", $parts) . "\n";
	$hash = substr(md5($bundled_code), 0, 8);
	$min_filename = $bundle_name . '.' . $hash . '.min.js';
	$target_path = $js_dir . '/' . $min_filename;

	file_put_contents($target_path, $bundled_code);
	echo "Bundle JS created: $min_filename ({$raw_bytes}B -> " . strlen($bundled_code) . "B)\n";

	foreach ($bundle['targets'] as $target) {
		if (!$target['file'] || !is_file($target['file'])) {
			continue;
		}
		$content = file_get_contents($target['file']);
		$new_content = preg_replace($target['pattern'], '${1}' . $min_filename . '${2}', $content);
		if ($new_content !== null && $new_content !== $content) {
			file_put_contents($target['file'], $new_content);
			echo "Updated " . $target['file'] . " -> $min_filename\n";
		}
	}
}

// 2. Standalone site JS files (guest.js, game-chat.js)
$standalone_js = [
	'guest' => [
		'source' => 'guest.js',
		'targets' => [
			realpath(__DIR__ . '/../tmpl/main.tpl'),
			realpath(__DIR__ . '/../tmpl/main_3.tpl'),
			realpath(__DIR__ . '/../tmpl/main_7.tpl'),
			realpath(__DIR__ . '/../tmpl/main_9.tpl'),
		],
	],
	'game-chat' => [
		'source' => 'game-chat.js',
		'targets' => [
			realpath(__DIR__ . '/../includes/functions.core.php'),
		],
	],
];

foreach ($standalone_js as $base_name => $cfg) {
	$src_path = $js_dir . '/' . $cfg['source'];
	if (!is_file($src_path)) {
		echo "Standalone JS source not found: $src_path\n";
		continue;
	}
	$raw_code = file_get_contents($src_path);
	try {
		$min_code = trim(JSMin::minify($raw_code)) . "\n";
	} catch (Exception $e) {
		echo "Error minifying {$cfg['source']}: " . $e->getMessage() . "\n";
		continue;
	}

	$hash = substr(md5($min_code), 0, 8);
	$min_filename = $base_name . '.' . $hash . '.min.js';
	file_put_contents($js_dir . '/' . $min_filename, $min_code);
	echo "Minified JS: {$cfg['source']} -> $min_filename (" . strlen($raw_code) . "B -> " . strlen($min_code) . "B)\n";

	$pattern = '/(\/js\/)' . preg_quote($base_name, '/') . '(?:\.[a-f0-9]{8})?(?:\.min)?\.js/i';
	foreach ($cfg['targets'] as $target_file) {
		if (!$target_file || !is_file($target_file)) {
			continue;
		}
		$content = file_get_contents($target_file);
		$new_content = preg_replace($pattern, '${1}' . $min_filename, $content);
		if ($new_content !== null && $new_content !== $content) {
			file_put_contents($target_file, $new_content);
			echo "Updated " . basename($target_file) . " -> $min_filename\n";
		}
	}
}

echo "Site JS Minification & Bundling Complete!\n";
