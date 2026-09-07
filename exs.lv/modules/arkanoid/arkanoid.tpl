<div class="arkanoid-wrapper">
	<div class="arkanoid-header">
		<h2><span class="arkanoid-title-icon">🧱</span> Arkanoid</h2>
		<p class="arkanoid-subtitle">
			Kustini <strong>Vaus</strong> ar peli vai <kbd>←</kbd> <kbd>→</kbd> / <kbd>A</kbd> <kbd>D</kbd>, palaid bumbiņu vai šauj ar <kbd>Spacebar</kbd> / kreiso peles klikšķi!
		</p>
	</div>

	<!-- START BLOCK : guest-notice -->
	<div class="alert alert-info arkanoid-guest-notice">
		<i class="icon-info-sign"></i> Tu neesi pieslēdzies sistēmai. Lai tavi rekordi tiktu saglabāti topā un rādīti aktivitāšu plūsmā, lūdzu, <a href="/login">autorizējies</a> vai <a href="/register">reģistrējies</a>!
	</div>
	<!-- END BLOCK : guest-notice -->

	<div class="arkanoid-game-layout">
		<!-- MAIN GAME STAGE -->
		<div class="arkanoid-main-stage">
			<!-- HUD BAR -->
			<div class="arkanoid-hud-bar">
				<div class="hud-item">
					<span class="hud-label">PUNKTI</span>
					<strong id="stat-score" class="hud-value highlight">0</strong>
				</div>
				<div class="hud-item">
					<span class="hud-label">LĪMENIS</span>
					<strong id="stat-round" class="hud-value">1</strong>
				</div>
				<div class="hud-item">
					<span class="hud-label">DZĪVĪBAS</span>
					<div id="stat-lives" class="hud-lives">
						<span class="vaus-life-icon"></span>
						<span class="vaus-life-icon"></span>
						<span class="vaus-life-icon"></span>
					</div>
				</div>
				<div class="hud-item hud-powerup-box">
					<span class="hud-label">BONUSS</span>
					<span id="stat-powerup" class="hud-powerup-badge none">NAV</span>
				</div>
				<div class="hud-actions">
					<button id="arkanoid-sound-btn" class="hud-btn" title="Skaņa (M)">🔊</button>
					<button id="arkanoid-pause-btn" class="hud-btn" title="Pauze (P)">⏸</button>
				</div>
			</div>

			<!-- CANVAS STAGE -->
			<div class="arkanoid-canvas-container">
				<canvas id="arkanoid-canvas" width="480" height="600"></canvas>

				<!-- START OVERLAY -->
				<div id="arkanoid-start-overlay" class="arkanoid-overlay">
					<div class="arkanoid-overlay-content">
						<div class="arkanoid-logo-badge">
							<span class="logo-brick c-red">A</span>
							<span class="logo-brick c-orange">R</span>
							<span class="logo-brick c-yellow">K</span>
							<span class="logo-brick c-green">A</span>
							<span class="logo-brick c-cyan">N</span>
							<span class="logo-brick c-blue">O</span>
							<span class="logo-brick c-pink">I</span>
							<span class="logo-brick c-silver">D</span>
						</div>
						<p class="overlay-lead">Atsit bumbu ar <strong>Vaus</strong>, sašķaidi blokus un savāc leģendāros kapsulu bonusus!</p>

						<div class="start-meta-grid">
							<div class="start-meta-card">
								<span class="card-icon">🎯</span>
								<strong>Precizitāte</strong>
								<small>Atsitiens no platformas malas nodrošina asāku leņķi</small>
							</div>
							<div class="start-meta-card">
								<span class="card-icon">💊</span>
								<strong>Bonusi</strong>
								<small>Ķer krītošās kapsulas papildu spējām un lāzeriem</small>
							</div>
							<div class="start-meta-card">
								<span class="card-icon">🏆</span>
								<strong>Rekordi</strong>
								<small>Tavs labākais: <strong>{user-high-score}</strong> pt</small>
							</div>
						</div>

						<button id="arkanoid-start-btn" class="arkanoid-btn primary-pulse">
							SĀKT SPĒLI (Space / Klikšķis)
						</button>
					</div>
				</div>

				<!-- GAME OVER OVERLAY -->
				<div id="arkanoid-gameover-overlay" class="arkanoid-overlay" style="display: none;">
					<div class="arkanoid-overlay-content">
						<h3 class="gameover-title">SPĒLE BEIGUSIES</h3>
						<div class="arkanoid-score-board">
							<div class="score-card">
								<span class="card-title">Punkti</span>
								<strong id="arkanoid-final-score" class="card-num">0</strong>
							</div>
							<div class="score-card">
								<span class="card-title">Līmenis</span>
								<strong id="arkanoid-final-round" class="card-num">1</strong>
							</div>
							<div class="score-card">
								<span class="card-title">Izsistie bloki</span>
								<strong id="arkanoid-final-bricks" class="card-num">0</strong>
							</div>
							<div class="score-card">
								<span class="card-title">Labākais rezultāts</span>
								<strong id="arkanoid-best-score" class="card-num">{user-high-score}</strong>
							</div>
						</div>

						<div id="arkanoid-record-alert" class="arkanoid-new-record" style="display: none;">
							🎉 JAUNS PERSONĪGAIS REKORDS! 🎉
						</div>

						<button id="arkanoid-restart-btn" class="arkanoid-btn primary-pulse">
							SPĒLĒT VĒLREIZ (Space)
						</button>
					</div>
				</div>

				<!-- VICTORY OVERLAY -->
				<div id="arkanoid-victory-overlay" class="arkanoid-overlay" style="display: none;">
					<div class="arkanoid-overlay-content">
						<h3 class="victory-title">👑 VISI LĪMEŅI PIEVEIKTI! 👑</h3>
						<p>Izcils sniegums! Visi 10 klasiskie Arkanoid līmeņi ir notīrīti.</p>
						<div class="score-card highlight-card">
							<span class="card-title">Gala Rezultāts</span>
							<strong id="arkanoid-victory-score" class="card-num">0</strong>
						</div>
						<button id="arkanoid-continue-btn" class="arkanoid-btn primary-pulse">
							TURPINĀT BEZGALĪGAJĀ REŽĪMĀ
						</button>
					</div>
				</div>

				<!-- PAUSE OVERLAY -->
				<div id="arkanoid-pause-overlay" class="arkanoid-overlay" style="display: none;">
					<div class="arkanoid-overlay-content">
						<h3>⏸ SPĒLE PAUZĒTA</h3>
						<p>Nospied <kbd>P</kbd> vai pogu zemāk, lai atgrieztos cīņā.</p>
						<button id="arkanoid-resume-btn" class="arkanoid-btn primary">TURPINĀT</button>
					</div>
				</div>
			</div>

			<!-- TOUCH CONTROLS (Mobile) -->
			<div class="arkanoid-touch-controls">
				<button id="touch-left" class="touch-btn" aria-label="Pa kreisi">◀</button>
				<button id="touch-fire" class="touch-btn touch-fire" aria-label="Šaut vai palaist">🚀 PALAIST / LĀZERS</button>
				<button id="touch-right" class="touch-btn" aria-label="Pa labi">▶</button>
			</div>

			<!-- POWER-UPS GUIDE -->
			<div class="arkanoid-powerups-guide">
				<h4><span class="guide-icon">💊</span> Arkanoid Kapsulu Ceļvedis</h4>
				<div class="powerups-grid">
					<div class="powerup-item">
						<span class="powerup-pill pill-slow">S</span>
						<div>
							<strong>Slow (Lēnāk)</strong>
							<p>Samazina enerģijas bumbas ātrumu līdz kontrolējamam līmenim.</p>
						</div>
					</div>
					<div class="powerup-item">
						<span class="powerup-pill pill-catch">C</span>
						<div>
							<strong>Catch (Pielipt)</strong>
							<p>Bumba pielīp platformai. Palaid to ar klikšķi vai Spacebar.</p>
						</div>
					</div>
					<div class="powerup-item">
						<span class="powerup-pill pill-expand">E</span>
						<div>
							<strong>Expand (Paplašināt)</strong>
							<p>Padara Vaus platformu par 50% platāku vieglākai bumbas atsišanai.</p>
						</div>
					</div>
					<div class="powerup-item">
						<span class="powerup-pill pill-disruption">D</span>
						<div>
							<strong>Disruption (3 Bumbas)</strong>
							<p>Bumba sadalās 3 neatkarīgās bumbās. Dzīvība zūd tikai zaudējot visas!</p>
						</div>
					</div>
					<div class="powerup-item">
						<span class="powerup-pill pill-laser">L</span>
						<div>
							<strong>Laser (Lāzeri)</strong>
							<p>Aprīko Vaus ar dubultajiem lāzeriem. Spied Space vai klikšķini, lai šautu blokus!</p>
						</div>
					</div>
					<div class="powerup-item">
						<span class="powerup-pill pill-break">B</span>
						<div>
							<strong>Break (Portāls)</strong>
							<p>Atver teleporta izeju uz nākamo līmeni ar +10 000 bonusa punktiem!</p>
						</div>
					</div>
					<div class="powerup-item">
						<span class="powerup-pill pill-player">P</span>
						<div>
							<strong>Player (+1 Dzīvība)</strong>
							<p>Piešķir papildu rezerves Vaus kuģīti.</p>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- SIDEBAR: LEADERBOARDS -->
		<div class="arkanoid-sidebar">
			<!-- TODAY TOP -->
			<div class="arkanoid-top-card">
				<div class="top-card-header">
					<h4>🏆 Šodienas Tops</h4>
					<span class="badge badge-info">Šodien</span>
				</div>
				<div class="top-table-responsive">
					<table class="table table-striped table-condensed table-hover arkanoid-top-table">
						<thead>
							<tr>
								<th style="width: 35px; text-align: center;">#</th>
								<th>Spēlētājs</th>
								<th style="text-align: right;">Punkti</th>
							</tr>
						</thead>
						<tbody>
							<!-- START BLOCK : today-top-node -->
							<tr{user-special}>
								<td style="text-align: center;">{user-place}</td>
								<td><a href="{user-url}">{user-nick}</a></td>
								<td style="text-align: right; font-weight: bold;">{score}</td>
							</tr>
							<!-- END BLOCK : today-top-node -->
							<!-- START BLOCK : today-empty -->
							<tr>
								<td colspan="3" class="empty-top-cell">Šodien vēl neviens nav spēlējis. Esi pirmais!</td>
							</tr>
							<!-- END BLOCK : today-empty -->
						</tbody>
					</table>
				</div>
			</div>

			<!-- ALL-TIME TOP -->
			<div class="arkanoid-top-card">
				<div class="top-card-header">
					<h4>⭐ Visu Laiku Tops</h4>
					<span class="badge badge-warning">Top 20</span>
				</div>
				<div class="top-table-responsive">
					<table class="table table-striped table-condensed table-hover arkanoid-top-table">
						<thead>
							<tr>
								<th style="width: 35px; text-align: center;">#</th>
								<th>Spēlētājs</th>
								<th style="text-align: right;">Punkti</th>
							</tr>
						</thead>
						<tbody>
							<!-- START BLOCK : alltime-top-node -->
							<tr{user-special}>
								<td style="text-align: center;">{user-place}</td>
								<td><a href="{user-url}">{user-nick}</a></td>
								<td style="text-align: right; font-weight: bold;">{score}</td>
							</tr>
							<!-- END BLOCK : alltime-top-node -->
							<!-- START BLOCK : alltime-empty -->
							<tr>
								<td colspan="3" class="empty-top-cell">Topā vēl nav ierakstu.</td>
							</tr>
							<!-- END BLOCK : alltime-empty -->
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
