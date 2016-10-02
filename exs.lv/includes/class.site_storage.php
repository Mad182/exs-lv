<?php

/**
 * Saglabā/nolasa vienkāršus key/value pārus datubāzē+memcache
 */
class SiteStorage {

	function get($key) {
		global $db, $m, $lang;

		if (($data = $m->get('ss_sid' . $lang . '_' . $key)) === false) {
			$data = $db->get_var("SELECT `value` FROM `site_storage` WHERE `key` = '" . sanitize($key) . "' AND `lang` = '$lang'");
			$m->set('ss_sid' . $lang . '_' . $key, $data, 10800);
		}
		return $data;
	}

	function set($key, $val = null) {
		global $db, $m, $lang;
		if ($db->get_var("SELECT count(*) FROM `site_storage` WHERE `key` = '" . sanitize($key) . "' AND `lang` = '$lang'")) {
			$db->query("UPDATE `site_storage` SET `value` = '" . sanitize($val) . "' WHERE `key` = '" . sanitize($key) . "' AND `lang` = '$lang'");
		} else {
			$db->query("INSERT INTO `site_storage` (`key`,`lang`,`value`) VALUES ('" . sanitize($key) . "', '$lang', '" . sanitize($val) . "')");
		}

		$m->delete('ss_sid' . $lang . '_' . $key);
	}

}
