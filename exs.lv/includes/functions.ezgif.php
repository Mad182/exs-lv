<?php

function ezgif_menu($file) {
	return '
	<a class="small button primary" href="/crop/' . htmlspecialchars($file) . '">crop</a>
	<a class="small button primary" href="/resize/' . htmlspecialchars($file) . '">resize</a>
	<a class="small button primary" href="/optimize/' . htmlspecialchars($file) . '">optimize</a>
	<a class="small button primary" href="/split/' . htmlspecialchars($file) . '">split</a>
	<a class="small button danger" href="/save/' . htmlspecialchars($file) . '?_">save</a>
';
}

function ezgif_png_menu($file) {
	return '
	<a class="small button danger" href="/save/' . htmlspecialchars($file) . '?_">save</a>
';
}

function ezgif_filename($file) {
	$file = preg_replace("/[^a-zA-Z0-9._-]/i", '', $file);
	$file = htmlspecialchars($file);
	return $file;
}
