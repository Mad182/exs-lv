<div style="float:left; width:950px;">
<?php
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
<font color="black">Kopā tika <?=(substr($izspeletas_speles, -1) == 1 ? "izpēlēta":"izpēlētas")?> <b><?=$izspeletas_speles;?></b> <?=(substr($izspeletas_speles, -1) == 1 ? "spēle":"spēles")?>, kopā bija jaizspēle <b><?=$kopa_jaizspele;?></b> spēles, kas ir <b><?=$izspeleti_procenti;?>%</b> <?=(substr($izspeleti_procenti, -1) == 1 ? "procents":"procenti")?> no kopējā spēļu skaita, <?=(substr($atlikusas_speles, -1) == 1 ? "atlikusi":"atlikušas")?> vēl bija <b><?=$atlikusas_speles;?></b> <?=(substr($atlikusas_speles, -1) == 1 ? "spēle":"spēles")?>.
<div style='height: 2px;'></div>
Ja komandām ir <strong>vienāds punktu skaits</strong>, komandas vieta tiek noteikta sekojošā kārtībā:</p>
<ol>
<li>Pēc <strong>pamatlaikā</strong> uzvarētajām spēlēm;</li>
<li>Pēc nospēlētajām spēlēm (jo mazāk, jo labāk);</li>
<li>Pēc gūto un ielaisto vārtu starpības.</li></ol>
</font>
	<div style="float:left; width:575px;">
		<img src="<?php echo BASE ?>/themes/<?php echo SITE_THEME ?>/images/league.gif" style="width: 464px;height:25px;" />
			<table class="west" cellspacing="0" cellpadding="1" style="width: 464px;">
				<tr align="center" style="color:#E2E4E3; font-weight:bold;" bgcolor="#6F7070">
					<td width="15">#</td>
					<td width="246"></td>
					<td width="27">GP</td>
					<td width="27">W</td>
					<td width="27">OTL</td>
					<td width="27">L</td>
					<td width="32">P</td>
					<td width="27">GF</td>
					<td width="27">GA</td>
					<td width="31">DIFF</td>
					<td width="50">STREAK</td>
				</tr>
<?php
$i = 1;
$num = 0;
$col = '';
$league_q = $db->query( "SELECT * FROM league_nhl_standings WHERE league_id = '" . LEAGUE_ID . "' ORDER BY pts DESC, gf - ga DESC, gp ASC" );
while( $league = $db->fetch( $league_q ) )
{
	$games_play_want = $league['gp'] < 30 ? " style='background: rgb(255, 204, 204);'" : "";
	if( IS_USER )
	$my_team = get_cookie('user_id') == $league['player_id'] ? " bgcolor=#C7E2C7" : "";
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
	$diffs = $league['gf']-$league['ga'];
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
	$player = $users->info($league['player_id']);
	$team_color = $league['player_id'] == 0 ? "colorless":"colorful";
	$team_situation = $league['player_id'] == 0 ? "" . $league['team_full'] . " :: Komanda ir brīva!":"" . $league['team_full'] . " :: " . $player['username'] . "";
?>
	<tr style="color:black; background-color:<?php echo $color; ?>;">
		<td <?php echo $my_team; ?> class="turnirs"><center><?php echo $num; ?></center></td>
		<td id="team_logo" <?php echo $my_team; ?> class="turnirs" title="<?php echo $team_situation; ?>">
			<a style="color:black;" href="/league/<?php echo $row['league_id']; ?>/team/<?php echo $league['team_small']; ?>/">
				<img alt="<?php echo $league['team_small']; ?>" src="<?php echo BASE ?>/style/images/teams/small/<?php echo $team_color; ?>/<?php echo $league['team_small']?>.png">
					<div class="teamname"><?php echo $league['team_full']; ?></div>
			</a>
		</td>
		<td class="turnirs" <?php echo $games_play_want; ?> <?php echo $my_team; ?>><center><?php echo $league['gp']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $league['w']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $league['ot']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $league['l']; ?></center></td>
		<td class="turnirs" style="background-color:#D8D8D8;"><center><b><?php echo $league['pts']; ?></b></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $league['gf']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>><center><?php echo $league['ga']; ?></center></td>
		<td class="turnirs" <?php echo $my_team; ?>>
			<center>
			<?php 
			if( $diff == '0' )
			{
				echo 'E';
			}
			else
			{
				echo "<span style='color:" . $diff_color . ";'>" . $diff . "</span>";	
			}
			?>
			</center>
		</td>
		<td class="turnirs_beigas" <?php echo $my_team; ?>>
			<center>
			<?php
			if( $league['streak_won'] == 0 AND $league['streak_lost'] == 0 )
			{
				echo '---';
			}
			else
			{
				if( $league['streak_won'] > 0 AND $league['streak_lost'] == 0 )
				{
					echo "WON " . $league['streak_won'] . "";
				}
				else
				{
					echo "LOST " . $league['streak_lost'] . "";
				}
				
			}
			?>
			</center>
		</td>
	</tr>
<?php
$i++;
}
?>
 </table>
 </div>
  <div style="float:left; width:272px; padding-left:6px;">
	<table class="west" cellspacing="0" cellpadding="1" style="width: 270px;">
			<tr align="center" style="color:#E2E4E3; font-weight:bold;" bgcolor="#6F7070">
				<td width="50">Saīsinājums</td><td width="50">Atšifrējums</td>
			</tr>
				<tr style="color:black; background-color:#D8D8D8;">
					<td class="turnirs">
						<center>GP</center>
					</td>
					<td class="turnirs_beigas">
						<center>Izspēlētās spēles</center>
					</td>
				</tr>
				<tr style="color:black; background-color:#F2F2F2;">
					<td class="turnirs">
						<center>W</center>
					</td>
					<td class="turnirs_beigas">
						<center>Uzvaras</center>
					</td>
				</tr>
				<tr style="color:black; background-color:#D8D8D8;">
					<td class="turnirs">
						<center>OTL</center>
					</td>
					<td class="turnirs_beigas">
						<center>Zaudējumi pagarinājumā</center>
					</td>
				</tr>
				<tr style="color:black; background-color:#F2F2F2;">
					<td class="turnirs">
						<center>L</center>
					</td>
					<td class="turnirs_beigas">
						<center>Zaudējumi</center>
					</td>
				</tr>
				<tr style="color:black; background-color:#D8D8D8;">
					<td class="turnirs">
						<center>P</center>
					</td>
					<td class="turnirs_beigas">
						<center>Punkti</center>
					</td>
				</tr>
				<tr style="color:black; background-color:#F2F2F2;">
					<td class="turnirs">
						<center>GF</center>
					</td>
					<td class="turnirs_beigas">
						<center>Gūtie vārti</center>
					</td>
				</tr>
				<tr style="color:black; background-color:#D8D8D8;">
					<td class="turnirs">
						<center>GA</center>
					</td>
					<td class="turnirs_beigas">
						<center>Ielaistie vārti</center>
					</td>
				</tr>
				<tr style="color:black; background-color:#F2F2F2;">
					<td class="turnirs">
						<center>DIFF</center>
					</td>
					<td class="turnirs_beigas">
						<center>Vārtu starpība</center>
					</td>
				</tr>
				<tr style="color:black; background-color:#D8D8D8;">
					<td class="turnirs">
						<center>STREAK</center>
					</td>
					<td class="turnirs_beigas">
						<center>WON/LOST sērija</center>
					</td>
				</tr>
			</tr>
		</table>
	</div>
</div>