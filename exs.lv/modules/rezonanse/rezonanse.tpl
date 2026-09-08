<div class="rezonanse-wrapper">
	<div class="rezonanse-header">
		<h2><span class="rezonanse-icon"><img src="/bildes/icons/games/rezonanse.png" alt="Rezonanse" width="28" height="28" style="vertical-align: middle; border-radius: 6px;" /></span> Rezonanse <span class="rezonanse-subtitle-tag">Synth Pulse Arena</span> {game-rate}</h2>
		<p class="rezonanse-subtitle">Hipnotiska ritma un izdzīvošanas arkāde, kur spēles pasaule ir reāllaika sintezators! Ķer ritmu, radi melodijas un veido harmoniskas kaskādes.</p>
	</div>

	<!-- START BLOCK : guest-notice -->
	<div class="alert alert-info rezonanse-guest-alert">
		<strong>Piezīme:</strong> Tu spēlē kā viesis. <a href="https://exs.lv/register">Reģistrējies</a> vai ieej profilā, lai saglabātu savu rezultātu topā!
	</div>
	<!-- END BLOCK : guest-notice -->

	<div class="rezonanse-main-stage">
		<div class="rezonanse-canvas-container" id="rezonanse-container">
			<canvas id="rezonanse-canvas" width="560" height="560"></canvas>

			<!-- Floating HUD -->
			<div id="rezonanse-hud" class="rezonanse-hud" style="display: none;">
				<div class="hud-item hud-score-box">
					<span class="hud-label">PUNKTI</span>
					<span id="hud-score-val" class="hud-value">0</span>
				</div>

				<div class="hud-item hud-combo-box">
					<span class="hud-label">COMBO</span>
					<span id="hud-combo-val" class="hud-value combo-glow">1x</span>
				</div>

				<div class="hud-item hud-shield-box">
					<span class="hud-label">VAIROGS</span>
					<div id="hud-shields" class="hud-shields-row">
						<span class="shield-dot active"></span>
						<span class="shield-dot active"></span>
						<span class="shield-dot active"></span>
					</div>
				</div>

				<div class="hud-item hud-overdrive-box">
					<span class="hud-label">OVERDRIVE</span>
					<div class="overdrive-bar-track">
						<div id="hud-overdrive-fill" class="overdrive-bar-fill" style="width: 0%;"></div>
					</div>
				</div>

				<div class="hud-item hud-bpm-box">
					<span id="hud-beat-indicator" class="beat-light" title="Bīta indikators"></span>
					<span class="bpm-text">128 BPM</span>
				</div>
			</div>

			<!-- Start Overlay -->
			<div id="rezonanse-start-overlay" class="rezonanse-overlay">
				<div class="rezonanse-overlay-card">
					<div class="rezonanse-logo-anim">
						<div class="pulse-ring r1"></div>
						<div class="pulse-ring r2"></div>
						<div class="center-core">⚡</div>
					</div>
					<h3>REZONANSE</h3>
					<p class="overlay-intro">Vadi harmonijas lodi, ķer ritma viļņus un atvairi disonanses triecienus. Katrs tavs sitiens rada mūziku!</p>

					<div class="rezonanse-guide-grid">
						<div class="guide-item">
							<span class="guide-icon">🖱️</span>
							<strong>Kustība</strong>
							<small>Pele, bultiņas vai pirksts vada lodi arēnā</small>
						</div>
						<div class="guide-item">
							<span class="guide-icon">🎵</span>
							<strong>Ritma Pulss</strong>
							<small><kbd>Space</kbd> vai klikšķis precīzi uz bīta iznīcina šķēršļus</small>
						</div>
						<div class="guide-item">
							<span class="guide-icon">🔥</span>
							<strong>Overdrive</strong>
							<small>Savāc skaņu kristālus, lai aktivizētu neievainojamību</small>
						</div>
					</div>

					<button id="rezonanse-start-btn" class="rezonanse-btn primary-glow">
						SĀKT SPĒLI (Klikšķis / Space)
					</button>
				</div>
			</div>

			<!-- Game Over Overlay -->
			<div id="rezonanse-gameover-overlay" class="rezonanse-overlay" style="display: none;">
				<div class="rezonanse-overlay-card">
					<h3 class="gameover-title">REZONANSE PĀRTRŪKA</h3>

					<div class="rezonanse-stats-grid">
						<div class="stat-card">
							<span class="stat-title">Punkti</span>
							<strong id="final-score" class="stat-num glow-cyan">0</strong>
						</div>
						<div class="stat-card">
							<span class="stat-title">Maks. Combo</span>
							<strong id="final-combo" class="stat-num glow-pink">0x</strong>
						</div>
						<div class="stat-card">
							<span class="stat-title">Perfekti Ritmi</span>
							<strong id="final-perfects" class="stat-num">0</strong>
						</div>
						<div class="stat-card">
							<span class="stat-title">Tavs Rekords</span>
							<strong id="final-best" class="stat-num">{user-high-score}</strong>
						</div>
					</div>

					<div id="rezonanse-new-record" class="new-record-alert" style="display: none;">
						✨ JAUNS PERSONĪGAIS REKORDS! ✨
					</div>

					<div id="rezonanse-rank-banner" class="rank-badge-banner" style="display: none;">
						Tava vieta topā: <strong id="rezonanse-rank-val">#1</strong>
					</div>

					<button id="rezonanse-restart-btn" class="rezonanse-btn primary-glow">
						MĒĢINĀT VĒLREIZ (Space)
					</button>
				</div>
			</div>

			<!-- Pause Overlay -->
			<div id="rezonanse-pause-overlay" class="rezonanse-overlay" style="display: none;">
				<div class="rezonanse-overlay-card small">
					<h3>PAUZE</h3>
					<p>Skaņu ritms apturēts.</p>
					<button id="rezonanse-resume-btn" class="rezonanse-btn primary-glow">Turpināt</button>
				</div>
			</div>
		</div>

		<!-- Controls Strip -->
		<div class="rezonanse-bottom-bar">
			<div class="bar-stat">
				<span class="bar-label">Rekords:</span>
				<strong id="bottom-best-score">{user-high-score} pt</strong>
			</div>
			<div class="bar-actions">
				<button id="rezonanse-sound-btn" class="bar-btn" title="Skaņa (M)">
					<span id="sound-icon">🔊</span> Skaņa
				</button>
				<button id="rezonanse-pause-btn" class="bar-btn" title="Pauze (P)" style="display: none;">
					⏸️ Pauze
				</button>
			</div>
		</div>

		<!-- Mobile Touch Action Bar -->
		<div class="rezonanse-mobile-bar">
			<button id="rezonanse-mobile-pulse-btn" class="mobile-action-btn pulse-action">
				⚡ PULSS (RITMĀ)
			</button>
			<button id="rezonanse-mobile-overdrive-btn" class="mobile-action-btn overdrive-action" disabled>
				💥 OVERDRIVE
			</button>
		</div>
	</div>

	<!-- Scores and Rules below the game -->
	<div class="rezonanse-bottom-section">
		<!-- Leaderboard Card -->
		<div class="rezonanse-scores-card">
			<div class="sidebar-tabs">
				<button id="tab-today" class="sidebar-tab active">Šodien</button>
				<button id="tab-alltime" class="sidebar-tab">Visu laiku</button>
			</div>

			<!-- Today Leaderboard Content -->
			<div id="content-today" class="tab-content active">
				<table class="table rezonanse-table">
					<thead>
						<tr>
							<th style="width: 40px;">#</th>
							<th>Spēlētājs</th>
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
							<td colspan="3" class="empty-cell">Šodien vēl nav rezultātu. Esi pirmais!</td>
						</tr>
						<!-- END BLOCK : today-empty -->
					</tbody>
				</table>
			</div>

			<!-- All-Time Leaderboard Content -->
			<div id="content-alltime" class="tab-content">
				<table class="table rezonanse-table">
					<thead>
						<tr>
							<th style="width: 40px;">#</th>
							<th>Spēlētājs</th>
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
							<td colspan="3" class="empty-cell">Pagaidām nav rekordu.</td>
						</tr>
						<!-- END BLOCK : alltime-empty -->
					</tbody>
				</table>
			</div>
		</div>

		<!-- Rules / How to play Card -->
		<div class="rezonanse-mechanics-card">
			<h4>💡 Kā spēlēt Rezonansi?</h4>
			<ul>
				<li><strong>Turiet ritmu:</strong> Koncentriskais gredzens lido uz centru ik pēc 1/4 takts. Kad tas šķērso tavu lodi, spied <code>Space</code> vai klikšķini!</li>
				<li><strong>Harmoniskā atgrūšana:</strong> Veiksmīgs ritma pulss izveido triecienvilni, kas satriec sarkanos disonanses ērkšķus.</li>
				<li><strong>Mūzikas slāņi:</strong> Sasniedzot 5x, 15x un 30x combo, dziesmai automātiski pievienojas dziļš bass, arpeggios un melodiskas sintezatora kaskādes.</li>
				<li><strong>Overdrive:</strong> Kad uzkrāta enerģija, aktivizē Overdrive ar dubultklikšķi vai <code>Shift</code>, lai 6 sekundes vāktu dubultus punktus pilnīgā neievainojamībā!</li>
			</ul>
		</div>
	</div>
</div>
