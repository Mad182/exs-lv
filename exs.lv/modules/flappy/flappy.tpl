<div class="flappy-wrapper">
	<div class="flappy-header">
		<h2><span class="flappy-icon">🐥</span> Lidojošais Eksis</h2>
		<p class="flappy-subtitle">Spied <strong>Spacebar</strong> vai klikšķini, lai lidotu cauri šķēršļiem un uzstādītu jaunu rekordu!</p>
	</div>

	<div class="flappy-game-layout">
		<div class="flappy-main-stage">
			<div class="flappy-canvas-container">
				<canvas id="flappy-canvas" width="400" height="560"></canvas>

				<div id="flappy-start-overlay" class="flappy-overlay">
					<div class="flappy-overlay-content">
						<div class="flappy-avatar-preview">
							<img id="flappy-player-avatar" src="{user-avatar}" alt="Pārlūka avatārs" />
						</div>
						<h3>Gatavs lidojumam?</h3>
						<p>Izmanto <kbd>Spacebar</kbd>, <kbd>↑</kbd> bultiņu vai klikšķini uz ekrāna, lai celtos gaisā.</p>
						<button id="flappy-start-btn" class="flappy-btn primary">Sākt Spēli</button>
					</div>
				</div>

				<div id="flappy-gameover-overlay" class="flappy-overlay" style="display: none;">
					<div class="flappy-overlay-content">
						<h3 class="gameover-title">Spēle Beigusies!</h3>
						<div class="flappy-score-board">
							<div class="score-box">
								<span class="score-label">Punkti</span>
								<span id="flappy-final-score" class="score-num">0</span>
							</div>
							<div class="score-box">
								<span class="score-label">Tavs Rekords</span>
								<span id="flappy-best-score" class="score-num">{user-high-score}</span>
							</div>
						</div>
						<div id="flappy-record-alert" class="flappy-new-record" style="display: none;">
							🎉 Jauns Personīgais Rekords!
						</div>
						<button id="flappy-restart-btn" class="flappy-btn primary">Spēlēt Vēlreiz (Space)</button>
					</div>
				</div>
			</div>

			<div class="flappy-controls-bar">
				<div class="stat-pill">
					<span class="pill-label">Rekords:</span>
					<strong id="stat-highscore">{user-high-score}</strong>
				</div>
				<div class="stat-pill">
					<span class="pill-label">Skaņa:</span>
					<button id="flappy-sound-toggle" class="flappy-btn btn-small">🔊 Ieslēgta</button>
				</div>
			</div>
		</div>

		<div class="flappy-sidebar">
			<div class="flappy-card">
				<h3>🏆 Šodienas Tops</h3>
				<ul class="flappy-top-list">
					<!-- START BLOCK : today-top-node -->
					<li>
						<span class="top-rank">{rank}.</span>
						<span class="top-user"><a href="{user-url}">{user-nick}</a></span>
						<strong class="top-score">{score}</strong>
					</li>
					<!-- END BLOCK : today-top-node -->
					<!-- START BLOCK : today-empty -->
					<li class="empty-msg">Šodien vēl nav uzstādītu rezultātu!</li>
					<!-- END BLOCK : today-empty -->
				</ul>
			</div>

			<div class="flappy-card" style="margin-top: 15px;">
				<h3>👑 Visu Laiku Tops</h3>
				<ul class="flappy-top-list">
					<!-- START BLOCK : alltime-top-node -->
					<li>
						<span class="top-rank">{rank}.</span>
						<span class="top-user"><a href="{user-url}">{user-nick}</a></span>
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
	window.FLAPPY_USER_AVATAR = "{user-avatar}";
	window.FLAPPY_USER_HIGHSCORE = {user-high-score};
</script>
