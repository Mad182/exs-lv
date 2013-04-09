<?php
$check_league_q_p = $db->query( "SELECT * FROM leagues WHERE league_id = " . LEAGUE_ID );
$check_league_p = $db->fetch( $check_league_q_p );
$res = $db->query( "SELECT * FROM league_nhl_standings WHERE player_id = '" . get_cookie('user_id') . "' AND league_id = " . LEAGUE_ID );
$row = $db->fetch( $res );
//playoff 1/8
$po_q_18 = $db->query( "SELECT * FROM league_nhl_po WHERE team = '" . $row['team_small'] . "' AND karta = '" . mysql_real_escape_string('1/8') . "' AND league_id = " . LEAGUE_ID );
$po_18 = $db->fetch( $po_q_18 );
//playoff 1/4
$po_q_14 = $db->query( "SELECT * FROM league_nhl_po WHERE team = '" . $row['team_small'] . "' AND karta = '" . mysql_real_escape_string('1/4') . "' AND league_id = " . LEAGUE_ID );
$po_14 = $db->fetch( $po_q_14 );
//playoff 1/2
$po_q_12 = $db->query( "SELECT * FROM league_nhl_po WHERE team = '" . $row['team_small'] . "' AND karta = '" . mysql_real_escape_string('1/2') . "' AND league_id = " . LEAGUE_ID );
$po_12 = $db->fetch( $po_q_12 );
//playoff final
$po_q_final = $db->query( "SELECT * FROM league_nhl_po WHERE team = '" . $row['team_small'] . "' AND karta = '" . mysql_real_escape_string('final') . "' AND league_id = " . LEAGUE_ID );
$po_final = $db->fetch( $po_q_final );
if( IS_USER )
{
if( get_cookie('user_id') == $row['player_id'] )
{

if( $check_league_p['playoff_type'] == '1/8' )
{
	if( $po_18 )
	{
		include ROOT . '/pages/league/NHL/playoff/profils/eightfinal.php';
	}
	else
	{
		echo error( "Diemžēl tu neesi ticis NHL <b>#" . LEAGUE_ID . "</b> līgas izslēgšanas spēlēs!<br />Paldies, ka piedalijies šajā līgā.", "898px" );

	}
}

if( $check_league_p['playoff_type'] == '1/4' )
{
	if( $po_14 )
	{
		include ROOT . '/pages/league/NHL/playoff/profils/fourfinal.php';
	}
	else
	{
		echo error( "Diemžēl tu neesi ticis NHL <b>#" . LEAGUE_ID . "</b> līgas ceturtdaļfinālā!<br />Paldies, ka piedalijies šajā līgā.", "898px" );
	}
}

if( $check_league_p['playoff_type'] == '1/2' )
{
	if( $po_12 )
	{
		include ROOT . '/pages/league/NHL/playoff/profils/twofinal.php';
	}
	else
	{
		echo error( "Diemžēl tu neesi ticis NHL <b>#" . LEAGUE_ID . "</b> līgas konferences finālā!<br />Paldies, ka piedalijies šajā līgā.", "898px" );
	}
}

if( $check_league_p['playoff_type'] == 'final' )
{
	if( $po_final )
	{
		include ROOT . '/pages/league/NHL/playoff/profils/final.php';
	}
	else
	{
		echo error( "Diemžēl tu neesi ticis NHL <b>#" . LEAGUE_ID . "</b> līgas finālā!<br />Paldies, ka piedalijies šajā līgā.", "898px" );
	}
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