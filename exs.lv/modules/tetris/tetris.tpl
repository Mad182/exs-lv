<h1>Tetris - klasiskā bluķīšu spēle</h1>

<div class="tabs">
	<li><a href="/tetris" class="tab{active-tab-game}">Spēle</a></li>
	<li><a href="/tetris/top" class="tab{active-tab-top}">Šodienas tops</a></li>
	<li><a href="/tetris/overall-top" class="tab{active-tab-overall-top}">Visu laiku tops</a></li>
</div>

<!-- START BLOCK : tetris-game -->
<!-- START BLOCK : guest-notice -->
<div class="alert alert-info tetris-guest-alert">
	<strong>Piezīme:</strong> Tu spēlē kā viesis. <a href="/register">Reģistrējies</a> vai ieej profilā, lai saglabātu savu rezultātu topā!
</div>
<!-- END BLOCK : guest-notice -->

<div class="tetris-wrapper">
	<div class="tetris-scaler-wrapper">
		<div class="tetris-main-panel">
			<div class="tetris-board-container">
				<canvas id="tetris-canvas" width="300" height="600"></canvas>
				
				<div id="tetris-overlay" class="tetris-overlay">
					<div id="overlay-content" class="overlay-card">
						<h2 id="overlay-title">SĀKT SPĒLI</h2>
						<p id="overlay-msg">Spied Sākt pogu vai atstarpi, lai sāktu spēli!</p>
						<button id="btn-start" class="btn btn-primary btn-large btn-tetris-action">Sākt spēli</button>
					</div>
				</div>
			</div>

			<div class="tetris-sidebar-panel">
				<div class="tetris-box">
					<h3>Nākamais</h3>
					<div class="canvas-box">
						<canvas id="next-canvas" width="100" height="100"></canvas>
					</div>
				</div>

				<div class="tetris-box">
					<h3>Turēt (Hold)</h3>
					<div class="canvas-box">
						<canvas id="hold-canvas" width="100" height="100"></canvas>
					</div>
				</div>

				<div class="tetris-box tetris-stats-box">
					<div class="stat-item">
						<span class="stat-label">Punkti</span>
						<span id="stat-score" class="stat-value">0</span>
					</div>
					<div class="stat-item">
						<span class="stat-label">Līmenis</span>
						<span id="stat-level" class="stat-value">1</span>
					</div>
					<div class="stat-item">
						<span class="stat-label">Līnijas</span>
						<span id="stat-lines" class="stat-value">0</span>
					</div>
					<div class="stat-item">
						<span class="stat-label">Mans rekords</span>
						<span id="stat-highscore" class="stat-value">{user-highscore}</span>
					</div>
				</div>

				<div class="tetris-touch-controls">
					<button type="button" id="btn-touch-left" class="btn btn-touch" aria-label="Pa kreisi">&larr;</button>
					<button type="button" id="btn-touch-rotate" class="btn btn-touch" aria-label="Pagriezt">&#8635;</button>
					<button type="button" id="btn-touch-right" class="btn btn-touch" aria-label="Pa labi">&rarr;</button>
					<button type="button" id="btn-touch-down" class="btn btn-touch" aria-label="Uz leju">&darr;</button>
					<button type="button" id="btn-touch-drop" class="btn btn-touch btn-touch-drop" aria-label="Krist">&#9196;</button>
					<button type="button" id="btn-touch-hold" class="btn btn-touch" aria-label="Turēt">HOLD</button>
				</div>

				<div class="tetris-controls-btns">
					<button id="btn-pause" class="btn btn-warning" disabled>Nopauzēt (P)</button>
					<button id="btn-sound" class="btn btn-info">Skaņa: IESL.</button>
				</div>
			</div>
		</div>
	</div>

	<div class="tetris-mini-top-panel">
		<div class="widget">
			<h3 class="title"><span>Labākie spēlētāji</span></h3>
			<table class="table table-striped table-condensed tetris-mini-table">
				<thead>
					<tr>
						<th>#</th>
						<th>Spēlētājs</th>
						<th>Punkti</th>
					</tr>
				</thead>
				<tbody>
					<!-- START BLOCK : mini-top-node -->
					<tr>
						<td>{place}.</td>
						<td><a href="{user-url}">{user-nick}</a></td>
						<td><strong>{score}</strong></td>
					</tr>
					<!-- END BLOCK : mini-top-node -->
				</tbody>
			</table>
			<div class="mini-top-footer">
				<a href="/tetris/overall-top" class="btn btn-mini">Skatīt pilno topu &raquo;</a>
			</div>
		</div>

		<div class="tetris-guide-box">
			<h4>Vadība</h4>
			<ul>
				<li><kbd>&larr;</kbd> <kbd>&rarr;</kbd> - Pārvietot pa kreisi / labi</li>
				<li><kbd>&darr;</kbd> - Ātrāka krišana (Soft drop)</li>
				<li><kbd>Atstarpe</kbd> - Momentāna krišana (Hard drop)</li>
				<li><kbd>&uarr;</kbd> vai <kbd>X</kbd> - Pagriezt pulksteņrādītāja virzienā</li>
				<li><kbd>Z</kbd> vai <kbd>Ctrl</kbd> - Pagriezt pretēji</li>
				<li><kbd>C</kbd> vai <kbd>Shift</kbd> - Samainīt / Turēt figūru (Hold)</li>
				<li><kbd>P</kbd> vai <kbd>Esc</kbd> - Pauze</li>
			</ul>
		</div>
	</div>
</div>
<!-- END BLOCK : tetris-game -->

<!-- START BLOCK : tetris-top -->
<h2>{top-title}</h2>
<table class="table table-striped table-hover tetris-top-table">
	<thead>
		<tr>
			<th style="width:10%">Vieta</th>
			<th style="width:40%">Lietotājs</th>
			<th style="width:25%">Punkti</th>
			<th style="width:25%">Datums</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : top-node -->
		<tr>
			<td{user-special}>{user-place}</td>
			<td{user-special}><a href="{user-url}">{user-nick}</a></td>
			<td{user-special}><strong>{user-score}</strong></td>
			<td{user-special}>{user-date}</td>
		</tr>
		<!-- END BLOCK : top-node -->
		<!-- START BLOCK : no-scores -->
		<tr>
			<td colspan="4" class="text-center muted" style="padding: 20px;">Pagaidām nav neviena rezultāta. Būsi pirmais!</td>
		</tr>
		<!-- END BLOCK : no-scores -->
	</tbody>
</table>
<!-- END BLOCK : tetris-top -->

<!-- START BLOCK : seo-text -->
<div class="game-description-box">
	<h2>Par Tetris spēli un kā spēlēt</h2>
	<p><strong>Tetris</strong> ir viena no vispopulārākajām un leģendārākajām loģikas spēlēm pasaulē. EXS.LV platformā vari spēlēt uzlabotu Tetris versiju tieši savā pārlūkprogrammā bez maksas!</p>
	<h3>Spēles noteikumi un vadība:</h3>
	<ul>
		<li>Izmanto <strong>Bultiņas (Pa kreisi / Pa labi)</strong> vai <strong>A / D</strong> taustiņus, lai pārvietotu krītošās figūras.</li>
		<li>Spied <strong>Bultiņu uz augšu / W</strong> vai <strong>Space</strong>, lai pagrieztu figūru.</li>
		<li>Spied <strong>Bultiņu uz leju / S</strong> ātrākai nolaišanai.</li>
		<li>Aizpildi pilnas horizontālās līnijas, lai tās izdzēstu un iegūtu punktus. Vācot 4 līnijas vienlaikus (Tetris!), saņemsi vislielāko punktu bonusu.</li>
	</ul>
</div>
<!-- END BLOCK : seo-text -->
