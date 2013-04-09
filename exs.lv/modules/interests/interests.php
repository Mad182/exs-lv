<?php

if (!$auth->ok) {
	redirect();
}

if ($db->get_var("SELECT `interest_quiz` FROM `users` WHERE `id` = '$auth->id'")) {

	if (isset($_POST['submit-interests'])) {
		$db->query("DELETE FROM `user_interests` WHERE `user_id` = '$auth->id'");
		foreach ($_POST['interests'] as $interest) {
			$interest = (int) $interest;
			if ($db->get_var("SELECT count(*) FROM `interests` WHERE `id` = '$interest'")) {

				$db->query("INSERT INTO `user_interests` (`user_id`, `interest_id`) VALUES ('$auth->id', '$interest')");
			}
		}
		$db->query("UPDATE `users` SET `interest_quiz` = 1 WHERE `id` = '$auth->id'");
		redirect('/' . $category->textid);
	}

	$interests = $db->get_results("SELECT * FROM `interests` ORDER BY `id` ASC");
	foreach ($interests as $interest) {
		if (in_array($interest->id, $auth->interests)) {
			$interest->sel = ' checked="checked"';
		}
		$tpl->newBlock('interest');
		$tpl->assignAll($interest);
	}
} else {

	if (isset($_POST['submit-interests'])) {
		foreach ($_POST['interests'] as $interest) {
			$interest = (int) $interest;
			if ($db->get_var("SELECT count(*) FROM `interests` WHERE `id` = '$interest'")) {


				$db->query("INSERT INTO `user_interests` (`user_id`, `interest_id`) VALUES ('$auth->id', '$interest')");


				/*$groups = $db->get_results("SELECT * FROM `clans` WHERE `interest_id` = '$interest'");

				if ($groups) {

					foreach ($groups as $group) {

						if (!$db->get_var("SELECT count(*) FROM `clans_members` WHERE `user` = '$auth->id' AND `clan` = '$group->id'")) {


							$db->query("INSERT INTO `clans_members` (user,clan,approve,date_added) VALUES ('$auth->id','$group->id','$group->auto_approve','" . time() . "')");
							$db->query("UPDATE `clans` SET `members` = '" . $db->get_var("SELECT count(*) FROM clans_members WHERE clan = '$group->id' AND approve = '1'") . "' WHERE id = '$group->id'");
							$url = '/group/' . $group->id;

							//notify($group->owner, 4, $group->id, $url . '/members', $group->title);

							if ($group->id == 53 || $group->id == 89) {
								$db->query("UPDATE `users` SET `show_code` = 1 WHERE `id` = '$auth->id'");
							}
						}
					}
				}*/
			}
		}
		$db->query("UPDATE `users` SET `interest_quiz` = 1 WHERE `id` = '$auth->id'");
		redirect();
	}


	$interests = $db->get_results("SELECT * FROM `interests` ORDER BY `id` ASC");
	foreach ($interests as $interest) {
		if ($interest->default) {
			$interest->sel = ' checked="checked"';
		}
		$tpl->newBlock('interest');
		$tpl->assignAll($interest);
	}
}
