<div class="augsup-wrapper">
	<div class="augsup-header">
		<h2><span class="augsup-icon">🦘</span> Augšup</h2>
		<p class="augsup-subtitle">Lēkā pa platformām ar savu avatāru, sasniedz mākoņus un uzstādi jaunu augstuma rekordu!</p>
	</div>

	<div class="augsup-game-layout">
		<div class="augsup-main-stage">
			<div class="augsup-canvas-container">
				<canvas id="augsup-canvas" width="400" height="560"></canvas>

				<div id="augsup-start-overlay" class="augsup-overlay">
					<div class="augsup-overlay-content">
						<div class="augsup-avatar-preview">
							<img id="augsup-player-avatar" src="{user-avatar}" alt="Pārlūka avatārs" />
						</div>
						<h3>Gatavs lēkt?</h3>
						<p>Izmanto <kbd>◄</kbd> / <kbd>►</kbd> bultiņas vai <kbd>A</kbd> / <kbd>D</kbd> taustiņus, lai stūrētu gaisā.</p>
						<button id="augsup-start-btn" class="augsup-btn primary">Sākt Spēli</button>
					</div>
				</div>

				<div id="augsup-gameover-overlay" class="augsup-overlay" style="display: none;">
					<div class="augsup-overlay-content">
						<h3 class="gameover-title">Spēle Beigusies!</h3>
						<div class="augsup-score-board">
							<div class="score-box">
								<span class="score-label">Augstums</span>
								<span id="augsup-final-score" class="score-num">0 m</span>
							</div>
							<div class="score-box">
								<span class="score-label">Tavs Rekords</span>
								<span id="augsup-best-score" class="score-num">{user-high-score} m</span>
							</div>
						</div>
						<div id="augsup-record-alert" class="augsup-new-record" style="display: none;">
							🎉 Jauns Personīgais Rekords!
						</div>
						<button id="augsup-restart-btn" class="augsup-btn primary">Spēlēt Vēlreiz (Space)</button>
					</div>
				</div>
			</div>

			<div class="augsup-touch-controls">
				<button id="touch-left" class="touch-btn">◄ Pa Kreisi</button>
				<button id="touch-right" class="touch-btn">Pa Labi ►</button>
			</div>

			<div class="augsup-controls-bar">
				<div class="stat-pill">
					<span class="pill-label">Rekords:</span>
					<strong id="stat-highscore">{user-high-score} m</strong>
				</div>
				<div class="stat-pill">
					<span class="pill-label">Skaņa:</span>
					<button id="augsup-sound-toggle" class="augsup-btn btn-small">🔊 Ieslēgta</button>
				</div>
			</div>
		</div>

		<div class="augsup-sidebar">
			<div class="augsup-card">
				<h3>🏆 Šodienas Tops</h3>
				<ul class="augsup-top-list">
					<!-- START BLOCK : today-top-node -->
					<li>
						<span class="top-rank">{rank}.</span>
						<span class="top-user">{user-nick}</span>
						<strong class="top-score">{score} m</strong>
					</li>
					<!-- END BLOCK : today-top-node -->
					<!-- START BLOCK : today-empty -->
					<li class="empty-msg">Šodien vēl nav uzstādītu rezultātu!</li>
					<!-- END BLOCK : today-empty -->
				</ul>
			</div>

			<div class="augsup-card" style="margin-top: 15px;">
				<h3>👑 Visu Laiku Tops</h3>
				<ul class="augsup-top-list">
					<!-- START BLOCK : alltime-top-node -->
					<li>
						<span class="top-rank">{rank}.</span>
						<span class="top-user">{user-nick}</span>
						<strong class="top-score">{score} m</strong>
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
	window.AUGSUP_USER_AVATAR = "{user-avatar}";
	window.AUGSUP_USER_HIGHSCORE = {user-high-score};
</script>
