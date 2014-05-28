<?php

$mtadb = new mdb($mta_username, $mta_password, $mta_database, $mta_hostname);
$mtadb->query("SET NAMES utf8");

$admins = $mtadb->get_results("SELECT `exs_id`, `username`, `admin`, `adminreports`, `lastlogin`, `responsibleFor` FROM `accounts` WHERE `admin` != 0 ORDER BY `admin` DESC");

if (!empty($admins)) {

	$module_content = '

	<table class="table">
		<tr>
			<th>Admins</th>
			<th>Exs niks</th>
			<th>Līmenis</th>
			<th>Reporti</th>
			<th>Pēdējo reizi redzēts</th>
			<th>Atbildīgs par</th>
		</tr>
	';

	foreach ($admins as $admin) {

		$usr = '';
		if ($exs = get_user($admin->exs_id)) {
			$usr = '<a href="/user/' . $exs->id . '">' . usercolor($exs->nick, $exs->level, false, $exs->id) . '</a>';
		}

		$module_content .= '<tr>';
		$module_content .= '	<td>' . htmlspecialchars($admin->username) . '</td>';
		$module_content .= '	<td>' . $usr . '</td>';
		$module_content .= '	<td>' . (int) $admin->admin . '</td>';
		$module_content .= '	<td>' . (int) $admin->adminreports . '</td>';
		$module_content .= '	<td>pirms ' . time_ago(strtotime($admin->lastlogin)) . '</td>';
		$module_content .= '	<td>' . htmlspecialchars($admin->responsibleFor) . '</td>';
		$module_content .= '</tr>';
	}

	$module_content .= '</table>';
} else {
	$module_content = '<div class="form"><p class="error"><strong>Kļūda!</strong><br />Nevar savienoties ar MTA serveri...</p></div>';
}

