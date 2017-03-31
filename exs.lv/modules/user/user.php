<?php

// papildu kods js "cluetip" iekļaušanai
$tpl->assignInclude('module-head', CORE_PATH . '/modules/' . $category->module . '/head.tpl');
$tpl->prepare();

/**
 * Lietotāja profila apskatīšanas un labošanas modulis
 */
$submodules = ['edit', 'avatar', 'settings', 'security', 'auth2f', 'email', 'buytitle', 'changenick'];

if (isset($_GET['var1']) && !in_array($_GET['var1'], $submodules)) {
	$userid = (int) $_GET['var1'];
} else {
	$userid = $auth->id;
}

$inprofile = $db->get_row("SELECT * FROM `users` WHERE `id` = '" . $userid . "' AND `deleted` = 0");

if ($inprofile && ($auth->ok === true || !$inprofile->private)) {

	profile_menu($inprofile, 'profile', 'profils', 'profilu');

	/**
	 * 	Lietotāja profilu bloķēšana
	 */
	if (isset($_GET['var2']) && $_GET['var2'] == 'block') {

		require CORE_PATH . '/modules/user/submodules/banning.php';
	}
	/**
	 * 	Ielādē submoduli pēc GET[var1]
	 */
	elseif ($auth->ok && $auth->id == $inprofile->id && isset($_GET['var1']) && in_array($_GET['var1'], $submodules)) {

		require CORE_PATH . '/modules/user/submodules/' . mkslug($_GET['var1']) . '.php';
	}
	/**
	 * 	expts dāvināšana citam lietotājam
	 */
	elseif ($auth->ok && $auth->id != $inprofile->id && isset($_GET['var2']) && $_GET['var2'] == 'give') {

		require CORE_PATH . '/modules/user/submodules/give.php';

	//view profile
	} else {

		include CORE_PATH . '/includes/class.friend.php';
		$friend = new Friend();

		if (isset($_GET['addfriend']) && $auth->level != 5 && check_token('addfriend', $_GET['token'])) {
			$friend->add_friend($auth->id, $inprofile->id);
		}

		$tpl->newBlock('user-profile');

		$days = ceil((time() - strtotime($inprofile->date)) / 60 / 60 / 24);

		$posts = ($db->get_var("SELECT count(*) FROM comments WHERE author = '$inprofile->id' AND `removed` = 0") +
				$db->get_var("SELECT count(*) FROM galcom WHERE author = '$inprofile->id' AND `removed` = 0") +
				$db->get_var("SELECT count(*) FROM miniblog WHERE author = '" . $inprofile->id . "' AND removed = '0'"));

		if ($posts != $inprofile->posts) {
			$db->update('users', $inprofile->id, ['posts' => $posts]);
		}

		$time = time_ago(strtotime($inprofile->lastseen));

		$voteval = $db->get_var("SELECT sum(vote_value) FROM comments WHERE author = '$inprofile->id'") +
				$db->get_var("SELECT sum(vote_value) FROM galcom WHERE author = '$inprofile->id'") +
				$db->get_var("SELECT sum(vote_value) FROM miniblog WHERE author = '$inprofile->id'");

		$tpl->assign([
			'user-nick' => h($inprofile->nick),
			'user-date' => $inprofile->date,
			'user-days' => round($days),
			'user-days-text' => lv_dsk($days, 'dienas', 'dienām'),
			'user-web' => add_smile($inprofile->web, 0, 1, 1), //add smile nofiltrē blacklistotās adreses
			'user-lastseen' => $time,
			'user-pages' => $db->get_var("SELECT count(*) FROM pages WHERE author = ('" . $inprofile->id . "') AND `lang` = '$lang'"),
			'user-posts' => $posts,
			'user-karma' => $inprofile->karma,
			'user-postsday' => round($inprofile->posts / $days, 2),
			'user-vote_others' => $inprofile->vote_others,
			'user-vote_total' => $inprofile->vote_total,
			'user-votes' => $voteval
		]);

		if ($auth->ok && $auth->id == $inprofile->id) {
			$tpl->assign([
				'edit' => '<p><a class="button primary" href="/user/edit">labot profilu</a> <a class="button primary" href="/user/changenick">mainīt niku</a> <a class="button primary" href="/subscribe">sekot/nesekot foruma sadaļām</a> <a class="button primary" href="/interests">interešu kategorijas</a></p>',
			]);
		}
		if ($auth->level != 5 && $inprofile->level != 5 && $auth->ok && $auth->id != $inprofile->id && !$friend->pending_friendship($auth->id, $inprofile->id) && !$friend->get_friendship_id($auth->id, $inprofile->id)) {
			$tpl->assign([
				'friend-link' => '<a class="button primary" href="/user/' . $inprofile->id . '/?addfriend=true&amp;token=' . make_token('addfriend') . '">Draudzēties</a><br />'
			]);
		}
		if ($auth->ok && $auth->id != $inprofile->id && $auth->level != 5) {
			$tpl->newBlock('user-profile-pm');
			$date = time();
			$viewed = $db->get_var("SELECT id FROM viewprofile WHERE profile = '$inprofile->id' AND viewer = '$auth->id' AND time > '" . ($date - 3600) . "'");
			if (!$viewed) {
				$db->query("INSERT INTO viewprofile (profile,viewer,time) VALUES ('$inprofile->id','$auth->id','$date')");
			} else {
				$db->update('viewprofile', $viewed, ['time' => $date]);
			}
		}
		if ($inprofile->about && $inprofile->posts >= 10) {
			$tpl->newBlock('user-profile-about');
			$tpl->assign([
				'user-about' => add_smile($inprofile->about)
			]);
		}


		if (!empty($inprofile->web)) {
			$tpl->newBlock('info-node');
			$tpl->assign([
				'title' => 'Mājaslapa',
				'value' => add_smile('<a href="' . h($inprofile->web) . '" rel="nofollow" target="_blank">' . h($inprofile->web) . '</a>', 0, 1, 1)
			]);
		}

		if ($auth->ok && !empty($inprofile->skype)) {
			$tpl->newBlock('info-node');
			$tpl->assign([
				'title' => 'Skype',
				'value' => '<a href="skype:' . h($inprofile->skype) . '?chat">' . h($inprofile->skype) . '</a>'
			]);
		}

		if ($auth->ok && $inprofile->city) {
			$tpl->newBlock('info-node');
			$tpl->assign([
				'title' => 'Pilsēta',
				'value' => $db->get_var("SELECT `title` FROM `city` WHERE `id` = '$inprofile->city'")
			]);
		}

		if ($inprofile->lastseen > date("Y-m-d H:i:s", time() - 480) && $auth->id != $inprofile->id && !empty($inprofile->last_action)) {
			$tpl->newBlock('user-profile-last_action');
			$tpl->assign([
				'user-last_action' => $inprofile->last_action,
			]);
		}

		if (im_mod()) {
			//dabū visus pievienoto (cookies) profilu nikus ar linkiem
			$profiles = 'nav!';
			if(!empty($inprofile->connected_profiles)) {
				$profiles = explode(',', $inprofile->connected_profiles);

				foreach ($profiles as $key => $id) {

					if(!empty($id)) {

						$nick = get_user($id);

						if(!empty($nick) && !$nick->deleted) {
							$profiles[$key] = userlink($nick);
						} else {
							unset($profiles[$key]);
						}
				
					}
				}

				array_splice($profiles, count($profiles) - 1);
				$profiles = implode(', ', $profiles);
				if (!$profiles) {
					$profiles = 'nav!';
				}
			}

			$tpl->newBlock('user-modinfo');
			$tpl->assign([
				'lastip' => $inprofile->lastip,
				'user_agent' => $inprofile->user_agent,
				'mail' => $inprofile->mail,
				'cookie_users' => $profiles
			]);
			if ($inprofile->lastip != '127.0.0.1') {
				$tpl->assign('asn', get_asn($inprofile->lastip));
			}
		}

		if (im_mod() && $inprofile->level != 1 && $inprofile->level != 2) {
			$tpl->newBlock('user-profile-ban');
		}

		if ($auth->id == 1 && $lang == 7) {
			$tpl->newBlock('user-profile-lol');
		}

		//coding.lv nerādam exs.lv apbalvojumus
		if($lang !== 3) {
			$awards = $db->get_results("SELECT * FROM `awards` WHERE `user` = " . $inprofile->id . " ORDER BY `date` DESC");
			if ($awards) {
				$tpl->newBlock('user-profile-awards');
				foreach ($awards as $award) {
					$tpl->newBlock('user-profile-awards-node');
					$tpl->assign([
						'award-title' => $award->title,
						'award-icon' => $award->icon,
						'award-link' => $award->link
					]);
				}
			}

			$tpl->newBlock('user-profile-yearstats');
			$date = date('Y-m-d', strtotime("-1 year last Monday"));

			$images = $db->get_results("SELECT COUNT(*) as `count`, DATE(`date`) as `date` FROM `images` WHERE `uid` = '" . $inprofile->id . "' AND `date` > date('$date') GROUP BY DATE(`images`.`date`) ORDER BY `date` DESC");
			$pages = $db->get_results("SELECT COUNT(*) as `count`, DATE(`date`) as `date` FROM `pages` WHERE `author` = '" . $inprofile->id . "' AND `date` > date('$date') GROUP BY DATE(`pages`.`date`) ORDER BY `date` DESC LIMIT 365");
			$mbs = $db->get_results("SELECT COUNT(*) as `count`, DATE(`date`) as `date` FROM `miniblog` WHERE `author` = '" . $inprofile->id . "' AND `miniblog`.`removed` = '0' AND `date` > date('$date') GROUP BY DATE(`miniblog`.`date`) ORDER BY `date` DESC");
			$comments = $db->get_results("SELECT COUNT(*) as `count`, DATE(`date`) as `date` FROM `comments` WHERE `author` = '" . $inprofile->id . "' AND `comments`.`removed` = '0' AND `date` > date('$date') GROUP BY DATE(`comments`.`date`) ORDER BY `date` DESC");

			$values = [];
			foreach ($mbs as $mb) {
				$values[$mb->date] = $mb->count;
			}
			foreach ($comments as $comments) {
				if (!empty($values[$comments->date])) {
				    $values[$comments->date] += $comments->count;
				} else {
				    $values[$comments->date] = $comments->count;
				}
			}
			foreach ($pages as $page) {
				if (!empty($values[$page->date])) {
				    $values[$page->date] += $page->count;
				} else {
				    $values[$page->date] = $page->count;
				}
			}
			foreach ($images as $image) {
				if (!empty($values[$image->date])) {
				    $values[$image->date] += $image->count;
				} else {
				    $values[$image->date] = $image->count;
				}
			}

			$data = [];
			//$max = 0;
			for ($i = 0; $i <= 374; $i++) {
				$key = date('Y-m-d', strtotime('-' . $i . ' days'));
				if (!empty($values[$key])) {
				    $data[$key] = $values[$key];
				    /* if($values[$key] > $max) {
				      $max = $values[$key];
				      } */
				} else {
				    $data[$key] = 0;
				}
			}

			$avg = array_average_nonzero($data);
			$max = $avg * 2.3;

			$values = array_reverse($data);

			$i = 1;
			foreach ($values as $key => $val) {

				if ($key > $date) {

				    if ($i == 1) {
				        $tpl->newBlock('week');
				    }

				    if ($i == 7) {
				        $i = 1;
				    } else {
				        $i++;
				    }

				    $tpl->newBlock('day');

				    $percent = ($max > 0) ? (int) (100 / $max * $val) : 0;
				    if ($percent > 100) {
				        $percent = 100;
				    }
                    $tpl->assign([
                        'date' => date('Y.m.d', strtotime($key)),
                        'count' => $val,
                        'percent' => $percent,
                        'decimal' => ($max > 0) ? round((100 / $max * $val / 100), 2) : 0,
                    ]);
				}
			}



		}

		$articles = $db->get_results("SELECT `title`,`strid` FROM `pages` WHERE `author` = '$inprofile->id' AND `category` != '83' AND `lang` = '$lang' ORDER BY `date` DESC LIMIT 10");
		if ($articles) {
			$tpl->newBlock('user-profile-lastpage');
			foreach ($articles as $article) {
				$tpl->newBlock('user-profile-lastpage-node');
				$tpl->assign([
					'node-url' => '/read/' . $article->strid,
					'lastpage-title' => textlimit($article->title, 42, '..')
				]);
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
				$tpl->assign([
					'node-url' => '/read/' . $article->strid,
					'bookmark-title' => textlimit($article->title, 42, '..')
				]);
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
				$tpl->assign([
					'id' => $view->viewer,
					'date' => date('d.m.Y. H:i', $view->time),
					'nick' => h($view->nick),
					'avatar' => $avatar
				]);
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

		$private = '';
		if (!$auth->ok) {
			$private = ' AND `private` = 0';
		}

		$actions = $db->get_results("SELECT * FROM `userlogs` WHERE `user` = '$inprofile->id' AND `lang` = '$lang' ".$private." ORDER BY `time` DESC, `id` DESC LIMIT $skip,$end");
		if ($actions) {
			$out .= '<ul class="user-actions" id="profile-user-actions">';
			foreach ($actions as $action) {
				if (!$action->avatar) {
					$action->avatar = get_avatar($inprofile, 's');
				}
				if (!$action->avatar || $action->avatar === '/dati/bildes/useravatar/') {
					$action->avatar = '//img.exs.lv/userpic/small/none.png';
				}
				if (substr($action->avatar, 0, 22) == '/dati/bildes/topic-av/') {
					$action->avatar = '//exs.lv' . $action->avatar;
				}
				if (substr($action->avatar, 0, 13) == '/dati/bildes/' && !file_exists('.'.$action->avatar)) {
					$action->avatar = '//img.exs.lv/userpic/small/none.png';
				}
				if (substr($action->avatar, 0, 8) == '/bildes/') {
					$action->avatar = $img_server . $action->avatar;
				}
				$out .= '<li><img class="av" src="' . str_replace(['http://img.exs.lv', 'http://exs.lv'], ['//img.exs.lv', '//exs.lv'], $action->avatar) . '" alt="" />';
				$out .= '<span class="post-time">' . time_ago($action->time) . '</span>' . $action->action . '</li>';
			}
			$out .= '</ul>';

		} elseif($lang != 1 && $lang != $inprofile->source_site) {
			//liek meklētājiem neindeksēt profilus apakšprojektos, kur tie nav reģistrēti un neko nav darījuši
			$robotstag[] = 'noindex';
		}

		$total = $db->get_var("SELECT count(*) FROM `userlogs` WHERE `user` = '$inprofile->id' AND `lang` = '$lang' ".$private." LIMIT 60");
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
				$pager_next = '<a class="pager-next next" title="Iepriekšējā lapa" href="/user/' . $inprofile->id . '/?actions=' . $iepriekseja / $end . '">&laquo;</a>';
			} else {
				$pager_next = '';
			}
			$pager_prev = '';
			if ($total > $skip + $end) {
				$pager_prev = '<span>-</span> <a class="pager-prev prev" title="Nākamā lapa" href="/user/' . $inprofile->id . '/?actions=' . ($skip + $end) / $end . '">&raquo;</a>';
			}
			$startnext = 0;
			$page_number = 0;
			$pager_numeric = '';
			while ($total - $startnext > 0) {
				$page_number++;
				$class = ' class="page-numbers"';
				if ($skip == $startnext) {
					$class = ' class="page-numbers selected"';
				}
				$pager_numeric .= '<span>-</span> <a href="/user/' . $inprofile->id . '/?actions=' . $startnext / $end . '"' . $class . '>' . $page_number . '</a> ';
				$startnext = $startnext + $end;
			}
			$out .= '<p class="core-pager ajax-pager">' . $pager_next . ' ' . $pager_numeric . ' ' . $pager_prev . '</p>';
		}

		if (isset($_GET['_']) && isset($_GET['actions'])) {
			die($out);
		}

		$tpl->assign([
			'out' => $out
		]);

		if ($lang == 1) {
			$g_owners = $db->get_results("SELECT title,id FROM clans WHERE owner = '$inprofile->id' ORDER BY title ASC");
			$g_members = $db->get_results("SELECT `clans_members`.`clan` AS `clan`,`clans_members`.`moderator` AS `moderator`,`clans`.`title` AS `title` FROM `clans_members`,`clans` WHERE `clans_members`.`user` = '$inprofile->id' AND `clans_members`.`approve` = '1' AND `clans`.`id` = `clans_members`.`clan` ORDER BY `clans_members`.`moderator` DESC, `clans_members`.`date_added` ASC");
			if ($g_owners or $g_members) {
				$tpl->newBlock('grouplist');
				if ($g_owners) {
					foreach ($g_owners as $g_owner) {
						$tpl->newBlock('g-admin');
						$tpl->assign([
							'group-id' => $g_owner->id,
							'group-title' => $g_owner->title,
						]);
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
						$tpl->assign([
							'group-id' => $g_member->clan,
							'group-class' => $class,
							'group-title' => $g_member->title,
						]);
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
					$tpl->assign([
						'url' => '/read/' . $comment->strid . '#c' . $comment->id,
						'comments-text' => textlimit($comment->text, 42, '..')
					]);
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
					$tpl->assign([
						'comments-image' => $comment->bid,
						'comments-uid' => $comment->uid,
						'comments-id' => $comment->id,
						'comments-text' => textlimit($comment->text, 42, '..')
					]);
				}
			}
		}

		if ($auth->ok && $auth->id == $inprofile->id) {
			$tpl->assignGlobal('profile-sel', ' class="selected"');
			$page_title = 'Tavs profils';
		}
	}
} else {

	$robotstag[] = 'noindex';
	$robotstag[] = 'nofollow';

	$tpl->newBlock('error-nouser');
	$page_title = 'Kļūda: profils nav atrasts!';
}

$pagepath = '';

