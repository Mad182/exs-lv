<div class="ut99-wrapper">
	<div class="ut99-header">
		<div class="ut99-title-group">
			<span class="ut99-logo-badge">
				<img src="/bildes/icons/games/ut99.png" alt="UT99" width="36" height="36" />
			</span>
			<div>
				<h2>Unreal Tournament 99 <span class="ut99-badge-wasm">WASM</span></h2>
				<p class="ut99-subtitle">Leģendārā 3D arēnas šaujamspēle tieši tavā pārlūkā ar WebAssembly &amp; WebGL!</p>
			</div>
		</div>

		<div class="ut99-quick-actions">
			<button id="ut99-theater-toggle" class="ut99-btn btn-dark" title="Teātra režīms">📺 Palielināt skatu</button>
			<button id="ut99-fullscreen-toggle" class="ut99-btn btn-primary" title="Pilnekrāna režīms">⛶ Pilnekrāns</button>
		</div>
	</div>

	<div class="ut99-game-layout">
		<div class="ut99-main-stage">
			<div class="ut99-canvas-container" id="ut99-stage-container">
				<!-- Emscripten WebGL Canvas -->
				<canvas id="canvas" class="emscripten" oncontextmenu="event.preventDefault()" tabindex="1" width="800" height="500"></canvas>

				<!-- START / LAUNCH OVERLAY -->
				<div id="ut99-start-overlay" class="ut99-overlay">
					<div class="ut99-overlay-content">
						<div class="ut99-hero-banner">
							<span class="ut99-pulsing-badge">🎮 ARENA READY</span>
							<h3 class="ut99-glow-title">Gatavs Arēnai?</h3>
							<p>Izvēlies spēles režīmu un dodies cīņā ar botiem vai citiem EXS lietotājiem!</p>
						</div>

						<!-- MODE SELECTOR TABS -->
						<div class="ut99-tabs">
							<button class="ut99-tab-btn active" data-tab="tab-botmatch">🤖 Cīņa ar Botiem</button>
							<button class="ut99-tab-btn" data-tab="tab-multiplayer">🌐 EXS Multiplayer</button>
							<button class="ut99-tab-btn" data-tab="tab-settings">⚙️ Iestatījumi</button>
						</div>

						<!-- TAB 1: BOT MATCH -->
						<div id="tab-botmatch" class="ut99-tab-pane active">
							<div class="ut99-form-grid">
								<div class="ut99-form-group">
									<label for="ut99-map-select">Arēnas Karte:</label>
									<select id="ut99-map-select" class="ut99-select">
										<option value="CityIntro" selected>CityIntro (Ievads / Flyby 3D Arēna)</option>
										<option value="UT-Logo-Map">UT-Logo-Map (Logo telpa)</option>
										<option value="Entry">Entry (Ieejas arēna)</option>
										<option value="UTCredits">UTCredits (Titru skats)</option>
									</select>
								</div>

								<div class="ut99-form-group">
									<label for="ut99-bot-count">Botu Skaits:</label>
									<select id="ut99-bot-count" class="ut99-select">
										<option value="3">3 Boti (1v1v1v1)</option>
										<option value="5" selected>5 Boti (Ieteicams)</option>
										<option value="7">7 Boti (Dinamisks)</option>
										<option value="11">11 Boti (Haoss)</option>
									</select>
								</div>

								<div class="ut99-form-group">
									<label for="ut99-bot-skill">Grūtības Pakāpe:</label>
									<select id="ut99-bot-skill" class="ut99-select">
										<option value="0">Iesācējs (Novice)</option>
										<option value="1" selected>Vidējs (Average)</option>
										<option value="2">Pieredzējis (Experienced)</option>
										<option value="3">Meistars (Master)</option>
										<option value="4">Dievišķs (Godlike)</option>
									</select>
								</div>

								<div class="ut99-form-group">
									<label for="ut99-frag-limit">Fragu Limits:</label>
									<select id="ut99-frag-limit" class="ut99-select">
										<option value="15" selected>15 Fragi</option>
										<option value="25">25 Fragi</option>
										<option value="50">50 Fragi</option>
									</select>
								</div>
							</div>

							<div class="ut99-action-row">
								<button id="ut99-launch-bot-btn" class="ut99-btn ut99-btn-glow primary">
									<span class="btn-icon">⚡</span> Palaist Spēli ar Botiem
								</button>
								<button id="ut99-direct-logo-btn" class="ut99-btn ut99-btn-glow secondary" style="margin-left: 10px;">
									<span class="btn-icon">⏭️</span> Sākt no Logo / Menu (Bez Ievada)
								</button>
							</div>
						</div>

						<!-- TAB 2: MULTIPLAYER -->
						<div id="tab-multiplayer" class="ut99-tab-pane">
							<div class="ut99-server-box">
								<div class="server-status-pill online" id="server-status-indicator">
									<span class="status-dot"></span> <span id="server-status-text">EXS Serveris Tiešsaistē</span>
								</div>
								<div class="server-info-details">
									<div class="server-stat">
										<span class="stat-label">Karte:</span>
										<strong id="server-map-name">DM-Deck16][</strong>
									</div>
									<div class="server-stat">
										<span class="stat-label">Spēlētāji:</span>
										<strong id="server-player-count">0 / 16</strong>
									</div>
									<div class="server-stat">
										<span class="stat-label">Savienojums:</span>
										<strong id="server-proxy-type">WebSocket / UDP Bridge</strong>
									</div>
								</div>
							</div>

							<div class="ut99-action-row">
								<button id="ut99-join-server-btn" class="ut99-btn ut99-btn-glow success">
									<span class="btn-icon">🌐</span> Pievienoties EXS Serverim
								</button>
							</div>
						</div>

						<!-- TAB 3: SETTINGS -->
						<div id="tab-settings" class="ut99-tab-pane">
							<div class="ut99-settings-grid">
								<div class="ut99-setting-item">
									<label for="ut99-mouse-sens">Peles Jūtība (Sensitivity): <span id="sens-val">3.0</span></label>
									<input type="range" id="ut99-mouse-sens" min="0.5" max="10" step="0.5" value="3.0" />
								</div>
								<div class="ut99-setting-item">
									<label for="ut99-volume-slider">Skaņas Skaļums (WebAudio): <span id="vol-val">80%</span></label>
									<input type="range" id="ut99-volume-slider" min="0" max="100" step="5" value="80" />
								</div>
								<div class="ut99-setting-item">
									<label for="ut99-res-select">Izšķirtspēja:</label>
									<select id="ut99-res-select" class="ut99-select">
										<option value="native" selected>800x500 (Optimāls)</option>
										<option value="hd">1280x720 (HD)</option>
										<option value="fhd">1920x1080 (Full HD)</option>
									</select>
								</div>
							</div>
						</div>

						<!-- ASSET LOADING / CACHING PROGRESS BAR -->
						<div id="ut99-loading-box" class="ut99-loading-box" style="display: none;">
							<div class="loading-label-group">
								<span id="ut99-loading-status" class="loading-title">Ielādē spēles resursus...</span>
								<span id="ut99-loading-percent" class="loading-percentage">0%</span>
							</div>
							<div class="ut99-progress-track">
								<div id="ut99-progress-bar" class="ut99-progress-fill" style="width: 0%;"></div>
							</div>
							<p class="ut99-cache-note">
								💾 <small>Spēles faili tiek automātiski saglabāti pārlūka <strong>IndexedDB</strong> atmiņā nākamajām spēlēm.</small>
							</p>
						</div>
					</div>
				</div>

				<!-- MATCH FINISHED / GAME OVER OVERLAY -->
				<div id="ut99-gameover-overlay" class="ut99-overlay" style="display: none;">
					<div class="ut99-overlay-content">
						<h3 class="ut99-glow-title gold">🏆 Cīņa Noslēgusies!</h3>
						<div class="ut99-stats-board">
							<div class="stat-card">
								<span class="stat-card-label">Tavi Fragi</span>
								<span id="ut99-final-frags" class="stat-card-num">0</span>
							</div>
							<div class="stat-card">
								<span class="stat-card-label">Nāves</span>
								<span id="ut99-final-deaths" class="stat-card-num">0</span>
							</div>
							<div class="stat-card">
								<span class="stat-card-label">K/D Attiecība</span>
								<span id="ut99-final-kd" class="stat-card-num">0.0</span>
							</div>
							<div class="stat-card">
								<span class="stat-card-label">Tavs Rekords</span>
								<span id="ut99-best-frags" class="stat-card-num">{user-high-score}</span>
							</div>
						</div>

						<div id="ut99-record-alert" class="ut99-new-record" style="display: none;">
							🎉 Apsveicam! Jauns Personīgais Fragu Rekords!
						</div>

						<div class="ut99-action-row">
							<button id="ut99-restart-btn" class="ut99-btn primary">Spēlēt Vēlreiz</button>
						</div>
					</div>
				</div>
			</div>

			<!-- CONTROLS & HUD STATUS BAR -->
			<div class="ut99-controls-bar">
				<div class="stat-pill">
					<span class="pill-label">Rekords:</span>
					<strong id="stat-highscore">{user-high-score} fragi</strong>
				</div>
				<div class="stat-pill">
					<span class="pill-label">Pele:</span>
					<span id="ut99-pointer-status" class="pill-badge">Klikšķini, lai nofiksētu</span>
				</div>
				<div class="stat-pill">
					<span class="pill-label">Skaņa:</span>
					<button id="ut99-sound-btn" class="ut99-btn btn-small">🔊 Ieslēgta</button>
				</div>
				<div class="stat-pill">
					<button id="ut99-skip-intro-btn" class="ut99-btn btn-small btn-primary" title="Pāriet uzreiz uz Logo / Menu">⏭️ Izlaist Ievadu</button>
				</div>
				<div class="stat-pill">
					<button id="ut99-reopen-menu-btn" class="ut99-btn btn-small">⚙️ Izvēlne</button>
				</div>
			</div>
		</div>

		<!-- SIDEBAR: LEADERBOARDS & CONTROLS -->
		<div class="ut99-sidebar">
			<!-- TODAY'S TOP -->
			<div class="ut99-card">
				<h3>🏆 Šodienas Tops</h3>
				<ul class="ut99-top-list">
					<!-- START BLOCK : today-top-node -->
					<li>
						<span class="top-rank">{rank}.</span>
						<span class="top-user">{user-nick}</span>
						<strong class="top-score">{score} fr.</strong>
					</li>
					<!-- END BLOCK : today-top-node -->
					<!-- START BLOCK : today-empty -->
					<li class="empty-msg">Šodien vēl nav fiksētu rezultātu!</li>
					<!-- END BLOCK : today-empty -->
				</ul>
			</div>

			<!-- ALL-TIME TOP -->
			<div class="ut99-card" style="margin-top: 15px;">
				<h3>👑 Visu Laiku Tops</h3>
				<ul class="ut99-top-list">
					<!-- START BLOCK : alltime-top-node -->
					<li>
						<span class="top-rank">{rank}.</span>
						<span class="top-user">{user-nick}</span>
						<strong class="top-score">{score} fr.</strong>
					</li>
					<!-- END BLOCK : alltime-top-node -->
					<!-- START BLOCK : alltime-empty -->
					<li class="empty-msg">Vēl nav uzstādītu rekordu!</li>
					<!-- END BLOCK : alltime-empty -->
				</ul>
			</div>

			<!-- CONTROLS CHEATSHEET -->
			<div class="ut99-card" style="margin-top: 15px;">
				<h3>⌨️ Spēles Vadība</h3>
				<div class="ut99-keys-guide">
					<div class="key-row"><kbd>W</kbd><kbd>A</kbd><kbd>S</kbd><kbd>D</kbd> <span>Kustība</span></div>
					<div class="key-row"><kbd>Pele</kbd> <span>Tēmēšana / Skats</span></div>
					<div class="key-row"><kbd>Kreisais Kl.</kbd> <span>Primārais šāviens</span></div>
					<div class="key-row"><kbd>Labais Kl.</kbd> <span>Alternatīvais šāviens</span></div>
					<div class="key-row"><kbd>Space</kbd> <span>Lēciens</span></div>
					<div class="key-row"><kbd>C</kbd> / <kbd>Ctrl</kbd> <span>Pieliekšanās (Crouch)</span></div>
					<div class="key-row"><kbd>1</kbd> - <kbd>0</kbd> <span>Ieroču izvēle</span></div>
					<div class="key-row"><kbd>Tab</kbd> <span>Rezultātu tabula</span></div>
					<div class="key-row"><kbd>Esc</kbd> <span>Atbrīvot peli / Pauze</span></div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	window.UT99_USER_AVATAR = "{user-avatar}";
	window.UT99_USER_HIGHSCORE = {user-high-score};
	window.UT99_IS_LOGGED = {is-logged};
</script>
