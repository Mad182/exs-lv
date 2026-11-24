<div class="ut99-wrapper">
	<div class="ut99-header">
		<div class="ut99-title-group">
			<span class="ut99-logo-badge">
				<img src="/bildes/icons/games/ut99.png" alt="UT99" width="38" height="38" />
			</span>
			<div>
				<h2>UNREAL TOURNAMENT <span class="ut99-title-year">99</span> <span class="ut99-badge-wasm">WASM</span></h2>
				<p class="ut99-subtitle">LIANDRI GRAND TOURNAMENT // EXS.LV ARĒNA PĀRLŪKĀ (WebAssembly &amp; WebGL)</p>
			</div>
		</div>

		<div class="ut99-quick-actions">
			<button id="ut99-theater-toggle" class="ut99-btn btn-dark" title="Teātra režīms">[ ] Palielināt skatu</button>
			<button id="ut99-fullscreen-toggle" class="ut99-btn primary" title="Pilnekrāna režīms">⛶ Pilnekrāns</button>
		</div>
	</div>

	<div class="ut99-game-layout">
		<div class="ut99-main-stage">
			<div class="ut99-canvas-container" id="ut99-stage-container">
				<!-- Emscripten WebGL Canvas -->
				<canvas id="canvas" class="emscripten" oncontextmenu="event.preventDefault()" tabindex="1" width="800" height="500"></canvas>

				<!-- START / LAUNCH OVERLAY (UT99 UMenu Style) -->
				<div id="ut99-start-overlay" class="ut99-overlay">
					<div class="ut99-umenu-window">
						<!-- UMenu Titlebar -->
						<div class="ut99-umenu-titlebar">
							<div class="ut99-umenu-brand">
								<img src="/bildes/icons/games/ut99.png" alt="UT" width="18" height="18" />
								<span>UNREAL TOURNAMENT // EXS.LV</span>
							</div>
							<div class="ut99-umenu-ver">v468 WASM</div>
						</div>

						<!-- UMenu Tab Bar (EXS Multiplayer Default) -->
						<div class="ut99-tabs">
							<button class="ut99-tab-btn active" data-tab="tab-multiplayer">EXS MULTIPLAYER</button>
							<button class="ut99-tab-btn" data-tab="tab-botmatch">BOTU CĪŅA</button>
							<button class="ut99-tab-btn" data-tab="tab-settings">IESTATĪJUMI</button>
						</div>

						<div class="ut99-umenu-body">
							<!-- TAB 1 (DEFAULT): MULTIPLAYER -->
							<div id="tab-multiplayer" class="ut99-tab-pane active">
								<div class="ut99-mp-panel">
									<div class="ut99-map-frame">
										<img id="server-map-preview" src="/bildes/ut/dm-deck16][.jpg" alt="DM-Deck16][" onerror="this.src='/bildes/ut/nopic.jpg'" />
										<div class="ut99-map-frame-label" id="server-map-name">DM-Deck16][</div>
									</div>
									<div class="ut99-mp-info">
										<div class="server-status-pill online" id="server-status-indicator">
											<span class="status-dot"></span>
											<span id="server-status-text">EXS SERVERIS TIEŠSAISTĒ</span>
										</div>
										<div class="ut99-kv-list">
											<div class="ut99-kv-row">
												<span class="kv-key">ADRESE</span>
												<strong class="kv-val">exs.lv:7777</strong>
											</div>
											<div class="ut99-kv-row">
												<span class="kv-key">REŽĪMS</span>
												<strong class="kv-val">DeathMatch</strong>
											</div>
											<div class="ut99-kv-row">
												<span class="kv-key">SPĒLĒTĀJI</span>
												<strong class="kv-val" id="server-player-count">0 / 10</strong>
											</div>
											<div class="ut99-kv-row">
												<span class="kv-key">SAVIEN.</span>
												<strong class="kv-val" id="server-proxy-type">WSS → UDP</strong>
											</div>
										</div>
									</div>
								</div>

								<div class="ut99-action-row">
									<button id="ut99-join-server-btn" class="ut99-btn ut99-btn-launch primary">
										► PIEVIENOTIES EXS SERVERIM
									</button>
								</div>
							</div>

							<!-- TAB 2: BOT MATCH -->
							<div id="tab-botmatch" class="ut99-tab-pane">
								<div class="ut99-bot-layout">
									<div class="ut99-map-frame ut99-bot-preview">
										<img id="bot-map-preview" src="/bildes/ut/dm-deck16][.jpg" alt="DM-Deck16][" onerror="this.src='/bildes/ut/nopic.jpg'" />
										<div class="ut99-map-frame-label" id="bot-map-label">DM-Deck16][</div>
									</div>
									<div class="ut99-form-grid">
										<div class="ut99-form-group">
											<label for="ut99-map-select">ARĒNAS KARTE</label>
											<select id="ut99-map-select" class="ut99-select">
												<option value="DM-Deck16][" selected>DM-Deck16][ (Klasiskā)</option>
												<option value="DM-Morpheus">DM-Morpheus (Zema Gravitācija)</option>
												<option value="DM-Turbine">DM-Turbine (Kompakta)</option>
												<option value="DM-Curse][">DM-Curse][ (Pazemes)</option>
												<option value="DM-Liandri">DM-Liandri (Reaktors)</option>
												<option value="DM-Phobos">DM-Phobos (Kosmosa Stacija)</option>
												<option value="DM-Fractal">DM-Fractal (Dinamiska)</option>
												<option value="DM-Oblivion">DM-Oblivion (Turnīrs)</option>
												<option value="DM-Tutorial">DM-Tutorial (Apmācība)</option>
												<option value="CTF-Face">CTF-Face (Facing Worlds)</option>
												<option value="CTF-Gauntlet">CTF-Gauntlet (CTF)</option>
												<option value="CityIntro">CityIntro (3D Ievads)</option>
												<option value="UT-Logo-Map">UT-Logo-Map (Logo telpa)</option>
											</select>
										</div>

										<div class="ut99-form-group">
											<label for="ut99-bot-count">BOTU SKAITS</label>
											<select id="ut99-bot-count" class="ut99-select">
												<option value="3">3 Boti (1v1v1v1)</option>
												<option value="5" selected>5 Boti (Ieteicams)</option>
												<option value="7">7 Boti (Dinamisks)</option>
												<option value="11">11 Boti (Haoss)</option>
											</select>
										</div>

										<div class="ut99-form-group">
											<label for="ut99-bot-skill">GRŪTĪBA</label>
											<select id="ut99-bot-skill" class="ut99-select">
												<option value="0">Novice (Iesācējs)</option>
												<option value="1" selected>Average (Vidējs)</option>
												<option value="2">Experienced (Pieredzējis)</option>
												<option value="3">Masterful (Meistars)</option>
												<option value="4">Godlike (Dievišķs)</option>
											</select>
										</div>

										<div class="ut99-form-group">
											<label for="ut99-frag-limit">FRAGU LIMITS</label>
											<select id="ut99-frag-limit" class="ut99-select">
												<option value="15" selected>15 Fragi</option>
												<option value="25">25 Fragi</option>
												<option value="50">50 Fragi</option>
											</select>
										</div>
									</div>
								</div>

								<div class="ut99-action-row">
									<button id="ut99-launch-bot-btn" class="ut99-btn ut99-btn-launch primary">
										► SĀKT CĪŅU AR BOTIEM
									</button>
									<button id="ut99-direct-logo-btn" class="ut99-btn secondary">
										UT99 IZVĒLNE
									</button>
								</div>
							</div>

							<!-- TAB 3: SETTINGS -->
							<div id="tab-settings" class="ut99-tab-pane">
								<div class="ut99-settings-grid">
									<div class="ut99-setting-item">
										<label for="ut99-mouse-sens">PELES JŪTĪBA (SENSITIVITY): <span id="sens-val">3.0</span></label>
										<input type="range" id="ut99-mouse-sens" min="0.5" max="10" step="0.5" value="3.0" />
									</div>
									<div class="ut99-setting-item">
										<label for="ut99-volume-slider">SKAŅAS SKAĻUMS (WEBAUDIO): <span id="vol-val">80%</span></label>
										<input type="range" id="ut99-volume-slider" min="0" max="100" step="5" value="80" />
									</div>
									<div class="ut99-setting-item">
										<label for="ut99-res-select">IZŠĶIRTSPĒJA</label>
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
									Spēles faili tiek automātiski saglabāti pārlūka <strong>IndexedDB</strong> atmiņā nākamajām spēlēm.
								</p>
							</div>
						</div>

						<!-- UMenu Status Footer -->
						<div class="ut99-umenu-footer">
							<span>STATUS: READY</span>
							<span>CONTROLS: WASD + MOUSE • ESC = MENU</span>
						</div>
					</div>
				</div>

				<!-- MATCH FINISHED / GAME OVER OVERLAY -->
				<div id="ut99-gameover-overlay" class="ut99-overlay" style="display: none;">
					<div class="ut99-umenu-window">
						<div class="ut99-umenu-titlebar">
							<div class="ut99-umenu-brand">
								<img src="/bildes/icons/games/ut99.png" alt="UT" width="18" height="18" />
								<span>MATCH COMPLETE // REZULTĀTI</span>
							</div>
						</div>
						<div class="ut99-umenu-body">
							<h3 class="ut99-glow-title gold">CĪŅA NOSLĒGUSIES</h3>
							<div class="ut99-stats-board">
								<div class="stat-card">
									<span class="stat-card-label">TAVI FRAGI</span>
									<span id="ut99-final-frags" class="stat-card-num">0</span>
								</div>
								<div class="stat-card">
									<span class="stat-card-label">NĀVES</span>
									<span id="ut99-final-deaths" class="stat-card-num">0</span>
								</div>
								<div class="stat-card">
									<span class="stat-card-label">K/D</span>
									<span id="ut99-final-kd" class="stat-card-num">0.0</span>
								</div>
								<div class="stat-card">
									<span class="stat-card-label">REKORDS</span>
									<span id="ut99-best-frags" class="stat-card-num">{user-high-score}</span>
								</div>
							</div>

							<div id="ut99-record-alert" class="ut99-new-record" style="display: none;">
								GODLIKE! JAUNS PERSONĪGAIS FRAGU REKORDS!
							</div>

							<div class="ut99-action-row">
								<button id="ut99-restart-btn" class="ut99-btn ut99-btn-launch primary">► SPĒLĒT VĒLREIZ</button>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- CONTROLS & HUD STATUS BAR -->
			<div class="ut99-controls-bar">
				<div class="stat-pill">
					<span class="pill-label">REKORDS:</span>
					<strong id="stat-highscore">{user-high-score} fragi</strong>
				</div>
				<div class="stat-pill">
					<span class="pill-label">PELE:</span>
					<span id="ut99-pointer-status" class="pill-badge">Klikšķini, lai nofiksētu</span>
				</div>
				<div class="stat-pill">
					<button id="ut99-trigger-esc-btn" class="ut99-btn btn-small primary" title="Nosūtīt ESC spēles dzinējam">ESC / MENU</button>
				</div>
				<div class="stat-pill">
					<button id="ut99-sound-btn" class="ut99-btn btn-small">🔊 Skaņa</button>
				</div>
				<div class="stat-pill">
					<button id="ut99-skip-intro-btn" class="ut99-btn btn-small" title="Pāriet uzreiz uz Logo / Menu">⏭️ Logo</button>
				</div>
				<div class="stat-pill">
					<button id="ut99-reopen-menu-btn" class="ut99-btn btn-small">⚙ Izvēlne</button>
				</div>
			</div>
		</div>

		<!-- SIDEBAR: LEADERBOARDS & CONTROLS -->
		<div class="ut99-sidebar">
			<!-- TODAY'S TOP -->
			<div class="ut99-card">
				<h3>🏆 ŠODIENAS TOPS</h3>
				<ul class="ut99-top-list">
					{today-top-html}
				</ul>
			</div>

			<!-- ALL-TIME TOP -->
			<div class="ut99-card" style="margin-top: 14px;">
				<h3>👑 VISU LAIKU TOPS</h3>
				<ul class="ut99-top-list">
					{alltime-top-html}
				</ul>
			</div>

			<!-- CONTROLS CHEATSHEET -->
			<div class="ut99-card" style="margin-top: 14px;">
				<h3>⌨️ SPĒLES VADĪBA</h3>
				<div class="ut99-keys-guide">
					<div class="key-row"><kbd>W</kbd><kbd>A</kbd><kbd>S</kbd><kbd>D</kbd> <span>Kustība</span></div>
					<div class="key-row"><kbd>Pele</kbd> <span>Tēmēšana / Skats</span></div>
					<div class="key-row"><kbd>LMB</kbd> <span>Primārais šāviens</span></div>
					<div class="key-row"><kbd>RMB</kbd> <span>Alternatīvais šāviens</span></div>
					<div class="key-row"><kbd>Space</kbd> <span>Lēciens</span></div>
					<div class="key-row"><kbd>C</kbd> / <kbd>Ctrl</kbd> <span>Pieliekšanās</span></div>
					<div class="key-row"><kbd>1</kbd>-<kbd>0</kbd> <span>Ieroču izvēle</span></div>
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
	window.UT99_USER_NICK = {user-nick-js};
</script>
