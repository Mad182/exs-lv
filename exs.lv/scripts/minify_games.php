<?php
/**
 * Minify Game JS and CSS files and update template inclusions with hashed filenames
 */

require_once __DIR__ . '/../includes/jsmin.php';
require_once __DIR__ . '/../includes/cssmin.php';

$game_modules = [
	'2048' => ['js' => ['2048.js'], 'css' => ['2048.css']],
	'augsup' => ['js' => ['augsup.js'], 'css' => ['augsup.css']],
	'desas' => ['js' => ['desas.js'], 'css' => ['desas.css']],
	'flappy' => ['js' => ['flappy.js'], 'css' => ['flappy.css']],
	'invaders' => ['js' => ['invaders.js'], 'css' => ['invaders.css']],
	'memory' => ['js' => ['memory.js'], 'css' => ['memory.css']],
	'minu-mekletajs' => ['js' => ['minu-mekletajs.js'], 'css' => ['minu-mekletajs.css']],
	'rulete' => ['js' => ['rulete.js'], 'css' => ['rulete.css']],
	'runner' => ['js' => ['runner.js'], 'css' => ['runner.css']],
	'snake' => ['js' => ['jquery.snake.js', 'common.js'], 'css' => ['snake.css']],
	'speles' => ['js' => [], 'css' => ['speles.css']],
	'sudoku' => ['js' => ['sudoku.js'], 'css' => ['sudoku.css']],
	'tetris' => ['js' => ['tetris.js'], 'css' => ['tetris.css']],
	'tic-tac-toe' => ['js' => ['tictactoe.js'], 'css' => []],
	'ut99' => ['js' => ['cacheAppData.js', 'ut99.js'], 'css' => ['ut99.css']],
	'vardes' => ['js' => ['vardes.js'], 'css' => ['vardes.css']],
	'wordle' => ['js' => ['wordle.js'], 'css' => ['wordle.css']],
];

$modules_dir = realpath(__DIR__ . '/../modules');
$cssmin = new CSSmin();

foreach ($game_modules as $module_name => $assets) {
	$dir = $modules_dir . '/' . $module_name;
	if (!is_dir($dir)) {
		echo "Module directory not found: $module_name\n";
		continue;
	}

	// Remove old minified JS files
	$old_min_js = glob($dir . '/*.min.js');
	if ($old_min_js) {
		foreach ($old_min_js as $f) {
			unlink($f);
			echo "Removed old minified JS: " . basename($f) . "\n";
		}
	}

	// Remove old minified CSS files
	$old_min_css = glob($dir . '/*.min.css');
	if ($old_min_css) {
		foreach ($old_min_css as $f) {
			unlink($f);
			echo "Removed old minified CSS: " . basename($f) . "\n";
		}
	}

	// Process JS files
	if (!empty($assets['js'])) {
		foreach ($assets['js'] as $js_filename) {
			$source_path = $dir . '/' . $js_filename;
			if (!file_exists($source_path)) {
				echo "Source JS file not found: $source_path\n";
				continue;
			}

			$source_code = file_get_contents($source_path);
			$hash = substr(md5($source_code), 0, 8);
			$base_name = pathinfo($js_filename, PATHINFO_FILENAME);
			$min_filename = $base_name . '.' . $hash . '.min.js';
			$target_path = $dir . '/' . $min_filename;

			try {
				$minified_code = JSMin::minify($source_code);
				file_put_contents($target_path, $minified_code);
				echo "Minified JS: $js_filename -> $min_filename (" . strlen($source_code) . "B -> " . strlen($minified_code) . "B)\n";
			} catch (Exception $e) {
				echo "Error minifying JS $js_filename: " . $e->getMessage() . "\n";
				continue;
			}

			// Update templates
			$tpl_files = glob($dir . '/*.tpl');
			foreach ($tpl_files as $tpl_file) {
				$tpl_content = file_get_contents($tpl_file);
				$pattern = '/\/modules\/' . preg_quote($module_name, '/') . '\/' . preg_quote($base_name, '/') . '(\.[a-f0-9]+)?(\.min)?\.js(\?[^"\'\s>]*)?/';
				$replacement = '/modules/' . $module_name . '/' . $min_filename;

				if (preg_match($pattern, $tpl_content)) {
					$new_tpl_content = preg_replace($pattern, $replacement, $tpl_content);
					file_put_contents($tpl_file, $new_tpl_content);
					echo "Updated " . basename($tpl_file) . " to reference $min_filename\n";
				}
			}
		}
	}

	// Process CSS files
	if (!empty($assets['css'])) {
		foreach ($assets['css'] as $css_filename) {
			$source_path = $dir . '/' . $css_filename;
			if (!file_exists($source_path)) {
				echo "Source CSS file not found: $source_path\n";
				continue;
			}

			$source_code = file_get_contents($source_path);
			$hash = substr(md5($source_code), 0, 8);
			$base_name = pathinfo($css_filename, PATHINFO_FILENAME);
			$min_filename = $base_name . '.' . $hash . '.min.css';
			$target_path = $dir . '/' . $min_filename;

			try {
				$minified_code = $cssmin->run($source_code);
				file_put_contents($target_path, $minified_code);
				echo "Minified CSS: $css_filename -> $min_filename (" . strlen($source_code) . "B -> " . strlen($minified_code) . "B)\n";
			} catch (Exception $e) {
				echo "Error minifying CSS $css_filename: " . $e->getMessage() . "\n";
				continue;
			}

			// Update templates
			$tpl_files = glob($dir . '/*.tpl');
			foreach ($tpl_files as $tpl_file) {
				$tpl_content = file_get_contents($tpl_file);
				$pattern = '/\/modules\/' . preg_quote($module_name, '/') . '\/' . preg_quote($base_name, '/') . '(\.[a-f0-9]+)?(\.min)?\.css(\?[^"\'\s>]*)?/';
				$replacement = '/modules/' . $module_name . '/' . $min_filename;

				if (preg_match($pattern, $tpl_content)) {
					$new_tpl_content = preg_replace($pattern, $replacement, $tpl_content);
					file_put_contents($tpl_file, $new_tpl_content);
					echo "Updated " . basename($tpl_file) . " to reference $min_filename\n";
				}
			}
		}
	}
}

echo "Game Asset Minification Complete!\n";
