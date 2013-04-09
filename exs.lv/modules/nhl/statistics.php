<center>	
		<input type="button" onclick="document.location='<?php echo BASE; ?>/league/<?php echo $row['league_id']; ?>/statistics/';" class="login_button" value="Spēlētāju statistika"> - 
		<input type="button" onclick="document.location='<?php echo BASE; ?>/league/<?php echo $row['league_id']; ?>/statistics/goalies/view/';" class="login_button" value="Vārtsargu statistika"> - 
		<input type="button" onclick="document.location='<?php echo BASE; ?>/league/<?php echo $row['league_id']; ?>/statistics/top/';" class="login_button" value="Spēlētāju tops">
</center>
<?php
ini_set('display_errors', '0');
if( get_segment('goalies') )
{
	include ROOT . '/pages/league/NHL/statistics/goalies.php';
}
elseif( get_segment('team') )
{
	include ROOT . '/pages/league/NHL/statistics/team.php';
}
elseif( get_get('other') == 'top' )
{
	include ROOT . '/pages/league/NHL/statistics/top.php';
}
else
{
?>
<div style="height: 4px;"></div>
<div id="nhl_statistics_bar">
<span class="team_stats_select" style="float:right;">
<select onchange="location = this.options[this.selectedIndex].value;" style="font-size: 11px;">
<option value="<?php echo BASE ?>/statistics/"> - - - IZVĒLIES KOMANDU - - -</option>
<?php
$team_q = $db->query("SELECT * FROM league_nhl_standings WHERE league_id = " . LEAGUE_ID . " ORDER BY team_full ASC");
while($team = $db->fetch($team_q))
{
?>
<option value="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/statistics/team/<?php echo $team['team_small']; ?>/"><?php echo $team['team_full']; ?></option>
<?php
}
?>
</select>
</span>
</div>
<table class="west" cellspacing="0" cellpadding="1" style="width: 950px;">
<tr align="center" style="color:#E2E4E3; font-weight:bold;" bgcolor="#6F7070">
	<td width="1"><div id="stats_color"><a  href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/statistics/sort/rank/<?php echo order_type('rank'); ?>">Rank</a></div></td>
	<td width="60"></td>
	<td width="30">Team</td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/statistics/sort/gp/<?php echo order_type('gp'); ?>/">GP</a></div></td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/statistics/sort/goals/<?php echo order_type('goals'); ?>/">GOALS</a></div></td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/statistics/sort/assists/<?php echo order_type('assists'); ?>/">ASSISTS</a></div></td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/statistics/sort/sa/<?php echo order_type('sa'); ?>/">SA%</a></div></td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/statistics/sort/points/<?php echo order_type('points'); ?>/">P</a></div></td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/statistics/sort/plusminus/<?php echo order_type('plusminus'); ?>/">+/-</a></div></td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/statistics/sort/shots/<?php echo order_type('shots'); ?>/">SHOTS</a></div></td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/statistics/sort/hits/<?php echo order_type('hits'); ?>/">HITS</a></div></td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/statistics/sort/ppg/<?php echo order_type('ppg'); ?>/">PPG</a></div></td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/statistics/sort/shg/<?php echo order_type('shg'); ?>/">SHG</a></div></td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/statistics/sort/pims/<?php echo order_type('pims'); ?>/">PIMS</a></div></td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/statistics/sort/toi/<?php echo order_type('toi'); ?>/">TOI</a></div></td>
</tr>
<?php
$count = $db->count('league_nhl_statistics', 'player_name', 'league_id = ' . LEAGUE_ID);
$pager_link = get_segment('sort') ? '' . BASE . '/league/' . $row['league_id'] . '/statistics/sort/'  . get_segment('sort') . '/' . order_type(get_get('id')) . '/page/':'' . BASE . '/league/' . $row['league_id'] . '/statistics/page/';
list($pager, $limit) = pager(50, $count, $pager_link);
if(get_segment('sort') == 'gp')
{
	$res = $db->query('SELECT * FROM league_nhl_statistics WHERE pos = "Player" AND league_id = ' . LEAGUE_ID . ' ORDER BY gp ' . order_type('gp') . ' ' . $limit);
}
elseif(get_segment('sort') == 'rank')
{
	$res = $db->query('SELECT * FROM league_nhl_statistics WHERE pos = "Player" AND league_id = ' . LEAGUE_ID . ' ORDER BY rank ' . order_type('rank') . ' ' . $limit);
}
elseif(get_segment('sort') == 'goals')
{
	$res = $db->query('SELECT * FROM league_nhl_statistics WHERE pos = "Player" AND league_id = ' . LEAGUE_ID . ' ORDER BY goal ' . order_type('goals') . ' ' . $limit);
}
elseif(get_segment('sort') == 'assists')
{
	$res = $db->query('SELECT * FROM league_nhl_statistics WHERE pos = "Player" AND league_id = ' . LEAGUE_ID . ' ORDER BY asst ' . order_type('assists') . ' ' . $limit);
}
elseif(get_segment('sort') == 'sa')
{
	$res = $db->query('SELECT * FROM league_nhl_statistics WHERE pos = "Player" AND league_id = ' . LEAGUE_ID . ' ORDER BY goal / shot_sa ' . order_type('sa') . ' ' . $limit);
}
elseif(get_segment('sort') == 'plusminus')
{
	$res = $db->query('SELECT * FROM league_nhl_statistics WHERE pos = "Player" AND league_id = ' . LEAGUE_ID . ' ORDER BY plus_minus ' . order_type('plusminus') . ' ' . $limit);
}
elseif(get_segment('sort') == 'shots')
{
	$res = $db->query('SELECT * FROM league_nhl_statistics WHERE pos = "Player" AND league_id = ' . LEAGUE_ID . ' ORDER BY shot_sa ' . order_type('shots') . ' ' . $limit);
}
elseif(get_segment('sort') == 'hits')
{
	$res = $db->query('SELECT * FROM league_nhl_statistics WHERE pos = "Player" AND league_id = ' . LEAGUE_ID . ' ORDER BY hits ' . order_type('hits') . ' ' . $limit);
}
elseif(get_segment('sort') == 'ppg')
{
	$res = $db->query('SELECT * FROM league_nhl_statistics WHERE pos = "Player" AND league_id = ' . LEAGUE_ID . ' ORDER BY ppg_ga ' . order_type('ppg') . ' ' . $limit);
}
elseif(get_segment('sort') == 'shg')
{
	$res = $db->query('SELECT * FROM league_nhl_statistics WHERE pos = "Player" AND league_id = ' . LEAGUE_ID . ' ORDER BY shg_eng ' . order_type('shg') . ' ' . $limit);
}
elseif(get_segment('sort') == 'pims')
{
	$res = $db->query('SELECT * FROM league_nhl_statistics WHERE pos = "Player" AND league_id = ' . LEAGUE_ID . ' ORDER BY pims ' . order_type('pims') . ' ' . $limit);
}
elseif(get_segment('sort') == 'toi')
{
	$res = $db->query('SELECT * FROM league_nhl_statistics WHERE pos = "Player" AND league_id = ' . LEAGUE_ID . ' ORDER BY time / gp ' . order_type('toi') . ' ' . $limit);
}
elseif(get_segment('sort') == 'points')
{
	$res = $db->query('SELECT * FROM league_nhl_statistics WHERE pos = "Player" AND league_id = ' . LEAGUE_ID . ' ORDER BY goal + asst ' . order_type('points') . ' ' . $limit);
}
else
{
	$res = $db->query('SELECT * FROM league_nhl_statistics WHERE pos = "Player" AND league_id = ' . LEAGUE_ID . ' ORDER BY goal + asst DESC ' . $limit);
}
$i = 0;
$num = 0;
while( $row=$db->fetch( $res ) )
{
if(get_segment('sort') == 'gp')
{
	$gp_color = 'style="background-color:#D8D8D8;font-weight:bold;"';
}
elseif(get_segment('sort') == 'rank')
{
	$ranks_color = 'style="background-color:#D8D8D8;font-weight:bold;"';
}
elseif(get_segment('sort') == 'goals')
{
	$goal_color = 'style="background-color:#D8D8D8;font-weight:bold;"';
}
elseif(get_segment('sort') == 'assists')
{
	$asst_color = 'style="background-color:#D8D8D8;font-weight:bold;"';
}
elseif(get_segment('sort') == 'sa')
{
	$sa_color = 'style="background-color:#D8D8D8;font-weight:bold;"';
}
elseif(get_segment('sort') == 'plusminus')
{
	$plusminus_color = 'style="background-color:#D8D8D8;font-weight:bold;"';
}
elseif(get_segment('sort') == 'shots')
{
	$shots_color = 'style="background-color:#D8D8D8;font-weight:bold;"';
}
elseif(get_segment('sort') == 'hits')
{
	$hits_color = 'style="background-color:#D8D8D8;font-weight:bold;"';
}
elseif(get_segment('sort') == 'ppg')
{
	$ppg_color = 'style="background-color:#D8D8D8;font-weight:bold;"';
}
elseif(get_segment('sort') == 'shg')
{
	$shg_color = 'style="background-color:#D8D8D8;font-weight:bold;"';
}
elseif(get_segment('sort') == 'pims')
{
	$pims_color = 'style="background-color:#D8D8D8;font-weight:bold;"';
}
elseif(get_segment('sort') == 'toi')
{
	$toi_color = 'style="background-color:#D8D8D8;font-weight:bold;"';
}
else
{
	$pts_color = 'style="background-color:#D8D8D8;font-weight:bold;"';
}
$col++;
if ( $col % 2 == 0 )
{
	$color = '#FFFFFF';
}
else
{
	$color = '#E8E8E8';
}
++$num;
if( IS_USER )
{
	$izvelk_team = $db->query( "SELECT * FROM league_nhl_standings WHERE player_id = '" . get_cookie('user_id') . "' AND league_id = '" . LEAGUE_ID . "'" );
	$team_name = $db->fetch( $izvelk_team );    
	$my_team = $team_name['team_small'] == $row['team'] ? " bgcolor=#C7E2C7" : "";
}
$simbols = $row['plus_minus'] > 0 ? "+":"";
$plus_minuss = $simbols.(int)$row['plus_minus'];
$player_stats_points = $row['goal'] + $row['asst'];
$sekundes = $row['time'] / $row['gp'];
$min = floor( $sekundes/60 );
$sec = $sekundes % 60;
$sa = round( ( $row['goal'] / $row['shot_sa'] ) * 100, 2 );
$izvelk_player = $db->query( "SELECT * FROM league_nhl_players WHERE player_name = '" . mysql_real_escape_string($row['player_name']) . "'" );
$player_name = $db->fetch( $izvelk_player );    
if( strlen($sec) == 1 )
{
	$zero = 0;
}
else
{
	$zero = '';
}
?>
<tr style="color:black; background-color:<?=$color;?>;">
	<td class="turnirs" <?=$ranks_color?> style="padding-top:3px;" <?php echo $my_team; ?>>
		<center><?=$row['rank'];?></center>
	</td>
	<td style="padding-left:5px;" class="turnirs" <?php echo $my_team; ?>>
		<b><a href="/player/<?=$player_name['display_name'];?>"><?=$row['player_name'];?></a></b>
	</td>
	<td style="padding-top:5px;padding-left:5px;padding-right:5px;" class="turnirs" <?php echo $my_team; ?>><span style="">
		<img height="12px" src="/style/images/teams/small/colorful/<?=$row['team'];?>.png">
			<div class="stats_team"><?=$row['team'];?></div></span>
	</td>
	<td class="turnirs" <?=$gp_color?> <?php echo $my_team; ?>>
		<center><?=$row['gp'];?></center>
	</td>
	<td class="turnirs" <?=$goal_color?> <?php echo $my_team; ?>>
		<center><?=$row['goal'];?></center>
	</td>
	<td class="turnirs" <?=$asst_color?> <?php echo $my_team; ?>>
		<center><?=$row['asst'];?></center>
	<td class="turnirs" <?=$sa_color?> <?php echo $my_team; ?>>
		<center><?=$sa;?>%</center>
	<td class="turnirs" <?=$pts_color?> <?php echo $my_team; ?>>
		<center><?=$player_stats_points;?></center></td>
	<td class="turnirs" <?=$plusminus_color?> <?php echo $my_team; ?>>
		<center><?=$plus_minuss;?></center>
	</td>
	<td class="turnirs" <?=$shots_color?> <?php echo $my_team; ?>>
		<center><?=$row['shot_sa'];?></center>
	</td>
	<td class="turnirs" <?=$hits_color?> <?php echo $my_team; ?>>
		<center><?=$row['hits'];?></center>
	</td>
	<td class="turnirs" <?=$ppg_color?> <?php echo $my_team; ?>>
		<center><?=$row['ppg_ga'];?></center>
	</td>
	<td class="turnirs" <?=$shg_color?> <?php echo $my_team; ?>>
		<center><?=$row['shg_eng'];?></center>
	</td>
	<td class="turnirs" <?=$pims_color?> <?php echo $my_team; ?>>
		<center><?=$row['pims'];?></center>
	</td>
	<td class="turnirs_beigas" <?=$toi_color?> <?php echo $my_team; ?>>
		<center><?=$min . ':'.$zero.'' . $sec;?></center>
	</td>
</tr>
<?php
}
echo '</table>';
if(!$count)
echo error("Turnīrā vēl nav izspēlēta neviena spēle, tāpēc pašlaik nav statistikas ko ievākt!", "892px");
if($count)
echo $pager;
}
?>