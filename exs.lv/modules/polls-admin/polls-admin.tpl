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
	<form class="form" action="/polladmin" method="post">
		<fieldset>
			<p>
				<label for="new-poll-q"><strong>Aptaujas jautājums</strong></label><br>
				<input type="text" class="title" name="new-poll-q" id="new-poll-q" style="width: 100%; max-width: 500px;" required />
			</p>
			<p><em>Ievadiet vismaz 2 atbilžu variantus:</em></p>
			<p>
				<label for="new-poll-a-1">1. atbilde</label><br>
				<input type="text" class="text" name="new-poll-a[]" id="new-poll-a-1" required />
			</p>
			<p>
				<label for="new-poll-a-2">2. atbilde</label><br>
				<input type="text" class="text" name="new-poll-a[]" id="new-poll-a-2" required />
			</p>
			<p>
				<label for="new-poll-a-3">3. atbilde</label><br>
				<input type="text" class="text" name="new-poll-a[]" id="new-poll-a-3" />
			</p>
			<p>
				<label for="new-poll-a-4">4. atbilde</label><br>
				<input type="text" class="text" name="new-poll-a[]" id="new-poll-a-4" />
			</p>
			<p>
				<label for="new-poll-a-5">5. atbilde</label><br>
				<input type="text" class="text" name="new-poll-a[]" id="new-poll-a-5" />
			</p>
			<p>
				<label for="new-poll-a-6">6. atbilde</label><br>
				<input type="text" class="text" name="new-poll-a[]" id="new-poll-a-6" />
			</p>
			<p>
				<label for="new-poll-a-7">7. atbilde</label><br>
				<input type="text" class="text" name="new-poll-a[]" id="new-poll-a-7" />
			</p>
			<p>
				<label for="new-poll-a-8">8. atbilde</label><br>
				<input type="text" class="text" name="new-poll-a[]" id="new-poll-a-8" />
			</p>
			<p>
				<label for="new-poll-a-9">9. atbilde</label><br>
				<input type="text" class="text" name="new-poll-a[]" id="new-poll-a-9" />
			</p>
			<p>
				<label for="new-poll-a-10">10. atbilde</label><br>
				<input type="text" class="text" name="new-poll-a[]" id="new-poll-a-10" />
			</p>

			<p>
				<input class="button primary" type="submit" value="Iesniegt aptauju" />
			</p>
		</fieldset>
	</form>
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
