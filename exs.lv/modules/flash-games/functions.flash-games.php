<?php

/**
 * Iztukšo cache folderi
 * 
 * @param string $dir
 */
function destroy_cdir($dir = 'cache/index/') {
	$mydir = opendir($dir);
	while (false !== ($file = readdir($mydir))) {
		if ($file != "." && $file != "..") {
			chmod($dir . $file, 0777);
			if (is_dir($dir . $file)) {
				chdir('.');
				destroy($dir . $file . '/');
				rmdir($dir . $file) or DIE("couldn't delete $dir$file<br />");
			} else
				unlink($dir . $file) or DIE("couldn't delete $dir$file<br />");
		}
	}
	closedir($mydir);
}
