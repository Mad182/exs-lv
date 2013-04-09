<?php

$tpl->assignInclude('module-currrent', 'modules/core/group.tpl');
$tpl->assignInclude('conversation', 'modules/core/conversation.tpl');
$tpl->prepare();

if (isset($_GET['group'])) {

	set_action('grupas');

	$group_id = (int) $_GET['group'];

	$group = $db->get_row("SELECT * FROM clans WHERE id = '$group_id'");

	if ($group) {

		if ($auth->ok && $db->get_var("SELECT count(*) FROM clans_members WHERE clan = '$group->id' AND user = '$auth->id' AND approve = '1' AND moderator = '1'")) {
			$is_mod = true;
		} else {
			$is_mod = false;
		}

		$pagepath = '<a href="/grupas">Exs grupas</a> / ' . $group->title;

		$tpl->newBlock('group-menu');
		$tpl->assign(array(
			'group-id' => $group->id,
			'group-title' => $group->title
		));

		if (isset($_GET['act']) && $_GET['act'] == 'edit' && ($group->owner == $auth->id or $auth->id == 1 or $is_mod)) {


			if (isset($_POST['edit-group-title'])) {
				$edit_text = sanitize(trim($_POST['edit-group-text']));
				$edit_title = htmlspecialchars(sanitize(trim($_POST['edit-group-title'])));


				require_once('includes/class.upload.php');

				if (isset($_FILES['edit-avatar'])) {
					$text = 'group_' . time() . '_' . $group->id;
					$foo = new Upload($_FILES['edit-avatar']);
					$foo->file_new_name_body = $text;
					$foo->image_resize = true;
					$foo->image_convert = 'jpg';
					$foo->image_x = 90;
					$foo->image_y = 90;
					$foo->allowed = array('image/*');
					$foo->image_ratio_crop = true;
					$foo->jpeg_quality = 90;
					$foo->file_auto_rename = false;
					$foo->file_overwrite = true;
					$foo->process('dati/bildes/useravatar/');
					if ($foo->processed) {

						$foo = new Upload($_FILES['edit-avatar']);
						$foo->file_new_name_body = $text;
						$foo->image_resize = true;
						$foo->image_convert = 'jpg';
						$foo->image_x = 45;
						$foo->image_y = 45;
						$foo->allowed = array('image/*');
						$foo->image_ratio_crop = true;
						$foo->jpeg_quality = 90;
						$foo->file_auto_rename = false;
						$foo->file_overwrite = true;
						$foo->process('dati/bildes/u_small/');

						$foo = new Upload($_FILES['edit-avatar']);
						$foo->file_new_name_body = $text;
						$foo->image_resize = true;
						$foo->image_convert = 'jpg';
						$foo->image_x = 150;
						$foo->image_y = 150;
						$foo->allowed = array('image/*');
						$foo->image_ratio_crop = true;
						$foo->image_ratio_no_zoom_in = true;
						$foo->jpeg_quality = 90;
						$foo->file_auto_rename = false;
						$foo->file_overwrite = true;
						$foo->process('dati/bildes/u_large/');

						if (file_exists('dati/bildes/useravatar/' . $text . '.jpg')) {
							if ($group->avatar != $config['default_user_avatar']) {
								unlink('dati/bildes/useravatar/' . $group->avatar);
								@unlink('dati/bildes/u_large/' . $group->avatar);
								@unlink('dati/bildes/u_small/' . $group->avatar);
							}
							$group->avatar = $text . '.jpg';
						}
						$foo->clean();
					}
				}


				$db->query("UPDATE clans SET avatar = ('$group->avatar'), text = ('$edit_text'), title = ('$edit_title'), date_modified = '" . time() . "' WHERE id = '$group->id' LIMIT 1");
				$db->query("INSERT INTO adminlog (who,place,action,time) VALUES ('$auth->id','group$group->id','edit','" . date('Y-m-d H:i:s') . "')");
				header('Location: /?group=' . $group->id);
				exit;
			}

			$tpl->assign('active-tab-info', ' activeTab');
			$tpl->newBlock('group-edit');
			$tpl->assign(array(
				'group-text' => htmlspecialchars($group->text),
				'group-title' => $group->title,
			));

			$tpl->newBlock('tinymce-enabled');
			$page_title = $group->title . ' | labo grupas profilu';
		} elseif (isset($_GET['act']) && $_GET['act'] == 'members') {
			$tpl->assign('active-tab-members', ' activeTab');
			$tpl->newBlock('group-members');

			if ($group->owner == $auth->id or $is_mod or $auth->id == 1) {
				$pendings = $db->get_results("SELECT * FROM clans_members WHERE clan = '$group->id' AND approve = '0'");
				if ($pendings) {
					$tpl->newBlock('pending');
					foreach ($pendings as $pending) {
						$p_user = $db->get_row("SELECT id,nick,level,avatar FROM users WHERE id = '$pending->user'");
						if ($p_user->avatar == '') {
							$p_user->avatar = $config['default_user_avatar'];
						}
						if ($pending->user) {
							$tpl->newBlock('pending-node');
							$tpl->assign(array(
								'group-id' => $group->id,
								'pending-id' => $pending->id,
								'pending-uid' => $p_user->id,
								'pending-date' => date('Y-m-d', $pending->date_added),
								'pending-avatar' => $p_user->avatar,
								'pending-nick' => usercolor($p_user->nick, $p_user->level),
							));
						}
					}
				}
			}

			$tpl->newBlock('members');

			$m_owner = $db->get_row("SELECT id,nick,level,avatar FROM users WHERE id = ('" . $group->owner . "')");

			//default avatar image
			if ($m_owner->avatar == '') {
				$m_owner->avatar = $config['default_user_avatar'];
			}

			$tpl->newBlock('members-node');
			$tpl->assign(array(
				'group-id' => $group->id,
				'member-class' => 'owner',
				'member-id' => $m_owner->id,
				'member-nick' => usercolor($m_owner->nick, $m_owner->level),
				'member-avatar' => $m_owner->avatar,
			));


			$members = $db->get_results("SELECT * FROM clans_members WHERE clan = '$group->id' AND approve = '1' ORDER BY moderator DESC, date_added ASC");
			if ($members) {
				foreach ($members as $member) {
					$m_user = $db->get_row("SELECT id,nick,level,avatar FROM users WHERE id = ('" . $member->user . "')");

					//default avatar image
					if ($m_user->avatar == '') {
						$m_user->avatar = $config['default_user_avatar'];
					}

					if ($member->moderator) {
						$mclas = 'mod';
					} else {
						$mclas = 'member';
					}

					$tpl->newBlock('members-node');
					$tpl->assign(array(
						'group-id' => $group->id,
						'member-class' => $mclas,
						'member-id' => $m_user->id,
						'member-nick' => usercolor($m_user->nick, $m_user->level),
						'member-avatar' => $m_user->avatar,
					));
					//cancel friendship
					if ($auth->ok && $group->owner == $auth->id or $auth->id == 1 or $is_mod) {
						$tpl->newBlock('member-delete');
						$tpl->assign(array(
							'group-id' => $group->id,
							'member-id' => $m_user->id,
						));
					}
					//set moderator
					if ($auth->ok && $auth->id == 1) {
						if ($member->moderator) {
							$tpl->newBlock('member-unmoderator');
							$tpl->assign(array(
								'group-id' => $group->id,
								'member-id' => $m_user->id,
							));
						} else {
							$tpl->newBlock('member-moderator');
							$tpl->assign(array(
								'group-id' => $group->id,
								'member-id' => $m_user->id,
							));
						}
					}
				}
			}


			$tpl->newBlock('friends-css');
			$page_title = $group->title . ' | grupas biedri';
		} elseif (isset($_GET['act']) && $_GET['act'] == 'drop' && ($group->owner == $auth->id or $auth->id == 1 or $is_mod)) {
			$drop = (int) $_GET['drop'];
			$db->query("DELETE FROM clans_members WHERE clan = '$group->id' AND user = '$drop'");
			$db->query("UPDATE clans SET members = '" . $db->get_var("SELECT count(*) FROM clans_members WHERE clan = '$group->id' AND approve = '1'") . "' WHERE id = '$group->id'");
			header('Location: /group/' . $group->id . '/members');
			exit;
		} elseif (isset($_GET['act']) && $_GET['act'] == 'setmod' && $auth->id == 1) {
			$uid = (int) $_GET['uid'];
			$db->query("UPDATE clans_members SET moderator = ('1') WHERE clan = '$group->id' AND user = '$uid'");
			header('Location: /group/' . $group->id . '/members');
			exit;
		} elseif (isset($_GET['act']) && $_GET['act'] == 'unsetmod' && $auth->id == 1) {
			$uid = (int) $_GET['uid'];
			$db->query("UPDATE clans_members SET moderator = ('0') WHERE clan = '$group->id' AND user = '$uid'");
			header('Location: /group/' . $group->id . '/members');
			exit;
		} elseif (isset($_GET['act']) && $_GET['act'] == 'confirm' && ($group->owner == $auth->id or $auth->id == 1 or $is_mod)) {
			$confirm = (int) $_GET['confirm'];
			$db->query("UPDATE clans_members SET approve = ('1') WHERE clan = '$group->id' AND id = '$confirm'");
			$db->query("UPDATE clans SET members = '" . $db->get_var("SELECT count(*) FROM clans_members WHERE clan = '$group->id' AND approve = '1'") . "' WHERE id = '$group->id'");
			header('Location: /group/' . $group->id . '/members');
			exit;
		} elseif (isset($_GET['act']) && $_GET['act'] == 'apply') {
			if (!$db->get_var("SELECT count(*) FROM clans_members WHERE clan = '$group->id' AND user = '$auth->id'") && $auth->id != $group->owner) {
				$db->query("INSERT INTO clans_members (user,clan,approve,date_added) VALUES ('$auth->id','$group->id','0','" . time() . "')");
				header('Location: /group/' . $group->id);
				exit;
			}
		} elseif (isset($_GET['act']) && $_GET['act'] == 'cancel') {
			$db->query("DELETE FROM clans_members WHERE clan = '$group->id' AND user = '$auth->id'");
			$db->query("UPDATE clans SET members = '" . $db->get_var("SELECT count(*) FROM clans_members WHERE clan = '$group->id' AND approve = '1'") . "' WHERE id = '$group->id'");
			header('Location: /group/' . $group->id);
			exit;
		} elseif (isset($_GET['act']) && $_GET['act'] == 'community' && !empty($group->id) || isset($_GET['act']) && $_GET['act'] == 'forum' && !empty($group->id)) {
			$tpl->assign('active-tab-community', ' activeTab');
			$tpl->newBlock('group-community');
			$page_title = $group->title . ' | grupas forums';

			if (isset($_GET['param']) && !empty($_GET['param'])) {
				$_GET['single'] = base_convert($_GET['param'], 36, 10);
			}

			if ($auth->ok && ($is_mod or $auth->id == $group->owner or $auth->id == 1 or $auth->level == '5' or $db->get_var("SELECT count(*) FROM clans_members WHERE clan = '$group->id' AND user = '$auth->id' AND approve = '1'"))) {

				if (isset($_GET['skip'])) {
					$skip = (int) $_GET['skip'];
				} else {
					$skip = 0;
				}
				$end = 6;

				$u_small_path1 = 'u_small';

				if ($auth->ok && isset($_POST['newminiblog']) && !$group->archived) {

					$body = post2db($_POST['newminiblog']);

					if (!isset($_SESSION['antiflood']) or $_SESSION['antiflood'] < time() - 10) {
						$_SESSION["antiflood"] = time();

						$ins = post_mb(array(
							'groupid' => $group->id,
							'text' => $body
								));

						push('Izveidoja tematu grupā <a href="/group/' . $group->id . '/forum/' . base_convert($ins, 10, 36) . '">' . $group->title . '</a>', '/dati/bildes/u_small/' . $group->avatar, 'g' . $ins);
						$db->query("UPDATE clans SET posts = '" . $db->get_var("SELECT count(*) FROM miniblog WHERE groupid = '$group->id'") . "' WHERE id = '$group->id'");

						$topic = $db->get_row("SELECT * FROM `miniblog` WHERE `id` = '$ins'");
						$topic->text = mention($topic->text, "/group/' . $group->id . '/forum/' . base_convert($ins, 10, 36) . '", 'group', $topic->id);
						$db->query("UPDATE `miniblog` SET `text` = '" . sanitize($topic->text) . "' WHERE id = '$topic->id'");

						header('Location: /group/' . $group->id . '/forum/' . base_convert($ins, 10, 36));

						exit;
					} else {
						die('<small>Flood detected! 10 second timeout.</small>');
					}
				}

				if ($auth->ok && isset($_POST['responseminiblog']) && !empty($_POST['responseminiblog']) && !$group->archived) {


					$to = (int) $_POST['response-to'];

					if (get_mb_level($to) > 3) {
						die('Too deep ;(');
					}

					if (!isset($_POST['token']) or $_POST['token'] != md5('mb' . intval($_GET['single']) . $remote_salt . $auth->nick)) {
						header('Location: /');
						exit;
					}

					$reply_to = $db->get_row("SELECT * FROM miniblog WHERE id = '$to'");

					$reply_to_id = 0;
					if ($reply_to->parent != 0) {
						$mainid = $reply_to->parent;
						$reply_to_id = $reply_to->id;
					} else {
						$mainid = $to;
					}

					$body = post2db($_POST['responseminiblog'], 'group', $mainid);

					$check = $db->get_var("SELECT author FROM miniblog WHERE id = '" . $mainid . "' AND removed = '0' AND groupid = '$group->id'");
					if (!$check) {
						die("Kļūdains parent id! Iespējams kamēr rakstīji komentāru, kāds izdzēsa tēmu.");
					}
					$check2 = $db->get_var("SELECT author FROM miniblog WHERE id = '" . $mainid . "' AND closed = '1'");
					if ($check2) {
						die("Tēma ir slēgta.");
					}
					if ($mainid) {
						if (!isset($_SESSION['antiflood']) or $_SESSION['antiflood'] < time() - 5) {
							$_SESSION["antiflood"] = time();
							$db->query("INSERT INTO miniblog (groupid,author,date,text,ip,parent,reply_to) VALUES ('$group->id','$auth->id',NOW(),'$body','$auth->ip','$mainid','$reply_to_id')");
							$newid = $db->insert_id;
							$db->query("UPDATE miniblog SET bump = '" . time() . "', posts = posts+1 WHERE id = '$mainid'");
							if (!empty($reply_to_id)) {
								$db->query("UPDATE miniblog SET bump = '" . time() . "', posts = posts+1 WHERE id = '$reply_to_id'");
							}
							update_karma($auth->id);
							destroy_cdir();

							$body = $db->get_var("SELECT `text` FROM `miniblog` WHERE `id` = '$mainid'");

							$title = mb_get_title(stripslashes($body));
							$strid = mb_get_strid($title);
							$url = '/group/' . $group->id . '/forum/' . base_convert($mainid, 10, 36);
							push('Atbildēja <a href="' . $url . '#m' . $newid . '">' . $group->title . ' grupā &quot;' . textlimit($title, 32, '...') . '&quot;</a>', '', 'mb-answ-' . $mainid);

							$newpost = $db->get_row("SELECT * FROM `miniblog` WHERE id = '$newid'");
							$newpost->text = mention($newpost->text, $url, 'group', $mainid);
							$db->query("UPDATE `miniblog` SET `text` = '" . sanitize($newpost->text) . "' WHERE id = '$newpost->id'");

							notify($inprofile->id, 8, $mainid, $url);
							if (!empty($reply_to_id) && $inprofile->id != $reply_to->author) {
								notify($reply_to->author, 8, $mainid, $url);
							}
							if (isset($_GET['postcomment'])) {
								die('ok');
							}
							header('Location: ' . $url);
							exit;
						} else {
							die('err: flood');
						}
					}
					if (isset($_GET['postcomment'])) {
						die('err: wrong params');
					}
				}

				if ($auth->ok && isset($_GET['delete'])) {
					$delete = (int) $_GET['delete'];
					if ($delete && $delete > 0) {
						$check = $db->get_row("SELECT author,parent FROM miniblog WHERE id = '" . $delete . "' AND removed = '0' AND groupid = '$group->id' LIMIT 1");
						if ($auth->id == $group->owner OR $auth->level == 1 OR $auth->level == 2 OR $is_mod) {
							$db->query("UPDATE miniblog SET removed = '1' WHERE id = ('" . $delete . "') AND groupid = '$group->id' LIMIT 1");
							$db->query("UPDATE miniblog SET removed = '1' WHERE parent = ('" . $delete . "') AND groupid = '$group->id'");
							$db->query("UPDATE clans SET posts = '" . $db->get_var("SELECT count(*) FROM miniblog WHERE groupid = '$group->id'") . "' WHERE id = '$group->id'");
							if ($check->parent) {
								$db->query("UPDATE miniblog SET posts = posts-1 WHERE id = '$check->parent'");
								header('Location: /?group=' . $group->id . '&act=community&single=' . $check->parent);
							} else {
								header('Location: /group/' . $group->id);
							}
							exit;
						}
					}
				}

				if ($auth->ok && ($auth->level == 1 OR $auth->level == 2) && isset($_GET['close']) && isset($_GET['single'])) {
					$sid = (int) $_GET['single'];
					$db->query("UPDATE miniblog SET closed = '1' WHERE id = '$sid'");
					header('Location: /?group=' . $group->id . '&act=community&single=' . $sid);
					exit;
				}

				if ($auth->ok && ($auth->level == 1 OR $auth->level == 2) && isset($_GET['unclose']) && isset($_GET['single'])) {
					$sid = (int) $_GET['single'];
					$db->query("UPDATE miniblog SET closed = '0' WHERE id = '$sid'");
					header('Location: /?group=' . $group->id . '&act=community&single=' . $sid);
					exit;
				}

				$tpl->newBlock('user-miniblog');

				if ($auth->ok && !isset($_GET['single']) && $auth->level != '5' && !$group->archived) {
					$tpl->newBlock('user-miniblog-form');
				} elseif ($group->archived) {
					$tpl->newBlock('archived');
				}

				if (!isset($_GET['single'])) {
					$records = $db->get_results("SELECT * FROM miniblog WHERE groupid = ('" . $group->id . "') AND removed = '0' AND parent = '0' ORDER BY date DESC LIMIT $skip,$end");
				} else {
					$single = (int) $_GET['single'];
					$records = $db->get_results("SELECT * FROM miniblog WHERE id = ('$single') AND groupid = ('" . $group->id . "') AND removed = '0' AND parent = '0' ORDER BY date DESC LIMIT $skip,$end");
				}

				if ($records) {

					$tpl->newBlock('user-miniblog-list');
					foreach ($records as $record) {
						$tpl->newBlock('user-miniblog-list-node');

						$title = textlimit(preg_replace("#(^|[\n ]|<a(.*?)>)http://(www\.)?youtube\.com/watch\?v=([a-zA-Z0-9\-_]+)(</a>)?#ime", 'get_youtube_title_mb("\\4") ', $record->text), 64, '...');
						$user = get_user($record->author);

						$url = '/group/' . $group->id . '/forum/' . base_convert($record->id, 10, 36);

						if (isset($_GET['single'])) {
							$page_title = $title . ' | ' . $group->title . ' sarunas';
						}

						if ($user->avatar == '') {
							$user->avatar = 'none.png';
						}
						if ($user->av_alt) {
							$u_small_path1 = 'u_small';
						} else {
							$u_small_path1 = 'useravatar';
						}

						$tpl->assign(array(
							'url' => $url,
							'text' => add_smile($record->text),
							'date' => display_time_simple(strtotime($record->date)),
							'author' => usercolor($user->nick, $user->level, false, $user->id),
							'author-id' => $record->author,
							'aurl' => mkurl('user', $record->author, $user->nick),
							'author-avatar' => $user->avatar,
							'av-path' => $u_small_path1,
							'author-nick' => $user->nick,
							'id' => $record->id,
							'title' => $title
						));

						if ($auth->ok && !isset($_GET['single'])) {
							$tpl->newBlock('user-miniblog-list-node-response');
							$tpl->assign(array(
								'miniblog-id' => $record->id,
								'group-id' => $group->id,
							));
						}

						if (isset($_GET['single'])) {
							$limit = '';
						} else {
							$limit = ' LIMIT 0,3';
						}

						if ($record->posts) {

							$responses = $db->get_results("
						SELECT
		 					`miniblog`.`text` AS `text`,
		 					`miniblog`.`vote_value` AS `vote_value`,
		 					`miniblog`.`vote_users` AS `vote_users`,
		 					`miniblog`.`date` AS `date`,
		 					`miniblog`.`author` AS `author`,
		 					`miniblog`.`id` AS `id`,
							`miniblog`.`posts` AS `posts`,
							`miniblog`.`reply_to` AS `reply_to`,
		 					`users`.`nick` AS `nick`,
		 					`users`.`avatar` AS `avatar`,
		 					`users`.`av_alt` AS `av_alt`,
		 					`users`.`level` AS `level`
						FROM
							miniblog,
							users
						WHERE
							`miniblog`.`parent` = '" . $record->id . "' AND
							`miniblog`.`removed` = '0' AND
							`users`.`id` = `miniblog`.`author`
						ORDER BY
							`miniblog`.`date`
						ASC" . $limit);

							if ($responses) {
								$json = array();
								foreach ($responses as $response) {
									$json[$response->reply_to][] = $response;
								}
								$tpl->newBlock('miniblog-posts');
								$tpl->assign('mbout', mb_recursive($json, 0, 0, !isset($_GET['single']), 4));
							}
						}

						if (!isset($_GET['single'])) {
							$tpl->newBlock('mb-more');
							if ($record->posts > 3) {
								$text = 'Apskatīt vēl ' . ($record->posts - 3) . ' ' . lv_dsk($record->posts - 3, 'atbildi', 'atbildes') . '&nbsp;&raquo;';
							} else {
								$text = 'Atvērt sarunu&nbsp;&raquo;';
							}
							$tpl->assign(array(
								'text' => $text,
								'url' => $url
							));
						} elseif (!$record->posts) {
							$tpl->newBlock('miniblog-no');
						}
					}

					if (isset($_GET['single']) && $auth->ok && !$record->closed && $auth->level != '5' && !$group->archived) {
						$tpl->newBlock('user-miniblog-resp');
						$tpl->assign(array(
							'id' => $record->id,
							'token' => md5('mb' . $record->id . $remote_salt . $auth->nick)
						));
						$tpl->newBlock('mb-head');
						$tpl->assign(array(
							'mbid' => $record->id,
							'usrid' => $user->id,
							'edit_time' => time(),
							'lastid' => (int) $db->get_var("SELECT id FROM miniblog WHERE parent = '$record->id' AND removed = '0' ORDER BY id DESC LIMIT 1")
						));
					}

					if (!isset($_GET['single'])) {

						$total = $db->get_var("SELECT count(*) FROM miniblog WHERE groupid = ('" . $group->id . "') AND removed = '0' AND parent = '0'");

						if ($skip > 0) {
							if ($skip > $end) {
								$iepriekseja = $skip - $end;
							} else {
								$iepriekseja = 0;
							}
							$pager_next = '<a class="pager-next" title="Iepriekšējā lapa" href="/?group=' . $group->id . '&amp;act=community&amp;skip=' . $iepriekseja . '">&laquo;</a> <span>-</span>';
						} else {
							$pager_next = '';
						}
						$pager_prev = '';
						if ($total > $skip + $end) {
							$pager_prev = '<span>-</span> <a class="pager-prev" title="Nākamā lapa" href="/?group=' . $group->id . '&amp;act=community&amp;skip=' . ($skip + $end) . '">&raquo;</a>';
						}
						$startnext = 0;
						$page_number = 0;
						$pager_numeric = '';
						while ($total - $startnext > 0) {
							$page_number++;
							$class = '';
							if ($skip == $startnext) {
								$class = ' class="selected"';
							}

							if ($total / $end < 10 or $page_number < 4 or $page_number > $total / $end - 2 or $startnext == $skip or $startnext == $skip + $end or $startnext == $skip - $end) {
								if ($page_number != 1) {
									$pager_numeric .='<span>-</span> ';
								}
								$pager_numeric .= '<a href="/?group=' . $group->id . '&amp;act=community&amp;skip=' . $startnext . '"' . $class . '>' . $page_number . '</a> ';
							} elseif ($startnext == $skip + $end * 2 or $startnext == $skip - $end * 2) {
								if ($page_number != 1) {
									$pager_numeric .='<span>-</span> ';
								}
								$pager_numeric .= ' ... ';
							} elseif ($page_number == 4 && $skip / $end < 5) {
								if ($page_number != 1) {
									$pager_numeric .='<span>-</span> ';
								}
								$pager_numeric .= ' ... ';
							}

							$startnext = $startnext + $end;
						}
						$tpl->assignGlobal(array(
							'pager-next' => $pager_next,
							'pager-prev' => $pager_prev,
							'pager-numeric' => $pager_numeric
						));
					}
				}

				$db->query("UPDATE clans_members SET seenposts = '$group->posts' WHERE user = '$auth->id' AND clan = '$group->id'");
				//$db->debug();
			} else {
				$tpl->newBlock('noguestacc');
				$tpl->assign(array(
					'group-id' => $group->id,
				));
			}
		} else {
			$tpl->assign('active-tab-info', ' activeTab');
			$tpl->newBlock('group-info');
			$tpl->assign(array(
				'group-text' => add_smile($group->text, 0),
				'group-posts' => $group->posts,
				'group-members' => $group->members + 1,
				'group-admin' => $db->get_var("SELECT nick FROM users WHERE id = '$group->owner'"),
			));
			if ($group->owner == $auth->id or $auth->id == 1 OR $is_mod) {
				$tpl->newBlock('group-options');
				$tpl->assign(array(
					'group-id' => $group->id,
				));
			}
			if ($auth->ok && $auth->id != $group->owner) {

				if (!$db->get_var("SELECT count(*) FROM clans_members WHERE clan = '$group->id' AND user = '$auth->id'")) {
					$tpl->newBlock('group-info-apply');
					$tpl->assign(array(
						'group-id' => $group->id,
					));
				} elseif ($db->get_var("SELECT count(*) FROM clans_members WHERE clan = '$group->id' AND user = '$auth->id' AND approve = '0'")) {
					$tpl->newBlock('group-info-cancel');
					$tpl->assign(array(
						'group-id' => $group->id,
					));
				} elseif ($db->get_var("SELECT count(*) FROM clans_members WHERE clan = '$group->id' AND user = '$auth->id' AND approve = '1'")) {
					$tpl->newBlock('group-info-quit');
					$tpl->assign(array(
						'group-id' => $group->id,
					));
				}
			}

			$page_title = $group->title . ' | grupas profils';
		}
	}
}
?>