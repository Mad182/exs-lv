<?php
$team = $db->fetch( $db->query( "SELECT * FROM league_nhl_standings WHERE team_small = '" . get_get('other') . "' AND league_id = '" . LEAGUE_ID . "'" ) );
if( !$team['team_small'] )
{
	echo error("Atvainojiet, bet šāda komanda turnīrā neeksistē!", "890px");
}
else
{
$team_ranks = mysql_query("SELECT *, pts AS myteamrank, (SELECT COUNT(*)+1 FROM league_nhl_standings WHERE pts>myteamrank) AS teamrank FROM league_nhl_standings WHERE team_small = '".get_get('other')."'") or die(mysql_error());
$team_rank = mysql_fetch_array($team_ranks);
$treneris = $users->info($team['player_id']);
$division_values = array ('pafific' => 'Pacific','northwest' => 'Northwest','central' => 'Central','southeast' => 'Southeast','northeast' => 'Northeast','atlantic' => 'Atlantic');
$conf_values = array ('western' => 'Western','eastern' => 'Eastern');
$team_stat = mysql_query("SELECT SUM(shot_sa) AS shots, SUM(goal) AS goals, SUM(asst) AS assts, SUM(goal+asst) AS goalasst, SUM(plus_minus) AS plusmin, SUM(hits) AS hitss, SUM(ppg_ga) AS ppg, SUM(shg_eng) AS shg, SUM(pims) AS pimss, SUM(gp) AS gps , SUM(goal/shot_sa) AS sa FROM league_nhl_statistics WHERE team = '".get_get('other')."' AND pos = 'Player'");
$team_statistic = mysql_fetch_assoc($team_stat);
$sum_shots = $team_statistic['shots'];
$sum_goals = $team_statistic['goals'];
$sum_asst = $team_statistic['assts'];
$sum_goalasst = $team_statistic['goalasst'];
$sum_plusmin = $team_statistic['plusmin'];
$simbols = $sum_plusmin > 0 ? "+":"";
$plus_min = $simbols.(int)$sum_plusmin;
$sum_hits = $team_statistic['hitss'];
$sum_shg = $team_statistic['shg'];
$sum_pims = $team_statistic['pimss'];
$team_statp = mysql_query("SELECT SUM(ppg_ga) AS ppgs FROM league_nhl_statistics WHERE team = '".get_get('other')."' AND pos = 'Goalie'");
$team_statisticp = mysql_fetch_assoc($team_statp);
$sum_ppgs = $team_statisticp['ppgs'];
$givs = $sum_goals - $sum_ppgs;
$simbols1 = $givs > 0 ? "+":"";
$givs_sum = $simbols1.(int)$givs;
?>
<div style="float:left; width:950px;">
<div style="float:left; width:100px;">
<fieldset class="tree-stars" style="width: 100px;margin-top:25px;"><legend class="tree-stars2"><a href="javascript:;">LOGO</a></legend>
<img alt="<?=$team['team_small']?>" title="<?=$team['team']?>" src="/style/images/teams/large/colorful/<?=$team['team_small']?>.png">
</fieldset>
</div>
<div style="float:left; width:300px; padding-left:40px;">
<fieldset class="tree-stars" style="width: 300px;margin-top:25px;"><legend class="tree-stars2"><a href="javascript:;">KOMANDAS INFORMĀCIJA</a></legend>
<table class="ipbtable" cellspacing="1" style="width:300px;">
<tr bgcolor="white"><td class="row8" style="width:90px;"><b>Komanda</b></td><td class="row3"><?=$team['team_full'];?></td></tr>
<tr bgcolor="white"><td class="row8"><b>Konforence</b></td><td class="row3"><?=$conf_values[$team['conference']];?></td></tr>
<tr bgcolor="white"><td class="row8"><b>Divīzija</b></td><td class="row3"><?=$division_values[$team['division']];?></td></tr>
<tr bgcolor="white"><td class="row8"><b>Treneris</b></td><td class="row3"><?if($team['player_id'] == 0){echo '<i>komandai nav treneris</i>';}else{?>
<a href="/user/<?=$treneris['id'];?>/<?=$treneris['username'];?>/"><?=$treneris['username'];?></a></td></tr><?}?>
</table>
</fieldset>
</div>
<div style="float:left; width:420px; padding-left:40px;">
<fieldset class="tree-stars" style="width: 450px;margin-top:25px;"><legend class="tree-stars2"><a href="javascript:;">KOMANDAS TOP SPĒLĒTĀJI</a></legend>
<?php
$res = $db->query('SELECT * FROM league_nhl_statistics WHERE pos = "Player" AND team = "' . get_get('other') . '" ORDER BY goal DESC LIMIT 1');
while( $row=$db->fetch( $res ) )
{
$goals_points = $row['goal'] + $row['asst'];
?>
<div style="float: left; width: 65px;">
<a href="#">
<span>
	<img src="/style/images/medium_default_avatar.jpg" width="63px" height="93px">
</span>
</a>
</div>
<div style="float: left; width: 83px;">
<a href="#"><b><?php echo $row['player_name']; ?></b></a><br>
Unknown<br>
Goals: <b><?php echo $row['goal']; ?></b><br />Assists: <b><?php echo $row['asst']; ?></b><br />Points: <b><?php echo $goals_points; ?></b>
</div>
<?php
}
?>

<?php
$res = $db->query('SELECT * FROM league_nhl_statistics WHERE pos = "Player" AND team = "' . get_get('other') . '" ORDER BY asst DESC LIMIT 1');
while( $row = $db->fetch( $res ) )
{
$asst_points = $row['goal'] + $row['asst'];
?>
<div style="float: left; width: 65px;">
<a href="#">
<span>
	<img src="/style/images/medium_default_avatar.jpg" width="63px" height="93px">
</span>
</a>
</div>
<div style="float: left; width: 83px;">
<a href="#"><b><?php echo $row['player_name']; ?></b></a><br>
Unknown<br>
Goals: <b><?php echo $row['goal']; ?></b><br />Assists: <b><?php echo $row['asst']; ?></b><br />Points: <b><?php echo $goals_points; ?></b>
</div>
<?php
}
?>

<?php
$res = $db->query('SELECT * FROM league_nhl_statistics WHERE pos = "Goalie" AND team = "' . get_get('other') . '" ORDER BY ppg_ga / shot_sa DESC LIMIT 1');
while( $row = $db->fetch( $res ) )
{
$stats_procents = round( ( $row['ppg_ga'] / $row['shot_sa'] ) * 100, 2 );
$goalies_stats_procents = 100 - $stats_procents;
?>
<div style="float: left; width: 65px;">
<a href="#">
<span>
	<img src="/style/images/medium_default_avatar.jpg" width="63px" height="93px">
</span>
</a>
</div>
<div style="float: left; width: 83px;">
<a href="#"><b><?php echo $row['player_name']; ?></b></a><br>
Goalie<br>
Saves: <b><?php echo $row['shot_sa']; ?></b><br />S.Against: <b><?php echo $row['ppg_ga']; ?></b><br />
Saves%: <b><?php echo $goalies_stats_procents; ?>%</b>
</div>
<?php
}
?>
</fieldset>
</div>
</div>
<div style="float:left; width:950px; padding-top:5px;">
<fieldset class="tree-stars" style="width: 930px;margin-top:15px;"><legend class="tree-stars2"><a href="javascript:;">PĒDĒJĀS 5 KOMANDAS SPĒLES</a></legend>
<?
$regular_games_qs = $db->query( "SELECT * FROM league_nhl_games WHERE km1 = '" . get_get('other') . "' OR km2 = '" . get_get('other') . "' AND league_id = " . LEAGUE_ID . " ORDER BY date DESC LIMIT 5" );
$i = 1;
while( $gamess = $db->fetch( $regular_games_qs ) )
{
	
	$game_ot = $db->query( "SELECT * FROM league_nhl_gamestats WHERE id = '$gamess[id]' AND league_id = " . LEAGUE_ID ); 
	while( $ot_or_not = $db->fetch( $game_ot ) ) {
	if( $ot_or_not['period'] == 'Shootout' )
	{
		$ot = "FINAL SO";
	}
	if( $ot_or_not['period'] == '1OT' && $ot_or_not['period'] != '3' && $ot_or_not['period'] != 'Shootout' )
	{
		$ot = "FINAL OT";
	}
	if( $ot_or_not['period'] >= 1 && $ot_or_not['period'] != '1OT' && $ot_or_not['period'] != 'Shootout' )
	{
		$ot = "FINAL";
	}
	}
	
	if( $gamess['km1_rez'] > $gamess['km2_rez'] )
	{
		$winner_bg_e = 'Winner';
	}
	elseif( $gamess['km1_rez'] < $gamess['km2_rez'] )
	{
		$winner_bg_e = '';
	}
	
	if( $gamess['km2_rez'] > $gamess['km1_rez'] )
	{
		$winner_bg_w = 'Winner';
	}
	elseif( $gamess['km2_rez'] < $gamess['km1_rez'] )
	{
		$winner_bg_w = '';
	}
	
	//txt
	if( $gamess['km1_rez'] > $gamess['km2_rez'] )
	{
		$winner_txt = '' . $gamess['km1'] . ' win this game';
	}
	elseif( $gamess['km1_rez'] < $gamess['km2_rez'] )
	{
		$winner_txt = '' . $gamess['km2'] . ' win this game';
	}
?>
<a href="/match/<?php echo $gamess['id']; ?>/">
<div class="r1WestMatchup" style="margin:10px;margin-left:15px; border:1px solid gray;"> 
<div style="height: 5px;"></div>
<div class="gameScore"> 
<div class="teamSeed"><img src="/style/images/teams/small/colorful/<?php echo $gamess['km1']; ?>.png" border="0"><?php echo $gamess['km1']; ?></div>
<div class="gameStatus">
<div class="teamScore<?php echo $winner_bg_e; ?>"><?php echo $gamess['km1_rez'] ?></div>
<div class="teamScore<?php echo $winner_bg_w; ?>"><?php echo $gamess['km2_rez'] ?></div>
<?php echo $ot; ?>
</div>
<div class="teamSeed">
<img src="/style/images/teams/small/colorful/<?php echo $gamess['km2']; ?>.png" border="0"><?php echo $gamess['km2']; ?></div>
</div>
<div class="seriesMatchup">
<?php echo $winner_txt; ?>
</div> 
</div>
</a>
<?
$i++;
}
if($i == 1) 
echo '' .  get_get('other') . ' nav aizvadījusi nevienu spēli šajā turnīrā!';
?>
</fieldset>
<div style="float:left; width:690px;">
<fieldset class="tree-stars" style="width: 690px;margin-top:25px;"><legend class="tree-stars2"><a href="javascript:;">KOMANDAS STATISTIKA</a></legend>
<table class="west" cellspacing="0" cellpadding="1" style="width: 690px;">
<tr style="color:#E2E4E3; font-weight:bold;" bgcolor="#6F7070"><td width="156"></td><td width="27"><center>GV</center></td><td width="27"><center>IV</center></td><td width="27"><center>GIVS</center></td><td width="27"><center>VV</center></td><td width="27"><center>VM</center></td><td width="27"><center>MPV</center></td><td width="27"><center>SA%</center></td><td width="27"><center>RP</center></td><td width="27"><center>PTS</center></td><td width="27"><center>+/-</center></td><td width="27"><center>PIMS</center></td><td width="27"><center>HITS</center></td></tr>
<tr style="color:black; background-color:#FFFFFF;"><td style="padding-left:5px;" class="turnirs"><b>REGULAR SEASON</b></td><td class="turnirs"><center><?if(empty($sum_goals)){echo '0';}else{?><?=$sum_goals;?><?}?></center></td><td class="turnirs"><center><?if(empty($sum_ppgs)){echo '0';}else{?><?=$sum_ppgs;?><?}?></center></td><td class="turnirs"><center><?if(empty($givs_sum)){echo '0';}else{?><?=$givs_sum;?><?}?></center><td class="turnirs"><center>0</center></td><td class="turnirs"><center>0</center></td><td class="turnirs"><center><?if(empty($p_stats['shot_sa'])){echo '0';}else{?><?=$p_stats['shot_sa'];?><?}?></center></td><td class="turnirs"><center><?if(empty($p_stats['hits'])){echo '0';}else{?><?=$p_stats['hits'];?><?}?></center></td><td class="turnirs"><center><?if(empty($p_stats['ppg_ga'])){echo '0';}else{?><?=$p_stats['ppg_ga'];?><?}?></center></td><td class="turnirs"><center><?if(empty($p_stats['shg_eng'])){echo '0';}else{?><?=$p_stats['shg_eng'];?><?}?></center></td><td class="turnirs"><center><?if(empty($p_stats['pims'])){echo '0';}else{?><?=$p_stats['pims'];?><?}?></center></td><td class="turnirs"><center><?if(empty($p_stats['time'])){echo '0';}else{?><?=$p_stats['time'];?><?}?></center><td class="turnirs_beigas"><center><?if(empty($p_stats['time'])){echo '0';}else{?><?=$p_stats['time'];?><?}?></center></td></tr>
</table>
</fieldset>
</div>
<div style="float:left; width:200px; padding-left:38px;">
<fieldset class="tree-stars" style="width: 200px;margin-top:25px;"><legend class="tree-stars2"><a href="javascript:;">TURNĪRA STATISTIKA</a></legend>
<table class="ipbtable" cellspacing="1" style="width:200px;">
<tr bgcolor="white"><td class="row8" style="width:120px;">Vieta līgas tabulā</td><td class="row3">#<?=$team_rank['teamrank'];?></td></tr>
</table>
</fieldset>
</div>
</div>
<?
}