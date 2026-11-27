<?php

/**
 * Autoload / alias Verot\Upload\Upload as Upload for compatibility with Verot 2.x
 */
if (class_exists("Verot\\Upload\\Upload", false) && !class_exists("Upload", false)) {
	class_alias(\Verot\Upload\Upload::class, "Upload");
}

spl_autoload_register(function ($class) {
	if (strcasecmp($class, "Upload") === 0) {
		if (!class_exists("Verot\\Upload\\Upload", false) && defined("LIB_PATH")) {
			$upload_file = LIB_PATH . "/verot/src/class.upload.php";
			if (file_exists($upload_file)) {
				require_once $upload_file;
			}
		}
		if (class_exists("Verot\\Upload\\Upload", false) && !class_exists("Upload", false)) {
			class_alias(\Verot\Upload\Upload::class, "Upload");
		}
	}
});
