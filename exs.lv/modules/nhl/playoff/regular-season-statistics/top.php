<div style="float:left; width:950px;">
<div style="float:left; width:330px;">
<div class="box">
<h6>POINTS</h6>
<div class="box_content">
<?
$res = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Player' AND league_id = '" . LEAGUE_ID . "' ORDER BY goal + asst DESC LIMIT 0,1");
$row = mysql_fetch_array($res);
$points = $row['goal'] + $row['asst'];
$izvelk_player = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row[player_name]'");
$player_name = mysql_fetch_array($izvelk_player); 
if($player_name['pos'] == 'forwards') { $pos = "Forwards"; } 
elseif($player_name['pos'] == 'center') { $pos = "Center"; } 
elseif($player_name['pos'] == 'left_wing') { $pos = "Left Wing"; }
elseif($player_name['pos'] == 'right_wing') { $pos = "Right Wing"; }
elseif($player_name['pos'] == 'defenseman') { $pos = "Defenseman"; }
elseif($player_name['pos'] == 'goalie') { $pos = "Goalie"; }
else { $pos = "UNKNOWN"; }
if(empty($player_name['player_image'])){$palyer_image = "/style/images/medium_default_avatar.jpg";}else{ $palyer_image = $player_name['player_image'];}
if(mysql_num_rows($res)!=0)
{
echo '<span style="float:left">
<font size="4"><b><a href="/player/'.$player_name['display_name'].'">'.$row['player_name'].'</a></b></font><br><b>Position:</b> '.$pos.'<div style="height: 8px;"></div>
<img src="/style/images/teams/medium/colorful/'.$row['team'].'.png"><div style="height: 4px;"></div>
<font size="2"><b>'.$nhl_team_values[$row['team']].'</b></font><div style="height: 33px;"></div>
<font size="5"><b>POINTS: '.$points.'</b></font>
</span><div style="float:right; padding-left:-5px; background: url('.$palyer_image.') no-repeat;" id="first_place_player_left"><img height="30px" src="/style/images/first_place.png"></div>';
$res1 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Player' AND league_id = '" . LEAGUE_ID . "' ORDER BY goal + asst DESC LIMIT 1,2");
$row1 = mysql_fetch_array($res1);
$points1 = $row1['goal'] + $row1['asst'];
$sekundes1 = $row1['time']/$row1['gp'];
$min1 = floor($sekundes1/60);
$sec1 = $sekundes1 % 60;
if(strlen($sec1) == 1){$zero1 = 0;}else{$zero1 = '' ;}
$simbols1 = $row1['plus_minus'] > 0 ? "+":"";
$plus_minuss1 = $simbols1.(int)$row1['plus_minus'];
$izvelk_player1 = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row1[player_name]'");
$player_name1 = mysql_fetch_array($izvelk_player1); 
echo '<table class="west" cellspacing="0" cellpadding="1" style="width: 306px;">
<tr style="color:#E2E4E3; font-weight:bold;" bgcolor="#6F7070"><td width="17"><center>#</center></td><td width="100"></td><td width="27"><center>Team</center></td><td width="27"><center>GP</center><td width="27"><center>G</center><td width="27"><center>A</center><td width="27"><center>P</center><td width="27"><center>+/-</center><td width="27"><center>PIM</center><td width="27"><center>TOI</center></tr>
';
echo "<tr style='background-color:#E8E8E8;'><td class='turnirs'><center>2</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name1['display_name']."'>".$row1['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row1['team'].".png'></td><td class='turnirs'><center>".$row1['gp']."</center></td><td class='turnirs'><center>".$row1['goal']."</center></td><td class='turnirs'><center>".$row1['asst']."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$points1."</center></td><td class='turnirs'><center>".$plus_minuss1."</center></td><td class='turnirs'><center>".$row1['pims']."</center></td><td class='turnirs_beigas'><center>".$min1 . ':'.$zero1.'' . $sec1."</center></td>";
$res2 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Player' AND league_id = '" . LEAGUE_ID . "'ORDER BY goal + asst DESC LIMIT 2,3");
$row2=mysql_fetch_array($res2);
$points2 = $row2['goal'] + $row2['asst'];
$sekundes2 = $row2['time']/$row2['gp'];
$min2 = floor($sekundes2/60);
$sec2 = $sekundes2 % 60;
if(strlen($sec2) == 1){$zero2 = 0;}else{$zero2 = '' ;}
$simbols2 = $row2['plus_minus'] > 0 ? "+":"";
$plus_minuss2 = $simbols2.(int)$row2['plus_minus'];
$izvelk_player2 = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row2[player_name]'");
$player_name2 = mysql_fetch_array($izvelk_player2); 
echo "<tr style='background-color:#FFFFFF;'><td class='turnirs'><center>3</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name2['display_name']."'>".$row2['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row2['team'].".png'></td><td class='turnirs'><center>".$row2['gp']."</center></td><td class='turnirs'><center>".$row2['goal']."</center></td><td class='turnirs'><center>".$row2['asst']."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$points2."</center></td><td class='turnirs'><center>".$plus_minuss2."</center></td><td class='turnirs'><center>".$row2['pims']."</center></td><td class='turnirs_beigas'><center>".$min2 . ':'.$zero2.'' . $sec2."</center></td>";
$res3 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Player' AND league_id = '" . LEAGUE_ID . "'ORDER BY goal + asst DESC LIMIT 3,4");
$row3=mysql_fetch_array($res3);
$points3 = $row3['goal'] + $row3['asst'];
$sekundes3 = $row3['time']/$row3['gp'];
$min3 = floor($sekundes3/60);
$sec3 = $sekundes3 % 60;
if(strlen($sec3) == 1){$zero3 = 0;}else{$zero3 = '' ;}
$simbols3 = $row3['plus_minus'] > 0 ? "+":"";
$plus_minuss3 = $simbols3.(int)$row3['plus_minus'];
$izvelk_player3 = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row3[player_name]'");
$player_name3 = mysql_fetch_array($izvelk_player3); 
echo "<tr style='background-color:#E8E8E8;'><td class='turnirs'><center>4</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name3['display_name']."'>".$row3['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row3['team'].".png'></td><td class='turnirs'><center>".$row3['gp']."</center></td><td class='turnirs'><center>".$row3['goal']."</center></td><td class='turnirs'><center>".$row3['asst']."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$points3."</center></td><td class='turnirs'><center>".$plus_minuss3."</center></td><td class='turnirs'><center>".$row3['pims']."</center></td><td class='turnirs_beigas'><center>".$min3 . ':'.$zero3.'' . $sec3."</center></td>";
$res4 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Player' AND league_id = '" . LEAGUE_ID . "' ORDER BY goal + asst DESC LIMIT 4,5");
$row4=mysql_fetch_array($res4);
$points4 = $row4['goal'] + $row4['asst'];
$sekundes4 = $row4['time']/$row4['gp'];
$min4 = floor($sekundes4/60);
$sec4 = $sekundes4 % 60;
if(strlen($sec4) == 1){$zero4 = 0;}else{$zero4 = '' ;}
$simbols4 = $row4['plus_minus'] > 0 ? "+":"";
$plus_minuss4 = $simbols4.(int)$row4['plus_minus'];
$izvelk_player4 = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row4[player_name]'");
$player_name4 = mysql_fetch_array($izvelk_player4); 
echo "<tr style='background-color:#FFFFFF;'><td class='turnirs'><center>5</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name4['display_name']."'>".$row4['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row4['team'].".png'></td><td class='turnirs'><center>".$row4['gp']."</center></td><td class='turnirs'><center>".$row4['goal']."</center></td><td class='turnirs'><center>".$row4['asst']."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$points4."</center></td><td class='turnirs'><center>".$plus_minuss4."</center></td><td class='turnirs'><center>".$row4['pims']."</center></td><td class='turnirs_beigas'><center>".$min4 . ':'.$zero4.'' . $sec4."</center></td></tr></table>";
}
else
{
echo 'NHL #' . LEAGUE_ID . ' līga vēl nav izspēlēta neviena spēle,<br> tāpēc pašlaik nav statistikas ko ievākt!';
}
?>
</div>
<div class="box_bottom"></div>
</div>
<div class="box">
<h6>GOALS</h6>
<div class="box_content">
<?
$res = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Player' AND league_id = '" . LEAGUE_ID . "' ORDER BY goal DESC LIMIT 0,1");
$row=mysql_fetch_array($res);
$points = $row['goal'] + $row['asst'];
$izvelk_player = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row[player_name]'");
$player_name = mysql_fetch_array($izvelk_player); 
$team_values = array ('ANA' => 'ANAHEIM DUCKS','CGY' => 'CALGARY FLAMES','CHI' => 'CHICAGO BLACKHAWKS','COL' => 'COLORADO AVALANCHE',
'CBJ' => 'COLUMBUS B. JACKETS','DAL' => 'DALLAS STARS','DET' => 'DETROIT RED WINGS','EDM' => 'EDMONTON OILERS','LA' => 'LOS ANGELES KINGS',
'MIN' => 'MINNESOTA WILD','NSH' => 'NASHVILLE PREDATORS','PHX' => 'PHOENIX COYOTES','SJ' => 'SAN JOSE SHARKS','STL' => 'ST LOUIS BLUES',
'VAN' => 'VANCOUVER CANUCKS','WPG' => 'WINNIPEG JETS','BOS' => 'BOSTON BRUINS','BUF' => 'BUFFALO SABRES','CAR' => 'CAROLINA HURRICANES',
'FLA' => 'FLORIDA PANTHERS','MTL' => 'MONTREAL CANADIENS','NJ' => 'NEW JERSEY DEVILS','NYI' => 'NEW YORK ISLANDERS','NYR' => 'NEW YORK RANGERS',
'OTT' => 'OTTAWA SENATORS','PHI' => 'PHILADELPHIA FLYERS','PIT' => 'PITTSBURGH PENGUINS','TB' => 'TAMPA BAY LIGHTNING','TOR' => 'TORONTO MAPLE LEAFS','WSH' => 'WASHINGTON CAPITALS');
if($player_name['pos'] == 'forwards') { $pos = "Forwards"; } 
elseif($player_name['pos'] == 'center') { $pos = "Center"; } 
elseif($player_name['pos'] == 'left_wing') { $pos = "Left Wing"; }
elseif($player_name['pos'] == 'right_wing') { $pos = "Right Wing"; }
elseif($player_name['pos'] == 'defenseman') { $pos = "Defenseman"; }
elseif($player_name['pos'] == 'goalie') { $pos = "Goalie"; }
else { $pos = "UNKNOWN"; }
if(empty($player_name['player_image'])){$palyer_image = "/style/images/medium_default_avatar.jpg";}else{ $palyer_image = $player_name['player_image'];}
if(mysql_num_rows($res)!=0)
{
echo '<span style="float:left">
<font size="4"><b><a href="/player/'.$player_name['display_name'].'">'.$row['player_name'].'</a></b></font><br><b>Position:</b> '.$pos.'<div style="height: 8px;"></div>
<img src="/style/images/teams/medium/colorful/'.$row['team'].'.png"><div style="height: 4px;"></div>
<font size="2"><b>'.$team_values[$row['team']].'</b></font><div style="height: 33px;"></div>
<font size="5"><b>GOALS: '.$row['goal'].'</b></font>
</span><div style="float:right; padding-left:-5px; background: url('.$palyer_image.') no-repeat;" id="first_place_player_left"><img height="30px" src="/style/images/first_place.png"></div>';
$res1 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Player' AND league_id = '" . LEAGUE_ID . "' ORDER BY goal DESC LIMIT 1,2");
$row1 = mysql_fetch_array($res1);
$points1 = $row1['goal'] + $row1['asst'];
$sekundes1 = $row1['time']/$row1['gp'];
$min1 = floor($sekundes1/60);
$sec1 = $sekundes1 % 60;
if(strlen($sec1) == 1){$zero1 = 0;}else{$zero1 = '' ;}
$simbols1 = $row1['plus_minus'] > 0 ? "+":"";
$plus_minuss1 = $simbols1.(int)$row1['plus_minus'];
$izvelk_player1 = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row1[player_name]'");
$player_name1 = mysql_fetch_array($izvelk_player1); 
echo '<table class="west" cellspacing="0" cellpadding="1" style="width: 306px;">
<tr style="color:#E2E4E3; font-weight:bold;" bgcolor="#6F7070"><td width="17"><center>#</center></td><td width="100"></td><td width="27"><center>Team</center></td><td width="27"><center>GP</center><td width="27"><center>G</center><td width="27"><center>A</center><td width="27"><center>P</center><td width="27"><center>+/-</center><td width="27"><center>PIM</center><td width="27"><center>TOI</center></tr>
';
echo "<tr style='background-color:#E8E8E8;'><td class='turnirs'><center>2</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name1['display_name']."'>".$row1['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row1['team'].".png'></td><td class='turnirs'><center>".$row1['gp']."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$row1['goal']."</center></td><td class='turnirs'><center>".$row1['asst']."</center></td><td class='turnirs'><center>".$points1."</center></td><td class='turnirs'><center>".$plus_minuss1."</center></td><td class='turnirs'><center>".$row1['pims']."</center></td><td class='turnirs_beigas'><center>".$min1 . ':'.$zero1.'' . $sec1."</center></td>";
$res2 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Player' AND league_id = '" . LEAGUE_ID . "' ORDER BY goal DESC LIMIT 2,3");
$row2=mysql_fetch_array($res2);
$points2 = $row2['goal'] + $row2['asst'];
$sekundes2 = $row2['time']/$row2['gp'];
$min2 = floor($sekundes2/60);
$sec2 = $sekundes2 % 60;
if(strlen($sec2) == 1){$zero2 = 0;}else{$zero2 = '' ;}
$simbols2 = $row2['plus_minus'] > 0 ? "+":"";
$plus_minuss2 = $simbols2.(int)$row2['plus_minus'];
$izvelk_player2 = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row2[player_name]'");
$player_name2 = mysql_fetch_array($izvelk_player2); 
echo "<tr style='background-color:#FFFFFF;'><td class='turnirs'><center>3</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name2['display_name']."'>".$row2['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row2['team'].".png'></td><td class='turnirs'><center>".$row2['gp']."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$row2['goal']."</center></td><td class='turnirs'><center>".$row2['asst']."</center></td><td class='turnirs'><center>".$points2."</center></td><td class='turnirs'><center>".$plus_minuss2."</center></td><td class='turnirs'><center>".$row2['pims']."</center></td><td class='turnirs_beigas'><center>".$min2 . ':'.$zero2.'' . $sec2."</center></td>";
$res3 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Player' AND league_id = '" . LEAGUE_ID . "' ORDER BY goal DESC LIMIT 3,4");
$row3=mysql_fetch_array($res3);
$points3 = $row3['goal'] + $row3['asst'];
$sekundes3 = $row3['time']/$row3['gp'];
$min3 = floor($sekundes3/60);
$sec3 = $sekundes3 % 60;
if(strlen($sec3) == 1){$zero3 = 0;}else{$zero3 = '' ;}
$simbols3 = $row3['plus_minus'] > 0 ? "+":"";
$plus_minuss3 = $simbols3.(int)$row3['plus_minus'];
$izvelk_player3 = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row3[player_name]'");
$player_name3 = mysql_fetch_array($izvelk_player3); 
echo "<tr style='background-color:#E8E8E8;'><td class='turnirs'><center>4</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name3['display_name']."'>".$row3['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row3['team'].".png'></td><td class='turnirs'><center>".$row3['gp']."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$row3['goal']."</center></td><td class='turnirs'><center>".$row3['asst']."</center></td><td class='turnirs'><center>".$points3."</center></td><td class='turnirs'><center>".$plus_minuss3."</center></td><td class='turnirs'><center>".$row3['pims']."</center></td><td class='turnirs_beigas'><center>".$min3 . ':'.$zero3.'' . $sec3."</center></td>";
$res4 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Player' AND league_id = '" . LEAGUE_ID . "' ORDER BY goal DESC LIMIT 4,5");
$row4=mysql_fetch_array($res4);
$points4 = $row4['goal'] + $row4['asst'];
$sekundes4 = $row4['time']/$row4['gp'];
$min4 = floor($sekundes4/60);
$sec4 = $sekundes4 % 60;
if(strlen($sec4) == 1){$zero4 = 0;}else{$zero4 = '' ;}
$simbols4 = $row4['plus_minus'] > 0 ? "+":"";
$plus_minuss4 = $simbols4.(int)$row4['plus_minus'];
$izvelk_player4 = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row4[player_name]'");
$player_name4 = mysql_fetch_array($izvelk_player4); 
echo "<tr style='background-color:#FFFFFF;'><td class='turnirs'><center>5</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name4['display_name']."'>".$row4['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row4['team'].".png'></td><td class='turnirs'><center>".$row4['gp']."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$row4['goal']."</center></td><td class='turnirs'><center>".$row4['asst']."</center></td><td class='turnirs'><center>".$points4."</center></td><td class='turnirs'><center>".$plus_minuss4."</center></td><td class='turnirs'><center>".$row4['pims']."</center></td><td class='turnirs_beigas'><center>".$min4 . ':'.$zero4.'' . $sec4."</center></td></tr></table>";
}
else
{
echo 'NHL #' . LEAGUE_ID . ' līga vēl nav izspēlēta neviena spēle,<br> tāpēc pašlaik nav statistikas ko ievākt!';
}
?>
</div>
<div class="box_bottom"></div>
</div>
<div class="box">
<h6>ASSISTS</h6>
<div class="box_content">
<?
$res = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Player' AND league_id = '" . LEAGUE_ID . "' ORDER BY asst DESC LIMIT 0,1");
$row=mysql_fetch_array($res);
$points = $row['goal'] + $row['asst'];
$izvelk_player = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row[player_name]'");
$player_name = mysql_fetch_array($izvelk_player); 
$team_values = array ('ANA' => 'ANAHEIM DUCKS','CGY' => 'CALGARY FLAMES','CHI' => 'CHICAGO BLACKHAWKS','COL' => 'COLORADO AVALANCHE',
'CBJ' => 'COLUMBUS B. JACKETS','DAL' => 'DALLAS STARS','DET' => 'DETROIT RED WINGS','EDM' => 'EDMONTON OILERS','LA' => 'LOS ANGELES KINGS',
'MIN' => 'MINNESOTA WILD','NSH' => 'NASHVILLE PREDATORS','PHX' => 'PHOENIX COYOTES','SJ' => 'SAN JOSE SHARKS','STL' => 'ST LOUIS BLUES',
'VAN' => 'VANCOUVER CANUCKS','WPG' => 'WINNIPEG JETS','BOS' => 'BOSTON BRUINS','BUF' => 'BUFFALO SABRES','CAR' => 'CAROLINA HURRICANES',
'FLA' => 'FLORIDA PANTHERS','MTL' => 'MONTREAL CANADIENS','NJ' => 'NEW JERSEY DEVILS','NYI' => 'NEW YORK ISLANDERS','NYR' => 'NEW YORK RANGERS',
'OTT' => 'OTTAWA SENATORS','PHI' => 'PHILADELPHIA FLYERS','PIT' => 'PITTSBURGH PENGUINS','TB' => 'TAMPA BAY LIGHTNING','TOR' => 'TORONTO MAPLE LEAFS','WSH' => 'WASHINGTON CAPITALS');
if($player_name['pos'] == 'forwards') { $pos = "Forwards"; } 
elseif($player_name['pos'] == 'center') { $pos = "Center"; } 
elseif($player_name['pos'] == 'left_wing') { $pos = "Left Wing"; }
elseif($player_name['pos'] == 'right_wing') { $pos = "Right Wing"; }
elseif($player_name['pos'] == 'defenseman') { $pos = "Defenseman"; }
elseif($player_name['pos'] == 'goalie') { $pos = "Goalie"; }
else { $pos = "UNKNOWN"; }
if(empty($player_name['player_image'])){$palyer_image = "/style/images/medium_default_avatar.jpg";}else{ $palyer_image = $player_name['player_image'];}
if(mysql_num_rows($res)!=0)
{
echo '<span style="float:left">
<font size="4"><b><a href="/player/'.$player_name['display_name'].'">'.$row['player_name'].'</a></b></font><br><b>Position:</b> '.$pos.'<div style="height: 8px;"></div>
<img src="/style/images/teams/medium/colorful/'.$row['team'].'.png"><div style="height: 4px;"></div>
<font size="2"><b>'.$team_values[$row['team']].'</b></font><div style="height: 33px;"></div>
<font size="5"><b>ASSISTS: '.$row['asst'].'</b></font>
</span><div style="float:right; padding-left:-5px; background: url('.$palyer_image.') no-repeat;" id="first_place_player_left"><img height="30px" src="/style/images/first_place.png"></div>';
$res1 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Player' AND league_id = '" . LEAGUE_ID . "' ORDER BY asst DESC LIMIT 1,2");
$row1 = mysql_fetch_array($res1);
$points1 = $row1['goal'] + $row1['asst'];
$sekundes1 = $row1['time']/$row1['gp'];
$min1 = floor($sekundes1/60);
$sec1 = $sekundes1 % 60;
if(strlen($sec1) == 1){$zero1 = 0;}else{$zero1 = '' ;}
$simbols1 = $row1['plus_minus'] > 0 ? "+":"";
$plus_minuss1 = $simbols1.(int)$row1['plus_minus'];
$izvelk_player1 = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row1[player_name]'");
$player_name1 = mysql_fetch_array($izvelk_player1); 
echo '<table class="west" cellspacing="0" cellpadding="1" style="width: 306px;">
<tr style="color:#E2E4E3; font-weight:bold;" bgcolor="#6F7070"><td width="17"><center>#</center></td><td width="100"></td><td width="27"><center>Team</center></td><td width="27"><center>GP</center><td width="27"><center>G</center><td width="27"><center>A</center><td width="27"><center>P</center><td width="27"><center>+/-</center><td width="27"><center>PIM</center><td width="27"><center>TOI</center></tr>
';
echo "<tr style='background-color:#E8E8E8;'><td class='turnirs'><center>2</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name1['display_name']."'>".$row1['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row1['team'].".png'></td><td class='turnirs'><center>".$row1['gp']."</center></td><td class='turnirs'><center>".$row1['goal']."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$row1['asst']."</center></td><td class='turnirs'><center>".$points1."</center></td><td class='turnirs'><center>".$plus_minuss1."</center></td><td class='turnirs'><center>".$row1['pims']."</center></td><td class='turnirs_beigas'><center>".$min1 . ':'.$zero1.'' . $sec1."</center></td>";
$res2 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Player' AND league_id = '" . LEAGUE_ID . "' ORDER BY asst DESC LIMIT 2,3");
$row2=mysql_fetch_array($res2);
$points2 = $row2['goal'] + $row2['asst'];
$sekundes2 = $row2['time']/$row2['gp'];
$min2 = floor($sekundes2/60);
$sec2 = $sekundes2 % 60;
if(strlen($sec2) == 1){$zero2 = 0;}else{$zero2 = '' ;}
$simbols2 = $row2['plus_minus'] > 0 ? "+":"";
$plus_minuss2 = $simbols2.(int)$row2['plus_minus'];
$izvelk_player2 = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row2[player_name]'");
$player_name2 = mysql_fetch_array($izvelk_player2); 
echo "<tr style='background-color:#FFFFFF;'><td class='turnirs'><center>3</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name2['display_name']."'>".$row2['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row2['team'].".png'></td><td class='turnirs'><center>".$row2['gp']."</center></td><td class='turnirs'><center>".$row2['goal']."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$row2['asst']."</center></td><td class='turnirs'><center>".$points2."</center></td><td class='turnirs'><center>".$plus_minuss2."</center></td><td class='turnirs'><center>".$row2['pims']."</center></td><td class='turnirs_beigas'><center>".$min2 . ':'.$zero2.'' . $sec2."</center></td>";
$res3 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Player' AND league_id = '" . LEAGUE_ID . "' ORDER BY asst DESC LIMIT 3,4");
$row3=mysql_fetch_array($res3);
$points3 = $row3['goal'] + $row3['asst'];
$sekundes3 = $row3['time']/$row3['gp'];
$min3 = floor($sekundes3/60);
$sec3 = $sekundes3 % 60;
if(strlen($sec3) == 1){$zero3 = 0;}else{$zero3 = '' ;}
$simbols3 = $row3['plus_minus'] > 0 ? "+":"";
$plus_minuss3 = $simbols3.(int)$row3['plus_minus'];
$izvelk_player3 = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row3[player_name]'");
$player_name3 = mysql_fetch_array($izvelk_player3); 
echo "<tr style='background-color:#E8E8E8;'><td class='turnirs'><center>4</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name3['display_name']."'>".$row3['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row3['team'].".png'></td><td class='turnirs'><center>".$row3['gp']."</center></td><td class='turnirs'><center>".$row3['goal']."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$row3['asst']."</center></td><td class='turnirs'><center>".$points3."</center></td><td class='turnirs'><center>".$plus_minuss3."</center></td><td class='turnirs'><center>".$row3['pims']."</center></td><td class='turnirs_beigas'><center>".$min3 . ':'.$zero3.'' . $sec3."</center></td>";
$res4 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Player' AND league_id = '" . LEAGUE_ID . "' ORDER BY asst DESC LIMIT 4,5");
$row4=mysql_fetch_array($res4);
$points4 = $row4['goal'] + $row4['asst'];
$sekundes4 = $row4['time']/$row4['gp'];
$min4 = floor($sekundes4/60);
$sec4 = $sekundes4 % 60;
if(strlen($sec4) == 1){$zero4 = 0;}else{$zero4 = '' ;}
$simbols4 = $row4['plus_minus'] > 0 ? "+":"";
$plus_minuss4 = $simbols4.(int)$row4['plus_minus'];
$izvelk_player4 = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row4[player_name]'");
$player_name4 = mysql_fetch_array($izvelk_player4); 
echo "<tr style='background-color:#FFFFFF;'><td class='turnirs'><center>5</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name4['display_name']."'>".$row4['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row4['team'].".png'></td><td class='turnirs'><center>".$row4['gp']."</center></td><td class='turnirs'><center>".$row4['goal']."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$row4['asst']."</center></td><td class='turnirs'><center>".$points4."</center></td><td class='turnirs'><center>".$plus_minuss4."</center></td><td class='turnirs'><center>".$row4['pims']."</center></td><td class='turnirs_beigas'><center>".$min4 . ':'.$zero4.'' . $sec4."</center></td></tr></table>";
}
else
{
echo 'NHL #' . LEAGUE_ID . ' līga vēl nav izspēlēta neviena spēle,<br> tāpēc pašlaik nav statistikas ko ievākt!';
}
?>
</div>
<div class="box_bottom"></div>
</div>
<div class="box">
<h6>HITS</h6>
<div class="box_content">
<?
$res = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Player' AND league_id = '" . LEAGUE_ID . "' ORDER BY hits DESC LIMIT 0,1");
$row=mysql_fetch_array($res);
$points = $row['goal'] + $row['asst'];
$izvelk_player = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row[player_name]'");
$player_name = mysql_fetch_array($izvelk_player); 
$team_values = array ('ANA' => 'ANAHEIM DUCKS','CGY' => 'CALGARY FLAMES','CHI' => 'CHICAGO BLACKHAWKS','COL' => 'COLORADO AVALANCHE',
'CBJ' => 'COLUMBUS B. JACKETS','DAL' => 'DALLAS STARS','DET' => 'DETROIT RED WINGS','EDM' => 'EDMONTON OILERS','LA' => 'LOS ANGELES KINGS',
'MIN' => 'MINNESOTA WILD','NSH' => 'NASHVILLE PREDATORS','PHX' => 'PHOENIX COYOTES','SJ' => 'SAN JOSE SHARKS','STL' => 'ST LOUIS BLUES',
'VAN' => 'VANCOUVER CANUCKS','WPG' => 'WINNIPEG JETS','BOS' => 'BOSTON BRUINS','BUF' => 'BUFFALO SABRES','CAR' => 'CAROLINA HURRICANES',
'FLA' => 'FLORIDA PANTHERS','MTL' => 'MONTREAL CANADIENS','NJ' => 'NEW JERSEY DEVILS','NYI' => 'NEW YORK ISLANDERS','NYR' => 'NEW YORK RANGERS',
'OTT' => 'OTTAWA SENATORS','PHI' => 'PHILADELPHIA FLYERS','PIT' => 'PITTSBURGH PENGUINS','TB' => 'TAMPA BAY LIGHTNING','TOR' => 'TORONTO MAPLE LEAFS','WSH' => 'WASHINGTON CAPITALS');
if($player_name['pos'] == 'forwards') { $pos = "Forwards"; } 
elseif($player_name['pos'] == 'center') { $pos = "Center"; } 
elseif($player_name['pos'] == 'left_wing') { $pos = "Left Wing"; }
elseif($player_name['pos'] == 'right_wing') { $pos = "Right Wing"; }
elseif($player_name['pos'] == 'defenseman') { $pos = "Defenseman"; }
elseif($player_name['pos'] == 'goalie') { $pos = "Goalie"; }
else { $pos = "UNKNOWN"; }
if(empty($player_name['player_image'])){$palyer_image = "/style/images/medium_default_avatar.jpg";}else{ $palyer_image = $player_name['player_image'];}
if(mysql_num_rows($res)!=0)
{
echo '<span style="float:left">
<font size="4"><b><a href="/player/'.$player_name['display_name'].'">'.$row['player_name'].'</a></b></font><br><b>Position:</b> '.$pos.'<div style="height: 8px;"></div>
<img src="/style/images/teams/medium/colorful/'.$row['team'].'.png"><div style="height: 4px;"></div>
<font size="2"><b>'.$team_values[$row['team']].'</b></font><div style="height: 33px;"></div>
<font size="5"><b>HITS: '.$row['hits'].'</b></font>
</span><div style="float:right; padding-left:-5px; background: url('.$palyer_image.') no-repeat;" id="first_place_player_left"><img height="30px" src="/style/images/first_place.png"></div>';
$res1 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Player' AND league_id = '" . LEAGUE_ID . "' ORDER BY hits DESC LIMIT 1,2");
$row1 = mysql_fetch_array($res1);
$points1 = $row1['goal'] + $row1['asst'];
$sekundes1 = $row1['time']/$row1['gp'];
$min1 = floor($sekundes1/60);
$sec1 = $sekundes1 % 60;
if(strlen($sec1) == 1){$zero1 = 0;}else{$zero1 = '' ;}
$simbols1 = $row1['plus_minus'] > 0 ? "+":"";
$plus_minuss1 = $simbols1.(int)$row1['plus_minus'];
$izvelk_player1 = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row1[player_name]'");
$player_name1 = mysql_fetch_array($izvelk_player1); 
echo '<table class="west" cellspacing="0" cellpadding="1" style="width: 306px;">
<tr style="color:#E2E4E3; font-weight:bold;" bgcolor="#6F7070"><td width="17"><center>#</center></td><td width="100"></td><td width="27"><center>Team</center></td><td width="27"><center>GP</center><td width="27"><center>G</center><td width="27"><center>A</center><td width="27"><center>P</center><td width="27"><center>+/-</center><td width="27"><center>HITS</center><td width="27"><center>TOI</center></tr>
';
echo "<tr style='background-color:#E8E8E8;'><td class='turnirs'><center>2</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name1['display_name']."'>".$row1['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row1['team'].".png'></td><td class='turnirs'><center>".$row1['gp']."</center></td><td class='turnirs'><center>".$row1['goal']."</center></td><td class='turnirs'><center>".$row1['asst']."</center></td><td class='turnirs'><center>".$points1."</center></td><td class='turnirs'><center>".$plus_minuss1."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$row1['hits']."</center></td><td class='turnirs_beigas'><center>".$min1 . ':'.$zero1.'' . $sec1."</center></td>";
$res2 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Player' AND league_id = '" . LEAGUE_ID . "' ORDER BY hits DESC LIMIT 2,3");
$row2=mysql_fetch_array($res2);
$points2 = $row2['goal'] + $row2['asst'];
$sekundes2 = $row2['time']/$row2['gp'];
$min2 = floor($sekundes2/60);
$sec2 = $sekundes2 % 60;
if(strlen($sec2) == 1){$zero2 = 0;}else{$zero2 = '' ;}
$simbols2 = $row2['plus_minus'] > 0 ? "+":"";
$plus_minuss2 = $simbols2.(int)$row2['plus_minus'];
$izvelk_player2 = mysql_query("SELECT * FROM nhl_players WHERE player_name = '$row2[player_name]'");
$player_name2 = mysql_fetch_array($izvelk_player2); 
echo "<tr style='background-color:#FFFFFF;'><td class='turnirs'><center>3</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name2['display_name']."'>".$row2['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row2['team'].".png'></td><td class='turnirs'><center>".$row2['gp']."</center></td><td class='turnirs'><center>".$row2['goal']."</center></td><td class='turnirs'><center>".$row2['asst']."</center></td><td class='turnirs'><center>".$points2."</center></td><td class='turnirs'><center>".$plus_minuss2."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$row2['hits']."</center></td><td class='turnirs_beigas'><center>".$min2 . ':'.$zero2.'' . $sec2."</center></td>";
$res3 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Player' AND league_id = '" . LEAGUE_ID . "' ORDER BY hits DESC LIMIT 3,4");
$row3=mysql_fetch_array($res3);
$points3 = $row3['goal'] + $row3['asst'];
$sekundes3 = $row3['time']/$row3['gp'];
$min3 = floor($sekundes3/60);
$sec3 = $sekundes3 % 60;
if(strlen($sec3) == 1){$zero3 = 0;}else{$zero3 = '' ;}
$simbols3 = $row3['plus_minus'] > 0 ? "+":"";
$plus_minuss3 = $simbols3.(int)$row3['plus_minus'];
$izvelk_player3 = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row3[player_name]'");
$player_name3 = mysql_fetch_array($izvelk_player3); 
echo "<tr style='background-color:#E8E8E8;'><td class='turnirs'><center>4</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name3['display_name']."'>".$row3['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row3['team'].".png'></td><td class='turnirs'><center>".$row3['gp']."</center></td><td class='turnirs'><center>".$row3['goal']."</center></td><td class='turnirs'><center>".$row3['asst']."</center></td><td class='turnirs'><center>".$points3."</center></td><td class='turnirs'><center>".$plus_minuss3."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$row3['hits']."</center></td><td class='turnirs_beigas'><center>".$min3 . ':'.$zero3.'' . $sec3."</center></td>";
$res4 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Player' AND league_id = '" . LEAGUE_ID . "' ORDER BY hits DESC LIMIT 4,5");
$row4=mysql_fetch_array($res4);
$points4 = $row4['goal'] + $row4['asst'];
$sekundes4 = $row4['time']/$row4['gp'];
$min4 = floor($sekundes4/60);
$sec4 = $sekundes4 % 60;
if(strlen($sec4) == 1){$zero4 = 0;}else{$zero4 = '' ;}
$simbols4 = $row4['plus_minus'] > 0 ? "+":"";
$plus_minuss4 = $simbols4.(int)$row4['plus_minus'];
$izvelk_player4 = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row4[player_name]'");
$player_name4 = mysql_fetch_array($izvelk_player4); 
echo "<tr style='background-color:#FFFFFF;'><td class='turnirs'><center>5</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name4['display_name']."'>".$row4['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row4['team'].".png'></td><td class='turnirs'><center>".$row4['gp']."</center></td><td class='turnirs'><center>".$row4['goal']."</center></td><td class='turnirs'><center>".$row4['asst']."</center></td><td class='turnirs'><center>".$points4."</center></td><td class='turnirs'><center>".$plus_minuss4."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$row4['hits']."</center></td><td class='turnirs_beigas'><center>".$min4 . ':'.$zero4.'' . $sec4."</center></td></tr></table>";
}
else
{
echo 'NHL #' . LEAGUE_ID . ' līga vēl nav izspēlēta neviena spēle,<br> tāpēc pašlaik nav statistikas ko ievākt!';
}
?>
</div>
<div class="box_bottom"></div>
</div>
</div>
<img style="padding-left:125px;padding-top:30px;" src="http://sportsdigita.com/wp-content/themes/sportsdigita/images/logo-nhl.png">
	<div style="float:right; width:340px;">
		<div class="box">
<h6>PLUS-MINUS</h6>
<div class="box_content">
<?
$res = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Player' AND league_id = '" . LEAGUE_ID . "' ORDER BY plus_minus DESC LIMIT 0,1");
$row=mysql_fetch_array($res);
$points = $row['goal'] + $row['asst'];
$izvelk_player = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row[player_name]'");
$player_name = mysql_fetch_array($izvelk_player); 
$team_values = array ('ANA' => 'ANAHEIM DUCKS','CGY' => 'CALGARY FLAMES','CHI' => 'CHICAGO BLACKHAWKS','COL' => 'COLORADO AVALANCHE',
'CBJ' => 'COLUMBUS B. JACKETS','DAL' => 'DALLAS STARS','DET' => 'DETROIT RED WINGS','EDM' => 'EDMONTON OILERS','LA' => 'LOS ANGELES KINGS',
'MIN' => 'MINNESOTA WILD','NSH' => 'NASHVILLE PREDATORS','PHX' => 'PHOENIX COYOTES','SJ' => 'SAN JOSE SHARKS','STL' => 'ST LOUIS BLUES',
'VAN' => 'VANCOUVER CANUCKS','WPG' => 'WINNIPEG JETS','BOS' => 'BOSTON BRUINS','BUF' => 'BUFFALO SABRES','CAR' => 'CAROLINA HURRICANES',
'FLA' => 'FLORIDA PANTHERS','MTL' => 'MONTREAL CANADIENS','NJ' => 'NEW JERSEY DEVILS','NYI' => 'NEW YORK ISLANDERS','NYR' => 'NEW YORK RANGERS',
'OTT' => 'OTTAWA SENATORS','PHI' => 'PHILADELPHIA FLYERS','PIT' => 'PITTSBURGH PENGUINS','TB' => 'TAMPA BAY LIGHTNING','TOR' => 'TORONTO MAPLE LEAFS','WSH' => 'WASHINGTON CAPITALS');
$simbolsx = $row['plus_minus'] > 0 ? "+":"";
$plus_minussx = $simbolsx.(int)$row['plus_minus'];
if($player_name['pos'] == 'forwards') { $pos = "Forwards"; } 
elseif($player_name['pos'] == 'center') { $pos = "Center"; } 
elseif($player_name['pos'] == 'left_wing') { $pos = "Left Wing"; }
elseif($player_name['pos'] == 'right_wing') { $pos = "Right Wing"; }
elseif($player_name['pos'] == 'defenseman') { $pos = "Defenseman"; }
elseif($player_name['pos'] == 'goalie') { $pos = "Goalie"; }
else { $pos = "UNKNOWN"; }
if(empty($player_name['player_image'])){$palyer_image = "/style/images/medium_default_avatar.jpg";}else{ $palyer_image = $player_name['player_image'];}
if(mysql_num_rows($res)!=0)
{
echo '<span style="float:left">
<font size="4"><b><a href="/player/'.$player_name['display_name'].'">'.$row['player_name'].'</a></b></font><br><b>Position:</b> '.$pos.'<div style="height: 8px;"></div>
<img src="/style/images/teams/medium/colorful/'.$row['team'].'.png"><div style="height: 4px;"></div>
<font size="2"><b>'.$team_values[$row['team']].'</b></font><div style="height: 33px;"></div>
<font size="5"><b>PLUS-MINUS: '.$plus_minussx.'</b></font>
</span><div style="float:right; padding-left:-5px; background: url('.$palyer_image.') no-repeat;" id="first_place_player_right"><img height="30px" src="/style/images/first_place.png"></div>';
$res1 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Player' AND league_id = '" . LEAGUE_ID . "' ORDER BY plus_minus DESC LIMIT 1,2");
$row1 = mysql_fetch_array($res1);
$points1 = $row1['goal'] + $row1['asst'];
$sekundes1 = $row1['time']/$row1['gp'];
$min1 = floor($sekundes1/60);
$sec1 = $sekundes1 % 60;
if(strlen($sec1) == 1){$zero1 = 0;}else{$zero1 = '' ;}
$simbols1 = $row1['plus_minus'] > 0 ? "+":"";
$plus_minuss1 = $simbols1.(int)$row1['plus_minus'];
$izvelk_player1 = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row1[player_name]'");
$player_name1 = mysql_fetch_array($izvelk_player1); 
echo '<table class="west" cellspacing="0" cellpadding="1" style="width: 317px;">
<tr style="color:#E2E4E3; font-weight:bold;" bgcolor="#6F7070"><td width="17"><center>#</center></td><td width="100"></td><td width="27"><center>Team</center></td><td width="27"><center>GP</center><td width="27"><center>G</center><td width="27"><center>A</center><td width="27"><center>P</center><td width="27"><center>+/-</center><td width="27"><center>PIM</center><td width="27"><center>TOI</center></tr>
';
echo "<tr style='background-color:#E8E8E8;'><td class='turnirs'><center>2</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name1['display_name']."'>".$row1['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row1['team'].".png'></td><td class='turnirs'><center>".$row1['gp']."</center></td><td class='turnirs'><center>".$row1['goal']."</center></td><td class='turnirs'><center>".$row1['asst']."</center></td><td class='turnirs'><center>".$points1."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$plus_minuss1."</center></td><td class='turnirs'><center>".$row1['pims']."</center></td><td class='turnirs_beigas'><center>".$min1 . ':'.$zero1.'' . $sec1."</center></td>";
$res2 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Player' AND league_id = '" . LEAGUE_ID . "' ORDER BY plus_minus DESC LIMIT 2,3");
$row2=mysql_fetch_array($res2);
$points2 = $row2['goal'] + $row2['asst'];
$sekundes2 = $row2['time']/$row2['gp'];
$min2 = floor($sekundes2/60);
$sec2 = $sekundes2 % 60;
if(strlen($sec2) == 1){$zero2 = 0;}else{$zero2 = '' ;}
$simbols2 = $row2['plus_minus'] > 0 ? "+":"";
$plus_minuss2 = $simbols2.(int)$row2['plus_minus'];
$izvelk_player2 = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row2[player_name]'");
$player_name2 = mysql_fetch_array($izvelk_player2); 
echo "<tr style='background-color:#FFFFFF;'><td class='turnirs'><center>3</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name2['display_name']."'>".$row2['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row2['team'].".png'></td><td class='turnirs'><center>".$row2['gp']."</center></td><td class='turnirs'><center>".$row2['goal']."</center></td><td class='turnirs'><center>".$row2['asst']."</center></td><td class='turnirs'><center>".$points2."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$plus_minuss2."</center></td><td class='turnirs'><center>".$row2['pims']."</center></td><td class='turnirs_beigas'><center>".$min2 . ':'.$zero2.'' . $sec2."</center></td>";
$res3 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Player' AND league_id = '" . LEAGUE_ID . "' ORDER BY plus_minus DESC LIMIT 3,4");
$row3=mysql_fetch_array($res3);
$points3 = $row3['goal'] + $row3['asst'];
$sekundes3 = $row3['time']/$row3['gp'];
$min3 = floor($sekundes3/60);
$sec3 = $sekundes3 % 60;
if(strlen($sec3) == 1){$zero3 = 0;}else{$zero3 = '' ;}
$simbols3 = $row3['plus_minus'] > 0 ? "+":"";
$plus_minuss3 = $simbols3.(int)$row3['plus_minus'];
$izvelk_player3 = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row3[player_name]'");
$player_name3 = mysql_fetch_array($izvelk_player3); 
echo "<tr style='background-color:#E8E8E8;'><td class='turnirs'><center>4</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name3['display_name']."'>".$row3['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row3['team'].".png'></td><td class='turnirs'><center>".$row3['gp']."</center></td><td class='turnirs'><center>".$row3['goal']."</center></td><td class='turnirs'><center>".$row3['asst']."</center></td><td class='turnirs'><center>".$points3."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$plus_minuss3."</center></td><td class='turnirs'><center>".$row3['pims']."</center></td><td class='turnirs_beigas'><center>".$min3 . ':'.$zero3.'' . $sec3."</center></td>";
$res4 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Player' AND league_id = '" . LEAGUE_ID . "' ORDER BY plus_minus DESC LIMIT 4,5");
$row4=mysql_fetch_array($res4);
$points4 = $row4['goal'] + $row4['asst'];
$sekundes4 = $row4['time']/$row4['gp'];
$min4 = floor($sekundes4/60);
$sec4 = $sekundes4 % 60;
if(strlen($sec4) == 1){$zero4 = 0;}else{$zero4 = '' ;}
$simbols4 = $row4['plus_minus'] > 0 ? "+":"";
$plus_minuss4 = $simbols4.(int)$row4['plus_minus'];
$izvelk_player4 = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row4[player_name]'");
$player_name4 = mysql_fetch_array($izvelk_player4); 
echo "<tr style='background-color:#FFFFFF;'><td class='turnirs'><center>5</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name4['display_name']."'>".$row4['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row4['team'].".png'></td><td class='turnirs'><center>".$row4['gp']."</center></td><td class='turnirs'><center>".$row4['goal']."</center></td><td class='turnirs'><center>".$row4['asst']."</center></td><td class='turnirs'><center>".$points4."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$plus_minuss4."</center></td><td class='turnirs'><center>".$row4['pims']."</center></td><td class='turnirs_beigas'><center>".$min4 . ':'.$zero4.'' . $sec4."</center></td></tr></table>";
}
else
{
echo 'NHL #' . LEAGUE_ID . ' līga vēl nav izspēlēta neviena spēle,<br> tāpēc pašlaik nav statistikas ko ievākt!';
}
?>
</div>
<div class="box_bottom"></div>
</div>
<div class="box">
<h6>Goals Against Average</h6>
<div class="box_content">
<?
$res = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Goalie' AND gp >= 5 AND league_id = '" . LEAGUE_ID . "' ORDER BY ppg_ga / gp ASC LIMIT 0,1");
$row=mysql_fetch_array($res);
$goalies_stats_average = number_format($row['ppg_ga'] / $row['gp'], 2);
$stats_procents = round(($row['ppg_ga'] / $row['shot_sa']) * 100, 2);
$goalies_stats_procents = 100 - $stats_procents;
$goalies_saves = $row['shot_sa'] - $row['ppg_ga'];
$izvelk_player = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row[player_name]'");
$player_name = mysql_fetch_array($izvelk_player); 
$team_values = array ('ANA' => 'ANAHEIM DUCKS','CGY' => 'CALGARY FLAMES','CHI' => 'CHICAGO BLACKHAWKS','COL' => 'COLORADO AVALANCHE',
'CBJ' => 'COLUMBUS B. JACKETS','DAL' => 'DALLAS STARS','DET' => 'DETROIT RED WINGS','EDM' => 'EDMONTON OILERS','LA' => 'LOS ANGELES KINGS',
'MIN' => 'MINNESOTA WILD','NSH' => 'NASHVILLE PREDATORS','PHX' => 'PHOENIX COYOTES','SJ' => 'SAN JOSE SHARKS','STL' => 'ST LOUIS BLUES',
'VAN' => 'VANCOUVER CANUCKS','WPG' => 'WINNIPEG JETS','BOS' => 'BOSTON BRUINS','BUF' => 'BUFFALO SABRES','CAR' => 'CAROLINA HURRICANES',
'FLA' => 'FLORIDA PANTHERS','MTL' => 'MONTREAL CANADIENS','NJ' => 'NEW JERSEY DEVILS','NYI' => 'NEW YORK ISLANDERS','NYR' => 'NEW YORK RANGERS',
'OTT' => 'OTTAWA SENATORS','PHI' => 'PHILADELPHIA FLYERS','PIT' => 'PITTSBURGH PENGUINS','TB' => 'TAMPA BAY LIGHTNING','TOR' => 'TORONTO MAPLE LEAFS','WSH' => 'WASHINGTON CAPITALS');
if($player_name['pos'] == 'forwards') { $pos = "Forwards"; } 
elseif($player_name['pos'] == 'center') { $pos = "Center"; } 
elseif($player_name['pos'] == 'left_wing') { $pos = "Left Wing"; }
elseif($player_name['pos'] == 'right_wing') { $pos = "Right Wing"; }
elseif($player_name['pos'] == 'defenseman') { $pos = "Defenseman"; }
elseif($player_name['pos'] == 'goalie') { $pos = "Goalie"; }
else { $pos = "UNKNOWN"; }
if(empty($player_name['player_image'])){$palyer_image = "/style/images/medium_default_avatar.jpg";}else{ $palyer_image = $player_name['player_image'];}
if(mysql_num_rows($res)!=0)
{
echo '<span style="float:left">
<font size="4"><b><a href="/player/'.$player_name['display_name'].'">'.$row['player_name'].'</a></b></font><br><b>Position:</b> '.$pos.'<div style="height: 8px;"></div>
<img src="/style/images/teams/medium/colorful/'.$row['team'].'.png"><div style="height: 4px;"></div>
<font size="2"><b>'.$team_values[$row['team']].'</b></font><div style="height: 33px;"></div>
<font size="5"><b>GAA: '.$goalies_stats_average.'</b></font>
</span><div style="float:right; padding-left:-5px; background: url('.$palyer_image.') no-repeat;" id="first_place_player_right"><img height="30px" src="/style/images/first_place.png"></div>';
$res1 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Goalie' AND gp >= 5 AND league_id = '" . LEAGUE_ID . "' ORDER BY ppg_ga / gp ASC LIMIT 1,2");
$row1 = mysql_fetch_array($res1);
$goalies_stats_average1 = number_format($row1['ppg_ga'] / $row1['gp'], 2);
$stats_procents1 = round(($row1['ppg_ga'] / $row1['shot_sa']) * 100, 2);
$goalies_stats_procents1 = 100 - $stats_procents1;
$goalies_saves1 = $row1['shot_sa'] - $row1['ppg_ga'];
$sekundes1 = $row1['time']/$row1['gp'];
$min1 = floor($sekundes1/60);
$sec1 = $sekundes1 % 60;
if(strlen($sec1) == 1){$zero1 = 0;}else{$zero1 = '' ;}
$simbols1 = $row1['plus_minus'] > 0 ? "+":"";
$plus_minuss1 = $simbols1.(int)$row1['plus_minus'];
$izvelk_player1 = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row1[player_name]'");
$player_name1 = mysql_fetch_array($izvelk_player1); 
echo '<table class="west" cellspacing="0" cellpadding="1" style="width: 317px;">
<tr style="color:#E2E4E3; font-weight:bold;" bgcolor="#6F7070"><td width="17"><center>#</center></td><td width="100"></td><td width="27"><center>Team</center></td><td width="27"><center>GP</center><td width="27"><center>SA</center><td width="27"><center>GA</center><td width="27"><center>GAA</center><td width="27"><center>SAVES%</center><td width="27"><center>TOI</center></tr>
';
echo "<tr style='background-color:#E8E8E8;'><td class='turnirs'><center>2</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name1['display_name']."'>".$row1['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row1['team'].".png'></td><td class='turnirs'><center>".$row1['gp']."</center></td><td class='turnirs'><center>".$row1['shot_sa']."</center></td><td class='turnirs'><center>".$row1['ppg_ga']."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$goalies_stats_average1."</center></td><td class='turnirs'><center>".$goalies_stats_procents1."%</center></td><td class='turnirs_beigas'><center>".$min1 . ':'.$zero1.'' . $sec1."</center></td>";
$res2 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Goalie' AND gp >= 5 AND league_id = '" . LEAGUE_ID . "' ORDER BY ppg_ga / gp ASC LIMIT 2,3");
$row2=mysql_fetch_array($res2);
$goalies_stats_average2 = number_format($row2['ppg_ga'] / $row2['gp'], 2);
$stats_procents2 = round(($row2['ppg_ga'] / $row2['shot_sa']) * 100, 2);
$goalies_stats_procents2 = 100 - $stats_procents2;
$goalies_saves2 = $row2['shot_sa'] - $row2['ppg_ga'];
$sekundes2 = $row2['time']/$row2['gp'];
$min2 = floor($sekundes2/60);
$sec2 = $sekundes2 % 60;
if(strlen($sec2) == 1){$zero2 = 0;}else{$zero2 = '' ;}
$izvelk_player2 = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row2[player_name]'");
$player_name2 = mysql_fetch_array($izvelk_player2); 
echo "<tr style='background-color:#FFFFFF;'><td class='turnirs'><center>3</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name2['display_name']."'>".$row2['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row2['team'].".png'></td><td class='turnirs'><center>".$row2['gp']."</center></td><td class='turnirs'><center>".$row2['shot_sa']."</center></td><td class='turnirs'><center>".$row2['ppg_ga']."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$goalies_stats_average2."</center></td><td class='turnirs'><center>".$goalies_stats_procents2."%</center></td><td class='turnirs_beigas'><center>".$min2 . ':'.$zero2.'' . $sec2."</center></td>";
$res3 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Goalie' AND gp >= 5 AND league_id = '" . LEAGUE_ID . "' ORDER BY ppg_ga / gp ASC LIMIT 3,4");
$row3=mysql_fetch_array($res3);
$goalies_stats_average3 = number_format($row3['ppg_ga'] / $row3['gp'], 2);
$stats_procents3 = round(($row3['ppg_ga'] / $row3['shot_sa']) * 100, 2);
$goalies_stats_procents3 = 100 - $stats_procents3;
$goalies_saves3 = $row3['shot_sa'] - $row3['ppg_ga'];
$sekundes3 = $row3['time']/$row3['gp'];
$min3 = floor($sekundes3/60);
$sec3 = $sekundes3 % 60;
if(strlen($sec3) == 1){$zero3 = 0;}else{$zero3 = '' ;}
$izvelk_player3 = mysql_query("SELECT * FROM nhl_players WHERE player_name = '$row3[player_name]'");
$player_name3 = mysql_fetch_array($izvelk_player3); 
echo "<tr style='background-color:#E8E8E8;'><td class='turnirs'><center>4</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name3['display_name']."'>".$row3['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row3['team'].".png'></td><td class='turnirs'><center>".$row3['gp']."</center></td><td class='turnirs'><center>".$row3['shot_sa']."</center></td><td class='turnirs'><center>".$row3['ppg_ga']."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$goalies_stats_average3."</center></td><td class='turnirs'><center>".$goalies_stats_procents3."%</center></td><td class='turnirs_beigas'><center>".$min3 . ':'.$zero3.'' . $sec3."</center></td>";
$res4 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Goalie' AND gp >= 5 AND league_id = '" . LEAGUE_ID . "' ORDER BY ppg_ga / gp ASC LIMIT 4,5");
$row4=mysql_fetch_array($res4);
$goalies_stats_average4 = number_format($row4['ppg_ga'] / $row4['gp'], 2);
$stats_procents4 = round(($row4['ppg_ga'] / $row4['shot_sa']) * 100, 2);
$goalies_stats_procents4 = 100 - $stats_procents4;
$goalies_saves4 = $row4['shot_sa'] - $row4['ppg_ga'];
$sekundes4 = $row4['time']/$row4['gp'];
$min4 = floor($sekundes4/60);
$sec4 = $sekundes4 % 60;
if(strlen($sec4) == 1){$zero4 = 0;}else{$zero4 = '' ;}
$izvelk_player4 = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row4[player_name]'");
$player_name4 = mysql_fetch_array($izvelk_player4); 
echo "<tr style='background-color:#FFFFFF;'><td class='turnirs'><center>5</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name4['display_name']."'>".$row4['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row4['team'].".png'></td><td class='turnirs'><center>".$row4['gp']."</center></td><td class='turnirs'><center>".$row4['shot_sa']."</center></td><td class='turnirs'><center>".$row4['ppg_ga']."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$goalies_stats_average4."</center></td><td class='turnirs'><center>".$goalies_stats_procents4."%</center></td><td class='turnirs_beigas'><center>".$min4 . ':'.$zero4.'' . $sec4."</center></td></tr></table>";
}
else
{
echo 'NHL #' . LEAGUE_ID . ' līga vēl nav izspēlēta neviena spēle,<br> tāpēc pašlaik nav statistikas ko ievākt!';
}
?>
</div>
<div class="box_bottom"></div>
</div>
<div class="box">
<h6>SAVES %</h6>
<div class="box_content">
<?
$res = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Goalie' AND gp >= 5 AND league_id = '" . LEAGUE_ID . "' ORDER BY ppg_ga / shot_sa ASC LIMIT 0,1");
$row=mysql_fetch_array($res);
$goalies_stats_average = number_format($row['ppg_ga'] / $row['gp'], 2);
$stats_procents = round(($row['ppg_ga'] / $row['shot_sa']) * 100, 2);
$goalies_stats_procents = 100 - $stats_procents;
$goalies_saves = $row['shot_sa'] - $row['ppg_ga'];
$izvelk_player = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row[player_name]'");
$player_name = mysql_fetch_array($izvelk_player); 
$team_values = array ('ANA' => 'ANAHEIM DUCKS','CGY' => 'CALGARY FLAMES','CHI' => 'CHICAGO BLACKHAWKS','COL' => 'COLORADO AVALANCHE',
'CBJ' => 'COLUMBUS B. JACKETS','DAL' => 'DALLAS STARS','DET' => 'DETROIT RED WINGS','EDM' => 'EDMONTON OILERS','LA' => 'LOS ANGELES KINGS',
'MIN' => 'MINNESOTA WILD','NSH' => 'NASHVILLE PREDATORS','PHX' => 'PHOENIX COYOTES','SJ' => 'SAN JOSE SHARKS','STL' => 'ST LOUIS BLUES',
'VAN' => 'VANCOUVER CANUCKS','WPG' => 'WINNIPEG JETS','BOS' => 'BOSTON BRUINS','BUF' => 'BUFFALO SABRES','CAR' => 'CAROLINA HURRICANES',
'FLA' => 'FLORIDA PANTHERS','MTL' => 'MONTREAL CANADIENS','NJ' => 'NEW JERSEY DEVILS','NYI' => 'NEW YORK ISLANDERS','NYR' => 'NEW YORK RANGERS',
'OTT' => 'OTTAWA SENATORS','PHI' => 'PHILADELPHIA FLYERS','PIT' => 'PITTSBURGH PENGUINS','TB' => 'TAMPA BAY LIGHTNING','TOR' => 'TORONTO MAPLE LEAFS','WSH' => 'WASHINGTON CAPITALS');
if($player_name['pos'] == 'forwards') { $pos = "Forwards"; } 
elseif($player_name['pos'] == 'center') { $pos = "Center"; } 
elseif($player_name['pos'] == 'left_wing') { $pos = "Left Wing"; }
elseif($player_name['pos'] == 'right_wing') { $pos = "Right Wing"; }
elseif($player_name['pos'] == 'defenseman') { $pos = "Defenseman"; }
elseif($player_name['pos'] == 'goalie') { $pos = "Goalie"; }
else { $pos = "UNKNOWN"; }
if(empty($player_name['player_image'])){$palyer_image = "/style/images/medium_default_avatar.jpg";}else{ $palyer_image = $player_name['player_image'];}
if(mysql_num_rows($res)!=0)
{
echo '<span style="float:left">
<font size="4"><b><a href="/player/'.$player_name['display_name'].'">'.$row['player_name'].'</a></b></font><br><b>Position:</b> '.$pos.'<div style="height: 8px;"></div>
<img src="/style/images/teams/medium/colorful/'.$row['team'].'.png"><div style="height: 4px;"></div>
<font size="2"><b>'.$team_values[$row['team']].'</b></font><div style="height: 33px;"></div>
<font size="5"><b>SAVES%: '.$goalies_stats_procents.'%</b></font>
</span><div style="float:right; padding-left:-5px; background: url('.$palyer_image.') no-repeat;" id="first_place_player_right"><img height="30px" src="/style/images/first_place.png"></div>';
$res1 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Goalie' AND gp >= 5 AND league_id = '" . LEAGUE_ID . "' ORDER BY ppg_ga / shot_sa ASC LIMIT 1,2");
$row1 = mysql_fetch_array($res1);
$goalies_stats_average1 = number_format($row1['ppg_ga'] / $row1['gp'], 2);
$stats_procents1 = round(($row1['ppg_ga'] / $row1['shot_sa']) * 100, 2);
$goalies_stats_procents1 = 100 - $stats_procents1;
$goalies_saves1 = $row1['shot_sa'] - $row1['ppg_ga'];
$sekundes1 = $row1['time']/$row1['gp'];
$min1 = floor($sekundes1/60);
$sec1 = $sekundes1 % 60;
if(strlen($sec1) == 1){$zero1 = 0;}else{$zero1 = '' ;}
$simbols1 = $row1['plus_minus'] > 0 ? "+":"";
$plus_minuss1 = $simbols1.(int)$row1['plus_minus'];
$izvelk_player1 = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row1[player_name]'");
$player_name1 = mysql_fetch_array($izvelk_player1); 
echo '<table class="west" cellspacing="0" cellpadding="1" style="width: 317px;">
<tr style="color:#E2E4E3; font-weight:bold;" bgcolor="#6F7070"><td width="17"><center>#</center></td><td width="100"></td><td width="27"><center>Team</center></td><td width="27"><center>GP</center><td width="27"><center>SA</center><td width="27"><center>GA</center><td width="27"><center>GAA</center><td width="27"><center>SAVES%</center><td width="27"><center>TOI</center></tr>
';
echo "<tr style='background-color:#E8E8E8;'><td class='turnirs'><center>2</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name1['display_name']."'>".$row1['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row1['team'].".png'></td><td class='turnirs'><center>".$row1['gp']."</center></td><td class='turnirs'><center>".$row1['shot_sa']."</center></td><td class='turnirs'><center>".$row1['ppg_ga']."</center></td><td class='turnirs'><center>".$goalies_stats_average1."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$goalies_stats_procents1."%</center></td><td class='turnirs_beigas'><center>".$min1 . ':'.$zero1.'' . $sec1."</center></td>";
$res2 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Goalie' AND gp >= 5 AND league_id = '" . LEAGUE_ID . "' ORDER BY ppg_ga / shot_sa ASC LIMIT 2,3");
$row2=mysql_fetch_array($res2);
$goalies_stats_average2 = number_format($row2['ppg_ga'] / $row2['gp'], 2);
$stats_procents2 = round(($row2['ppg_ga'] / $row2['shot_sa']) * 100, 2);
$goalies_stats_procents2 = 100 - $stats_procents2;
$goalies_saves2 = $row2['shot_sa'] - $row2['ppg_ga'];
$sekundes2 = $row2['time']/$row2['gp'];
$min2 = floor($sekundes2/60);
$sec2 = $sekundes2 % 60;
if(strlen($sec2) == 1){$zero2 = 0;}else{$zero2 = '' ;}
$izvelk_player2 = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row2[player_name]'");
$player_name2 = mysql_fetch_array($izvelk_player2); 
echo "<tr style='background-color:#FFFFFF;'><td class='turnirs'><center>3</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name2['display_name']."'>".$row2['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row2['team'].".png'></td><td class='turnirs'><center>".$row2['gp']."</center></td><td class='turnirs'><center>".$row2['shot_sa']."</center></td><td class='turnirs'><center>".$row2['ppg_ga']."</center></td><td class='turnirs'><center>".$goalies_stats_average2."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$goalies_stats_procents2."%</center></td><td class='turnirs_beigas'><center>".$min2 . ':'.$zero2.'' . $sec2."</center></td>";
$res3 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Goalie' AND gp >= 5 AND league_id = '" . LEAGUE_ID . "' ORDER BY ppg_ga / shot_sa ASC LIMIT 3,4");
$row3=mysql_fetch_array($res3);
$goalies_stats_average3 = number_format($row3['ppg_ga'] / $row3['gp'], 2);
$stats_procents3 = round(($row3['ppg_ga'] / $row3['shot_sa']) * 100, 2);
$goalies_stats_procents3 = 100 - $stats_procents3;
$goalies_saves3 = $row3['shot_sa'] - $row3['ppg_ga'];
$sekundes3 = $row3['time']/$row3['gp'];
$min3 = floor($sekundes3/60);
$sec3 = $sekundes3 % 60;
if(strlen($sec3) == 1){$zero3 = 0;}else{$zero3 = '' ;}
$izvelk_player3 = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row3[player_name]'");
$player_name3 = mysql_fetch_array($izvelk_player3); 
echo "<tr style='background-color:#E8E8E8;'><td class='turnirs'><center>4</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name3['display_name']."'>".$row3['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row3['team'].".png'></td><td class='turnirs'><center>".$row3['gp']."</center></td><td class='turnirs'><center>".$row3['shot_sa']."</center></td><td class='turnirs'><center>".$row3['ppg_ga']."</center></td><td class='turnirs'><center>".$goalies_stats_average3."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$goalies_stats_procents3."%</center></td><td class='turnirs_beigas'><center>".$min3 . ':'.$zero3.'' . $sec3."</center></td>";
$res4 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Goalie' AND gp >= 5 AND league_id = '" . LEAGUE_ID . "' ORDER BY ppg_ga / shot_sa ASC LIMIT 4,5");
$row4=mysql_fetch_array($res4);
$goalies_stats_average4 = number_format($row4['ppg_ga'] / $row4['gp'], 2);
$stats_procents4 = round(($row4['ppg_ga'] / $row4['shot_sa']) * 100, 2);
$goalies_stats_procents4 = 100 - $stats_procents4;
$goalies_saves4 = $row4['shot_sa'] - $row4['ppg_ga'];
$sekundes4 = $row4['time']/$row4['gp'];
$min4 = floor($sekundes4/60);
$sec4 = $sekundes4 % 60;
if(strlen($sec4) == 1){$zero4 = 0;}else{$zero4 = '' ;}
$izvelk_player4 = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row4[player_name]'");
$player_name4 = mysql_fetch_array($izvelk_player4); 
echo "<tr style='background-color:#FFFFFF;'><td class='turnirs'><center>5</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name4['display_name']."'>".$row4['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row4['team'].".png'></td><td class='turnirs'><center>".$row4['gp']."</center></td><td class='turnirs'><center>".$row4['shot_sa']."</center></td><td class='turnirs'><center>".$row4['ppg_ga']."</center></td><td class='turnirs'><center>".$goalies_stats_average4."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$goalies_stats_procents4."%</center></td><td class='turnirs_beigas'><center>".$min4 . ':'.$zero4.'' . $sec4."</center></td></tr></table>";
}
else
{
echo 'NHL #' . LEAGUE_ID . ' līga vēl nav izspēlēta neviena spēle,<br> tāpēc pašlaik nav statistikas ko ievākt!';
}
?>
</div>
<div class="box_bottom"></div>
</div>
<div class="box">
<h6>Penalty minutes</h6>
<div class="box_content">
<?
$res = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Player' AND league_id = '" . LEAGUE_ID . "' ORDER BY pims DESC LIMIT 0,1");
$row=mysql_fetch_array($res);
$points = $row['goal'] + $row['asst'];
$izvelk_player = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row[player_name]'");
$player_name = mysql_fetch_array($izvelk_player); 
$team_values = array ('ANA' => 'ANAHEIM DUCKS','CGY' => 'CALGARY FLAMES','CHI' => 'CHICAGO BLACKHAWKS','COL' => 'COLORADO AVALANCHE',
'CBJ' => 'COLUMBUS B. JACKETS','DAL' => 'DALLAS STARS','DET' => 'DETROIT RED WINGS','EDM' => 'EDMONTON OILERS','LA' => 'LOS ANGELES KINGS',
'MIN' => 'MINNESOTA WILD','NSH' => 'NASHVILLE PREDATORS','PHX' => 'PHOENIX COYOTES','SJ' => 'SAN JOSE SHARKS','STL' => 'ST LOUIS BLUES',
'VAN' => 'VANCOUVER CANUCKS','WPG' => 'WINNIPEG JETS','BOS' => 'BOSTON BRUINS','BUF' => 'BUFFALO SABRES','CAR' => 'CAROLINA HURRICANES',
'FLA' => 'FLORIDA PANTHERS','MTL' => 'MONTREAL CANADIENS','NJ' => 'NEW JERSEY DEVILS','NYI' => 'NEW YORK ISLANDERS','NYR' => 'NEW YORK RANGERS',
'OTT' => 'OTTAWA SENATORS','PHI' => 'PHILADELPHIA FLYERS','PIT' => 'PITTSBURGH PENGUINS','TB' => 'TAMPA BAY LIGHTNING','TOR' => 'TORONTO MAPLE LEAFS','WSH' => 'WASHINGTON CAPITALS');
if($player_name['pos'] == 'forwards') { $pos = "Forwards"; } 
elseif($player_name['pos'] == 'center') { $pos = "Center"; } 
elseif($player_name['pos'] == 'left_wing') { $pos = "Left Wing"; }
elseif($player_name['pos'] == 'right_wing') { $pos = "Right Wing"; }
elseif($player_name['pos'] == 'defenseman') { $pos = "Defenseman"; }
elseif($player_name['pos'] == 'goalie') { $pos = "Goalie"; }
else { $pos = "UNKNOWN"; }
if(empty($player_name['player_image'])){$palyer_image = "/style/images/medium_default_avatar.jpg";}else{ $palyer_image = $player_name['player_image'];}
if(mysql_num_rows($res)!=0)
{
echo '<span style="float:left">
<font size="4"><b><a href="/player/'.$player_name['display_name'].'">'.$row['player_name'].'</a></b></font><br><b>Position:</b> '.$pos.'<div style="height: 8px;"></div>
<img src="/style/images/teams/medium/colorful/'.$row['team'].'.png"><div style="height: 4px;"></div>
<font size="2"><b>'.$team_values[$row['team']].'</b></font><div style="height: 33px;"></div>
<font size="5"><b>PENALTY: '.$row['pims'].' min.</b></font>
</span><div style="float:right; padding-left:-5px; background: url('.$palyer_image.') no-repeat;" id="first_place_player_left"><img height="30px" src="/style/images/first_place.png"></div>';
$res1 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Player' AND league_id = '" . LEAGUE_ID . "' ORDER BY pims DESC LIMIT 1,2");
$row1 = mysql_fetch_array($res1);
$points1 = $row1['goal'] + $row1['asst'];
$sekundes1 = $row1['time']/$row1['gp'];
$min1 = floor($sekundes1/60);
$sec1 = $sekundes1 % 60;
if(strlen($sec1) == 1){$zero1 = 0;}else{$zero1 = '' ;}
$simbols1 = $row1['plus_minus'] > 0 ? "+":"";
$plus_minuss1 = $simbols1.(int)$row1['plus_minus'];
$izvelk_player1 = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row1[player_name]'");
$player_name1 = mysql_fetch_array($izvelk_player1); 
echo '<table class="west" cellspacing="0" cellpadding="1" style="width: 317px;">
<tr style="color:#E2E4E3; font-weight:bold;" bgcolor="#6F7070"><td width="17"><center>#</center></td><td width="100"></td><td width="27"><center>Team</center></td><td width="27"><center>GP</center><td width="27"><center>G</center><td width="27"><center>A</center><td width="27"><center>P</center><td width="27"><center>+/-</center><td width="27"><center>PIM</center><td width="27"><center>TOI</center></tr>
';
echo "<tr style='background-color:#E8E8E8;'><td class='turnirs'><center>2</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name1['display_name']."'>".$row1['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row1['team'].".png'></td><td class='turnirs'><center>".$row1['gp']."</center></td><td class='turnirs'><center>".$row1['goal']."</center></td><td class='turnirs'><center>".$row1['asst']."</center></td><td class='turnirs'><center>".$points1."</center></td><td class='turnirs'><center>".$plus_minuss1."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$row1['pims']."</center></td><td class='turnirs_beigas'><center>".$min1 . ':'.$zero1.'' . $sec1."</center></td>";
$res2 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Player' AND league_id = '" . LEAGUE_ID . "' ORDER BY pims DESC LIMIT 2,3");
$row2=mysql_fetch_array($res2);
$points2 = $row2['goal'] + $row2['asst'];
$sekundes2 = $row2['time']/$row2['gp'];
$min2 = floor($sekundes2/60);
$sec2 = $sekundes2 % 60;
if(strlen($sec2) == 1){$zero2 = 0;}else{$zero2 = '' ;}
$simbols2 = $row2['plus_minus'] > 0 ? "+":"";
$plus_minuss2 = $simbols2.(int)$row2['plus_minus'];
$izvelk_player2 = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row2[player_name]'");
$player_name2 = mysql_fetch_array($izvelk_player2); 
echo "<tr style='background-color:#FFFFFF;'><td class='turnirs'><center>3</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name2['display_name']."'>".$row2['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row2['team'].".png'></td><td class='turnirs'><center>".$row2['gp']."</center></td><td class='turnirs'><center>".$row2['goal']."</center></td><td class='turnirs'><center>".$row2['asst']."</center></td><td class='turnirs'><center>".$points2."</center></td><td class='turnirs'><center>".$plus_minuss2."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$row2['pims']."</center></td><td class='turnirs_beigas'><center>".$min2 . ':'.$zero2.'' . $sec2."</center></td>";
$res3 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Player' AND league_id = '" . LEAGUE_ID . "' ORDER BY pims DESC LIMIT 3,4");
$row3=mysql_fetch_array($res3);
$points3 = $row3['goal'] + $row3['asst'];
$sekundes3 = $row3['time']/$row3['gp'];
$min3 = floor($sekundes3/60);
$sec3 = $sekundes3 % 60;
if(strlen($sec3) == 1){$zero3 = 0;}else{$zero3 = '' ;}
$simbols3 = $row3['plus_minus'] > 0 ? "+":"";
$plus_minuss3 = $simbols3.(int)$row3['plus_minus'];
$izvelk_player3 = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row3[player_name]'");
$player_name3 = mysql_fetch_array($izvelk_player3); 
echo "<tr style='background-color:#E8E8E8;'><td class='turnirs'><center>4</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name3['display_name']."'>".$row3['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row3['team'].".png'></td><td class='turnirs'><center>".$row3['gp']."</center></td><td class='turnirs'><center>".$row3['goal']."</center></td><td class='turnirs'><center>".$row3['asst']."</center></td><td class='turnirs'><center>".$points3."</center></td><td class='turnirs'><center>".$plus_minuss3."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$row3['pims']."</center></td><td class='turnirs_beigas'><center>".$min3 . ':'.$zero3.'' . $sec3."</center></td>";
$res4 = mysql_query("SELECT * FROM league_nhl_statistics WHERE pos = 'Player' AND league_id = '" . LEAGUE_ID . "' ORDER BY pims DESC LIMIT 4,5");
$row4=mysql_fetch_array($res4);
$points4 = $row4['goal'] + $row4['asst'];
$sekundes4 = $row4['time']/$row4['gp'];
$min4 = floor($sekundes4/60);
$sec4 = $sekundes4 % 60;
if(strlen($sec4) == 1){$zero4 = 0;}else{$zero4 = '' ;}
$simbols4 = $row4['plus_minus'] > 0 ? "+":"";
$plus_minuss4 = $simbols4.(int)$row4['plus_minus'];
$izvelk_player4 = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '$row4[player_name]'");
$player_name4 = mysql_fetch_array($izvelk_player4); 
echo "<tr style='background-color:#FFFFFF;'><td class='turnirs'><center>5</center></td><td class='turnirs' style='width:100px; padding-left:5px;'><a href='/player/".$player_name4['display_name']."'>".$row4['player_name']."</a></td><td style='padding-top:5px;' class='turnirs'><span style='padding-left:6px;'><img height='12px' src='/style/images/teams/small/colorful/".$row4['team'].".png'></td><td class='turnirs'><center>".$row4['gp']."</center></td><td class='turnirs'><center>".$row4['goal']."</center></td><td class='turnirs'><center>".$row4['asst']."</center></td><td class='turnirs'><center>".$points4."</center></td><td class='turnirs'><center>".$plus_minuss4."</center></td><td class='turnirs' style='background-color:#D8D8D8;font-weight:bold;'><center>".$row4['pims']."</center></td><td class='turnirs_beigas'><center>".$min4 . ':'.$zero4.'' . $sec4."</center></td></tr></table>";
}
else
{
echo 'NHL #' . LEAGUE_ID . ' līga vēl nav izspēlēta neviena spēle,<br> tāpēc pašlaik nav statistikas ko ievākt!';
}
?>
</div>
<div class="box_bottom"></div>
</div>
	</div>
</div>