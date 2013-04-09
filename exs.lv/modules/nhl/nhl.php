<?php
$nhldb = new ezSQL_mysql('nhl', 'shuffle', 'nhl', $hostname);
$nhldb->query("set names utf8");


$league_id = (int) $_GET['var1'];

$league = $nhldb->get_row("SELECT * FROM `leagues` WHERE `league_id` = '$league_id'");

if ($league->league_signup != 1) {
	if ($league->league_status != 'trade') {
		if ($league->playoff == 1) {
			$page_title = 'NHL #' . $league_id . ' līga :: PlayOFF';
			include('playoff.php');
		} else {
			$page_title = 'NHL #' . $league_id . ' līga :: Regular Season';

			$myteam = $nhldb->get_row("SELECT * FROM league_nhl_standings WHERE player_id = '$auth->id' AND league_id = '$league_id'");
			$kopa_jaizspele = $nhldb->get_var("SELECT count(*) FROM league_nhl_calendar WHERE league_id = '$league_id'") / 2;
			$izspeletas_speles = $nhldb->get_var("SELECT count(*) FROM league_nhl_calendar WHERE played = '1' AND league_id = '$league_id'") / 2;
			$atlikusas_speles = $kopa_jaizspele - $izspeletas_speles;
			$izspeleti_procenti = round($izspeletas_speles / $kopa_jaizspele * 100, 1);
			?>
			<h1>
				<a href="/leagues/">Līgas</a> NHL #<?php
			echo $league_id;
			?> līga :: Regular Season
				<span style="float:right;">
					<div id="tourney_time">
						Turnīrs ilgst <?php
			echo time_ago_m($league['league_start_time']);
			?>
					</div>
				</span>
			</h1>
			<div id="turnirs_bg">
				<div id="turnirs">
					<a class="first" href="/league/<?php
			echo $league_id;
			?>/"> Konferenču tabulas</a>
					<a href="/league/<?php
			echo $league_id;
			?>/division/"> Divīziju tabulas</a>
					<a href="/league/<?php
			echo $league_id;
			?>/league/"> Līgas tabula</a>
					<a href="/league/<?php
			echo $league_id;
			?>/statistics/"> Statistika</a>
					<a href="/league/<?php
			echo $league_id;
			?>/teams/"> Līgas komandas</a>
					<?php
					if ($auth->id == $myteam->player_id) {
						?>
						<a href="/league/<?php
				echo $league_id;
						?>/profile/" style="color:red;"> Līgas profils</a>
						<a href="/league/<?php
				echo $league_id;
						?>/calendar/"> <b><?php
				echo $myteam->team_full;
						?></b> - <i>spēļu kalendārs</i></a>
						   <?php
					   }
					   ?>
				</div>
			</div>
			<div style="height: 4px;"></div>
			<?php
			if (get_get('id') != 'calendar' AND get_get('id') != 'statistics' AND get_get('id') != 'teams' AND get_get('id') != 'team' AND get_get('id') != 'profile') {
				?>
				<font color="black">
				Kopā jaizspēlē <b><?php
				echo $kopa_jaizspele;
				?></b> spēles, <?php
				echo (substr($izspeletas_speles, -1) == 1 ? "izpēlēta" : "izpēlētas");
				?> <b><?php
				echo $izspeletas_speles;
				?></b> <?php
				echo (substr($izspeletas_speles, -1) == 1 ? "spēle" : "spēles");
				?>, <?php
				echo (substr($atlikusas_speles, -1) == 1 ? "atlikusi" : "atlikušas");
				?> vēl <b><?php
				echo $atlikusas_speles;
				?></b> <?php
				echo (substr($atlikusas_speles, -1) == 1 ? "spēle" : "spēles");
				?>, kas ir <b><?php
				echo $izspeleti_procenti;
				?>%</b> <?php
				echo (substr($izspeleti_procenti, -1) == 1 ? "procents" : "procenti");
				?> no kopējā spēļu skaita!
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
			if (get_get('id') == 'league') {
				include('league.php');
			} elseif (get_get('id') == 'division') {
				include('ivision.php');
			} elseif (get_get('id') == 'statistics') {
				include('statistics.php');
			} elseif (get_get('id') == 'teams') {
				include('teams.php');
			} elseif (get_get('id') == 'profile') {
				include('profile.php');
			} elseif (get_get('id') == 'calendar') {
				include('calendar.php');
			} elseif (get_get('id') == 'team') {
				include('team.php');
			} else {
				include('conforence.php');
			}
		}
	} else {
		$page_title = 'NHL #' . $league_id . ' līga :: Trade';
		include('trade.php');
	}
} else {
	$page_title = 'NHL #' . $league_id . ' līga :: Pieteikšanās';
	include('signup.php');
}


exit;