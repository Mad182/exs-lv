<?php

function update_blog_stats() {
	global $db;

	$blogs = $db->get_results("SELECT * FROM `cat` WHERE isblog != '0'");
	foreach ($blogs as $blog) {
		update_stats($blog->id);
	}

}

