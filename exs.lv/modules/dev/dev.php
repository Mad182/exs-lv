<?php
exit;
$folder = 'exs_2013_party';
$user_id = 30912;


if ($handle = opendir('dati/bildes/'.$folder.'/full/')) {
	while (false !== ($file = readdir($handle))) {
		if ($file != "." && $file != "..") {
			
			$image = sanitize('dati/bildes/'.$folder.'/full/'.$file);	
			$thb = sanitize('dati/bildes/'.$folder.'/thb/'.$file);	
			$text = sanitize('<p>'.str_replace('.jpg','',$file).' (autors @Cauzey)</p>');

			remake_thb($image, $thb);

			$sql = "

			INSERT INTO `images` (uid,url,thb,text,date,bump,ip,lang,interest_id) VALUES

				('$user_id','$image','$thb','$text',NOW(),0,'127.0.0.1',1,11)
			";

			$db->query($sql);

			userlog($user_id,'Pievienoja <a href="/gallery/' .$user_id . '/' . $db->insert_id . '">jaunu attēlu ' . textlimit(strip_tags($text), 32, '...') . '</a>', '/' . $thb);

			echo $sql . '<br />';
		}
	}
	closedir($handle);
}
