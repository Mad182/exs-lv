<?php
if(($auth->ok && $auth->level == 1) or ($auth->ok && $auth->level == 2) or ($auth->id == 4273)) {

$tpl->newBlock('bumbum-samplis');

$check = $db->get_row("SELECT * FROM zgame WHERE game_id = ('" . $auth->id . "')");
$levels = array
(1=>'100',
 2=>'200',
 3=>'500',
 4=>'1000',
 5=>'2000',
 6=>'4000',
 7=>'8000',
 8=>'16000',
 9=>'32000',
 10=>'64000',
 11=>'128000',
 );

if($levels[($check->level+1)] <= $check->xp) {
$db->query("UPDATE zgame SET level = '$check->level'+'1', add_points = '$check->add_points'+'3' WHERE game_id = '$auth->id'");	
$levelup = 1;
}




// *************NEW PLAYER ****************
if($check->characterx == 99){
$tpl->newBlock('character-x-node');
}

if($check->game_id == $auth->id) {
$registrets = TRUE;
}
else
{

$db->query("INSERT INTO zgame (game_id,nick,avatar) VALUES ('".intval($auth->id)."','".sanitize($auth->nick)."','".sanitize($auth->avatar)."')");
$registrets = TRUE;
header('Location: ?c=199');
}
$char = sanitize($_POST['characterx']);

// NEW PLAYER CHARACTER SELECT ************************ (remake)
if($char == 1) {
$attack = 12;
$defense = 13;
$strenght = 8;
$speed = 10;
$db->query("UPDATE zgame SET characterx='$char',attack='$attack',defense='$defense',strenght='$strenght',speed='$speed' WHERE game_id = '$auth->id'");
header('Location: ?c=199');
}
if($char == 2) {
$attack = 12;
$defense = 10;
$strenght = 12;
$speed = 9.5;
$db->query("UPDATE zgame SET characterx='$char',attack='$attack',defense='$defense',strenght='$strenght',speed='$speed' WHERE game_id = '$auth->id'");
header('Location: ?c=199');
}
if($char == 3) {
$attack = 15;
$defense = 8;
$strenght = 10;
$speed = 10;
$db->query("UPDATE zgame SET characterx='$char',attack='$attack',defense='$defense',strenght='$strenght',speed='$speed' WHERE game_id = '$auth->id'");
header('Location: ?c=199');
}
// *************NEW PLAYER END ****************

// ********** CHARACTER INFO  **********	
if($check->characterx <= 10) 
{
$tpl->newBlock('character-combat-node');
$tpl->assign(array(
			  'character' => $check->characterx,
			  'attack' => $check->attack,
			  'defense' => $check->defense,
			  'strenght' => $check->strenght,
			  'speed' => $check->speed,
			  'xp' => $check->xp,
			  'level' => $check->level,
			  'money' => $check->money,
			  ));
}
	
	
	
	
$training = (int)$_GET['action'];	
$oponent = (int)$_GET['op'];	

//*** BONUSS *****///

if($training == 30) {
$db->query("UPDATE zgame SET equip_weapon = '114' WHERE game_id = '$auth->id'");
header('Location: ?c=199&action=12');
}


// ********|SĀKUMS|***********|SĀKUMS |****************| SĀKUMS|**********
if($training == 15) {
$tpl->newBlock('character-news-node');
}


// ####### TRENINS && COMBAT  ####### TRENINS && COMBAT ############## (remake)


if($training == 13) 
{

$tpl->newBlock('character-trenins-node');
if($levelup == 1) {
$tpl->assign(array(
'levelup' => 'Apsveicu, tu sasniedzi jaunu līmeni un tev tika piešķirti 3 attīstības punkti!' ,
));
}
if($oponent == 2) 
{	
$xpa = rand(0.1, 3);
$xp = $xpa/5;
$db->query("UPDATE zgame SET xp = '$check->xp'+'$xp' WHERE game_id = '$auth->id'");
$tpl->assign(array(
'training' => 'Tu smagi trenējies laukā un saņēmi ' . $xp. ' pieredzi' ,
));
}

if($check->xp >= 100)  {
$tpl->assign(array(
'mezs' => '| <a href="?c=199&action=13&op=3"><img src="/dati/bildes/zgame/forest.png" title="in dark forest"/></a>'
));
}
if($check->xp >= 500)  {
$tpl->assign(array(
'barakas' => '| <a href="?c=199&action=13&op=4"><img src="/dati/bildes/zgame/barracks.png" title="in barracks"/></a>',
));
}

// * Constants on attack * //
$attack_a = 0.35;
$defense_a = 0.2;
$strenght_a	= 0.35;
$speed_a =	1;

// * Constants on defense * //
$attack_d = 0.3;
$defense_d = 0.3;
$strenght_d	= 0.3;
$speed_d =	1;

// * Character Pure Power calculation * //

$power_a = (($check->attack*$attack_a)+($check->defense*$defense_a)+($check->strenght*$strenght_a)*($check->speed*$speed_a)); // +rand(-20, 20)
$power_d = (($check->attack*$attack_d)+($check->defense*$defense_d)+($check->strenght*$strenght_d)*($check->speed*$speed_d));

// * Weapon Power calculation * // 
$weapon = $db->get_row("SELECT * FROM zgame WHERE id = '$check->equip_weapon'");
$weapon_power = (($weapon->attack*$attack_a)+($weapon->defense*$defense_a)+($weapon->strenght*$strenght_a)*($weapon->speed*$speed_a)); // +rand(-20, 20)


// * get opponent info * //
$en = (int)$_GET['enemy'];
if ($en) {
$getenemy = $db->get_row("SELECT * FROM zgame WHERE id = ('" . $en . "') "); // AND is_npc = '1'

// * Enemy Pure Power calculation  * //
$power_ae = (($getenemy->attack*$attack_a)+($getenemy->defense*$defense_a)+($getenemy->strenght*$strenght_a)*($getenemy->speed*$speed_a));
$power_de = (($getenemy->attack*$attack_d)+($getenemy->defense*$defense_d)+($getenemy->strenght*$strenght_d)*($getenemy->speed*$speed_d));
}

// * check if defending or attacking * //



// * calculate winner, print info * //  
$tpl->newBlock('character-fight-node');
$xpc = ($power_de/30)-1;
if ($power_a+$weapon_power > $power_de) {

$tpl->assign(array(
'fight' => 'Tu cīnijies ar ' . ($getenemy->nick) .' '. ($power_de) . ' un to pieveici ' . ($power_a+$weapon_power) . ' un saņēmi ' . ($xpc) .' pieredzi',
));
$db->query("UPDATE zgame SET xp = '$check->xp'+'$xpc' WHERE game_id = '$auth->id'");
}	
else {
$tpl->assign(array(
'fight' => 'Tu cīnijies ar ' . ($getenemy->nick) .'  '. ($power_de) . ' un to nepieveici ' . ($power_a+$weapon_power) . '',
));
}

}
// TRAINING && FIGHTING END && CALCULATIONS




	

//********CITY********CITY********city city stuff
if($training == 14) {
$tpl->newBlock('character-city-node');	


// ************** TOP TOP TOP 50 50 50 ***************
}	
if($training == 11) {
$tpl->newBlock('character-players-node');

	
$players = $db->get_results("SELECT * FROM zgame WHERE is_npc = '0' ORDER BY xp DESC LIMIT 10");	
foreach ($players as $player) {
$tpl->newBlock('character-player-node');
if($player->characterx == 2) {
$tpl->assign(array(
'character' => 'Blood Wolwerine',
));
}

if($player->characterx == 3) {
$tpl->assign(array(
'character' => 'Sun wizard',
));
}

if($player->characterx == 1) {
$tpl->assign(array(
'character' => 'Leafs Elf',
));
}

if($player->characterx == 99) {
$tpl->assign(array(
'character' => '<i>Nav Izvēlēts</i>',
));
}

			
			$tpl->assign(array(
			  'players' => $player->nick,
			  'xp' => $player->xp,
			  'level' => $player->level,
			  'attack' => $player->attack,
			  'defense' => $player->defense,
			  'strenght' => $player->strenght,
			  'speed' => $player->speed,
			  ));
		}
}
		
// **************************Palidziba ******************
if($training == 10) {
 $tpl->newBlock('character-help-node');
$tpl->assign(array( 
 'levels' => $levels,
));

$npcs = $db->get_results("SELECT * FROM zgame WHERE is_npc = '1' ORDER BY level DESC LIMIT 50");	
foreach ($npcs as $npc) {
$tpl->newBlock('character-npc-node');
$tpl->assign(array(
			  'npc-name' => $npc->nick,
			  'npc-level' => $npc->level,
			  'npc-attack' => $npc->attack,
			  'npc-defense' => $npc->defense,
			  'npc-strenght' => $npc->strenght,
			  'npc-speed' => $npc->speed,
			  'npc-notes' => $npc->notes,
			  'npc-sum' => $npc->attack+$npc->strenght+$npc->defense+$npc->speed,
			  ));
}

$equips = $db->get_results("SELECT * FROM zgame WHERE is_inv = '1' ORDER BY level DESC LIMIT 50");	
foreach ($equips as $equip) {
$tpl->newBlock('character-equip-node');
$tpl->assign(array(
			  'equip-name' => $equip->nick,
			  'equip-avatar' => '<img src="' .$equip->avatar . '" title="' . $equip->nick .'" />',
			  'equip-level' => $equip->level,
			  'equip-attack' => $equip->attack,
			  'equip-defense' => $equip->defense,
			  'equip-strenght' => $equip->strenght,
			  'equip-speed' => $equip->speed,
			  'equip-notes' => $equip->notes,
			  'equip-sum' => $equip->attack+$equip->strenght+$equip->defense+$equip->speed,
			  ));
}

}
if($training == 12) {	

$xpleft =  round($check->xp/$levels[($check->level+1)]*100,0);


 

 $tpl->newBlock('character-majas-node');
 $tpl->assign(array(
			  'character' => $check->characterx,
			  'attack' => $check->attack,
			  'defense' => $check->defense,
			  'strenght' => $check->strenght,
			  'speed' => $check->speed,
			  'xp' => $check->xp,
			  'level' => $check->level,
			  'xpleft' => $xpleft,
));		


$equipedwep = $db->get_row("SELECT * FROM zgame WHERE id = '$check->equip_weapon'");
if($check->equip_weapon > 0) {
$tpl->assign(array(
'equip_weapon' => '<img src="' .$equipedwep->avatar .'" width="50"/>',
'equip_name' => $equipedwep->nick,
'equip_attack' => '+ (' . $equipedwep->attack .')',
'equip_defense' => '+ (' . $equipedwep->defense .')',
'equip_strenght' => '+ (' . $equipedwep->strenght .')',
'equip_speed' => '+ (' .$equipedwep->speed .')',
'equip_armor' => '<img src="' .$equipedarm->avatar .'"/>',
'equip_disequip' => '<a href="/?c=199&action=12&weapon=1">Novilkt</a>',
));
}
$equip = (int)$_GET['weapon'];
$toequip = (int)$_GET['wepid'];
if($equip == 1) {
$db->query("UPDATE zgame SET equip_weapon = '0' WHERE game_id = '$auth->id'");
header('Location: ?c=199&action=12');
}


$inventory = $db->get_row("SELECT inventory FROM zgame WHERE game_id = '$auth->id'");
$inv_ids = unserialize($inventory->inventory);
foreach ($inv_ids as $inv_i) {
$inv_infos = $db->get_results("SELECT * FROM zgame WHERE id = '$inv_i'"); 
foreach ($inv_infos as $inv_info) {
$tpl->newBlock('character-inventory-node');
if($inv_info->is_inv == 1) 
{
$tpl->assign(array(
			  'inv-equip' => '<a href="/?c=199&action=12&weapon=2&wepid='. $inv_info->id .'">Uzvilkt</a>',
			  ));
}

if($equip == 2 && in_array($toequip, $inv_ids) ) {
$db->query("UPDATE zgame SET equip_weapon = '$toequip' WHERE game_id = '$auth->id'");
header('Location: ?c=199&action=12');
}

$tpl->assign(array(
			  'inv-name' => $inv_info->nick,
			  'inv-avatar' => '<img src="' .$inv_info->avatar . '" title="' . $inv_info->nick .'" width="30" />',
			  'inv-level' => $inv_info->level,
			  'inv-attack' => $inv_info->attack,
			  'inv-defense' => $inv_info->defense,
			  'inv-strenght' => $inv_info->strenght,
			  'inv-speed' => $inv_info->speed,
			  'inv-notes' => $inv_info->notes,
			  ));
}}
if(empty($inventory->inventory)) {
		$tpl->assign(array(
'inv' => 'Tev nav neviena priekšmeta',
));			

	} else {
$tpl->assign(array(
'inv' => unserialize($inventory),
));				
	}


		  
			  
if($check->add_points > 0) {
$tpl->newBlock('character-points-node');
$tpl->assign(array(
'points' => $check->add_points,
'add1' => '<a href="?c=199&action=12&add=1">[+1]</a>',
			  'add2' => '<a href="?c=199&action=12&add=2">[+1]</a>',
			  'add3' => '<a href="?c=199&action=12&add=3">[+1]</a>',
			  'add4' => '<a href="?c=199&action=12&add=4">[+1]</a>',
));			  
			  
			  
			  
$add = (int)$_GET['add'];	
if($add == 1 && $check->add_points > 0) {
$db->query("UPDATE zgame SET attack = '$check->attack'+'1',add_points = '$check->add_points'-'1' WHERE game_id = '$auth->id'");
}	
if($add == 2 && $check->add_points > 0) {
$db->query("UPDATE zgame SET defense = '$check->defense'+'1',add_points = '$check->add_points'-'1' WHERE game_id = '$auth->id'");
}	
if($add == 3 && $check->add_points > 0) {
$db->query("UPDATE zgame SET strenght = '$check->strenght'+'1',add_points = '$check->add_points'-'1' WHERE game_id = '$auth->id'");
}	
if($add == 4 && $check->add_points > 0) {
$db->query("UPDATE zgame SET speed = '$check->speed'+'1',add_points = '$check->add_points'-'1' WHERE game_id = '$auth->id'");
}	

		  
}


}

	
	
	
	
	
// no exland below	
function birthday ($diena){
    list($year,$month,$day) = explode("-",$diena);
    $year_diff  = date("Y") - $year;
    $month_diff = date("m") - $month;
    $day_diff   = date("d") - $day;
    if ($day_diff < 0 || $month_diff < 0)
      $year_diff--;
    return $year_diff;
  }
	
	
$lastip = sanitize($_POST['lastip']);	
$tpl->newBlock('user-ipmenu-node');
if($lastip) {
$ips = $db->get_results("SELECT * FROM users WHERE lastip = '$lastip' ORDER BY karma");	
foreach ($ips as $ip) {
$tpl->newBlock('user-ip-node');
$tpl->assign(array(
'ip-name' => $ip->nick,
'ip-lastseen' => $ip->lastseen,
'ip-karma' => $ip->karma,
));
}
}
	
$day =  date("d");
$month =  date("m");
$bday = $db->get_results("SELECT nick,birthday FROM users WHERE birthday like '%-$month-$day'");
if($bday > 0) {
 
foreach ($bday as $bg) {
			$tpl->newBlock('bday-list-node');
			$tpl->assign(array(
			  'bg-nick' => $bg->nick,
			  'bg-age' => birthday($bg->birthday),
	
			  ));
		}
}
else 
{

			$tpl->newBlock('bdayn-list-node');
			$tpl->assign(array(
			  ));
		}

	
	
	
	
$tpl->newBlock('award-list-node');	


	$ratings = $db->get_results("SELECT * FROM pages WHERE rating_count > 50 AND (rating/rating_count) > 4.5 ");
	if($ratings) {
		foreach ($ratings as $rating) {
$nick = $db->get_var("SELECT nick FROM users WHERE id = '$rating->author' LIMIT 1");
$linkuser = '<a href="'.mkurl('user',$rating->author,$nick).'">'.$nick.'</a>';
			$tpl->newBlock('rating-list-node');
			$tpl->assign(array(
			  'rating-id' => $rating->id,
			  'rating-title' => mkslug($rating->title),
			  'rating-total_votes' => $rating->rating,
			  'rating-total_value' => $rating->rating_count,
			  'rating-total' => round($rating->rating/$rating->rating_count, 3),
			  'rating-author' => $linkuser,
			  'rating-views' => $rating->views,
			  ));
		}
	}


	
	
	
}

?>