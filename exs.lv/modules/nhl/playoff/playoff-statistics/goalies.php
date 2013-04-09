<div style="height: 4px;"></div>
<div id="nhl_statistics_bar"></div>
<table class="west" cellspacing="0" cellpadding="1" style="width: 950px;">
<tr align="center" style="color:#E2E4E3; font-weight:bold;" bgcolor="#6F7070">
	<td width="20"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/statistics/goalies/<?php echo get_segment('goalies'); ?>/sort/rank/<?php echo order_type('rank'); ?>/">Rank</a></div></td>
	<td width="60"></td>
	<td width="20">Team</td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/statistics/goalies/<?php echo get_segment('goalies'); ?>/sort/gp/<?php echo order_type('gp'); ?>/">GP</a></div></td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/statistics/goalies/<?php echo get_segment('goalies'); ?>/sort/sa/<?php echo order_type('sa'); ?>/">SA</a></div></td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/statistics/goalies/<?php echo get_segment('goalies'); ?>/sort/ga/<?php echo order_type('ga'); ?>/">GA</a></div></td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/statistics/goalies/<?php echo get_segment('goalies'); ?>/sort/gaa/<?php echo order_type('gaa'); ?>/">GAA</a></div></td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/statistics/goalies/<?php echo get_segment('goalies'); ?>/sort/saves/<?php echo order_type('saves'); ?>/">GAVES</a></div></td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/statistics/goalies/<?php echo get_segment('goalies'); ?>/sort/saves-procent/<?php echo order_type('saves-procent'); ?>/">SAVES%</a></div></td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/statistics/goalies/<?php echo get_segment('goalies'); ?>/sort/assists/<?php echo order_type('assists'); ?>/">ASSISTS</a></div></td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/statistics/goalies/<?php echo get_segment('goalies'); ?>/sort/pims/<?php echo order_type('pims'); ?>/">PIMS</a></div></td>
	<td width="30"><div id="stats_color"><a href="<?php echo BASE ?>/league/<?php echo $row['league_id']; ?>/statistics/goalies/<?php echo get_segment('goalies'); ?>/sort/toi/<?php echo order_type('toi'); ?>/">TOI</a></div></td>
</tr>
<?php
$count = $db->rows( $db->query("SELECT * FROM league_nhl_po_statistics WHERE pos = 'Goalie' AND league_id = '" . LEAGUE_ID . "'"));
$pager_link = get_segment('sort') ? '' . BASE . '/league/' . $row['league_id'] . '/statistics/goalies/sort/'  . get_segment('sort') . '/' . order_type(get_get('id')) . '/page/':'' . BASE . '/league/' . $row['league_id'] . '/statistics/goalies/page/';
list($pager, $limit) = pager(100, $count, $pager_link);
if(get_segment('sort') == 'gp')
{
	$res = $db->query('SELECT * FROM league_nhl_po_statistics WHERE pos = "Goalie" AND gp >= 1 AND league_id = "' . LEAGUE_ID . '" ORDER BY gp ' . order_type('gp') . ' ' . $limit);
}
elseif(get_segment('sort') == 'rank')
{
	$res = $db->query('SELECT * FROM league_nhl_po_statistics WHERE pos = "Goalie" AND gp >= 1 AND league_id = "' . LEAGUE_ID . '" ORDER BY rank ' . order_type('rank') . ' ' . $limit);
}
elseif(get_segment('sort') == 'sa')
{
	$res = $db->query('SELECT * FROM league_nhl_po_statistics WHERE pos = "Goalie" AND gp >= 1 AND league_id = "' . LEAGUE_ID . '" ORDER BY shot_sa ' . order_type('sa') . ' ' . $limit);
}
elseif(get_segment('sort') == 'ga')
{
	$res = $db->query('SELECT * FROM league_nhl_po_statistics WHERE pos = "Goalie" AND gp >= 1 AND league_id = "' . LEAGUE_ID . '" ORDER BY ppg_ga ' . order_type('ga') . ' ' . $limit);
}
elseif(get_segment('sort') == 'gaa')
{
	$res = $db->query('SELECT * FROM league_nhl_po_statistics WHERE pos = "Goalie" AND gp >= 1 AND league_id = "' . LEAGUE_ID . '" ORDER BY ppg_ga / gp ' . order_type('gaa') . ' ' . $limit);
}
elseif(get_segment('sort') == 'saves')
{
	$res = $db->query('SELECT * FROM league_nhl_po_statistics WHERE pos = "Goalie" AND gp >= 1 AND league_id = "' . LEAGUE_ID . '" ORDER BY shot_sa - ppg_ga ' . order_type('saves') . ' ' . $limit);
}
elseif(get_segment('sort') == 'saves-procent')
{
	$res = $db->query('SELECT * FROM league_nhl_po_statistics WHERE pos = "Goalie" AND gp >= 1 AND league_id = "' . LEAGUE_ID . '" ORDER BY ppg_ga / shot_sa ' . order_type('saves-procent') . ' ' . $limit);
}
elseif(get_segment('sort') == 'assists')
{
	$res = $db->query('SELECT * FROM league_nhl_po_statistics WHERE pos = "Goalie" AND gp >= 1 AND league_id = "' . LEAGUE_ID . '" ORDER BY asst ' . order_type('assists') . ' ' . $limit);
}
elseif(get_segment('sort') == 'pims')
{
	$res = $db->query('SELECT * FROM league_nhl_po_statistics WHERE pos = "Goalie" AND gp >= 1 AND league_id = "' . LEAGUE_ID . '" ORDER BY pims ' . order_type('pims') . ' ' . $limit);
}
elseif(get_segment('sort') == 'toi')
{
	$res = $db->query('SELECT * FROM league_nhl_po_statistics WHERE pos = "Goalie" AND gp >= 1 AND league_id = "' . LEAGUE_ID . '" ORDER BY time / gp ' . order_type('toi') . ' ' . $limit);
}
else
{
	$res = $db->query('SELECT * FROM league_nhl_po_statistics WHERE pos = "Goalie" AND gp >= 1 AND league_id = "' . LEAGUE_ID . '" ORDER BY ppg_ga / gp ASC ' . $limit);
}
$i = 0;
$num = 0;
while( $goalies_statistic = $db->fetch( $res ) )
{
if(get_segment('sort') == 'gp')
{
	$gp_color = 'style="background-color:#D8D8D8;font-weight:bold;"';
}
elseif(get_segment('sort') == 'rank')
{
	$ranks_color = 'style="background-color:#D8D8D8;font-weight:bold;"';
}
elseif(get_segment('sort') == 'sa')
{
	$sa_color = 'style="background-color:#D8D8D8;font-weight:bold;"';
}
elseif(get_segment('sort') == 'ga')
{
	$ga_color = 'style="background-color:#D8D8D8;font-weight:bold;"';
}
elseif(get_segment('sort') == 'gaa')
{
	$gaa_color = 'style="background-color:#D8D8D8;font-weight:bold;"';
}
elseif(get_segment('sort') == 'saves')
{
	$saves_color = 'style="background-color:#D8D8D8;font-weight:bold;"';
}
elseif(get_segment('sort') == 'saves-procent')
{
	$saves_procent_color = 'style="background-color:#D8D8D8;font-weight:bold;"';
}
elseif(get_segment('sort') == 'assists')
{
	$asst_color = 'style="background-color:#D8D8D8;font-weight:bold;"';
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
	$gaa_color = 'style="background-color:#D8D8D8;font-weight:bold;"';
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
	$izvelk_teamx = $db->query( "SELECT * FROM league_nhl_standings WHERE player_id = '" . get_cookie('user_id') . "' AND league_id = '" . LEAGUE_ID . "'" );
	$team_namxe = $db->fetch( $izvelk_teamx );    
	$my_goalie_team = $team_namxe['team_small'] == $goalies_statistic['team'] ? " bgcolor=#C7E2C7" : "";
}
$goalies_stats_average = number_format($goalies_statistic['ppg_ga'] / $goalies_statistic['gp'], 2);
$stats_procents = round(($goalies_statistic['ppg_ga'] / $goalies_statistic['shot_sa']) * 100, 2);
$goalies_stats_procents = 100 - $stats_procents;
$goalies_saves = $goalies_statistic['shot_sa'] - $goalies_statistic['ppg_ga'];
$sekundes = $goalies_statistic['time']/$goalies_statistic['gp'];
$min = floor($sekundes/60);
$sec = $sekundes % 60;
$izvelk_player = mysql_query("SELECT * FROM league_nhl_players WHERE player_name = '".$goalies_statistic['player_name']."' AND league_id = '" . LEAGUE_ID . "'" );
$player_name = mysql_fetch_array($izvelk_player);
if(strlen($sec) == 1){$zero = 0;}else{$zero = '' ;}

?>
<tr style="color:black; background-color:<?=$color;?>;">
	<td class="turnirs" <?php echo $ranks_color; ?>style="padding-top:0px;" <?php echo $my_goalie_team; ?>><center><?=$num;?></center></td>
	<td style="padding-left:5px;" class="turnirs" <?php echo $my_goalie_team; ?>><b><a href="/player/<?=$player_name['display_name'];?>"><?=$goalies_statistic['player_name'];?></a></b></td>
	<td style="padding-top:5px;" class="turnirs" <?php echo $my_goalie_team; ?>><center><img height="12px" src="/style/images/teams/small/colorful/<?=$goalies_statistic['team'];?>.png"><div class="stats_team"><?=$goalies_statistic['team'];?></div></center></td>
	<td class="turnirs" <?=$gp_color?> <?php echo $my_goalie_team; ?>><center><?=$goalies_statistic['gp'];?></center></td>
	<td class="turnirs" <?=$sa_color?> <?php echo $my_goalie_team; ?>><center><?=$goalies_statistic['shot_sa'];?></center></td>
	<td class="turnirs" <?=$ga_color?> <?php echo $my_goalie_team; ?>><center><?=$goalies_statistic['ppg_ga'];?></center></td>
	<td class="turnirs" <?=$gaa_color?> <?php echo $my_goalie_team; ?>><center><b><?=$goalies_stats_average;?></b></center></td>
	<td class="turnirs" <?=$saves_color?> <?php echo $my_goalie_team; ?>><center><?=$goalies_saves;?></center></td>
	<td class="turnirs" <?=$saves_procent_color?> <?php echo $my_goalie_team; ?>><center><?=$goalies_stats_procents;?>%</center></td>
	<td class="turnirs" <?=$asst_color?> <?php echo $my_goalie_team; ?>><center><?=$goalies_statistic['asst'];?></center></td>
	<td class="turnirs" <?=$pims_color?> <?php echo $my_goalie_team; ?>><center><?=$goalies_statistic['pims'];?></center></td>
	<td class="turnirs_beigas" <?=$toi_color?> <?php echo $my_goalie_team; ?>><center><?=$min . ':'.$zero.'' . $sec;?></center></td>
</tr>
<?php
}
echo '</table>';
if(!$count)
echo error("Līgā vēl nav izspēlēta neviena playoff spēle, tāpēc pašlaik nav vārtsargu statistikas ko ievākt!", "892px");