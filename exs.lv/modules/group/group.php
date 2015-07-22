<?php

/**
 * Konkrētās grupas skats
 */
/**
 * Aptuvens saturs pa galvenajiem blokiem:
 * (CTRL + F -> <nr>.)
 *
 * 1. grupas biedru cilne
 * 2. grupas miniblogu cilne
 * 3. specifiska grupas cilne
 * 4. grupas opcijas
 * 5. grupas meklēšanas cilne
 * 6. grupas sākumlapas cilne
 */
if (!empty($category->content)) {
	$_GET['var5'] = esr($_GET['var4']);
	$_GET['var4'] = esr($_GET['var3']);
	$_GET['var3'] = esr($_GET['var2']);
	$_GET['var2'] = esr($_GET['var1']);
	$_GET['var1'] = $category->content;
	$group_link = '/' . $category->textid;
} else {
	$group_link = '/group/' . intval($_GET['var1']);
}

$tpl->assignGlobal('group-link', $group_link);

// atvērta neeksistējoša grupa
if (!isset($_GET['var1']) || !$group = $db->get_row("SELECT * FROM `clans` WHERE `id` = '" . intval($_GET['var1']) . "'")) {
	redirect('/grupas');
}

if (!empty($group->strid) && $group->strid != $category->textid) {
	redirect(str_replace('/group/' . $group->id, '/' . $group->strid, $_SERVER['REQUEST_URI']), true);
}

set_action('grupas');

if ($group->id == 65) {
	redirect('https://lol.exs.lv/', true);
}
// redirekts uz pareizo apakšprojektu, ja grupa pārvietota
else if ($group->lang != $lang) {
	redirect('https://' . $config_domains[$group->lang]['domain'] . $_SERVER['REQUEST_URI'], true);
}

// grupas avatars
$group->av_alt = 1;
if (empty($group->avatar)) {
	$group->avatar = 'none.png';
}

// reklāmas
if (!empty($group->disable_adsense)) {
	$disable_adsense = true;
}

/* top ad in group */
if (!$auth->mobile) {
	if (!empty($group->top_ad)) {
		$tpl->assignGlobal('top-group-ad', $group->top_ad);
	} elseif (empty($disable_adsense)) {
		if (file_exists(CORE_PATH . '/tmpl/ads/' . $lang . '_728_adsense.tpl')) {
			$tpl->assignGlobal('top-group-ad', file_get_contents(CORE_PATH . '/tmpl/ads/' . $lang . '_728_adsense.tpl'));
		}
	} else {
		if (file_exists(CORE_PATH . '/tmpl/ads/' . $lang . '_728_adsense.tpl')) {
			$tpl->assignGlobal('top-group-ad', file_get_contents(CORE_PATH . '/tmpl/ads/' . $lang . '_728.tpl'));
		}
	}
}

/* grupas administratora vai moderatora pieeja */
$is_mod = false;
$is_admin = false;
$is_member = false;
if ($auth->ok && ($auth->id == $group->owner || $auth->level == 1)) {
	$is_admin = true;
} elseif ($auth->ok && $db->get_var("SELECT count(*) FROM `clans_members` WHERE `clan` = '$group->id' AND `user` = '$auth->id' AND `approve` = 1 AND `moderator` = 1")) {
	$is_mod = true;
} elseif ($db->get_var("SELECT count(*) FROM clans_members WHERE clan = '$group->id' AND user = '$auth->id' AND approve = '1'")) {
	$is_member = true;
}

/* pending member count */
if (($is_admin || $is_mod) && !$group->public && $pending_count = $db->get_var("SELECT count(*) FROM `clans_members` WHERE `clan` = $group->id AND `approve` = 0")) {
	$tpl->assignGlobal('pending_count', '&nbsp;(<span class="red">' . $pending_count . '</span>)');
}

$ingroup = $group;
$group_tabs = $db->get_results("SELECT `id`,`title`,`slug` FROM `clans_tabs` WHERE `clan_id` = '$group->id'");

$pagepath = '<a href="/grupas">Domubiedru grupas</a> / ' . $group->title;

$tpl->newBlock('group-menu');
$tpl->assign(array(
	'group-id' => $group->id,
	'group-title' => $group->title
));

// pievienos grupas cilnes un iekrāsos atvērto
if ($group_tabs) {
	foreach ($group_tabs as $tab) {
		$sel = '';
		if (isset($_GET['var2']) && $_GET['var2'] == 'tab' && isset($_GET['var3']) && $_GET['var3'] == $tab->slug) {
			$sel = 'active';
		}
		$tpl->newBlock('group-menu-add');
		$tpl->assign(array(
			'title' => $tab->title,
			'sel' => $sel,
			'url' => $tab->slug,
			'group-id' => $group->id,
		));
	}
}

// grupas administratoru fīčas
if ($is_admin) {
	$tpl->newBlock('group-menu-options');
	$tpl->assign(array(
		'group-id' => $group->id
	));
}

// grupas apraksta rediģēšana
if (isset($_GET['var2']) && $_GET['var2'] == 'edit' && ($is_admin || $is_mod || im_mod())) {

	if (isset($_POST['edit-group-text'])) {
		$edit_text = htmlpost2db($_POST['edit-group-text']);

		if (isset($_FILES['edit-avatar'])) {
			$group->avatar = upload_user_avatar($_FILES['edit-avatar'], $group->avatar, 'group_' . time() . '_' . $group->id);
		}

		if (im_mod() && isset($_POST['edit-category_id']) && $_POST['edit-category_id'] > 0) {
			$group->category_id = intval($_POST['edit-category_id']);
		}

		if ($auth->level == 1) {
			$group->interest_id = intval($_POST['edit-interest_id']);
		}

		$db->update('clans', $group->id, array('category_id' => $group->category_id, 'interest_id' => $group->interest_id, 'avatar' => $group->avatar, 'text' => $edit_text, 'date_modified' => time()));

		$auth->log('Laboja grupas aprakstu', 'clans', $group->id);
		redirect($group_link);
	}

	$tpl->assignGlobal('active-tab-info', 'active');
	$tpl->newBlock('group-edit');
	$tpl->assign(array(
		'group-text' => h($group->text),
		'group-title' => $group->title,
	));

	// grupas kategorijas mainīšanas forma
	if (im_mod()) {
		$tpl->newBlock('group-edit-category');
		$fcategorys = $db->get_results("SELECT `id`,`title` FROM `clans_categories` ORDER BY `importance` DESC");
		if ($fcategorys) {
			foreach ($fcategorys as $fcategory) {
				$tpl->newBlock('select-category');
				$sel = '';
				if ($group->category_id == $fcategory->id) {
					$sel = ' selected="selected"';
				}
				$tpl->assign(array(
					'title' => $fcategory->title,
					'sel' => $sel,
					'id' => $fcategory->id,
				));
			}
		}
	}

	// ...
	if ($auth->level == 1) {
		$tpl->newBlock('group-edit-interest');
		$fcategorys = $db->get_results("SELECT `id`,`title` FROM `interests` ORDER BY `id` ASC");
		if ($fcategorys) {
			foreach ($fcategorys as $fcategory) {
				$tpl->newBlock('select-interest');
				if ($group->interest_id == $fcategory->id) {
					$sel = ' selected="selected"';
				} else {
					$sel = '';
				}
				$tpl->assign(array(
					'title' => $fcategory->title,
					'sel' => $sel,
					'id' => $fcategory->id,
				));
			}
		}
	}

	$tpl->newBlock('tinymce-enabled');
	$page_title = $group->title . ' - labo grupu';
}



/**
 * 1. GRUPAS BIEDRU CILNE UN TĀS IESPĒJAS
 */ elseif (isset($_GET['var2']) && $_GET['var2'] == 'members') {

	$robotstag[] = 'noindex';

	$tpl->assignGlobal('active-tab-members', 'active');
	$tpl->newBlock('group-members');

	// modiem / adminiem atlasa biedru pieteikumus
	if ($is_admin || $is_mod) {
		$pendings = $db->get_results("SELECT * FROM `clans_members` WHERE `clan` = '$group->id' AND `approve` = '0' ORDER BY `id` DESC");
		if ($pendings) {
			$tpl->newBlock('pending');
			foreach ($pendings as $pending) {
				$p_user = get_user($pending->user);
				$avatar = get_avatar($p_user, 's');
				if ($pending->user) {
					$tpl->newBlock('pending-node');
					$tpl->assign(array(
						'group-id' => $group->id,
						'pending-id' => $pending->id,
						'pending-uid' => $p_user->id,
						'pending-date' => date('Y-m-d', $pending->date_added),
						'avatar' => $avatar,
						'pending-nick' => usercolor($p_user->nick, $p_user->level, false, $p_user->id),
						'deny_token' => make_token('deny'),
						'confirm_token' => make_token('confirm')
					));
				}
			}
		}
	}

	// pirms biedru saraksta pievieno grupas administratoru
	$tpl->newBlock('members');

	$m_owner = get_user($group->owner);

	$avatar = get_avatar($m_owner);

	$tpl->newBlock('members-node');
	$tpl->assign(array(
		'group-id' => $group->id,
		'member-class' => 'owner',
		'member-id' => $m_owner->id,
		'member-nick' => usercolor($m_owner->nick, $m_owner->level, false, $m_owner->id),
		'avatar' => $avatar
	));

	$skip = 0;
	if (isset($_GET['skip'])) {
		$skip = (int) $_GET['skip'];
	}
	$end = 59;

	// atlasa grupas biedrus
	$members = $db->get_results("SELECT * FROM `clans_members` WHERE `clan` = '$group->id' AND `approve` = '1' ORDER BY `moderator` DESC, `date_added` ASC LIMIT $skip,$end");
	if ($members) {

		// pievieno biedrus sarakstam
		foreach ($members as $member) {
			$m_user = get_user($member->user);

			//clean up deleted users
			if (empty($m_user)) {
				$db->query("DELETE FROM `clans_members` WHERE `id` = '$member->id' LIMIT 1");
				$db->query("UPDATE `clans` SET `members` = `members`-1 WHERE `id` = '$group->id' LIMIT 1");
				continue;
			}

			$avatar = get_avatar($m_user);

			$mclas = 'member';
			if ($member->moderator) {
				$mclas = 'mod';
			}

			$tpl->newBlock('members-node');
			$tpl->assign(array(
				'group-id' => $group->id,
				'member-class' => $mclas,
				'member-id' => $m_user->id,
				'member-nick' => usercolor($m_user->nick, $m_user->level, false, $m_user->id),
				'avatar' => $avatar
			));

			//delete member from group
			if ($is_admin || $is_mod) {
				$tpl->newBlock('member-delete');
				$tpl->assign(array(
					'group-id' => $group->id,
					'member-id' => $m_user->id,
					'token' => make_token('drop')
				));
			}

			//set moderator
			if ($is_admin) {
				if ($member->moderator) {
					$tpl->newBlock('member-unmoderator');
				} else {
					$tpl->newBlock('member-moderator');
				}
				$tpl->assign(array(
					'group-id' => $group->id,
					'member-id' => $m_user->id,
					'token' => make_token('mod')
				));
			}
		}
	}

	// biedru saraksta lappuses
	$pager = pager($group->members, $skip, $end, $group_link . '/members/?skip=');
	$tpl->assignGlobal(array(
		'pager-next' => $pager['next'],
		'pager-prev' => $pager['prev'],
		'pager-numeric' => $pager['pages']
	));

	$page_title = $group->title . ' - biedri';
}

// grupas biedra dzēšana
elseif (isset($_GET['var2']) && $_GET['var2'] == 'drop' && ($is_admin || $is_mod) && check_token('drop', $_GET['token'])) {
	$drop = (int) $_GET['var3'];
	$db->query("DELETE FROM `clans_members` WHERE `clan` = '$group->id' AND `user` = '$drop'");
	update_members($group->id);
	$auth->log('Izmeta biedru #' . $drop, 'clans', $group->id);
	redirect($group_link . '/members');
}
// uzliek biedram moderatora statusu
elseif (isset($_GET['var2']) && $_GET['var2'] == 'setmod' && $is_admin && check_token('mod', $_GET['token'])) {
	$uid = (int) $_GET['var3'];
	$db->query("UPDATE clans_members SET moderator = '1' WHERE clan = '$group->id' AND user = '$uid'");
	$auth->log('Uzlika par moderatoru #' . $uid, 'clans', $group->id);
	redirect($group_link . '/members');
}
// noņem biedram moderatora statusu
elseif (isset($_GET['var2']) && $_GET['var2'] == 'unsetmod' && $is_admin && check_token('mod', $_GET['token'])) {
	$uid = (int) $_GET['var3'];
	$db->query("UPDATE clans_members SET moderator = '0' WHERE clan = '$group->id' AND user = '$uid'");
	$auth->log('Noņēma moderatora tiesības #' . $uid, 'clans', $group->id);
	redirect($group_link . '/members');
}
/* confirm pending member */ elseif (isset($_GET['var2']) && $_GET['var2'] == 'confirm' && ($is_admin || $is_mod) && check_token('confirm', $_GET['token'])) {

	$confirm = (int) $_GET['var3'];

	$db->query("UPDATE clans_members SET approve = '1' WHERE clan = '$group->id' AND id = '$confirm'");
	$auser = $db->get_var("SELECT user FROM clans_members WHERE clan = '$group->id' AND id = '$confirm'");
	update_members($group->id);

	userlog($auser, 'Tika apstiprināts grupā &quot;<a href="' . $group_link . '">' . $group->title . '</a>&quot;', get_avatar($group, 's', true), 'gsign' . $group->id);

	$auth->log('Apstiprināja grupā biedru #' . $auser, 'clans', $group->id);
	redirect($group_link . '/members');
}
/* deny pendig member, remove pending status */ elseif (isset($_GET['var2']) && $_GET['var2'] == 'deny' && ($is_admin || $is_mod) && check_token('deny', $_GET['token'])) {

	$confirm = (int) $_GET['var3'];

	$auser = $db->get_var("SELECT user FROM clans_members WHERE clan = '$group->id' AND id = '$confirm'");
	$db->query("DELETE FROM `clans_members` WHERE clan = '$group->id' AND id = '$confirm' LIMIT 1");

	$auth->log('Noraidīja iestāšanās pieteikumu lietotājam #' . $auser, 'clans', $group->id);
	redirect($group_link . '/members');
}
// lietotājs piesakās būt par grupas biedru
elseif (isset($_GET['var2']) && $_GET['var2'] == 'apply' && $group->paid == 0 && $auth->ok && check_token('apply', $_GET['token'])) {
	if (!$db->get_var("SELECT count(*) FROM clans_members WHERE clan = '$group->id' AND user = '$auth->id'") && $auth->id != $group->owner) {
		$db->query("INSERT INTO clans_members (user,clan,approve,date_added) VALUES ('$auth->id','$group->id','$group->auto_approve','" . time() . "')");
		update_members($group->id);

		push('Pieteicās grupā &quot;<a href="' . $group_link . '">' . $group->title . '</a>&quot;', get_avatar($group, 's', true), 'gsign' . $group->id);
		notify($group->owner, 4, $group->id, $group_link . '/members', $group->title);
		if ($group->id == 53 || $group->id == 89) {
			$db->query("UPDATE `users` SET `show_code` = 1 WHERE `id` = '$auth->id'");
		}
		redirect($group_link);
	}
}
// lietotājs piesakās par grupas biedru maksas grupā
elseif (isset($_GET['var2']) && $_GET['var2'] == 'submitpay' && $auth->ok && $group->paid) {
	if (!$db->get_var("SELECT count(*) FROM clans_members WHERE clan = '$group->id' AND user = '$auth->id'") && $auth->id != $group->owner) {

		$credit = $db->get_var("SELECT `credit` FROM `users` WHERE `id` = '$auth->id'");

		if ($credit < 3) {
			set_flash('Nepietiek exs.lv kredīta!', 'error');
			redirect($group_link);
		}
		$db->query("UPDATE users SET credit = credit-'3' WHERE id = ('" . $auth->id . "')");
		$db->query("INSERT INTO clans_paid (clan_id,user_id,time) VALUES ('$group->id','$auth->id','" . time() . "')");
		$db->query("INSERT INTO clans_members (user,clan,approve,date_added) VALUES ('$auth->id','$group->id','1','" . time() . "')");
		update_members($group->id);
		push('Pieteicās grupā &quot;<a href="' . $group_link . '">' . $group->title . '</a>&quot;', get_avatar($group, 's', true));
		redirect($group_link);
	}
}
// iestāšanās maksas grupā
elseif (isset($_GET['var2']) && $_GET['var2'] == 'pay' && $auth->ok && $group->paid && check_token('apply', $_GET['token'])) {
	if (!$db->get_var("SELECT count(*) FROM clans_members WHERE clan = '$group->id' AND user = '$auth->id'") && $auth->id != $group->owner) {

		$tpl->assignGlobal('active-tab-info', 'active');
		$tpl->newBlock('group-pay');
		$page_title = $group->title . ' - iestāties grupā';

		$credit = $db->get_var("SELECT credit FROM users WHERE id = '$auth->id'");
		$owner = get_user($group->owner);
		$tpl->assign(array(
			'user-credit' => $credit,
			'group-text' => add_smile($group->text),
			'group-posts' => $group->posts,
			'group-members' => $group->members + 1,
			'group-admin' => $owner->nick
		));

		if ($credit >= 3) {
			$tpl->assign('pay', '<p><a href="' . $group_link . '/submitpay">Pieteikties grupā</a></p>');
		}

		$members = $db->get_col("SELECT user, moderator FROM clans_members WHERE clan = '$group->id' AND approve = '1' ORDER BY date_added DESC LIMIT 16");
		if (count($members) < 16) {
			$members[] = $group->owner;
		}
		if ($members) {
			$tpl->newBlock('nmembers-pay');
			foreach ($members as $member) {
				$m_user = get_user($member);

				$avatar = get_avatar($m_user, 's');

				if ($member->moderator) {
					$mclas = 'mod';
				} else {
					$mclas = 'member';
				}

				$tpl->newBlock('nmembers-pay-node');
				$tpl->assign(array(
					'member-id' => $m_user->id,
					'member-nick' => h($m_user->nick),
					'avatar' => $avatar,
				));
			}
		}
	}
}
// izstāšanās no grupas
elseif (isset($_GET['var2']) && $_GET['var2'] == 'cancel' && check_token('cancel', $_GET['token'])) {
	if ($db->query("DELETE FROM clans_members WHERE clan = '$group->id' AND user = '$auth->id'")) {
		update_members($group->id);
		push('Izstājās no grupas &quot;<a href="' . $group_link . '">' . $group->title . '</a>&quot;', get_avatar($group, 's', true));
	}
	redirect($group_link);
}



/**
 * 2. GRUPAS MINIBLOGU CILNE UN TĀS IESPĒJAS
 */ elseif (isset($_GET['var2']) && $_GET['var2'] == 'community' && !empty($group->id) || isset($_GET['var2']) && $_GET['var2'] == 'forum' && !empty($group->id)) {

	// iekrāso atvērto cilni
	$tpl->assignGlobal('active-tab-community', 'active');
	$tpl->newBlock('group-community');
	$page_title = $group->title . ' - forums';

	// ...
	if (isset($_GET['var3']) && !empty($_GET['var3'])) {
		$_GET['single'] = base_convert($_GET['var3'], 36, 10);
	}

	// miniblogu saraksts aplūkojams tikai grupas biedriem,
	// ja vien grupa nav publiska
	if ($group->public || ($is_mod || $is_admin || $is_member)) {

		// mb sarakstam ir lappuses,
		// tāpēc nepieciešams datubāzes pieprasījumiem
		$skip = 0;
		if (isset($_GET['skip'])) {
			$skip = (int) $_GET['skip'];
		}
		$end = 6;

		// jauna minibloga pievienošana
		// (iespējama vien nearhivētām grupām)
		if ($auth->ok && isset($_POST['newminiblog']) && !empty($_POST['newminiblog']) && !$group->archived) {

			$body = post2db($_POST['newminiblog']);

			// ja nav pārkāpts flood limits, pievieno miniblogu un visādas notifikācijas
			if (!isset($_SESSION['antiflood']) or $_SESSION['antiflood'] < time() - 8) {
				$_SESSION["antiflood"] = time();

				$ins = post_mb(array(
					'groupid' => $group->id,
					'text' => $body
				));

				push('Izveidoja tematu grupā <a href="' . $group_link . '/forum/' . base_convert($ins, 10, 36) . '">' . $group->title . '</a>', get_avatar($group, 's', true), 'g' . $ins);

				update_post_count($group->id);

				$topic = $db->get_row("SELECT * FROM `miniblog` WHERE `id` = '$ins'");
				$topic->text = mention($topic->text, $group_link . '/forum/' . base_convert($ins, 10, 36), 'group', $topic->id);
				$db->query("UPDATE `miniblog` SET `text` = '" . sanitize($topic->text) . "' WHERE id = '$topic->id'");

				redirect($group_link . '/forum/' . base_convert($ins, 10, 36));
			} else {
				set_flash('Izskatās pēc flooda. Pagaidi 10 sekundes, pirms pievieno jaunu tēmu!');
			}
		}

		// komentāra pievienošana, ja vien grupa nav arhivēta
		if ($auth->ok && isset($_POST['responseminiblog']) && !empty($_POST['responseminiblog']) && !$group->archived) {

			$to = (int) $_POST['response-to'];

			$mlevel = 3;

			// security pārbaudes...
			if (get_mb_level($to) > $mlevel && $auth->level != 1) {
				die('Too deep ;(');
			}
			if (!isset($_POST['token']) or $_POST['token'] != md5('mb' . intval($_GET['single']) . $remote_salt . $auth->nick)) {
				set_flash('Kļūdains pieprasījums! Hacking around?');
				redirect();
			}

			// nosaka parent miniblogus
			$reply_to = $db->get_row("SELECT * FROM miniblog WHERE id = '$to'");

			$reply_to_id = 0;
			if ($reply_to->parent != 0) {
				$mainid = $reply_to->parent;
				$reply_to_id = $reply_to->id;
			} else {
				$mainid = $to;
			}

			// eskeipos un embeddos dažādas specifiskas fīčas
			$body = post2db($_POST['responseminiblog']);

			$check = $db->get_var("SELECT author FROM miniblog WHERE id = '" . $mainid . "' AND removed = '0' AND groupid = '$group->id'");
			if (!$check) {
				die("Kļūdains parent id! Iespējams kamēr rakstīji komentāru, kāds izdzēsa tēmu.");
			}
			$check2 = $db->get_var("SELECT author FROM miniblog WHERE id = '" . $mainid . "' AND closed = '1'");
			if ($check2) {
				die("Tēma ir slēgta.");
			}

			// pievieno komentāru, ja nav pārsniegts flood limits
			if ($mainid) {
				if (!isset($_SESSION['antiflood']) or $_SESSION['antiflood'] < time() - 5) {
					$_SESSION["antiflood"] = time();

					$newid = post_mb(array(
						'groupid' => $group->id,
						'text' => $body,
						'parent' => $mainid,
						'reply_to' => $reply_to_id
					));

					$body = $db->get_var("SELECT `text` FROM `miniblog` WHERE `id` = '$mainid'");

					$title = mb_get_title(stripslashes($body));
					$url = $group_link . '/forum/' . base_convert($mainid, 10, 36);

					//ja grupas ieraksti ir slēpti, ievieto userlogā tikai nosaukumu
					if (!$group->hide_intro) {
						push('Atbildēja <a href="' . $url . '#m' . $newid . '">' . $group->title . ' grupā &quot;' . textlimit($title, 32, '...') . '&quot;</a>', get_avatar($group, 's', true), 'g-' . $mainid);
					} else {
						push('Atbildēja ' . $group->title . ' grupā', get_avatar($group, 's', true), 'g-' . $mainid);
					}

					// mentions & notifikācijas
					$newpost = $db->get_row("SELECT * FROM `miniblog` WHERE id = '$newid'");
					$newpost->text = mention($newpost->text, $url, 'group', $mainid);
					$db->query("UPDATE `miniblog` SET `text` = '" . sanitize($newpost->text) . "' WHERE id = '$newpost->id'");

					notify($check, 8, $mainid, $url, textlimit($group->title . ' - ' . $title, 64));
					if (!empty($reply_to_id) && $check != $reply_to->author) {
						notify($reply_to->author, 8, $mainid, $url, textlimit($group->title . ' - ' . $title, 64));
					}

					update_post_count($group->id);

					/* auto close after 500 posts */
					$topic = $db->get_row("SELECT * FROM `miniblog` WHERE `id` = '$mainid'");
					if ($topic->posts >= 500) {
						$body = sanitize($topic->text . '<p>(<a href="' . $url . '">Tēmas</a> turpinājums)</p>');
						$db->query("INSERT INTO miniblog (`groupid`, `author`,`date`,`text`,`ip`,`bump`,`lang`) VALUES ('$group->id', '$topic->author',NOW(),'$body','$topic->ip','" . time() . "','$topic->lang')");
						$newurl = $group_link . '/forum/' . base_convert($db->insert_id, 10, 36);
						$reason = sanitize('Sasniegts 500 atbilžu limits, slēgts automātiski. Tēmas tupinājums: <a href="' . $newurl . '">//' . $_SERVER['HTTP_HOST'] . $newurl . '</a>.');
						$db->query("UPDATE `miniblog` SET `closed` = '1', `close_reason` = '$reason', `closed_by` = '17077' WHERE `id` = '$mainid'");
						redirect($newurl);
					}


					if (isset($_GET['postcomment'])) {
						die('ok');
					}
					redirect($url);
				} else {
					die('err: flood');
				}
			}
			if (isset($_GET['postcomment'])) {
				die('err: wrong params');
			}
		}

		// minibloga slēgšana
		if ((im_mod() || $is_mod || $is_admin) && isset($_GET['var4']) && $_GET['var4'] === 'close' && isset($_GET['single'])) {
			$sid = (int) $_GET['single'];
			if (isset($_POST['reason']) && !empty($_POST['reason'])) {
				$reason = post2db($_POST['reason']);
				$db->query("UPDATE `miniblog` SET `closed` = '1', `close_reason` = '$reason', `closed_by` = '$auth->id' WHERE `id` = '$sid' AND `lang` = '$lang' AND `groupid` = '$group->id'");
				$auth->log('Aizslēdza miniblogu (' . strip_tags($reason) . ')', 'miniblog', $sid);
				redirect($group_link . '/forum/' . base_convert($sid, 10, 36));
			} else {
				$tpl->newBlock('close-reason');
			}
		}

		// minibloga atslēgšana
		if ((im_mod() || $is_mod || $is_admin) && isset($_GET['var4']) && $_GET['var4'] === 'open' && isset($_GET['single'])) {
			$sid = (int) $_GET['single'];
			$db->query("UPDATE `miniblog` SET `closed` = '0', `closed_by` = '0' WHERE `id` = '$sid' AND `groupid` = '$group->id'");
			redirect($group_link . '/forum/' . base_convert($sid, 10, 36));
		}

		// minibloga lasīšanas skats
		$tpl->newBlock('user-miniblog');

		// jauna minibloga pievienošanas forma atkarībā no tā,
		// vai grupa ir arhivēta
		if ($auth->ok && !isset($_GET['single']) && !$group->archived) {
			$tpl->newBlock('user-miniblog-form');
		} elseif ($group->archived) {
			$tpl->newBlock('archived');
		}

		// grupā esošo miniblogu saraksts
		if (!isset($_GET['single'])) {
			$records = $db->get_results("SELECT * FROM miniblog WHERE groupid = ('" . $group->id . "') AND removed = '0' AND parent = '0' ORDER BY bump DESC LIMIT $skip,$end");
		}
		// atvērtā minibloga dati
		else {
			$single = (int) $_GET['single'];
			$records = $db->get_results("SELECT * FROM miniblog WHERE id = ('$single') AND groupid = ('" . $group->id . "') AND removed = '0' AND parent = '0' LIMIT 1");
		}

		// izdrukā visus atlasītos ierakstus
		if ($records) {

			$tpl->newBlock('user-miniblog-list');

			foreach ($records as $record) {
				$tpl->newBlock('user-miniblog-list-node');

				$title = mb_get_title($record->text);

				$user = get_user($record->author);

				$url = $group_link . '/forum/' . base_convert($record->id, 10, 36);

				if (isset($_GET['single'])) {
					$page_title = textlimit(youtube_title($record->text), 64, '...') . ' - forums';
				}

				// twitter ierakstu fīčas
				$append = '';
				if ($record->twitterid && $record->twitteruser != 'rssbot') {
					$append .= '<p><a title="' . $record->twitteruser . ' iekš Twitter" href="https://twitter.com/' . $record->twitteruser . '/status/' . $record->twitterid . '" rel="nofollow" class="mb-api-twitter">@' . $record->twitteruser . '</a></p>';
				}

				// lietotāja apbalvojumu ikonas
				$add_deco = '';
				if (!empty($user->decos)) {
					$decos = unserialize($user->decos);
					if (!empty($decos)) {
						$di = 0;
						foreach ($decos as $deco) {
							$add_deco .= '<img src="' . $deco['icon'] . '" alt="' . $deco['title'] . '" title="' . $deco['title'] . '" class="user-deco deco-pos-' . $di . '" />';
							$di++;
						}
					}
				}

				// dzēstu lietotāju lietotājvārdi
				if (!$user->deleted) {
					$author = '<a href="/user/' . $user->id . '">' . usercolor($user->nick, $user->level, false, $user->id) . '</a>';
				} else {
					$author = '<em>dzēsts</em>';
				}

				// samazina attēlus
				if ($record->pic_heavy && stripos($record->text, 'src="http') !== false) {
					$record->text = resize_html_images($record->text);
				}

				$tpl->assign(array(
					'url' => $url,
					'text' => add_smile($record->text) . $append,
					'add_deco' => $add_deco,
					'date' => display_time(strtotime($record->date)),
					'date-title' => date('d.m.Y. H:i', strtotime($record->date)),
					'author' => $author,
					'author-id' => $record->author,
					'avatar' => get_avatar($user, 's'),
					'author-nick' => $user->nick,
					'id' => $record->id,
					'title' => $title
				));

				// atsevišķās grupās ierakstu vērtēšana ir atslēdzama
				if (empty($group->disable_vote)) {
					$tpl->assign(array(
						'rater' => mb_rater($record, $url)
					));
				}

				// atvērtos miniblogos rāda administratīvās iespējas un komentārus
				if (isset($_GET['single'])) {

					if ($auth->ok) {
						$tpl->newBlock('mb-reply-main');
					}

					if ((im_mod() || (!$record->closed && $auth->karma > 99 && $record->author == $auth->id)) && (strtotime($record->date) > time() - 1800) || $auth->level == 1 || $auth->id == 115) {
						$tpl->newBlock('mb-edit-main');
						$tpl->assign(array(
							'id' => $record->id,
						));
					}

					// linki ieraksta aizslēgšanai/atslēgšanai
					if (im_mod() || $is_mod || $is_admin) {
						if ($record->closed) {
							$tpl->newBlock('mb-edit-unclose');
						} else {
							$tpl->newBlock('mb-edit-close');
						}
						$tpl->assign('url', $url);
					}

					// lūdzu, neņem nost laika ierobežojumu :/
					if (im_mod() && strtotime($record->date) > time() - 86400) {
						$tpl->newBlock('mb-delete');
						$tpl->assign(array(
							'id' => $record->id,
							'token' => make_token('delmb')
						));
					}

					// podziņa parent mb pārkāpuma ziņošanai
					if ($auth->ok && !$auth->mobile && in_array($lang, array(1, 9))) {
						$tpl->newBlock('report-mb');
						$tpl->assign('id', $record->id);
					}

					$limit = '';
				} else {
					$limit = ' LIMIT 0,3';
				}

				// komentāri
				if ($record->posts) {

					$responses = $db->get_results("
						SELECT
							`miniblog`.`text` AS `text`,
							`miniblog`.`vote_value` AS `vote_value`,
							`miniblog`.`vote_users` AS `vote_users`,
							`miniblog`.`date` AS `date`,
							`miniblog`.`author` AS `author`,
							`miniblog`.`groupid` AS `groupid`,
							`miniblog`.`id` AS `id`,
							`miniblog`.`posts` AS `posts`,
							`miniblog`.`reply_to` AS `reply_to`,
							`miniblog`.`removed` AS `mb_removed`,
							`miniblog`.`hidden` AS `hidden`,
							`users`.`nick` AS `nick`,
							`users`.`decos` AS `decos`,
							`users`.`avatar` AS `avatar`,
							`users`.`av_alt` AS `av_alt`,
							`users`.`level` AS `level`,
							`users`.`deleted` AS `user_deleted`
						FROM
							miniblog,
							users
						WHERE
							`miniblog`.`parent` = '" . $record->id . "' AND
							`users`.`id` = `miniblog`.`author`
						ORDER BY
							`miniblog`.`id`
						ASC" . $limit
					);

					// komentāri tiks pievienoti rekursīvi
					if ($responses) {
						$json = array();
						foreach ($responses as $response) {
							$json[$response->reply_to][] = $response;
						}
						$mlevel = 5;
						$tpl->newBlock('miniblog-posts');
						$tpl->assign('mbout', mb_recursive($json, 0, 0, !isset($_GET['single']), $mlevel, $record->closed, $group->disable_vote, $record->pic_heavy));
					}
				}

				// minibloga atvēršanas iespēja
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

			// minibloga komentēšanas forma
			if (isset($_GET['single']) && $auth->ok && !$record->closed && !$group->archived) {
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
					'type' => 'miniblog',
					'lastid' => (int) $db->get_var("SELECT id FROM miniblog WHERE parent = '$record->id' ORDER BY id DESC LIMIT 1")
				));
			}
			// par slēgtiem komentāriem paziņo
			elseif ($record->closed) {
				$tpl->newBlock('user-miniblog-closed');
				if (!empty($record->close_reason)) {
					$tpl->assign('reason', add_smile($record->close_reason));
				}
				if (!empty($record->closed_by)) {
					$closer = get_user($record->closed_by);
					$tpl->assign('by', '<br />Aizslēdza: ' . usercolor($closer->nick, $closer->level, false, $record->closed_by));
				}
			}

			// grupas mb sarakstā rāda lappuses
			if (!isset($_GET['single'])) {

				$total = $db->get_var("SELECT count(*) FROM `miniblog` WHERE `groupid` = '" . $group->id . "' AND `removed` = '0' AND `parent` = '0'");
				$pager = pager($total, $skip, $end, $group_link . '/forum/?skip=');
				$tpl->newBlock('mb-pager');
				$tpl->assign(array(
					'pager-next' => $pager['next'],
					'pager-prev' => $pager['prev'],
					'pager-numeric' => $pager['pages']
				));
			}
		}

		// fiksē, cik daudz komentāru attiecīgajā grupā lietotājs ir jau lasījis
		if ($group->owner == $auth->id) {
			$db->query("UPDATE clans SET owner_seenposts = '$group->posts' WHERE owner = '$auth->id' AND id = '$group->id'");
		} else {
			$db->query("UPDATE clans_members SET seenposts = '$group->posts' WHERE user = '$auth->id' AND clan = '$group->id'");
		}
	}
	// viesiem nepubliskotas grupas nav skatāmas
	else {
		$tpl->newBlock('noguestacc');
		$tpl->assign(array(
			'group-id' => $group->id,
		));
		if (!$auth->ok) {
			$tpl->newBlock('noguestacc-login');
			$tpl->assign('xsrf', $auth->xsrf);
		}

		$robotstag[] = 'noindex';
	}
}



/**
 * 3. SPECIFISKA GRUPAS CILNE UN TĀS IESPĒJAS
 */ elseif (isset($_GET['var2']) && $_GET['var2'] == 'tab' && isset($_GET['var3'])) {
	$tab = mkslug($_GET['var3']);

	$tab = $db->get_row("SELECT * FROM clans_tabs WHERE slug = '$tab' AND clan_id = '$group->id'");
	$module_content = '';

	// iekļaus cilnes moduli, ja tāds cilnei ir norādīts
	if (!empty($tab->module)) {
		include(CORE_PATH . '/modules/groups/tabs/' . $tab->module . '.php');
	}
	if ($tab) {

		// cilnes satura rediģēšana
		if (isset($_GET['var4']) && $_GET['var4'] == 'edit' && ($is_admin || $is_mod)) {

			if (isset($_POST['tab-text'])) {
				$tab_text = htmlpost2db($_POST['tab-text']);
				$db->query("UPDATE clans_tabs SET `text` = '$tab_text' WHERE id = '$tab->id'");
				redirect($group_link . '/tab/' . $tab->slug);
			}

			$tpl->newBlock('tinymce-enabled');
			$tpl->newBlock('group-tab-edit');
			$tpl->assign(array(
				'tab-module' => $module_content,
				'tab-text' => h($tab->text)
			));
			$page_title = $group->title . ' - labot &quot;' . $tab->title . '&quot;';
		}
		// cilnes satura skatīšana
		elseif ($tab->public or ($is_mod || $is_admin || $is_member)) {

			if ($is_admin || $is_mod) {
				$tpl->newBlock('tab-options');
				$tpl->assign(array(
					'group-id' => $group->id,
					'slug' => $tab->slug
				));
			}

			// cilnes šārēšana draugos un twitterī
			$share = '';
			if ($tab->share) {
				$share = '
					<div style="float: left;width: 136px; height: 65px;">
						<div style="float: right;width: 65px; height: 65px;">
							<script type="text/javascript" src="//www.draugiem.lv/api/api.js"></script>
							<div id="draugiemLike"></div>
							<script type="text/javascript">
							var p = {
							 layout:"bubble"
							};
							new DApi.Like(p).append(\'draugiemLike\');
							</script>
						</div>

						<div style="float: right;width: 65px; height: 65px;">

							<a href="https://twitter.com/share" class="twitter-share-button" data-count="vertical">Tweet</a>
							<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+\'://platform.twitter.com/widgets.js\';fjs.parentNode.insertBefore(js,fjs);}}(document, \'script\', \'twitter-wjs\');</script>

						</div>
					</div>
				';
			}

			// samazina attēlus
			if ($tab->pic_heavy && stripos($tab->text, 'src="http') !== false) {
				$tab->text = resize_html_images($tab->text);
			}

			$tpl->newBlock('group-tab');
			$tpl->assign(array(
				'tab-module' => $module_content,
				'tab-text' => $share . add_smile($tab->text, 1)
			));
			$page_title = $group->title . ' - ' . $tab->title;
		} else {
			$page_title = $group->title . ' | ' . $tab->title;
			$tpl->newBlock('noguestacc-tab');

			$robotstag[] = 'noindex';
		}
	} else {
		redirect($group_link);
	}
}



/**
 * 4. GRUPAS OPCIJAS
 */ elseif (isset($_GET['var2']) && $_GET['var2'] == 'options') {

	$tpl->assignGlobal('active-tab-options', 'active');
	$tpl->newBlock('group-settings');

	if ($is_admin) {

		// cilnes dzēšana
		if (isset($_GET['deltab']) && check_token('deltab', $_GET['token'])) {
			$delete = intval($_GET['deltab']);
			if ($delete && $delete != 303) {
				$db->query("DELETE FROM `clans_tabs` WHERE `clan_id` = '$group->id' AND `id` = '$delete' AND `module` = '' LIMIT 1");
			}
			$auth->log('Izdzēsa tabu', 'clans', $group->id);
			redirect($group_link . '/options');
		}

		// cilnes pievienošana
		if (isset($_POST['tab-title']) && count($group_tabs) < 6 && strlen($_POST['tab-title']) > 2) {
			$title = trim(substr(strip_tags($_POST['tab-title']), 0, 16));
			$slug = mkslug($title);
			$title = sanitize($title);
			$public = (bool) $_POST['public'];
			if (!$db->get_var("SELECT count(*) FROM clans_tabs WHERE clan_id = '$group->id' AND slug = '$slug'") && !empty($slug)) {
				$db->query("INSERT INTO clans_tabs (clan_id,slug,title,date_modified,public)
											VALUES ('$group->id','$slug','$title','" . time() . "','$public')");
			}
			$auth->log('Izveidoja tabu &quot;' . $title . '&quot;', 'clans', $group->id);
			redirect($group_link . '/tab/' . $slug . '/edit');
		}

		// citas iespējas
		if (isset($_POST['submit-main'])) {
			$public = (bool) $_POST['main-public'];
			$auto_approve = (bool) $_POST['main-auto_approve'];
			$db->query("UPDATE `clans` SET `public` = '$public', `auto_approve` = '$auto_approve' WHERE `id` = '$group->id'");
			redirect($group_link . '/options');
		}

		// ciļņu skaitam ir noteikts limits, lai neaizcūkātu visu skatu
		if (count($group_tabs) < 6) {
			$tpl->newBlock('group-settings-newtab');
		}

		if ($group_tabs) {
			foreach ($group_tabs as $tab) {
				$tpl->newBlock('group-settings-tab');
				$tpl->assign(array(
					'id' => $tab->id,
					'slug' => $tab->slug,
					'title' => $tab->title,
					'token' => make_token('deltab')
				));
			}
		}

		$tpl->newBlock('group-settings-main');

		if ($group->public) {
			$tpl->assign('public-sel', ' checked="checked"');
		}
		if ($group->auto_approve) {
			$tpl->assign('auto_approve-sel', ' checked="checked"');
		}

		//grupas settingi kas pieejami globālajiem adminiem
		if ($auth->level == 1) {

			// saglabā
			if (isset($_POST['submit-admin'])) {
				$disable_vote = (bool) $_POST['admin-disable_vote'];
				$hide_intro = (bool) $_POST['admin-hide_intro'];
				$db->query("UPDATE `clans` SET `hide_intro` = '$hide_intro', `disable_vote` = '$disable_vote' WHERE `id` = '$group->id'");
				redirect($group_link . '/options');
			}

			$tpl->newBlock('group-settings-admin');
			if ($group->disable_vote) {
				$tpl->assign('disable_vote-sel', ' checked="checked"');
			}
			if ($group->hide_intro) {
				$tpl->assign('hide_intro-sel', ' checked="checked"');
			}
		}

		// grupas aptaujas
		$tpl->newBlock('polls_admin-body');
		$tpl->assign('list-active', 'active');
		$polls = $db->get_results("SELECT * FROM `poll` WHERE `group` = '$group->id' ORDER BY `id` DESC");
		if ($polls) {
			$tpl->newBlock('polls_admin-list');
			foreach ($polls as $poll) {
				$tpl->newBlock('polls_admin-list-node');
				$tpl->assign(array(
					'question' => $poll->name,
					'topic' => $poll->topic,
				));
			}
		}

		// jaunas aptaujas pievienošana
		$tpl->assign('exist-active', 'active');
		if (isset($_POST['new-poll-q']) && isset($_POST['new-poll-a'])) {
			$new_q = sanitize(h(trim($_POST['new-poll-q'])));
			$db->query("INSERT INTO poll (`name`,`topic`,`group`) VALUES ('$new_q','0','$group->id')");
			$poll_id = $db->insert_id;
			foreach ($_POST['new-poll-a'] as $new_a) {
				$new_a = trim($new_a);
				if (!empty($new_a)) {
					$new_a = sanitize(h($new_a));
					$db->query("INSERT INTO `questions` (`pid`,`question`) VALUES ('$poll_id','$new_a')");
				}
			}
			$auth->log('Izveidoja aptauju &quot;' . h(trim($_POST['new-poll-q'])) . '&quot;', 'clans', $group->id);
			$tpl->newBlock('polls_admin-success');
		} else {
			$tpl->newBlock('polls_admin-add');
		}
	} else {
		redirect($group_link);
	}

	$page_title = $group->title . ' - rīki';
}



/**
 * 5. GRUPAS MEKLĒŠANAS CILNE
 */ elseif (isset($_GET['var2']) && $_GET['var2'] == 'search') {

	$robotstag = array('noindex', 'follow');
	$tpl->assignGlobal('active-tab-search', 'active');
	$tpl->newBlock('group-search');

	// meklēt ļauts tikai grupas biedriem, ja vien grupa nav publiska
	if ($group->public || ($is_mod || $is_admin || $is_member)) {

		$tpl->newBlock('form-search');

		// meklēšanas forma jau aizpildīta; meklē...
		if (isset($_GET['q'])) {

			$q_string = str_replace(array(',', '.', '+', '-', '_'), ' ', $_GET['q']);
			$q_string = strip_tags($q_string);
			$tpl->assign('qstr', h($q_string));
			$q_strings = explode(' ', $q_string);
			$cond = '';
			foreach ($q_strings as $str) {
				$cond .= " AND `text` LIKE '%" . sanitize($str) . "%'";
			}

			// atlasa meklēšanas rezultātus un izvada lapā
			$results = $db->get_results("SELECT * FROM miniblog WHERE `groupid` = '$group->id' $cond ORDER BY id DESC LIMIT 50");
			if ($results) {
				$tpl->newBlock('res-search');
				foreach ($results as $result) {
					$tpl->newBlock('res-search-node');
					$result->text = strip_tags($result->text);
					foreach ($q_strings as $str) {
						$result->text = str_replace($str, '<strong>' . h($str) . '</strong>', $result->text);
					}
					$link = base_convert($result->id, 10, 36);
					if (!empty($result->parent)) {
						$link = base_convert($result->parent, 10, 36) . '#m' . $result->id;
					}
					$tpl->assign(array(
						'text' => $result->text,
						'group-id' => $group->id,
						'link' => $link,
					));
				}
			}
		}
	}
	// nav tiesību izmantot meklētāju
	else {
		$tpl->newBlock('noguestacc-search');

		$robotstag[] = 'noindex';
	}

	$page_title = $group->title . ' - meklēšana';
}



/**
 * 6. GRUPAS SĀKUMLAPAS CILNE UN TĀS IESPĒJAS
 */ else {

	$tpl->assignGlobal('active-tab-info', 'active');

	// grupas statistika
	$tpl->newBlock('group-info');
	$owner = get_user($group->owner);

	//cilnes moduli var iekļaut arī sākumlapā
	$module_content = '';
	if (!empty($group->firstpage_module)) {
		include(CORE_PATH . '/modules/groups/tabs/' . $group->firstpage_module . '.php');
		$module_content = '<div class="c"></div>' . $module_content . '<div class="c"></div>';
	}

	$tpl->assign(array(
		'group-text' => add_smile($group->text, 0),
		'group-posts' => $group->posts,
		'group-members' => $group->members + 1,
		'group-admin' => $owner->nick,
		'module_content' => $module_content
	));

	// iespējas
	if ($group->owner == $auth->id || im_mod() || $is_mod) {
		$tpl->newBlock('group-options');
		$tpl->assign(array(
			'group-id' => $group->id,
		));
	}

	// ...
	if ($auth->ok && $auth->id != $group->owner) {

		if (!$db->get_var("SELECT count(*) FROM clans_members WHERE clan = '$group->id' AND user = '$auth->id'") && $group->paid == 0 && !$group->archived) {
			$tpl->newBlock('group-info-apply');
			$tpl->assign(array(
				'group-id' => $group->id,
				'token' => make_token('apply')
			));
		} elseif (!$db->get_var("SELECT count(*) FROM clans_members WHERE clan = '$group->id' AND user = '$auth->id'") && !$group->archived) {
			$tpl->newBlock('group-info-apply-paid');
			$tpl->assign(array(
				'group-id' => $group->id,
				'token' => make_token('apply')
			));
		} elseif ($db->get_var("SELECT count(*) FROM clans_members WHERE clan = '$group->id' AND user = '$auth->id' AND approve = '0'")) {
			$tpl->newBlock('group-info-cancel');
			$tpl->assign(array(
				'group-id' => $group->id,
				'token' => make_token('cancel')
			));
		} elseif ($db->get_var("SELECT count(*) FROM clans_members WHERE clan = '$group->id' AND user = '$auth->id' AND approve = '1'")) {
			$tpl->newBlock('group-info-quit');
			$tpl->assign(array(
				'group-id' => $group->id,
				'token' => make_token('cancel')
			));
		}
	}

	// balsošana aptaujā
	if (isset($_POST['g-vote']) && isset($_POST['g-questions'])) {
		$q_pid = $db->get_var("SELECT `questions`.`pid` FROM `responses`, `questions` WHERE `responses`.`qid`=`questions`.`id` AND `responses`.`user_id`='" . $auth->id . "' AND pid=(SELECT pid FROM `questions` WHERE id='" . intval($_POST['g-questions']) . "' LIMIT 1)");
		if (empty($q_pid)) {
			$db->query("INSERT INTO `responses` (`qid`, `user_id`) VALUES ('" . intval($_POST['g-questions']) . "', '" . $auth->id . "')");
			push('Nobalsoja aptaujā', '/bildes/poll-icon.png');
			update_karma($auth->id, 1);
		} else {
			$error = 'Tu jau nobalsoji!';
		}
	} else if (!isset($_POST['questions']) && isset($_POST['vote'])) {
		$error = 'Jāizvēlas atbilde!';
	}

	// aptaujas forma
	$poll = $db->get_row("SELECT * FROM `poll` WHERE `group` = '$group->id' AND `lang` = '$lang' ORDER BY `id` DESC LIMIT 1");
	$title = 'Nav aptaujas!';
	if ($poll) {
		$title = $poll->name;

		$responded = $db->get_var("SELECT count(*) FROM `responses`, `questions` WHERE `responses`.`qid`=`questions`.`id` AND `responses`.`user_id`='" . $auth->id . "' AND pid='" . $poll->id . "'");

		if ($responded or !($is_mod or $is_admin or $is_member)) {
			$total = $db->get_var("SELECT count(*) FROM `responses`, `questions` WHERE `responses`.`qid`=`questions`.`id` AND `pid` = '" . $poll->id . "'");
			$tpl->newBlock('g-poll-box');
			$tpl->assign('poll-title', $title);
			$questions = $db->get_results("SELECT * FROM `questions` WHERE `pid` = '" . $poll->id . "' ORDER BY `id`");
			if (!empty($questions)) {
				$tpl->newBlock('g-poll-answers');

				foreach ($questions as $question) {
					$responses = $db->get_var("SELECT count(*) FROM `responses` WHERE `qid` = '" . $question->id . "'");
					$tpl->newBlock('g-poll-answers-node');
					$tpl->assign(array(
						'poll-answer-question' => $question->question,
						'poll-answer-percentage' => round(($responses / $total) * 100)
					));
				}

				$tpl->gotoBlock('g-poll-answers');
				$tpl->assign(array(
					'poll-totalvotes' => $total
				));
			}
		} else {
			$tpl->newBlock('g-poll-box');
			$tpl->assign('poll-title', $title);
			$questions = $db->get_results("SELECT * FROM `questions` WHERE `pid` = '" . $poll->id . "' ORDER BY `id`");
			if (!empty($questions)) {
				$tpl->newBlock('g-poll-questions');
				if (isset($error)) {
					$tpl->newBlock('g-poll-error');
					$tpl->assign('poll-error', $error);
				}
				$tpl->newBlock('g-poll-options');
				foreach ($questions as $question) {
					$tpl->newBlock('g-poll-options-node');
					$tpl->assign(array(
						'poll-options-question' => $question->question,
						'poll-options-id' => $question->id
					));
				}
			}
		}
	}

	// jaunāko biedru saraksts grupas sākumlapā
	if (!$auth->mobile) {
		$members = $db->get_results("SELECT user,moderator FROM clans_members WHERE clan = '$group->id' AND approve = '1' ORDER BY date_added DESC LIMIT 16");
		if ($members) {
			$tpl->newBlock('nmembers');
			foreach ($members as $member) {
				$m_user = get_user($member->user);

				$avatar = get_avatar($m_user, 's');

				$mclas = 'member';
				if ($member->moderator) {
					$mclas = 'mod';
				}

				$tpl->newBlock('nmembers-node');
				$tpl->assign(array(
					'member-id' => $m_user->id,
					'member-nick' => h($m_user->nick),
					'avatar' => $avatar,
				));
			}
		}
		
		//grupas admina maiņa
		if($auth->level == 1) {
		
			if(isset($_POST['group-chown'])) {
				$newuser = get_user($_POST['group-chown']);
				
				if(!empty($newuser) && $newuser->deleted == 0 && $newuser->id != $group->owner) {
					$db->query("UPDATE `clans` SET `owner` = ".intval($newuser->id).", `owner_seenposts` = 0 WHERE `id` = " . intval($group->id));
					$db->query("DELETE FROM `clans_members` WHERE `user` = ".intval($newuser->id)." AND `clan` = " . intval($group->id));
					$olduser = get_user($group->owner);
					
					if(!empty($olduser) && $olduser->deleted == 0) {
						$db->query("INSERT INTO `clans_members` (`user`, `clan`, `approve`, `date_added`) VALUES (".intval($group->owner).", " . intval($group->id) .", 1, ".intval($group->date_created).")");
					}
					
					$auth->log('Nomainija grupas administratoru ('.$olduser->nick.' &raquo; '.$newuser->nick.')', 'clans', $group->id);
					redirect($group_link);
				}
			
			}
		
		
			$tpl->newBlock('group-chown');
		}
		
	}

	// jaunāko miniblogu fragmenti, ja tie nav slēpti
	if (!$group->hide_intro || im_mod()) {
		$tpl->newBlock('glatest-box');
		$tpl->assign('out', get_latest_mbs('all', $group->id));
	}

	$page_title = $group->title;
}

