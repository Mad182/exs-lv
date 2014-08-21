<?php

/**
 * Lietotāja profila apskatīšanas un labošanas modulis
 */
$submodules = array('edit', 'avatar', 'settings', 'security', 'email', 'buytitle', 'changenick');

if (isset($_GET['var1']) && !in_array($_GET['var1'], $submodules)) {
	$userid = (int) $_GET['var1'];
} else {
	$userid = $auth->id;
}
$inprofile = $db->get_row("SELECT * FROM `users` WHERE `id` = '" . $userid . "' AND `deleted` = 0");

if ($inprofile) {

	profile_menu($inprofile, 'profile', 'profils', 'profilu');

	/**
	 * 	Lietotāja bloķēšana
	 */
	if (isset($_GET['var2']) && $_GET['var2'] == 'block' && im_mod() && $inprofile->level != 1 && ($inprofile->level != 2 or $auth->level == 1)) {

		// nosaka lietotāja aktīvo brīdinājumu skaitu
		$warn_count = $db->get_var("
			SELECT count(*) FROM `warns`
			WHERE
				`warns`.`user_id` 	= " . $inprofile->id . " AND
				`warns`.`active` 	= 1	AND
				`warns`.`site_id`	= " . $lang);

		// iesniegti bloķēšanas dati
		if (isset($_POST['block-reason'])) {

			$reason = sanitize(htmlspecialchars($_POST['block-reason']));
			$length = (int) $_POST['block-length'];

			/**
			 * Ja admins nav "globāls", t.i. norādīts sub-exa konfigurācijā, bans attiecas tikai uz to lapu
			 * Globālie admini var izvēlēties domēnu, vai visus domēnus (0)
			 */
			if (in_array($auth->id, $site_access[1]) || in_array($auth->id, $site_access[2])) {
				$site = $lang;
			} else {
				$site = (int) $_POST['block-domain'];
			}

			$db->query("INSERT INTO `banned` (`user_id`,`reason`,`time`,`length`,`author`,`ip`,`lang`)
				VALUES ('$inprofile->id','$reason','" . time() . "','$length','$auth->id','$inprofile->lastip', '$site')");
			get_banlist(true);

			// pārbauda, vai nav nepieciešams noņemt aktīvos brīdinājumus
			if (isset($_POST['warn-removal-reason']) && isset($_POST['warn-removal'])) {

				$removal_reason = post2db($_POST['warn-removal-reason']);
				$remove_count = (int) $_POST['warn-removal'];
				$remove_count = ($remove_count > $warn_count || $remove_count < 0) ? 0 : $remove_count;

				// norādīts, ka vismaz viens brīdinājums jānoņem
				if ($remove_count > 0) {

					// atlasa visu noņemamo brīdinājumu ids
					$get_ids = $db->get_results("
						SELECT `id` FROM `warns`
						WHERE `user_id` = '$inprofile->id' AND `active` = 1 AND `site_id` = $lang
						ORDER BY `created` ASC
						LIMIT $remove_count
					");
					if ($get_ids) {
						foreach ($get_ids as $single_id) {
							$ids[] = $single_id->id;
						}
						// noņem visus norādītos brīdinājumus
						if (!empty($ids)) {
							$db->query("
								UPDATE `warns`
								SET
									`warns`.`active` 		= 0,
									`warns`.`removed` 		= NOW(),
									`warns`.`removed_by` 	= $auth->id,
									`warns`.`remove_reason` = '$removal_reason'
								WHERE `warns`.`id` IN(" . implode(',', $ids) . ")
							");
						}
					}
				}
			}
			redirect('/banned');
		}

		$tpl->newBlock('user-profile-block');

		// izdrukā (vai neizdrukā) izvēlni ar brīdinājumu skaitu
		if (!$warn_count) {
			$tpl->newBlock('no-active-warns');
		} else {
			$tpl->newBlock('warn-removal');
			for ($i = 1; $i <= $warn_count; $i++) {
				$tpl->newBlock('warn-removal-option');
				$tpl->assign('x', $i);
			}
		}

		// globālajiem modiem rāda domēnu izvēli
		if (!in_array($auth->id, $site_access[1]) && !in_array($auth->id, $site_access[2])) {
			$tpl->newBlock('block-domain');

			foreach ($config_domains as $key => $domain) {
				if ($domain['domain'] !== 'secure.exs.lv' && $domain['domain'] !== 'android.exs.lv') {
					$tpl->newBlock('block-domain-node');
					$tpl->assign(array(
						'id' => $key,
						'domain' => $domain['domain']
					));
				}
			}
		}

		$page_title = 'Bloķēt lietotāju &quot;' . $inprofile->nick . '&quot;';
	}

	/**
	 * 	Ielādē submoduli pēc GET[var1]
	 */ elseif ($auth->ok && $auth->id == $inprofile->id && isset($_GET['var1']) && in_array($_GET['var1'], $submodules)) {

		require CORE_PATH . '/modules/user/submodules/' . mkslug($_GET['var1']) . '.php';
	}
	/**
	 * 	expts dāvināšana citam lietotājam
	 */ elseif ($auth->ok && $auth->id != $inprofile->id && isset($_GET['var2']) && $_GET['var2'] == 'give') {

		require CORE_PATH . '/modules/user/submodules/give.php';

		//view profile
	} else {

		include CORE_PATH . '/includes/class.friend.php';
		$friend = new Friend();

		if (isset($_GET['addfriend']) && $auth->level != 5) {
			$friend->add_friend($auth->id, $inprofile->id);
		}

		$tpl->newBlock('user-profile');

		$days = ceil((time() - strtotime($inprofile->date)) / 60 / 60 / 24);

		$posts = ($db->get_var("SELECT count(*) FROM comments WHERE author = '$inprofile->id' AND `removed` = 0") +
				$db->get_var("SELECT count(*) FROM galcom WHERE author = '$inprofile->id' AND `removed` = 0") +
				$db->get_var("SELECT count(*) FROM miniblog WHERE author = '" . $inprofile->id . "' AND removed = '0'"));

		if ($posts != $inprofile->posts) {
			$db->update('users', $inprofile->id, array('posts' => $posts));
		}

		$time = time_ago(strtotime($inprofile->lastseen));

		$voteval = $db->get_var("SELECT sum(vote_value) FROM comments WHERE author = '$inprofile->id'") +
				$db->get_var("SELECT sum(vote_value) FROM galcom WHERE author = '$inprofile->id'") +
				$db->get_var("SELECT sum(vote_value) FROM miniblog WHERE author = '$inprofile->id'");

		$tpl->assign(array(
			'user-nick' => htmlspecialchars($inprofile->nick),
			'user-date' => $inprofile->date,
			'user-days' => round($days),
			'user-days-text' => lv_dsk($days, 'dienas', 'dienām'),
			'user-web' => htmlspecialchars($inprofile->web),
			'user-lastseen' => $time,
			'user-pages' => $db->get_var("SELECT count(*) FROM pages WHERE author = ('" . $inprofile->id . "') AND `lang` = '$lang'"),
			'user-posts' => $posts,
			'user-karma' => $inprofile->karma,
			'user-postsday' => round($inprofile->posts / $days, 2),
			'user-vote_others' => $inprofile->vote_others,
			'user-vote_total' => $inprofile->vote_total,
			'user-votes' => $voteval
		));

		if ($auth->ok && $auth->id == $inprofile->id) {
			$tpl->assign(array(
				'edit' => '<p>[<a href="/user/edit">labot profilu</a>]
								[<a href="/user/changenick">mainīt niku</a>]
								[<a href="/subscribe">sekot/nesekot foruma sadaļām</a>]
								[<a href="/interests">interešu kategorijas</a>]</p>',
			));
		}
		if ($auth->level != 5 && $inprofile->level != 5 && $auth->ok && $auth->id != $inprofile->id && !$friend->pending_friendship($auth->id, $inprofile->id) && !$friend->get_friendship_id($auth->id, $inprofile->id)) {
			$tpl->assign(array(
				'friend-link' => '<a class="button primary" href="/user/' . $inprofile->id . '/?addfriend=true">Draudzēties</a><br />'
			));
		}
		if ($auth->ok && $auth->id != $inprofile->id && $auth->level != 5) {
			$tpl->newBlock('user-profile-pm');
			$date = time();
			$viewed = $db->get_var("SELECT id FROM viewprofile WHERE profile = '$inprofile->id' AND viewer = '$auth->id' AND time > '" . ($date - 3600) . "'");
			if (!$viewed) {
				$db->query("INSERT INTO viewprofile (profile,viewer,time) VALUES ('$inprofile->id','$auth->id','$date')");
			} else {
				$db->update('viewprofile', $viewed, array('time' => $date));
			}
		}
		if ($inprofile->about) {
			$tpl->newBlock('user-profile-about');
			$tpl->assign(array(
				'user-about' => add_smile($inprofile->about)
			));
		}


		if (!empty($inprofile->web)) {
			$tpl->newBlock('info-node');
			$tpl->assign(array(
				'title' => 'Mājaslapa',
				'value' => add_smile('<a href="' . htmlspecialchars($inprofile->web) . '" rel="nofollow">' . htmlspecialchars($inprofile->web) . '</a>', 0, 1, 1)
			));
		}

		if ($auth->ok && !empty($inprofile->skype)) {
			$tpl->newBlock('info-node');
			$tpl->assign(array(
				'title' => 'Skype',
				'value' => '<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script><a href="skype:' . htmlspecialchars($inprofile->skype) . '?chat"><img src="http://download.skype.com/share/skypebuttons/buttons/chat_green_transparent_97x23.png" style="border: none;" width="97" height="23" alt="Chat with me" /></a>'
			));
		}

		if ($auth->ok && $inprofile->city) {
			$tpl->newBlock('info-node');
			$tpl->assign(array(
				'title' => 'Pilsēta',
				'value' => $db->get_var("SELECT `title` FROM `city` WHERE `id` = '$inprofile->city'")
			));
		}

		if ($inprofile->lastseen > date("Y-m-d H:i:s", time() - 480) && $auth->id != $inprofile->id && !empty($inprofile->last_action)) {
			$tpl->newBlock('user-profile-last_action');
			$tpl->assign(array(
				'user-last_action' => $inprofile->last_action,
			));
		}

		if (im_mod()) {
			$tpl->newBlock('user-modinfo');
			$tpl->assign(array(
				'lastip' => $inprofile->lastip,
				'user_agent' => $inprofile->user_agent,
				'mail' => $inprofile->mail
			));
		}

		if (im_mod() && $inprofile->level != 1 && $inprofile->level != 2) {
			$tpl->newBlock('user-profile-ban');
		}

		if ($auth->id == 1 && $lang == 7) {
			$tpl->newBlock('user-profile-lol');
		}

		$awards = $db->get_results("SELECT * FROM `awards` WHERE `user` = " . $inprofile->id . " ORDER BY `date` DESC");
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

		$articles = $db->get_results("SELECT `title`,`strid` FROM `pages` WHERE `author` = '$inprofile->id' AND `category` != '83' AND `lang` = '$lang' ORDER BY `date` DESC LIMIT 10");
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
			`bookmarks`.`userid` = '" . $inprofile->id . "' AND
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

		if (!empty($auth->mobile)) {
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
		`viewprofile`.`profile` = '$inprofile->id' AND
		`users`.`id` = `viewprofile`.`viewer`
	ORDER BY
		`viewprofile`.`time`
	DESC LIMIT " . $profile_views_limit);

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
		$actions = $db->get_results("SELECT * FROM `userlogs` WHERE `user` = '$inprofile->id' AND `lang` = '$lang' ORDER BY `time` DESC, `id` DESC LIMIT $skip,$end");
		if ($actions) {
			$out .= '<ul class="user-actions" id="profile-user-actions">';
			foreach ($actions as $action) {
				if (!$action->avatar) {
					$action->avatar = get_avatar($inprofile, 's');
				} else {
					$action->avatar = $action->avatar;
				}
				if (substr($action->avatar, 0, 22) == '/dati/bildes/topic-av/') {
					$action->avatar = 'http://exs.lv' . $action->avatar;
				}
				if (substr($action->avatar, 0, 8) == '/bildes/') {
					$action->avatar = 'http://img.exs.lv' . $action->avatar;
				}
				$out .= '<li><img class="av" src="' . $action->avatar . '" alt="" /><span>Pirms ' . time_ago($action->time) . '</span><br />' . $action->action . '</li>';
			}
			$out .= '</ul>';
		}

		$total = $db->get_var("SELECT count(*) FROM `userlogs` WHERE `user` = '$inprofile->id' AND `lang` = '$lang' LIMIT 60");
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
				$pager_next = '<a class="pager-next" title="Iepriekšējā lapa" href="/user/' . $inprofile->id . '/?actions=' . $iepriekseja / $end . '">&laquo;</a>';
			} else {
				$pager_next = '';
			}
			$pager_prev = '';
			if ($total > $skip + $end) {
				$pager_prev = '<span>-</span> <a class="pager-prev" title="Nākamā lapa" href="/user/' . $inprofile->id . '/?actions=' . ($skip + $end) / $end . '">&raquo;</a>';
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
				$pager_numeric .= '<span>-</span> <a href="/user/' . $inprofile->id . '/?actions=' . $startnext / $end . '"' . $class . '>' . $page_number . '</a> ';
				$startnext = $startnext + $end;
			}
			$out .= '<p class="core-pager ajax-pager">' . $pager_next . ' ' . $pager_numeric . ' ' . $pager_prev . '</p>';
		}

		if (isset($_GET['_']) && isset($_GET['actions'])) {
			die($out);
		}

		$tpl->assign(array(
			'out' => $out
		));

		if ($lang == 1) {
			$g_owners = $db->get_results("SELECT title,id FROM clans WHERE owner = '$inprofile->id' ORDER BY title ASC");
			$g_members = $db->get_results("SELECT `clans_members`.`clan` AS `clan`,`clans_members`.`moderator` AS `moderator`,`clans`.`title` AS `title` FROM `clans_members`,`clans` WHERE `clans_members`.`user` = '$inprofile->id' AND `clans_members`.`approve` = '1' AND `clans`.`id` = `clans_members`.`clan` ORDER BY `clans_members`.`moderator` DESC, `clans_members`.`date_added` ASC");
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
			`pages`.`title` AS `title`,
			`pages`.`strid` AS `strid`
		FROM
			`comments`,
			pages
		WHERE
			`comments`.`author` = ('" . $inprofile->id . "') AND
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
						'url' => '/read/' . $comment->strid . '#c' . $comment->id,
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
				`galcom`.`author` = ('" . $inprofile->id . "') AND
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

		if ($auth->ok && $auth->id == $inprofile->id) {
			$tpl->assignGlobal('profile-sel', ' class="selected"');
			$page_title = 'Tavs profils';
		}
	}
} else {
	$tpl->newBlock('error-nouser');
	$page_title = 'Kļūda: profils nav atrasts!';
}

$pagepath = '';
