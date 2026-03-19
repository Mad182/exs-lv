<!-- START BLOCK : hm-gbody-top-->

<script>

	$(document).ready(function () {
		$('#hm-game-alphabet a').on('click', function () {
			$('#hm-game-answer').fadeTo(200, 0.6);
			$('#hm-game-container').load($(this).attr('href'), function () {
				$(this).fadeTo(300, 1);
			});
			return false;
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
	<div class="form">
		<p class="notice">Tu neesi ielogojies!</p>
	</div>
	<!-- END BLOCK : hm-login-->

	<!-- START BLOCK : hm-game-->
	<div style="background: #fff url('/modules/wg-play/images/{img}.jpg') no-repeat -60px -50px;" id="hm-game-body">

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
	<p style="color: #888;" class="comment-edited-by">Kļūdas? Ieteikumi? <a
			href="/?c=104&amp;act=compose&amp;to=1">Raksti man</a> ;)

		<!-- END BLOCK : hm-game-->

		<!-- START BLOCK : hm-gbody-bottom-->
</div>
<!-- END BLOCK : hm-gbody-bottom-->