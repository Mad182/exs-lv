<h1>Rulete - Klasiskā Kazino Spēle</h1>

<div class="tabs-container" style="margin-bottom: 20px;">
	<ul class="tabs clearfix">
		<li class="{active-tab-game}"><a href="/rulete">Spēle</a></li>
		<li class="{active-tab-today}"><a href="/rulete?act=today">Pašreizējais tops</a></li>
		<li class="{active-tab-all}"><a href="/rulete?act=all">Visu laiku tops</a></li>
	</ul>
</div>

<!-- START BLOCK : game-login -->
<div class="alert alert-info">
	<strong>Piezīme:</strong> Tu spēlē kā viesis (demo režīmā ar 100 zelta žetoniem). <a href="/login">Reģistrējies vai ieej profilā</a>, lai saglabātu savu zeltu un sacenstos EXS.LV topos!
</div>
<!-- END BLOCK : game-login -->

<!-- START BLOCK : game-play -->
<div class="roulette-main-container">
	<!-- Top Bar: Gold Balance & Reset Notice -->
	<div class="roulette-status-bar">
		<div class="roulette-balance-card">
			<span class="gold-icon">💰</span>
			<span class="gold-label">Zelta Bilance:</span>
			<strong id="user-gold-val">{user-gold}</strong>
		</div>
		<div class="roulette-daily-note">
			🎁 <em>Ja tev ir mazāk par 100 zeltu, katru dienu tava bilance automātiski atjaunojas uz 100 zeltu!</em>
		</div>
	</div>

	<!-- Main Play Area: Wheel & Board -->
	<div class="roulette-game-grid">
		<!-- Left: Wheel Section -->
		<div class="roulette-wheel-section">
			<div class="roulette-wheel-wrapper">
				<div class="wheel-pointer">▼</div>
				<canvas id="roulette-wheel" width="340" height="340"></canvas>
			</div>
			<div id="roulette-result-display" class="roulette-result-display">
				Spied <strong>Vērpt</strong>, lai sāktu spēli!
			</div>
		</div>

		<!-- Right: Betting & Controls Section -->
		<div class="roulette-board-section">
			<!-- Chips Selector -->
			<div class="roulette-chip-selector">
				<span class="selector-title">Izvēlies žetonu:</span>
				<div class="chip-options">
					<button type="button" class="chip-btn active" data-value="1"><span class="chip-val">1</span></button>
					<button type="button" class="chip-btn" data-value="5"><span class="chip-val">5</span></button>
					<button type="button" class="chip-btn" data-value="10"><span class="chip-val">10</span></button>
					<button type="button" class="chip-btn" data-value="25"><span class="chip-val">25</span></button>
					<button type="button" class="chip-btn" data-value="100"><span class="chip-val">100</span></button>
				</div>
			</div>

			<!-- Betting Table Grid -->
			<div class="roulette-table-wrapper">
				<div class="roulette-table">
					<!-- Zero Cell -->
					<div class="cell cell-zero" data-type="num" data-val="0">0</div>

					<!-- Numbers Grid (3 rows, 12 columns) -->
					<div class="numbers-grid">
						<!-- Row 3: 3, 6, 9, 12, 15, 18, 21, 24, 27, 30, 33, 36 -->
						<div class="grid-row">
							<div class="cell cell-num red" data-type="num" data-val="3">3</div>
							<div class="cell cell-num black" data-type="num" data-val="6">6</div>
							<div class="cell cell-num red" data-type="num" data-val="9">9</div>
							<div class="cell cell-num red" data-type="num" data-val="12">12</div>
							<div class="cell cell-num black" data-type="num" data-val="15">15</div>
							<div class="cell cell-num red" data-type="num" data-val="18">18</div>
							<div class="cell cell-num red" data-type="num" data-val="21">21</div>
							<div class="cell cell-num black" data-type="num" data-val="24">24</div>
							<div class="cell cell-num red" data-type="num" data-val="27">27</div>
							<div class="cell cell-num red" data-type="num" data-val="30">30</div>
							<div class="cell cell-num black" data-type="num" data-val="33">33</div>
							<div class="cell cell-num red" data-type="num" data-val="36">36</div>
						</div>
						<!-- Row 2: 2, 5, 8, 11, 14, 17, 20, 23, 26, 29, 32, 35 -->
						<div class="grid-row">
							<div class="cell cell-num black" data-type="num" data-val="2">2</div>
							<div class="cell cell-num red" data-type="num" data-val="5">5</div>
							<div class="cell cell-num black" data-type="num" data-val="8">8</div>
							<div class="cell cell-num black" data-type="num" data-val="11">11</div>
							<div class="cell cell-num red" data-type="num" data-val="14">14</div>
							<div class="cell cell-num black" data-type="num" data-val="17">17</div>
							<div class="cell cell-num black" data-type="num" data-val="20">20</div>
							<div class="cell cell-num red" data-type="num" data-val="23">23</div>
							<div class="cell cell-num black" data-type="num" data-val="26">26</div>
							<div class="cell cell-num black" data-type="num" data-val="29">29</div>
							<div class="cell cell-num red" data-type="num" data-val="32">32</div>
							<div class="cell cell-num black" data-type="num" data-val="35">35</div>
						</div>
						<!-- Row 1: 1, 4, 7, 10, 13, 16, 19, 22, 25, 28, 31, 34 -->
						<div class="grid-row">
							<div class="cell cell-num red" data-type="num" data-val="1">1</div>
							<div class="cell cell-num black" data-type="num" data-val="4">4</div>
							<div class="cell cell-num red" data-type="num" data-val="7">7</div>
							<div class="cell cell-num black" data-type="num" data-val="10">10</div>
							<div class="cell cell-num black" data-type="num" data-val="13">13</div>
							<div class="cell cell-num red" data-type="num" data-val="16">16</div>
							<div class="cell cell-num red" data-type="num" data-val="19">19</div>
							<div class="cell cell-num black" data-type="num" data-val="22">22</div>
							<div class="cell cell-num red" data-type="num" data-val="25">25</div>
							<div class="cell cell-num black" data-type="num" data-val="28">28</div>
							<div class="cell cell-num black" data-type="num" data-val="31">31</div>
							<div class="cell cell-num red" data-type="num" data-val="34">34</div>
						</div>
					</div>

					<!-- Column Bets -->
					<div class="column-bets">
						<div class="cell cell-col" data-type="col3">2:1</div>
						<div class="cell cell-col" data-type="col2">2:1</div>
						<div class="cell cell-col" data-type="col1">2:1</div>
					</div>
				</div>

				<!-- Dozens Bets -->
				<div class="dozens-row">
					<div class="cell cell-doz" data-type="1st12">1. desmits (1-12)</div>
					<div class="cell cell-doz" data-type="2nd12">2. desmits (13-24)</div>
					<div class="cell cell-doz" data-type="3rd12">3. desmits (25-36)</div>
				</div>

				<!-- Outside Bets -->
				<div class="outside-row">
					<div class="cell cell-out" data-type="1-18">1-18</div>
					<div class="cell cell-out" data-type="even">PĀRIS</div>
					<div class="cell cell-out red-bg" data-type="red">SARKANS</div>
					<div class="cell cell-out black-bg" data-type="black">MELNS</div>
					<div class="cell cell-out" data-type="odd">NEPĀRIS</div>
					<div class="cell cell-out" data-type="19-36">19-36</div>
				</div>
			</div>

			<!-- Bet Summary & Actions Bar -->
			<div class="roulette-actions-bar">
				<div class="bet-info-card">
					<span>Kopējā likme: <strong id="total-bet-val">0</strong></span>
					<span>Pēdējo laimests: <strong id="last-win-val">0</strong></span>
				</div>
				<div class="action-buttons">
					<button type="button" id="clear-btn" class="btn btn-warning">🧹 Attīrīt</button>
					<button type="button" id="double-btn" class="btn btn-info">✖️2 Dubultot</button>
					<button type="button" id="spin-btn" class="btn btn-success btn-large">🔴 VĒRPT RULETI</button>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- END BLOCK : game-play -->

<!-- START BLOCK : rulete-top -->
<table class="table table-striped table-bordered table-hover mbox">
	<thead>
		<tr>
			<th style="width: 70px;">Vieta</th>
			<th>Lietotājs</th>
			<th style="width: 200px; text-align: right;">Zelta daudzums</th>
		</tr>
	</thead>
	<tbody>
		<!-- START BLOCK : top-row -->
		<tr>
			<td><strong>{user-place}</strong> {user-special}</td>
			<td><a href="{user-url}">{user-nick}</a></td>
			<td style="text-align: right;"><strong>{user-score}</strong></td>
		</tr>
		<!-- END BLOCK : top-row -->
		<!-- START BLOCK : no-scores -->
		<tr>
			<td colspan="3" class="text-center muted" style="padding: 20px;">Pagaidām nav neviena rezultāta. Būsi pirmais!</td>
		</tr>
		<!-- END BLOCK : no-scores -->
	</tbody>
</table>
<!-- END BLOCK : rulete-top -->

<!-- START BLOCK : seo-text -->
<div class="game-description-box">
	<h2>Par Kazino Ruleti un kā spēlēt</h2>
	<p><strong>Rulete</strong> ir viena no populārākajām un aizraujošākajām kazino spēlēm pasaulē. EXS.LV platformā vari spēlēt Eiropas ruleti ar 100 zelta sākuma kapitālu bez maksas!</p>
	<h3>Spēles noteikumi un vadība:</h3>
	<ul>
		<li><strong>Katras dienas papildinājums:</strong> Ja tavs zelta krājums noslīd zem 100 zeltam, katru dienu tas tiek automātiski papildināts atpakaļ uz 100 zeltu!</li>
		<li><strong>Likmju veikšana:</strong> Izvēlies žetona nominālu (1, 5, 10, 25, 100) un uzklikšķini uz izvēlētā lauka spēles galdā.</li>
		<li><strong>Laimestu izmaksas:</strong>
			<ul>
				<li>Tiešais skaitlis (0-36): <strong>35:1</strong></li>
				<li>Sarkans / Melns, Pāris / Nepāris, 1-18 / 19-36: <strong>1:1</strong></li>
				<li>Desmiti (1-12, 13-24, 25-36) un Slejas (2:1): <strong>2:1</strong></li>
			</ul>
		</li>
		<li><strong>Topejot ar zeltu:</strong> Pašreizējais tops parāda spēlētāju aktīvo zelta bilanci, bet visu laiku tops saglabā augstāko nåkad sasniegto zelta rekordu!</li>
	</ul>
</div>
<!-- END BLOCK : seo-text -->
