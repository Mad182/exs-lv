<h1>Čūska - klasiskā spēle</h1>

<div class="tabs">
	<li><a href="/snake" class="tab{active-tab-game}">Spēle</a></li>
	<li><a href="/snake/top" class="tab{active-tab-top}">Šodienas tops</a></li>
	<li><a href="/snake/overall-top" class="tab{active-tab-overall-top}">Visu laiku tops</a></li>
</div>

<!-- START BLOCK : snake-game -->
<!-- START BLOCK : guest-notice -->
<div class="alert alert-info snake-guest-alert">
	<strong>Piezīme:</strong> Tu spēlē kā viesis. <a href="/register">Reģistrējies</a> vai ieej profilā, lai saglabātu savu rezultātu topā!
</div>
<!-- END BLOCK : guest-notice -->

<div class="snake-wrapper">
	<div class="snake-main-panel">
		<div id="snake-col-map" class="snake-board-container">
			<div id="map1">
				<span id="map-msg">
					<a href="#start" id="start-game" class="btn btn-primary btn-large btn-snake-start">
						Sākt spēli
					</a>
				</span>
			</div>
		</div>

		<div class="snake-sidebar-panel">
			<div class="snake-box snake-stats-box">
				<h3>Statistika</h3>
				<div class="stat-item">
					<span class="stat-label">Punkti</span>
					<span id="stats-score" class="stat-value">0</span>
				</div>
				<div class="stat-item">
					<span class="stat-label">Līmenis</span>
					<span id="stats-level" class="stat-value">1</span>
				</div>
				<div class="stat-item">
					<span class="stat-label">Dzīvības</span>
					<span id="stats-lives" class="stat-value">3</span>
				</div>
				<div class="stat-item">
					<span class="stat-label">Apēsts</span>
					<span class="stat-value"><span id="stats-eaten">0</span> / <span id="stats-totcherries">0</span></span>
				</div>
				<div class="stat-item">
					<span class="stat-label">Mans rekords</span>
					<span id="stat-highscore" class="stat-value">{user-highscore}</span>
				</div>
			</div>

			<div class="snake-controls-btns">
				<button id="btn-pause" class="btn btn-warning" onclick="Snake.pause();">Pauze (P)</button>
				<button id="btn-newgame" class="btn btn-info" onclick="Snake.newGame(true);">Jauna spēle (N)</button>
			</div>
		</div>
	</div>

	<div class="snake-mini-top-panel">
		<div class="widget">
			<h3 class="title"><span>Labākie spēlētāji</span></h3>
			<table class="table table-striped table-condensed snake-mini-table">
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
				<a href="/snake/overall-top" class="btn btn-mini">Skatīt pilno topu &raquo;</a>
			</div>
		</div>

		<div class="snake-guide-box">
			<h4>Vadība</h4>
			<ul>
				<li><kbd>&larr;</kbd> <kbd>&rarr;</kbd> <kbd>&uarr;</kbd> <kbd>&darr;</kbd> - Kustības virziens</li>
				<li><kbd>P</kbd> - Pauze</li>
				<li><kbd>N</kbd> - Sākt jaunu spēli</li>
				<li><kbd>G</kbd> - Ieslēgt / izslēgt tīklu (Grid)</li>
			</ul>
		</div>
	</div>
</div>
<!-- END BLOCK : snake-game -->

<!-- START BLOCK : snake-top -->
<h2>{top-title}</h2>
<table class="table table-striped table-hover snake-top-table">
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
<!-- END BLOCK : snake-top -->

<!-- START BLOCK : seo-text -->
<div class="game-description-box">
	<h2>Par Čūskas (Snake) spēli un kā spēlēt</h2>
	<p><strong>Čūska (Snake)</strong> ir klasiska arkādes spēle. Vadot čūsku pa laukumu, tavs uzdevums ir apēst pēc iespējas vairāk sarkanos ābolus un izaugt pēc iespējas garākam.</p>
	<h3>Spēles noteikumi un vadība:</h3>
	<ul>
		<li>Izmanto <strong>Tastatūras bultiņas</strong> vai <strong>W, A, S, D</strong> taustiņus, lai mainītu čūskas kustības virzienu.</li>
		<li>Katrs apēstais ābols palielina čūskas garumu un tavu punktu skaitu.</li>
		<li>Uzmanies — neietriecies spēles laukuma malās vai savas čūskas astē!</li>
	</ul>
</div>
<!-- END BLOCK : seo-text -->
