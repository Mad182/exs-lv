<?php

if (!$auth->ok) {
	$tpl->newBlock('error-nologin');
} else {

	$add_css[] = 'pm.css';

	set_action('vēstules');

	if (isset($_GET['history']) && isset($_GET['msg_id'])) {

		$read = (int) $_GET['msg_id'];
		$tpl = new TemplatePower(CORE_PATH . '/modules/pm/history.tpl');
		$tpl->prepare();

		$pm = $db->get_row("SELECT * FROM `pm` WHERE `id` = " . $read . " AND (`from_uid` = " . $auth->id . " OR `to_uid` = " . $auth->id . ")");
		if ($pm) {
			$to = $pm->to_uid;
			$from = $pm->from_uid;
			if (empty($pm->imap_email)) {
				$hmsgs = $db->get_results("SELECT * FROM pm WHERE (`from_uid` = $pm->to_uid AND `to_uid` = $pm->from_uid) OR (from_uid = '$pm->from_uid' AND to_uid = '$pm->to_uid') ORDER BY `date` DESC LIMIT 40");
				foreach ($hmsgs as $hmsg) {
					$fromuser = get_user($hmsg->from_uid);
					$tpl->newBlock('pm-h-node');
					$tpl->assign(array(
						'title' => $hmsg->title,
						'text' => add_smile($hmsg->text),
						'date' => $hmsg->date,
						'from_nick' => $fromuser->nick
					));
				}
			} else {
				$hmsgs = $db->get_results("SELECT * FROM pm WHERE ((`from_uid` = $pm->to_uid AND `to_uid` = $pm->from_uid) OR (from_uid = '$pm->from_uid' AND to_uid = '$pm->to_uid')) AND imap_email = '$pm->imap_email' ORDER BY `date` DESC LIMIT 40");
				foreach ($hmsgs as $hmsg) {
					$tpl->newBlock('pm-h-node');
					if (!empty($hmsg->imap_name)) {
						$from = $hmsg->imap_name . ' (' . $hmsg->imap_email . ')';
					} else {
						$from = $hmsg->imap_email;
					}
					$tpl->assign(array(
						'title' => $hmsg->title,
						'text' => add_smile($hmsg->text),
						'date' => $hmsg->date,
						'from_nick' => $from
					));
				}
			}
		}

		$tpl->printToScreen();
		exit;
	} else {

		//convert links to new ones, will rewrite later
		if (isset($_GET['var1']) && $_GET['var1'] == 'write') {
			$_GET['act'] = 'compose';
		}
		if (isset($_GET['var1']) && $_GET['var1'] == 'sent') {
			$_GET['act'] = 'outbox';
		}

		$end = 50;

		$inprofile = get_user($auth->id);

		if (isset($_POST['compose-to']) && $_POST['compose-to'] != 0) {

			if (!isset($_SESSION['antiflood']) or $_SESSION['antiflood'] < time() - 3) {
				$_SESSION['antiflood'] = time();
				$send_to = (int) $_POST['compose-to'];

				if ($auth->id == $send_to) {
					set_flash('Kāda jēga rakstīt sev? :/', 'error');
					redirect();
				}

				if (!isset($_POST['die-motherfcker-wannabes']) or $_POST['die-motherfcker-wannabes'] != md5($category->title . $remote_salt . $auth->id)) {
					set_flash('Kļūdains pieprasījums! Hacking around?', 'error');
					redirect();
				}

				$send_title = $_POST['compose-title'];
				$send_body = htmlpost2db($_POST['compose-body']);

				if (get_user($send_to) && !empty($send_body)) {
					$send_title = sanitize(trim(stripslashes(h(strip_tags($send_title)))));
					if (!$send_title) {
						$send_title = '[bez nosaukuma]';
					}
					$send_title = str_replace('Re:Re:', 'Re:', $send_title);

					$date = date('Y-m-d H:i:s');
					$receiver = get_user($send_to, true);

					if ($receiver) {

						$device_type = ($auth->via_android) ? 2 : 0;

						//write into database
						$db->query("INSERT INTO pm (id,from_uid,to_uid,date,ip,title,text,device) VALUES (NULL,'$auth->id','$receiver->id','$date','$auth->ip','$send_title','$send_body','$device_type')");
						$msgid = $db->insert_id;

						notify($receiver->id, 9);
						update_karma($auth->id);

						//suta meilu, atbilstoši notifikāciju iestatījumiem
						if ($receiver->pm_notify_email == 2 || ($receiver->pm_notify_email == 1 && strtotime($receiver->lastseen) < time() - 259200)) {

							//send email
							$subject = 'Tev pienākusi vēstule portālā ' . $_SERVER['HTTP_HOST'];
							$message = '
									<h3>Saņemta vēstule portālā ' . $_SERVER['HTTP_HOST'] . '</h3>
									<p>
										Čau! Tev pienākusi jauna ziņa no ' . h($auth->nick) . ' - &quot;' . stripslashes($send_title) . '&quot;
									</p>
									<p>
										To vari izlasīt šeit: <a href="https://exs.lv/pm/?act=inbox&read=' . $msgid . '">https://exs.lv/pm/?act=inbox&read=' . $msgid . '</a>
									</p>';

							send_email($receiver->mail, $subject, $message);
						}
					}

					redirect('/pm/sent');
				}
			} else {
				set_flash('Izskatās pēc flooda. Pagaidi 10 sekundes, pirms sūti jaunu vēstuli!', 'error');
			}
		}

		if (isset($_GET['act']) && $_GET['act'] == 'compose') {
			$tpl->newBlock('tinymce-enabled');
			$tpl->newBlock('pm-menu');
			$tpl->assign('compose-active', 'active');
			$page_title = 'Rakstīt vēstuli';
			$pm_title = 'Rakstīt vēstuli';
			$tpl->newBlock('pm-compose');
			$tpl->assign('pm-check', md5($category->title . $remote_salt . $auth->id));

			//contact list
			$friends = $db->get_results("SELECT friend1,friend2 FROM friends WHERE (friend1 = ('" . $auth->id . "') OR friend2 = ('" . $auth->id . "')) AND confirmed = '1' ORDER BY `date_confirmed` DESC");

			$doneselect = false;
			foreach ($friends as $friend) {
				if ($friend->friend1 == $auth->id) {
					$theother = $friend->friend2;
				} else {
					$theother = $friend->friend1;
				}
				$friendinfo = get_user($theother);

				//selected
				if (isset($_GET['to']) && $_GET['to'] == $theother) {
					$selected_to = ' selected="selected"';
					$doneselect = true;
				} else {
					$selected_to = '';
				}

				$tpl->newBlock('pm-compose-option');
				$tpl->assign(array(
					'friend-id' => $theother,
					'friend-nick' => h($friendinfo->nick),
					'friend-sel' => $selected_to
				));
			}

			if (isset($_GET['to']) && !$doneselect && $_GET['to'] != $auth->id) {
				$toid = (int) $_GET['to'];
				$tonick = $db->get_var("SELECT nick FROM users WHERE id = ('" . $toid . "')");
				if ($tonick) {
					$tpl->newBlock('pm-compose-option');
					$tpl->assign(array(
						'friend-id' => $toid,
						'friend-nick' => h($tonick),
						'friend-sel' => ' selected="selected"'
					));
				}
			} elseif (isset($_GET['replyto']) && !$doneselect) {
				$reply_to = (int) $_GET['replyto'];
				$reply_content = $db->get_row("SELECT * FROM `pm` WHERE `id` = " . $reply_to . " AND `to_uid` = " . $auth->id);
				$reply_user = get_user($reply_content->from_uid);
				if ($reply_content && $reply_user) {
					$tpl->newBlock('pm-compose-option');
					$tpl->assign(array(
						'friend-id' => $reply_user->id,
						'friend-nick' => h($reply_user->nick),
						'friend-sel' => ' selected="selected"'
					));

					$tpl->gotoBlock('pm-compose');
					$tpl->assign(array(
						'compose-title' => 'Re:' . str_replace('Re:', '', $reply_content->title)
					));
				}
			}
		} elseif (isset($_GET['act']) && $_GET['act'] == 'outbox') {
			$tpl->newBlock('pm-menu');
			$tpl->assign('outbox-active', 'active');
			$page_title = 'Sūtītās vēstules';
			$pm_title = 'Sūtītās vēstules';

			if (!empty($_GET['var2']) && is_numeric($_GET['var2'])) {
				$_GET['read'] = $_GET['var2'];
			}

			if (isset($_GET['read'])) {
				$read = (int) $_GET['read'];
				$pm = $db->get_row("SELECT * FROM pm WHERE id = ('" . $read . "') AND from_uid = ('" . $auth->id . "')");
				if ($pm) {
					$to = get_user($pm->to_uid);

					$tpl->newBlock('pm-read-outbox');
					$tpl->assign(array(
						'pm-title' => $pm->title,
						'pm-text' => add_smile($pm->text),
						'pm-id' => $pm->id,
						'pm-date' => substr($pm->date, 0, 16),
						'pm-to-nick' => usercolor($to->nick, $to->level, false, $to->id),
						'pm-to-id' => $pm->to_uid,
						'avatar' => get_avatar($to),
						'pm-to-title' => h($to->nick),
						'pm-read' => $pm->is_read,
					));

					$page_title = $pm->title . ' - lasīt vēstuli';
				} else {
					$tpl->newBlock('pm-read-error');
				}
			} else {
				$tpl->newBlock('pm-list-outbox');

				if (isset($_GET['skip'])) {
					$skip = (int) $_GET['skip'];
				} else {
					$skip = 0;
				}

				$pms = $db->get_results("SELECT
					`pm`.*,
					`users`.`nick`,
					`users`.`level`,
					`users`.`deleted` AS `user_deleted`
				FROM
					`pm`,
					`users`
				WHERE
					`pm`.`from_uid` = '" . $auth->id . "' AND
					`users`.`id` = `pm`.`to_uid`
				ORDER BY
					`pm`.`date` DESC
				LIMIT
					$skip,50");

				if ($pms) {
					foreach ($pms as $pm) {
						$tpl->newBlock('pm-list-outbox-node');
						$from = '<a href="/user/' . $pm->to_uid . '">' . usercolor($pm->nick, $pm->level, false, $pm->to_uid) . '</a>';
						$type = 'pm';
						if ($pm->is_read) {
							$type = 'pm-read';
						}
						if (!empty($pm->imap_uid)) {
							if (!stristr($pm->imap_name, '?')) {
								$from = wordwrap(textlimit(h($pm->imap_name), 48, '...'), 20, "\n", 1);
							} else {
								$from = wordwrap(textlimit(h($pm->imap_email), 48, '...'), 20, "\n", 1);
							}
							$type = 'email';
						}

						if (!empty($pm->user_deleted)) {
							$from = '<em>dzēsts</em>';
						}

						$tpl->assign(array(
							'pm-title' => strip_tags($pm->title),
							'pm-id' => $pm->id,
							'pm-date' => display_time(strtotime($pm->date)),
							'to' => $from,
							'pm-read' => $pm->is_read,
							'type' => $type
						));
					}

					$total = $db->get_var("SELECT count(*) FROM pm WHERE from_uid = ('" . $auth->id . "')");
					$pager = pager($total, $skip, $end, '/pm/sent/?skip=', true);
					$tpl->assignGlobal(array(
						'pager-next' => $pager['next'],
						'pager-prev' => $pager['prev'],
						'pager-numeric' => $pager['pages']
					));
				} else {
					$tpl->newBlock('pm-list-outbox-empty');
				}
			}
		} elseif (isset($_GET['var1']) && $_GET['var1'] == 'search') {
			$tpl->newBlock('pm-menu');
			$tpl->assign('search-active', 'active');
			$page_title = 'Meklēt vēstuli';
			$pm_title = 'Meklēt vēstuli';
			$tpl->newBlock('pm-search');

			if (isset($_GET['q'])) {
				$q_string = str_replace(array(',', '.', '+', '-', '_'), ' ', $_GET['q']);
				$q_string = strip_tags($q_string);
				$tpl->assign('qstr', h($q_string));
				$q_strings = explode(' ', $q_string);
				$cond = '';
				foreach ($q_strings as $str) {
					$cond .= " AND (`text` LIKE '%" . sanitize($str) . "%' OR `title` LIKE '%" . sanitize($str) . "%')";
				}

				$results = $db->get_results("SELECT * FROM `pm` WHERE (`to_uid` = $auth->id OR `from_uid` = $auth->id) $cond ORDER BY id DESC LIMIT 50");
				if ($results) {
					$tpl->newBlock('res-search');
					foreach ($results as $result) {
						$tpl->newBlock('res-search-node');
						$result->text = textlimit($result->text, 250);
						$result->title = textlimit($result->title, 64);
						foreach ($q_strings as $str) {
							$result->text = str_replace($str, '<strong>' . h($str) . '</strong>', $result->text);
						}
						foreach ($q_strings as $str) {
							$result->title = str_replace($str, '<strong>' . h($str) . '</strong>', $result->title);
						}
						if ($result->to_uid == $auth->id) {
							$link = '/' . $category->textid . '/inbox/' . $result->id;
						} else {
							$link = '/' . $category->textid . '/sent/' . $result->id;
						}
						$tpl->assign(array(
							'text' => $result->text,
							'title' => $result->title,
							'link' => $link,
						));
					}
				}
			}
		} else {
			$tpl->newBlock('pm-menu');
			$tpl->assign('inbox-active', 'active');
			$page_title = 'Saņemtās vēstules';
			$pm_title = 'Saņemtās vēstules';

			if (!empty($_GET['var2']) && is_numeric($_GET['var2'])) {
				$_GET['read'] = $_GET['var2'];
			}

			if (isset($_GET['read'])) {
				$read = (int) $_GET['read'];
				$pm = $db->get_row("SELECT * FROM `pm` WHERE `id` = '" . $read . "' AND `to_uid` = '" . $auth->id . "'");
				if ($pm) {
					//remove unread status
					if ($pm->is_read == 0) {
						$db->query("UPDATE `pm` SET `is_read` = '1' WHERE `id` = '" . $read . "'");
						if ($new_messages = $db->get_var("SELECT count(*) FROM `pm` WHERE `to_uid` = " . $auth->id . " AND `is_read` = 0")) {
							$new_msg_string = '&nbsp;(<span class="red" style="display:inline">' . $new_messages . '</span>)';
						} else {
							$new_msg_string = '';
						}
					}
					$from = get_user($pm->from_uid);

					$tpl->newBlock('pm-read-inbox');
					$tpl->assign(array(
						'pm-title' => $pm->title,
						'pm-text' => add_smile($pm->text),
						'pm-id' => $pm->id,
						'pm-date' => substr($pm->date, 0, 16),
						'pm-from-nick' => usercolor($from->nick, $from->level, false, $from->id),
						'pm-from-id' => $pm->from_uid,
						'avatar' => get_avatar($from),
						'pm-from-title' => h($from->nick),
						'pm-read' => $pm->is_read,
					));

					if (empty($pm->imap_uid)) {
						$tpl->newBlock('pm-read-from');

						if (!empty($from->deleted)) {
							$nick = '<em>dzēsts</em>';
						} else {
							$nick = usercolor($from->nick, $from->level, false, $from->id);
						}

						$tpl->assign(array(
							'pm-title' => $pm->title,
							'pm-text' => add_smile($pm->text),
							'pm-id' => $pm->id,
							'pm-date' => substr($pm->date, 0, 16),
							'pm-from-nick' => $nick,
							'pm-from-id' => $pm->from_uid,
							'avatar' => get_avatar($from),
							'pm-from-title' => h($from->nick),
							'pm-read' => $pm->is_read,
						));
					}
					$page_title = $pm->title . ' - lasīt vēstuli';
				} else {
					$tpl->newBlock('pm-read-error');
				}
			} else {
				$tpl->newBlock('pm-list-inbox');

				if (isset($_GET['skip'])) {
					$skip = (int) $_GET['skip'];
				} else {
					$skip = 0;
				}

				$pms = $db->get_results("SELECT
						`pm`.*,
						`users`.`nick`,
						`users`.`level`,
						`users`.`deleted` AS `user_deleted`
					FROM
						`pm`,
						`users`
					WHERE
						`pm`.`to_uid` = '" . $auth->id . "' AND
						`users`.`id` = `pm`.`from_uid`
					ORDER BY
						`pm`.`date` DESC
					LIMIT
						$skip,50");

				if ($pms) {
					foreach ($pms as $pm) {
						$tpl->newBlock('pm-list-inbox-node');
						$from = '<a href="/user/' . $pm->from_uid . '">' . usercolor($pm->nick, $pm->level, false, $pm->from_uid) . '</a>';
						$type = 'pm';
						if ($pm->is_read) {
							$type = 'pm-read';
						}
						if (!empty($pm->imap_uid)) {
							if (!stristr($pm->imap_name, '?')) {
								$from = wordwrap(textlimit(h($pm->imap_name), 48, '...'), 20, "\n", 1);
							} else {
								$from = wordwrap(textlimit(h($pm->imap_email), 48, '...'), 20, "\n", 1);
							}
							$type = 'email';
						}

						if (!empty($pm->user_deleted)) {
							$from = '<em>dzēsts</em>';
						}

						$tpl->assign(array(
							'pm-title' => wordwrap(textlimit(strip_tags($pm->title), 48, '...'), 20, "\n", 1),
							'pm-id' => $pm->id,
							'pm-date' => display_time(strtotime($pm->date)),
							'from' => $from,
							'pm-read' => $pm->is_read,
							'type' => $type
						));
					}

					$total = $db->get_var("SELECT count(*) FROM `pm` WHERE `to_uid` = '" . $auth->id . "'");
					$pager = pager($total, $skip, $end, '/pm/?skip=', true);
					$tpl->assignGlobal(array(
						'pager-next' => $pager['next'],
						'pager-prev' => $pager['prev'],
						'pager-numeric' => $pager['pages']
					));
				} else {
					$tpl->newBlock('pm-list-inbox-empty');
				}
			}
		}
	}
}

if (!empty($pm_title)) {
	$tpl->assignGlobal(array(
		'pm-top-title' => $pm_title,
	));
}

unset($pagepath);

