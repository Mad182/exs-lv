<?php
/**
 * Minify Game JS files and update template inclusions with hashed filenames
 */

require_once __DIR__ . '/../includes/jsmin.php';

$game_modules = [
	'2048' => ['2048.js'],
	'augsup' => ['augsup.js'],
	'desas' => ['desas.js'],
	'flappy' => ['flappy.js'],
	'invaders' => ['invaders.js'],
	'memory' => ['memory.js'],
	'minu-mekletajs' => ['minu-mekletajs.js'],
	'rulete' => ['rulete.js'],
	'runner' => ['runner.js'],
	'snake' => ['jquery.snake.js', 'common.js'],
	'sudoku' => ['sudoku.js'],
	'tetris' => ['tetris.js'],
	'tic-tac-toe' => ['tictactoe.js'],
	'vardes' => ['vardes.js'],
	'wordle' => ['wordle.js'],
];

$modules_dir = realpath(__DIR__ . '/../modules');

foreach ($game_modules as $module_name => $js_files) {
	$dir = $modules_dir . '/' . $module_name;
	if (!is_dir($dir)) {
		echo "Module directory not found: $module_name\n";
		continue;
	}

	// Remove any existing *.min.js files in this module directory
	$old_min_files = glob($dir . '/*.min.js');
	if ($old_min_files) {
		foreach ($old_min_files as $f) {
			unlink($f);
			echo "Removed old minified file: " . basename($f) . "\n";
		}
	}

	foreach ($js_files as $js_filename) {
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

		// Minify
		try {
			$minified_code = JSMin::minify($source_code);
			file_put_contents($target_path, $minified_code);
			echo "Minified: $js_filename -> $min_filename (" . strlen($source_code) . "B -> " . strlen($minified_code) . "B)\n";
		} catch (Exception $e) {
			echo "Error minifying $js_filename: " . $e->getMessage() . "\n";
			continue;
		}

		// Update templates in the module directory
		$tpl_files = glob($dir . '/*.tpl');
		foreach ($tpl_files as $tpl_file) {
			$tpl_content = file_get_contents($tpl_file);

			// Match patterns like /modules/module_name/js_filename(?v=xxx) or old minified files
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

echo "Game JS Minification Complete!\n";
