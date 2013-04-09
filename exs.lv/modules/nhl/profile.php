<?php
// Pārbauda vai ir atvērta pieteikšanās turnīram
$check_league_q = $db->query( "SELECT * FROM leagues WHERE league_id = " . LEAGUE_ID );
$check_league = $db->fetch( $check_league_q );
// Parbauda vai lietotājs piedalas šajā turnīrā
$res = $db->query( "SELECT * FROM league_nhl_standings WHERE player_id = '" . get_cookie('user_id') . "' AND league_id = '" . LEAGUE_ID . "'" );
$row = $db->fetch( $res );
if( IS_USER )
{
if( get_cookie('user_id') == $row['player_id'] )
{
if( $check_league['playoff'] == 1 )
{
	include ROOT . '/pages/league/NHL/playoff.php';
}
else
{
$player = $users->info(get_cookie('user_id'));

if($player['tourney_warns'] == 0 ){
	$warn_color = "black";
}
elseif($player['tourney_warns'] == 1 ){
	$warn_color = "green";
}
elseif($player['tourney_warns'] == 2 ){
	$warn_color = "gold";
}
elseif($player['tourney_warns'] == 3 ){
	$warn_color = "red";
}

$kopa_jaizspele = $db->rows($db->query( "SELECT * FROM league_nhl_calendar WHERE team1 = '" . $row['team_small'] . "' AND league_id = '" . LEAGUE_ID . "'" ));
$izspeletas_speles = $db->rows($db->query( "SELECT * FROM league_nhl_calendar WHERE team1 = '" . $row['team_small'] . "' AND played = '1'  AND league_id = '" . LEAGUE_ID . "'" ));
$atlikusas_speles = $kopa_jaizspele - $izspeletas_speles;
?>
<div style="float:left; width:950px; padding-top:5px;">
<div style="float:left; width:200px;">

<fieldset class='myteam'><legend class='myteam2'><b>TURNĪRA KOMANDA</b></legend>
<center>
	<img src="<?php echo BASE ?>/style/images/teams/medium/colorful/<?php echo $row['team_small']; ?>.png"><br>
		<?php echo $row['team_full']; ?>
	<br />
	<span style="color:black;">
		Turnīra brīdinājumi: <b>0</b>
	</span>
</center>
</fieldset>

<br />

<fieldset class='myteam'><legend class='myteam2'><b>INFORMĀCIJA</b></legend>
Kopā tev jaizspēle <b><?php echo format_number($kopa_jaizspele); ?></b> <?php echo (substr($kopa_jaizspele, -1) == 1 ? "spēle":"spēles"); ?>
<br />
Tu esi izspēlējis <b><?php echo format_number($izspeletas_speles); ?></b> <?php echo (substr($izspeletas_speles, -1) == 1 ? "spēli":"spēles"); ?>
<br />
<?php echo (substr($atlikusas_speles, -1) == 1 ? "Atlikusi":"Atlikušas"); ?> <b><?php echo $atlikusas_speles; ?></b> <?php echo (substr($atlikusas_speles, -1) == 1 ? "spēle":"spēles"); ?>
</fieldset>

<br />

<fieldset class='myteam'><legend class='myteam2'><b>MĀJAS & VIESOS</b></legend>
Mājas <?php echo (substr($row['hw'], -1) == 1 ? "uzvarēta":"uzvarētas"); ?> <b><?php echo $row['hw'];?></b> <?php echo (substr($row['hw'], -1) == 1 ? "spēle":"spēles"); ?>
<br />
Mājas OT <?php echo (substr($row['hot'], -1) == 1 ? "zaudēta":"zaudētas")?> <b><?php echo $row['hot'];?></b> <?php echo (substr($row['hot'], -1) == 1 ? "spēle":"spēles"); ?>
<br />
Mājas <?php echo (substr($row['hl'], -1) == 1 ? "zaudēta":"zaudētas")?> <b><?php echo $row['hl'];?></b> <?php echo (substr($row['hl'], -1) == 1 ? "spēle":"spēles"); ?>
<br />
Viesos <?php echo (substr($row['aw'], -1) == 1 ? "uzvarēta":"uzvarētas")?> <b><?php echo $row['aw'];?></b> <?php echo (substr($row['aw'], -1) == 1 ? "spēle":"spēles"); ?>
<br />
Viesos OT <?php echo (substr($row['aot'], -1) == 1 ? "zaudēta":"zaudētas")?> <b><?php echo $row['aot'];?></b> <?php echo (substr($row['aot'], -1) == 1 ? "spēle":"spēles"); ?>
<br />
Viesos <?php echo (substr($row['al'], -1) == 1 ? "zaudēta":"zaudētas")?> <b><?php echo $row['al'];?></b> <?php echo (substr($row['al'], -1) == 1 ? "spēle":"spēles"); ?>
</fieldset>

</div>
<div style="float:left; width:565px;">
<fieldset class='stats_upload'><legend class='stats_upload2'><b>PIEVIENOT REZULTĀTU</b></legend>
<?php
if( get_post('upload_stats') ){

$error = array();

if( get_file_extension( $_FILES["gamestats"]["name"] ) != 'csv' )
{
	$error[] = 'Pievienotais gamestat fails nav <strong>.csv</strong> formāta!';
}

if( get_file_extension( $_FILES["playerstats"]["name"] ) != 'csv' )
{
	$error[] = 'Pievienotais playerstat fails nav <strong>.csv</strong> formāta!';
}

if( get_file_extension( $_FILES["teamstats"]["name"] ) != 'csv' )
{
	$error[] = 'Pievienotais teamstat fails nav <strong>.csv</strong> formāta!';
}

$game_stats = $_FILES["gamestats"]["name"];
$player_stats = $_FILES["playerstats"]["name"];
$team_stats = $_FILES["teamstats"]["name"];

if( preg_match( "/gamestat/i", "$game_stats" ) ) {
	echo '';
}
else
{
	$error[] = 'Pievienotais <i>gamestat</i> fails, nav <strong>gamestat</strong> fails!';
}

if( preg_match( "/playerstat/i", "$player_stats" ) ) {
	echo '';
}
else
{
	$error[] = 'Pievienotais <i>playerstat</i> fails, nav <strong>playerstat</strong> fails!';
}

if( preg_match( "/teamstat/i", "$team_stats" ) ) {
	echo '';
}
else
{
	$error[] = 'Pievienotais <i>teamstat</i> fails, nav <strong>teamstat</strong> fails!';
}

// Izņemuma komanda
if( $row['team_small'] == 'WPG' )
{
	$row['team_small'] = 'ATL';
}

if( preg_match( "/$row[team_small]/i", "$game_stats" ) ) {
	echo '';
}
else
{
	$error[] = 'Pievienotais <i>gamestat</i> fails, nav jūsu komandas gamestati ar ko jūs spēlējiet šinī turnīrā!';
}

if( preg_match( "/$row[team_small]/i", "$player_stats" ) ) {
	echo '';
}
else
{
	$error[] = 'Pievienotais <i>playerstat</i> fails, nav jūsu komandas gamestati ar ko jūs spēlējiet šinī turnīrā!';
}

if( preg_match( "/$row[team_small]/i", "$team_stats" ) ) {
	echo '';
}
else
{
	$error[] = 'Pievienotais <i>teamstat</i> fails, nav jūsu komandas teamstati ar ko jūs spēlējiet šinī turnīrā!';
}

if( count( $error ) >= 1 )
{
echo "<div class='message error' style='width:473x;'>";
foreach( $error as $err )
{
	echo "$err<br />";
}
echo "</div>";
// Update Tourney warns
$db->query( "UPDATE users SET `tourney_warns` = `tourney_warns` +1 WHERE id =  " . get_cookie('user_id') );
}
else
{
/*** Start uploading game stats ***/

// gamestats
$game_stat = $db->query( "SELECT * FROM league_nhl_gamestats WHERE league_id = '" . LEAGUE_ID . "' ORDER BY `id` DESC" );
$game_stat_id = $db->fetch( $game_stat );
$game_stat_nr = $game_stat_id['id'];

if($game_stat_nr == '')
{
	$game_stat_nr1 = 1;
}
else
{
	$game_stat_nr1 = $game_stat_nr+1;
}

$handle = fopen( $_FILES["gamestats"]["tmp_name"], "r" );
$data = fgetcsv( $handle, 1000, "Period" );
while( ( $data = fgetcsv( $handle, 1000, "," ) ) != FALSE )
{

	if( $data[3] == 'ATL' )
	{
		$data_team = 'WPG';
	}
	else
	{
		$data_team = $data[3];
	}

        $gamestats_data = array(
					  'id'=>$game_stat_nr1,
                      'period'=>$data[0],
                      'time'=>$data[1],
					  'goal_penaty'=>$data[2],
					  'team'=>$data_team,
					  'player_name'=>$data[4],
                      'note'=>$data[5],
					  'league_id'=>LEAGUE_ID
                      );
					  
        $db->insert_array( 'league_nhl_gamestats', $gamestats_data );
}
fclose($handle);

// playerstats
$player_stat = $db->query( "SELECT * FROM league_nhl_playerstats WHERE league_id = '" . LEAGUE_ID . "' ORDER BY `id` DESC" );
$player_stat_id = $db->fetch( $player_stat );
$player_stat_nr = $player_stat_id['id'];

if($player_stat_nr == '')
{
	$player_stat_nr1 = 1;
}
else
{
	$player_stat_nr1 = $player_stat_nr+1;
}

$handle1 = fopen( $_FILES["playerstats"]["tmp_name"], "r" );
$data1 = fgetcsv( $handle1, 1000, "Team" );
while( ( $data1 = fgetcsv( $handle1, 1000, "," ) ) != FALSE )
{

$time = $data1[3];
$time = explode(":", $time);
$player_time = $time[0] * 60 + $time[1];

	if( $data1[1] == 'ATL' )
	{
		$data1_team = 'WPG';
	}
	else
	{
		$data1_team = $data1[1];
	}

        $playerstats_data = array(
					  'id'=>$player_stat_nr1,
                      'player_name'=>$data1[0],
                      'team'=>$data1_team,
					  'pos'=>$data1[2],
					  'time'=>$player_time,
					  'goal'=>$data1[4],
                      'asst'=>$data1[5],
					  'pims'=>$data1[6],
					  'plus_minus'=>$data1[7],
					  'ppg_ga'=>$data1[8],
					  'shg_eng'=>$data1[9],
					  'shot_sa'=>$data1[10],
					  'hits'=>$data1[11],
					  'added'=>time(),
					  'league_id'=>LEAGUE_ID
                      );
					  
        $db->insert_array( 'league_nhl_playerstats', $playerstats_data );
		
if($data1[3] == '00:00')
{
	$game_played = 0;
}
else
{
	$game_played = 1;
}

// Update or add playerstats
$query = $db->query( "SELECT * FROM `league_nhl_statistics` WHERE `player_name` = '" . mysql_real_escape_string($data1[0]) . "' AND league_id = " . LEAGUE_ID );

if( $db->rows( $query ) )
{
	$db->query( "UPDATE league_nhl_statistics SET gp=gp+'" . $game_played . "', time=time+'" . $player_time . "', goal=goal+'" . $data1[4] . "', asst=asst+'" . $data1[5] . "', pims=pims+'" . $data1[6] . "', plus_minus=plus_minus+'" . $data1[7] . "', ppg_ga=ppg_ga+'" . $data1[8] . "', shg_eng=shg_eng+'" . $data1[9] . "', shot_sa=shot_sa+'" . $data1[10] . "', hits=hits+'" . $data1[11] . "' WHERE player_name = '" . mysql_real_escape_string($data1[0]) . "' AND league_id = " . LEAGUE_ID );
}
else
{
       $insertstats_data = array(
                      'player_name'=>mysql_real_escape_string($data1[0]),
					  'gp'=>$game_played,
                      'team'=>$data1_team,
					  'pos'=>$data1[2],
					  'time'=>$player_time,
					  'goal'=>$data1[4],
                      'asst'=>$data1[5],
					  'pims'=>$data1[6],
					  'plus_minus'=>$data1[7],
					  'ppg_ga'=>$data1[8],
					  'shg_eng'=>$data1[9],
					  'shot_sa'=>$data1[10],
					  'hits'=>$data1[11],
					  'league_id'=>LEAGUE_ID
                      );
					  
       $db->insert_array( 'league_nhl_statistics', $insertstats_data );
}
}
fclose($handle1);

// teamstats
$team_stat = $db->query( "SELECT * FROM league_nhl_teamstats WHERE league_id = '" . LEAGUE_ID . "' ORDER BY `id` DESC" );
$team_stat_id = $db->fetch( $team_stat );
$team_stat_nr = $team_stat_id['id'];

if($team_stat_nr == '')
{
	$team_stat_nr1 = 1;
}
else
{
	$team_stat_nr1 = $team_stat_nr+1;
}

$handle2 = fopen( $_FILES["teamstats"]["tmp_name"], "r" );
$data2 = fgetcsv( $handle2, 1000, "Team" );
while( ( $data2 = fgetcsv( $handle2, 1000, "," ) ) != FALSE )
{

	if( $data2[1] == 'ATL' )
	{
		$data2_team = 'WPG';
	}
	else
	{
		$data2_team = $data2[1];
	}

if( $data2[15] == 0 )
{
	$vs = 'Normal';
}
else
{
	$vs = 'CAW';
}

        $teamstats_data = array(
					  'id'=>$team_stat_nr1,
                      'host'=>$data2[0],
                      'team'=>$data2_team,
					  'goal'=>$data2[2],
					  'shot'=>$data2[3],
					  'sh_goal'=>$data2[4],
                      'breakaway'=>$data2[5],
					  'onetimers'=>$data2[6],
					  'onetimer_goal'=>$data2[7],
					  'faceoff'=>$data2[8],
					  'hits'=>$data2[9],
					  'penalties'=>$data2[10],
					  'en_goal'=>$data2[11],
					  'gp1'=>$data2[12],
					  'gp2'=>$data2[13],
					  'gp3'=>$data2[14],
					  'gpot'=>$data2[15],
					  'pp'=>$data2[16],
                      'added'=>time(),
					  'league_id'=>LEAGUE_ID
                      );
					  
        $db->insert_array( 'league_nhl_teamstats', $teamstats_data );
}
fclose($handle2);

$gs_results = $db->query( "SELECT * FROM league_nhl_gamestats WHERE id = '" . $game_stat_nr1 . "' AND league_id = " . LEAGUE_ID ); 
while( $gs_result = $db->fetch( $gs_results ) )
{
	if( $gs_result['period'] == 'Shootout' ){
		$ot = 'OT';
	}
	if( $gs_result['period'] == '1OT' && $gs_result['period'] != '3' && $gs_result['period'] != 'Shootout' ){
		$ot = 'OT';
	}
	if( $gs_result['period'] >= 1 && $gs_result['period'] != '1OT' && $gs_result['period'] != 'Shootout' ){
		$ot = 'Normal';
	}
}

// Home teamstats
$teamstats_home = $db->query( "SELECT * FROM league_nhl_teamstats WHERE id = '" . $team_stat_nr1 . "' && host = 'Home' AND league_id = " . LEAGUE_ID );
$ts_home = $db->fetch( $teamstats_home );
$rez1 = $ts_home['goal'];
$hosts1 = $ts_home['host'];
// Away teamstats
$teamstats_away = $db->query( "SELECT * FROM league_nhl_teamstats WHERE id = '" . $team_stat_nr1 . "' && host = 'Away' AND league_id = " . LEAGUE_ID );
$ts_away = $db->fetch( $teamstats_away );
$rez2 = $ts_away['goal'];
$hosts2 = $ts_away['host'];

if( $rez1 > $rez2 && $ot == 'Normal' )
{
	$w1 = 1;
	$l2 = 1;
	$l1 = 0;
	$w2 = 0;
	$ot1 = 0;
	$ot2 = 0;
	$pt1 = 2;
	$pt2 = 0; 
}

if( $rez1 < $rez2 && $ot == 'Normal' )
{
	$w2 = 1;
	$w1 = 0;
	$l1 = 1;
	$l2 = 0;
	$ot1 = 0;
	$ot2 = 0; 
	$pt1 = 0;
	$pt2 = 2; 
}

if( $rez1 < $rez2 && $ot == 'OT' )
{
	$w2 = 1;
	$w1 = 0;
	$l1 = 0;
	$l2 = 0;
	$ot1 = 1;
	$ot2 = 0; 
	$pt1 = 1;
	$pt2 = 2; 
}

if( $rez1 > $rez2 && $ot == 'OT' )
{
	$w2 = 0;
	$w1 = 1;
	$l1 = 0;
	$l2 = 0;
	$ot1 = 0;
	$ot2 = 1; 
	$pt1 = 2;
	$pt2 = 1; 
}

if(	$hosts1 == 'Home' && $ot != 'OT' && $rez1 > $rez2 )
{
	$hw = 1;
	$hl = 0;
	$hot = 0;
	$aw = 0;
	$al = 1;
	$aot = 0;
}

if(	$hosts1 == 'Home' && $ot == 'OT' && $rez1 > $rez2 )
{
	$hw = 1;
	$hl = 0;
	$hot = 0;
	$aw = 0;
	$al = 0;
	$aot = 1;
}

if( $hosts1 == 'Home' && $ot != 'OT' && $rez1 < $rez2 )
{
	$hw = 0;
	$hl = 1;
	$hot = 0;
	$aw = 1;
	$al = 0;
	$aot = 0;
}

if( $hosts1 == 'Home' && $ot == 'OT' && $rez1 < $rez2 )
{
	$hw = 0;
	$hl = 0;
	$hot = 1;
	$aw = 1;
	$al = 0;
	$aot = 0;
}

if( $hosts1 != 'Home' && $ot != 'OT' && $rez1 < $rez2 )
{
	$hw = 0;
	$hl = 1;
	$hot = 0;
	$aw = 1;
	$al = 0;
	$aot = 0;
}

if( $hosts1 != 'Home' && $ot == 'OT' && $rez1 < $rez2 )
{
	$hw = 0;
	$hl = 0;
	$hot = 1;
	$aw = 1;
	$al = 0;
	$aot = 0;
}

if( $hosts1 != 'Home' && $ot != 'OT' && $rez1 > $rez2 )
{
	$hw = 1;
	$hl = 0;
	$hot = 0;
	$aw = 0;
	$al = 1;
	$aot = 0;
}

if( $hosts1 != 'Home' && $ot == 'OT' && $rez1 > $rez2 )
{
	$hw = 1;
	$hl = 0;
	$hot = 0;
	$aw = 0;
	$al = 0;
	$aot = 1;
}

// Komanda ATL => WPG, home game
if( $ts_home['team'] == 'ATL' )
{
	$home_team = 'WPG';
}
else
{
	$home_team = $ts_home['team'];
}

// Komanda ATL => WPG, away game
if( $ts_away['team'] == 'ATL' )
{
	$away_team = 'WPG';
}
else
{
	$away_team = $ts_away['team'];
}

// Calendar home
$calendar_q_home = $db->query( "SELECT * FROM league_nhl_calendar WHERE played = '0' AND team1 = '" . $home_team . "' AND team2 = '" . $away_team . "' AND league_id = " . LEAGUE_ID );
$calendar_row_home = $db->fetch( $calendar_q_home );
// Calendar away
$calendar_q_away = $db->query( "SELECT * FROM league_nhl_calendar WHERE played = '0' AND team1 = '" . $away_team . "' AND team2 = '" . $home_team . "' AND league_id = " . LEAGUE_ID );
$calendar_row_away = $db->fetch( $calendar_q_away );

// Update calendar start
if(	$hosts1 == 'Home' && $ot != 'OT' )
{
	$db->query( "UPDATE league_nhl_calendar SET played = '1' WHERE id = '" . $calendar_row_home['id'] . "' AND team1 = '" . $home_team . "' AND team2 = '" . $away_team . "' AND league_id = " . LEAGUE_ID );
}
if(	$hosts1 == 'Home' && $ot == 'OT' )
{
	$db->query( "UPDATE league_nhl_calendar SET played = '1' WHERE id = '" . $calendar_row_home['id'] . "' AND team1 = '" . $home_team . "' AND team2 = '" . $away_team . "' AND league_id = " . LEAGUE_ID );
}

if(	$hosts2 == 'Away' && $ot != 'OT' )
{
	$db->query( "UPDATE league_nhl_calendar SET played = '1' WHERE id = '" . $calendar_row_away['id'] . "' AND team1 = '" . $away_team . "' AND team2 = '" . $home_team . "' AND league_id = " . LEAGUE_ID );
}
if(	$hosts2 == 'Away' && $ot == 'OT' )
{
	$db->query( "UPDATE league_nhl_calendar SET played = '1' WHERE id = '" . $calendar_row_away['id'] . "' AND team1 = '" . $away_team . "' AND team2 = '" . $home_team . "' AND league_id = " . LEAGUE_ID );
}
// Update calendar end

$tourney_game = $db->query( "SELECT * FROM league_nhl_games WHERE league_id = '" . LEAGUE_ID . "' ORDER BY `id` DESC" );
$tourney_game_id = $db->fetch( $tourney_game );
$tourney_game_nr = $tourney_game_id['id'];

if( $tourney_game_nr == '' )
{
	$tourney_game_nr1 = 1;
}
else
{
	$tourney_game_nr1 = $tourney_game_nr+1;
}

// Treneris 1
$trener_home = $db->query( "SELECT * FROM league_nhl_standings WHERE team_small = '" . $home_team . "' AND league_id = " . LEAGUE_ID );
$trener1 = $db->fetch( $trener_home );
// Treneris 2
$trener_away = $db->query( "SELECT * FROM league_nhl_standings WHERE team_small = '" . $away_team . "' AND league_id = " . LEAGUE_ID );
$trener2 = $db->fetch( $trener_away );

// Insert tourney game
$tourney_game_data = array(
              'id'=>$tourney_game_nr1,
			  'km1'=>$home_team,
			  'km1_rez'=>$ts_home['goal'],
			  'km2'=>$away_team,
			  'km2_rez'=>$ts_away['goal'],
			  'treneris1'=>$trener1['player_id'],
			  'treneris2'=>$trener2['player_id'],
			  'date'=>time(),
              'komentars1'=>get_post('game_comment'),
			  'league_id'=>LEAGUE_ID
             );
					  
$db->insert_array( 'league_nhl_games', $tourney_game_data );

// Update standings 
$db->query( "UPDATE league_nhl_standings SET gp=gp+1, w=w+'".$w1."', l=l+'".$l1."', ot=ot+'".$ot1."', pts=pts+'".$pt1."', gf=gf+'".$ts_home['goal']."', ga=ga+'".$ts_away['goal']."', hw=hw+'".$hw."', hl=hl+'".$hl."', hot=hot+'".$hot."' WHERE team_small = '" . $home_team . "' AND league_id = " . LEAGUE_ID );
$db->query( "UPDATE league_nhl_standings SET gp=gp+1, w=w+'".$w2."', l=l+'".$l2."', ot=ot+'".$ot2."', pts=pts+'".$pt2."', gf=gf+'".$ts_away['goal']."', ga=ga+'".$ts_home['goal']."', aw=aw+'".$aw."', al=al+'".$al."', aot=aot+'".$aot."' WHERE team_small = '" . $away_team . "' AND league_id = " . LEAGUE_ID );

// Update team streak start
if(	$hosts1 == 'Home' && $ot != 'OT' && $rez1 > $rez2 )
{
	$db->query( "UPDATE league_nhl_standings SET streak_lost = 0 WHERE team_small = '" . $home_team . "' AND league_id = " . LEAGUE_ID );
	$db->query( "UPDATE league_nhl_standings SET streak_won=streak_won+1 WHERE team_small = '" . $home_team . "' AND league_id = " . LEAGUE_ID );
}
if(	$hosts1 == 'Home' && $ot == 'OT' && $rez1 > $rez2 )
{
	$db->query( "UPDATE league_nhl_standings SET streak_lost = 0 WHERE team_small = '" . $home_team . "' AND league_id = " . LEAGUE_ID );
	$db->query( "UPDATE league_nhl_standings SET streak_won=streak_won+1 WHERE team_small = '" . $home_team . "' AND league_id = " . LEAGUE_ID );
}
if( $hosts1 == 'Home' && $ot != 'OT' && $rez1 < $rez2 )
{
	$db->query( "UPDATE league_nhl_standings SET streak_won = 0 WHERE team_small = '" . $home_team . "' AND league_id = " . LEAGUE_ID );
	$db->query( "UPDATE league_nhl_standings SET streak_lost=streak_lost+1 WHERE team_small = '" . $home_team . "' AND league_id = " . LEAGUE_ID );
}
if( $hosts1 == 'Home' && $ot == 'OT' && $rez1 < $rez2 )
{
	$db->query( "UPDATE league_nhl_standings SET streak_won = 0 WHERE team_small = '" . $home_team . "' AND league_id = " . LEAGUE_ID );
	$db->query( "UPDATE league_nhl_standings SET streak_lost=streak_lost+1 WHERE team_small = '" . $home_team . "' AND league_id = " . LEAGUE_ID );
}

if(	$hosts2 == 'Away' && $ot != 'OT' && $rez1 > $rez2 )
{
	$db->query( "UPDATE league_nhl_standings SET streak_won = 0 WHERE team_small = '" . $away_team . "' AND league_id = " . LEAGUE_ID );
	$db->query( "UPDATE league_nhl_standings SET streak_lost=streak_lost+1 WHERE team_small = '" . $away_team . "' AND league_id = " . LEAGUE_ID );
}
if(	$hosts2 == 'Away' && $ot == 'OT' && $rez1 > $rez2 )
{
	$db->query( "UPDATE league_nhl_standings SET streak_won = 0 WHERE team_small = '" . $away_team . "' AND league_id = " . LEAGUE_ID );
	$db->query( "UPDATE league_nhl_standings SET streak_lost=streak_lost+1 WHERE team_small = '" . $away_team . "' AND league_id = " . LEAGUE_ID );
}
if( $hosts2 == 'Away' && $ot != 'OT' && $rez1 < $rez2 )
{
	$db->query( "UPDATE league_nhl_standings SET streak_lost = 0 WHERE team_small = '" . $away_team . "' AND league_id = " . LEAGUE_ID );
	$db->query( "UPDATE league_nhl_standings SET streak_won=streak_won+1 WHERE team_small = '" . $away_team . "' AND league_id = " . LEAGUE_ID );
}
if( $hosts2 == 'Away' && $ot == 'OT' && $rez1 < $rez2 )
{
	$db->query( "UPDATE league_nhl_standings SET streak_lost = 0 WHERE team_small = '" . $away_team . "' AND league_id = " . LEAGUE_ID );
	$db->query( "UPDATE league_nhl_standings SET streak_won=streak_won+1 WHERE team_small = '" . $away_team . "' AND league_id = " . LEAGUE_ID );
}
// Update team streak end

// Update nhl statistics
update_tourney_statistics_rank();

echo success( 'Rezultāts veiksmīgi pievienots!', '473px' );
/*** End uploading game stats ***/
}
}

if( $player['tourney_warns'] >= 999 )
{
	echo error( 'Tu esi saņēmis jau trīs brīdinājumus un tev ir liegta iespēja pievienot rezultātus!<br>
				Ja gribi dzēst visus brīdinājumus, tad <a href="javascript:;" onclick="if (confirm(\'Esi drošs, ka vēlies dzēst visus turnīra brīdinājumus?\')) document.location.href=\'' . BASE . '/tourney/' . TOURNEY_ID . '/profile/del-warns/\' ">Spied šeit!</a>', '473px' );
}
else
{
echo info( 'GPC status nepieciešams augšuplādēt uzreiz pēc spēles beigām!<br />
<i>GPC status augšuplādē tikai spēles uzvarētājs!</i><br />
Visi lauciņi ir jaizspilda obligāti un pareizi, pretējā gadijumā jūs saņemsiet vienu turnīra brīdinājumu!', '473px' );
?>
<form enctype="multipart/form-data" method="post" name="check_stats">
	<table class="ipbtable" cellspacing="1" style="width: 530px;">
		<tr><td align="right" style="width: 80px;" class="row2">gamestats:</td><td class="row1"><input name="gamestats" style="width: 221px;" title="gamestats" alt="gamestats" type="file"></td></tr>
		<tr><td align="right" class="row2">playerstats:</td><td class="row1"><input name="playerstats" style="width: 221px;" title="playerstats" alt="playerstats" type="file"></td></tr>
		<tr><td align="right" class="row2">teamstats:</td><td class="row1"><input name="teamstats" style="width: 221px;" title="teamstats" alt="teamstats" type="file"></td></tr>
		<tr><td align="right" class="row2">Komentārs:</td><td class="row1"><textarea name="game_comment" style="width: 419px; height: 80px;"></textarea></td></tr>
		<tr><td class="row3"></td><td class="row3"><input type="submit" name="upload_stats" value="Augšuplādēt spēles status" onclick="return check_upload_stats();"></td></tr>
	</table>
</form>
<?php 
}
?>
</fieldset>

<br />

<fieldset class='stats_upload'><legend class='stats_upload2'><b>PĀRBAUDI SPĒLI</b></legend>
<?php
if( get_post('test_game') )
{
	$post_team = get_post('test_team');
	$test_speles_jaspele = $db->rows($db->query( "SELECT * FROM league_nhl_calendar WHERE played = '0' AND team1 = '" . $row['team_small'] . "' AND team2 = '" . $post_team . "'  AND league_id = " . LEAGUE_ID ));
	if($test_speles_jaspele > 0){
		echo success( "Pret <i>" . $nhl_team_values[$post_team]. "</i> komandu tev vēl ir jaizspēle " . format_number($test_speles_jaspele) . " " . (substr($test_speles_jaspele, -1) == 1 ? "spēle":"spēles") . "!", "473px" );
	}
	else
	{
		echo error( "Pret <i>" . $nhl_team_values[$post_team] . "</i> komandu vairs nav spēles!", "473px" );
	}
}
?>
<form method="post">
Komanda: <select name="test_team">
	<option value="0"> - - - IZVĒLIES KOMANDU - - -</option>
		<?php
		$choose_team = $db->query( "SELECT * FROM league_nhl_standings WHERE league_id = " . LEAGUE_ID );
		while( $choose_test_team = $db->fetch( $choose_team ) )
		{
			if( $choose_test_team['player_id'] != get_cookie('user_id') )
			{
			?>
			   <option value="<?php echo $choose_test_team['team_small']; ?>"><?php echo $choose_test_team['team_full']; ?></option>
			<?php
		}
	}
?>
</select>
<input type="submit" name="test_game" value="Pārbaudīt"></input>
</form>
</fieldset>
</div>

<div style="float:left; width:150px;">
<fieldset class='myteam'><legend class='myteam2'><b>ATLIKUŠĀS SPĒLES</b></legend>
<?php
$calendar_rows = $db->query( "SELECT * FROM league_nhl_calendar WHERE team1 = '" . $row['team_small'] . "' AND played = '0' AND league_id = '" . LEAGUE_ID . "'" );
$nr = 1;
while( $calendar = $db->fetch($calendar_rows) )
{
	$team_user = $db->query( "SELECT * FROM league_nhl_standings WHERE team_small = '" . $calendar['team2'] . "' AND league_id = '" . LEAGUE_ID . "'" );
	$team_owner = $db->fetch( $team_user );
	$komandas_ipasnieks = $users->info($team_owner['player_id']);
	$team_krasa = $team_owner['player_id'] == 0 ? "colorless":"colorful";
	$url_sakums = $team_owner['player_id'] == 0 ? "":"<a href=skype:" . $komandas_ipasnieks['skype'] . "?chat>";
	$url_beigas = $team_owner['player_id'] == 0 ? "":"</a>";
	$team_action = $team_owner['player_id'] == 0 ? "" . $team_owner['team_full'] . " :: Komanda ir brīva!":"" . $team_owner['team_full'] . " :: " . $komandas_ipasnieks['username'] . "";
		echo '
		' . $url_sakums . '<img alt="' . $team_action . '" title="' . $team_action . '" style="padding-left:12px;" src="' . BASE . '/style/images/teams/medium/' . $team_krasa . '/' . $calendar['team2'] . '.png">' . $url_beigas . '
		';
  $nr++;
}
if($nr == 1) 
echo 'Apsveicu, Tu esi izspēlējis visas regulārās sezonas spēles!';
?>
</fieldset>
</div>
</div>
<?php
}
}
else
{
	echo error( "Atvainojiet, bet jūs šajā turnīrā nepiedalieties!", "898px" );

}
}
else
{
	echo error( "Lai apskatītu šo lapu nepieciešams <a href='" . BASE . "/registration/'>reģistrēties</a> vai <a href='" . BASE . "/login/'>ielogoties</a>.", "898px" );
}
?>