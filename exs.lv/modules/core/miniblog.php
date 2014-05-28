<?php

/**
 * Miniblogi (saraksts, atvērums, komentēšana...)
 */
if (!$inprofile = get_user(intval($_GET['m']))) {
	header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
	header("Status: 404 Not Found");
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

		$lastins = post_mb(array(
			'text' => $body
		));

		$topic = $db->get_row("SELECT * FROM `miniblog` WHERE `id` = '$lastins'");

        // dažādas notifikācijas
		$title = mb_get_title($topic->text);
		$strid = mb_get_strid($title, $topic->id);
		push('Izveidoja <a href="/say/' . $inprofile->id . '/' . $topic->id . '-' . $strid . '">minibloga ierakstu &quot;' . textlimit(hide_spoilers($title), 32, '...') . '&quot;</a>');

		$topic->text = mention($topic->text, "/say/' . $inprofile->id . '/' . $topic->id . '-' . $strid . '", 'mb', $topic->id);
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

	if (get_mb_level($to) > 1 && $auth->level != 1) {
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
			$newid = post_mb(array(
				'text' => $body,
				'parent' => $mainid,
				'reply_to' => $reply_to_id
			));

			if ($check == $auth->id) {
				$str = 'savā';
			} else {
				$str = $inprofile->nick;
			}
			$body = $db->get_var("SELECT `text` FROM `miniblog` WHERE `id` = '$mainid'");

			$title = mb_get_title(stripslashes($body));
			$strid = mb_get_strid($title, $mainid);
			$url = '/say/' . $check . '/' . $mainid . '-' . $strid;

            // bump, notifikācijas
			if (!isset($_POST['no-bump'])) {
				push('Atbildēja <a href="' . $url . '#m' . $newid . '">' . $str . ' miniblogā &quot;' . textlimit(hide_spoilers($title), 32, '...') . '&quot;</a>', '', 'mb-answ-' . $mainid);

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
				$db->query("INSERT INTO miniblog (`author`,`date`,`text`,`ip`,`bump`,`lang`) VALUES ('$topic->author',NOW(),'$body','$topic->ip','" . time() . "','$topic->lang')");
				$new = $db->insert_id;
				$newtopic = $db->get_row("SELECT * FROM miniblog WHERE id = '$new'");
				$newtitle = mb_get_title($newtopic->text);
				$newstrid = mb_get_strid($newtitle, $new);
				$newurl = '/say/' . $topic->author . '/' . $newtopic->id . '-' . $newstrid;
				$reason = sanitize('Sasniegts 500 atbilžu limits, slēgts automātiski. Tēmas tupinājums: <a href="' . $newurl . '">http://' . $_SERVER['HTTP_HOST'] . $newurl . '</a>.');
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
if ($auth->ok && im_mod() && isset($_GET['unclose']) && isset($_GET['single'])) {
	$sid = (int) $_GET['single'];
	if ($sid > 0) {
		$db->query("UPDATE miniblog SET closed = '0' WHERE id = '$sid' AND closed_by != '17077' AND `lang` = '$lang'");
		$auth->log('Atvēra miniblogu', 'miniblog', $sid);
		redirect('/?m=' . $inprofile->id . '&single=' . $sid);
	}
}

// iekešo minibloga template
if ($debug == true || ($tpl2 = $m->get('tpl_mb_' . $lang . '_' . intval($auth->mobile) . $bootstrap_cache_key)) == false) {
	$tpl->assignInclude('module-currrent', CORE_PATH . '/modules/core/miniblog.tpl');
	$tpl->assignInclude('conversation', CORE_PATH . '/modules/core/conversation.tpl');
	$tpl->prepare();
	$m->set('tpl_mb_' . $lang . '_' . intval($auth->mobile) . $bootstrap_cache_key, $tpl, false, 7200);
} else {
	$tpl = $tpl2;
	unset($tpl2);
}

$tpl->newBlock('profile-menu');
$tpl->assign('user-menu-add', ' miniblogs');


// visas turpmākās darbības tikai tad, ja ir zināms,
// kura lietotāja miniblogs atvērts
if ($inprofile->id) {

	set_action('<a href="/say/' . $inprofile->id . '">' . $inprofile->nick . '</a> miniblogu');

    // izveido pašu minibloga skatu
	$tpl->assignGlobal(array(
		'user-id' => $inprofile->id,
		'user-nick' => htmlspecialchars($inprofile->nick),
		'active-tab-miniblog' => 'active'
	));
	$page_title = $inprofile->nick . ' miniblogs';

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
	}

    // saraksts ar visiem lietotāja miniblogiem
	if (!isset($_GET['single'])) {
		$records = $db->get_results("SELECT * FROM `miniblog` WHERE `author` = " . $inprofile->id . " AND `groupid` = '0' AND `removed` = '0' AND `parent` = '0' AND `lang` = '$lang' ORDER BY `bump` DESC LIMIT $skip,$end");
	} 
    // atvērts konkrēts miniblogs 
    else {
		$single = (int) $_GET['single'];
		$records = $db->get_results("SELECT * FROM `miniblog` WHERE `id` = '$single' AND `author` = " . $inprofile->id . " AND `groupid` = '0' AND `removed` = '0' AND `parent` = '0' AND `lang` = '$lang' LIMIT 1");
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

				if (isset($_GET['single'])) {

					// pieliek tagus
					if (im_mod() && isset($_POST['newtags'])) {
                    
						include_once(CORE_PATH . '/includes/class.tags.php');
						$newtags = explode(',', $_POST['newtags']);
						$tags = new tags;
						foreach ($newtags as $newtag) {
							if (strlen(trim($newtag)) > 1) {
								$newtag = htmlspecialchars(ucfirst(strip_tags(trim($newtag))));
								$nslug = mkslug($newtag);
								if (!empty($newtag)) {
									$tagid = $db->get_var("SELECT id FROM tags WHERE slug = '$nslug'");
									if (!$tagid) {
										$db->query("INSERT INTO tags (name,slug) VALUES ('" . sanitize($newtag) . "','$nslug')");
										$tagid = $db->insert_id;
									}
									if ($tags->add_tag($record->id, $tagid, 2)) {
										echo '<li><a href="/tag/' . $nslug . '" rel="tag">' . $newtag . '</a></li>';
									}
								}
							}
						}
						exit;
					}

					if (!isset($_GET['vc']) && !isset($_GET['close'])) {
						if ($_SERVER['REQUEST_URI'] != $url) {
							redirect($url, true);
						}

						if (!empty($title)) {
							$page_title = $title . ' | ' . $inprofile->nick;
						} else {
							$page_title = 'Ieraksts #' . $record->id . ' | ' . $inprofile->nick;
						}
					}
				}

                // Twitter profila dati
				$append = '';
				if ($record->twitterid) {
					$append .= '<p><a title="' . $record->twitteruser . ' iekš Twitter" href="http://twitter.com/' . $record->twitteruser . '/status/' . $record->twitterid . '" rel="nofollow" class="mb-api-twitter">@' . $record->twitteruser . '</a></p>';
				}

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

				$tpl->assign(array(
					'url' => $url,
					'text' => add_smile($record->text) . $append,
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
				));

                // ieraksta iespēju pogas redzamas vien tad, 
                // ja miniblogs ir atvērts (nevis lietotāja miniblogu sarakstā)
				if (isset($_GET['single'])) {

					if ($auth->ok) {
						$tpl->newBlock('mb-reply-main');
					}

                    // ieraksta rediģēšanas poga
					if ((im_mod() || (!$record->closed && $auth->karma >= $min_post_edit && $record->author == $auth->id)) && (strtotime($record->date) > time() - 1800) || $auth->level == 1) {
						$tpl->newBlock('mb-edit-main');
						$tpl->assign(array(
							'id' => $record->id,
						));
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
					// lūdzu neņem nost laika ierobežojumu :/
					if (im_mod() && strtotime($record->date) > time() - 86400) {
						$tpl->newBlock('mb-delete');
						$tpl->assign(array(
							'id' => $record->id
						));
					}

					// podziņa mb pārkāpuma ziņošanai
					if ($auth->ok && !$auth->mobile && in_array($lang, array(1, 7, 9))) {
						$tpl->newBlock('report-mb');
						$tpl->assign('id', $record->id);
					}

					$limit = '';
				} else {
					$limit = ' LIMIT 0,3';
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
						$json = array();
						foreach ($responses as $response) {
							$json[$response->reply_to][] = $response;
						}
						$tpl->newBlock('miniblog-posts');
						$tpl->assign('mbout', mb_recursive($json, 0, 0, !isset($_GET['single']), 3, $record->closed));
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
					$tpl->assign(array(
						'text' => $text,
						'url' => $url
					));
				} 
                // atvērtā miniblogā parāda pievienotos tagus
                else {
					if (!$record->posts) {
						$tpl->newBlock('miniblog-no');
					}

					$tpl->newBlock('mb-tags-wrapper');
					$tags = $db->get_results("
                        SELECT
                            `tags`.`name` AS `name`,
                            `tags`.`slug` AS `slug`
                        FROM
                            `taged`,
                            `tags`
                        WHERE
                            `taged`.`page_id` = '$record->id' AND
                            `taged`.`type` = '2' AND
                            `taged`.`lang` = '$lang' AND
                            `tags`.`id` = `taged`.`tag_id`
                        LIMIT 6
                    ");

					if ($tags) {
						$tpl->newBlock('mb-tags');
						foreach ($tags as $tag) {
							$tpl->newBlock('mb-tags-node');
							$tpl->assign(array(
								'slug' => $tag->slug,
								'name' => $tag->name
							));
						}
					}
					if (im_mod()) {
						$tpl->newBlock('mb-newtags');
					}
				}
			} else {

				//$tpl->newBlock('user-miniblog-list-private');
			}
		}

		// ???
		if (isset($_GET['single']) && $auth->ok && !$record->closed) {
			$tpl->newBlock('user-miniblog-resp');
			$tpl->assign(array(
				'id' => $record->id,
				'token' => md5('mb' . $record->id . $remote_salt . $auth->nick)
			));

			if ($auth->id == 1) {
				$tpl->newBlock('resp-tools');
			}

			$tpl->newBlock('mb-head');
			$tpl->assign(array(
				'mbid' => $record->id,
				'usrid' => $inprofile->id,
				'edit_time' => time(),
				'type' => 'miniblog',
				'lastid' => (int) $db->get_var("SELECT `id` FROM `miniblog` WHERE `parent` = '$record->id' AND `type` = 'miniblog' ORDER BY `id` DESC LIMIT 1")
			));
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
			if(!$auth->ok) {
				$private = ' AND `private` = 0';
			}
		
            // lappušu saraksts
			$total = $db->get_var("SELECT count(*) FROM `miniblog` USE INDEX (`count_pager`) WHERE `author` = " . $inprofile->id . " AND `groupid` = 0 AND `removed` = '0' AND `parent` = 0 AND `lang` = '$lang'" . $private);
			$pager = pager($total, $skip, $end, '/say/' . $inprofile->id . '/skip-');
			$tpl->newBlock('mb-pager');
			$tpl->assign(array(
				'pager-next' => $pager['next'],
				'pager-prev' => $pager['prev'],
				'pager-numeric' => $pager['pages']
			));
		}
	}
	if ($auth->ok && $auth->id == $inprofile->id) {
		$tpl->assignGlobal('mb-sel', ' class="selected"');
	}
} else {
	$tpl->newBlock('error-nouser');
	$page_title = 'Kļūda: profils nav atrasts!';
}
