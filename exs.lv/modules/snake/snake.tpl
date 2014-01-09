<h2>Čūska</h2>

<div id="snake-col-map" class="col-l">

	<!--
	<noscript class="error">
		You need javascript enabled on your browser to be able to play this game.
	</noscript>
	-->

	<div id="map1">

		<span id="map-msg">
			<!-- click to give focus to the document -->
			<a href="#start" id="start-game">
				<img src="/modules/snake/images/start_game.jpg" alt="Start Game!" />
			</a>
		</span>
	</div>
	<div id="stats">
		<span class="right">Eaten:
			<span id="stats-eaten">0</span> /
			<span id="stats-totcherries">0</span>

		</span>
		Level: <span id="stats-level">0</span> |
		Lives: <span id="stats-lives">0</span> |
		Score: <span id="stats-score">0</span>
	</div>
</div>

<div class="c"></div>
<br />
<h2>Highscores</h2>
<table class="main-table" style="margin: 10px 0;">
	<tr>
		<th>Vieta</th>
		<th>Lietotājs</th>
		<th>Punkti</th>
		<th>Datums</th>
  </tr>
	<!-- START BLOCK : score-tr-->
	<tr>
		<td>{place}</td>
		<td><a href="{user-url}">{user}</a></td>
		<td>{score}</td>
		<td>{date}</td>
	</tr>
	<!-- END BLOCK : score-tr-->

</table>