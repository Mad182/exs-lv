<?php

if (!$auth->ok || !im_mod()) {
	redirect();
	exit;
}

// jquery ielādē izvēlētā lietotāja vecos nikus
if (isset($_GET['display']) && is_numeric($_GET['display'])) {
	
	$userid = (int)$_GET['display'];
	$content = '';
				
	$user = $db->get_row("SELECT `id`,`nick`,`pwd`,`level` FROM `users` WHERE `id` = '".$userid."' ");
	if ($user) {
		
		// atrod vecos lietotājvārdus
		$usernames = $db->get_results("SELECT `user_id` AS `id`,`nick`,`changed` FROM `nick_history` WHERE `user_id` = '".$user->id."' ORDER BY `changed` DESC ");
		if ($usernames) {
			$content .= '<p><strong>Iepriekšējie lietotājvārdi:</strong><br />';
			foreach ($usernames as $uname) {
				$uname->changed = date("d.m.Y, H:i",strtotime($uname->changed));
				$content .= '<a href="/user/'.$uname->id.'">'.$uname->nick.'</a> (mainīts: '.$uname->changed.')<br />';
			}
			$content .= '</p>';
		} else {
			$content .= '<p style="font-weight:bold;">Šis lietotājs savu lietotājvārdu pēdējā laikā nav mainījis!</p>';
		}
			
		// veic salīdzināšanu, vai paroles hash sakrīt ar kādu citu
		if (strlen($user->pwd) > 5 && !in_array($user->pwd,array('',' '))) {
			$pass = $db->get_results("SELECT `id`,`nick`,`lastseen`,`level` FROM `users` WHERE `pwd` LIKE '%".$user->pwd."%' AND `id` != '".$user->id."' ORDER BY `nick` ASC ");		
			if ($pass) {
				$content .= '<p><strong>Parole sakrīt ar šādiem lietotājiem:</strong><br />';
				foreach ($pass as $pwd) {					
					$pwd->lastseen = time_ago(strtotime($pwd->lastseen));
					//$content .= '<a href="/user/'.$pwd->id.'">'.$pwd->nick.'</a>&nbsp;&nbsp;&nbsp;pēdējoreiz manīts pirms '.$pwd->lastseen.'<br />';
					$pwd->nick = usercolor($pwd->nick, $pwd->level, false, $pwd->id);
					$content .= '<a href="/user/'.$pwd->id.'">'.$pwd->nick.'</a>&nbsp;&nbsp;&nbsp;manīts pirms '.$pwd->lastseen.'<br />';
				}
				$content .= '</p>';
			} else $content .= '<p><strong>Parole ne ar vienu lietotāju nesakrīt.<strong></p>';
			
		// jāpārbauda vecā parole
		} else {
			$content .= '<p><strong>Parole ne ar vienu lietotāju nesakrīt.<strong></p>';
		}
		
		echo $content;
	}
	
	exit;
}




$tpl->assignInclude('module-head', CORE_PATH . '/modules/' . $category->module . '/head.tpl');
$tpl->prepare();
//$skinid = ($auth->skin == 1) ? 'dark' : 'light';
$tpl->assign('skinid', 'light');

$tpl->newBlock('mod-cpanel');

if (isset($_POST['submit'])) {

	// meklēšana pēc lietotāja nika
	if (isset($_POST['nick']) && strlen($_POST['nick']) >= 3) {
		$field 		= 'nick';
		$criteria 	= '`nick` LIKE \'%'.sanitize($_POST['nick']).'%\'';
		$tpl->assign('niks',$_POST['nick']);
		
	// meklēšana pēc e-pasta
	} else if (isset($_POST['mail']) && strlen($_POST['mail']) >= 3) {
		$field 		= 'mail';
		$criteria 	= '`mail` LIKE \'%'.sanitize($_POST['mail']).'%\'';
		$tpl->assign('mails',$_POST['mail']);
		
	// meklēšana pēc pēdējās lietotājs IP adreses
	} else if (isset($_POST['ip']) && strlen($_POST['ip']) >= 3) {
		$field 		= 'ip';
		$criteria 	= '`lastip` LIKE \'%'.sanitize($_POST['ip']).'%\'';
		$tpl->assign('aipii',$_POST['ip']);

	// kļūdu gadījumā
	} else {
		$criteria 	= '1';
		$field 		= '';
	}	

	
	$results 	= $db->get_results("SELECT `id`,`nick`,`mail`,`lastip`,`karma`,`date` FROM `users` WHERE ".$criteria." ORDER BY ABS(`level`) DESC, `nick` ASC LIMIT 0,50");
	
	if ($results) {
	
		$tpl->newBlock('search-results');
		
		foreach ($results as $res) {

			$res->date = ceil((time() - strtotime($res->date)) / 60 / 60 / 24);		
			if ($field == 'mail') {
				$res->mail = str_replace($_POST['mail'],'<strong>'.$_POST['mail'].'</strong>',$res->mail);
			}
			if (isset($_POST['ip']) && !empty($_POST['ip'])) {
				$res->lastip = str_replace($_POST['ip'],'<strong>'.$_POST['ip'].'</strong>',$res->lastip);
			}
			$res->nick = usercolor($res->nick, $res->level, false, $res->id);
			$tpl->newBlock('search-result');
			$tpl->assignAll($res);
			
		}
	}
}	

	
	

?>