<?php
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
$res = $db->query( "SELECT * FROM league_nhl_standings WHERE player_id = '" . get_cookie('user_id') . "' AND league_id = " . LEAGUE_ID );
$row = $db->fetch( $res );

$team_user = $users->info($row1['player_id']);
?>
<div style="float:left; width:950px; padding-top:5px;">
<div style="float:left; width:200px;">

<fieldset class='myteam'><legend class='myteam2'><b>LĪGAS PLAYOFF KOMANDA</b></legend>
<center>
	<img src="<?php echo BASE ?>/style/images/teams/medium/colorful/<?php echo $row['team_small']; ?>.png"><br>
		<?php echo $row['team_full']; ?>
	<br />
	<span style="color:<?php echo $warn_color; ?>;">
		Līgas brīdinājumi: <b><?php echo $player['tourney_warns']; ?></b>
	</span>
</center>
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
$game_stat = $db->query( "SELECT * FROM league_nhl_gamestats ORDER BY `id` DESC" );
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
$player_stat = $db->query( "SELECT * FROM league_nhl_playerstats ORDER BY `id` DESC" );
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
$query = $db->query( "SELECT * FROM `league_nhl_po_statistics` WHERE `player_name` = '" . mysql_real_escape_string($data1[0]) . "' AND league_id = " . LEAGUE_ID );

if( $db->rows( $query ) )
{
	$db->query( "UPDATE league_nhl_po_statistics SET gp=gp+'" . $game_played . "', time=time+'" . $player_time . "', goal=goal+'" . $data1[4] . "', asst=asst+'" . $data1[5] . "', pims=pims+'" . $data1[6] . "', plus_minus=plus_minus+'" . $data1[7] . "', ppg_ga=ppg_ga+'" . $data1[8] . "', shg_eng=shg_eng+'" . $data1[9] . "', shot_sa=shot_sa+'" . $data1[10] . "', hits=hits+'" . $data1[11] . "' WHERE player_name = '" . mysql_real_escape_string($data1[0]) . "' AND league_id = " . LEAGUE_ID );
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
					  
       $db->insert_array( 'league_nhl_po_statistics', $insertstats_data );
}
}
fclose($handle1);

// teamstats
$team_stat = $db->query( "SELECT * FROM league_nhl_teamstats ORDER BY `id` DESC" );
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

$gs_results = $db->query( "SELECT * FROM league_nhl_gamestats WHERE id = '" . $game_stat_nr1 . "'" ); 
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
$teamstats_home = $db->query( "SELECT * FROM league_nhl_teamstats WHERE id = '" . $team_stat_nr1 . "' && host = 'Home'" );
$ts_home = $db->fetch( $teamstats_home );
$rez1 = $ts_home['goal'];
$hosts1 = $ts_home['host'];
// Away teamstats
$teamstats_away = $db->query( "SELECT * FROM league_nhl_teamstats WHERE id = '" . $team_stat_nr1 . "' && host = 'Away'" );
$ts_away = $db->fetch( $teamstats_away );
$rez2 = $ts_away['goal'];
$hosts2 = $ts_away['host'];

if( $rez1 > $rez2 && $ot == 'Normal' )
{
	$w1 = 1;
	$w2 = 0;
}

if( $rez1 < $rez2 && $ot == 'Normal' )
{
	$w2 = 1;
	$w1 = 0;
}

if( $rez1 < $rez2 && $ot == 'OT' )
{
	$w2 = 1;
	$w1 = 0;
}

if( $rez1 > $rez2 && $ot == 'OT' )
{
	$w2 = 0;
	$w1 = 1;
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

$tourney_game = $db->query( "SELECT * FROM league_nhl_playerstats ORDER BY `id` DESC" );
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
$trener_home = $db->query( "SELECT * FROM league_nhl_standings WHERE team_small = '" . $home_team . "' AND league_id = '" . LEAGUE_ID . "'" );
$trener1 = $db->fetch( $trener_home );
// Treneris 2
$trener_away = $db->query( "SELECT * FROM league_nhl_standings WHERE team_small = '" . $away_team . "' AND league_id = '" . LEAGUE_ID . "'" );
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
					  
$db->insert_array( 'league_nhl_po_games', $tourney_game_data );

// Update po standings
$db->query( "UPDATE league_nhl_po SET serie=serie+'".$w1."', hw=hw+'".$hw."', hl=hl+'".$hl."', hot=hot+'".$hot."' WHERE team = '" . $home_team . "' AND league_id = " . LEAGUE_ID );
$db->query( "UPDATE league_nhl_po SET serie=serie+'".$w2."', aw=aw+'".$aw."', al=al+'".$al."', aot=aot+'".$aot."' WHERE team = '" . $away_team . "' AND league_id = " . LEAGUE_ID );

update_tourney_statistics_playoff_rank();

echo success( 'Rezultāts veiksmīgi pievienots!', '473px' );
/*** End uploading game stats ***/
}
}

if( $player['tourney_warns'] >= 3 )
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
</div>

<div style="float:left; width:150px;">
<fieldset class='myteam'><legend class='myteam2'><b>PLAYOFF INFORMĀCIJA</b></legend>
<center>
BROKEN!
</center>
</fieldset>
</div>
</div>