<?php

class Comment {

	function check_page($page_id, $place = 0) {
		global $db;
		if ($place == 0) {
			$table = 'pages';
		} else {
			$table = 'images';
		}
		$pageinfo = $db->get_row("SELECT id FROM " . $table . " WHERE id = '$page_id' AND closed = '0'");
		if ($pageinfo) {
			return true;
		} else {
			return false;
		}
	}

	function check_user($user_id) {
		if (get_user($user_id)) {
			return true;
		} else {
			return false;
		}
	}

	function check_isforum($catid) {
		$cat = get_cat($catid);
		if ($cat->isforum) {
			return true;
		} else {
			return false;
		}
	}

	function format_text($text) {
		return htmlpost2db($text);
	}

	function add_comment($page_id, $user_id, $text, $place = 0, $reply = 0) {
		global $db, $auth, $article;
		$page_id = (int) $page_id;
		$reply = (int) $reply;
		if ($this->check_page($page_id, $place) && $this->check_user($user_id)) {

			$text = htmlpost2db($text);

			if (!isset($_SESSION['antiflood']) or $_SESSION['antiflood'] < time() - 10) {
				$_SESSION["antiflood"] = time();

				if ($place == 0) {

					if(!isset($_POST['no-bump'])) {
						$db->query("UPDATE `pages` SET `bump` = NOW(), `posts` = posts+1, `readby` = '' WHERE `id` = '$page_id'");
					} else {
						$db->query("UPDATE `pages` SET `posts` = posts+1, `readby` = '' WHERE `id` = '$page_id'");
					}

					$db->query("INSERT INTO `comments` (id,pid,author,text,date,ip,parent) VALUES (NULL,'$page_id','$user_id','$text',NOW(),'$auth->ip','$reply')");
					$newid = $db->insert_id;
					if ($reply != 0) {
						$db->query("UPDATE comments SET replies = replies+1 WHERE id = '$reply'");
					}
					$url = '/read/' . $article->strid;

					$newpost = $db->get_row("SELECT * FROM `comments` WHERE `id` = '$newid'");
					$newpost->text = mention($newpost->text, $url, 'page', $page_id);
					$db->query("UPDATE `comments` SET `text` = '" . sanitize($newpost->text) . "' WHERE id = '$newpost->id'");

					if(!isset($_POST['no-bump'])) {

						push('Komentēja rakstu &quot;<a href="' . $url . '#c' . $newid . '">' . $article->title . '</a>&quot;', '/dati/bildes/topic-av/'.$article->id.'.jpg');
						notify($article->author, 2, $article->id, $url, textlimit(hide_spoilers($article->title), 64));
						build_latest();

					}
				} else {
					$db->query("INSERT INTO galcom (id,bid,author,text,date,ip) VALUES (NULL,'$page_id','$user_id','$text',NOW(),'$auth->ip')");
					$newid = $db->insert_id;
					$db->query("UPDATE `images` SET `bump` = NOW(), `posts` = `posts`+1, `readby` = '' WHERE `id` = '$page_id'");

					$newpost = $db->get_row("SELECT * FROM `galcom` WHERE `id` = '$newid'");
					$newpost->text = mention($newpost->text, '#', 'image', $page_id);
					$db->query("UPDATE `galcom` SET `text` = '" . sanitize($newpost->text) . "' WHERE id = '$newpost->id'");

				}
				update_karma($user_id);
			} else {
				set_flash('Izskatās pēc flooda. Pagaidi 10 sekundes, pirms pievieno jaunu komentāru!', 'error');
			}
		} else {
			set_flash('Neizdevās pievienot komentāru. Nēesi ielogojies? Slēgta lapa? Dzēsta lapa? Hacking around?', 'error');
		}
	}

}
