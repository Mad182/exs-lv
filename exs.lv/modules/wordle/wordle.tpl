<h1>Wordle (Vārdu mīkla)</h1>

<div class="tabs">
	<li><a href="/wordle" class="tab{active-tab-game}">Spēle</a></li>
	<li><a href="/wordle/top" class="tab{active-tab-top}">Šodienas tops</a></li>
	<li><a href="/wordle/overall-top" class="tab{active-tab-overall-top}">Visu laiku tops</a></li>
</div>

<div class="tabMain" id="wordle-container">
	<!-- START BLOCK : top-table-->
	<table class="table table-striped table-hover tetris-top-table">
		<thead>
			<tr>
				<th style="width: 50px;">Vieta</th>
				<th>Spēlētājs</th>
				<th style="width: 170px; text-align: right;">Rezultāts</th>
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
		<strong>Piezīme:</strong> Tu spēlē kā viesis. <a href="/register">Reģistrējies</a> vai ieej profilā, lai saglabātu savus rezultātus topā!
	</div>
	<!-- END BLOCK : game-login-->

	<!-- START BLOCK : game-play-->
	<div class="wdl-wrapper">
		<div class="wdl-toolbar">
			<div class="wdl-mode-selector">
				<button id="wdl-btn-daily" class="wdl-mode-btn active">📅 Dienas vārds</button>
				<button id="wdl-btn-practice" class="wdl-mode-btn">🎯 Trenēšanās</button>
			</div>
			<div class="wdl-stats">
				<div class="wdl-stat-badge">
					<span>Laiks: <strong id="wdl-timer">00:00</strong></span>
				</div>
				<div class="wdl-stat-badge">
					<span>Labākais: <strong>{user-best-score}</strong></span>
				</div>
			</div>
		</div>

		<!-- Toast Alert Message -->
		<div id="wdl-toast" class="wdl-toast"></div>

		<!-- 6x5 Wordle Grid -->
		<div class="wdl-grid-container">
			<div id="wdl-grid" class="wdl-grid"></div>
		</div>

		<!-- Action Bar -->
		<div class="wdl-action-bar">
			<button id="wdl-btn-new" class="wdl-btn wdl-btn-primary">🔄 Jauna spēle</button>
		</div>

		<!-- Latvian Virtual Keyboard -->
		<div class="wdl-keyboard">
			<div class="wdl-key-row">
				<button class="wdl-key" data-key="Q">Q</button>
				<button class="wdl-key" data-key="W">W</button>
				<button class="wdl-key" data-key="E">E</button>
				<button class="wdl-key" data-key="Ē">Ē</button>
				<button class="wdl-key" data-key="R">R</button>
				<button class="wdl-key" data-key="T">T</button>
				<button class="wdl-key" data-key="Y">Y</button>
				<button class="wdl-key" data-key="U">U</button>
				<button class="wdl-key" data-key="Ū">Ū</button>
				<button class="wdl-key" data-key="I">I</button>
				<button class="wdl-key" data-key="Ī">Ī</button>
				<button class="wdl-key" data-key="O">O</button>
				<button class="wdl-key" data-key="P">P</button>
			</div>
			<div class="wdl-key-row">
				<button class="wdl-key" data-key="A">A</button>
				<button class="wdl-key" data-key="Ā">Ā</button>
				<button class="wdl-key" data-key="S">S</button>
				<button class="wdl-key" data-key="Š">Š</button>
				<button class="wdl-key" data-key="D">D</button>
				<button class="wdl-key" data-key="F">F</button>
				<button class="wdl-key" data-key="G">G</button>
				<button class="wdl-key" data-key="Ģ">Ģ</button>
				<button class="wdl-key" data-key="H">H</button>
				<button class="wdl-key" data-key="J">J</button>
				<button class="wdl-key" data-key="K">K</button>
				<button class="wdl-key" data-key="Ķ">Ķ</button>
				<button class="wdl-key" data-key="L">L</button>
				<button class="wdl-key" data-key="Ļ">Ļ</button>
			</div>
			<div class="wdl-key-row">
				<button class="wdl-key wdl-key-wide" data-key="ENTER">IEVADĪT</button>
				<button class="wdl-key" data-key="Z">Z</button>
				<button class="wdl-key" data-key="Ž">Ž</button>
				<button class="wdl-key" data-key="C">C</button>
				<button class="wdl-key" data-key="Č">Č</button>
				<button class="wdl-key" data-key="V">V</button>
				<button class="wdl-key" data-key="B">B</button>
				<button class="wdl-key" data-key="N">N</button>
				<button class="wdl-key" data-key="Ņ">Ņ</button>
				<button class="wdl-key" data-key="M">M</button>
				<button class="wdl-key wdl-key-wide" data-key="BACKSPACE">⌫ DZĒST</button>
			</div>
		</div>

		<!-- Instructions Guide Box -->
		<div class="wdl-guide-box">
			<h4>Kā spēlēt Wordle:</h4>
			<ul>
				<li>Tev ir <strong>6 mēģinājumi</strong>, lai uzminētu noslēpto 5 burtu vārdu.</li>
				<li>Katram minējumam jābūt derīgam latviešu valodas 5 burtu vārdam.</li>
				<li>Pēc katra minējuma rāmīšu krāsa mainīsies, rādot tavu progresu:</li>
				<li style="list-style: none; margin-left: -10px; margin-top: 6px;">
					<span class="wdl-demo-tile correct">S</span> — Burts ir pareizs un atrodas īstajā vietā.
				</li>
				<li style="list-style: none; margin-left: -10px;">
					<span class="wdl-demo-tile present">A</span> — Burts ir vārdā, bet atrodas citā vietā.
				</li>
				<li style="list-style: none; margin-left: -10px;">
					<span class="wdl-demo-tile absent">U</span> — Burts vispār nav šajā vārdā.
				</li>
			</ul>
		</div>
	</div>
	<!-- END BLOCK : game-play-->
</div>

<!-- START BLOCK : seo-text -->
<div class="game-seo-box" style="margin-top: 30px; padding: 20px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px;">
	<h2 style="margin-top: 0; color: #0f172a; font-size: 20px;">Par Wordle spēli latviešu valodā un kā spēlēt</h2>
	<div style="display: flex; gap: 20px; flex-wrap: wrap; margin-top: 15px;">
		<div style="flex: 1; min-width: 280px; color: #334155; line-height: 1.6;">
			<p><strong>Wordle</strong> ir 5 burtu vārdu minēšanas spēle latviešu valodā. Tev ir 6 mēģinājumi, lai atšifrētu apslēpto vārdu no vairāk nekā 25,000 latviešu vārdu krājuma.</p>
			<h3 style="color: #1e293b; font-size: 16px; margin-top: 15px;">Spēles noteikumi un vadība:</h3>
			<ul style="padding-left: 20px; margin-bottom: 0;">
				<li>Ievadi 5 burtu vārdu un spied Enter.</li>
				<li><span style="color: #10b981; font-weight: bold;">Zaļš rāmītis:</span> Burts ir pareizs un atrodas īstajā vietā.</li>
				<li><span style="color: #f59e0b; font-weight: bold;">Dzeltens rāmītis:</span> Burts ir vārdā, bet atrodas citā vietā.</li>
				<li><span style="color: #64748b; font-weight: bold;">Pelēks rāmītis:</span> Burts nav šajā vārdā.</li>
			</ul>
		</div>
		<div style="width: 300px; max-width: 100%;">
			<img src="/bildes/speles/wordle.png" alt="Wordle latviešu valodā EXS.LV" style="width: 100%; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.15);" />
		</div>
	</div>
</div>
<!-- END BLOCK : seo-text -->
