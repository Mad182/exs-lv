<div style="float:left; width:950px;">
	<div style="float:left; width:480px;">
			<img src="<?php echo BASE ?>/themes/<?php echo SITE_THEME ?>/images/western.gif" style="width: 464px;" />
			<table class="west" cellspacing="0" cellpadding="1" style="width: 464px;">
				<tr align="center" style="color:#E2E4E3; font-weight:bold;" bgcolor="#6F7070">
					<td width="15">#</td>
					<td width="246">CENTRAL</td>
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
$division_q = $db->query( "SELECT * FROM league_nhl_standings WHERE division = 'central' AND league_id = '" . LEAGUE_ID . "' ORDER BY pts DESC, gf - ga DESC, gp ASC" );
while( $division = $db->fetch( $division_q ) )
{
	$games_play_want = $division['gp'] < 0 ? " style='background: rgb(255, 204, 204);'" : "";
	if( IS_USER )
	$my_team = get_cookie('user_id') == $division['player_id'] ? " bgcolor=#C7E2C7" : "";
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
	$diffs = $division['gf']-$division['ga'];
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
	$player = $users->info($division['player_id']);
	$team_color = $division['player_id'] == 0 ? "colorless":"colorful";
	$team_situation = $division['player_id'] == 30 ? "" . $division['team_full'] . " :: Komanda ir brīva!":"" . $division['team_full'] . " :: " . $player['username'] . "";
?>
	<tr style="color:black; background-color:<?php echo $color; ?>;">
		<td <?php echo $my_team; ?> class="turnirs"><center><?php echo $num; ?></center></td>
		<td id="team_logo" <?php echo $my_team; ?> class="turnirs" title="<?php echo $team_situation; ?>">
			<a style="color:black;" href="/league/<?php echo $row['league_id']; ?>/team/<?php echo $division['team_small']; ?>/">
				<img alt="<?php echo $division['team_small']; ?>" src="<?php echo BASE ?>/style/images/teams/small/<?php echo $team_color; ?>/<?php echo $division['team_small']?>.png">
					<div class="teamname"><?php echo $division['team_full']; ?></div>
			</a>
		</td>
		<td class="turnirs" <?php echo $games_play_want; ?> <?php echo $my_team; ?>><center><?php echo $division['gp']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $division['w']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $division['ot']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $division['l']; ?></center></td>
		<td class="turnirs" style="background-color:#D8D8D8;"><center><b><?php echo $division['pts']; ?></b></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $division['gf']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $division['ga']; ?></center></td>
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
			if( $division['streak_won'] == 0 AND $division['streak_lost'] == 0 )
			{
				echo '---';
			}
			else
			{
				if( $division['streak_won'] > 0 AND $division['streak_lost'] == 0 )
				{
					echo "WON " . $division['streak_won'] . "";
				}
				else
				{
					echo "LOST " . $division['streak_lost'] . "";
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
 <br />
 			<table class="west" cellspacing="0" cellpadding="1" style="width: 464px;">
				<tr align="center" style="color:#E2E4E3; font-weight:bold;" bgcolor="#6F7070">
					<td width="15">#</td>
					<td width="246">NORTHWEST</td>
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
$division_q = $db->query( "SELECT * FROM league_nhl_standings WHERE division = 'northwest' AND league_id = '" . LEAGUE_ID . "' ORDER BY pts DESC, gf - ga DESC, gp ASC" );
while( $division = $db->fetch( $division_q ) )
{
	$games_play_want = $division['gp'] < 0 ? " style='background: rgb(255, 204, 204);'" : "";
	if( IS_USER )
	$my_team = get_cookie('user_id') == $division['player_id'] ? " bgcolor=#C7E2C7" : "";
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
	$diffs = $division['gf']-$division['ga'];
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
	$player = $users->info($division['player_id']);
	$team_color = $division['player_id'] == 0 ? "colorless":"colorful";
	$team_situation = $division['player_id'] == 0 ? "" . $division['team_full'] . " :: Komanda ir brīva!":"" . $division['team_full'] . " :: " . $player['username'] . "";
?>
	<tr style="color:black; background-color:<?php echo $color; ?>;">
		<td <?php echo $my_team; ?> class="turnirs"><center><?php echo $num; ?></center></td>
		<td id="team_logo" <?php echo $my_team; ?> class="turnirs" title="<?php echo $team_situation; ?>">
			<a style="color:black;" href="/league/<?php echo $row['league_id']; ?>/team/<?php echo $division['team_small']; ?>/">
				<img alt="<?php echo $division['team_small']; ?>" src="<?php echo BASE ?>/style/images/teams/small/<?php echo $team_color; ?>/<?php echo $division['team_small']?>.png">
					<div class="teamname"><?php echo $division['team_full']; ?></div>
			</a>
		</td>
		<td class="turnirs" <?php echo $games_play_want; ?> <?php echo $my_team; ?>><center><?php echo $division['gp']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $division['w']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $division['ot']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $division['l']; ?></center></td>
		<td class="turnirs" style="background-color:#D8D8D8;"><center><b><?php echo $division['pts']; ?></b></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $division['gf']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $division['ga']; ?></center></td>
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
			if( $division['streak_won'] == 0 AND $division['streak_lost'] == 0 )
			{
				echo '---';
			}
			else
			{
				if( $division['streak_won'] > 0 AND $division['streak_lost'] == 0 )
				{
					echo "WON " . $division['streak_won'] . "";
				}
				else
				{
					echo "LOST " . $division['streak_lost'] . "";
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
   <br />
 			<table class="west" cellspacing="0" cellpadding="1" style="width: 464px;">
				<tr align="center" style="color:#E2E4E3; font-weight:bold;" bgcolor="#6F7070">
					<td width="15">#</td>
					<td width="246">PACIFIC</td>
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
$division_q = $db->query( "SELECT * FROM league_nhl_standings WHERE division = 'pafific' AND league_id = '" . LEAGUE_ID . "' ORDER BY pts DESC, gf - ga DESC, gp ASC" );
while( $division = $db->fetch( $division_q ) )
{
	$games_play_want = $division['gp'] < 0 ? " style='background: rgb(255, 204, 204);'" : "";
	if( IS_USER )
	$my_team = get_cookie('user_id') == $division['player_id'] ? " bgcolor=#C7E2C7" : "";
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
	$diffs = $division['gf']-$division['ga'];
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
	$player = $users->info($division['player_id']);
	$team_color = $division['player_id'] == 0 ? "colorless":"colorful";
	$team_situation = $division['player_id'] == 0 ? "" . $division['team_full'] . " :: Komanda ir brīva!":"" . $division['team_full'] . " :: " . $player['username'] . "";
?>
	<tr style="color:black; background-color:<?php echo $color; ?>;">
		<td <?php echo $my_team; ?> class="turnirs"><center><?php echo $num; ?></center></td>
		<td id="team_logo" <?php echo $my_team; ?> class="turnirs" title="<?php echo $team_situation; ?>">
			<a style="color:black;" href="/league/<?php echo $row['league_id']; ?>/team/<?php echo $division['team_small']; ?>/">
				<img alt="<?php echo $division['team_small']; ?>" src="<?php echo BASE ?>/style/images/teams/small/<?php echo $team_color; ?>/<?php echo $division['team_small']?>.png">
					<div class="teamname"><?php echo $division['team_full']; ?></div>
			</a>
		</td>
		<td class="turnirs" <?php echo $games_play_want; ?> <?php echo $my_team; ?>><center><?php echo $division['gp']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $division['w']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $division['ot']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $division['l']; ?></center></td>
		<td class="turnirs" style="background-color:#D8D8D8;"><center><b><?php echo $division['pts']; ?></b></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $division['gf']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $division['ga']; ?></center></td>
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
			if( $division['streak_won'] == 0 AND $division['streak_lost'] == 0 )
			{
				echo '---';
			}
			else
			{
				if( $division['streak_won'] > 0 AND $division['streak_lost'] == 0 )
				{
					echo "WON " . $division['streak_won'] . "";
				}
				else
				{
					echo "LOST " . $division['streak_lost'] . "";
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
<div style="float:left; width:272px; padding-left:6px;">
	<img src="<?php echo BASE ?>/themes/<?php echo SITE_THEME ?>/images/eastern.gif" style="width: 464px;" />
			<table class="west" cellspacing="0" cellpadding="1" style="width: 464px;">
				<tr align="center" style="color:#E2E4E3; font-weight:bold;" bgcolor="#6F7070">
					<td width="15">#</td>
					<td width="246">ATLANTIC</td>
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
$division_q = $db->query( "SELECT * FROM league_nhl_standings WHERE division = 'atlantic' AND league_id = '" . LEAGUE_ID . "' ORDER BY pts DESC, gf - ga DESC, gp ASC" );
while( $division = $db->fetch( $division_q ) )
{
	$games_play_want = $division['gp'] < 0 ? " style='background: rgb(255, 204, 204);'" : "";
	if( IS_USER )
	$my_team = get_cookie('user_id') == $division['player_id'] ? " bgcolor=#C7E2C7" : "";
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
	$diffs = $division['gf']-$division['ga'];
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
	$player = $users->info($division['player_id']);
	$team_color = $division['player_id'] == 0 ? "colorless":"colorful";
	$team_situation = $division['player_id'] == 0 ? "" . $division['team_full'] . " :: Komanda ir brīva!":"" . $division['team_full'] . " :: " . $player['username'] . "";
?>
	<tr style="color:black; background-color:<?php echo $color; ?>;">
		<td <?php echo $my_team; ?> class="turnirs"><center><?php echo $num; ?></center></td>
		<td id="team_logo" <?php echo $my_team; ?> class="turnirs" title="<?php echo $team_situation; ?>">
			<a style="color:black;" href="/league/<?php echo $row['league_id']; ?>/team/<?php echo $division['team_small']; ?>/">
				<img alt="<?php echo $division['team_small']; ?>" src="<?php echo BASE ?>/style/images/teams/small/<?php echo $team_color; ?>/<?php echo $division['team_small']?>.png">
					<div class="teamname"><?php echo $division['team_full']; ?></div>
			</a>
		</td>
		<td class="turnirs" <?php echo $games_play_want; ?> <?php echo $my_team; ?>><center><?php echo $division['gp']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $division['w']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $division['ot']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $division['l']; ?></center></td>
		<td class="turnirs" style="background-color:#D8D8D8;"><center><b><?php echo $division['pts']; ?></b></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $division['gf']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $division['ga']; ?></center></td>
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
			if( $division['streak_won'] == 0 AND $division['streak_lost'] == 0 )
			{
				echo '---';
			}
			else
			{
				if( $division['streak_won'] > 0 AND $division['streak_lost'] == 0 )
				{
					echo "WON " . $division['streak_won'] . "";
				}
				else
				{
					echo "LOST " . $division['streak_lost'] . "";
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
    <br />
 		<table class="west" cellspacing="0" cellpadding="1" style="width: 464px;">
				<tr align="center" style="color:#E2E4E3; font-weight:bold;" bgcolor="#6F7070">
					<td width="15">#</td>
					<td width="246">NORTHEAST</td>
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
$division_q = $db->query( "SELECT * FROM league_nhl_standings WHERE division = 'northeast' AND league_id = '" . LEAGUE_ID . "' ORDER BY pts DESC, gf - ga DESC, gp ASC" );
while( $division = $db->fetch( $division_q ) )
{
	$games_play_want = $division['gp'] < 0 ? " style='background: rgb(255, 204, 204);'" : "";
	if( IS_USER )
	$my_team = get_cookie('user_id') == $division['player_id'] ? " bgcolor=#C7E2C7" : "";
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
	$diffs = $division['gf']-$division['ga'];
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
	$player = $users->info($division['player_id']);
	$team_color = $division['player_id'] == 0 ? "colorless":"colorful";
	$team_situation = $division['player_id'] == 0 ? "" . $division['team_full'] . " :: Komanda ir brīva!":"" . $division['team_full'] . " :: " . $player['username'] . "";
?>
	<tr style="color:black; background-color:<?php echo $color; ?>;">
		<td <?php echo $my_team; ?> class="turnirs"><center><?php echo $num; ?></center></td>
		<td id="team_logo" <?php echo $my_team; ?> class="turnirs" title="<?php echo $team_situation; ?>">
			<a style="color:black;" href="/league/<?php echo $row['league_id']; ?>/team/<?php echo $division['team_small']; ?>/">
				<img alt="<?php echo $division['team_small']; ?>" src="<?php echo BASE ?>/style/images/teams/small/<?php echo $team_color; ?>/<?php echo $division['team_small']?>.png">
					<div class="teamname"><?php echo $division['team_full']; ?></div>
			</a>
		</td>
		<td class="turnirs" <?php echo $games_play_want; ?> <?php echo $my_team; ?>><center><?php echo $division['gp']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $division['w']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $division['ot']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $division['l']; ?></center></td>
		<td class="turnirs" style="background-color:#D8D8D8;"><center><b><?php echo $division['pts']; ?></b></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $division['gf']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $division['ga']; ?></center></td>
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
			if( $division['streak_won'] == 0 AND $division['streak_lost'] == 0 )
			{
				echo '---';
			}
			else
			{
				if( $division['streak_won'] > 0 AND $division['streak_lost'] == 0 )
				{
					echo "WON " . $division['streak_won'] . "";
				}
				else
				{
					echo "LOST " . $division['streak_lost'] . "";
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
     <br />
 		<table class="west" cellspacing="0" cellpadding="1" style="width: 464px;">
				<tr align="center" style="color:#E2E4E3; font-weight:bold;" bgcolor="#6F7070">
					<td width="15">#</td>
					<td width="246">SOUTHEAST</td>
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
$division_q = $db->query( "SELECT * FROM league_nhl_standings WHERE division = 'southeast' AND league_id = '" . LEAGUE_ID . "' ORDER BY pts DESC, gf - ga DESC, gp ASC" );
while( $division = $db->fetch( $division_q ) )
{
	$games_play_want = $division['gp'] < 0 ? " style='background: rgb(255, 204, 204);'" : "";
	if( IS_USER )
	$my_team = get_cookie('user_id') == $division['player_id'] ? " bgcolor=#C7E2C7" : "";
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
	$diffs = $division['gf']-$division['ga'];
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
	$player = $users->info($division['player_id']);
	$team_color = $division['player_id'] == 0 ? "colorless":"colorful";
	$team_situation = $division['player_id'] == 0 ? "" . $division['team_full'] . " :: Komanda ir brīva!":"" . $division['team_full'] . " :: " . $player['username'] . "";
?>
	<tr style="color:black; background-color:<?php echo $color; ?>;">
		<td <?php echo $my_team; ?> class="turnirs"><center><?php echo $num; ?></center></td>
		<td id="team_logo" <?php echo $my_team; ?> class="turnirs" title="<?php echo $team_situation; ?>">
			<a style="color:black;" href="/league/<?php echo $row['league_id']; ?>/team/<?php echo $division['team_small']; ?>/">
				<img alt="<?php echo $division['team_small']; ?>" src="<?php echo BASE ?>/style/images/teams/small/<?php echo $team_color; ?>/<?php echo $division['team_small']?>.png">
					<div class="teamname"><?php echo $division['team_full']; ?></div>
			</a>
		</td>
		<td class="turnirs" <?php echo $games_play_want; ?> <?php echo $my_team; ?>><center><?php echo $division['gp']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $division['w']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $division['ot']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $division['l']; ?></center></td>
		<td class="turnirs" style="background-color:#D8D8D8;"><center><b><?php echo $division['pts']; ?></b></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $division['gf']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $division['ga']; ?></center></td>
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
			if( $division['streak_won'] == 0 AND $division['streak_lost'] == 0 )
			{
				echo '---';
			}
			else
			{
				if( $division['streak_won'] > 0 AND $division['streak_lost'] == 0 )
				{
					echo "WON " . $division['streak_won'] . "";
				}
				else
				{
					echo "LOST " . $division['streak_lost'] . "";
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