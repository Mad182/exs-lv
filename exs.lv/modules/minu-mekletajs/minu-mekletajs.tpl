<h1>Mīnu Meklētājs (Minesweeper)</h1>

<div class="tabs">
	<li><a href="/minu-mekletajs" class="tab{active-tab-game}">Spēle</a></li>
	<li><a href="/minu-mekletajs/top" class="tab{active-tab-top}">Šodienas tops</a></li>
	<li><a href="/minu-mekletajs/overall-top" class="tab{active-tab-overall-top}">Visu laiku tops</a></li>
</div>

<div class="tabMain" id="minesweeper-container">
	<!-- START BLOCK : diff-section -->
	<div class="ms-top-section">
		<h3 class="ms-top-heading"><span class="ms-top-icon">{diff-icon}</span> {diff-title}</h3>
		<!-- START BLOCK : top-table -->
		<table class="table table-striped table-hover tetris-top-table ms-top-table">
			<thead>
				<tr>
					<th style="width: 50px;">Vieta</th>
					<th>Spēlētājs</th>
					<th style="width: 120px; text-align: right;">Laiks</th>
					<th style="width: 150px; text-align: right;">Datums</th>
				</tr>
			</thead>
			<tbody>
				<!-- START BLOCK : top-node -->
				<tr>
					<td{user-special}>{user-place}</td>
					<td{user-special}><a href="{user-url}">{user-nick}</a></td>
					<td{user-special} style="text-align: right; font-weight: bold;">{user-score}</td>
					<td{user-special} style="text-align: right; color: #888;">{user-time}</td>
				</tr>
				<!-- END BLOCK : top-node -->
			</tbody>
		</table>
		<!-- END BLOCK : top-table -->

		<!-- START BLOCK : no-scores -->
		<div class="alert alert-info ms-no-scores">
			Šajā sarežģītības pakāpē pagaidām nav ierakstītu rezultātu.
		</div>
		<!-- END BLOCK : no-scores -->
	</div>
	<!-- END BLOCK : diff-section -->

	<!-- START BLOCK : game-login -->
	<div class="alert alert-info karatavas-guest-alert" style="margin-bottom: 15px;">
		<strong>Piezīme:</strong> Tu spēlē kā viesis. <a href="/register">Reģistrējies</a> vai ieej profilā, lai saglabātu savu laika rezultātu topā!
	</div>
	<!-- END BLOCK : game-login -->

	<!-- START BLOCK : game-play -->
	<div class="ms-wrapper">
		<div class="ms-toolbar">
			<div class="ms-diff-selector">
				<label>Sarežģītība:</label>
				<select id="ms-difficulty" class="ms-select" data-best-easy="{user-best-easy}" data-best-medium="{user-best-medium}" data-best-hard="{user-best-hard}">
					<option value="easy" selected>Iesācējs (9x9, 10 mīnas)</option>
					<option value="medium">Vidējs (16x16, 40 mīnas)</option>
					<option value="hard">Eksperts (30x16, 99 mīnas)</option>
				</select>
			</div>
			<div class="ms-stat-badge">
				<span>Labākais: <strong id="ms-user-best">{user-best-time}</strong></span>
			</div>
		</div>

		<div class="ms-window">
			<div class="ms-header">
				<div id="ms-mine-counter" class="ms-digital-counter">010</div>
				<button id="ms-btn-face" class="ms-btn-face">😊</button>
				<div id="ms-timer" class="ms-digital-counter">000</div>
			</div>

			<!-- Board container -->
			<div class="ms-board-scroll">
				<div id="ms-board" class="ms-board ms-easy"></div>
			</div>
		</div>

		<!-- Mobile Flag Toggle Toolbar -->
		<div class="ms-mobile-mode-bar">
			<button id="ms-mode-reveal" class="ms-mode-btn active">⛏️ Atvērt</button>
			<button id="ms-mode-flag" class="ms-mode-btn">🚩 Karodziņš</button>
		</div>

		<div class="ms-guide-box">
			<h4>Kā spēlēt:</h4>
			<ul>
				<li><strong>Kreisais klikšķis</strong> (vai pieskāriens): Atver lauciņu. Pirmais klikšķis VIENMĒR ir drošs!</li>
				<li><strong>Labais klikšķis</strong> (vai turot nospiestu): Uzliek vai noņem mīnas karodziņu (🚩).</li>
				<li><strong>Dubultklikšķis</strong> uz atvērta skaitļa: Ātri atver blakus lauciņus, ja atbilstošs karodziņu skaits jau atzīmēts.</li>
				<li>Atver visus drošos lauciņus, neuzduroties uz mīnām, lai uzvarētu ar labāko laiku!</li>
			</ul>
		</div>
	</div>
	<!-- END BLOCK : game-play -->
</div>

<!-- START BLOCK : seo-text -->
<div class="game-description-box">
	<h2>Par Mīnu Meklētāju (Minesweeper) un kā spēlēt</h2>
	<p><strong>Mīnu Meklētājs (Minesweeper)</strong> ir klasiskā loģikas spēle. Tavs uzdevums ir atsegt visus drošos lauciņus, neuzkāpjot uz nevienas apslēptās mīnas.</p>
	<h3>Spēles noteikumi un vadība:</h3>
	<ul>
		<li><strong>Kreisais klikšķis</strong> atsedz lauciņu. Skaitlis lauciņā norāda, cik mīnas atrodas 8 kaimiņu rāmīšos.</li>
		<li><strong>Labais klikšķis</strong> (vai karodziņa režīms mobilajā) uzliek karodziņu uz aizdomīgā lauciņa.</li>
		<li>Pirmais klikšķis vienmēr ir 100% drošs un garantēti neatsegs mīnu!</li>
	</ul>
</div>
<!-- END BLOCK : seo-text -->
