<?php 
$team = $db->fetch($db->query("SELECT * FROM league_nhl_standings WHERE player_id = '" . get_cookie('user_id') . "'"));
$no_played = mysql_num_rows(mysql_query("SELECT * FROM league_nhl_calendar WHERE played = '0' AND team1 = '$team[team_small]'"));
?>
<fieldset class="tree-stars" style="width: 930px;margin-top:25px;">
<legend class="tree-stars2"><a href="javascript:;">Neizspēlētās spēles (<?php echo $no_played; ?>)</a></legend>
<?
$i = 1;
$western_q = $db->query( "SELECT * FROM league_nhl_calendar WHERE played = '0' AND team1 = '$team[team_small]'" );
while( $western = $db->fetch( $western_q ) )
{
?>
<div class="r1WestMatchup" style="margin:10px;margin-left:15px; border:1px solid gray;"> 
<div style="height: 5px;"></div>
<div class="gameScore"> 
<div class="teamSeed"><img src="/style/images/teams/small/colorful/<?php echo $western['team1']; ?>.png" border="0"><?php echo $western['team1']; ?></div>
<div class="gameStatus">
<div class="teamScore">0</div>
<div class="teamScore">0</div>
---
</div>
<div class="teamSeed">
<img src="/style/images/teams/small/colorful/<?php echo $western['team2']; ?>.png" border="0"><?php echo $western['team2']; ?></div>
</div>
<div class="seriesMatchup">
NO PLAYED THIS GAME
</div> 
</div>
<?
}
if($i == 1) 
echo 'Tu esi izspēlējis visas regulārās sezonas spēles šajā līgā.';
$played = mysql_num_rows(mysql_query("SELECT * FROM league_nhl_calendar WHERE played = '1' AND team1 = '$team[team_small]'"));
?>
</fieldset>

<fieldset class="tree-stars" style="width: 930px;margin-top:15px;">
<legend class="tree-stars2"><a href="javascript:;">Izspēlētās spēles (<?php echo $played; ?>)</a></legend>
<?
$regular_games_qs = $db->query( "SELECT * FROM league_nhl_games WHERE km1 = '$team[team_small]' OR km2 = '$team[team_small]' AND league_id = " . LEAGUE_ID . "" );
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
echo 'Tu neesi izspēlējis nevienu spēli šajā līgā.';
?>
</fieldset>