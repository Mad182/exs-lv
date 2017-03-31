<?php

$data = file_get_contents('/home/www/exs.lv/modules/dev/preload.json');

$data = json_decode($data);

foreach($data->entries as $dom) {
	echo "UPDATE `pages` SET `text` = REPLACE(`text`, 'http://".$dom->name."', 'https://".$dom->name."' );<br />";
}


exit;

/*$a = unserialize('a:2:{s:12:"header_image";s:162:"http://ksenija.exs.lv/wp-content/uploads/2010/12/cropped-latrines-cluster-new-with-bright-blue-doors-corrugated-metal-roof-in-slums-of-Dhaka-Bangladesh-1-AJHD.jpg";s:16:"header_textcolor";s:3:"fff";}');

$a['header_image'] = str_replace('http://ksenija.exs.lv','',$a['header_image']);

pr(serialize($a));
exit;*/

$https_sites = get_sitelist('https');

foreach ($https_sites as $site) {
	if(stripos($site, '*') === false) {

		echo "UPDATE `cms_pages` SET `text` = REPLACE(`text`, 'http://".$site."', 'https://".$site."' );<br />";

	}
}





exit;



redirect();
echo get_fb_likes('https://exs.lv/');

exit;
$folder = 'florbols-05-03-2016';
$user_id = 39299;


if ($handle = opendir('dati/bildes/' . $folder . '/large/')) {
	while (false !== ($file = readdir($handle))) {
		if ($file != "." && $file != "..") {

			$image = sanitize('dati/bildes/' . $folder . '/large/' . $file);
			$thb = sanitize('dati/bildes/' . $folder . '/thb/' . $file);
			$text = sanitize('<p>' . strtolower(str_replace(['.JPG', '.jpg'], '', $file)) . " - Exs #4 florbola turnīrs 05.03.2016, Jūrmala</p>");

			remake_thb($image, $thb);

			$sql = "

			INSERT INTO `images` (`uid`,`url`,`thb`,`text`,`date`,`bump`,`ip`,`lang`,`interest_id`) VALUES

				('$user_id','$image','$thb','$text',NOW(),NOW(),'127.0.0.1',1,8)
			";

			//$db->query($sql);

			//userlog($user_id, 'Pievienoja <a href="/gallery/' . $user_id . '/' . $db->insert_id . '">jaunu attēlu ' . textlimit(strip_tags($text), 32, '...') . '</a>', '/' . $thb);

			echo $sql . '<br />';
		}
	}
	closedir($handle);
	update_karma($user_id, true);
	update_karma($user_id, true);
	update_karma($user_id, true);
}

exit;

