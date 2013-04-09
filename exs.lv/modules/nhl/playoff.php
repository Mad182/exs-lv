<?php
$tourney_team_playoff = $db->fetch( $db->query( "SELECT * FROM league_nhl_standings WHERE player_id = '" . get_cookie('user_id') . "' AND league_id = " . LEAGUE_ID ) );
?>
<h1>
<a href="<?php echo BASE ?>/leagues/">Līgas</a> <img src="<?php echo BASE ?>/style/images/list.gif"> NHL #<?php echo $row['league_id']; ?> līga :: PlayOFF
<span style="float:right;">
Katra kārta iet līdz <font color='#6E6E6E'>4</font> uzvarām
</span>
</h1>
<div id="turnirs_bg">
	<div id="turnirs">
		<a class="first" href="/league/<?php echo $row['league_id']; ?>/"> PlayOFF</a>
		<a href="/league/<?php echo $row['league_id']; ?>/statistics/"> PlayOFF statistika</a>
		<a href="/league/<?php echo $row['league_id']; ?>/regular-season/"> Konferenču tabulas</a>
		<a href="/league/<?php echo $row['league_id']; ?>/division/"> Divīziju tabulas</a>
		<a href="/league/<?php echo $row['league_id']; ?>/league/"> Līgas tabula</a>
		<a href="/league/<?php echo $row['league_id']; ?>/regular-season-statistics/"> Regulārās sezonas statistika</a>
		<a href="/league/<?php echo $row['league_id']; ?>/teams/"> Līgas Komandas</a>
		<?php
		if( IS_USER && get_cookie('user_id') == $tourney_team_playoff['player_id'] )
		{
		?>
		<a href="/league/<?php echo $row['league_id']; ?>/profile/" style="color:red;"> Līgas profils</a>
		<?php
		}
		?>
	</div>
</div>
<div style="height: 4px;"></div>
<?php
if( get_get('id') == 'league' )
{
	include ROOT . '/pages/league/NHL/playoff/league.php';
}
elseif( get_get('id') == 'regular-season' )
{
	include ROOT . '/pages/league/NHL/playoff/conforence.php';
}
elseif( get_get('id') == 'division' )
{
	include ROOT . '/pages/league/NHL/playoff/division.php';
}
elseif( get_get('id') == 'regular-season-statistics' )
{
	include ROOT . '/pages/league/NHL/playoff/regular-season-statistics.php';
}
elseif( get_get('id') == 'statistics' )
{
	include ROOT . '/pages/league/NHL/playoff/playoff-statistics.php';
}
elseif( get_get('id') == 'teams' )
{
	include ROOT . '/pages/league/NHL/teams.php';
}
elseif( get_get('id') == 'profile' )
{
	include ROOT . '/pages/league/NHL/playoff/profile.php';
}
elseif( get_get('id') == 'team' )
{
	include ROOT . '/pages/league/NHL/team.php';
}
else
{
	include ROOT . '/pages/league/NHL/playoff/index.php';
}