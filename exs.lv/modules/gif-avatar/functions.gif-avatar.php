<?php

function ezgif_filename($file) {
	$file = preg_replace("/[^a-zA-Z0-9._-]/i", '', $file);
	$file = htmlspecialchars($file);
	return $file;
}
