<?php

/**
 * Primitīvs "drop in" aizvietotājs ezSQL
 * balstīts uz php iebūvēto mysqli klasi
 */
class mdb extends mysqli {

	var $num_queries = 0;

	function __construct($username, $password, $database, $hostname = 'localhost') {

		//atslēdzam error reporting, lai ignorētu
		//"client library minor version mismatch" paziņojumu
		//lietojot MariaDB 10
		$err_level = error_reporting(0);
		parent::__construct($hostname, $username, $password, $database);
		error_reporting($err_level);
		/**
		 * atkomentē šo rindu ja tiek darbināts uz servera
		 * kur mysql nav utf-8 savienojums pēc noklusējuma
		 */
		//$this->query("set names utf8");
	}

	function query($query = null) {
		$this->num_queries++;
		return parent::query($query);
	}

	function get_results($query = null) {
		if (!empty($query) && $data = $this->query($query)) {
			$return = array();
			while ($row = $data->fetch_object()) {
				$return[] = $row;
			}
			return $return;
		}
		return null;
	}

	function get_row($query = null) {
		if (!empty($query) && $data = $this->query($query)) {
			while ($row = $data->fetch_object()) {
				return $row;
			}
		}
		return null;
	}

	function get_var($query = null) {
		if (!empty($query) && $data = $this->query($query)) {
			while ($row = $data->fetch_row()) {
				return $row[0];
			}
		}
		return null;
	}

	function get_col($query = null) {
		if (!empty($query) && $data = $this->query($query)) {
			$return = array();
			while ($row = $data->fetch_array()) {
				$return[] = $row[0];
			}
			return $return;
		}
		return null;
	}

	function update($table = null, $params = null, $data = null) {

		if (empty($table) || empty($params) || empty($data))
			return false;

		$criteria = array();
		$updates = array();
		$limit = 'LIMIT 1';

		if (is_array($params)) {
			foreach ($params as $key => $value) {
				$criteria[] = "`" . $key . "` = '" . $value . "'";
			}
			$limit = '';
		} else {
			if ((int) $params < 1)
				return false;
			$criteria[] = "`id` = " . (int) $params;
		}

		foreach ($data as $key => $val) {
			if ($val != 'NOW()' && $val != 'NULL' && $val != 'null') {
				$val = "'" . $val . "'";
			}
			$updates[] = '`' . $key . '` = ' . $val;
		}
		if (empty($updates) || empty($criteria))
			return false;

		$updates = implode(', ', $updates);
		$criteria = implode(' AND ', $criteria);

		$this->query("UPDATE `$table` SET $updates WHERE $criteria $limit");
		return ($this->affected_rows > 0);
	}

	function insert($table = null, $data = null) {

		if (empty($data))
			return false;

		$keys = array();
		$values = array();

		foreach ($data as $key => $val) {
			$keys[] = $key;
			$values[] = "'" . $val . "'";
		}
		if (empty($keys) || empty($values))
			return false;

		$keys = implode(',', $keys);
		$values = implode(',', $values);

		$this->query("INSERT INTO `$table` ($keys) VALUES($values)");
		return ($this->affected_rows > 0);
	}

}

