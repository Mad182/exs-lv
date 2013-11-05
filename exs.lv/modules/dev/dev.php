<?php
exit;
$folder = 'florbols-02-11-2013';
$user_id = 32068;


if ($handle = opendir('dati/bildes/'.$folder.'/large/')) {
	while (false !== ($file = readdir($handle))) {
		if ($file != "." && $file != "..") {
			
			$image = sanitize('dati/bildes/'.$folder.'/large/'.$file);	
			$thb = sanitize('dati/bildes/'.$folder.'/thb/'.$file);	
			$text = sanitize('<p>'.str_replace('.jpg','',$file).' - exs.lv 2. florbola turnīrs. 02.11.2013</p>');

			remake_thb($image, $thb);

			$sql = "

			INSERT INTO `images` (uid,url,thb,text,date,bump,ip,lang,interest_id) VALUES

				('$user_id','$image','$thb','$text',NOW(),NOW(),'127.0.0.1',1,8)
			";

			$db->query($sql);

			userlog($user_id,'Pievienoja <a href="/gallery/' .$user_id . '/' . $db->insert_id . '">jaunu attēlu ' . textlimit(strip_tags($text), 32, '...') . '</a>', '/' . $thb);

			echo $sql . '<br />';
		}
	}
	closedir($handle);
	update_karma($user_id, true);
}
