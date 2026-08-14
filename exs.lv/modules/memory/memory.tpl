<h1>Atmiņas spēle - EXS.LV Avatari</h1>

<div class="tabs">
	<li><a href="/memory" class="tab{active-tab-game}">Spēle</a></li>
	<li><a href="/memory/top" class="tab{active-tab-top}">Šodienas tops</a></li>
	<li><a href="/memory/overall-top" class="tab{active-tab-overall-top}">Visu laiku tops</a></li>
</div>

<!-- START BLOCK : memory-game -->
<!-- START BLOCK : guest-notice -->
<div class="alert alert-info memory-guest-alert">
	<strong>Piezīme:</strong> Tu spēlē kā viesis. <a href="/register">Reģistrējies</a> vai ieej profilā, lai saglabātu savu rezultātu topā!
</div>
<!-- END BLOCK : guest-notice -->

<div class="memory-wrapper">
	<div class="memory-main-panel">
		<div class="memory-board-container">
			<div id="memory-board" class="grid-4x4">
				<div class="memory-overlay" id="memory-overlay">
					<div class="overlay-card">
						<h2 id="overlay-title">ATMIŅAS SPĒLE</h2>
						<p id="overlay-msg">Atrodi vienādos EXS.LV lietotāju meklējamos avatarus!</p>
						<button id="btn-start" class="btn btn-primary btn-large btn-memory-action">Sākt spēli</button>
					</div>
				</div>
			</div>
		</div>

		<div class="memory-sidebar-panel">
			<div class="memory-box">
				<h3>Tīkla izmērs</h3>
				<select id="grid-select" class="grid-dropdown">
					<option value="4x4" selected>4 x 4 (8 pāri)</option>
					<option value="6x4">6 x 4 (12 pāri)</option>
					<option value="6x6">6 x 6 (18 pāri)</option>
				</select>
			</div>

			<div class="memory-box memory-stats-box">
				<h3>Statistika</h3>
				<div class="stat-item">
					<span class="stat-label">Punkti</span>
					<span id="stat-score" class="stat-value">0</span>
				</div>
				<div class="stat-item">
					<span class="stat-label">Gājieni</span>
					<span id="stat-moves" class="stat-value">0</span>
				</div>
				<div class="stat-item">
					<span class="stat-label">Atrasti pāri</span>
					<span class="stat-value"><span id="stat-pairs">0</span> / <span id="stat-totpairs">8</span></span>
				</div>
				<div class="stat-item">
					<span class="stat-label">Laiks</span>
					<span id="stat-time" class="stat-value">0s</span>
				</div>
				<div class="stat-item">
					<span class="stat-label">Mans rekords</span>
					<span id="stat-highscore" class="stat-value">{user-highscore}</span>
				</div>
			</div>

			<div class="memory-controls-btns">
				<button id="btn-restart" class="btn btn-info">Sākt no jauna</button>
			</div>
		</div>
	</div>

	<div class="memory-mini-top-panel">
		<div class="widget">
			<h3 class="title"><span>Labākie spēlētāji</span></h3>
			<table class="table table-striped table-condensed memory-mini-table">
				<thead>
					<tr>
						<th>#</th>
						<th>Spēlētājs</th>
						<th>Punkti</th>
					</tr>
				</thead>
				<tbody>
					<!-- START BLOCK : mini-top-node -->
					<tr>
						<td>{place}.</td>
						<td><a href="{user-url}">{user-nick}</a></td>
						<td><strong>{score}</strong></td>
					</tr>
					<!-- END BLOCK : mini-top-node -->
				</tbody>
			</table>
			<div class="mini-top-footer">
				<a href="/memory/overall-top" class="btn btn-mini">Skatīt pilno topu &raquo;</a>
			</div>
		</div>

		<div class="memory-guide-box">
			<h4>Noteikumi</h4>
			<ul>
				<li>Atver divas kārtis, meklējot vienādus EXS.LV lietotāju avatarus.</li>
				<li>Ja avatari sakrīt, kārtis paliek atvērtas!</li>
				<li>Mazāk gājienu un ātrāks laiks dos vairāk punktus.</li>
				<li>Lielāki tīkla izmēri (6x4 un 6x6) dod punktu reizinātāju!</li>
			</ul>
		</div>
	</div>
</div>
<!-- END BLOCK : memory-game -->

<!-- START BLOCK : memory-top -->
<h2>{top-title}</h2>
<table class="table table-striped table-hover memory-top-table">
	<thead>
		<tr>
			<th style="width:10%">Vieta</th>
			<th style="width:40%">Lietotājs</th>
			<th style="width:25%">Punkti</th>
			<th style="width:25%">Datums</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : top-node -->
		<tr>
			<td{user-special}>{user-place}</td>
			<td{user-special}><a href="{user-url}">{user-nick}</a></td>
			<td{user-special}><strong>{user-score}</strong></td>
			<td{user-special}>{user-date}</td>
		</tr>
		<!-- END BLOCK : top-node -->
		<!-- START BLOCK : no-scores -->
		<tr>
			<td colspan="4" class="text-center muted" style="padding: 20px;">Pagaidām nav neviena rezultāta. Būsi pirmais!</td>
		</tr>
		<!-- END BLOCK : no-scores -->
	</tbody>
</table>
<!-- END BLOCK : memory-top -->

<!-- START BLOCK : seo-text -->
<div class="game-description-box">
	<h2>Par Atmiņas spēli (Memory) un kā spēlēt</h2>
	<p><strong>Atmiņas spēle (Memory)</strong> ir lielisks veids, kā trenēt vizuālo atmiņu un koncentrēšanās spējas. Laukumā ir izvietotas aizsegtas kārtis, kuras jāatver pa pāriem.</p>
	<h3>Spēles noteikumi un vadība:</h3>
	<ul>
		<li>Spied uz kārtīm, lai tās apgrieztu un ieraudzītu paslēpto attēlu.</li>
		<li>Atrodi divas vienādas kārtis pēc kārtas, lai tās paliktu atvērtas.</li>
		<li>Mērķis ir atvērt visas kārtis ar pēc iespējas mazāk gājieniem un visātrākajā laikā.</li>
	</ul>
</div>
<!-- END BLOCK : seo-text -->
