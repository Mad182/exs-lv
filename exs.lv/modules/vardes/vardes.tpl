<div class="vardes-wrapper">
	<div class="vardes-header">
		<h2><span class="vardes-icon">🐸</span> Vardes</h2>
		<p class="vardes-subtitle">Šķērso bīstamo šoseju un upi ar baļķiem, lai sasniegtu liliju lapas un uzstādītu rekordu!</p>
	</div>

	<div class="vardes-game-layout">
		<div class="vardes-main-stage">
			<div class="vardes-canvas-container">
				<canvas id="vardes-canvas" width="440" height="560"></canvas>

				<div id="vardes-start-overlay" class="vardes-overlay">
					<div class="vardes-overlay-content">
						<div class="vardes-avatar-preview">
							🐸
						</div>
						<h3>Sākt Pārgājienu?</h3>
						<p>Izmanto <kbd>▲</kbd> <kbd>◄</kbd> <kbd>▼</kbd> <kbd>►</kbd> bultiņas vai <kbd>WASD</kbd>, lai lēktu no lauciņa uz lauciņu.</p>
						<button id="vardes-start-btn" class="vardes-btn primary">Sākt Spēli</button>
					</div>
				</div>

				<div id="vardes-gameover-overlay" class="vardes-overlay" style="display: none;">
					<div class="vardes-overlay-content">
						<h3 class="gameover-title">Spēle Beigusies!</h3>
						<div class="vardes-score-board">
							<div class="score-box">
								<span class="score-label">Punkti</span>
								<span id="vardes-final-score" class="score-num">0</span>
							</div>
							<div class="score-box">
								<span class="score-label">Tavs Rekords</span>
								<span id="vardes-best-score" class="score-num">{user-high-score}</span>
							</div>
						</div>
						<div id="vardes-record-alert" class="vardes-new-record" style="display: none;">
							🎉 Jauns Personīgais Rekords!
						</div>
						<button id="vardes-restart-btn" class="vardes-btn primary">Spēlēt Vēlreiz (Space)</button>
					</div>
				</div>
			</div>

			<div class="vardes-dpad-controls">
				<div class="dpad-row">
					<button id="dpad-up" class="dpad-btn">▲</button>
				</div>
				<div class="dpad-row">
					<button id="dpad-left" class="dpad-btn">◄</button>
					<button id="dpad-down" class="dpad-btn">▼</button>
					<button id="dpad-right" class="dpad-btn">►</button>
				</div>
			</div>

			<div class="vardes-controls-bar">
				<div class="stat-pill">
					<span class="pill-label">Rekords:</span>
					<strong id="stat-highscore">{user-high-score}</strong>
				</div>
				<div class="stat-pill">
					<span class="pill-label">Skaņa:</span>
					<button id="vardes-sound-toggle" class="vardes-btn btn-small">🔊 Ieslēgta</button>
				</div>
			</div>
		</div>

		<div class="vardes-sidebar">
			<div class="vardes-card">
				<h3>🏆 Šodienas Tops</h3>
				<ul class="vardes-top-list">
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

			<div class="vardes-card" style="margin-top: 15px;">
				<h3>👑 Visu Laiku Tops</h3>
				<ul class="vardes-top-list">
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
		</div>
	</div>
</div>

<script type="text/javascript">
	window.VARDES_USER_AVATAR = "{user-avatar}";
	window.VARDES_USER_HIGHSCORE = {user-high-score};
</script>
