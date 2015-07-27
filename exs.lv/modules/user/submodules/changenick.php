<?php

/**
 * Nika maiņa
 */

$robotstag[] = 'noindex';

deny_proxies();

if (isset($_POST['new-nick']) && check_token('changenick', $_POST['xsrf_token'])) {

	if ($inprofile->credit < 5) {
		set_flash('Nepietiek exs.lv kredīta!', 'error');
		redirect('/user/changenick');
	}

	$slugnick = mkslug($_POST['new-nick']);

	if (strlen(trim($_POST['new-nick'])) >= 3 && strlen(trim($_POST['new-nick'])) <= 16 && !empty($slugnick)) {
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
		set_flash('Niks neatbilst atļautajam garumam (3-16 simboli)!', 'error');
	}
}

$tpl->newBlock('user-profile-changenick');
$tpl->assign(array(
	'user-credit' => $inprofile->credit,
	'xsrf' => make_token('changenick')
));

$page_title = 'Exs.lv nika maiņa';

