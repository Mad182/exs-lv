<?php

if (isset($_GET['var1']) && !in_array($_GET['var1'], array('edit', 'buytitle', 'changenick'))) {
	$userid = (int) $_GET['var1'];
} else {
	$userid = $auth->id;
}
$user = $db->get_row("SELECT * FROM `users` WHERE `id` = '" . $userid . "'");

if ($user) {

	if ($auth->ok) {
		set_action('<a href="/user/' . $user->id . '">' . sanitize($user->nick) . '</a> profilu');
	}

	$inprofile = $user;

	include(CORE_PATH . '/includes/class.friend.php');
	$friend = new Friend();

	if (isset($_GET['addfriend']) && $auth->level != 5) {
		$friend->add_friend($auth->id, $user->id);
	}

	$tpl->newBlock('profile-menu');

	if (isset($_GET['var2']) && $_GET['var2'] == 'block' && im_mod() && $user->level != 1 && ($user->level != 2 or $auth->level == 1)) {

		if (isset($_POST['block-reason'])) {
			$reason = sanitize(htmlspecialchars($_POST['block-reason']));
			$length = (int) $_POST['block-length'];
			
			$site = 0;
			/* ja admins nav "globāls", tb norādīts sub-exa konfigurācijā, bans attiecas tikai uz to lapu */
			if(in_array($auth->id, $site_mods) || in_array($auth->id, $site_admins)) {
				$site = $lang;
			}

			$db->query("INSERT INTO `banned` (`user_id`,`reason`,`time`,`length`,`author`,`ip`,`lang`)
				VALUES ('$user->id','$reason','" . time() . "','$length','$auth->id','$user->lastip', '$site')");
			get_banlist(true);
			redirect('/banned');
		}

		$tpl->newBlock('user-profile-block');

		$tpl->assignGlobal(array(
			'user-id' => $user->id,
			'user-nick' => htmlspecialchars($user->nick),
			'active-tab-profile' => 'active'
		));
		$page_title = 'Bloķet lietotāju &quot;' . $user->nick . '&quot;';
	} elseif ($auth->id == $user->id && isset($_GET['var1']) && $_GET['var1'] == 'edit') {

		if ($user->avatar == '') {
			$user->avatar = 'none.png';
		}
		$tpl->newBlock('user-profile-edit');

		//write changes
		if ($auth->ok && $auth->id == $user->id && isset($_POST['edit-mail'])) {

			/* load libraries */
			require(CORE_PATH . '/includes/class.upload.php');

			$user->skype = input2db($_POST['edit-skype'], 20);
			$user->yt_name = input2db($_POST['edit-yt_name'], 20);
			$user->twitter = input2db($_POST['edit-twitter'], 30);

			if ($user->karma >= 500 || im_mod() || $user->custom_title_paid) {
				$user->custom_title = input2db($_POST['edit-custom_title'], 32);
			}

			if (filter_var($_POST['edit-mail'], FILTER_VALIDATE_EMAIL)) {
				$user->mail = email2db($_POST['edit-mail']);
			} else {
				$tpl->newBlock('invalid-mail');
			}

			$user->web = '';
			if (!empty($_POST['edit-web'])) {
				if (substr($_POST['edit-web'], 0, 4) == 'www.') {
					$web = 'http://' . $_POST['edit-web'];
				} else {
					$web = $_POST['edit-web'];
				}
				if (filter_var($web, FILTER_VALIDATE_URL)) {
					$user->web = sanitize(filter_var($web, FILTER_SANITIZE_URL));
				}
			}

			$user->signature = htmlpost2db($_POST['edit-signature']);
			$user->about = htmlpost2db($_POST['edit-about']);

			$user->show_code = (bool) $_POST['edit-show_code'];
			$user->show_lol = (bool) $_POST['edit-show_lol'];
			$user->show_rp = (bool) $_POST['edit-show_rp'];
			$user->showsig = (bool) $_POST['edit-enablesig'];
			$user->rte = (bool) $_POST['edit-enablerte'];
			$user->skin = (int) $_POST['edit-skin'];
			$user->city = (int) $_POST['edit-city'];

			//new avatar image
			if (isset($_FILES['edit-avatar'])) {
			
				$rand = md5(microtime() . $auth->id);
				$avatar_path = substr($rand, 0, 1) . '/' . substr($rand, 1, 1) . '/';

				$text = time() . '_' . $auth->id;
				$foo = new Upload($_FILES['edit-avatar']);
				$foo->file_new_name_body = $text;
				$foo->image_resize = true;
				$foo->image_convert = 'jpg';
				$foo->image_x = 90;
				$foo->image_y = 90;
				$foo->allowed = array('image/*');
				$foo->image_ratio_crop = true;
				$foo->jpeg_quality = 98;
				$foo->file_auto_rename = false;
				$foo->file_overwrite = true;
				$foo->process(CORE_PATH . '/dati/bildes/useravatar/'.$avatar_path);
				if ($foo->processed) {

					$foo->file_new_name_body = $text;
					$foo->image_resize = true;
					$foo->image_convert = 'jpg';
					$foo->image_x = 45;
					$foo->image_y = 45;
					$foo->allowed = array('image/*');
					$foo->image_ratio_crop = true;
					$foo->jpeg_quality = 98;
					$foo->file_auto_rename = false;
					$foo->file_overwrite = true;
					$foo->process(CORE_PATH . '/dati/bildes/u_small/'.$avatar_path);

					$foo->file_new_name_body = $text;
					$foo->image_resize = true;
					$foo->image_convert = 'jpg';
					$foo->image_x = 170;
					$foo->image_y = 220;
					$foo->allowed = array('image/*');
					$foo->image_ratio_crop = false;
					$foo->image_ratio_no_zoom_in = true;
					$foo->jpeg_quality = 98;
					$foo->file_auto_rename = false;
					$foo->file_overwrite = true;
					$foo->process(CORE_PATH . '/dati/bildes/u_large/'.$avatar_path);

					if (file_exists(CORE_PATH . '/dati/bildes/useravatar/' . $avatar_path . $text . '.jpg')) {
						if ($user->avatar != 'none.png' && !empty($user->avatar) && !empty($user->av_alt)) {
							$db->query("INSERT INTO `avatar_history` (user_id,avatar,changed) VALUES ('$user->id','$user->avatar',NOW())");
						}
						$user->avatar = $avatar_path . $text . '.jpg';
						$user->av_alt = 1;
					}
					$foo->clean();
				}
			}

			$db->update('users', $auth->id, array(
				'show_code' => $user->show_code,
				'show_lol' => $user->show_lol,
				'show_rp' => $user->show_rp,
				'web' => $user->web,
				'city' => $user->city,
				'skype' => $user->skype,
				'mail' => $user->mail,
				'avatar' => $user->avatar,
				'rs_nick' => $user->rs_nick,
				'av_alt' => $user->av_alt,
				'signature' => $user->signature,
				'about' => $user->about,
				'showsig' => $user->showsig,
				'skin' => $user->skin,
				'yt_name' => $user->yt_name,
				'twitter' => $user->twitter,
				'rte' => $user->rte,
				'custom_title' => $user->custom_title
			));

			$auth->reset();
			update_karma($auth->id, true);

			if (!empty($_POST['password-1']) && !empty($_POST['password-2']) && $_POST['password-1'] === $_POST['password-2']) {
				if (pwd($_POST['password-old']) == $user->pwd || ($user->pwd == '' && (!empty($user->draugiem_id) || !empty($user->facebook_id)))) {
					if (strlen($_POST['password-1']) > 5) {

						$db->update('users', $auth->id, array('pwd' => pwd($_POST['password-1'])));

						$auth->login($user->nick, $_POST['password-1']);

						$tpl->newBlock('save-pwd');
					} else {
						$tpl->newBlock('invalid-pwdlen');
					}
				} else {
					$tpl->newBlock('invalid-pwd');
				}
			}
			redirect('/user/edit');
		}

		if ($user->showsig) {
			$sigmark = ' checked="checked"';
		} else {
			$sigmark = '';
		}
		if ($user->rte) {
			$rtemark = ' checked="checked"';
		} else {
			$rtemark = '';
		}
		if ($user->show_code) {
			$show_codemark = ' checked="checked"';
		} else {
			$show_codemark = '';
		}
		if ($user->show_lol) {
			$show_lolmark = ' checked="checked"';
		} else {
			$show_lolmark = '';
		}
		if ($user->show_rp) {
			$show_rpmark = ' checked="checked"';
		} else {
			$show_rpmark = '';
		}

		//show form
		$tpl->gotoBlock('user-profile-edit');
		$tpl->assign(array(
			'user-nick' => $user->nick,
			'user-avatar' => $user->avatar,
			'user-mail' => $user->mail,
			'user-skype' => $user->skype,
			'user-yt_name' => $user->yt_name,
			'user-twitter' => $user->twitter,
			'user-skin-' . $user->skin => ' selected="selected"',
			'user-web' => htmlspecialchars($user->web),
			'user-signature' => htmlspecialchars($user->signature),
			'user-date' => $user->date,
			'user-about' => htmlspecialchars($user->about),
			'edit-enablesig-mark' => $sigmark,
			'edit-show_code-mark' => $show_codemark,
			'edit-show_lol-mark' => $show_lolmark,
			'edit-show_rp-mark' => $show_rpmark,
			'edit-enablerte-mark' => $rtemark
		));

		if ($user->karma >= 500 || im_mod() || $user->custom_title_paid) {
			$tpl->newBlock('custom_title');
			$tpl->assign(array(
				'user-custom_title' => $user->custom_title,
			));
		} else {
			$tpl->newBlock('custom_title_buy');
		}

		$citys = $db->get_results("SELECT * FROM `city` ORDER BY `id` ASC");

		foreach ($citys as $city) {
			$tpl->newBlock('user-profile-edit-city');
			if ($user->city == $city->id) {
				$sel = ' selected="selected"';
			} else {
				$sel = '';
			}
			$tpl->assign(array(
				'city-id' => $city->id,
				'city-title' => $city->title,
				'city-sel' => $sel
			));
		}

		$tpl->assignGlobal(array(
			'user-id' => $user->id,
			'user-nick' => htmlspecialchars($user->nick),
			'active-tab-profile' => 'active',
			'profile-sel' => ' class="selected"'
		));
		$page_title = 'Tavs profils';

		$tpl->newBlock('tinymce-enabled');
	} elseif ($auth->ok && $auth->id == $user->id && isset($_GET['var1']) && $_GET['var1'] == 'buytitle') {

		//write changes
		if (isset($_GET['buytitle_pay']) && $_GET['buytitle_pay'] == 'true') {

			if ($user->credit < 3) {
				set_flash('Nepietiek exs.lv kredīta!');
			} else {
				$db->query("UPDATE users SET custom_title_paid = '1', credit = credit-'3' WHERE id = ('" . $user->id . "')");
				set_flash('Tagad Tu vari mainīt savu lietotāja nosaukumu!', 'success');
			}

			redirect('/user/edit');
		}

		$pay = '';
		if ($user->credit >= 3) {
			$pay = '<p><a href="/user/buytitle/?buytitle_pay=true"><strong>Nopirkt iespēju mainīt lietotāja nosaukumu</strong></a></p>';
		}

		$tpl->newBlock('user-profile-buytitle');
		$tpl->assign(array(
			'user-credit' => $user->credit,
			'pay' => $pay
		));

		$tpl->assignGlobal(array(
			'user-id' => $user->id,
			'user-nick' => htmlspecialchars($user->nick),
			'active-tab-profile' => 'active',
			'profile-sel' => ' class="selected"'
		));
		$page_title = 'Lietotāja nosaukuma maiņa';
	} elseif ($auth->ok && $auth->id != $user->id && isset($_GET['var2']) && $_GET['var2'] == 'give') {

		$credit = $db->get_var("SELECT credit FROM users WHERE id = '$auth->id'");
		if ($credit) {

			$tpl->newBlock('user-profile-give');

			if (isset($_POST['submit']) && isset($_POST['exs-amount']) && !empty($_POST['exs-amount'])) {
				$amount = intval($_POST['exs-amount']);
				if ($credit >= $amount && $amount > 0) {
					$db->query("UPDATE users SET credit = credit+'" . $amount . "' WHERE id = ('" . $user->id . "')");
					$db->query("UPDATE users SET credit = credit-'" . $amount . "' WHERE id = ('" . $auth->id . "')");

					userlog($auth->id, 'Uzdāvināja ' . $amount . ' expunktus ' . $user->nick, '/dati/bildes/useravatar/' . $user->avatar);
					userlog($user->id, 'Saņēma ' . $amount . ' expunktus no ' . $auth->nick, '/dati/bildes/useravatar/' . $auth->avatar);

					$credit = $credit - $amount;
					set_flash('Pārskaitījums veikts!', 'success');
				} else {
					set_flash('Kļūda!', 'error');
				}
			}

			for ($i = 1; $i <= $credit; $i++) {
				$tpl->newBlock('give-am');
				$tpl->assign(array(
					'value' => $i,
				));
			}
		}

		$tpl->assignGlobal(array(
			'user-id' => $user->id,
			'user-nick' => htmlspecialchars($user->nick),
			'active-tab-profile' => 'active',
			'profile-sel' => ' class="selected"'
		));
		$page_title = 'Dāvināt exs kredītu';
	} elseif ($auth->ok && $auth->id == $user->id && isset($_GET['var1']) && $_GET['var1'] == 'changenick') {

		//write changes
		if (isset($_POST['new-nick'])) {

			if ($user->credit < 5) {
				set_flash('Nepietiek exs.lv kredīta!', 'error');
				redirect('/user/changenick');
			}

			$slugnick = mkslug($_POST['new-nick']);

			if (strlen(trim($_POST['new-nick'])) > 3 && strlen(trim($_POST['new-nick'])) <= 16 && !empty($slugnick)) {
				$newnick = sanitize(trim($_POST['new-nick']));

				if ($slugnick == 'page' || $slugnick == '' || $slugnick == '-') {
					set_flash('Izskatās, ka niks sastāv no neatļautiem simboliem!', 'error');
				} elseif ($db->get_var("SELECT count(*) FROM `users` WHERE `nick` = '" . $newnick . "' OR `nick` = '" . $slugnick . "'")) {
					set_flash('Niks ir aizņemts!', 'error');
				} else {
					$db->query("INSERT INTO `nick_history` (`user_id`,`nick`,`changed`) VALUES ('$auth->id','" . sanitize($auth->nick) . "',NOW())");
					$db->query("UPDATE users SET nick = '$newnick', credit = credit-'5' WHERE id = '" . $auth->id . "'");
					$isblog = get_blog_by_user($auth->id);
					if ($isblog) {
						$db->query("UPDATE cat SET title = '" . $newnick . " blogs' WHERE isblog = '$auth->id' AND id = '$isblog'");
					}
					$auth->reset();

					set_flash('Tavs lietotājvārds ir nomainīts!', 'success');
					redirect();
				}
			} else {
				set_flash('Niks neatbilst atļautajam garumam (4-16 simboli)!', 'error');
			}
		}

		$tpl->newBlock('user-profile-changenick');
		$tpl->assign(array(
			'user-credit' => $user->credit
		));


		$tpl->assignGlobal(array(
			'user-id' => $user->id,
			'user-nick' => htmlspecialchars($user->nick),
			'active-tab-profile' => 'active',
			'profile-sel' => ' class="selected"'
		));
		$page_title = 'Exs.lv nika maiņa';

//view profile
	} else {
		$tpl->newBlock('user-profile');

		$days = ceil((time() - strtotime($user->date)) / 60 / 60 / 24);

		$posts = ($db->get_var("SELECT count(*) FROM comments WHERE author = '$user->id' AND `removed` = 0") + 
				$db->get_var("SELECT count(*) FROM galcom WHERE author = '$user->id' AND `removed` = 0") + 
				$db->get_var("SELECT count(*) FROM miniblog WHERE author = '" . $user->id . "' AND removed = '0'"));

		if ($posts != $user->posts) {
			$db->update('users', $user->id, array('posts' => $posts));
		}

		$time = time_ago(strtotime($user->lastseen));

		$voteval =
				$db->get_var("SELECT sum(vote_value) FROM comments WHERE author = '$user->id'") +
				$db->get_var("SELECT sum(vote_value) FROM galcom WHERE author = '$user->id'") +
				$db->get_var("SELECT sum(vote_value) FROM miniblog WHERE author = '$user->id'");

		$tpl->assign(array(
			'user-nick' => htmlspecialchars($user->nick),
			'user-date' => $user->date,
			'user-days' => round($days),
			'user-days-text' => lv_dsk($days, 'dienas', 'dienām'),
			'user-web' => htmlspecialchars($user->web),
			'user-lastseen' => $time,
			'user-pages' => $db->get_var("SELECT count(*) FROM pages WHERE author = ('" . $user->id . "') AND `lang` = '$lang'"),
			'user-posts' => $posts,
			'user-karma' => $user->karma,
			'user-postsday' => round($user->posts / $days, 2),
			'user-vote_others' => $user->vote_others,
			'user-vote_total' => $user->vote_total,
			'user-votes' => $voteval
		));
		$tpl->assignGlobal(array(
			'user-id' => $user->id,
			'user-nick' => htmlspecialchars($user->nick),
			'active-tab-profile' => 'active'
		));
		if ($auth->ok && $auth->id == $user->id) {
			$tpl->assign(array(
				'edit' => '<p>[<a href="/user/edit">labot profilu</a>]
								[<a href="/user/changenick">mainīt niku</a>]
								[<a href="/subscribe">sekot/nesekot foruma sadaļām</a>]
								[<a href="/interests">interešu kategorijas</a>]</p>',
			));
		}
		if ($auth->level != 5 && $user->level != 5 && $auth->ok && $auth->id != $user->id && !$friend->pending_friendship($auth->id, $user->id) && !$friend->get_friendship_id($auth->id, $user->id)) {
			$tpl->assign(array(
				'friend-link' => '<a class="button primary" href="/user/' . $user->id . '/?addfriend=true">Draudzēties</a><br />'
			));
		}
		if ($auth->ok && $auth->id != $user->id && $auth->level != 5) {
			$tpl->newBlock('user-profile-pm');
			$date = time();
			$viewed = $db->get_var("SELECT id FROM viewprofile WHERE profile = '$user->id' AND viewer = '$auth->id' AND time > '" . ($date - 3600) . "'");
			if (!$viewed) {
				$db->query("INSERT INTO viewprofile (profile,viewer,time) VALUES ('$user->id','$auth->id','$date')");
			} else {
				$db->update('viewprofile', $viewed, array('time' => $date));
			}
		}
		if ($user->about) {
			$tpl->newBlock('user-profile-about');
			$tpl->assign(array(
				'user-about' => add_smile($user->about)
			));
		}
		
		
		if(!empty($user->web)) {
			$tpl->newBlock('info-node');
			$tpl->assign(array(
				'title' => 'Mājaslapa',
				'value' => '<a href="'.htmlspecialchars($user->web).'" rel="nofollow">'.htmlspecialchars($user->web).'</a>'
			));
		}
		
		if ($auth->ok && !empty($user->skype)) {
			$tpl->newBlock('info-node');
			$tpl->assign(array(
				'title' => 'Skype',
				'value' => '<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script><a href="skype:'.htmlspecialchars($user->skype).'?chat"><img src="http://download.skype.com/share/skypebuttons/buttons/chat_green_transparent_97x23.png" style="border: none;" width="97" height="23" alt="Chat with me" /></a>'
			));
		}
		
		if ($auth->ok && $user->city) {
			$tpl->newBlock('info-node');
			$tpl->assign(array(
				'title' => 'Pilsēta',
				'value' => $db->get_var("SELECT `title` FROM `city` WHERE `id` = '$user->city'")
			));
		}
	
		if ($user->lastseen > date("Y-m-d H:i:s", time() - 480) && $auth->id != $user->id && !empty($user->last_action)) {
			$tpl->newBlock('user-profile-last_action');
			$tpl->assign(array(
				'user-last_action' => $user->last_action,
			));
		}

		if (im_mod()) {
			$tpl->newBlock('user-modinfo');
			$tpl->assign(array(
				'lastip' => $user->lastip,
				'mail' => $user->mail
			));
		}

		if (im_mod() && $user->level != 1 && $user->level != 2) {
			$tpl->newBlock('user-profile-ban');
		}

		if($auth->id == 1 && $lang == 7) {
			$tpl->newBlock('user-profile-lol');
		}

		if(substr($user->nick, 0, 9) != 'Dzēsts #') {
			$awards = $db->get_results("SELECT * FROM `awards` WHERE `user` = " . $user->id . " ORDER BY `date` DESC");
			if ($awards) {
				$tpl->newBlock('user-profile-awards');
				foreach ($awards as $award) {
					$tpl->newBlock('user-profile-awards-node');
					$tpl->assign(array(
						'award-title' => $award->title,
						'award-icon' => $award->icon,
						'award-link' => $award->link
					));
				}
			}

			$articles = $db->get_results("SELECT `title`,`strid` FROM `pages` WHERE `author` = '$user->id' AND `category` != '83' AND `lang` = '$lang' ORDER BY `date` DESC LIMIT 10");
			if ($articles) {
				$tpl->newBlock('user-profile-lastpage');
				foreach ($articles as $article) {
					$tpl->newBlock('user-profile-lastpage-node');
					$tpl->assign(array(
						'node-url' => '/read/' . $article->strid,
						'lastpage-title' => textlimit($article->title, 42, '..')
					));
				}
			}

			$articles = $db->get_results("
			SELECT
				`bookmarks`.`pageid` AS `pageid`,
				`pages`.`title` AS `title`,
				`pages`.`strid` AS `strid`
			FROM
				`bookmarks`,
				`pages`
			WHERE
				`bookmarks`.`userid` = '" . $user->id . "' AND
				`pages`.`id` = `bookmarks`.`pageid` AND
				`pages`.`lang` = '$lang'
			ORDER BY
				`bookmarks`.`id`
			DESC LIMIT 10");
			if ($articles) {
				$tpl->newBlock('user-profile-lastbookmark');
				foreach ($articles as $article) {
					$tpl->newBlock('user-profile-lastbookmark-node');
					$tpl->assign(array(
						'node-url' => '/read/' . $article->strid,
						'bookmark-title' => textlimit($article->title, 42, '..')
					));
				}
			}

			if(!empty($auth->mobile)) {
				$profile_views_limit = 10;
			}

			$views = $db->get_results("
		SELECT
			`viewprofile`.`viewer` AS `viewer`,
			`viewprofile`.`time` AS `time`,
			`users`.`nick` AS `nick`,
			`users`.`avatar` AS `avatar`,
			`users`.`av_alt` AS `av_alt`
		FROM
			viewprofile,
			users
		WHERE
			`viewprofile`.`profile` = '$user->id' AND
			`users`.`id` = `viewprofile`.`viewer`
		ORDER BY
			`viewprofile`.`time`
		DESC LIMIT ".$profile_views_limit);

			if ($views) {
				$tpl->newBlock('user-profile-views');
				foreach ($views as $view) {
					$avatar = get_avatar($view, 's');
					$tpl->newBlock('user-profile-views-node');
					$tpl->assign(array(
						'id' => $view->viewer,
						'date' => date('d.m.Y. H:i', $view->time),
						'nick' => htmlspecialchars($view->nick),
						'avatar' => $avatar
					));
				}
			}

			$tpl->newBlock('user-actions');
			$end = 10;
			$out = '';
			if (isset($_GET['actions'])) {
				$skip = (int) $_GET['actions'] * $end;
			} else {
				$skip = 0;
			}
			$actions = $db->get_results("SELECT * FROM `userlogs` WHERE `user` = '$user->id' AND `lang` = '$lang' ORDER BY `time` DESC, `id` DESC LIMIT $skip,$end");
			if ($actions) {
				$out .= '<ul class="user-actions" id="profile-user-actions">';
				foreach ($actions as $action) {
					if (!$action->avatar) {
						$action->avatar = get_avatar($user, 's');
					} else {
						$action->avatar = $action->avatar;
					}
					$out .= '<li><img class="av" src="' . $action->avatar . '" alt="" /><span>Pirms ' . time_ago($action->time) . '</span><br />' . $action->action . '</li>';
				}
				$out .= '</ul>';
			}

			$total = $db->get_var("SELECT count(*) FROM `userlogs` WHERE `user` = '$user->id' AND `lang` = '$lang' LIMIT 60");
			if ($total > 60) {
				$total = 60;
			}
			if ($total > $end) {
				if ($skip > 0) {
					if ($skip > $end) {
						$iepriekseja = $skip - $end;
					} else {
						$iepriekseja = 0;
					}
					$pager_next = '<a class="pager-next" title="Iepriekšējā lapa" href="/user/' . $user->id . '/?actions=' . $iepriekseja / $end . '">&laquo;</a>';
				} else {
					$pager_next = '';
				}
				$pager_prev = '';
				if ($total > $skip + $end) {
					$pager_prev = '<span>-</span> <a class="pager-prev" title="Nākamā lapa" href="/user/' . $user->id . '/?actions=' . ($skip + $end) / $end . '">&raquo;</a>';
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
					$pager_numeric .= '<span>-</span> <a href="/user/' . $user->id . '/?actions=' . $startnext / $end . '"' . $class . '>' . $page_number . '</a> ';
					$startnext = $startnext + $end;
				}
				$out .= '<p class="core-pager ajax-pager">' . $pager_next . ' ' . $pager_numeric . ' ' . $pager_prev . '</p>';
			}
		}

		if (isset($_GET['_']) && isset($_GET['actions'])) {
			die($out);
		}

		$tpl->assign(array(
			'out' => $out
		));

		if ($lang == 1) {
			$g_owners = $db->get_results("SELECT title,id FROM clans WHERE owner = '$user->id' ORDER BY title ASC");
			$g_members = $db->get_results("SELECT `clans_members`.`clan` AS `clan`,`clans_members`.`moderator` AS `moderator`,`clans`.`title` AS `title` FROM `clans_members`,`clans` WHERE `clans_members`.`user` = '$user->id' AND `clans_members`.`approve` = '1' AND `clans`.`id` = `clans_members`.`clan` ORDER BY `clans_members`.`moderator` DESC, `clans_members`.`date_added` ASC");
			if ($g_owners or $g_members) {
				$tpl->newBlock('grouplist');
				if ($g_owners) {
					foreach ($g_owners as $g_owner) {
						$tpl->newBlock('g-admin');
						$tpl->assign(array(
							'group-id' => $g_owner->id,
							'group-title' => $g_owner->title,
						));
					}
				}
				if ($g_members) {
					foreach ($g_members as $g_member) {
						$tpl->newBlock('g-member');
						if ($g_member->moderator) {
							$class = 'l-gmod';
						} else {
							$class = 'l-gmember';
						}
						$tpl->assign(array(
							'group-id' => $g_member->clan,
							'group-class' => $class,
							'group-title' => $g_member->title,
						));
					}
				}
			}
		}

		if (im_mod()) {

			$comments = $db->get_results("
		SELECT
			`comments`.`pid` AS `pid`,
			`comments`.`id` AS `id`,
			`comments`.`text` AS `text`,
			`pages`.`title` AS `title`
		FROM
			`comments`,
			pages
		WHERE
			`comments`.`author` = ('" . $user->id . "') AND
			`pages`.`id` = `comments`.`pid` AND
			`pages`.`category` != '83' AND
			`pages`.`lang` = '$lang'
		ORDER BY
			`comments`.`date`
		DESC
		LIMIT 20");

			if ($comments) {
				$tpl->newBlock('user-profile-lastcom');
				foreach ($comments as $comment) {
					$tpl->newBlock('user-profile-lastcom-node');
					$tpl->assign(array(
						'url' => mkurl('page', $comment->pid, $comment->title, '#c' . $comment->id),
						'comments-text' => textlimit($comment->text, 42, '..')
					));
				}
			}

			$comments = $db->get_results("
			SELECT
				`galcom`.`bid` AS `bid`,
				`galcom`.`id` AS `id`,
				`galcom`.`text` AS `text`,
				`images`.`uid` AS `uid`
			FROM
				`galcom`,
				images
			WHERE
				`galcom`.`author` = ('" . $user->id . "') AND
				`images`.`id` = `galcom`.`bid`
			ORDER BY
				`galcom`.`date`
			DESC
			LIMIT 20");
			if ($comments && $lang == 1) {
				$tpl->newBlock('user-profile-lastgcom');
				foreach ($comments as $comment) {
					$tpl->newBlock('user-profile-lastgcom-node');
					$tpl->assign(array(
						'comments-image' => $comment->bid,
						'comments-uid' => $comment->uid,
						'comments-id' => $comment->id,
						'comments-text' => textlimit($comment->text, 42, '..')
					));
				}
			}
		}

		if ($auth->ok && $auth->id == $user->id) {
			$tpl->assignGlobal('profile-sel', ' class="selected"');
			$page_title = 'Tavs profils';
		} else {
			$page_title = $user->nick . ' | Profils';
		}
	}
} else {
	$tpl->newBlock('error-nouser');
	$page_title = 'Kļūda: profils nav atrasts!';
}

$pagepath = '';
