<?php

/**
 * Satura tagošana
 */
class tags {

	var $tag_id;
	var $page_id;

	function check_tag($tag_id) {
		global $db;
		return $db->get_var("SELECT count(*) FROM tags WHERE id = '$tag_id'");
	}

	function check_page($page_id, $type = 0) {
		global $db, $lang;
		if ($type == 2) {
			return $db->get_var("SELECT count(*) FROM miniblog WHERE id = '$page_id' AND `lang` = '$lang'");
		} elseif ($type == 3) {
			return $db->get_var("SELECT count(*) FROM clans WHERE id = '$page_id'");
		} elseif ($type == 1) {
			return $db->get_var("SELECT count(*) FROM images WHERE id = '$page_id'");
		} else {
			return $db->get_var("SELECT count(*) FROM pages WHERE id = '$page_id' AND `lang` = '$lang'");
		}
	}

	function check_tagged($page_id, $tag_id, $type = 0) {
		global $db, $lang;
		if ($db->get_var("SELECT count(*) FROM taged WHERE tag_id = '" . intval($tag_id) . "' AND page_id = '" . intval($page_id) . "' AND type = '" . intval($type) . "' AND `lang` = '$lang'")) {
			return true;
		} else {
			return false;
		}
	}

	function add_tag($page_id, $tag_id, $type = 0) {
		global $db, $lang;
		if ($this->check_tag($tag_id) && $this->check_page($page_id, $type) && !$this->check_tagged($page_id, $tag_id, $type)) {
			return $db->query("INSERT INTO taged (tag_id,page_id,type,lang) VALUES ('" . intval($tag_id) . "','" . intval($page_id) . "','" . intval($type) . "','$lang')");
		} else {
			return false;
		}
	}

	function remove_tag($taged_id) {
		global $db, $lang;
		if ($taged_id) {
			return $db->query("DELETE FROM `taged` WHERE `id` = '$taged_id' AND `lang` = '$lang' LIMIT 1");
		} else {
			return false;
		}
	}

}
