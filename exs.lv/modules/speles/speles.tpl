<h1>EXS.LV Spēles</h1>
<p class="speles-intro-text">Izmēģini mūsu tiešsaistes spēles, sacenties ar citiem lietotājiem un ieraksti savu vārdu līderu topos!</p>

<!-- START BLOCK : seo-intro -->
<div class="speles-seo-intro">
	<p>Spēlē <strong>bezmaksas tiešsaistes spēles pārlūkā bez lejupielādes</strong> un instalēšanas! Visas EXS.LV spēles darbojas kā <strong>spēles telefonā un datorā latviešu valodā</strong> — izvēlies iecienīto žanru un sāc spēlēt uzreiz vai <a href="/register">reģistrējies</a>, lai saglabātu savus rekordus.</p>
</div>
<!-- END BLOCK : seo-intro -->

<div class="speles-cat-bar" id="speles-cat-bar" role="navigation" aria-label="Spēļu kategorijas">
	<button type="button" class="speles-cat-tag is-active" data-filter="all">Visas spēles</button>
	<button type="button" class="speles-cat-tag" data-filter="arkades">Arkādes</button>
	<button type="button" class="speles-cat-tag" data-filter="logikas">Loģikas spēles</button>
	<button type="button" class="speles-cat-tag" data-filter="vardu">Vārdu spēles</button>
	<button type="button" class="speles-cat-tag" data-filter="diviem">Spēles 2 spēlētājiem</button>
</div>

<div class="speles-grid" id="speles-grid">
	<!-- START BLOCK : game-card -->
	<div class="speles-card" data-categories="{game-cat-keys}">
		<div class="speles-card-header">
			<a href="{game-url}" class="speles-card-icon-link">
				<img src="{game-icon}" alt="{game-title}" class="speles-card-icon-img" width="48" height="48" loading="lazy" />
			</a>
			<div class="speles-card-title-group">
				{game-badge}
				<h3><a href="{game-url}">{game-title}</a> {game-rate}</h3>
				<span class="speles-card-cat">{game-cat-label}</span>
			</div>
		</div>
		<p class="speles-card-desc">{game-desc}</p>
		<div class="speles-card-footer">
			<span class="speles-card-top">{top-player}</span>
			<a href="{game-url}" class="btn btn-primary btn-small speles-play-btn">Spēlēt &raquo;</a>
		</div>
	</div>
	<!-- END BLOCK : game-card -->
</div>

<!-- START BLOCK : recent-scores-block -->
<div class="speles-recent-block">
	<h2>Pēdējie uzstādītie rekordi</h2>
	<div class="speles-recent-grid">
		<!-- START BLOCK : recent-score-node -->
		<div class="recent-score-item">
			<a href="{user-url}" class="recent-user">{user-nick}</a>
			<span class="recent-details">spēlē <a href="{game-url}"><strong>{game-name}</strong></a> ieguva <strong>{score}</strong> pnk.</span>
			<span class="recent-time">{time-ago}</span>
		</div>
		<!-- END BLOCK : recent-score-node -->
	</div>
</div>
<!-- END BLOCK : recent-scores-block -->

<div class="speles-reviews-link-box">
	<h3>Meklē spēļu apskatus?</h3>
	<p>Apskati jaunākos PC, konsoļu un mobilo spēļu apskatus mūsu rakstu sadaļā: <a href="/spelu-apskati" class="btn btn-info btn-small">Dodies uz Spēļu Apskatiem &raquo;</a></p>
</div>

<!-- START BLOCK : seo-text -->
<div class="game-description-box speles-seo-footer">
	<h2>Bezmaksas tiešsaistes spēles pārlūkā bez lejupielādes</h2>
	<p>EXS.LV spēļu sadaļā ir apkopotas ātras un aizraujošas <strong>bezmaksas tiešsaistes spēles pārlūkā bez lejupielādes</strong>. Visas mūsu HTML5 minispēles ir pielāgotas gan skārienekrāniem, gan klaviatūrai, nodrošinot ērtas <strong>spēles telefonā un datorā latviešu valodā</strong> bez liekas programmatūras instalēšanas.</p>
	<h3>Spēļu kategorijas un populārākie žanri:</h3>
	<ul>
		<li><strong>Arkādes spēles:</strong> Pārbaudi reakciju un veiklību klasiskajās arkādēs — <a href="/pacman">Exs-Man (Pac-Man)</a>, <a href="/snake">Čūska (Snake)</a>, <a href="/tanki">Tanki 1990</a>, <a href="/arkanoid">Arkanoid</a>, <a href="/invaders">Space Invaders</a>, <a href="/flappy">Lidojošais Eksis</a>, <a href="/augsup">Augšup</a>, <a href="/vardes">Vardes</a>, <a href="/tornis">Tornis</a>, <a href="/runner">Runner</a> un leģendārajā 3D šūterī <a href="/ut99">Unreal Tournament 99</a>.</li>
		<li><strong>Loģikas spēles:</strong> Trenē prātu un stratēģisko domāšanu ar <a href="/tetris">Tetris</a>, skaitļu mīklu <a href="/2048-spele">2048</a>, klasisko <a href="/sudoku">Sudoku</a>, <a href="/minu-mekletajs">Mīnu meklētāju (Minesweeper)</a> un <a href="/memory">Atmiņas spēli</a>.</li>
		<li><strong>Vārdu spēles latviešu valodā:</strong> Paplašini vārdu krājumu ikdienas 5 burtu izaicinājumā <a href="/wordle">Wordle latviski</a> un klasiskajā vārdu minēšanas spēlē <a href="/karatavas">Karātavas</a>.</li>
		<li><strong>Spēles 2 spēlētājiem un sacensībām:</strong> Cīnies pret citiem spēlētājiem tiešsaistē vai pārspēj draugu rezultātus spēlēs <a href="/desas">Desas (Tic-Tac-Toe)</a> un <a href="/rezonanse">Rezonanse</a>, vai izmēģini veiksmi bezmaksas virtuālajā <a href="/rulete">Ruletē</a>.</li>
	</ul>
</div>
<!-- END BLOCK : seo-text -->

<script>
(function() {
	var bar = document.getElementById('speles-cat-bar');
	var grid = document.getElementById('speles-grid');
	if (!bar || !grid) return;
	var buttons = bar.querySelectorAll('.speles-cat-tag');
	var cards = grid.querySelectorAll('.speles-card');
	bar.addEventListener('click', function(e) {
		var btn = e.target.closest ? e.target.closest('.speles-cat-tag') : e.target;
		if (!btn || !btn.hasAttribute('data-filter')) return;
		var filter = btn.getAttribute('data-filter');
		for (var i = 0; i < buttons.length; i++) {
			buttons[i].classList.toggle('is-active', buttons[i] === btn);
		}
		for (var j = 0; j < cards.length; j++) {
			var cats = (' ' + (cards[j].getAttribute('data-categories') || '') + ' ');
			cards[j].style.display = (filter === 'all' || cats.indexOf(' ' + filter + ' ') !== -1) ? '' : 'none';
		}
	});
})();
</script>
