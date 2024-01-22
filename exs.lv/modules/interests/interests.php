<?php

if (!$auth->ok) {
	redirect();
}



if (isset($_POST['submit-interests'])) {
	$db->query("DELETE FROM `user_interests` WHERE `user_id` = '$auth->id'");
	foreach ($_POST['interests'] as $interest) {
		$interest = (int) $interest;
		if ($db->get_var("SELECT count(*) FROM `interests` WHERE `id` = '$interest'")) {

			$db->query("INSERT INTO `user_interests` (`user_id`, `interest_id`) VALUES ('$auth->id', '$interest')");
		}
	}
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

