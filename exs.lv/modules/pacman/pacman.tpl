<div class="pacman-wrapper">
	<div class="pacman-header">
		<h2><span class="pacman-title-icon">🟡</span> Exs-Man (Pac-Man) {game-rate}</h2>
		<p class="pacman-subtitle">
			Vadi savu profila tēlu ar bultiņām <kbd>←</kbd> <kbd>↑</kbd> <kbd>→</kbd> <kbd>↓</kbd> vai <kbd>W</kbd> <kbd>A</kbd> <kbd>S</kbd> <kbd>D</kbd>, ēd punktus, bēdz no spokiem un vāc augļu bonusus!
		</p>
	</div>

	<!-- START BLOCK : guest-notice -->
	<div class="alert alert-info pacman-guest-notice">
		<i class="icon-info-sign"></i> Tu neesi pieslēdzies sistēmai. Lai tavi rekordi tiktu saglabāti topā un rādīti aktivitāšu plūsmā, lūdzu, <a href="/login">autorizējies</a> vai <a href="/register">reģistrējies</a>!
	</div>
	<!-- END BLOCK : guest-notice -->

	<div class="pacman-game-layout">
		<!-- MAIN GAME STAGE -->
		<div class="pacman-main-stage">
			<!-- HUD BAR -->
			<div class="pacman-hud-bar">
				<div class="hud-item">
					<span class="hud-label">PUNKTI</span>
					<strong id="stat-score" class="hud-value highlight">0</strong>
				</div>
				<div class="hud-item">
					<span class="hud-label">REKORDS</span>
					<strong id="stat-highscore" class="hud-value">{user-high-score}</strong>
				</div>
				<div class="hud-item">
					<span class="hud-label">LĪMENIS</span>
					<strong id="stat-level" class="hud-value">1</strong>
				</div>
				<div class="hud-item hud-lives-item">
					<span class="hud-label">DZĪVĪBAS</span>
					<div id="stat-lives" class="hud-lives-container">
						<span class="pacman-life-icon"></span>
						<span class="pacman-life-icon"></span>
						<span class="pacman-life-icon"></span>
					</div>
				</div>
				<div class="hud-actions">
					<button id="pacman-sound-btn" class="hud-btn" title="Skaņa (M)">🔊</button>
					<button id="pacman-pause-btn" class="hud-btn" title="Pauze (P)">⏸</button>
				</div>
			</div>

			<!-- CANVAS STAGE -->
			<div class="pacman-canvas-container">
				<canvas id="pacman-canvas" width="448" height="576"></canvas>

				<!-- START OVERLAY -->
				<div id="pacman-start-overlay" class="pacman-overlay">
					<div class="pacman-overlay-content">
						<div class="pacman-arcade-title">
							<span class="arcade-letter l-e">E</span>
							<span class="arcade-letter l-x">X</span>
							<span class="arcade-letter l-s">S</span>
							<span class="arcade-letter l-dash">-</span>
							<span class="arcade-letter l-m">M</span>
							<span class="arcade-letter l-a">A</span>
							<span class="arcade-letter l-n">N</span>
						</div>

						<div class="pacman-marquee">
							<div class="marquee-character avatar-chomper">
								<img src="{user-avatar}" alt="Pacman" class="avatar-preview-img" onerror="this.src='/bildes/icons/games/pacman.png'" />
							</div>
							<div class="marquee-ghost blinky-sprite"></div>
							<div class="marquee-ghost pinky-sprite"></div>
							<div class="marquee-ghost inky-sprite"></div>
							<div class="marquee-ghost clyde-sprite"></div>
						</div>

						<p class="pacman-start-hint">
							Autentiski arkādes noteikumi, visi oriģinālie līmeņi, spoku AI un augļu bonusi.<br>
							Tavs profila avatars ir galvenais varonis!
						</p>

						<div class="pacman-controls-preview">
							<span>Kustība: <kbd>←</kbd> <kbd>↑</kbd> <kbd>→</kbd> <kbd>↓</kbd> vai <kbd>W</kbd><kbd>A</kbd><kbd>S</kbd><kbd>D</kbd> / Ekrāna vilkšana</span>
						</div>

						<button id="pacman-start-btn" class="pacman-btn primary pulse">
							SĀKT SPĒLI (Space)
						</button>
					</div>
				</div>

				<!-- GAME OVER OVERLAY -->
				<div id="pacman-gameover-overlay" class="pacman-overlay" style="display: none;">
					<div class="pacman-overlay-content">
						<h3 class="gameover-title">SPĒLE BEIGUSIES!</h3>
						<div class="pacman-score-board">
							<div class="score-box">
								<span class="score-label">Iegūtie Punkti</span>
								<strong id="final-score" class="score-val">0</strong>
							</div>
							<div class="score-box">
								<span class="score-label">Sasniedzis Līmeni</span>
								<strong id="final-level" class="score-val">1</strong>
							</div>
							<div class="score-box">
								<span class="score-label">Tavs Rekords</span>
								<strong id="final-best" class="score-val">{user-high-score}</strong>
							</div>
						</div>

						<div id="record-alert" class="pacman-record-alert" style="display: none;">
							🎉 Apsveicam! Jauns personīgais rekords!
						</div>

						<button id="pacman-restart-btn" class="pacman-btn primary">
							Spēlēt Vēlreiz (Space)
						</button>
					</div>
				</div>

				<!-- PAUSE OVERLAY -->
				<div id="pacman-pause-overlay" class="pacman-overlay" style="display: none;">
					<div class="pacman-overlay-content">
						<h3 class="pause-title">⏸ SPĒLE PAUZĒTA</h3>
						<p>Nospied <kbd>P</kbd> vai pogu zemāk, lai turpinātu spēli.</p>
						<button id="pacman-resume-btn" class="pacman-btn primary">Turpināt</button>
					</div>
				</div>
			</div>

			<!-- TOUCH / MOBILE D-PAD -->
			<div class="pacman-touch-controls">
				<div class="dpad-container">
					<button id="btn-up" class="dpad-btn dpad-up" aria-label="Uz augšu">▲</button>
					<div class="dpad-middle-row">
						<button id="btn-left" class="dpad-btn dpad-left" aria-label="Pa kreisi">◀</button>
						<div class="dpad-center"></div>
						<button id="btn-right" class="dpad-btn dpad-right" aria-label="Pa labi">▶</button>
					</div>
					<button id="btn-down" class="dpad-btn dpad-down" aria-label="Uz leju">▼</button>
				</div>
			</div>
		</div>

		<!-- SIDEBAR: LEADERBOARDS & RULES -->
		<div class="pacman-sidebar">
			<!-- TODAY'S TOP -->
			<div class="pacman-card">
				<h3 class="card-title">🏆 Šodienas Tops</h3>
				<ul class="pacman-top-list">
					<!-- START BLOCK : today-top-node -->
					<li{user-special}>
						<span class="top-rank">{user-place}</span>
						<span class="top-user"><a href="{user-url}">{user-nick}</a></span>
						<strong class="top-score">{score}</strong>
					</li>
					<!-- END BLOCK : today-top-node -->
					<!-- START BLOCK : today-empty -->
					<li class="empty-msg">Šodien vēl nav uzstādītu rezultātu! Esi pirmais!</li>
					<!-- END BLOCK : today-empty -->
				</ul>
			</div>

			<!-- ALL-TIME TOP -->
			<div class="pacman-card" style="margin-top: 15px;">
				<h3 class="card-title">👑 Visu Laiku Rekordi</h3>
				<ul class="pacman-top-list">
					<!-- START BLOCK : alltime-top-node -->
					<li{user-special}>
						<span class="top-rank">{user-place}</span>
						<span class="top-user"><a href="{user-url}">{user-nick}</a></span>
						<strong class="top-score">{score}</strong>
					</li>
					<!-- END BLOCK : alltime-top-node -->
					<!-- START BLOCK : alltime-empty -->
					<li class="empty-msg">Vēl nav neviena rezultāta.</li>
					<!-- END BLOCK : alltime-empty -->
				</ul>
			</div>

			<!-- PACMAN RULES & BONUSES REFERENCE CARD -->
			<div class="pacman-card" style="margin-top: 15px;">
				<h3 class="card-title">🍒 Noteikumi un Punkti</h3>
				<div class="pacman-info-section">
					<div class="info-row">
						<span class="info-icon dot-preview"></span>
						<div class="info-text">
							<strong>Mazais punkts:</strong> 10 punkti (kopā 240)
						</div>
					</div>
					<div class="info-row">
						<span class="info-icon energizer-preview"></span>
						<div class="info-text">
							<strong>Enerģijas granula:</strong> 50 punkti (spoki kļūst zili!)
						</div>
					</div>
					<div class="info-row">
						<span class="info-icon ghost-eaten-preview"></span>
						<div class="info-text">
							<strong>Spoku ēšana:</strong> 200 &rarr; 400 &rarr; 800 &rarr; 1600 p
						</div>
					</div>
					<div class="info-row">
						<span class="info-icon extra-life-preview">💛</span>
						<div class="info-text">
							<strong>Papildu dzīvība:</strong> Pie 10 000 punktiem!
						</div>
					</div>
				</div>

				<h4 class="sub-title">Līmeņu Augļu Bonusi (70 &amp; 170 punkti):</h4>
				<ul class="fruit-list">
					<li>🍒 <strong>Ķirsis (1. līm.):</strong> 100 p</li>
					<li>🍓 <strong>Zemene (2. līm.):</strong> 300 p</li>
					<li>🍊 <strong>Apelsīns (3.-4. līm.):</strong> 500 p</li>
					<li>🍏 <strong>Ābols (5.-6. līm.):</strong> 700 p</li>
					<li>🍈 <strong>Melone (7.-8. līm.):</strong> 1000 p</li>
					<li>🛸 <strong>Galaxian (9.-10. līm.):</strong> 2000 p</li>
					<li>🔔 <strong>Zvans (11.-12. līm.):</strong> 3000 p</li>
					<li>🗝️ <strong>Atslēga (13.+ līm.):</strong> 5000 p</li>
				</ul>

				<h4 class="sub-title">Spoku Raksturi:</h4>
				<ul class="ghost-list">
					<li><strong style="color: #ff3333;">Blinky (Sarkans):</strong> Sekotājs – tieši vajā Exs-Man, paātrinās, kad atlicis maz punktu.</li>
					<li><strong style="color: #ff99cc;">Pinky (Rozā):</strong> Slazdots – mēģina nogriezt ceļu 4 lauciņus uz priekšu.</li>
					<li><strong style="color: #00e5ff;">Inky (Gaiši zils):</strong> Viltnieks – sadarbojas ar Blinky pincer uzbrukumam.</li>
					<li><strong style="color: #ffaa00;">Clyde (Oranžs):</strong> Bailulis – tuvojas, bet nobīstas un bēg savā stūrī.</li>
				</ul>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	window.PACMAN_USER_AVATAR = "{user-avatar}";
	window.PACMAN_USER_HIGHSCORE = {user-high-score};
	window.PACMAN_IS_LOGGED = {is-logged};
</script>
