<div class="hexgl-wrapper">
	<h1>HexGL 3D Nākotnes Sacīkstes</h1>
	<p>Stūrē anti-gravitācijas gaisa kuģi nākotnes 3D trasē, vāc ātruma paātrinājumus, izvairies no sadursmēm un uzstādi jaunu rekordu!</p>

	<ul class="nav nav-tabs">
		<li class="{active-tab-game}"><a href="/hexgl">Spēle</a></li>
		<li class="{active-tab-top}"><a href="/hexgl/top">Šodienas tops</a></li>
		<li class="{active-tab-overall-top}"><a href="/hexgl/overall-top">Kopējais tops</a></li>
	</ul>

	<!-- START BLOCK : game-play -->
	<div class="hexgl-container">
		<div id="hexgl-canvas" class="hexgl-canvas-box"></div>

		<!-- HUD Overlay -->
		<div class="hexgl-hud">
			<div class="hexgl-hud-top">
				<div class="hexgl-hud-card">
					APLIS <span id="hexgl-lap-val" class="val">1 / 3</span>
				</div>
				<div class="hexgl-hud-card hexgl-shield-container">
					VAIROGS
					<div class="hexgl-shield-bar-bg">
						<div id="hexgl-shield-bar" class="hexgl-shield-bar-fill"></div>
					</div>
				</div>
				<div class="hexgl-hud-card">
					LAIKS <span id="hexgl-time-val" class="val">0.00s</span>
				</div>
			</div>

			<div class="hexgl-hud-bottom">
				<div class="hexgl-speedometer">
					<div id="hexgl-speed-val" class="speed-val">0</div>
					<div class="speed-unit">KM / H</div>
				</div>
			</div>
		</div>

		<!-- Boost Banner -->
		<div id="hexgl-boost-banner" class="hexgl-boost-msg">⚡ TURBO BOOST! ⚡</div>

		<!-- Start Overlay Screen -->
		<div id="hexgl-start-overlay" class="hexgl-overlay">
			<h2>HexGL 3D Racing</h2>
			<p>Izmanto klaviatūru vai ekrāna pogas, lai stūrētu gaisa kuģi trīs apļus un sasniegtu visātrāko laiku!</p>

			<div class="hexgl-controls-info">
				<div class="hexgl-control-badge"><kbd>W</kbd> / <kbd>&uarr;</kbd> Uz priekšu</div>
				<div class="hexgl-control-badge"><kbd>A</kbd> / <kbd>D</kbd> Stūrēt pa kreisi / pa labi</div>
				<div class="hexgl-control-badge"><kbd>S</kbd> / <kbd>&darr;</kbd> Bremzēt</div>
				<div class="hexgl-control-badge"><kbd>Space</kbd> Turbo spēks</div>
			</div>

			<button id="hexgl-start-btn" class="hexgl-btn">SĀKT SPĒLI &raquo;</button>
		</div>

		<!-- End Overlay Screen -->
		<div id="hexgl-end-overlay" class="hexgl-overlay" style="display:none;">
			<h2 id="hexgl-end-title">FINIŠS!</h2>
			<p id="hexgl-end-msg">Rezultāts tika saglabāts topos.</p>
			<button id="hexgl-restart-btn" class="hexgl-btn">MĒĢINĀT VĒLREIZ &raquo;</button>
		</div>

		<!-- Touch Controls for Mobile -->
		<div class="hexgl-touch-controls">
			<div class="hexgl-touch-group">
				<div id="hexgl-touch-left" class="hexgl-touch-btn">&larr;</div>
				<div id="hexgl-touch-right" class="hexgl-touch-btn">&rarr;</div>
			</div>
			<div class="hexgl-touch-group">
				<div id="hexgl-touch-accel" class="hexgl-touch-btn">&uarr;</div>
				<div id="hexgl-touch-boost" class="hexgl-touch-btn">⚡</div>
			</div>
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
