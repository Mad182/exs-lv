<div class="hexgl-wrapper">
	<h1>HexGL 3D Nākotnes Sacīkstes</h1>
	<p>Oficiālā 3D anti-gravitācijas gaisa kuģu sacīkšu spēle (BKcore HexGL). Uzstādi jaunu rekordu pabeidzot 3 apļus visātrākajā laikā!</p>

	<ul class="nav nav-tabs">
		<li class="{active-tab-game}"><a href="/hexgl">Spēle</a></li>
		<li class="{active-tab-top}"><a href="/hexgl/top">Šodienas tops</a></li>
		<li class="{active-tab-overall-top}"><a href="/hexgl/overall-top">Kopējais tops</a></li>
	</ul>

	<!-- START BLOCK : game-play -->
	<div id="hexgl-game-box" style="position: relative; width: 100%; height: 550px; background: #000; border-radius: 8px; overflow: hidden; box-shadow: 0 8px 24px rgba(0, 243, 255, 0.2); margin-bottom: 20px;">
		<div id="step-1">
			<div id="global"></div>
			<div id="title"></div>
			<div id="menucontainer">
				<div id="menu">
					<div id="start">Sākt spēli</div>
					<div id="s-controlType">Vadība: Keyboard</div>
					<div id="s-quality">Kvalitāte: High</div>
					<div id="s-hud">HUD: On</div>
					<div id="s-godmode" style="display: none">Godmode: Off</div>
					<div id="s-credits">Autori (Credits)</div>
				</div>
			</div>
		</div>

		<div id="step-2" style="display: none">
			<div id="ctrl-help">Klikšķini vai pieskaries, lai turpinātu.</div>
		</div>

		<div id="step-3" style="display: none">
			<div id="progressbar"></div>
		</div>

		<div id="step-4" style="display: none">
			<div id="overlay"></div>
			<div id="main"></div>
		</div>

		<div id="step-5" style="display: none">
			<div id="finish-title" style="font-size: 2.2em; color: #00f3ff; margin-bottom: 10px;">FINIŠS</div>
			<div id="time">00'00''00</div>
			<div id="score-push-msg" style="font-size: 0.6em; color: #00f3ff; margin-top: 15px; text-shadow: 0 0 10px rgba(0, 243, 255, 0.8);"></div>
			<div id="ctrl-help" style="margin-top: 25px; cursor: pointer;">Klikšķini jebkur, lai spēlētu vēlreiz.</div>
		</div>

		<div id="credits" style="display: none">
			<h3>Izstrādātāji</h3>
			<p><b>Koncepcija un izstrāde</b><br>Thibaut Despoulain (BKcore)</p>
			<p><b>Tehnoloģijas</b><br>WebGL, JavaScript, Three.js</p>
			<h4>Klikšķini jebkur, lai atgrieztos.</h4>
		</div>
	</div>

	<!-- Personal Best Stats Bar -->
	<div class="hexgl-stats-bar">
		<div class="hexgl-stat-card">
			<div class="label">Tavs labākais rekords</div>
			<div class="value">{user-high-score} pnk</div>
		</div>
	</div>
	<!-- END BLOCK : game-play -->

	<!-- START BLOCK : today-top -->
	<h3>Šodienas labākie braucēji</h3>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th style="width:40px;">#</th>
				<th>Lietotājs</th>
				<th style="text-align:right;">Punkti</th>
			</tr>
		</thead>
		<tbody>
			<!-- START BLOCK : today-top-node -->
			<tr>
				<td>{rank}</td>
				<td>{user-nick}</td>
				<td style="text-align:right;font-weight:bold;">{score}</td>
			</tr>
			<!-- END BLOCK : today-top-node -->
			<!-- START BLOCK : today-empty -->
			<tr>
				<td colspan="3" style="text-align:center;" class="muted">Šodien vēl neviens nav uzstādījis rekordu. Esi pirmais!</td>
			</tr>
			<!-- END BLOCK : today-empty -->
		</tbody>
	</table>
	<!-- END BLOCK : today-top -->

	<!-- START BLOCK : alltime-top -->
	<h3>Visu laiku labākie HexGL braucēji</h3>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th style="width:40px;">#</th>
				<th>Lietotājs</th>
				<th style="text-align:right;">Punkti</th>
			</tr>
		</thead>
		<tbody>
			<!-- START BLOCK : alltime-top-node -->
			<tr>
				<td>{rank}</td>
				<td>{user-nick}</td>
				<td style="text-align:right;font-weight:bold;">{score}</td>
			</tr>
			<!-- END BLOCK : alltime-top-node -->
			<!-- START BLOCK : alltime-empty -->
			<tr>
				<td colspan="3" style="text-align:center;" class="muted">Reģistrētu rezultātu vēl nav.</td>
			</tr>
			<!-- END BLOCK : alltime-empty -->
		</tbody>
	</table>
	<!-- END BLOCK : alltime-top -->
</div>
