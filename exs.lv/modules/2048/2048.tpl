<h1>2048 - skaitļu apvienošanas spēle</h1>

<div class="tabs">
	<li><a href="/2048-spele" class="tab{active-tab-game}">Spēle</a></li>
	<li><a href="/2048-spele/top" class="tab{active-tab-top}">Šodienas tops</a></li>
	<li><a href="/2048-spele/overall-top" class="tab{active-tab-overall-top}">Visu laiku tops</a></li>
</div>

<div class="tabMain" id="twenty48-container">
	<!-- START BLOCK : top-table-->
	<table class="table table-striped table-hover tetris-top-table">
		<thead>
			<tr>
				<th style="width: 50px;">Vieta</th>
				<th>Spēlētājs</th>
				<th style="width: 120px; text-align: right;">Rezultāts</th>
				<th style="width: 150px; text-align: right;">Laiks</th>
			</tr>
		</thead>
		<tbody>
			<!-- START BLOCK : top-node-->
			<tr>
				<td{user-special}>{user-place}</td>
				<td{user-special}><a href="{user-url}">{user-nick}</a></td>
				<td{user-special} style="text-align: right; font-weight: bold;">{user-score}</td>
				<td{user-special} style="text-align: right; color: #888;">{user-time}</td>
			</tr>
			<!-- END BLOCK : top-node-->
		</tbody>
	</table>
	<!-- END BLOCK : top-table-->

	<!-- START BLOCK : game-login-->
	<div class="alert alert-info karatavas-guest-alert" style="margin-bottom: 15px;">
		<strong>Piezīme:</strong> Tu spēlē kā viesis. <a href="/register">Reģistrējies</a> vai ieej profilā, lai saglabātu savu rezultātu topā!
	</div>
	<!-- END BLOCK : game-login-->

	<!-- START BLOCK : game-play-->
	<div class="twenty48-game-wrapper">
		<div class="twenty48-header">
			<div class="twenty48-title-box">
				<h2 class="twenty48-logo">2048</h2>
				<p class="twenty48-subtitle">Bīdi skaitļus un apvieno līdz <strong>2048</strong>!</p>
			</div>
			<div class="twenty48-scores">
				<div class="twenty48-score-box">
					<span class="twenty48-score-label">REZULTĀTS</span>
					<span id="twenty48-current-score" class="twenty48-score-val">0</span>
				</div>
				<div class="twenty48-score-box">
					<span class="twenty48-score-label">LABĀKAIS</span>
					<span id="twenty48-best-score" class="twenty48-score-val">{user-high-score}</span>
				</div>
			</div>
		</div>

		<div class="twenty48-action-bar">
			<button id="twenty48-btn-undo" class="twenty48-btn twenty48-btn-secondary" title="Atcelt pēdējo gājienu" disabled>↩ Atcelt gājienu</button>
			<button id="twenty48-btn-restart" class="twenty48-btn twenty48-btn-primary">🔄 Jauna spēle</button>
		</div>

		<div id="twenty48-board" class="twenty48-board">
			<div class="twenty48-grid-container">
				<div class="twenty48-row">
					<div class="twenty48-cell"></div>
					<div class="twenty48-cell"></div>
					<div class="twenty48-cell"></div>
					<div class="twenty48-cell"></div>
				</div>
				<div class="twenty48-row">
					<div class="twenty48-cell"></div>
					<div class="twenty48-cell"></div>
					<div class="twenty48-cell"></div>
					<div class="twenty48-cell"></div>
				</div>
				<div class="twenty48-row">
					<div class="twenty48-cell"></div>
					<div class="twenty48-cell"></div>
					<div class="twenty48-cell"></div>
					<div class="twenty48-cell"></div>
				</div>
				<div class="twenty48-row">
					<div class="twenty48-cell"></div>
					<div class="twenty48-cell"></div>
					<div class="twenty48-cell"></div>
					<div class="twenty48-cell"></div>
				</div>
			</div>

			<div id="twenty48-tile-container" class="twenty48-tile-container"></div>

			<div id="twenty48-overlay" class="twenty48-overlay">
				<div class="twenty48-overlay-content">
					<h3 id="twenty48-overlay-title">Spēle beigusies!</h3>
					<p id="twenty48-overlay-msg"></p>
					<button id="twenty48-btn-retry" class="twenty48-btn twenty48-btn-primary">Spēlēt vēlreiz</button>
				</div>
			</div>
		</div>

		<!-- Mobile Direction Controls -->
		<div class="twenty48-mobile-controls">
			<div class="twenty48-m-row">
				<button class="twenty48-m-btn" data-dir="up">▲</button>
			</div>
			<div class="twenty48-m-row">
				<button class="twenty48-m-btn" data-dir="left">◀</button>
				<button class="twenty48-m-btn" data-dir="down">▼</button>
				<button class="twenty48-m-btn" data-dir="right">▶</button>
			</div>
		</div>

		<div class="twenty48-guide-box">
			<h4>Kā spēlēt:</h4>
			<ul>
				<li>Lieto <strong>Klaviatūras bultiņas</strong> (<kbd>←</kbd> <kbd>↑</kbd> <kbd>→</kbd> <kbd>↓</kbd>) vai <kbd>W</kbd> <kbd>A</kbd> <kbd>S</kbd> <kbd>D</kbd> taustiņus, lai pārvietotu skaitļu flīzes.</li>
				<li>Uz mobilajām ierīcēm bīdi ar pirkstu (Swipe) ekrānā jebkurā virzienā.</li>
				<li>Kad divas flīzes ar vienādu skaitli saskaras, tās <strong>apvienojas vienā</strong> ar dubultotu vērtību!</li>
				<li>Mans mērķis ir sasniegt <strong>2048</strong> flīzi un uzstādīt maksimālo punktu rekordu!</li>
			</ul>
		</div>
	</div>
	<!-- END BLOCK : game-play-->
</div>

<!-- START BLOCK : seo-text -->
<div class="game-seo-box" style="margin-top: 30px; padding: 20px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px;">
	<h2 style="margin-top: 0; color: #0f172a; font-size: 20px;">Par 2048 skaitļu spēli un kā spēlēt</h2>
	<div style="display: flex; gap: 20px; flex-wrap: wrap; margin-top: 15px;">
		<div style="flex: 1; min-width: 280px; color: #334155; line-height: 1.6;">
			<p><strong>2048</strong> ir atkarību izraisoša skaitļu puzzle spēle. 4x4 laukumā bīdot skaitļu flīzes, apvieno vienādās vērtības, lai sasniegtu mērķa flīzi 2048 vai pat vairāk!</p>
			<h3 style="color: #1e293b; font-size: 16px; margin-top: 15px;">Spēles noteikumi un vadība:</h3>
			<ul style="padding-left: 20px; margin-bottom: 0;">
				<li>Izmanto <strong>Bultiņas</strong> uz klaviatūras vai spied uz ekrāna vadības pogām / pārvelc ar pirkstu mobilajā ierīcē.</li>
				<li>Kad divas flīzes ar vienādu skaitli saskaras, tās apvienojas vienā ar dubultotu vērtību (2+2=4, 4+4=8, 1024+1024=2048).</li>
				<li>Spēle beidzas, kad laukums ir pilns un vairs nav iespējams veikt nevienu apvienošanu.</li>
			</ul>
		</div>
		<div style="width: 300px; max-width: 100%;">
			<img src="/bildes/speles/2048.png" alt="2048 spēle EXS.LV" style="width: 100%; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.15);" />
		</div>
	</div>
</div>
<!-- END BLOCK : seo-text -->
