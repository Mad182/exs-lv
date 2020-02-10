<?php

/**
 * Miniblogi (saraksts, atvērums, komentēšana...)
 */
if (!$inprofile = get_user(intval($_GET['m']))) {
	header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
	set_flash('Pieprasītā lapa netika atrasta!', 'error');
	redirect();
}

if ($_SERVER['REQUEST_URI'] == '/?m=' . $inprofile->id) {
	redirect('/say/' . $inprofile->id, true);
}

//paginator fīčas
if (isset($_GET['skip'])) {
	$skip = (int) $_GET['skip'];
} else {
	$skip = 0;
}
$end = 6;

// jauna minibloga pievienošana
if ($auth->ok === true && $auth->id === $inprofile->id && isset($_POST['newminiblog']) && !empty($_POST['newminiblog'])) {

	if (!isset($_POST['token']) or $_POST['token'] != md5('mb' . $remote_salt . $auth->nick)) {
		set_flash('Kļūdains pieprasījums! Hacking around?', 'error');
		redirect();
	}

	$body = post2db($_POST['newminiblog']);

	// flood kontrole
	if (!isset($_SESSION['antiflood']) or $_SESSION['antiflood'] < time() - 15) {
		$_SESSION["antiflood"] = time();

		$private = 0;
		if (isset($_POST['private'])) {
			$private = 1;
		}

		if(
			stristr($body, 'fano.in') || 
			stristr($body, 'ielūgum') || 
			stristr($body, 'ielugum') || 
			stristr($body, 'ieluugum') || 
			stristr($body, 'skatīties') || 
			stristr($body, 'stream') || 
			stristr($body, 'filmas onl') || 
			stristr($body, 'online film') || 
			stristr($body, 'weed') || 
			stristr($body, 'menti') || 
			stristr($body, 'legalize') || 
			stristr($body, 'sex') || 
			stristr($body, 'huj') || 
			stristr($body, 'dirs') || 
			stristr($body, 'pimp') || 
			stristr($body, 'pipel') || 
			stristr($body, 'sūkā') || 
			stristr($body, 'balinātāju') || 
			stristr($body, 'kys') || 
			stristr($body, 'mauka') || 
			stristr($body, 'poker') || 
			stristr($body, 'optibet') || 
			stristr($body, ' vs ') || 
			stristr($body, ' vs. ') || 
			stristr($body, 'olybet') || 
			stristr($body, 'likm') || 
			stristr($body, 'ugunsgr') || 
			stristr($body, 'svešā seja') || 
			stristr($body, 'svešo seju') || 
			stristr($body, 'casino') || 
			stristr($body, 'kazino') || 
			stristr($body, 'torrent') || 
			stristr($body, 'nokačāt') || 
			stristr($body, 'kačājat') || 
			stristr($body, 'novilkt') || 
			stristr($body, 'autism') || 
			stristr($body, 'autist') || 
			stristr($body, 'crack') || 
			stristr($body, 'drug') || 
			stristr($body, 'cdkey') || 
			stristr($body, 'kreku') || 
			stristr($body, 'penis') || 
			stristr($body, 'sezonas') || 
			stristr($body, 'casino') || 
			stristr($body, 'zaļo') || 
			stristr($body, 'narkotikas')
		) {
			$private = 1;
		}

		$lastins = post_mb([
			'text' => $body,
			'private' => $private
		]);

		$topic = $db->get_row("SELECT * FROM `miniblog` WHERE `id` = '$lastins'");

		// dažādas notifikācijas
		$title = mb_get_title($topic->text);
		$strid = mb_get_strid($title, $topic->id);
		push('Izveidoja <a href="/say/' . $inprofile->id . '/' . $topic->id . '-' . $strid . '">minibloga ierakstu &quot;' . textlimit(hide_spoilers($title), 32, '...') . '&quot;</a>', '', '', $private);

		$topic->text = mention($topic->text, '/say/' . $inprofile->id . '/' . $topic->id . '-' . $strid, 'mb', $topic->id);
		$db->query("UPDATE `miniblog` SET `text` = '" . sanitize($topic->text) . "' WHERE id = '$topic->id'");

		redirect('/say/' . $inprofile->id . '/' . $topic->id . '-' . $strid);
	} else {
		set_flash('Izskatās pēc flooda. Pagaidi 15 sekundes, pirms pievieno jaunu tēmu!', 'error');
	}
}

// atbildes pievienošana (jeb komentēšana)
if ($auth->ok === true && isset($_POST['responseminiblog']) && !empty($_POST['responseminiblog'])) {

	$to = (int) $_POST['response-to'];

	if (!isset($_POST['token']) || $_POST['token'] != md5('mb' . intval($_GET['single']) . $remote_salt . $auth->nick)) {
		set_flash('Kļūdains pieprasījums! Hacking around?');
		redirect();
	}

	if (get_mb_level($to) > 2 && $auth->level != 1) {
		die('Too deep ;(');
	}

	// parent komentāra dati
	$reply_to = $db->get_row("SELECT * FROM `miniblog` WHERE `id` = '$to' AND `removed` = '0' AND `groupid` = '0'");

	$reply_to_id = 0;
	if ($reply_to->parent != 0) {
		$mainid = $reply_to->parent;
		$reply_to_id = $reply_to->id;
	} else {
		$mainid = $to;
	}

	$body = post2db($_POST['responseminiblog']);

	// vai parents eksistē? vai tēma nav slēgta?
	$check = $db->get_var("SELECT `author` FROM miniblog WHERE id = '" . $mainid . "' AND removed = '0' AND groupid = '0'");
	if (!$check || $check != $inprofile->id) {
		die("Kļūdains parent id! Iespējams kamēr rakstīji komentāru, kāds izdzēsa tēmu.");
	}
	$check2 = $db->get_var("SELECT author FROM miniblog WHERE id = '" . $mainid . "' AND closed = '1'");
	if ($check2) {
		die("Tēma ir slēgta.");
	}

	// viss kārtībā, var pievienot
	if ($mainid) {

		// flood kontrole
		if (!isset($_SESSION['antiflood']) or $_SESSION['antiflood'] < time() - 4) {
			$_SESSION["antiflood"] = time();

			// pievieno komentāru
			$newid = post_mb([
				'text' => $body,
				'parent' => $mainid,
				'reply_to' => $reply_to_id
			]);

			if ($check == $auth->id) {
				$str = 'savā miniblogā';
			} elseif(!$inprofile->deleted) {
				$str = $inprofile->nick . ' miniblogā';
			} else {
				$str = 'miniblogā';
			}
			$body = $db->get_var("SELECT `text` FROM `miniblog` WHERE `id` = '$mainid'");
			$private = $db->get_var("SELECT `private` FROM `miniblog` WHERE `id` = '$mainid'");

			$title = mb_get_title(stripslashes($body));
			$strid = mb_get_strid($title, $mainid);
			$url = '/say/' . $check . '/' . $mainid . '-' . $strid;

			// bump, notifikācijas
			if (!isset($_POST['no-bump'])) {
				push('Atbildēja <a href="' . $url . '#m' . $newid . '">' . $str . ' &quot;' . textlimit(hide_spoilers($title), 32, '...') . '&quot;</a>', '', 'mb-answ-' . $mainid, $private);

				$newpost = $db->get_row("SELECT * FROM `miniblog` WHERE id = '$newid'");
				$newpost->text = mention($newpost->text, $url, 'mb', $mainid);
				$db->query("UPDATE `miniblog` SET `text` = '" . sanitize($newpost->text) . "' WHERE id = '$newpost->id'");

				notify($inprofile->id, 3, $mainid, $url, textlimit(hide_spoilers($title), 64));
				if (!empty($reply_to_id) && $inprofile->id != $reply_to->author) {
					notify($reply_to->author, 3, $mainid, $url, textlimit(hide_spoilers($title), 64));
				}
			}

			// ja miniblogā ir vismaz 500 komentāri, to aizver un izveido jaunu miniblogu,
			// kurā viss turpinās
			$topic = $db->get_row("SELECT * FROM `miniblog` WHERE `id` = '$mainid'");
			if ($topic->posts >= 500) {
				$body = sanitize($topic->text . '<p>(<a href="' . $url . '">Tēmas</a> turpinājums)</p>');
				$db->query("INSERT INTO miniblog (`author`,`date`,`text`,`ip`,`bump`,`lang`,`private`) VALUES ('$topic->author',NOW(),'$body','$topic->ip','" . time() . "','$topic->lang','$topic->private')");
				$new = $db->insert_id;
				$newtopic = $db->get_row("SELECT * FROM miniblog WHERE id = '$new'");
				$newtitle = mb_get_title($newtopic->text);
				$newstrid = mb_get_strid($newtitle, $new);
				$newurl = '/say/' . $topic->author . '/' . $newtopic->id . '-' . $newstrid;
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

// minibloga atslēgšana
if ($auth->ok && im_mod() && isset($_GET['open']) && isset($_GET['single'])) {
	$sid = (int) $_GET['single'];
	if ($sid > 0) {
		$db->query("UPDATE miniblog SET closed = '0' WHERE id = '$sid' AND closed_by != '17077' AND `lang` = '$lang'");
		$auth->log('Atvēra miniblogu', 'miniblog', $sid);
		redirect('/?m=' . $inprofile->id . '&single=' . $sid);
	}
}

// iekešo minibloga template
$key = md5('tpl_mb_' . $lang . '_' . intval($auth->mobile) . '_' . $_SERVER['HTTP_HOST']);
if ($debug == true || ($tpl2 = $m->get($key)) == false) {
	$tpl->assignInclude('module-currrent', CORE_PATH . '/modules/core/miniblog.tpl');
	$tpl->assignInclude('conversation', CORE_PATH . '/modules/core/conversation.tpl');
	$tpl->prepare();
	$m->set($key, $tpl, 3600);
} else {
	$tpl = $tpl2;
	unset($tpl2);
}
	
// runescape apakšprojektā smukāk izskatās, 
// ja miniblogam blakus ir tikai 1 kolonna
if ($lang === 9) {
	$tpl_options = 'no-right';
}

// visas turpmākās darbības tikai tad, ja ir zināms,
// kura lietotāja miniblogs atvērts
if (!empty($inprofile)) {

	profile_menu($inprofile, 'miniblog', 'miniblogs', 'miniblogu');

	$tpl->newBlock('user-miniblog');

	// minibloga slēgšana (tikai moderatoriem)
	if ($auth->ok && im_mod() && isset($_GET['close']) && isset($_GET['single'])) {

		$sid = (int) $_GET['single'];

		// minibloga aizvēršanas iemeslam ir jābūt
		// (iemesls ar atstarpēm gan arī der :p )
		if (isset($_POST['reason']) && !empty($_POST['reason'])) {
			$reason = post2db($_POST['reason']);
			$db->query("UPDATE `miniblog` SET `closed` = '1', `close_reason` = '$reason', `closed_by` = '$auth->id' WHERE `id` = '$sid' AND `lang` = '$lang'");
			$auth->log('Aizslēdza miniblogu (' . strip_tags($reason) . ')', 'miniblog', $sid);
			redirect('/?m=' . $inprofile->id . '&single=' . $sid);
		} else {
			$tpl->newBlock('close-reason');
		}
	}

	// ? kaut kāds security tokens
	if ($auth->ok && $auth->id == $inprofile->id && !isset($_GET['single'])) {
		$tpl->newBlock('user-miniblog-form');
		$tpl->assign('token', md5('mb' . $remote_salt . $auth->nick));
		$tpl->newBlock('private-checkbox');
	}

	// saraksts ar visiem lietotāja miniblogiem
	if (!isset($_GET['single'])) {
		$records = $db->get_results("SELECT * FROM `miniblog` WHERE `author` = " . $inprofile->id . " AND `groupid` = '0' AND `removed` = '0' AND `parent` = '0' AND `lang` = '$lang' ORDER BY `bump` DESC LIMIT $skip,$end");
	}
	// atvērts konkrēts miniblogs
	else {
		$single = (int) $_GET['single'];
		$records = $db->get_results("SELECT * FROM `miniblog` WHERE `id` = '$single' AND `author` = " . $inprofile->id . " AND `groupid` = '0' AND `removed` = '0' AND `parent` = '0' LIMIT 1");
	}

	if ($records) {

		$tpl->newBlock('user-miniblog-list');

		// iet cauri visiem atlasītajiem miniblogiem
		// (tāds var būt arī tikai viens, ja miniblogs atvērts)
		foreach ($records as $record) {

			if (!$record->private || $auth->ok === true) {

				$tpl->newBlock('user-miniblog-list-node');

				$title = textlimit(youtube_title($record->text), 64, '...');
				$url = '/say/' . $record->author . '/' . $record->id . '-' . mb_get_strid($record->text, $record->id);

				//ja pieprasīts raksts nepareizā subdomēnā
				if ($record->lang != $lang) {
					redirect(get_protocol($record->lang) . $config_domains[$record->lang]['domain'] . $url, true);
				}

				if (isset($_GET['single'])) {

					//noindex
					if($record->private || $record->noindex || (strlen(strip_tags($record->text)) < 70 && $record->posts < 5 && $record->id != 2389427)) {
						$robotstag = ['noindex', 'nofollow'];
					}

					if (!isset($_GET['vc']) && !isset($_GET['close'])) {
						if ($_SERVER['REQUEST_URI'] != $url) {
							redirect($url, true);
						}

						if (!empty($title)) {
							$page_title = $title;
							if(!$inprofile->deleted) {
								$page_title .= ' | ' . $inprofile->nick;
							}
						} else {
							$page_title = 'Ieraksts #' . $record->id;
							if(!$inprofile->deleted) {
								$page_title .= ' | ' . $inprofile->nick;
							}
						}

					}

					$canonical = get_protocol($record->lang) . $config_domains[$record->lang]['domain'] . $url;

				}

				$post_bump = $record->bump;

				// apbalvojumu ikonas pie lietotājvārda
				$add_deco = '';
				if (!empty($inprofile->decos)) {
					$decos = unserialize($inprofile->decos);
					if (!empty($decos)) {
						$di = 0;
						foreach ($decos as $deco) {
							$add_deco .= '<img src="' . $deco['icon'] . '" alt="' . $deco['title'] . '" title="' . $deco['title'] . '" class="user-deco deco-pos-' . $di . '" />';
							$di++;
						}
					}
				}

				// dzēstu profilu lietotājvārdi
				if (!$inprofile->deleted) {
					$author = '<a href="/user/' . $inprofile->id . '">' . usercolor($inprofile->nick, $inprofile->level, false, $inprofile->id) . '</a>';
				} else {
					$author = '<em>dzēsts</em>';
				}

				$tpl->assign([
					'url' => $url,
					'text' => add_smile($record->text),
					'add_deco' => $add_deco,
					'date' => display_time(strtotime($record->date)),
					'date-title' => date('Y-m-d H:i:s', strtotime($record->date)),
					'author' => $author,
					'author-id' => $record->author,
					'avatar' => get_avatar($inprofile, 's'),
					'author-nick' => $inprofile->nick,
					'id' => $record->id,
					'title' => $title,
					'rater' => mb_rater($record, $url)
				]);

				// ieraksta iespēju pogas redzamas vien tad,
				// ja miniblogs ir atvērts (nevis lietotāja miniblogu sarakstā)
				if (isset($_GET['single'])) {

					if ($auth->ok) {
						$tpl->newBlock('mb-reply-main');
					}

					// ieraksta rediģēšanas poga
					if ((im_mod() || (!$record->closed && $auth->karma >= $min_post_edit && $record->author == $auth->id)) && (strtotime($record->date) > time() - 111800) || $auth->level == 1 || $auth->id == 115) {
						$tpl->newBlock('mb-edit-main');
						$tpl->assign([
							'id' => $record->id,
						]);
					}

					// linki ieraksta aizslēgšanai/atslēgšanai
					if (im_mod()) {
						if ($record->closed) {
							$tpl->newBlock('mb-edit-unclose');
						} else {
							$tpl->newBlock('mb-edit-close');
						}
						$tpl->assign('url', $url);
					}

					// ieraksta dzēšana;
					if ((im_mod() && strtotime($record->date) > time() - 286400) || ($record->author == $auth->id) || ($auth->level == 1 && $debug)) {
						$tpl->newBlock('mb-delete');
						$tpl->assign([
							'id' => $record->id,
							'token' => make_token('delmb')
						]);
					}

					// podziņa mb pārkāpuma ziņošanai
					if ($auth->ok && !$auth->mobile && in_array($lang, [1, 7, 9])) {
						$tpl->newBlock('report-mb');
						$tpl->assign('id', $record->id);
					}

					$limit = '';
				} else {
					$limit = ' LIMIT 0,3';
					$robotstag = ['noindex', 'follow'];
				}

				// atvērtiem miniblogiem pievieno komentārus
				if ($record->posts) {

					$responses = $db->get_results("
						SELECT
							`miniblog`.`text` AS `text`,
							`miniblog`.`vote_value` AS `vote_value`,
							`miniblog`.`vote_users` AS `vote_users`,
							`miniblog`.`date` AS `date`,
							`miniblog`.`author` AS `author`,
							`miniblog`.`groupid` AS `groupid`,
							`miniblog`.`posts` AS `posts`,
							`miniblog`.`reply_to` AS `reply_to`,
							`miniblog`.`id` AS `id`,
							`miniblog`.`removed` AS `mb_removed`,
							`miniblog`.`hidden` AS `hidden`,
							`miniblog`.`private` AS `private`,
							`users`.`nick` AS `nick`,
							`users`.`decos` AS `decos`,
							`users`.`avatar` AS `avatar`,
							`users`.`av_alt` AS `av_alt`,
							`users`.`level` AS `level`,
							`users`.`deleted` AS `user_deleted`
						FROM
							`miniblog`,
							`users`
						WHERE
							`miniblog`.`parent` = '" . $record->id . "' AND
							`miniblog`.`type` = 'miniblog' AND
							`users`.`id` = `miniblog`.`author`
						ORDER BY
							`miniblog`.`id`
						ASC" . $limit
					);

					if ($responses) {
						$json = [];
						foreach ($responses as $response) {
							$json[$response->reply_to][] = $response;
						}
						$tpl->newBlock('miniblog-posts');
						$tpl->assign('mbout', mb_recursive($json, 0, 0, !isset($_GET['single']), 4, $record->closed));
					}
				}

				// lietotāja miniblogu sarakstā rāda iespēju tos atvērt
				if (!isset($_GET['single'])) {
					$tpl->newBlock('mb-more');
					if ($record->posts > 3) {
						$text = 'Apskatīt vēl ' . ($record->posts - 3) . ' ' . lv_dsk($record->posts - 3, 'atbildi', 'atbildes') . '&nbsp;&raquo;';
					} else {
						$text = 'Atvērt sarunu&nbsp;&raquo;';
					}
					$tpl->assign([
						'text' => $text,
						'url' => $url
					]);
				}
				// atvērtā miniblogā parāda pievienotos tagus
				else {
					if (!$record->posts) {
						$tpl->newBlock('miniblog-no');
					}

				}
			} else {
				if(!empty($single)) {
					$robotstag = ['noindex', 'nofollow'];
				}
			}
		}

		// komentāru forma
		if (isset($_GET['single']) && $auth->ok && !$record->closed) {
			$tpl->newBlock('user-miniblog-resp');
			$tpl->assign([
				'id' => $record->id,
				'token' => md5('mb' . $record->id . $remote_salt . $auth->nick)
			]);

			if ($auth->id == 1) {
				$tpl->newBlock('resp-tools');
			}

			$tpl->newBlock('mb-head');
			$tpl->assign([
				'mbid' => $record->id,
				'usrid' => $inprofile->id,
				'edit_time' => time(),
				'type' => 'miniblog',
				'lastid' => (int) $db->get_var("SELECT `id` FROM `miniblog` WHERE `parent` = '$record->id' AND `type` = 'miniblog' ORDER BY `id` DESC LIMIT 1")
			]);
		}
		// paziņojums, ka miniblogs slēgts
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
		// paziņojums, ka, lai komentētu, jāautorizējas
		elseif (isset($_GET['single']) && !$auth->ok) {
			$tpl->newBlock('user-miniblog-login');
		}

		if (!isset($_GET['single'])) {

			$private = '';
			if (!$auth->ok) {
				$private = ' AND `private` = 0';
			}

			// lappušu saraksts
			$total = $db->get_var("SELECT count(*) FROM `miniblog` USE INDEX (`count_pager`) WHERE `author` = " . $inprofile->id . " AND `groupid` = 0 AND `removed` = '0' AND `parent` = 0 AND `lang` = '$lang'" . $private);
			$pager = pager($total, $skip, $end, '/say/' . $inprofile->id . '/skip-');
			$tpl->newBlock('mb-pager');
			$tpl->assign([
				'pager-next' => $pager['next'],
				'pager-prev' => $pager['prev'],
				'pager-numeric' => $pager['pages']
			]);
		}
	} elseif(!empty($_GET['single'])) {
		$robotstag = ['noindex', 'nofollow'];
		error_404();
	} else {
		$robotstag = ['noindex'];
	}
	if ($auth->ok && $auth->id == $inprofile->id) {
		$tpl->assignGlobal('mb-sel', ' class="selected"');
	}
} else {
	$robotstag = ['noindex', 'nofollow'];
	$tpl->newBlock('error-nouser');
	$page_title = 'Kļūda: profils nav atrasts!';
}

// poga ritināšanai līdz pašai augšai mobilajā versijā
if ($auth->mobile) {
    $tpl->newBlock('scroll-up-mobile');
}
