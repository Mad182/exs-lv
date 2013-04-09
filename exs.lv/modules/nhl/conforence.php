<div style="float:left; width:950px;">
	<div style="float:left; width:482px;">
		<img src="<?php echo BASE ?>/themes/<?php echo SITE_THEME ?>/images/western.gif" style="width: 464px;" />
			<table class="west" cellspacing="0" cellpadding="1" style="width: 464px;">
				<tr align="center" style="color:#E2E4E3; font-weight:bold;" bgcolor="#6F7070">
					<td width="15">#</td>
					<td width="246"></td>
					<td width="27">GP</td>
					<td width="27">W</td>
					<td width="27">OTL</td>
					<td width="27">L</td>
					<td width="32">P</td>
					<td width="27">GF</td>
					<td width="27">GA</td>
					<td width="31">DIFF</td>
					<td width="50">STREAK</td>
				</tr>
<!-- Western conference -->
<?php
$i = 1;
$num = 0;
$col = '';
$western_q = $db->query( "SELECT * FROM league_nhl_standings WHERE conference = 'western' AND league_id = '" . LEAGUE_ID . "' ORDER BY pts DESC, gf - ga DESC, gp ASC" );
while( $western = $db->fetch( $western_q ) )
{
	$games_play_want = $western['gp'] < 0 ? " style='background: rgb(255, 204, 204);'" : "";
	if( IS_USER )
	{
	$my_team = get_cookie('user_id') == $western['player_id'] ? " bgcolor=#C7E2C7" : "";
	}
	++$num;
	$col++;
	if ( $col % 2 == 0 )
	{
		$color = '#FFFFFF';
	}
	else
	{
		$color = '#E8E8E8';
	}
	$diffs = $western['gf']-$western['ga'];
	$simbols = $diffs > 0 ? "+":"";
	$diff = $simbols.(int)$diffs;
	if($diff > 0)
	{
		$diff_color = "darkgreen";
	}
	elseif($diff < 0)
	{
		$diff_color = "darkred";
	}
	$player = $users->info($western['player_id']);
	$team_color = $western['player_id'] == 0 ? "colorless":"colorful";
	$team_situation = $western['player_id'] == 0 ? "" . $western['team_full'] . " :: Komanda ir brīva!":"" . $western['team_full'] . " :: " . $player['username'] . "";
	if($i !== 0 && $i % 9 == 0)
	echo "
	<tr style='background-color:darkgray;'>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>";
?>
	<tr style="color:black; background-color:<?php echo $color; ?>;">
		<td <?php echo $my_team; ?> class="turnirs"><center><?php echo $num; ?></center></td>
		<td id="team_logo" <?php echo $my_team; ?> class="turnirs" title="<?php echo $team_situation; ?>">
			<a style="color:black;" href="/league/<?php echo $row['league_id']; ?>/team/<?php echo $western['team_small']; ?>/">
				<img alt="<?php echo $western['team_small']; ?>" src="<?php echo BASE ?>/style/images/teams/small/<?php echo $team_color; ?>/<?php echo $western['team_small']?>.png">
					<div class="teamname"><?php echo $western['team_full']; ?></div>
			</a>
		</td>
		<td class="turnirs" <?php echo $games_play_want; ?> <?php echo $my_team; ?>><center><?php echo $western['gp']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $western['w']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $western['ot']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $western['l']; ?></center></td>
		<td class="turnirs" style="background-color:#D8D8D8;"><center><b><?php echo $western['pts']; ?></b></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $western['gf']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $western['ga']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>>
			<center>
			<?php 
			if( $diff == '0' )
			{
				echo 'E';
			}
			else
			{
				echo "<span style='color:" . $diff_color . ";'>" . $diff . "</span>";	
			}
			?>
			</center>
		</td>
		<td class="turnirs_beigas" <?php echo $my_team; ?>>
			<center>
			<?php
			if( $western['streak_won'] == 0 AND $western['streak_lost'] == 0 )
			{
				echo '---';
			}
			else
			{
				if( $western['streak_won'] > 0 AND $western['streak_lost'] == 0 )
				{
					echo "WON " . $western['streak_won'] . "";
				}
				else
				{
					echo "LOST " . $western['streak_lost'] . "";
				}
				
			}
			?>
			</center>
		</td>
	</tr>
<?php
$i++;
}
?>
 </table>
</div>
<!-- Eastern conference -->
<div style="float:left; width:452px; padding-left:6px;">
	<img src="<?php echo BASE ?>/themes/<?php echo SITE_THEME ?>/images/eastern.gif" style="width: 464px;" />
		<table class="west" cellspacing="0" cellpadding="1" style="width: 464px;">
				<tr align="center" style="color:#E2E4E3; font-weight:bold;" bgcolor="#6F7070">
					<td width="15">#</td>
					<td width="246"></td>
					<td width="27">GP</td>
					<td width="27">W</td>
					<td width="27">OTL</td>
					<td width="27">L</td>
					<td width="32">P</td>
					<td width="27">GF</td>
					<td width="27">GA</td>
					<td width="31">DIFF</td>
					<td width="50">STREAK</td>
				</tr>
<?php
$i = 1;
$num = 0;
$col = '';
$eastern_q = $db->query( "SELECT * FROM league_nhl_standings WHERE conference = 'eastern' AND league_id = '" . LEAGUE_ID . "' ORDER BY pts DESC, gf - ga DESC, gp ASC" );
while( $eastern = $db->fetch( $eastern_q ) )
{
	$games_play_want = $eastern['gp'] < 0 ? " style='background: rgb(255, 204, 204);'" : "";
	if( IS_USER )
	{
	$my_team = get_cookie('user_id') == $eastern['player_id'] ? " bgcolor=#C7E2C7" : "";
	}
	++$num;
	$col++;
	if ( $col % 2 == 0 )
	{
		$color = '#FFFFFF';
	}
	else
	{
		$color = '#E8E8E8';
	}
	$diffs = $eastern['gf']-$eastern['ga'];
	$simbols = $diffs > 0 ? "+":"";
	$diff = $simbols.(int)$diffs;
	if($diff > 0)
	{
		$diff_color = "darkgreen";
	}
	elseif($diff < 0)
	{
		$diff_color = "darkred";
	}
	$player = $users->info($eastern['player_id']);
	$team_color = $eastern['player_id'] == 0 ? "colorless":"colorful";
	$team_situation = $eastern['player_id'] == 0 ? "" . $eastern['team_full'] . " :: Komanda ir brīva!":"" . $eastern['team_full'] . " :: " . $player['username'] . "";
	if($i !== 0 && $i % 9 == 0)
	echo "
	<tr style='background-color:darkgray;'>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>";
?>
	<tr style="color:black; background-color:<?php echo $color; ?>;">
		<td <?php echo $my_team; ?> class="turnirs"><center><?php echo $num; ?></center></td>
		<td id="team_logo" <?php echo $my_team; ?> class="turnirs" title="<?php echo $team_situation; ?>">
			<a style="color:black;" href="/league/<?php echo $row['league_id']; ?>/team/<?php echo $eastern['team_small']; ?>/">
				<img alt="<?php echo $eastern['team_small']; ?>" src="<?php echo BASE ?>/style/images/teams/small/<?php echo $team_color; ?>/<?php echo $eastern['team_small']?>.png">
					<div class="teamname"><?php echo $eastern['team_full']; ?></div>
			</a>
		</td>
		<td class="turnirs" <?php echo $games_play_want; ?> <?php echo $my_team; ?>><center><?php echo $eastern['gp']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $eastern['w']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $eastern['ot']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $eastern['l']; ?></center></td>
		<td class="turnirs" style="background-color:#D8D8D8;"><center><b><?php echo $eastern['pts']; ?></b></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $eastern['gf']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $eastern['ga']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>>
			<center>
			<?php 
			if( $diff == '0' )
			{
				echo 'E';
			}
			else
			{
				echo "<span style='color:" . $diff_color . ";'>" . $diff . "</span>";	
			}
			?>
			</center>
		</td>
		<td class="turnirs_beigas" <?php echo $my_team; ?>>
			<center>
			<?php
			if( $eastern['streak_won'] == 0 AND $eastern['streak_lost'] == 0 )
			{
				echo '---';
			}
			else
			{
				if( $eastern['streak_won'] > 0 AND $eastern['streak_lost'] == 0 )
				{
					echo "WON " . $eastern['streak_won'] . "";
				}
				else
				{
					echo "LOST " . $eastern['streak_lost'] . "";
				}
				
			}
			?>
			</center>
		</td>
	</tr>
<?php
$i++;
}
?>
  </table>
 </div>
</div>