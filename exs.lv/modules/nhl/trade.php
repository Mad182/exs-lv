<?php 
$page->set_page_title( 'NHL turnīrs #' . $row['tourney_id'] . ' :: Regular Season' );
if( get_get('id') == 'accept_trade' AND FLAG_t )
{
	$db->query( "UPDATE nhl_trade SET trade_type = 'accepted'  WHERE id = '" . get_get('other') . "' AND tourney_id = '" . TOURNEY_ID . "'") or die(mysql_error());
	header('location: /tourney/' . TOURNEY_ID . '/');
}
if( get_get('id') == 'reject_trade' AND FLAG_t )
{
	$db->query( "UPDATE nhl_trade SET trade_type = 'rejected'  WHERE id = '" . get_get('other') . "' AND tourney_id = '" . TOURNEY_ID . "'") or die(mysql_error());
	header('location: /tourney/' . TOURNEY_ID . '/');
}
if( get_get('id') == 'start_tourney' AND FLAG_a )
{
	$db->query( "UPDATE tourneys SET tourney_signup = 0, tourney_status = 'open'  WHERE tourney_id = '" . TOURNEY_ID . "'") or die(mysql_error());
	header('location: /tourney/' . TOURNEY_ID . '/');
}
?>
<h1><a href="<?php echo BASE ?>/tourneys/">Turnīri</a> <img src="<?php echo BASE ?>/style/images/list.gif"> NHL #<?php echo $row['tourney_id']; ?> :: Trade</h1>
<fieldset class='orginal' style="width:923px;"><legend class='orginal2'><b>TRADE PLAYERS</b></legend>
<?php 
if( IS_USER ) 
{
$team = $db->fetch($db->query("SELECT * FROM nhl_standings WHERE player_id = '" . get_cookie('user_id') . "'"));
if($team['team_id'])
{
?>
<?php
$choose_team = $db->query( "SELECT * FROM nhl_standings WHERE tourney_id = " . TOURNEY_ID );
while( $choose_test_team = $db->fetch( $choose_team ) )
{
	if( $choose_test_team['player_id'] == get_cookie('user_id') )
	{
		?>
		<img src="/style/images/teams/small/colorful/<?php echo $choose_test_team['team_small']; ?>.png"> <?php echo $choose_test_team['team_full']; ?>
		<?php
	}
}
?>
<br />
<b>Treids:</b><br/>
<?php 
if( get_post('add_trade') && IS_USER )
{
  if( get_post('trade') != '' )
   {
        $insert_data = array(
					  'trade'=>get_post('trade'),
					  'trade_type'=>'inactive',
                      'time'=>time(),
					  'user_id'=>get_cookie('user_id'),
					  'tourney_id'=>TOURNEY_ID
                      );
					  
        $db->insert_array( 'nhl_trade', $insert_data );
		echo '<font color="green">Tavs treids veiksmīgi aizsūtīts adminstrācijai!</font>';
   }
}
?>
<form method="post">
<input type="text" name="trade" style="width:280px;" value="">
<br/>
<small>
<b>Piemers:</b><br />
<font color="black">
Jarome Iginla(W)+Anton Babchuk(OD)+Mikael Backlund(C) vs Derek Roy(C) + Robyn Regehr(DD) +Jochen Heht(C)
</font>
</small>
<br />
<span style="float:right;">
<input class="login_button" type="submit" value="Treidot spēlētāju" name="add_trade">
</span>
</form>
<?php 
} 
else 
{ 
	echo error('Tu šajā turnīrā nepiedalies!', '870px');
}
} 
else 
{ 
	echo error('Lai treidotos, tev nepieciešams ielogoties!', '870px');
}
?>
</fieldset>
<?php if ( FLAG_t ) { ?>
<div style="float:left; width:950px; padding-top:10px;">
<fieldset class='orginal' style="width:923px;"><legend class='orginal2'><b>IESŪTĪTIE TREIDI (admin only)</b></legend>
<?php
$trade_a = $db->query( "SELECT * FROM nhl_trade WHERE trade_type = 'inactive' AND tourney_id = " . TOURNEY_ID . " ORDER BY id DESC" );
$i = 0;
while( $trade_admin = $db->fetch( $trade_a ) )
{
	$author = $users->info($trade_admin['user_id']);
	echo '
		<b>Treids:</b> <i><font color="black">' . $trade_admin['trade'] . '</font> no <a href="/user/' . $author['id'] . '/' . $author['username'] . '/">' . $author['username'] . '</a></i> <span style="float:right;">[ <a style="color:green;" href="javascript:;" onclick="if (confirm(\'Esi drošs, ka vēlies apstiprināt šo treidu?\')) document.location.href=\'/tourney/' . TOURNEY_ID . '/accept_trade/' . $trade_admin['id'] . '\' ">Apstiprināt</a> / <a style="color:red;" href="javascript:;" onclick="if (confirm(\'Esi drošs, ka vēlies noraidīt šo treidu?\')) document.location.href=\'/tourney/' . TOURNEY_ID . '/reject_trade/' . $trade_admin['id'] . '/\' ">Noraidīt</a> ]</span>
		<br>
		iesūtīts ' . format_time($trade_admin['time']) . '
		<div class="dashed_line"></div>
	';
	$i++;
}
if($i == 0)
echo 'Nav iesūtīts neviens jauns treids!';
?>
</fieldset>
</div>
<?php } ?>
<div style="float:left; width:950px; padding-top:10px;">
<div style="float:left; width:450px;">
<fieldset class='orginal' style="width:440px;"><legend class='orginal2'><b><font color="green">APSTIPRINĀTIE TREIDI</font></b></legend>
<?php
$trade_a1 = $db->query( "SELECT * FROM nhl_trade WHERE trade_type = 'accepted' AND tourney_id = " . TOURNEY_ID . " ORDER BY id DESC" );
$i = 0;
while( $trade_accept = $db->fetch( $trade_a1 ) )
{
	$author = $users->info($trade_accept['user_id']);
	echo '
		<b>Treids:</b> <i><font color="black">' . $trade_accept['trade'] . '</font> no <a href="/user/' . $author['id'] . '/' . $author['username'] . '/">' . $author['username'] . '</a></i>
		<div class="dashed_line"></div>
	';
	$i++;
}
if($i == 0)
echo 'Nav apstiprināts neviens treids!';
?>
</fieldset>
</div>
<div style="float:left; width:450px;padding-left:33px;">
<fieldset class='orginal' style="width:440px;"><legend class='orginal2'><b><font color="red">NORAIDĪTIE TREIDI</font></b></legend>
<?php
$trade_a2 = $db->query( "SELECT * FROM nhl_trade WHERE trade_type = 'rejected' AND tourney_id = " . TOURNEY_ID . " ORDER BY id DESC" );
$i = 0;
while( $trade_rej = $db->fetch( $trade_a2 ) )
{
	$author = $users->info($trade_rej['user_id']);
	echo '
		<b>Treids:</b> <i><font color="black">' . $trade_rej['trade'] . '</font> no <a href="/user/' . $author['id'] . '/' . $author['username'] . '/">' . $author['username'] . '</a></i>
		<div class="dashed_line"></div>
	';
	$i++;
}
if($i == 0)
echo 'Nav noraidīts neviens treids!';
?>
</fieldset>
</div>
</div>