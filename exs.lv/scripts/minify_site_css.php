<?php
/**
 * Bundle & Minify Site CSS files and update template inclusions & PHP files with single hashed CSS filenames
 */

require_once __DIR__ . '/../includes/cssmin.php';

$css_dir = realpath(__DIR__ . '/../css');
if (!is_dir($css_dir)) {
	die("CSS directory not found: $css_dir\n");
}

$cssmin = new CSSmin();

// Clean up old minified CSS files in /css/
$old_min_css = glob($css_dir . '/*.min.css');
if ($old_min_css) {
	foreach ($old_min_css as $f) {
		unlink($f);
		echo "Removed old minified CSS: " . basename($f) . "\n";
	}
}

// Define template CSS bundles (concatenates source files into a single minified CSS per template)
$template_bundles = [
	'main.tpl' => [
		'file' => realpath(__DIR__ . '/../tmpl/main.tpl'),
		'output_prefix' => 'main',
		'sources' => ['responsive.css', 'bs.css'],
		'pattern' => '/<link\s+rel="stylesheet"\s+href="([^"]*\/css\/)[^"]*"\s*media="all">/i',
	],
	'main_3.tpl' => [
		'file' => realpath(__DIR__ . '/../tmpl/main_3.tpl'),
		'output_prefix' => 'main_3',
		'sources' => ['coding.css', 'prettify.css'],
		'pattern' => '/<link\s+rel="stylesheet"\s+href="([^"]*\/css\/)[^"]*coding[^"]*">/i',
	],
	'main_7.tpl' => [
		'file' => realpath(__DIR__ . '/../tmpl/main_7.tpl'),
		'output_prefix' => 'main_7',
		'sources' => ['core.css', 'lol.css'],
		'pattern' => '/<link\s+rel="stylesheet"\s+href="([^"]*\/css\/)[^"]*lol[^"]*">/i',
	],
	'main_8.tpl' => [
		'file' => realpath(__DIR__ . '/../tmpl/main_8.tpl'),
		'output_prefix' => 'main_8',
		'sources' => ['mobile.css'],
		'pattern' => '/<link\s+rel="stylesheet"\s+href="([^"]*\/css\/)[^"]*mobile[^"]*">/i',
	],
	'main_9.tpl' => [
		'file' => realpath(__DIR__ . '/../tmpl/main_9.tpl'),
		'output_prefix' => 'main_9',
		'sources' => ['core.css', 'runescape.css'],
		'pattern' => '/<link\s+rel="stylesheet"\s+href="([^"]*\/css\/)[^"]*runescape[^"]*">/i',
	],
];

// 1. Process Template Bundles into Single CSS Files
foreach ($template_bundles as $tpl_name => $bundle) {
	if (!file_exists($bundle['file'])) {
		echo "Template file not found: {$bundle['file']}\n";
		continue;
	}

	$combined_css = '';
	foreach ($bundle['sources'] as $src_file) {
		$src_path = $css_dir . '/' . $src_file;
		if (file_exists($src_path)) {
			$combined_css .= file_get_contents($src_path) . "\n\n";
		} else {
			echo "Source CSS file not found: $src_path\n";
		}
	}

	if (empty($combined_css)) {
		continue;
	}

	$hash = substr(md5($combined_css), 0, 8);
	$min_filename = $bundle['output_prefix'] . '.' . $hash . '.min.css';
	$target_path = $css_dir . '/' . $min_filename;

	try {
		$minified_code = $cssmin->run($combined_css);
		file_put_contents($target_path, $minified_code);
		echo "Bundle CSS created: $min_filename (" . strlen($combined_css) . "B -> " . strlen($minified_code) . "B)\n";
	} catch (Exception $e) {
		echo "Error minifying bundle $tpl_name: " . $e->getMessage() . "\n";
		continue;
	}

	// Update the template file to use the single minified bundle
	$tpl_content = file_get_contents($bundle['file']);
	if (preg_match($bundle['pattern'], $tpl_content, $matches)) {
		$prefix = $matches[1]; // e.g. "{static-server}/css/" or "/css/"
		$has_media = strpos($matches[0], 'media="all"') !== false ? ' media="all"' : '';
		$new_link = '<link rel="stylesheet" href="' . $prefix . $min_filename . '"' . $has_media . '>';
		$new_tpl_content = preg_replace($bundle['pattern'], $new_link, $tpl_content);
		file_put_contents($bundle['file'], $new_tpl_content);
		echo "Updated " . basename($bundle['file']) . " to reference single CSS file: $min_filename\n";
	}
}

// 2. Process Individual CSS Files for Module $add_css and tinymce content_css
$individual_sources = glob($css_dir . '/*.css');
$indiv_mappings = [];

foreach ($individual_sources as $src_path) {
	$filename = basename($src_path);
	if (substr($filename, -8) === '.min.css') {
		continue;
	}

	$code = file_get_contents($src_path);
	$hash = substr(md5($code), 0, 8);
	$base_name = pathinfo($filename, PATHINFO_FILENAME);
	$min_filename = $base_name . '.' . $hash . '.min.css';
	$target_path = $css_dir . '/' . $min_filename;

	// If not already written as part of bundle name conflict, write it
	if (!file_exists($target_path)) {
		try {
			$minified = $cssmin->run($code);
			file_put_contents($target_path, $minified);
			echo "Minified CSS: $filename -> $min_filename (" . strlen($code) . "B -> " . strlen($minified) . "B)\n";
		} catch (Exception $e) {
			echo "Error minifying $filename: " . $e->getMessage() . "\n";
		}
	}
	$indiv_mappings[$base_name] = $min_filename;
}

// Update PHP files ($add_css[] references)
$php_files = array_merge(
	[realpath(__DIR__ . '/../index.php')],
	glob(__DIR__ . '/../modules/*/*.php') ?: []
);

foreach ($php_files as $php_file) {
	if (!$php_file || !file_exists($php_file)) {
		continue;
	}

	$content = file_get_contents($php_file);
	$orig = $content;

	foreach ($indiv_mappings as $base_name => $min_filename) {
		$pattern = '/(\$add_css\[\]\s*=\s*[\'"])' . preg_quote($base_name, '/') . '(\.[a-f0-9]+)?(\.min)?\.css([\'"];)/i';
		$content = preg_replace($pattern, '${1}' . $min_filename . '${4}', $content);
	}

	if ($content !== $orig) {
		file_put_contents($php_file, $content);
		echo "Updated " . basename($php_file) . " with minified \$add_css references\n";
	}
}

// Update tinymce content_css in template files
$all_tpls = glob(__DIR__ . '/../tmpl/*.tpl') ?: [];
if (isset($indiv_mappings['style'])) {
	$style_min = $indiv_mappings['style'];
	foreach ($all_tpls as $tpl_path) {
		$content = file_get_contents($tpl_path);
		$pattern = '/content_css:\s*([\'"])([^"\'\s]*\/css\/)style(\.[a-f0-9]+)?(\.min)?\.css\1/i';
		if (preg_match($pattern, $content)) {
			$new_content = preg_replace($pattern, 'content_css: ${1}${2}' . $style_min . '${1}', $content);
			file_put_contents($tpl_path, $new_content);
			echo "Updated " . basename($tpl_path) . " tinymce content_css reference\n";
		}
	}
}

echo "Site CSS Minification & Bundling Complete!\n";
