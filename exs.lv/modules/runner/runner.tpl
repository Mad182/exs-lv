<h1>Runner</h1>

<div class="tabs">
	<li><a href="/runner" class="tab active">Spēle</a></li>
</div>

<!-- START BLOCK : guest-notice -->
<div class="alert alert-info runner-guest-alert" style="margin-bottom: 15px;">
	<strong>Piezīme:</strong> Tu spēlē kā viesis. <a href="/register">Reģistrējies</a> vai ieej profilā, lai saglabātu savu rezultātu topā!
</div>
<!-- END BLOCK : guest-notice -->

<div class="tabMain" id="runner-container">
	<div class="runner-wrapper">
		<!-- Game Toolbar HUD -->
		<div class="runner-hud">
			<div class="hud-stat">
				<span>Distance: <strong id="runner-score">0 m</strong></span>
			</div>
			<div class="hud-stat">
				<span>Zvaigznes: <strong id="runner-coins">⭐ 0</strong></span>
			</div>
			<div class="hud-stat">
				<span>Ātrums: <strong id="runner-speed">1.0x</strong></span>
			</div>
			<div class="hud-stat">
				<span>Rekords: <strong>{user-high-score} m</strong></span>
			</div>
		</div>

		<!-- Game Canvas Wrapper -->
		<div class="runner-canvas-container">
			<canvas id="runner-canvas" width="800" height="400" data-avatar="{user-avatar}"></canvas>

			<!-- Start Screen Overlay -->
			<div id="runner-start-overlay" class="runner-overlay">
				<div class="overlay-card">
					<h2>🏃 Runner</h2>
					<p>Bēdz no akadēmijas šķēršļiem un lietotāju avatariem, vāc zvaigznes un uzstādi jaunu rekordu!</p>
					<button id="runner-start-btn" class="runner-btn runner-btn-lg">Sākt Spēli 🚀</button>
				</div>
			</div>

			<!-- Game Over Overlay -->
			<div id="runner-gameover-overlay" class="runner-overlay" style="display: none;">
				<div class="overlay-card">
					<h2 class="gameover-title">💥 Sadursme!</h2>
					<p class="final-stats">Sasnniegtā distance: <strong id="final-score">0 m</strong></p>
					<p class="final-stats">Savāktās zvaigznes: <strong id="final-coins">0</strong></p>
					<div id="runner-record-badge" class="record-badge" style="display: none;">🎉 JAUNS REKORDS!</div>
					<button id="runner-restart-btn" class="runner-btn runner-btn-lg">Mēģināt Vēlreiz 🔄 (Enter)</button>
				</div>
			</div>
		</div>

		<!-- Mobile / On-Screen Touch Controls -->
		<div class="runner-controls">
			<button id="btn-duck" class="runner-ctrl-btn btn-duck">⬇️ Liekties (S / Down)</button>
			<button id="btn-jump" class="runner-ctrl-btn btn-jump">🦘 Lēkt (W / Up / Space)</button>
		</div>

		<!-- Instructions Guide Box -->
		<div class="runner-guide">
			<h4>Kā spēlēt:</h4>
			<ul>
				<li><strong>W / Bultiņa uz augšu / Atstarpe / Lēkt poga:</strong> Lēkt pāri zemes šķēršļiem un lietotāju avatariem.</li>
				<li><strong>S / Bultiņa uz leju / Liekties poga:</strong> Liekties zem lidojošajiem avatāru droīdiem.</li>
				<li><strong>Pārvarēšanas laiks & ātrums:</strong> Spēles ātrums pakāpeniski pieaug ar katru noskrieto kilometru!</li>
				<li><strong>Zvaigznes:</strong> Vāc zelta zvaigznes ⭐ par papildu punktiem!</li>
			</ul>
		</div>

		<!-- Leaderboards Section -->
		<div class="runner-leaderboards">
			<div class="leaderboard-col">
				<h3>📅 Šodienas Tops</h3>
				<table class="table table-striped">
					<thead>
						<tr>
							<th style="width: 40px;">Vieta</th>
							<th>Spēlētājs</th>
							<th style="text-align: center;">Zvaigznes</th>
							<th style="text-align: right;">Distance</th>
						</tr>
					</thead>
					<tbody>
						<!-- START BLOCK : today-top-node -->
						<tr>
							<td>{rank}.</td>
							<td>{user-nick}</td>
							<td style="text-align: center;">⭐ {coins}</td>
							<td style="text-align: right; font-weight: bold;">{score} m</td>
						</tr>
						<!-- END BLOCK : today-top-node -->
						<!-- START BLOCK : today-empty -->
						<tr>
							<td colspan="4" style="text-align: center; color: #888;">Šodien vēl nav uzstādīts neviens rezultāts. Esi pirmais!</td>
						</tr>
						<!-- END BLOCK : today-empty -->
					</tbody>
				</table>
			</div>

			<div class="leaderboard-col">
				<h3>🏆 Visu laiku Tops</h3>
				<table class="table table-striped">
					<thead>
						<tr>
							<th style="width: 40px;">Vieta</th>
							<th>Spēlētājs</th>
							<th style="text-align: center;">Zvaigznes</th>
							<th style="text-align: right;">Distance</th>
						</tr>
					</thead>
					<tbody>
						<!-- START BLOCK : alltime-top-node -->
						<tr>
							<td>{rank}.</td>
							<td>{user-nick}</td>
							<td style="text-align: center;">⭐ {coins}</td>
							<td style="text-align: right; font-weight: bold;">{score} m</td>
						</tr>
						<!-- END BLOCK : alltime-top-node -->
						<!-- START BLOCK : alltime-empty -->
						<tr>
							<td colspan="4" style="text-align: center; color: #888;">Visu laiku tops pagaidām ir tukšs!</td>
						</tr>
						<!-- END BLOCK : alltime-empty -->
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
