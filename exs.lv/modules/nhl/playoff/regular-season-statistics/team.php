<div style="height: 4px;"></div>
<div id="nhl_statistics_bar">
<span class="team_stats_select" style="float:right;">
<select onchange="location = this.options[this.selectedIndex].value;" style="font-size: 11px;">
<option value="<?php echo BASE ?>/regular-season-statistics/"> - - - IZVĒLIES KOMANDU - - -</option>
<?php
$team_q = $db->query("SELECT * FROM league_nhl_standings WHERE league_id = " . LEAGUE_ID . " ORDER BY team_full ASC");
while($team = $db->fetch($team_q))
{
$selected = get_segment('team') == $team['team_small'] ? " selected=1":"";
?>
<option value="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/regular-season-statistics/team/<?php echo $team['team_small']; ?>/" <?php echo $selected; ?>><?php echo $team['team_full']; ?></option>
<?php
}
?>
</select>
</span>
</div>
<?php 
$team_e = $db->query('SELECT * FROM league_nhl_statistics WHERE team = "' . get_segment('team') . '" AND league_id = "' . LEAGUE_ID . '"');
$team_exist = $db->fetch( $team_e );
if( $team_exist['team'] )
{
?>
<table class="west" cellspacing="0" cellpadding="1" style="width: 950px;">
<tr align="center" style="color:#E2E4E3; font-weight:bold;" bgcolor="#6F7070">
	<td width="1"><div id="stats_color"><a  href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/regular-season-statistics/team/<?php echo get_segment('team'); ?>/sort/rank/<?php echo order_type('rank'); ?>">Rank</a></div></td>
	<td width="60"></td>
	<td width="30">Team</td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/regular-season-statistics/team/<?php echo get_segment('team'); ?>/sort/gp/<?php echo order_type('gp'); ?>/">GP</a></div></td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/regular-season-statistics/team/<?php echo get_segment('team'); ?>/sort/goals/<?php echo order_type('goals'); ?>/">GOALS</a></div></td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/regular-season-statistics/team/<?php echo get_segment('team'); ?>/sort/assists/<?php echo order_type('assists'); ?>/">ASSISTS</a></div></td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/regular-season-statistics/team/<?php echo get_segment('team'); ?>/sort/sa/<?php echo order_type('sa'); ?>/">SA%</a></div></td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/regular-season-statistics/team/<?php echo get_segment('team'); ?>/sort/points/<?php echo order_type('points'); ?>/">P</a></div></td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/regular-season-statistics/team/<?php echo get_segment('team'); ?>/sort/plusminus/<?php echo order_type('plusminus'); ?>/">+/-</a></div></td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/regular-season-statistics/team/<?php echo get_segment('team'); ?>/sort/shots/<?php echo order_type('shots'); ?>/">SHOTS</a></div></td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/regular-season-statistics/team/<?php echo get_segment('team'); ?>/sort/hits/<?php echo order_type('hits'); ?>/">HITS</a></div></td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/regular-season-statistics/team/<?php echo get_segment('team'); ?>/sort/ppg/<?php echo order_type('ppg'); ?>/">PPG</a></div></td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/regular-season-statistics/team/<?php echo get_segment('team'); ?>/sort/shg/<?php echo order_type('shg'); ?>/">SHG</a></div></td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/regular-season-statistics/team/<?php echo get_segment('team'); ?>/sort/pims/<?php echo order_type('pims'); ?>/">PIMS</a></div></td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/regular-season-statistics/team/<?php echo get_segment('team'); ?>/sort/toi/<?php echo order_type('toi'); ?>/">TOI</a></div></td>
</tr>
<?php
$count = $db->rows( $db->query("SELECT * FROM league_nhl_statistics WHERE team = '" . get_segment('team') . "' AND league_id = '" . LEAGUE_ID . "'"));
$pager_link = '';
list($pager, $limit) = pager(100, $count, $pager_link);
if(get_segment('sort') == 'gp')
{
	$res = $db->query('SELECT * FROM league_nhl_statistics WHERE team = "' . get_segment('team') . '" AND pos = "Player" AND league_id = "' . LEAGUE_ID . '" ORDER BY gp ' . order_type('gp') . ' ' . $limit);
}
elseif(get_segment('sort') == 'rank')
{
	$res = $db->query('SELECT * FROM league_nhl_statistics WHERE team = "' . get_segment('team') . '" AND pos = "Player" AND league_id = "' . LEAGUE_ID . '" ORDER BY rank ' . order_type('rank') . ' ' . $limit);
}
elseif(get_segment('sort') == 'goals')
{
	$res = $db->query('SELECT * FROM league_nhl_statistics WHERE team = "' . get_segment('team') . '" AND pos = "Player" AND league_id = "' . LEAGUE_ID . '" ORDER BY goal ' . order_type('goals') . ' ' . $limit);
}
elseif(get_segment('sort') == 'assists')
{
	$res = $db->query('SELECT * FROM league_nhl_statistics WHERE team = "' . get_segment('team') . '" AND pos = "Player" AND league_id = "' . LEAGUE_ID . '" ORDER BY asst ' . order_type('assists') . ' ' . $limit);
}
elseif(get_segment('sort') == 'sa')
{
	$res = $db->query('SELECT * FROM league_nhl_statistics WHERE team = "' . get_segment('team') . '" AND pos = "Player" AND league_id = "' . LEAGUE_ID . '" ORDER BY goal / shot_sa ' . order_type('sa') . ' ' . $limit);
}
elseif(get_segment('sort') == 'plusminus')
{
	$res = $db->query('SELECT * FROM league_nhl_statistics WHERE team = "' . get_segment('team') . '" AND pos = "Player" AND league_id = "' . LEAGUE_ID . '" ORDER BY plus_minus ' . order_type('plusminus') . ' ' . $limit);
}
elseif(get_segment('sort') == 'shots')
{
	$res = $db->query('SELECT * FROM league_nhl_statistics WHERE team = "' . get_segment('team') . '" AND pos = "Player" AND league_id = "' . LEAGUE_ID . '" ORDER BY shot_sa ' . order_type('shots') . ' ' . $limit);
}
elseif(get_segment('sort') == 'hits')
{
	$res = $db->query('SELECT * FROM league_nhl_statistics WHERE team = "' . get_segment('team') . '" AND pos = "Player" AND league_id = "' . LEAGUE_ID . '" ORDER BY hits ' . order_type('hits') . ' ' . $limit);
}
elseif(get_segment('sort') == 'ppg')
{
	$res = $db->query('SELECT * FROM league_nhl_statistics WHERE team = "' . get_segment('team') . '" AND pos = "Player" AND league_id = "' . LEAGUE_ID . '" ORDER BY ppg_ga ' . order_type('ppg') . ' ' . $limit);
}
elseif(get_segment('sort') == 'shg')
{
	$res = $db->query('SELECT * FROM league_nhl_statistics WHERE team = "' . get_segment('team') . '" AND pos = "Player" AND league_id = "' . LEAGUE_ID . '" ORDER BY shg_eng ' . order_type('shg') . ' ' . $limit);
}
elseif(get_segment('sort') == 'pims')
{
	$res = $db->query('SELECT * FROM league_nhl_statistics WHERE team = "' . get_segment('team') . '" AND pos = "Player" AND league_id = "' . LEAGUE_ID . '" ORDER BY pims ' . order_type('pims') . ' ' . $limit);
}
elseif(get_segment('sort') == 'toi')
{
	$res = $db->query('SELECT * FROM league_nhl_statistics WHERE team = "' . get_segment('team') . '" AND pos = "Player" AND league_id = "' . LEAGUE_ID . '" ORDER BY time / gp ' . order_type('toi') . ' ' . $limit);
}
elseif(get_segment('sort') == 'points')
{
	$res = $db->query('SELECT * FROM league_nhl_statistics WHERE team = "' . get_segment('team') . '" AND pos = "Player" AND league_id = "' . LEAGUE_ID . '" ORDER BY goal + asst ' . order_type('points') . ' ' . $limit);
}
else
{
	$res = $db->query('SELECT * FROM league_nhl_statistics WHERE team = "' . get_segment('team') . '" AND pos = "Player" AND league_id = "' . LEAGUE_ID . '" ORDER BY goal + asst DESC ' . $limit);
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
$simbols = $row['plus_minus'] > 0 ? "+":"";
$plus_minuss = $simbols.(int)$row['plus_minus'];
$player_stats_points = $row['goal'] + $row['asst'];
$sekundes = $row['time'] / $row['gp'];
$min = floor( $sekundes/60 );
$sec = $sekundes % 60;
$sa = round( ( $row['goal'] / $row['shot_sa'] ) * 100, 2 );
$izvelk_player = $db->query( "SELECT * FROM league_nhl_players WHERE player_name = '" . mysql_real_escape_string($row['player_name']) . "' AND league_id = '" . LEAGUE_ID . "'" );
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
	<td class="turnirs" <?=$ranks_color?> style="padding-top:3px;">
		<center><?=$row['rank'];?></center>
	</td>
	<td style="padding-left:5px;" class="turnirs">
		<b><a href="/player/<?=$player_name['display_name'];?>"><?=$row['player_name'];?></a></b>
	</td>
	<td style="padding-top:5px;padding-left:5px;padding-right:5px;" class="turnirs"><span style="">
		<img height="12px" src="/style/images/teams/small/colorful/<?=$row['team'];?>.png">
			<div class="stats_team"><?=$row['team'];?></div></span>
	</td>
	<td class="turnirs" <?=$gp_color?>>
		<center><?=$row['gp'];?></center>
	</td>
	<td class="turnirs" <?=$goal_color?>>
		<center><?=$row['goal'];?></center>
	</td>
	<td class="turnirs" <?=$asst_color?>>
		<center><?=$row['asst'];?></center>
	<td class="turnirs" <?=$sa_color?>>
		<center><?=$sa;?>%</center>
	<td class="turnirs" <?=$pts_color?>>
		<center><?=$player_stats_points;?></center></td>
	<td class="turnirs" <?=$plusminus_color?>>
		<center><?=$plus_minuss;?></center>
	</td>
	<td class="turnirs" <?=$shots_color?>>
		<center><?=$row['shot_sa'];?></center>
	</td>
	<td class="turnirs" <?=$hits_color?>>
		<center><?=$row['hits'];?></center>
	</td>
	<td class="turnirs" <?=$ppg_color?>>
		<center><?=$row['ppg_ga'];?></center>
	</td>
	<td class="turnirs" <?=$shg_color?>>
		<center><?=$row['shg_eng'];?></center>
	</td>
	<td class="turnirs" <?=$pims_color?>>
		<center><?=$row['pims'];?></center>
	</td>
	<td class="turnirs_beigas" <?=$toi_color?>>
		<center><?=$min . ':'.$zero.'' . $sec;?></center>
	</td>
</tr>
<?php
}
echo '</table>';
if(!$count)
echo error("Līgā ši komanda vēl nav izspēlējusi nevienu spēli, tāpēc nav statistikas ko ievākt!", "892px");
}
else
{
	echo error("Šāda komanda neeksistē!", "892px");
}