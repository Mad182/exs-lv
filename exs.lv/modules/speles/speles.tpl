<h1>EXS.LV Spēles</h1>
<p class="speles-intro-text">Izmēģini mūsu tiešsaistes spēles, sacenties ar citiem lietotājiem un ieraksti savu vārdu līderu topos!</p>

<div class="speles-grid">
	<!-- START BLOCK : game-card -->
	<div class="speles-card">
		<div class="speles-card-header">
			<span class="speles-card-icon">{game-icon}</span>
			<div class="speles-card-title-group">
				<span class="label {game-badge-class} pull-right">{game-badge}</span>
				<h3><a href="{game-url}">{game-title}</a></h3>
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
