<h1>Sudoku</h1>

<div class="tabs">
	<li><a href="/sudoku" class="tab{active-tab-game}">Spēle</a></li>
	<li><a href="/sudoku/top" class="tab{active-tab-top}">Šodienas tops</a></li>
	<li><a href="/sudoku/overall-top" class="tab{active-tab-overall-top}">Visu laiku tops</a></li>
</div>

<div class="tabMain" id="sudoku-container">
	<!-- START BLOCK : top-table-->
	<table class="table table-striped table-hover tetris-top-table">
		<thead>
			<tr>
				<th style="width: 50px;">Vieta</th>
				<th>Spēlētājs</th>
				<th style="width: 120px; text-align: right;">Laiks</th>
				<th style="width: 150px; text-align: right;">Datums</th>
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
		<strong>Piezīme:</strong> Tu spēlē kā viesis. <a href="/register">Reģistrējies</a> vai ieej profilā, lai saglabātu savu laika rezultātu topā!
	</div>
	<!-- END BLOCK : game-login-->

	<!-- START BLOCK : game-play-->
	<div class="sdk-wrapper">
		<div class="sdk-toolbar">
			<div class="sdk-diff-selector">
				<label>Sarežģītība:</label>
				<select id="sdk-difficulty" class="sdk-select">
					<option value="easy" selected>Vienkāršs (Easy)</option>
					<option value="medium">Vidējs (Medium)</option>
					<option value="hard">Grūts (Hard)</option>
				</select>
			</div>
			<div class="sdk-stats">
				<div class="sdk-stat-badge">
					<span>Laiks: <strong id="sdk-timer">00:00</strong></span>
				</div>
				<div class="sdk-stat-badge">
					<span>Labākais: <strong>{user-best-time}</strong></span>
				</div>
			</div>
		</div>

		<!-- Sudoku 9x9 Board -->
		<div class="sdk-board-wrapper">
			<div id="sdk-board" class="sdk-board"></div>
		</div>

		<!-- Action Controls -->
		<div class="sdk-action-bar">
			<button id="sdk-btn-new" class="sdk-btn sdk-btn-primary">🔄 Jauna spēle</button>
			<button id="sdk-btn-pencil" class="sdk-btn sdk-btn-secondary" title="Piezīmju režīms">✏️ Zīmulis (OFF)</button>
			<button id="sdk-btn-erase" class="sdk-btn sdk-btn-secondary" title="Dzēst skaitli">⌫ Dzēst</button>
			<button id="sdk-btn-hint" class="sdk-btn sdk-btn-secondary" title="Saņemt mājienu (3 pieejami)">💡 Mājiena padoms (3)</button>
		</div>

		<!-- On-Screen Numpad for Touch / Quick Click -->
		<div class="sdk-numpad">
			<button class="sdk-num-btn" data-num="1">1</button>
			<button class="sdk-num-btn" data-num="2">2</button>
			<button class="sdk-num-btn" data-num="3">3</button>
			<button class="sdk-num-btn" data-num="4">4</button>
			<button class="sdk-num-btn" data-num="5">5</button>
			<button class="sdk-num-btn" data-num="6">6</button>
			<button class="sdk-num-btn" data-num="7">7</button>
			<button class="sdk-num-btn" data-num="8">8</button>
			<button class="sdk-num-btn" data-num="9">9</button>
		</div>

		<!-- Instructions Guide Box -->
		<div class="sdk-guide-box">
			<h4>Kā spēlēt:</h4>
			<ul>
				<li>Aizpildi 9x9 rāmi tā, lai katrā <strong>rindiņā</strong>, <strong>kolonnā</strong> un <strong>3x3 blokā</strong> skaitļi no 1 līdz 9 atrastos tieši vienu reizi.</li>
				<li>Lieto <strong>Klaviatūras skaitļus (1-9)</strong> vai spied uz <strong>Ekrāna ciparnīcas</strong>, lai ievadītu skaitļus.</li>
				<li>Ieslēdz <strong>✏️ Zīmuli</strong>, lai ierakstītu mazās piezīmes un iespējamos variantus.</li>
				<li>Lieto <kbd>Backspace</kbd> vai pogu <strong>⌫ Dzēst</strong>, lai noņemtu skaitli vai piezīmes.</li>
			</ul>
		</div>
	</div>
	<!-- END BLOCK : game-play-->
</div>
