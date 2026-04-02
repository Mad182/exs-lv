<?php

/**
 * Callback klase regexpam, kas atpazīst @mention tekstos
 */
class Mention {

	private $url = null;
	private $type = null;
	private $uniq = null;
	private $newid = null;

	function __construct($url, $type, $uniq, $newid = 0) {
		$this->url = $url;
		$this->type = $type;
		$this->uniq = $uniq;
		$this->newid = $newid;
	}

	public function mention($matches) {
		global $db, $auth, $mention_counter;

		$nick = $matches[1];

		$usr = $db->get_row("SELECT * FROM `users` WHERE `nick` = '" . sanitize($nick) . "'");

		if (empty($usr) && stristr($nick, '_')) {
			$nick = str_replace('_', ' ', $nick);
			$usr = $db->get_row("SELECT * FROM `users` WHERE `nick` = '" . sanitize($nick) . "'");
		}

		if (empty($usr) && stristr($nick, '-')) {
			$nick = str_replace('-', ' ', $nick);
			$usr = $db->get_row("SELECT * FROM `users` WHERE `nick` = '" . sanitize($nick) . "'");
		}

		if($usr->id == 43040) {
			//die('test');
			if ($this->type == 'mb' || $this->type == 'group') {

				if(!empty($this->newid)) {
					$mb = $db->get_row("SELECT `text`, `id`, `author`,`parent`,`groupid`,`reply_to` FROM `miniblog` WHERE `id` = '" . intval($this->newid) . "'");
				} else {
					$mb = $db->get_row("SELECT `text`, `id`, `author`,`parent`,`groupid`,`reply_to` FROM `miniblog` WHERE `id` = '" . intval($this->uniq) . "'");
				}

				$content = get_ai_thread_context($mb->id);

				$messages = [
					["role" => "system", "content" => "Tu esi Exsperts - foruma un sociālā tīkla exs.lv izpalīdzīgais bots. Tev draudzīgi jāatbild uz lietotāju uzdotajiem jautājumiem un komentāriem, kur esi pieminēts. Atbildēm jābūt īsām, līdz 200 vārdiem. Atbildi noformē izmantojot html (<p>, <b>, <br> un līdzīgus tagus, bez dokumenta struktūras). Tevi izsauca lietotājs ".$auth->nick.", pieminot tavu vārdu @exsperts."],
					["role" => "user", "content" => $content],
				];

				$response = askAI($messages);

				$aiReply = $response['choices'][0]['message']['content'] ?? null;

				if ($aiReply === null) {
					// handle error or fallback
				} else {

					if(!empty($mb->parent)) {
						$parent = $mb->parent;
						$reply_to = $mb->id;
					} else {
						$parent = $mb->id;
						$reply_to = 0;
					}

					post_mb_ai(htmlpost2db($aiReply), $parent, $reply_to, $mb->groupid);
				}
			}
		}

		if (!empty($usr) && !in_array($nick, ['exs', 'inbox', 'gmail', 'mail', 'twitter', 'hotmail']) && $mention_counter <= 6) {
			$mention_counter++;

			if ($this->type == 'mb') {
				if (!empty($this->uniq)) {
					$mb = $db->get_row("SELECT `text`, `id`, `author` FROM `miniblog` WHERE `id` = '" . intval($this->uniq) . "'");
					$title = mb_get_title($mb->text);
					$strid = mb_get_strid($title, $mb->id);
					$this->url = '/say/' . $mb->author . '/' . $mb->id . '-' . $strid;
					if ($mb->author != $usr->id && $usr->id != $auth->id) {
						notify($usr->id, 14, $mb->id, $this->url, $title);
					}
				}
			}

			if ($this->type == 'group') {
				if (!empty($this->uniq)) {
					$mb = $db->get_row("SELECT `id`, `groupid`, `text`, `author` FROM `miniblog` WHERE `id` = '" . intval($this->uniq) . "'");
					$group = $db->get_row("SELECT `title`, `strid`, `id` FROM `clans` WHERE `id` = '$mb->groupid'");
					$title = mb_get_title($mb->text);
					if (!empty($group->strid)) {
						$this->url = '/' . $group->strid . '/forum/' . base_convert($mb->id, 10, 36);
					} else {
						$this->url = '/group/' . $group->id . '/forum/' . base_convert($mb->id, 10, 36);
					}
					if ($mb->author != $usr->id && $usr->id != $auth->id) {
						notify($usr->id, 13, $mb->id, $this->url, $group->title . ': ' . $title);
					}
				}
			}

			if ($this->type == 'page') {
				if (!empty($this->uniq)) {
					$page = $db->get_row("SELECT `id`, `title`, `author` FROM `pages` WHERE `id` = '" . intval($this->uniq) . "'");
					if ($page->author != $usr->id && $usr->id != $auth->id) {
						notify($usr->id, 15, $page->id, $this->url, $page->title);
					}
				}
			}

			if ($this->type == 'image') {
				if (!empty($this->uniq)) {
					$image = $db->get_row("SELECT `id`, `uid`, `text` FROM `images` WHERE `id` = '" . intval($this->uniq) . "'");
					$this->url = '/gallery/' . $image->uid . '/' . $image->id;
					if ($usr->id != $auth->id) {
						notify($usr->id, 16, $image->id, $this->url, strip_tags($image->text));
					}
				}
			}

			if ($this->type == 'junk') {
				if (!empty($this->uniq)) {
					$junk = $db->get_row("SELECT `id`, `title` FROM `junk` WHERE `id` = '" . intval($this->uniq) . "'");
					$this->url = '/junk/' . $junk->id;
					if ($usr->id != $auth->id) {
						notify($usr->id, 15, $junk->id, $this->url, strip_tags($junk->title));
					}
				}
			}

			return '<a class="post-mention" href="/user/' . $usr->id . '"><span class="at-sign">@</span>' . usercolor($usr->nick, $usr->level, 'disable', $usr->id) . '</a>';
		} else {
			return '@' . $nick;
		}
	}

}
