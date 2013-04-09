<?php

if ($auth->ok) {

	$have = $db->get_var("SELECT mc_id FROM mc_users WHERE id = '$auth->id'");
	if ($have) {
		set_flash('Tev jau ir piesaistīts mc.exs.lv profils!', 'error');
		redirect('/');
	} else {

		if (isset($_POST['mc_user']) && isset($_POST['mc_pass'])) {
			$usr = $_POST['mc_user'];
			$pwd = $_POST['mc_pass'];

			$response = intval(file_get_contents('http://mc.exs.lv/mc_chk.php?user=' . $usr . '&pass=' . $pwd));

			if ($response > 0) {

				$have_added = $db->get_var("SELECT id FROM mc_users WHERE mc_id = '$response'");

				if ($have_added > 0) {
					sleep(1);
					set_flash('Šis mc profils jau ir piesaistīts kādam exs.lv lietotājam!', 'error');
					redirect('/mc-award');
				} else {
					$db->query("INSERT INTO mc_users (id,mc_id) VALUES ('$auth->id', '$response')");
					set_flash('Viss OK! ;)', 'success');
					update_karma($auth->id, true);
					redirect('/');
				}
			} else {
				sleep(1);
				set_flash('Nepareizs lietotājvārds un/vai parole!', 'error');
				redirect('/mc-award');
			}
		}
		$tpl->newBlock('mc-award');
	}
}

