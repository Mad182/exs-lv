<div class="tornis-wrapper">
	<div class="tornis-header">
		<h2><span class="tornis-icon">🏢</span> Tornis <span class="tornis-subtitle-tag">Tower Stacker</span></h2>
		<p class="tornis-subtitle">Liec 3D blokus vienu virs otra ar perfektu laika izjūtu, veido combo sērijas un uzbūvē augstāko debesskrāpi!</p>
	</div>

	<div class="tornis-game-layout">
		<div class="tornis-main-stage">
			<div class="tornis-canvas-container" id="tornis-container">
				<canvas id="tornis-canvas" width="440" height="600"></canvas>

				<!-- In-game Floating HUD -->
				<div id="tornis-hud" class="tornis-hud" style="display: none;">
					<div class="hud-score-wrap">
						<span class="hud-score-label">STĀVI</span>
						<span id="hud-current-score" class="hud-score-val">0</span>
					</div>
					<div id="hud-combo-badge" class="hud-combo-badge" style="display: none;">
						<span id="hud-combo-text">COMBO x1</span>
					</div>
				</div>

				<!-- Start Overlay -->
				<div id="tornis-start-overlay" class="tornis-overlay">
					<div class="tornis-overlay-card">
						<div class="tornis-avatar-box">
							<img src="{user-avatar}" alt="Tavs avatārs" class="tornis-avatar-img" />
						</div>
						<h3>Gatavs celtniecībai?</h3>
						<p class="tornis-instructions">
							Kustīgais bloks slīd virs torņa. Spied <kbd>Spacebar</kbd>, klikšķini ar peli vai pieskaries ekrānam, lai to nomestu.
						</p>
						<div class="tornis-hints-box">
							<span>✨ <strong>Perfekta sakritība</strong> dod combo sēriju un augstākas muzikālās notis.</span>
							<span>✂️ <strong>Pārliektā daļa</strong> tiek nogriezta – bloks kļūst šaurāks!</span>
							<span>🎁 <strong>5 perfekti gājieni pēc kārtas</strong> nedaudz palielina bloku.</span>
						</div>
						<button id="tornis-start-btn" class="tornis-btn primary pulse">Sākt Būvēt</button>
					</div>
				</div>

				<!-- Game Over Overlay -->
				<div id="tornis-gameover-overlay" class="tornis-overlay" style="display: none;">
					<div class="tornis-overlay-card">
						<h3 class="gameover-heading">Tornis Sabruka!</h3>
						<div class="tornis-results-grid">
							<div class="result-cell">
								<span class="res-label">Uzbūvētie stāvi</span>
								<strong id="final-score-val" class="res-val highlight">0</strong>
							</div>
							<div class="result-cell">
								<span class="res-label">Maksimālais Combo</span>
								<strong id="final-combo-val" class="res-val">0</strong>
							</div>
							<div class="result-cell">
								<span class="res-label">Augstums</span>
								<strong id="final-meters-val" class="res-val">0 m</strong>
							</div>
							<div class="result-cell">
								<span class="res-label">Tavs Rekords</span>
								<strong id="final-best-val" class="res-val">{user-high-score}</strong>
							</div>
						</div>

						<div id="tornis-new-record-banner" class="tornis-record-banner" style="display: none;">
							🎉 Jauns Personīgais Rekords!
						</div>

						<div id="tornis-rank-info" class="tornis-rank-info" style="display: none;">
							Tava vieta topā: <strong id="tornis-rank-val">#1</strong>
						</div>

						<button id="tornis-restart-btn" class="tornis-btn primary">Būvēt Vēlreiz (Space)</button>
					</div>
				</div>

				<!-- Pause Overlay -->
				<div id="tornis-pause-overlay" class="tornis-overlay" style="display: none;">
					<div class="tornis-overlay-card">
						<h3>Pauze</h3>
						<p>Celtniecība apturēta.</p>
						<button id="tornis-resume-btn" class="tornis-btn primary">Turpināt</button>
					</div>
				</div>
			</div>

			<!-- Mobile & Desktop Bottom Controls Bar -->
			<div class="tornis-controls-strip">
				<div class="control-stat">
					<span class="stat-tag">Tavs rekords:</span>
					<strong id="bottom-best-score">{user-high-score} stāvi</strong>
				</div>
				<div class="control-actions">
					<button id="tornis-sound-btn" class="tornis-btn secondary icon-btn" title="Ieslēgt/Izslēgt skaņu">
						<span id="sound-icon">🔊</span> Skaņa
					</button>
					<button id="tornis-pause-btn" class="tornis-btn secondary icon-btn" title="Pauze (P)" style="display: none;">
						⏸️ Pauze
					</button>
				</div>
			</div>

			<!-- Mobile Big Drop Button -->
			<div class="tornis-mobile-touch-bar">
				<button id="tornis-mobile-drop-btn" class="tornis-mobile-btn">
					⏬ NOLIKT BLOKU
				</button>
			</div>
		</div>

		<!-- Leaderboard Sidebar -->
		<div class="tornis-sidebar">
			<div class="tornis-sidebar-card">
				<div class="card-header-flex">
					<h3>🏆 Šodienas Tops</h3>
					<span class="card-sub-badge">Šodien</span>
				</div>
				<ul class="tornis-top-list">
					<!-- START BLOCK : today-top-node -->
					<li{user-special}>
						<span class="top-rank-icon">{user-place}</span>
						<a href="{user-url}" class="top-user-name">{user-nick}</a>
						<strong class="top-score-num">{score} <small>stāvi</small></strong>
					</li>
					<!-- END BLOCK : today-top-node -->
					<!-- START BLOCK : today-empty -->
					<li class="empty-notice">Šodien vēl neviens nav uzbūvējis torni. Esi pirmais!</li>
					<!-- END BLOCK : today-empty -->
				</ul>
			</div>

			<div class="tornis-sidebar-card" style="margin-top: 15px;">
				<div class="card-header-flex">
					<h3>👑 Visu Laiku Tops</h3>
					<span class="card-sub-badge alltime">Visu laiku</span>
				</div>
				<ul class="tornis-top-list">
					<!-- START BLOCK : alltime-top-node -->
					<li{user-special}>
						<span class="top-rank-icon">{user-place}</span>
						<a href="{user-url}" class="top-user-name">{user-nick}</a>
						<strong class="top-score-num">{score} <small>stāvi</small></strong>
					</li>
					<!-- END BLOCK : alltime-top-node -->
					<!-- START BLOCK : alltime-empty -->
					<li class="empty-notice">Vēl nav uzstādītu rekordu!</li>
					<!-- END BLOCK : alltime-empty -->
				</ul>
			</div>

			<div class="tornis-sidebar-card info-card" style="margin-top: 15px;">
				<h4>💡 Noderīgi padomi</h4>
				<ul class="tornis-tips-list">
					<li><strong>Ritmika:</strong> Bloki kustas ar vienmērīgu ātrumu – atrodi savu klikšķa ritmu.</li>
					<li><strong>Atmosfēras:</strong> Kāpjot augstāk, fons mainās no rīta debesīm uz saulrietu, stratosfēru un dziļu kosmosu.</li>
					<li><strong>Combo bonuss:</strong> Pēc katriem 5 perfektiem gājieniem saņem bloka paplašinājumu un glābšanas rezervi nākamajiem stāviem.</li>
				</ul>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	window.TORNIS_USER_AVATAR = "{user-avatar}";
	window.TORNIS_USER_HIGHSCORE = {user-high-score};
</script>
