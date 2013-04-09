<h1>Iepriekšējo aptauju arhīvs</h1>
<!-- START BLOCK : polls-box-->
<div class="poll-archive-box">
	<div class="box">
		<p><strong>{polls-title}</strong></p>
		<!-- START BLOCK : polls-answers-->
		<ol class="poll-answers">
			<!-- START BLOCK : polls-answers-node-->
			<li>
				{polls-answer-question}
				<div>
					<span>{polls-answer-percentage}%</span>
					<div style="width: {polls-answer-percentage}%;"></div>
				</div>
			</li>
			<!-- END BLOCK : polls-answers-node-->
		</ol>
		<p class="bottom">
			Balsis: {polls-totalvotes} | <a href="{url}">Komentāri</a>
		</p>
		<!-- END BLOCK : polls-answers-->
	</div>
</div>
<!-- END BLOCK : polls-box-->
