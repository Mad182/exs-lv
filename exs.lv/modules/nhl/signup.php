<?php
/*if( get_get('id') == 'start_tourney' AND FLAG_a )
{
	$db->query( "UPDATE tourneys SET tourney_signup = 0, tourney_start_time = '".time()."'  WHERE tourney_id = '" . TOURNEY_ID . "'") or die(mysql_error());
	header('location: /tourney/' . TOURNEY_ID . '/');
}*/

$standing = $nhldb->get_row("SELECT * FROM league_nhl_standings WHERE player_id = '" . $auth->id . "' AND league_id = " . $league_id );

$buyer = $nhldb->get_row("SELECT * FROM league_nhl_signup_buy WHERE user_id = '" . $auth->id . "' AND league_id = " . $league_id );
?>
<div style="float:left; width:950px;">
<h1><?php echo $league->league_title; ?> :: Pieteikšanās</h1>
<?php
if($auth->ok)
{
if( $auth->id == $standing->player_id)
{
	echo 'Tava izvēlētā komanda: <font color="black">' . $standing->team_full . '</font>, gaidi līgās sākumu!';
}
else
{
	if(true or $auth->id == $buyer->user_id)
	{
		if($_POST['choose_team'])
		{
			$db->query( "UPDATE league_nhl_standings SET player_id = '" . $buyer->user_id . "' WHERE team_small = '" . get_post('team') . "' AND league_id = '" . $league_id . "'" );
			echo '<script>window.location="/league/' . $league_id . '/";</script>';
		}
		?>
		<div style="width:950px;">
			<form method="post">
			<center>
			<select name="team">
			<option value="0"> - - - IZVĒLIES KOMANDU - - -</option>
			<?php
			$choose_team_q = $db->query( "SELECT * FROM league_nhl_standings WHERE league_id = " . $league_id );
			while( $choose_team = $db->fetch( $choose_team_q ) )
			{
				if( $choose_team['player_id'] == 0 )
				{
				?>
					<option value="<?php echo $choose_team['team_small']; ?>"><?php echo $choose_team['team_full']; ?></option>
				<?php
				}
			}
			?>
			</select><div style="height:10px;"></div>
			<input class="login_button" type="submit" name="choose_team" value="Paņemt komandu"></input>
			</center>
			</form>
		</div>
		<?php
	}
	else
	{
		include ROOT . '/suncore/nhl-signup/index.php';
	}
}
}
else
{
	echo error( "Lai pieteiktos šajā līgā jums ir jaielogojas savā profilā.", "898px" );
}
?>
</div>
