<?php

if (!$auth->ok) {
	redirect();
}



if (isset($_POST['submit-interests'])) {
	$db->query("DELETE FROM `user_interests` WHERE `user_id` = '$auth->id'");
	if (!empty($_POST['interests']) && is_array($_POST['interests'])) {
		$valid_ids = $db->get_col("SELECT `id` FROM `interests`");
		$valid_map = $valid_ids ? array_flip($valid_ids) : [];
		$posted = array_unique(array_map('intval', $_POST['interests']));
		foreach ($posted as $interest) {
			if (isset($valid_map[$interest])) {
				$db->query("INSERT INTO `user_interests` (`user_id`, `interest_id`) VALUES ('$auth->id', '$interest')");
			}
		}
	}
	redirect('/' . $category->textid);
}

$interests = $db->get_results("SELECT * FROM `interests` ORDER BY `id` ASC");
if ($interests) {
	foreach ($interests as $interest) {
		if (in_array($interest->id, $auth->interests)) {
			$interest->sel = ' checked="checked"';
		}
		$tpl->newBlock('interest');
		$tpl->assignAll($interest);
	}
}

