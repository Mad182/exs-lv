<?php

$mtadb = new mdb($mta_username, $mta_password, $mta_database, $mta_hostname);

$admins = $mtadb->get_results("SELECT `username`,`admin`,`adminreports`,`lastlogin` FROM `accounts` WHERE `admin` != 0 ORDER BY `admin` DESC");

if(!empty($admins)) {

	$module_content = '

	<table class="main-table">
		<tr>
			<th>Admins</th>
			<th>Līmenis</th>
			<th>Reporti</th>
			<th>Pēdējo reizi redzēts</th>
		</tr>
	';

	foreach($admins as $admin) {
		$module_content .= '<tr>';
		$module_content .= '	<td>' . $admin->username . '</td>';
		$module_content .= '	<td>' . $admin->admin . '</td>';
		$module_content .= '	<td>' . $admin->adminreports . '</td>';
		$module_content .= '	<td>' . $admin->lastlogin . '</td>';
		$module_content .= '</tr>';
	}
	
	$module_content .= '</table>';

} else {
	$module_content = '<div class="form"><p class="error"><strong>Kļūda!</strong><br />Nevar savienoties ar MTA serveri...</p></div>';
}

