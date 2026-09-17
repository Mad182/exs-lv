<div class="tanki-wrapper">
	<div class="tanki-header">
		<h2><span class="tanki-title-icon">🎖️</span> Tanki 1990 {game-rate}</h2>
		<p class="tanki-subtitle">
			Aizsargā <strong>EXS bāzi</strong>! Kusties ar <kbd>↑</kbd><kbd>↓</kbd><kbd>←</kbd><kbd>→</kbd> vai <kbd>W</kbd><kbd>A</kbd><kbd>S</kbd><kbd>D</kbd>, šauj ar <kbd>Spacebar</kbd> / <kbd>J</kbd>!
		</p>
	</div>

	<!-- START BLOCK : guest-notice -->
	<div class="alert alert-info tanki-guest-notice">
		<i class="icon-info-sign"></i> Tu neesi pieslēdzies sistēmai. Lai tavi rekordi tiktu saglabāti topā un rādīti aktivitāšu plūsmā, lūdzu, autorizējies vai <a href="/register">reģistrējies</a>!
	</div>
	<!-- END BLOCK : guest-notice -->

	<!-- FULL-WIDTH GAME STAGE -->
	<div class="tanki-main-stage">
		<!-- HUD BAR -->
		<div class="tanki-hud-bar">
			<div class="hud-item">
				<span class="hud-label">PUNKTI</span>
				<strong id="stat-score" class="hud-value highlight">0</strong>
			</div>
			<div class="hud-item">
				<span class="hud-label">LĪMENIS</span>
				<strong id="stat-stage" class="hud-value">1</strong>
			</div>
			<div class="hud-item">
				<span class="hud-label">DZĪVĪBAS</span>
				<div id="stat-lives" class="hud-lives">
					<span class="tank-life-icon"></span>
					<span class="tank-life-icon"></span>
					<span class="tank-life-icon"></span>
				</div>
			</div>
			<div class="hud-item hud-powerup-box">
				<span class="hud-label">BONUSS</span>
				<span id="stat-powerup" class="hud-powerup-badge none">NAV</span>
			</div>
			<div class="hud-actions">
				<button id="tanki-sound-btn" class="hud-btn" title="Skaņa (M)">🔊</button>
				<button id="tanki-pause-btn" class="hud-btn" title="Pauze (P)">⏸</button>
			</div>
		</div>

		<!-- CANVAS & SIDEBAR CONTAINER (LARGE ARCADE WINDOW) -->
		<div class="tanki-viewport-row">
			<div class="tanki-canvas-container">
				<canvas id="tanki-canvas" width="416" height="416"></canvas>

				<!-- START OVERLAY -->
				<div id="tanki-start-overlay" class="tanki-overlay">
					<div class="tanki-overlay-content">
						<div class="tanki-logo-badge">
							<span class="logo-text-tanki">TANKI</span>
							<span class="logo-text-year">1990</span>
						</div>
						<p class="overlay-lead">Klasiskā <strong>Battle City</strong> tanku cīņa! Aizsargā EXS bāzi no 20 ienaidnieku tanku uzbrukumiem!</p>

						<div class="start-meta-grid">
							<div class="start-meta-card">
								<span class="card-icon">⭐</span>
								<strong>Uzlabojumi</strong>
								<small>Vāc zvaigznes, lai palielinātu ātrumu, ugunsspēku un caursistu dzelzi</small>
							</div>
							<div class="start-meta-card">
								<span class="card-icon">🦅</span>
								<strong>EXS Bāze</strong>
								<small>Neļauj ienaidniekiem trāpīt zelta bāzei – tās zaudējums nozīmē tūlītēju sakāvi</small>
							</div>
							<div class="start-meta-card">
								<span class="card-icon">🏆</span>
								<strong>Rekordi</strong>
								<small>Tavs labākais: <strong id="tanki-start-best-score">{user-high-score}</strong> pt</small>
							</div>
						</div>

						<button id="tanki-start-btn" class="tanki-btn primary-pulse">
							SĀKT KAUJU (Space / Enter)
						</button>
					</div>
				</div>

				<!-- STAGE CLEARED OVERLAY -->
				<div id="tanki-stage-overlay" class="tanki-overlay" style="display: none;">
					<div class="tanki-overlay-content">
						<h3 id="stage-cleared-title" class="stage-title">LĪMENIS 1 PABEIGTS</h3>
						<div class="stage-tally-table">
							<div class="tally-row">
								<span class="tally-tank-icon tank-basic"></span>
								<span id="tally-basic-count" class="tally-count">0</span>
								<span class="tally-pts">100 pt</span>
								<span id="tally-basic-pts" class="tally-total">0</span>
							</div>
							<div class="tally-row">
								<span class="tally-tank-icon tank-fast"></span>
								<span id="tally-fast-count" class="tally-count">0</span>
								<span class="tally-pts">200 pt</span>
								<span id="tally-fast-pts" class="tally-total">0</span>
							</div>
							<div class="tally-row">
								<span class="tally-tank-icon tank-power"></span>
								<span id="tally-power-count" class="tally-count">0</span>
								<span class="tally-pts">300 pt</span>
								<span id="tally-power-pts" class="tally-total">0</span>
							</div>
							<div class="tally-row">
								<span class="tally-tank-icon tank-armor"></span>
								<span id="tally-armor-count" class="tally-count">0</span>
								<span class="tally-pts">400 pt</span>
								<span id="tally-armor-pts" class="tally-total">0</span>
							</div>
							<hr class="tally-divider" />
							<div class="tally-row total-row">
								<span>KOPĀ IZNĪCINĀTI:</span>
								<strong id="tally-total-tanks">0</strong>
								<span>PUNKTI:</span>
								<strong id="tally-total-score">0</strong>
							</div>
						</div>

						<button id="tanki-next-stage-btn" class="tanki-btn primary-pulse">
							NĀKAMAIS LĪMENIS (Space)
						</button>
					</div>
				</div>

				<!-- GAME OVER OVERLAY -->
				<div id="tanki-gameover-overlay" class="tanki-overlay" style="display: none;">
					<div class="tanki-overlay-content">
						<h3 id="gameover-header" class="gameover-title">SPĒLE BEIGUSIES</h3>
						<p id="gameover-reason" class="gameover-reason">Tavi tanki tika iznīcināti!</p>
						
						<div class="tanki-score-board">
							<div class="score-card">
								<span class="card-title">Punkti</span>
								<strong id="tanki-final-score" class="card-num">0</strong>
							</div>
							<div class="score-card">
								<span class="card-title">Līmenis</span>
								<strong id="tanki-final-stage" class="card-num">1</strong>
							</div>
							<div class="score-card">
								<span class="card-title">Tanki</span>
								<strong id="tanki-final-kills" class="card-num">0</strong>
							</div>
							<div class="score-card">
								<span class="card-title">Labākais</span>
								<strong id="tanki-best-score" class="card-num">{user-high-score}</strong>
							</div>
						</div>

						<div id="tanki-record-alert" class="tanki-new-record" style="display: none;">
							🎉 JAUNS PERSONĪGAIS REKORDS! 🎉
						</div>

						<div id="tanki-gameover-status" class="tanki-gameover-status" style="display: none;"></div>

						<button id="tanki-restart-btn" class="tanki-btn primary-pulse">
							SPĒLĒT VĒLREIZ (Space / Enter)
						</button>
					</div>
				</div>

				<!-- PAUSE OVERLAY -->
				<div id="tanki-pause-overlay" class="tanki-overlay" style="display: none;">
					<div class="tanki-overlay-content">
						<h3>⏸ KAUJA PAUZĒTA</h3>
						<p>Nospied <kbd>P</kbd> vai pogu zemāk, lai turpinātu kauju.</p>
						<button id="tanki-resume-btn" class="tanki-btn primary">TURPINĀT</button>
					</div>
				</div>
			</div>

			<!-- RETRO SIDEBAR HUD (CLASSIC NES QUEUE) -->
			<div class="tanki-side-hud">
				<div class="side-hud-section">
					<span class="side-hud-title">IENAIDNIEKI</span>
					<div id="enemy-queue-container" class="enemy-queue-grid">
						<!-- 20 mini tank icons generated via JS -->
					</div>
				</div>
				<div class="side-hud-section player-stats-box">
					<div class="side-stat-row">
						<span class="side-stat-icon">IP</span>
						<strong id="side-lives-count">3</strong>
					</div>
					<div class="side-stat-row">
						<span class="side-stat-icon">🚩</span>
						<strong id="side-stage-count">1</strong>
					</div>
					<div class="side-stat-row">
						<span class="side-stat-icon">⭐</span>
						<strong id="side-tier-count">1</strong>
					</div>
				</div>
			</div>
		</div>

		<!-- TOUCH CONTROLS (Mobile) -->
		<div class="tanki-touch-controls">
			<div class="touch-dpad">
				<button id="touch-up" class="touch-btn dpad-up" aria-label="Uz augšu">▲</button>
				<div class="dpad-middle">
					<button id="touch-left" class="touch-btn dpad-left" aria-label="Pa kreisi">◀</button>
					<div class="dpad-center"></div>
					<button id="touch-right" class="touch-btn dpad-right" aria-label="Pa labi">▶</button>
				</div>
				<button id="touch-down" class="touch-btn dpad-down" aria-label="Uz leju">▼</button>
			</div>
			<div class="touch-action-box">
				<button id="touch-fire" class="touch-btn touch-fire" aria-label="Šaut">🔥 ŠAUT</button>
			</div>
		</div>
	</div>

	<!-- BOTTOM SECTION: STATS / LEADERBOARDS NEXT TO INSTRUCTIONS -->
	<div class="tanki-bottom-section">
		<!-- LEFT COLUMN: LEADERBOARDS -->
		<div class="tanki-scores-column">
			<div class="tanki-scores-card">
				<div class="tanki-tab-buttons">
					<button id="tab-today" class="tanki-tab-btn active">📅 Šodienas Tops</button>
					<button id="tab-alltime" class="tanki-tab-btn">🏆 Visu Laiku Rekordi</button>
				</div>

				<!-- TODAY LEADERBOARD CONTENT -->
				<div id="content-today" class="tanki-tab-content active">
					<table class="table table-striped table-hover tanki-leaderboard-table">
						<thead>
							<tr>
								<th style="width: 40px;">#</th>
								<th>Lietotājs</th>
								<th style="text-align: right;">Punkti</th>
							</tr>
						</thead>
						<tbody>
							<!-- START BLOCK : today-top-node -->
							<tr{user-special}>
								<td>{user-place}</td>
								<td><a href="{user-url}">{user-nick}</a></td>
								<td style="text-align: right;"><strong>{score}</strong></td>
							</tr>
							<!-- END BLOCK : today-top-node -->
							<!-- START BLOCK : today-empty -->
							<tr>
								<td colspan="3" class="text-center empty-leaderboard">Šodien vēl nav uzstādīts neviens rekords. Esi pirmais!</td>
							</tr>
							<!-- END BLOCK : today-empty -->
						</tbody>
					</table>
				</div>

				<!-- ALL-TIME LEADERBOARD CONTENT -->
				<div id="content-alltime" class="tanki-tab-content">
					<table class="table table-striped table-hover tanki-leaderboard-table">
						<thead>
							<tr>
								<th style="width: 40px;">#</th>
								<th>Lietotājs</th>
								<th style="text-align: right;">Punkti</th>
							</tr>
						</thead>
						<tbody>
							<!-- START BLOCK : alltime-top-node -->
							<tr{user-special}>
								<td>{user-place}</td>
								<td><a href="{user-url}">{user-nick}</a></td>
								<td style="text-align: right;"><strong>{score}</strong></td>
							</tr>
							<!-- END BLOCK : alltime-top-node -->
							<!-- START BLOCK : alltime-empty -->
							<tr>
								<td colspan="3" class="text-center empty-leaderboard">Neviens rekords vēl nav reģistrēts.</td>
							</tr>
							<!-- END BLOCK : alltime-empty -->
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<!-- RIGHT COLUMN: INSTRUCTIONS & ARSENAL GUIDE -->
		<div class="tanki-guide-column">
			<div class="tanki-guide-card">
				<h4><span class="guide-icon">📦</span> Bonusu un Arsenāla Ceļvedis</h4>
				<div class="guide-grid">
					<div class="guide-item">
						<span class="item-badge badge-star">⭐</span>
						<div>
							<strong>Zvaigzne (Star)</strong>
							<p>Uzlabo tanku līdz 4 līmeņiem: ātrāki šāviņi, dubultie lādiņi un dzelzs betona sagraušana!</p>
						</div>
					</div>
					<div class="guide-item">
						<span class="item-badge badge-bomb">💣</span>
						<div>
							<strong>Granāta (Bomb)</strong>
							<p>Nekavējoties uzspridzina visus aktīvos ienaidnieku tankus kaujas laukā.</p>
						</div>
					</div>
					<div class="guide-item">
						<span class="item-badge badge-clock">⏰</span>
						<div>
							<strong>Pulkstenis (Timer)</strong>
							<p>Iesaldē visus ienaidnieku tankus uz 10 sekundēm nekustīgā stāvoklī.</p>
						</div>
					</div>
					<div class="guide-item">
						<span class="item-badge badge-shield">🛡️</span>
						<div>
							<strong>Bruņas (Helmet)</strong>
							<p>Piešķir necaursitamas enerģijas bruņas uz 12 sekundēm.</p>
						</div>
					</div>
					<div class="guide-item">
						<span class="item-badge badge-shovel">⛏️</span>
						<div>
							<strong>Lāpsta (Shovel)</strong>
							<p>Nocietina EXS bāzes aizsargmūri ar dzelzs betona blokiem uz 20 sekundēm.</p>
						</div>
					</div>
					<div class="guide-item">
						<span class="item-badge badge-tank">🎖️</span>
						<div>
							<strong>Papildu Dzīvība</strong>
							<p>Piešķir papildu rezerves tanku tavam arsenālam.</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
