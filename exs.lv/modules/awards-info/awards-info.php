<?php

$out = 'Par šo medaļu informācija nav pieejama!';
$user = null;

if ($auth->ok) {
	$user = get_user($auth->id);
}

if (!empty($user)) {

	switch ($_GET['var1']) {

		case 'karma-20':
		case 'karma-100':
		case 'karma-500':
		case 'karma-1000':
		case 'karma-2000':
		case 'karma-5000':
			$out = 'Šobrīd karma ' . $user->karma;
			break;

		case 'first-post':
			$out = 'Šobrīd posti ' . $user->posts . ', vajadzīgi 5';
			break;

		case 'avatar-have':
			$out = 'Lai iegūtu šo medaļu tev profilā jāuzliek sev avatara attēlu';
			break;

		case 'news-1':
		case 'news-5':
		case 'news-15':
			$out = 'Tev ir ' . $db->get_var("SELECT count(*) FROM pages WHERE author = '$user->id' AND category = '1'") . ' jaunumu raksti';
			break;

		case 'game-pages-1':
		case 'game-pages-5':
			$out = 'Tu esi uzrakstījis ' . $db->get_var("SELECT count(*) FROM pages WHERE author = '$user->id' AND category = '81'") . ' spēļu apskatus';
			break;

		case 'music-pages-1':
		case 'music-pages-5':
			$out = 'Tu esi uzrakstījis ' . $db->get_var("SELECT count(*) FROM pages WHERE author = '$user->id' AND category = '323'") . ' rakstus mūzikas sadaļā';
			break;

		case 'film-pages-1':
		case 'film-pages-5':
			$out = 'Tu esi uzrakstījis ' . $db->get_var("SELECT count(*) FROM pages WHERE author = '$user->id' AND category = '80'") . ' filmu apskatus';
			break;

		case 'rs-pages-1':
		case 'rs-pages-5':
			$out = 'Tu esi uzrakstījis ' . $db->get_var("SELECT count(*) FROM pages WHERE author = '$user->id' AND category IN(599,4,5,99,100,102,160,193,195,194,792,787,788,789,790,791,793)") . ' rakstus runescape sadaļā';
			break;

		case 'miniblog-10':
		case 'miniblog-100':
		case 'miniblog-1000':
		case 'miniblog-10000':
			$out = 'Tev ir ' . $db->get_var("SELECT count(*) FROM `miniblog` WHERE `author` = '$user->id' AND `removed` = '0'") . ' minibloga ieraksti';
			break;

		case 'friends-20':
		case 'friends-50':
			$out = 'Tev ir ' . $db->get_var("SELECT count(*) FROM `friends` WHERE (friend1 = '$user->id' OR friend2 = '$user->id') AND confirmed = 1") . ' draugi';
			break;

		case 'thumbs-up':
		case 'thumbs-up-100':
			$pcom = $db->get_var("SELECT SUM(`vote_value`) FROM `comments` WHERE `author` = '$user->id'");
			$gcom = $db->get_var("SELECT SUM(`vote_value`) FROM `galcom` WHERE `author` = '$user->id'");
			$mbvt = $db->get_var("SELECT SUM(`vote_value`) FROM `miniblog` WHERE `author` = '$user->id'");
			$out = 'Tavs vērtējums: ' . ($pcom + $gcom + $mbvt);
			break;

		case 'online-7days':
		case 'online-30days':
		case 'online-100days':
			$out = 'Šobrīd tu esi bijis online ' . ($user->days_in_row + $user->seen_today) . ' dienas pēc kārtas';
			break;

		case 'mentioned':
			$out = 'Tu esi pieminēts ' . $db->get_var("SELECT count(*) FROM `notify` WHERE `user_id` = '$user->id' AND `type` IN(13,14,15,16)") . ' reizes';
			break;

		case 'desas':
			$out = 'Tu esi uzvarējis desās ' . $db->get_var("SELECT count(*) FROM desas WHERE (user_1 = '$user->id' AND winner = '1') OR (user_2 = '$user->id' AND winner = '2')") . ' reizes';
			break;

		case 'polls-50':
			$out = 'Tu esi nobalsojis ' . $db->get_var("SELECT count(*) FROM responses WHERE user_id = '$user->id'") . ' aptaujās';
			break;

		case 'blogcom-100':
			$blog_have = get_blog_by_user($user->id);
			if ($blog_have) {
				$out = 'Tavā blogā ir ' . $db->get_var("SELECT SUM(`posts`) FROM `pages` WHERE `author` = '$user->id' AND `category` = '$blog_have'") . ' komentāri';
			} else {
				$out = 'Tev nav bloga!';
			}
			break;

		case 'daily-first':
		case 'daily-first-5':
			$out = 'Tu esi bijis dienas aktīvākais postotājs ' . $db->get_var("SELECT `daily_first` FROM `users` WHERE `id` = '$user->id'") . ' reizes';
			break;

		case 'draugiem-follow':
			$out = 'Pievienojies sekotājiem mūsu lapā www.draugiem.lv/exs.lv/ un ielogojies ar draugiem.lv pasi, lai saņemtu šo medaļu!';
			break;

		case 'facebook-like':
			$out = 'Nospied "like" mūsu lapai www.facebook.com/exs.lv/ un ielogojies ar facebook, lai saņemtu šo medaļu!';
			break;

		case 'mc-exs':
			$out = 'Spēlē mc.exs.lv serverī';
			break;

		case 'mta-user':
			$out = 'MTA lietotājs. Aktīvs rp.exs.lv forumā';
			break;

		case 'lol-user':
			$out = 'lol.exs.lv lietotājs. Aktīvs lol.exs.lv forumā';
			break;

		/*
		  case '':
		  $out = '';
		  break;

		 */
	}
} else {
	$out = 'Nav norādīts lietotājs!';
}

die($out);
