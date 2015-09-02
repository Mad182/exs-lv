<?php


$clans = $db->get_results("SELECT id from clans");

foreach($clans as $clan) {

	$post = $db->get_var("SELECT MAX(`date`) FROM miniblog WHERE groupid = $clan->id");
	
	
	$db->query("UPDATE clans SET last_activity = '$post' WHERE id = $clan->id");
}

exit;
$number_of_days_from_now = 3299;
$now = time();

$arr_days = array();

$i = 0;
while($i <> $number_of_days_from_now){
	$str_stamp = "- $i day";
	$arr_days[] = date('Y-m-d',strtotime($str_stamp,$now));
	$i ++;
}

foreach($arr_days as $day) {
	$arr = get_todays_top_comment($day);
	if(!empty($arr['best-rating']) && $arr['best-rating'] > 1) {
		echo '<p>'.$day.' <strong><a href="'.$arr['best-link'].'">'.$arr['best-nick']. '</a> ('.$arr['best-rating'].')</strong>: ' . add_smile($arr['best-comment']).'</p>';
	}
}



//get_todays_top_comment();


exit;
$json = curl_get('https://www.googleapis.com/youtube/v3/videos?id=JIsmQPX6sAM&key=AIzaSyAY_u1YzIGq8jeDufkmsNGRKbJ4_bea0AI&part=snippet');
$data = json_decode($json);

pr($data->items[0]->snippet);

exit;
$folder = 'florbols-01-11-2014';
$user_id = 35809;


if ($handle = opendir('dati/bildes/' . $folder . '/large/')) {
	while (false !== ($file = readdir($handle))) {
		if ($file != "." && $file != "..") {

			$image = sanitize('dati/bildes/' . $folder . '/large/' . $file);
			$thb = sanitize('dati/bildes/' . $folder . '/thb/' . $file);
			$text = sanitize('<p>' . strtolower(str_replace(array('.JPG', '.jpg'), '', $file)) . " - Exs florbola turnīrs 01.11.2014, Jūrmala</p>");

			remake_thb($image, $thb);

			$sql = "

			INSERT INTO `images` (`uid`,`url`,`thb`,`text`,`date`,`bump`,`ip`,`lang`,`interest_id`) VALUES

				('$user_id','$image','$thb','$text',NOW(),NOW(),'127.0.0.1',1,8)
			";

			$db->query($sql);

			userlog($user_id, 'Pievienoja <a href="/gallery/' . $user_id . '/' . $db->insert_id . '">jaunu attēlu ' . textlimit(strip_tags($text), 32, '...') . '</a>', '/' . $thb);

			echo $sql . '<br />';
		}
	}
	closedir($handle);
	update_karma($user_id, true);
	update_karma($user_id, true);
	update_karma($user_id, true);
}

exit;

