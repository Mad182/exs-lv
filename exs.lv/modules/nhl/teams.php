<div style="float:left; width:950px;">
	<div style="float:left; width:485px;">
			<img src="<?php echo BASE ?>/themes/<?php echo SITE_THEME ?>/images/western.gif" style="width: 464px;" />
			<table class="west" cellspacing="0" cellpadding="1" style="width: 464px;">
				<tr style="color:white; font-size:13px; font-weight:bold;" bgcolor="#6F7070">
					<td width="464" style="padding-left:3px;">CENTRAL DIVISION</td>
				</tr>
<?php
$i = 1;
$num = 0;
$col = '';
$division_q = $db->query( "SELECT * FROM league_nhl_standings WHERE division = 'central' AND league_id = '" . LEAGUE_ID . "' ORDER BY pts DESC, gf - ga DESC, gp ASC" );
while( $division = $db->fetch( $division_q ) )
{
	$games_play_want = $division['gp'] < 30 ? " style='background: rgb(255, 204, 204);'" : "";
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
	$team_situation = $division['player_id'] == 0 ? "<i>Komanda ir brīva</i>":"<a href='/user/" . $player['id'] . "/" . $player['username'] . "/'>" . $player['username'] . "</a>";
?>
	<tr style="color:black;">
		<td id="team_logo" <?php echo $my_team; ?> class="turnirs_beigas">
				<div style="float: left;">
<img alt="<?php echo $division['team_small']; ?>" src="<?php echo BASE ?>/style/images/teams/medium/<?php echo $team_color; ?>/<?php echo $division['team_small']?>.png">
</div>
<div style="float: left; padding-left: 5px; margin-top:-3px; font-size: 11px;">
<b><?php echo $division['team_full']; ?><br></b>
Treneris: <?php echo $team_situation; ?>
</div>

			</a>
		</td>
	</tr>
<?php
$i++;
}
?>
 </table>
 <br />
 			<table class="west" cellspacing="0" cellpadding="1" style="width: 464px;">
				<tr style="color:white; font-size:13px; font-weight:bold;" bgcolor="#6F7070">
					<td width="464" style="padding-left:3px;">NORTHWEST DIVISION</td>
				</tr>
<?php
$i = 1;
$num = 0;
$col = '';
$division_q = $db->query( "SELECT * FROM league_nhl_standings WHERE division = 'northwest' AND league_id = '" . LEAGUE_ID . "' ORDER BY pts DESC, gf - ga DESC, gp ASC" );
while( $division = $db->fetch( $division_q ) )
{
	$games_play_want = $division['gp'] < 30 ? " style='background: rgb(255, 204, 204);'" : "";
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
	$team_situation = $division['player_id'] == 0 ? "<i>Komanda ir brīva</i>":"<a href='/user/" . $player['id'] . "/" . $player['username'] . "/'>" . $player['username'] . "</a>";
?>
	<tr style="color:black;">
		<td id="team_logo" <?php echo $my_team; ?> class="turnirs_beigas">
				<div style="float: left;">
<img alt="<?php echo $division['team_small']; ?>" src="<?php echo BASE ?>/style/images/teams/medium/<?php echo $team_color; ?>/<?php echo $division['team_small']?>.png">
</div>
<div style="float: left; padding-left: 5px; margin-top:-3px; font-size: 11px;">
<b><?php echo $division['team_full']; ?><br></b>
Treneris: <?php echo $team_situation; ?>
</div>

			</a>
		</td>
	</tr>
<?php
$i++;
}
?>
 </table>
   <br />
 	 			<table class="west" cellspacing="0" cellpadding="1" style="width: 464px;">
				<tr style="color:white; font-size:13px; font-weight:bold;" bgcolor="#6F7070">
					<td width="464" style="padding-left:3px;">PACIFIC DIVISION</td>
				</tr>
<?php
$i = 1;
$num = 0;
$col = '';
$division_q = $db->query( "SELECT * FROM league_nhl_standings WHERE division = 'pafific' AND league_id = '" . LEAGUE_ID . "' ORDER BY pts DESC, gf - ga DESC, gp ASC" );
while( $division = $db->fetch( $division_q ) )
{
	$games_play_want = $division['gp'] < 30 ? " style='background: rgb(255, 204, 204);'" : "";
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
	$team_situation = $division['player_id'] == 0 ? "<i>Komanda ir brīva</i>":"<a href='/user/" . $player['id'] . "/" . $player['username'] . "/'>" . $player['username'] . "</a>";
?>
	<tr style="color:black;">
		<td id="team_logo" <?php echo $my_team; ?> class="turnirs_beigas">
				<div style="float: left;">
<img alt="<?php echo $division['team_small']; ?>" src="<?php echo BASE ?>/style/images/teams/medium/<?php echo $team_color; ?>/<?php echo $division['team_small']?>.png">
</div>
<div style="float: left; padding-left: 5px; margin-top:-3px; font-size: 11px;">
<b><?php echo $division['team_full']; ?><br></b>
Treneris: <?php echo $team_situation; ?>
</div>

			</a>
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
				<tr style="color:white; font-size:13px; font-weight:bold;" bgcolor="#6F7070">
					<td width="464" style="padding-left:3px;">ATLANTIC DIVISION</td>
				</tr>
<?php
$i = 1;
$num = 0;
$col = '';
$division_q = $db->query( "SELECT * FROM league_nhl_standings WHERE division = 'atlantic' AND league_id = '" . LEAGUE_ID . "' ORDER BY pts DESC, gf - ga DESC, gp ASC" );
while( $division = $db->fetch( $division_q ) )
{
	$games_play_want = $division['gp'] < 30 ? " style='background: rgb(255, 204, 204);'" : "";
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
	$team_situation = $division['player_id'] == 0 ? "<i>Komanda ir brīva</i>":"<a href='/user/" . $player['id'] . "/" . $player['username'] . "/'>" . $player['username'] . "</a>";
?>
	<tr style="color:black;">
		<td id="team_logo" <?php echo $my_team; ?> class="turnirs_beigas">
				<div style="float: left;">
<img alt="<?php echo $division['team_small']; ?>" src="<?php echo BASE ?>/style/images/teams/medium/<?php echo $team_color; ?>/<?php echo $division['team_small']?>.png">
</div>
<div style="float: left; padding-left: 5px; margin-top:-3px; font-size: 11px;">
<b><?php echo $division['team_full']; ?><br></b>
Treneris: <?php echo $team_situation; ?>
</div>

			</a>
		</td>
	</tr>
<?php
$i++;
}
?>
 </table>
    <br />
 		 	 			<table class="west" cellspacing="0" cellpadding="1" style="width: 464px;">
				<tr style="color:white; font-size:13px; font-weight:bold;" bgcolor="#6F7070">
					<td width="464" style="padding-left:3px;">NORTHEAST DIVISION</td>
				</tr>
<?php
$i = 1;
$num = 0;
$col = '';
$division_q = $db->query( "SELECT * FROM league_nhl_standings WHERE division = 'northeast' AND league_id = '" . LEAGUE_ID . "' ORDER BY pts DESC, gf - ga DESC, gp ASC" );
while( $division = $db->fetch( $division_q ) )
{
	$games_play_want = $division['gp'] < 30 ? " style='background: rgb(255, 204, 204);'" : "";
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
	$team_situation = $division['player_id'] == 0 ? "<i>Komanda ir brīva</i>":"<a href='/user/" . $player['id'] . "/" . $player['username'] . "/'>" . $player['username'] . "</a>";
?>
	<tr style="color:black;">
		<td id="team_logo" <?php echo $my_team; ?> class="turnirs_beigas">
				<div style="float: left;">
<img alt="<?php echo $division['team_small']; ?>" src="<?php echo BASE ?>/style/images/teams/medium/<?php echo $team_color; ?>/<?php echo $division['team_small']?>.png">
</div>
<div style="float: left; padding-left: 5px; margin-top:-3px; font-size: 11px;">
<b><?php echo $division['team_full']; ?><br></b>
Treneris: <?php echo $team_situation; ?>
</div>

			</a>
		</td>
	</tr>
<?php
$i++;
}
?>
 </table>
     <br />
 	 		 	 			<table class="west" cellspacing="0" cellpadding="1" style="width: 464px;">
				<tr style="color:white; font-size:13px; font-weight:bold;" bgcolor="#6F7070">
					<td width="464" style="padding-left:3px;">SOUTHEAST DIVISION</td>
				</tr>
<?php
$i = 1;
$num = 0;
$col = '';
$division_q = $db->query( "SELECT * FROM league_nhl_standings WHERE division = 'southeast' AND league_id = '" . LEAGUE_ID . "' ORDER BY pts DESC, gf - ga DESC, gp ASC" );
while( $division = $db->fetch( $division_q ) )
{
	$games_play_want = $division['gp'] < 30 ? " style='background: rgb(255, 204, 204);'" : "";
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
	$team_situation = $division['player_id'] == 0 ? "<i>Komanda ir brīva</i>":"<a href='/user/" . $player['id'] . "/" . $player['username'] . "/'>" . $player['username'] . "</a>";
?>
	<tr style="color:black;">
		<td id="team_logo" <?php echo $my_team; ?> class="turnirs_beigas">
				<div style="float: left;">
<img alt="<?php echo $division['team_small']; ?>" src="<?php echo BASE ?>/style/images/teams/medium/<?php echo $team_color; ?>/<?php echo $division['team_small']?>.png">
</div>
<div style="float: left; padding-left: 5px; margin-top:-3px; font-size: 11px;">
<b><?php echo $division['team_full']; ?><br></b>
Treneris: <?php echo $team_situation; ?>
</div>

			</a>
		</td>
	</tr>
<?php
$i++;
}
?>
 </table>
	</div>
</div>