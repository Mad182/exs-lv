<!-- START BLOCK : polls_admin-body-->
<h1>{page-title}</h1>

<!-- START BLOCK : polls_admin-tabs-mod-->
<ul class="tabs">
	<li><a href="/polladmin" class="{exist-active}">Pievienot jaunu</a></li>
	<li><a href="/polladmin?act=pending" class="{pending-active}">Jaunās aptaujas ({pending-count})</a></li>
	<li><a href="/polladmin?act=list" class="{list-active}">Esošās aptaujas</a></li>
</ul>
<!-- END BLOCK : polls_admin-tabs-mod-->

<!-- START BLOCK : polls_admin-tabs-user-->
<ul class="tabs">
	<li><a href="/polladmin" class="active">Iesniegt jaunu aptauju</a></li>
</ul>
<!-- END BLOCK : polls_admin-tabs-user-->

<div class="tabMain">

	<!-- START BLOCK : polls_admin-success-->
	<div class="form">
		<p class="success">
			{success-message}
		</p>
	</div>
	<!-- END BLOCK : polls_admin-success-->

	<!-- START BLOCK : polls_admin-add-->
	<div class="poll-form-card">
		<div class="poll-form-header">
			<h2>Izveidot jaunu aptauju</h2>
			<p>Iesniedz savu aptaujas jautājumu un atbilžu variantus. Pēc moderatora apstiprināšanas tā tiks publicēta portālā!</p>
		</div>

		<form action="/polladmin" method="post" class="poll-admin-form">
			<div class="poll-field-group">
				<label for="new-poll-q" class="poll-label">Aptaujas jautājums <span class="required">*</span></label>
				<input type="text" class="poll-input title-input" name="new-poll-q" id="new-poll-q" placeholder="Piemēram: Kura ir tava mīļākā spēle?" required />
			</div>

			<div class="poll-answers-section">
				<label class="poll-label">Atbilžu varianti <span class="poll-sublabel">(ievasdiet vismaz 2 atbildes)</span></label>
				<div class="poll-answers-grid">
					<div class="poll-answer-item">
						<span class="answer-num">1</span>
						<input type="text" class="poll-input" name="new-poll-a[]" id="new-poll-a-1" placeholder="1. atbilde" required />
					</div>
					<div class="poll-answer-item">
						<span class="answer-num">2</span>
						<input type="text" class="poll-input" name="new-poll-a[]" id="new-poll-a-2" placeholder="2. atbilde" required />
					</div>
					<div class="poll-answer-item">
						<span class="answer-num">3</span>
						<input type="text" class="poll-input" name="new-poll-a[]" id="new-poll-a-3" placeholder="3. atbilde (neobligāti)" />
					</div>
					<div class="poll-answer-item">
						<span class="answer-num">4</span>
						<input type="text" class="poll-input" name="new-poll-a[]" id="new-poll-a-4" placeholder="4. atbilde (neobligāti)" />
					</div>
					<div class="poll-answer-item">
						<span class="answer-num">5</span>
						<input type="text" class="poll-input" name="new-poll-a[]" id="new-poll-a-5" placeholder="5. atbilde (neobligāti)" />
					</div>
					<div class="poll-answer-item">
						<span class="answer-num">6</span>
						<input type="text" class="poll-input" name="new-poll-a[]" id="new-poll-a-6" placeholder="6. atbilde (neobligāti)" />
					</div>
					<div class="poll-answer-item">
						<span class="answer-num">7</span>
						<input type="text" class="poll-input" name="new-poll-a[]" id="new-poll-a-7" placeholder="7. atbilde (neobligāti)" />
					</div>
					<div class="poll-answer-item">
						<span class="answer-num">8</span>
						<input type="text" class="poll-input" name="new-poll-a[]" id="new-poll-a-8" placeholder="8. atbilde (neobligāti)" />
					</div>
				</div>
			</div>

			<div class="poll-form-actions">
				<button class="button primary btn-submit-poll" type="submit">
					Iesniegt aptauju &rarr;
				</button>
			</div>
		</form>
	</div>
	<!-- END BLOCK : polls_admin-add-->

	<!-- START BLOCK : polls_admin-pending-->
	<!-- START BLOCK : polls_admin-pending-none-->
	<p>Nav nevienas jaunas aptaujas, kas gaidītu apstiprināšanu.</p>
	<!-- END BLOCK : polls_admin-pending-none-->

	<!-- START BLOCK : polls_admin-pending-list-->
	<table id="pm-table">
		<tr>
			<th class="title">Jautājums</th>
			<th>Atbildes</th>
			<th>Iesniedza</th>
			<th>Datums</th>
			<th>Darbības</th>
		</tr>
		<!-- START BLOCK : polls_admin-pending-node-->
		<tr>
			<td><strong>{question}</strong></td>
			<td>{answers}</td>
			<td>{author}</td>
			<td>{date}</td>
			<td style="white-space: nowrap;">
				<a href="/polladmin?act=approve&amp;id={id}" class="button small primary" onclick="return confirm('Apstiprināt un publicēt šo aptauju?');">Apstiprināt</a>
				<a href="/polladmin?act=delete&amp;id={id}" class="button small red" onclick="return confirm('Noraidīt un dzēst šo aptauju?');">Noraidīt</a>
			</td>
		</tr>
		<!-- END BLOCK : polls_admin-pending-node-->
	</table>
	<!-- END BLOCK : polls_admin-pending-list-->
	<!-- END BLOCK : polls_admin-pending-->

	<!-- START BLOCK : polls_admin-list-->
	<table id="pm-table">
		<tr>
			<th class="title">Jautājums</th>
			<th>Tēma</th>
			<th>Darbības</th>
		</tr>
		<!-- START BLOCK : polls_admin-list-node-->
		<tr>
			<td>{question}</td>
			<td>{topic}</td>
			<td>
				<a href="/polladmin?act=delete&amp;id={id}" class="button small red" onclick="return confirm('Dzēst šo aptauju?');">Dzēst</a>
			</td>
		</tr>
		<!-- END BLOCK : polls_admin-list-node-->
	</table>
	<!-- END BLOCK : polls_admin-list-->

</div>
<!-- END BLOCK : polls_admin-body-->
