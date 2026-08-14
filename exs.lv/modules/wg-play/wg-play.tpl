<!-- START BLOCK : hm-gbody-top-->

<script>

	$(document).ready(function () {
		// Delegated click handler for alphabet links and new game button
		$(document).off('click.karatavas', '#hm-game-alphabet a, #hm-new-game').on('click.karatavas', '#hm-game-alphabet a, #hm-new-game', function () {
			$('#hm-game-answer').fadeTo(200, 0.6);
			$('#hm-game-container').load($(this).attr('href'), function () {
				$(this).fadeTo(300, 1);
			});
			return false;
		});

		// Keyboard input support for Karātavas
		$(document).off('keydown.karatavas').on('keydown.karatavas', function (e) {
			if ($(e.target).is('input, textarea, select')) {
				return;
			}

			var key = e.key ? e.key.toLowerCase() : '';
			
			// Enter or Space key starts new game if game finished
			if (key === 'enter' || key === ' ') {
				var $newGame = $('#hm-new-game');
				if ($newGame.length > 0) {
					e.preventDefault();
					$newGame.trigger('click');
					return;
				}
			}

			if (!key || key.length > 1) {
				return;
			}

			// Match pressed key with available letters in #hm-game-alphabet
			$('#hm-game-alphabet a').each(function () {
				var letter = $(this).text().trim().toLowerCase();
				if (letter === key) {
					e.preventDefault();
					$(this).trigger('click');
					return false;
				}
			});
		});
	});

</script>

<h1>Karātavas - vārdu minēšanas spēle</h1>

<div class="tabs">
	<li><a href="/karatavas" class="tab{active-tab-game}">Spēle</a></li>
	<li><a href="/karatavas/top" class="tab{active-tab-top}">Šodienas tops</a></li>
	<li><a href="/karatavas/overall-top" class="tab{active-tab-overall-top}">Visu laiku tops</a></li>
</div>

<div class="tabMain" id="hm-game-container">
	<!-- END BLOCK : hm-gbody-top-->

	<!-- START BLOCK : hm-top-->
	<table class="table">
		<tr>
			<th>Vieta</th>
			<th>Niks</th>
			<th>Atbildēti jautājumi</th>
			<th>Iegūtie punkti</th>
			<th>Punkti/spēlē</th>
		</tr>
		<!-- START BLOCK : top-node-->
		<tr>
			<td{user-special}>{user-place}</td>
				<td{user-special}><a href="{user-url}">{user-nick}</td>
						<td{user-special}>{user-ig_done}</td>
							<td{user-special}>{user-ig_points}</td>
								<td{user-special}>{p-game}</td>
		</tr>
		<!-- END BLOCK : top-node-->
	</table>

	<p style="color:#888" class="comment-edited-by">Kļūdas? Ieteikumi? <a href="/?c=104&amp;act=compose&amp;to=1">Raksti
			man</a> ;)
		<!-- END BLOCK : hm-top-->

		<!-- START BLOCK : hm-login-->
	<div class="alert alert-info karatavas-guest-alert" style="margin-bottom: 15px;">
		<strong>Piezīme:</strong> Tu spēlē kā viesis. <a href="/register">Reģistrējies</a> vai ieej profilā, lai saglabātu savu rezultātu topā!
	</div>
	<!-- END BLOCK : hm-login-->

	<!-- START BLOCK : hm-game-->
	<div style="background-image: url('/modules/wg-play/images/{img}.png'); background-repeat: no-repeat; background-position: -60px -50px;" id="hm-game-body">

		<div id="hm-game-question">{hint} <div id="hm-game-answer">{guess}</div>
		</div>

		<div id="hm-game-alphabet">
			<!-- START BLOCK : hm-letter-->
			{letter}
			<!-- END BLOCK : hm-letter-->
			<div class="c"></div>
		</div>

	</div>
	<div class="c"></div>

	<div class="alert alert-info karatavas-keyboard-tip" style="margin-top: 15px; margin-bottom: 10px;">
		<strong>Padoms:</strong> Burtiem vari uzspiest arī, izmantojot klaviatūru!
	</div>

	<p style="color: #888;" class="comment-edited-by">Kļūdas? Ieteikumi? <a
			href="/?c=104&amp;act=compose&amp;to=1">Raksti man</a> ;)

		<!-- END BLOCK : hm-game-->

		<!-- START BLOCK : hm-gbody-bottom-->
	<!-- START BLOCK : seo-text -->
	<div class="game-seo-box" style="margin-top: 30px; padding: 20px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px;">
		<h2 style="margin-top: 0; color: #0f172a; font-size: 20px;">Par Karātavu vārdu spēli un kā spēlēt</h2>
		<div style="display: flex; gap: 20px; flex-wrap: wrap; margin-top: 15px;">
			<div style="flex: 1; min-width: 280px; color: #334155; line-height: 1.6;">
				<p><strong>Karātavas</strong> ir leģendārā vārdu minēšanas spēle latviešu valodā. Dators nejauši izvēlas vārdu no apjomīgas latviešu vārdnīcas, un tavs mērķis ir to atšifrēt pa burtiem.</p>
				<h3 style="color: #1e293b; font-size: 16px; margin-top: 15px;">Spēles noteikumi un vadība:</h3>
				<ul style="padding-left: 20px; margin-bottom: 0;">
					<li>Klikšķini uz burtu pogām ekrānā vai spied burts uz klaviatūras.</li>
					<li>Katrs pareizi uzminētais burts atklāj savu vietu vārdā.</li>
					<li>Nepareizi minējumi pakāpeniski zīmē karātavu zīmējumu — tev ir ierobežots kļūdu skaits!</li>
				</ul>
			</div>
			<div style="width: 300px; max-width: 100%;">
				<img src="/bildes/speles/karatavas.png" alt="Karātavas spēle EXS.LV" style="width: 100%; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.15);" />
			</div>
		</div>
	</div>
	<!-- END BLOCK : seo-text -->
</div>
<!-- END BLOCK : hm-gbody-bottom-->