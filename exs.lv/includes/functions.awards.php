<?php

/**
 * Visas funkcijas, kas saistītas ar lietotāju apbalvojumiem
 */

/**
 * 4 svarīgākās ikonas ko parādīt zem profila bildes
 *
 * @return string HTML ar ikonām
 */
function get_top_awards($user) {
	global $db, $m, $img_server;
	$user = (int) $user;
	if (($data = $m->get('aw_' . $user)) === false) {
		$data = '';
		$res = $db->get_results("SELECT `id`,`award`,`title` FROM `autoawards` WHERE `user_id` = '$user' ORDER BY `importance` DESC LIMIT 4");
		if ($res) {
			$data .= '<p id="profile-awards">';
			foreach ($res as $award) {
				$data .= '<img width="32" height="32" src="' . $img_server . '/dati/bildes/awards/' . $award->award . '.png" alt="' . $award->award . '" title="' . h(strip_tags($award->title)) . '" />&nbsp;';
			}
			$total = $db->get_var("SELECT count(*) FROM `autoawards` WHERE `user_id` = '$user'");
			if ($total > 4) {
				$data .= '<a title="Visas ' . $total . ' medaļas" href="/awards/' . $user . '">(' . $total . ')</a>';
			}
			$data .= '</p>';
		}
		$m->set('aw_' . $user, $data, false, 3600);
	}
	return $data;
}

/**
 * Awardu saraksts
 * secībā sākot no mazākajiem un mazāk nozīmīgajiem līdz svarīgākajiem,
 * piem 20 draugi jāliek pirms 50.
 * lietotājs pats pēc tam varēs pārkārtot, ja gribēs.
 * profilā rādīs tikai svarīgākos.
 *
 * speciālos - piem veterāns vai ala te neliksim, tos varēs piešķirt manuāli,
 * ievietojot ierakstu db tabulā, šeit tikai tos, kurus updato automātiski
 */
function list_awards() {
	return array(
		'first-post' => array(
			'title' => 'Pirmie 5 posti ;)',
			'state' => 'inactive'
		),
		'avatar-have' => array(
			'title' => 'Uzlika sev avataru',
			'state' => 'inactive'
		),
		'group-created' => array(
			'title' => 'Izveidoja grupu',
			'state' => 'inactive'
		),
		'popular' => array(
			'title' => 'Populārs (apskatīja 100 biedri)',
			'state' => 'inactive'
		),
		'ingroup-5' => array(
			'title' => '5 grupu biedrs',
			'state' => 'inactive'
		),
		'group-100' => array(
			'title' => 'Izveidoja grupu ar 100 biedriem',
			'state' => 'inactive'
		),
		'friends-20' => array(
			'title' => 'Sadraudzējās ar 20 lietotājiem',
			'state' => 'inactive'
		),
		'friends-50' => array(
			'title' => 'Sadraudzējās ar 50 lietotājiem',
			'state' => 'inactive'
		),
		'gallery' => array(
			'title' => 'Ievietoja bildi galerijā',
			'state' => 'inactive'
		),
		'blog-have' => array(
			'title' => 'Ieguva blogu',
			'state' => 'inactive'
		),
		'messages-100' => array(
			'title' => 'Nosūtīja 100 vēstules',
			'state' => 'inactive'
		),
		'topics-20' => array(
			'title' => 'Izveidoja 20 diskusijas',
			'state' => 'inactive'
		),
		'blogcom-100' => array(
			'title' => '100 komentāri tavā blogā',
			'state' => 'inactive'
		),
		'game-pages-1' => array(
			'title' => 'Uzrakstīja vienas spēles apskatu',
			'state' => 'inactive'
		),
		'game-pages-5' => array(
			'title' => 'Uzrakstīja 5 spēļu apskatus',
			'state' => 'inactive'
		),
		'game-pages-10' => array(
			'title' => 'Uzrakstīja 10 spēļu apskatus',
			'state' => 'inactive'
		),
		'rs-pages-1' => array(
			'title' => 'Uzrakstīja 1 rakstu <a href="//exs.lv/runescape" title="RuneScape">RS</a> sadaļā',
			'state' => 'inactive'
		),
		'rs-pages-5' => array(
			'title' => 'Uzrakstīja 5 rakstus <a href="//exs.lv/runescape" title="RuneScape">RS</a> sadaļā',
			'state' => 'inactive'
		),
		'rs-pages-10' => array(
			'title' => 'Uzrakstīja 10 rakstus <a href="//exs.lv/runescape" title="RuneScape">RS</a> sadaļā',
			'state' => 'inactive'
		),
		'film-pages-1' => array(
			'title' => 'Uzrakstīja vienu filmas apskatu',
			'state' => 'inactive'
		),
		'film-pages-5' => array(
			'title' => 'Uzrakstīja 5 filmu apskatus',
			'state' => 'inactive'
		),
		'film-pages-10' => array(
			'title' => 'Uzrakstīja 10 filmu apskatus',
			'state' => 'inactive'
		),
		'music-pages-1' => array(
			'title' => 'Raksts <a href="//exs.lv/muzika">mūzikas</a> sadaļā',
			'state' => 'inactive'
		),
		'music-pages-5' => array(
			'title' => '5 raksti <a href="//exs.lv/muzika">mūzikas</a> sadaļā',
			'state' => 'inactive'
		),
		'music-pages-10' => array(
			'title' => '10 raksti <a href="//exs.lv/muzika">mūzikas</a> sadaļā',
			'state' => 'inactive'
		),
		'history-pages-1' => array(
			'title' => 'Uzrakstīja rakstu vēstures sadaļā',
			'state' => 'inactive'
		),
		'history-pages-5' => array(
			'title' => 'Uzrakstīja 5 rakstus vēstures sadaļā',
			'state' => 'inactive'
		),
		'history-pages-10' => array(
			'title' => 'Uzrakstīja 10 rakstus vēstures sadaļā',
			'state' => 'inactive'
		),
		'news-1' => array(
			'title' => 'Uzrakstīja vienu jaunumu rakstu',
			'state' => 'inactive'
		),
		'news-5' => array(
			'title' => 'Uzrakstīja 5 jaunumu rakstus',
			'state' => 'inactive'
		),
		'news-15' => array(
			'title' => 'Uzrakstīja 15 jaunumu rakstus',
			'state' => 'inactive'
		),
		'daily-first' => array(
			'title' => 'Dienas aktīvākais postotājs',
			'state' => 'inactive'
		),
		'daily-first-5' => array(
			'title' => 'Dienas aktīvākais 5 reizes',
			'state' => 'inactive'
		),
		'miniblog-10' => array(
			'title' => '10 ieraksti miniblogā',
			'state' => 'inactive'
		),
		'miniblog-100' => array(
			'title' => '100 ieraksti miniblogā',
			'state' => 'inactive'
		),
		'miniblog-1000' => array(
			'title' => '1000 ieraksti miniblogā',
			'state' => 'inactive'
		),
		'miniblog-10000' => array(
			'title' => '10000 ieraksti miniblogā',
			'state' => 'inactive'
		),
		'miniblog-r-100' => array(
			'title' => 'Izveidoja MB ar 100 atbildēm',
			'state' => 'inactive'
		),
		'best-pages' => array(
			'title' => 'Augsti novērtēti autora raksti',
			'state' => 'inactive'
		),
		'exs-cup' => array(
			'title' => 'Uzvarēja rakstu konkursā',
			'state' => 'inactive'
		),
		'desas' => array(
			'title' => 'Uzvarēja 25 <a href="//exs.lv/desas">desu</a> partijas',
			'state' => 'inactive'
		),
		'mta-user' => array(
			'title' => '<a href="//rp.exs.lv/">rp.exs.lv lietotājs</a>',
			'state' => 'inactive'
		),
		'coding-user' => array(
			'title' => '<a href="http://coding.lv/">coding.lv lietotājs</a>',
			'state' => 'inactive'
		),
		'lol-exs-lv' => array(
			'title' => '<a href="//lol.exs.lv/">lol.exs.lv lietotājs</a>',
			'state' => 'inactive'
		),
		'runescape-exs-lv' => array(
			'title' => '<a href="//runescape.exs.lv/">runescape.exs.lv lietotājs</a>',
			'state' => 'inactive'
		),
		'mobile' => array(
			'title' => 'Apmeklēja m.exs.lv',
			'state' => 'inactive'
		),
		'android' => array(
			'title' => '<a href="https://play.google.com/store/apps/details?id=lv.exs.android" rel="nofollow">exs.lv android lietotājs</a>',
			'state' => 'inactive'
		),
		'draugiem-follow' => array(
			'title' => 'Draugiem.lv sekotājs',
			'state' => 'inactive'
		),
		'facebook-like' => array(
			'title' => 'Facebook.com <a href="https://www.facebook.com/exs.lv" rel="nofollow">like</a>',
			'state' => 'inactive'
		),
		'twitter-follower' => array(
			'title' => 'Twitter.com <a href="https://twitter.com/exs_lv" rel="nofollow">sekotājs</a>',
			'state' => 'inactive'
		),
		'lastfm' => array(
			'title' => '<a href="https://exs.lv/lastfm">LastFM</a> lietotājs',
			'state' => 'inactive'
		),
		'steam' => array(
			'title' => '<a href="https://exs.lv/steam-online">Steam</a> lietotājs',
			'state' => 'inactive'
		),
		'blogs-50' => array(
			'title' => 'Veica 50 bloga ierakstus',
			'state' => 'inactive'
		),
		'polls-50' => array(
			'title' => 'Atbildēja 50 aptaujās',
			'state' => 'inactive'
		),
		'karma-20' => array(
			'title' => 'Karmas zaķis (20)',
			'state' => 'inactive'
		),
		'karma-100' => array(
			'title' => 'Karmena (100)',
			'state' => 'inactive'
		),
		'karma-500' => array(
			'title' => 'Karmas iemiesojums (500)',
			'state' => 'inactive'
		),
		'karma-1000' => array(
			'title' => 'Karma Whore (1000)',
			'state' => 'inactive'
		),
		'karma-2000' => array(
			'title' => 'How about a nice cup of karma? (2000)',
			'state' => 'inactive'
		),
		'karma-5000' => array(
			'title' => 'Alus no Maadinsh (Karma 5000)',
			'state' => 'inactive'
		),
		'online-7days' => array(
			'title' => '7 dienas online',
			'state' => 'inactive'
		),
		'online-30days' => array(
			'title' => '30 dienas online',
			'state' => 'inactive'
		),
		'online-100days' => array(
			'title' => '100 dienas online',
			'state' => 'inactive'
		),
		'online-year' => array(
			'title' => 'Gads online',
			'state' => 'inactive'
		),
		'online-year-2' => array(
			'title' => '2 gadi online',
			'state' => 'inactive'
		),
		'online-year-3' => array(
			'title' => '3 gadi online',
			'state' => 'inactive'
		),
		'thumbs-up-100' => array(
			'title' => 'Atzītais (saņēma 100 plusiņus)',
			'state' => 'inactive'
		),
		'thumbs-up' => array(
			'title' => 'Ievērotais (saņēma 1000 plusiņus)',
			'state' => 'inactive'
		),
		'plus' => array(
			'title' => '10 plusi vienam komentāram',
			'state' => 'inactive'
		),
		'mentioned' => array(
			'title' => '@pieminēts 10 reizes',
			'state' => 'inactive'
		),
		'positive' => array(
			'title' => 'Pozitīvais (vērtēja citus +100)',
			'state' => 'inactive'
		),
		'active-poster' => array(
			'title' => 'Aktīvais postotājs (5 posti dienā)',
			'state' => 'inactive'
		),
		'savejais' => array(
			'title' => 'Savējais (aktīvs 1000 dienas)',
			'state' => 'inactive'
		),
		'hangman' => array(
			'title' => '<a href="//exs.lv/karatavas">Karātavu</a> dienas uzvarētājs',
			'state' => 'inactive'
		)
	);
}

/**
 * Atrod visus lietotāja apbalvojumus
 */
function get_awards($user) {
	global $db;
	$user = (int) $user;
	$ret = $db->get_results("SELECT `id`,`award`,`title`,`created`,`importance` FROM `autoawards` WHERE `user_id` = $user ORDER BY `importance` DESC", 0);
	if ($ret) {
		return $ret;
	} else {
		return array();
	}
}

/**
 * Atrod visus lietotāja apbalvojumus (array)
 */
function get_awards_list($user) {
	global $db;
	$user = (int) $user;
	$ret = (array) $db->get_col("SELECT `award` FROM `autoawards` WHERE `user_id` = $user ORDER BY `importance` DESC");
	if ($ret) {
		return $ret;
	} else {
		return array();
	}
}

/**
 * Apbalvojumu piešķiršana
 */
function update_awards($user) {

	global $db, $m, $img_server;
	$user = (int) $user;
	$awards_list = list_awards();
	$existing_awards = get_awards_list($user);

	$userr = get_user($user, true);
	if (!$userr) {
		return false;
	}

	//ja lietotajs nav redzets 6 menesus, nemaz necensamies vinjam updatot medaļas, ienāks - saņems
	if ($userr->lastseen > date('Y-m-d H:i:s', time() - 15778463)) {
		$karma = $userr->karma;
		if ($karma >= 20) {
			$awards_list['karma-20']['state'] = 'active';
		}
		if ($karma >= 100) {
			$awards_list['karma-100']['state'] = 'active';
		}
		if ($karma >= 500) {
			$awards_list['karma-500']['state'] = 'active';
		}
		if ($karma >= 1000) {
			$awards_list['karma-1000']['state'] = 'active';
		}
		if ($karma >= 2000) {
			$awards_list['karma-2000']['state'] = 'active';
		}
		if ($karma >= 5000) {
			$awards_list['karma-5000']['state'] = 'active';
		}

		if (!in_array('draugiem-follow', $existing_awards) && !empty($userr->draugiem_id)) {
			if ($db->get_var("SELECT count(*) FROM `draugiem_followers` WHERE `id` = '$userr->draugiem_id'")) {
				$awards_list['draugiem-follow']['state'] = 'active';
			}
		}

		if ($userr->posts >= 5) {
			$awards_list['first-post']['state'] = 'active';
		}

		if (!empty($userr->lastfm_username)) {
			$awards_list['lastfm']['state'] = 'active';
		}

		if (!empty($userr->steam_id)) {
			$awards_list['steam']['state'] = 'active';
		}

		if (!empty($userr->avatar) && $userr->avatar != 'none.png') {
			$awards_list['avatar-have']['state'] = 'active';
		}

		if (!in_array('news-15', $existing_awards) && $userr->posts > 5) {
			$news = $db->get_var("SELECT count(*) FROM pages WHERE author = '$user' AND category = '1'");
			if ($news >= 1) {
				$awards_list['news-1']['state'] = 'active';
			}
			if ($news >= 5) {
				$awards_list['news-5']['state'] = 'active';
			}
			if ($news >= 15) {
				$awards_list['news-15']['state'] = 'active';
			}
		}

		if (!in_array('miniblog-10000', $existing_awards) && $userr->posts > 5) {
			$miniblog = $db->get_var("SELECT count(*) FROM `miniblog` WHERE `author` = '$user' AND `removed` = '0'");
			if ($miniblog >= 10) {
				$awards_list['miniblog-10']['state'] = 'active';
			}
			if ($miniblog >= 100) {
				$awards_list['miniblog-100']['state'] = 'active';
			}
			if ($miniblog >= 1000) {
				$awards_list['miniblog-1000']['state'] = 'active';
			}
			if ($miniblog >= 10000) {
				$awards_list['miniblog-10000']['state'] = 'active';
			}
		}

		if (!in_array('miniblog-r-100', $existing_awards) && $userr->posts > 5) {
			if ($db->get_var("SELECT count(*) FROM `miniblog` WHERE `author` = '$user' AND `removed` = '0' AND `posts` >= 100")) {
				$awards_list['miniblog-r-100']['state'] = 'active';
			}
		}

		if (!in_array('mta-user', $existing_awards)) {
			if ($db->get_var("SELECT count(*) FROM `userlogs` WHERE `user` = '$user' AND `lang` = 5") >= 10) {
				$awards_list['mta-user']['state'] = 'active';
			}
		}

		if (!in_array('lol-exs-lv', $existing_awards)) {
			if ($db->get_var("SELECT count(*) FROM `userlogs` WHERE `user` = '$user' AND `lang` = 7") >= 10) {
				$awards_list['lol-exs-lv']['state'] = 'active';
			}
		}

		if (!in_array('coding-user', $existing_awards)) {
			if ($db->get_var("SELECT count(*) FROM `userlogs` WHERE `user` = '$user' AND `lang` = 3") >= 10) {
				$awards_list['coding-user']['state'] = 'active';
			}
		}

		if (!in_array('runescape-exs-lv', $existing_awards)) {
			if ($db->get_var("SELECT count(*) FROM `userlogs` WHERE `user` = '$user' AND `lang` = 9") >= 10) {
				$awards_list['runescape-exs-lv']['state'] = 'active';
			}
		}

		if (!in_array('group-created', $existing_awards)) {
			$group_created = $db->get_var("SELECT count(*) FROM clans WHERE owner = '$user'");
			if ($group_created) {
				$awards_list['group-created']['state'] = 'active';
			}
		} else {
			$group_created = 1;
		}

		if (!in_array('ingroup-5', $existing_awards)) {
			$ingroups = $db->get_var("SELECT count(*) FROM clans_members WHERE user = '$user' AND approve = '1'");
			if ($ingroups + $group_created >= 5) {
				$awards_list['ingroup-5']['state'] = 'active';
			}
		}

		if (!in_array('group-100', $existing_awards)) {
			$group_100 = $db->get_var("SELECT count(*) FROM clans WHERE owner = '$user' AND members >= 99");
			if ($group_100) {
				$awards_list['group-100']['state'] = 'active';
			}
		}

		//draugi
		if (!in_array('friends-50', $existing_awards)) {
			$fcount = $db->get_var("SELECT count(*) FROM `friends` WHERE `friend1` = '$user' AND `confirmed` = 1") + $db->get_var("SELECT count(*) FROM `friends` WHERE `friend2` = '$user' AND `confirmed` = 1");
			if ($fcount >= 20) {
				$awards_list['friends-20']['state'] = 'active';
			}
			if ($fcount >= 50) {
				$awards_list['friends-50']['state'] = 'active';
			}
		}

		if (!in_array('gallery', $existing_awards)) {
			$gallery = $db->get_var("SELECT count(*) FROM images WHERE uid = '$user'");
			if ($gallery) {
				$awards_list['gallery']['state'] = 'active';
			}
		}

		if (!in_array('popular', $existing_awards) && $userr->posts > 1) {
			$views = $db->get_var("SELECT count(*) FROM `viewprofile` WHERE `profile` = '$user'");
			if ($views >= 100) {
				$awards_list['popular']['state'] = 'active';
			}
		}

		if (!in_array('messages-100', $existing_awards)) {
			$messages_100 = $db->get_var("SELECT count(*) FROM `pm` WHERE from_uid = '$user'");
			if ($messages_100 >= 100) {
				$awards_list['messages-100']['state'] = 'active';
			}
		}

		if (!in_array('mentioned', $existing_awards)) {
			if ($db->get_var("SELECT count(*) FROM `notify` WHERE `user_id` = '$user' AND `type` IN(13,14,15,16)") >= 10) {
				$awards_list['mentioned']['state'] = 'active';
			}
		}

		if (!in_array('topics-20', $existing_awards) && $userr->posts > 5) {
			$topics_20 = $db->get_var("SELECT count(*) FROM `pages` WHERE author = '$user'");
			if ($topics_20 >= 20) {
				$awards_list['topics-20']['state'] = 'active';
			}
		}

		//medaļas par noteiktu rakstu skaitu kādā kategorijā
		// 'award title' => array(CATEGORY IDS)
		$topic_awards = array(
			'game' => array(81, 603),
			'music' => array(323),
			'film' => array(80),
			'history' => array(565),
			'rs' => array(4, 5, 99, 100, 160, 193, 194, 195, 599, 789, 791, 792)
		); // prasmes,padomi,f2p,p2p,minisp,minikv,tasks,celvezi,rs-zinas,stasti&vesture,gildes,d&d

		$topic_award_levels = array(1, 5, 10);

		foreach ($topic_awards as $key => $val) {
			if (!in_array($key . '-pages-10', $existing_awards) && $userr->posts > 3) {
				$game_pages = $db->get_var("SELECT count(*) FROM `pages` WHERE `author` = '$user' AND `category` IN(" . implode(',', $val) . ")");
				foreach ($topic_award_levels as $level) {
					if ($game_pages >= $level) {
						$awards_list[$key . '-pages-' . $level]['state'] = 'active';
					}
				}
			}
		}

		if (!in_array('thumbs-up', $existing_awards) && $userr->karma > 10) {
			$pcom = $db->get_var("SELECT SUM(`vote_value`) FROM `comments` WHERE `author` = '$user'");
			$gcom = $db->get_var("SELECT SUM(`vote_value`) FROM `galcom` WHERE `author` = '$user'");
			$mbvt = $db->get_var("SELECT SUM(`vote_value`) FROM `miniblog` WHERE `author` = '$user'");
			if (($pcom + $gcom + $mbvt) > 99) {
				$awards_list['thumbs-up-100']['state'] = 'active';
			}
			if (($pcom + $gcom + $mbvt) > 999) {
				$awards_list['thumbs-up']['state'] = 'active';
			}
		}

		if (!in_array('plus', $existing_awards) && $userr->posts > 1) {

			$plus = $db->get_var("SELECT `id` FROM `miniblog` WHERE `author` = '$user' AND `vote_value` >= 10 LIMIT 1");
			if (!$plus) {
				$plus = $db->get_var("SELECT `id` FROM `comments` WHERE `author` = '$user' AND `vote_value` >= 10 LIMIT 1");
			}
			if (!$plus) {
				$plus = $db->get_var("SELECT `id` FROM `galcom` WHERE `author` = '$user' AND `vote_value` >= 10 LIMIT 1");
			}
			if ($plus) {
				$awards_list['plus']['state'] = 'active';
			}
		}

		if ($userr->days_in_row >= 7 || ($userr->days_in_row >= 6 && $userr->seen_today == 1)) {
			$awards_list['online-7days']['state'] = 'active';

			if ($userr->days_in_row >= 30 || ($userr->days_in_row >= 29 && $userr->seen_today == 1)) {
				$awards_list['online-30days']['state'] = 'active';

				if ($userr->days_in_row >= 100 || ($userr->days_in_row >= 99 && $userr->seen_today == 1)) {
					$awards_list['online-100days']['state'] = 'active';

					if ($userr->days_in_row >= 365 || ($userr->days_in_row >= 364 && $userr->seen_today == 1)) {
						$awards_list['online-year']['state'] = 'active';
						
						if ($userr->days_in_row >= 730 || ($userr->days_in_row >= 729 && $userr->seen_today == 1)) {
							$awards_list['online-year-2']['state'] = 'active';

							if ($userr->days_in_row >= 1096 || ($userr->days_in_row >= 1095 && $userr->seen_today == 1)) {
								$awards_list['online-year-3']['state'] = 'active';
							}
						}
					}
				}
			}
		}

		if ($userr->vote_others > 99) {
			$awards_list['positive']['state'] = 'active';
		}

		if ($userr->mobile_seen == 1) {
			$awards_list['mobile']['state'] = 'active';
		}
		
		if ($userr->android_seen == 1) {
			$awards_list['android']['state'] = 'active';
		}

		if ($userr->year_first == 1) {
			$awards_list['year-first']['state'] = 'active';
			$awards_list['year-first']['title'] = 'Iepostoja gada 1. minūtē';
		}

		if (!in_array('best-pages', $existing_awards) && $userr->karma > 10) {
			//augsti novērtēti raksti
			$ratings = $db->get_var("SELECT count(*) FROM `pages` WHERE rating_count > 15 AND (rating/rating_count) > 4 AND author = '$user'");
			if ($ratings >= 3) {
				$awards_list['best-pages']['state'] = 'active';
			}
		}

		//blog
		if ($blog_have = get_blog_by_user($user)) {
			$awards_list['blog-have']['state'] = 'active';
			$blogs = $db->get_var("SELECT count(*) FROM `pages` WHERE `category` = '$blog_have'");
			if ($blogs >= 50) {
				$awards_list['blogs-50']['state'] = 'active';
			}
			//blogcom-100
			$blogcom = $db->get_var("SELECT SUM(`posts`) FROM `pages` WHERE `author` = '$user' AND `category` = '$blog_have'");
			if ($blogcom > 99) {
				$awards_list['blogcom-100']['state'] = 'active';
			}
		}

		if (!in_array('active-poster', $existing_awards)) {
			$days = ceil((time() - strtotime($userr->date)) / 60 / 60 / 24);
			$posts = $userr->posts / $days;
			if ($days >= 30 && $posts >= 5) {
				$awards_list['active-poster']['state'] = 'active';
			}
		}

		//50 aptaujas
		if (!in_array('polls-50', $existing_awards)) {
			if ($db->get_var("SELECT count(*) FROM `responses` WHERE `user_id` = '$user'") >= 50) {
				$awards_list['polls-50']['state'] = 'active';
			}
		}

		//dienas spameris
		if (!in_array('daily-first-5', $existing_awards)) {
			$first = $db->get_var("SELECT `daily_first` FROM `users` WHERE `id` = '$user'");
			if ($first >= 1) {
				$awards_list['daily-first']['state'] = 'active';
			}
			if ($first >= 5) {
				$awards_list['daily-first-5']['state'] = 'active';
			}
		}

		//dienas hangman
		if (!in_array('hangman', $existing_awards)) {
			if ($db->get_var("SELECT count(*) FROM users WHERE id = '$user' AND daily_hangman > 0")) {
				$awards_list['hangman']['state'] = 'active';
			}
		}

		//desas
		if (!in_array('desas', $existing_awards)) {

			$cnt = $db->get_var("SELECT count(*) FROM `desas` WHERE `user_1` = '$user' AND `winner` = '1'") + $db->get_var("SELECT count(*) FROM `desas` WHERE `user_2` = '$user' AND `winner` = '2'");
			if ($cnt >= 25) {
				$awards_list['desas']['state'] = 'active';
			}
		}

		//savējais
		if (!in_array('savejais', $existing_awards)) {
			if ($db->get_var("SELECT count(*) FROM users WHERE id = '$user' AND karma > 99 AND DATEDIFF(lastseen,date) > 999 AND lastseen != '0000-00-00 00:00:00'")) {
				$awards_list['savejais']['state'] = 'active';
			}
		}

		$user_age = user_age($userr->date);
		for ($i = 1; $i < 13; $i++) {
			if ($user_age >= $i) {
				$awards_list['cake-' . $i] = array(
					'title' => $i . ' ' . lv_dsk($i, 'gads', 'gadi') . ' exā ;)',
					'state' => 'active'
				);
			}
		}
	}

	//custom medalas. atkomentēt pēc vajadzības un updatot tiem lietotajiem. ja masīvā nav izmaiņu, nav ko lieki trenkāt procesoru :crazy:
	/*
	  if(in_array($user,array(2,140,325,543,1822,2324,2339,3650,3962,4711,6001,8531,8872,9048,9247,12605,14911,16267,21600))) {
	  $awards_list['exs-cup']['state'] = 'active';
	  } */

	/*if (in_array($user, array(25709,1621,4506,7272,5969,23583,27552,12108,16705,23107,24437,1306,5965,1,34877,4432,22051,23282,16261,11807,655,2145,15978))) {
		$awards_list['exs-party-2015'] = array(
			'title' => 'Exs.lv ballīte 2015',
			'state' => 'active'
		);
	}*/
	
	// florbola turnīrs
	/*if (in_array($user, array(115,1351,1621,1822,2145,2222,2357,3650,5056,5205,5876,6963,8096,10065,10595,10734,11525,11722,11807,
			12108,12304,12382,13004,14782,15390,16261,18057,19604,20858,21450,22518,23282,23512,24437,
			24998,25299,25385,31410,31919,36660,37513,39175))) {

		$awards_list['floorball-4'] = array(
			'title' => 'Exs 4. florbola turnīrs 05.03.2016',
			'state' => 'active'
		);
	}*/

	//ghetto games floorball
	/* if (in_array($user, array(1822, 12382, 21450, 13004, 22518, 24437, 273, 11722, 19604, 23282, 6446, 10492))) {
	  $awards_list['ghetto-floorball'] = array(
	  'title' => 'Piedalījās Ghetto Games (florbolā)',
	  'state' => 'active'
	  );
	  } */

	//ghetto games football
	/* if (in_array($user, array(1822, 13004, 858, 23282, 23715, 21450))) {
	  $awards_list['ghetto-football'] = array(
	  'title' => 'Piedalījās Ghetto Games (futbolā)',
	  'state' => 'active'
	  );
	  } */

	foreach ($awards_list as $key => $val) {
		if ($val['state'] === 'active') {
			//ja lietotājam jau ir šāds awards, neko nedaram
			if (!in_array($key, $existing_awards)) {
				$db->query("INSERT INTO `autoawards` (user_id,award,title,created) VALUES ('$user','$key','" . $val['title'] . "',NOW())");
				$db->update('autoawards', $db->insert_id, array('importance' => $db->insert_id));
				userlog($user, 'Ieguva medaļu &quot;' . $val['title'] . '&quot;', $img_server . '/dati/bildes/awards/' . $key . '.png');
				notify($user, 7);
				$m->delete('aw_' . $user);
				// 6 - pēdējie 6 svarīgākie apbalvojumi (tik daudz rāda lietotnes profilos)
				$m->delete('android_awards_'.$user.'-6');
			}
		}
	}
}
