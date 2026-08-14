<div class="invaders-wrapper">
	<div class="invaders-header">
		<h2><span class="invaders-icon">👾</span> Space Invaders</h2>
		<p class="invaders-subtitle">Izmanto <kbd>←</kbd> <kbd>→</kbd> vai <kbd>A</kbd> <kbd>D</kbd> lai kustētos, un <kbd>Spacebar</kbd> / <kbd>↑</kbd> lai šautu!</p>
	</div>

	<div class="invaders-game-layout">
		<div class="invaders-main-stage">
			<div class="invaders-canvas-container">
				<canvas id="invaders-canvas" width="500" height="600"></canvas>

				<!-- START OVERLAY -->
				<div id="invaders-start-overlay" class="invaders-overlay">
					<div class="invaders-overlay-content">
						<div class="invaders-title-art">
							<span class="invader-art-sprite squid">
								<svg width="32" height="32" viewBox="0 0 8 8" shape-rendering="crispEdges">
									<path fill="#00e5ff" d="M3,0 h2 v1 h-2 z M2,1 h4 v1 h-4 z M1,2 h6 v1 h-6 z M0,3 h2 v1 h-2 z M3,3 h2 v1 h-2 z M6,3 h2 v1 h-2 z M0,4 h8 v1 h-8 z M2,5 h1 v1 h-1 z M5,5 h1 v1 h-1 z M1,6 h1 v1 h-1 z M3,6 h2 v1 h-2 z M6,6 h1 v1 h-1 z M0,7 h1 v1 h-1 z M2,7 h1 v1 h-1 z M5,7 h1 v1 h-1 z M7,7 h1 v1 h-1 z" />
								</svg>
							</span>
							<span class="invader-art-sprite crab">
								<svg width="32" height="32" viewBox="0 0 8 8" shape-rendering="crispEdges">
									<path fill="#ff007f" d="M2,0 h1 v1 h-1 z M5,0 h1 v1 h-1 z M3,1 h2 v1 h-2 z M2,2 h4 v1 h-4 z M1,3 h2 v1 h-2 z M5,3 h2 v1 h-2 z M0,4 h8 v1 h-8 z M0,5 h1 v1 h-1 z M2,5 h4 v1 h-4 z M7,5 h1 v1 h-1 z M0,6 h1 v1 h-1 z M2,6 h1 v1 h-1 z M5,6 h1 v1 h-1 z M7,6 h1 v1 h-1 z M3,7 h2 v1 h-2 z" />
								</svg>
							</span>
							<span class="invader-art-sprite octopus">
								<svg width="32" height="32" viewBox="0 0 8 8" shape-rendering="crispEdges">
									<path fill="#00ff66" d="M3,0 h2 v1 h-2 z M1,1 h6 v1 h-6 z M0,2 h8 v1 h-8 z M0,3 h2 v1 h-2 z M3,3 h2 v1 h-2 z M6,3 h2 v1 h-2 z M0,4 h8 v1 h-8 z M2,5 h1 v1 h-1 z M5,5 h1 v1 h-1 z M1,6 h1 v1 h-1 z M3,6 h2 v1 h-2 z M6,6 h1 v1 h-1 z M1,7 h1 v1 h-1 z M6,7 h1 v1 h-1 z" />
								</svg>
							</span>
						</div>
						<h3>Gatavs Cīņai?</h3>
						<p>Izmanto bultiņas vai taustiņus <kbd>A</kbd> / <kbd>D</kbd> kustībai un <kbd>Spacebar</kbd> šaušanai.</p>
						<p class="infinite-badge">⚡ BEZGALĪGAIS REŽĪMS – Katrs nākamais vilnis ir ātrāks un grūtāks!</p>
						<button id="invaders-start-btn" class="invaders-btn primary">Sākt Spēli</button>
					</div>
				</div>

				<!-- GAME OVER OVERLAY -->
				<div id="invaders-gameover-overlay" class="invaders-overlay" style="display: none;">
					<div class="invaders-overlay-content">
						<h3 class="gameover-title">Spēle Beigusies!</h3>
						<div class="invaders-score-board">
							<div class="score-box">
								<span class="score-label">Punkti</span>
								<span id="invaders-final-score" class="score-num">0</span>
							</div>
							<div class="score-box">
								<span class="score-label">Sasniedzis Vilni</span>
								<span id="invaders-final-wave" class="score-num">1</span>
							</div>
							<div class="score-box">
								<span class="score-label">Tavs Rekords</span>
								<span id="invaders-best-score" class="score-num">{user-high-score}</span>
							</div>
						</div>
						<div id="invaders-record-alert" class="invaders-new-record" style="display: none;">
							🎉 Jauns Personīgais Rekords!
						</div>
						<button id="invaders-restart-btn" class="invaders-btn primary">Spēlēt Vēlreiz (Space)</button>
					</div>
				</div>

				<!-- PAUSE OVERLAY -->
				<div id="invaders-pause-overlay" class="invaders-overlay" style="display: none;">
					<div class="invaders-overlay-content">
						<h3>⏸ Spēle Pauzēta</h3>
						<p>Nospied <kbd>P</kbd> vai pogu zemāk, lai turpinātu spēli.</p>
						<button id="invaders-resume-btn" class="invaders-btn primary">Turpināt Spēli</button>
					</div>
				</div>
			</div>

			<!-- CONTROLS & STATS BAR -->
			<div class="invaders-controls-bar">
				<div class="stat-pill">
					<span class="pill-label">Rekords:</span>
					<strong id="stat-highscore">{user-high-score}</strong>
				</div>
				<div class="stat-pill">
					<span class="pill-label">Skaņa:</span>
					<button id="invaders-sound-toggle" class="invaders-btn btn-small">🔊 Ieslēgta</button>
				</div>
				<div class="stat-pill">
					<button id="invaders-pause-toggle-btn" class="invaders-btn btn-small">⏸ Pauze</button>
				</div>
			</div>

			<!-- TOUCH / MOBILE CONTROLS -->
			<div class="invaders-touch-controls">
				<button id="touch-left-btn" class="touch-btn">◀ Kustēties pa kreisi</button>
				<button id="touch-fire-btn" class="touch-btn fire-btn">🔥 ŠAUT</button>
				<button id="touch-right-btn" class="touch-btn">Kustēties pa labi ▶</button>
			</div>
		</div>

		<!-- SIDEBAR -->
		<div class="invaders-sidebar">
			<!-- TODAY'S TOP -->
			<div class="invaders-card">
				<h3>🏆 Šodienas Tops</h3>
				<ul class="invaders-top-list">
					<!-- START BLOCK : today-top-node -->
					<li>
						<span class="top-rank">{rank}.</span>
						<span class="top-user">{user-nick}</span>
						<strong class="top-score">{score}</strong>
					</li>
					<!-- END BLOCK : today-top-node -->
					<!-- START BLOCK : today-empty -->
					<li class="empty-msg">Šodien vēl nav uzstādītu rezultātu!</li>
					<!-- END BLOCK : today-empty -->
				</ul>
			</div>

			<!-- ALL-TIME TOP -->
			<div class="invaders-card" style="margin-top: 15px;">
				<h3>👑 Visu Laiku Tops</h3>
				<ul class="invaders-top-list">
					<!-- START BLOCK : alltime-top-node -->
					<li>
						<span class="top-rank">{rank}.</span>
						<span class="top-user">{user-nick}</span>
						<strong class="top-score">{score}</strong>
					</li>
					<!-- END BLOCK : alltime-top-node -->
					<!-- START BLOCK : alltime-empty -->
					<li class="empty-msg">Vēl nav uzstādītu rezultātu!</li>
					<!-- END BLOCK : alltime-empty -->
				</ul>
			</div>

			<!-- POINT VALUES REFERENCE CARD -->
			<div class="invaders-card" style="margin-top: 15px;">
				<h3>🎯 Punktu Vērtības</h3>
				<ul class="invaders-info-list">
					<li>
						<span class="invader-icon">
							<svg class="invader-svg ufo-svg" width="22" height="14" viewBox="0 0 12 8" shape-rendering="crispEdges">
								<path fill="#ff0055" d="M3,1 h6 v1 h-6 z M1,2 h10 v2 h-10 z M0,4 h12 v2 h-12 z" />
								<path fill="#ffe600" d="M3,3 h2 v1 h-2 z M7,3 h2 v1 h-2 z" />
							</svg>
						</span>
						NLO Kosmosa Kuģis: <strong>100 - 300 p</strong>
					</li>
					<li>
						<span class="invader-icon">
							<svg class="invader-svg squid-svg" width="18" height="18" viewBox="0 0 8 8" shape-rendering="crispEdges">
								<path fill="#00e5ff" d="M3,0 h2 v1 h-2 z M2,1 h4 v1 h-4 z M1,2 h6 v1 h-6 z M0,3 h2 v1 h-2 z M3,3 h2 v1 h-2 z M6,3 h2 v1 h-2 z M0,4 h8 v1 h-8 z M2,5 h1 v1 h-1 z M5,5 h1 v1 h-1 z M1,6 h1 v1 h-1 z M3,6 h2 v1 h-2 z M6,6 h1 v1 h-1 z M0,7 h1 v1 h-1 z M2,7 h1 v1 h-1 z M5,7 h1 v1 h-1 z M7,7 h1 v1 h-1 z" />
							</svg>
						</span>
						Kalmārs (Augšējā rinda): <strong>30 p</strong>
					</li>
					<li>
						<span class="invader-icon">
							<svg class="invader-svg crab-svg" width="18" height="18" viewBox="0 0 8 8" shape-rendering="crispEdges">
								<path fill="#ff007f" d="M2,0 h1 v1 h-1 z M5,0 h1 v1 h-1 z M3,1 h2 v1 h-2 z M2,2 h4 v1 h-4 z M1,3 h2 v1 h-2 z M5,3 h2 v1 h-2 z M0,4 h8 v1 h-8 z M0,5 h1 v1 h-1 z M2,5 h4 v1 h-4 z M7,5 h1 v1 h-1 z M0,6 h1 v1 h-1 z M2,6 h1 v1 h-1 z M5,6 h1 v1 h-1 z M7,6 h1 v1 h-1 z M3,7 h2 v1 h-2 z" />
							</svg>
						</span>
						Krabis (Vidus rindas): <strong>20 p</strong>
					</li>
					<li>
						<span class="invader-icon">
							<svg class="invader-svg octopus-svg" width="18" height="18" viewBox="0 0 8 8" shape-rendering="crispEdges">
								<path fill="#00ff66" d="M3,0 h2 v1 h-2 z M1,1 h6 v1 h-6 z M0,2 h8 v1 h-8 z M0,3 h2 v1 h-2 z M3,3 h2 v1 h-2 z M6,3 h2 v1 h-2 z M0,4 h8 v1 h-8 z M2,5 h1 v1 h-1 z M5,5 h1 v1 h-1 z M1,6 h1 v1 h-1 z M3,6 h2 v1 h-2 z M6,6 h1 v1 h-1 z M1,7 h1 v1 h-1 z M6,7 h1 v1 h-1 z" />
							</svg>
						</span>
						Astoņkājis (Apakšējās rindas): <strong>10 p</strong>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	window.INVADERS_USER_AVATAR = "{user-avatar}";
	window.INVADERS_USER_HIGHSCORE = {user-high-score};
	window.INVADERS_IS_LOGGED = {is-logged};
</script>
