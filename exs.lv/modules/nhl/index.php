<?php
$check_league_q = $db->query( "SELECT * FROM leagues WHERE league_id = " . LEAGUE_ID );
$check_league = $db->fetch( $check_league_q );
if( !$check_league['league_signup'] == 1 )
{
if( $check_league['league_status'] != 'trade' )
{
if( $check_league['playoff'] == 1 )
{
	echo page_title( 'NHL #' . $row['league_id'] . ' līga :: PlayOFF' );
	include ROOT . '/pages/league/NHL/playoff.php';
}
else
{
echo page_title( 'NHL #' . $row['league_id'] . ' līga :: Regular Season' );
$tourney_team = $db->fetch( $db->query( "SELECT * FROM league_nhl_standings WHERE player_id = '" . get_cookie('user_id') . "' AND league_id = " . LEAGUE_ID ) );
$kopa_jaizspele = $db->rows( $db->query( "SELECT * FROM league_nhl_calendar WHERE league_id = " . LEAGUE_ID ) )/2;
$izspeletas_speles = $db->rows( $db->query( "SELECT * FROM league_nhl_calendar WHERE played = '1' AND league_id = " . LEAGUE_ID ) )/2;
$atlikusas_speles = $kopa_jaizspele - $izspeletas_speles;
$izspeleti_procenti = '';
if($izspeleti_procenti == 100)
{
	$izspeleti_procenti = 100;
}
else
{
	$izspeleti_procenti = round($izspeletas_speles / $kopa_jaizspele * 100, 1);
}
?>
<h1>
<a href="<?php echo BASE ?>/leagues/">Līgas</a> <img src="<?php echo BASE ?>/style/images/list.gif"> NHL #<?php echo $row['league_id']; ?> līga :: Regular Season
<span style="float:right;">
<div id="tourney_time">
Turnīrs ilgst <?php echo tourney_time( $check_league['league_start_time'] ); ?>
</div>
</span>
</h1>
<div id="turnirs_bg">
	<div id="turnirs">
		<a class="first" href="/league/<?php echo $row['league_id']; ?>/"> Konferenču tabulas</a>
		<a href="/league/<?php echo $row['league_id']; ?>/division/"> Divīziju tabulas</a>
		<a href="/league/<?php echo $row['league_id']; ?>/league/"> Līgas tabula</a>
		<a href="/league/<?php echo $row['league_id']; ?>/statistics/"> Statistika</a>
		<a href="/league/<?php echo $row['league_id']; ?>/teams/"> Līgas komandas</a>
		<?php
		if( IS_USER && get_cookie('user_id') == $tourney_team['player_id'] )
		{
		?>
		<a href="/league/<?php echo $row['league_id']; ?>/profile/" style="color:red;"> Līgas profils</a>
		<a href="/league/<?php echo $row['league_id']; ?>/calendar/"> <b><?php echo $tourney_team['team_full']; ?></b> - <i>spēļu kalendārs</i></a>
		<?php
		}
		?>
	</div>
</div>
<div style="height: 4px;"></div>
<?php
if( get_get('id') != 'calendar' AND get_get('id') != 'statistics' AND get_get('id') != 'teams' AND get_get('id') != 'team' AND get_get('id') != 'profile' )
{
?>
<font color="black">
Kopā jaizspēlē <b><?php echo $kopa_jaizspele; ?></b> spēles, <?php echo (substr($izspeletas_speles, -1) == 1 ? "izpēlēta":"izpēlētas") ?> <b><?php echo $izspeletas_speles; ?></b> <?php echo (substr($izspeletas_speles, -1) == 1 ? "spēle":"spēles") ?>, <?php echo (substr($atlikusas_speles, -1) == 1 ? "atlikusi":"atlikušas") ?> vēl <b><?php echo $atlikusas_speles; ?></b> <?php echo (substr($atlikusas_speles, -1) == 1 ? "spēle":"spēles") ?>, kas ir <b><?php echo $izspeleti_procenti; ?>%</b> <?php echo (substr($izspeleti_procenti, -1) == 1 ? "procents":"procenti") ?> no kopējā spēļu skaita!
<br>
<div style='height: 2px;'></div>
Ja komandām ir <strong>vienāds punktu skaits</strong>, komandas vieta tiek noteikta sekojošā kārtībā:</p>
<ol>
	<li>Pēc <strong>pamatlaikā</strong> uzvarētajām spēlēm;</li>
	<li>Pēc nospēlētajām spēlēm (jo mazāk, jo labāk);</li>
	<li>Pēc gūto un ielaisto vārtu starpības.</li>
</ol>
</font>
<?php
}
if( get_get('id') == 'league' )
{
	include ROOT . '/pages/league/NHL/league.php';
}
elseif( get_get('id') == 'division' )
{
	include ROOT . '/pages/league/NHL/division.php';
}
elseif( get_get('id') == 'statistics' )
{
	include ROOT . '/pages/league/NHL/statistics.php';
}
elseif( get_get('id') == 'teams' )
{
	include ROOT . '/pages/league/NHL/teams.php';
}
elseif( get_get('id') == 'profile' )
{
	include ROOT . '/pages/league/NHL/profile.php';
}
elseif( get_get('id') == 'calendar' )
{
	include ROOT . '/pages/league/NHL/calendar.php';
}
elseif( get_get('id') == 'team' )
{
	include ROOT . '/pages/league/NHL/team.php';
}
else
{
	include ROOT . '/pages/league/NHL/conforence.php';
}
}
}
else
{
	echo page_title( 'NHL #' . $row['league_id'] . ' līga :: Trade' );
	include ROOT . '/pages/league/NHL/trade.php';
}
}
else
{
	echo page_title( 'NHL #' . $row['league_id'] . ' līga :: Pieteikšanās' );
	include ROOT . '/pages/league/NHL/signup.php';
}