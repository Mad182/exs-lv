<div id="playoff_bg">
<div id="playoff">
<div class="west18">
<?
$playoff_t_west18 = mysql_query("SELECT * FROM league_nhl_po WHERE conference = '".mysql_real_escape_string('western')."' AND karta = '".mysql_real_escape_string('1/8')."' AND league_id = '" . LEAGUE_ID . "'");
while($playoff_team18 = mysql_fetch_assoc($playoff_t_west18))
{
$team1 = mysql_query("SELECT * FROM league_nhl_standings WHERE team_small = '$playoff_team18[team]' AND league_id = '" . LEAGUE_ID . "'");
$izvelk_id1=mysql_fetch_array($team1);
$player = $users->info($izvelk_id1['player_id']);
$res1 = $db->query('SELECT * FROM league_nhl_standings WHERE player_id = "' . $player['id'] . '"');
while( $row1 = $db->fetch( $res1 ) )
{
	$my_nhl_rating1 = number_format( $row1['w'] / $row1['gp'] - $row1['l'] / $row1['gp'], 3 ) * 10;
}
?>
<div class="team"><a href="skype:<?=$player['skype'];?>?chat"><img src="/style/images/teams/medium/colorful/<?=$playoff_team18['team'];?>.png" alt="" title="<?=$izvelk_id1['team_full'];?> - <?=$player['username'];?> (<?=$my_nhl_rating1?>)" /></a><div class="goalsw" title=""><?=$playoff_team18['serie'];?></div></div>
<?
}
?>
</div>
<div class="west14">
<?
$playoff_t_west14 = mysql_query("SELECT * FROM league_nhl_po WHERE conference = '".mysql_real_escape_string('western')."' AND karta = '".mysql_real_escape_string('1/4')."' AND league_id = '" . LEAGUE_ID . "'");
while($playoff_team14 = mysql_fetch_assoc($playoff_t_west14))
{
$team1 = mysql_query("SELECT * FROM league_nhl_standings WHERE team_small = '$playoff_team14[team]' AND league_id = '" . LEAGUE_ID . "'");
$izvelk_id1=mysql_fetch_array($team1);
$player = $users->info($izvelk_id1['player_id']);
$res1 = $db->query('SELECT * FROM league_nhl_standings WHERE player_id = "' . $player['id'] . '"');
while( $row1 = $db->fetch( $res1 ) )
{
	$my_nhl_rating1 = number_format( $row1['w'] / $row1['gp'] - $row1['l'] / $row1['gp'], 3 ) * 10;
}
$team_img1 = $playoff_team14['team'] == '' ? "/style/images/no_team.png":"/style/images/teams/medium/colorful/$playoff_team14[team].png";
$title1 = $playoff_team14['team'] == '' ? "":"$izvelk_id1[team_full] - $player[username] ($my_nhl_rating1)";
$url_sakums1 = $playoff_team14['team'] == '' ? "":"<a href=skype:$player[skype]?chat>";
$url_beigas1 = $playoff_team14['team'] == '' ? "":"</a>";
?>
<div class="team"><?=$url_sakums1;?><img src="<?=$team_img1;?>" alt="" title="<?=$title1;?>" /><?=$url_beigas1;?><div class="goalsw" title=""><?=$playoff_team14['serie'];?></div></div>
<?
}
?>
</div>
<div class="west12">
<?
$playoff_t_west12 = mysql_query("SELECT * FROM league_nhl_po WHERE conference = '".mysql_real_escape_string('western')."' AND karta = '".mysql_real_escape_string('1/2')."' AND league_id = '" . LEAGUE_ID . "'");
while($playoff_team12 = mysql_fetch_assoc($playoff_t_west12))
{
$team1 = mysql_query("SELECT * FROM league_nhl_standings WHERE team_small = '$playoff_team12[team]' AND league_id = '" . LEAGUE_ID . "'");
$izvelk_id1=mysql_fetch_array($team1);
$player = $users->info($izvelk_id1['player_id']);
$res1 = $db->query('SELECT * FROM league_nhl_standings WHERE player_id = "' . $player['id'] . '"');
while( $row1 = $db->fetch( $res1 ) )
{
	$my_nhl_rating1 = number_format( $row1['w'] / $row1['gp'] - $row1['l'] / $row1['gp'], 3 ) * 10;
}
$team_img1 = $playoff_team12['team'] == '' ? "/style/images/no_team.png":"/style/images/teams/medium/colorful/$playoff_team12[team].png";
$title1 = $playoff_team12['team'] == '' ? "":"$izvelk_id1[team_full] - $player[username] ($my_nhl_rating1)";
$url_sakums1 = $playoff_team12['team'] == '' ? "":"<a href=skype:$player[skype]?chat>";
$url_beigas1 = $playoff_team12['team'] == '' ? "":"</a>";
?>
<div class="team"><?=$url_sakums1;?><img src="<?=$team_img1;?>" alt="" title="<?=$title1;?>" /><?=$url_beigas1;?><div class="goalsw" title=""><?=$playoff_team12['serie'];?></div></div>
<?
}
?>
</div>
<div class="westf">
<?
$playoff_t_westf = mysql_query("SELECT * FROM league_nhl_po WHERE conference = '".mysql_real_escape_string('western')."' AND karta = '".mysql_real_escape_string('final')."' AND league_id = '" . LEAGUE_ID . "'");
while($playoff_teamf = mysql_fetch_assoc($playoff_t_westf))
{
$team1 = mysql_query("SELECT * FROM league_nhl_standings WHERE team_small = '$playoff_teamf[team]' AND league_id = '" . LEAGUE_ID . "'");
$izvelk_id1=mysql_fetch_array($team1);
$player = $users->info($izvelk_id1['player_id']);
$res1 = $db->query('SELECT * FROM league_nhl_standings WHERE player_id = "' . $player['id'] . '"');
while( $row1 = $db->fetch( $res1 ) )
{
	$my_nhl_rating1 = number_format( $row1['w'] / $row1['gp'] - $row1['l'] / $row1['gp'], 3 ) * 10;
}
$team_img1 = $playoff_teamf['team'] == '' ? "/style/images/no_team.png":"/style/images/teams/medium/colorful/$playoff_teamf[team].png";
$title1 = $playoff_teamf['team'] == '' ? "":"$izvelk_id1[team_full] - $player[username] ($my_nhl_rating1)";
$url_sakums1 = $playoff_teamf['team'] == '' ? "":"<a href=skype:$player[skype]?chat>";
$url_beigas1 = $playoff_teamf['team'] == '' ? "":"</a>";
?>
<div class="team"><?=$url_sakums1;?><img src="<?=$team_img1;?>" alt="" title="<?=$title1;?>" /><?=$url_beigas1;?><div class="goalsw" title=""><?=$playoff_teamf['serie'];?></div></div>
<?
}
?>
</div>
<div class="eastf">
<?
$playoff_t_eastf = mysql_query("SELECT * FROM league_nhl_po WHERE conference = '".mysql_real_escape_string('eastern')."' AND karta = '".mysql_real_escape_string('final')."' AND league_id = '" . LEAGUE_ID . "'");
while($playoff_teamf = mysql_fetch_assoc($playoff_t_eastf))
{
$team1 = mysql_query("SELECT * FROM league_nhl_standings WHERE team_small = '$playoff_teamf[team]' AND league_id = '" . LEAGUE_ID . "'");
$izvelk_id1=mysql_fetch_array($team1);
$player = $users->info($izvelk_id1['player_id']);
$res1 = $db->query('SELECT * FROM league_nhl_standings WHERE player_id = "' . $player['id'] . '"');
while( $row1 = $db->fetch( $res1 ) )
{
	$my_nhl_rating2 = number_format( $row1['w'] / $row1['gp'] - $row1['l'] / $row1['gp'], 3 ) * 10;
}
$team_img2 = $playoff_teamf['team'] == '' ? "/style/images/no_team.png":"/style/images/teams/medium/colorful/$playoff_teamf[team].png";
$title2 = $playoff_teamf['team'] == '' ? "":"$izvelk_id1[team_full] - $player[username] ($my_nhl_rating2)";
$url_sakums2 = $playoff_teamf['team'] == '' ? "":"<a href=skype:$player[skype]?chat>";
$url_beigas2 = $playoff_teamf['team'] == '' ? "":"</a>";
?>
<div class="team"><div class="goalse" title=""><?=$playoff_teamf['serie'];?></div><?=$url_sakums2;?><img src="<?=$team_img2;?>" alt="" title="<?=$title2;?>" /><?=$url_beigas2;?></div>
<?
}
?>
</div>
<div class="east12">
<?
$playoff_t_east12 = mysql_query("SELECT * FROM league_nhl_po WHERE conference = '".mysql_real_escape_string('eastern')."' AND karta = '".mysql_real_escape_string('1/2')."' AND league_id = '" . LEAGUE_ID . "'");
while($playoff_team12 = mysql_fetch_assoc($playoff_t_east12))
{
$team1 = mysql_query("SELECT * FROM league_nhl_standings WHERE team_small = '$playoff_team12[team]' AND league_id = '" . LEAGUE_ID . "'");
$izvelk_id1=mysql_fetch_array($team1);
$player = $users->info($izvelk_id1['player_id']);
$res1 = $db->query('SELECT * FROM league_nhl_standings WHERE player_id = "' . $player['id'] . '"');
while( $row1 = $db->fetch( $res1 ) )
{
	$my_nhl_rating1 = number_format( $row1['w'] / $row1['gp'] - $row1['l'] / $row1['gp'], 3 ) * 10;
}
$team_img1 = $playoff_team12['team'] == '' ? "/style/images/no_team.png":"/style/images/teams/medium/colorful/$playoff_team12[team].png";
$title1 = $playoff_team12['team'] == '' ? "":"$izvelk_id1[team_full] - $player[username] ($my_nhl_rating1)";
$url_sakums1 = $playoff_team12['team'] == '' ? "":"<a href=skype:$player[skype]?chat>";
$url_beigas1 = $playoff_team12['team'] == '' ? "":"</a>";
?>
<div class="team"><div class="goalse" title=""><?=$playoff_team12['serie'];?></div><?=$url_sakums1;?><img src="<?=$team_img1;?>" alt="" title="<?=$title1;?>" /><?=$url_beigas1;?></div>
<?
}
?></div>
<div class="east14">
<?
$playoff_t_east14 = mysql_query("SELECT * FROM league_nhl_po WHERE conference = '".mysql_real_escape_string('eastern')."' AND karta = '".mysql_real_escape_string('1/4')."' AND league_id = '" . LEAGUE_ID . "'");
while($playoff_team14 = mysql_fetch_assoc($playoff_t_east14))
{
$team1 = mysql_query("SELECT * FROM league_nhl_standings WHERE team_small = '$playoff_team14[team]' AND league_id = '" . LEAGUE_ID . "'");
$izvelk_id1=mysql_fetch_array($team1);
$player = $users->info($izvelk_id1['player_id']);
$res1 = $db->query('SELECT * FROM league_nhl_standings WHERE player_id = "' . $player['id'] . '"');
while( $row1 = $db->fetch( $res1 ) )
{
	$my_nhl_rating1 = number_format( $row1['w'] / $row1['gp'] - $row1['l'] / $row1['gp'], 3 ) * 10;
}
$team_img1 = $playoff_team14['team'] == '' ? "/style/images/no_team.png":"/style/images/teams/medium/colorful/$playoff_team14[team].png";
$title1 = $playoff_team14['team'] == '' ? "":"$izvelk_id1[team_full] - $player[username] ($my_nhl_rating1)";
$url_sakums1 = $playoff_team14['team'] == '' ? "":"<a href=skype:$player[skype]?chat>";
$url_beigas1 = $playoff_team14['team'] == '' ? "":"</a>";
?>
<div class="team"><div class="goalse" title=""><?=$playoff_team14['serie'];?></div><?=$url_sakums1;?><img src="<?=$team_img1;?>" alt="" title="<?=$title1;?>" /><?=$url_beigas1;?></div>
<?
}
?>
</div>
<div class="east18">
<?
$playoff_t_east18 = mysql_query("SELECT * FROM league_nhl_po WHERE conference = '".mysql_real_escape_string('eastern')."' AND karta = '".mysql_real_escape_string('1/8')."' AND league_id = '" . LEAGUE_ID . "'");
while($playoff_team18 = mysql_fetch_assoc($playoff_t_east18))
{
$team1 = mysql_query("SELECT * FROM league_nhl_standings WHERE team_small = '$playoff_team18[team]' AND league_id = '" . LEAGUE_ID . "'");
$izvelk_id1=mysql_fetch_array($team1);
$player = $users->info($izvelk_id1['player_id']);
$res1 = $db->query('SELECT * FROM league_nhl_standings WHERE player_id = "' . $player['id'] . '"');
while( $row1 = $db->fetch( $res1 ) )
{
	$my_nhl_rating1 = number_format( $row1['w'] / $row1['gp'] - $row1['l'] / $row1['gp'], 3 ) * 10;
}
?>
<div class="team"><div class="goalse" title=""><?=$playoff_team18['serie'];?></div><a href="skype:<?=$player['skype'];?>?chat"><img src="/style/images/teams/medium/colorful/<?=$playoff_team18['team'];?>.png" alt="" title="<?=$izvelk_id1['team_full'];?> - <?=$player['username'];?> (<?=$my_nhl_rating1?>)" /></a></div>
<?
}
?>
</div>
<?
$sc_winner = mysql_query("SELECT *, SUM(hw) AS h_wins, SUM(hl) AS h_loses, SUM(hot) AS h_otl, SUM(aw) AS a_wins, SUM(al) AS a_loses, SUM(aot) AS a_otl FROM league_nhl_po WHERE karta = '".mysql_real_escape_string('final')."' AND league_id = '" . LEAGUE_ID . "'");
$winner = mysql_fetch_array($sc_winner);
if($winner['serie'] == 4)
{
$win_username = mysql_query("SELECT * FROM league_nhl_standings WHERE team_small = '$winner[team]' AND league_id = '" . LEAGUE_ID . "'");
$winner_username = mysql_fetch_array($win_username);
$sc_winner_player = $users->info($winner_username['player_id']);
$check_league_q1 = $db->query( "SELECT * FROM leagues WHERE league_id = " . LEAGUE_ID );
$check_league1 = $db->fetch( $check_league_q1 );
?>
<div style="float:left; width:152px;">
<div class="stenly_cup">
<div style="float:left; width:50px;">
<img style="margin-top:-84px; margin-left:5px;" src="/themes/<?php echo SITE_THEME; ?>/images/stanley.png" height="80px" alt="" title=""/>
</div>
<div style="float:left; width:102px;">
<img style="margin-top:-82px;" src="/style/images/teams/medium/colorful/<?=$winner['team'];?>.png" alt="" title=""/>
<div><a href="/user/<?=$sc_winner_player['id']?>/<?=$sc_winner_player['username']?>"><?=$sc_winner_player['username']?></a><br>
<small><b><?=$check_league1['league_title']?>s</b><br>STANLEY CUP WINNER</small></div>
</div>
</div>
</div>
<?
}
?>
</div>
</div>