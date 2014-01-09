<?php

$find = '<a href="%5C">';
$replace = '<a href="/">';

$db->query("UPDATE `ajax_comments` SET `text` = REPLACE(`text`, '" . sanitize($find) . "', '" . sanitize($replace) . "')");
echo 'ajax_comments: ' . $db->rows_affected . '<br />';
$db->query("UPDATE `pages` SET `text` = REPLACE(`text`, '" . sanitize($find) . "', '" . sanitize($replace) . "')");
echo 'pages: ' . $db->rows_affected . '<br />';
$db->query("UPDATE `miniblog` SET `text` = REPLACE(`text`, '" . sanitize($find) . "', '" . sanitize($replace) . "')");
echo 'miniblog: ' . $db->rows_affected . '<br />';
$db->query("UPDATE `comments` SET `text` = REPLACE(`text`, '" . sanitize($find) . "', '" . sanitize($replace) . "')");
echo 'comments: ' . $db->rows_affected . '<br />';
$db->query("UPDATE `galcom` SET `text` = REPLACE(`text`, '" . sanitize($find) . "', '" . sanitize($replace) . "')");
echo 'galcom: ' . $db->rows_affected . '<br />';
$db->query("UPDATE `images` SET `text` = REPLACE(`text`, '" . sanitize($find) . "', '" . sanitize($replace) . "')");
echo 'images: ' . $db->rows_affected . '<br />';
?>