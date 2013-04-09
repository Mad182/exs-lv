<?php


exit;
$users = $db->get_results("SELECT `id` FROM `users` WHERE `lastseen` > '".date('Y-m-d H:i:s',time()-2629743)."'");

$i = 0;
foreach($users as $user) {
	$i++;
	echo $user->id.'<br />';
	notify($user->id,12,1,'/read/bridinajumu-sistema','Brīdinājumu sistēma');
}
echo '<strong>'.$i.'</strong>';

exit;
?>
