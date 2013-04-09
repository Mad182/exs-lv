<?php

class Friend {

	var $user_id;
	var $friend1;
	var $friend2;
	var $myself;
	var $friend;
	var $userinfo;
	var $date;
	var $done = false;

	function Friend() {
		$this->done = false;
	}

	//get friendship id
	function get_friendship_id($myself = 0, $friend = 0) {
		global $db;
		$friendship_id = $db->get_var("SELECT id FROM friends WHERE ((friend1 = ('" . $myself . "') AND friend2 = ('" . $friend . "')) OR (friend2 = ('" . $myself . "') AND friend1 = ('" . $friend . "'))) AND confirmed = '1'");
		if ($friendship_id) {
			return $friendship_id;
		} else {
			return false;
		}
	}

	//get friendship id
	function pending_friendship($myself = 0, $friend = 0) {
		global $db;
		$friendship_id = $db->get_var("SELECT id FROM friends WHERE ((friend1 = ('" . $myself . "') AND friend2 = ('" . $friend . "')) OR (friend2 = ('" . $myself . "') AND friend1 = ('" . $friend . "'))) AND confirmed = '0'");
		if ($friendship_id) {
			return $friendship_id;
		} else {
			return false;
		}
	}

	//check if user exists
	function check_user($user_id) {
		if (get_user($user_id)) {
			return true;
		} else {
			return false;
		}
	}

	//delete friend by friednship id
	function delete_friend($id) {
		global $db, $auth;
		$id = (int) $id;
		if ($auth->ok && $id) {
			if ($db->query("DELETE FROM friends WHERE id = '$id' AND (friend1 = '$auth->id' OR friend2 = '$auth->id') LIMIT 1")) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	//adds friend
	function add_friend($myself, $friend) {
		global $db;
		if ($this->check_user($friend) && !$this->get_friendship_id($myself, $friend) && $myself && $friend && $friend != $myself) {
			if (!$this->pending_friendship($myself, $friend)) {
				$db->query("INSERT INTO friends (id,friend1,friend2,date,confirmed) VALUES (NULL,'$myself','$friend',NOW(),'0')");
				notify($friend, 5, 0);
			}
		}
	}

	//confirm friendship
	function confirm_friendship($myself, $friend) {
		global $db;
		if ($this->check_user($friend) && !$this->get_friendship_id($myself, $friend) && $myself && $friend && $friend != $myself) {
			if ($this->pending_friendship($myself, $friend)) {
				$date = date('Y-m-d H:i:s');
				$db->query("UPDATE friends SET date_confirmed = ('$date'), confirmed = ('1') WHERE ((friend1 = ('" . $myself . "') AND friend2 = ('" . $friend . "')) OR (friend2 = ('" . $myself . "') AND friend1 = ('" . $friend . "'))) AND confirmed = '0'");
				notify($friend, 6);
			}
		}
	}

}
