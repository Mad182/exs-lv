<!-- START BLOCK : polls_admin-body-->
<h1>{page-title}</h1>

<ul class="tabs">
	<li><a href="/?c=255" class="{exist-active}">Pievienot jaunu</a></li>
	<li><a href="/?c=255&amp;act=list" class="{list-active}">Esošās aptaujas</a></li>
</ul>

<div class="tabMain">

	<!-- START BLOCK : polls_admin-success-->
	<div class="form">
		<p class="success">
			Jautājums izveidots...<br />
			Komentāru tēma izveidota...<br />
			Atbilžu varianti izveidoti...
		</p>
	</div>
	<!-- END BLOCK : polls_admin-success-->

	<!-- START BLOCK : polls_admin-add-->
	<form class="form" action="" method="post">
		<fieldset>
			<p>
				<label for="new-poll-q">Aptaujas jautājums</label><br />
				<input type="text" class="title" name="new-poll-q" id="new-poll-q" />
			</p>
			<p>
				<label for="new-poll-q">1. atbilde</label><br />
				<input type="text" class="text" name="new-poll-a[]" id="new-poll-a" />
			</p>
			<p>
				<label for="new-poll-q">2. atbilde</label><br />
				<input type="text" class="text" name="new-poll-a[]" id="new-poll-a" />
			</p>
			<p>
				<label for="new-poll-q">3. atbilde</label><br />
				<input type="text" class="text" name="new-poll-a[]" id="new-poll-a" />
			</p>
			<p>
				<label for="new-poll-q">4. atbilde</label><br />
				<input type="text" class="text" name="new-poll-a[]" id="new-poll-a" />
			</p>
			<p>
				<label for="new-poll-q">5. atbilde</label><br />
				<input type="text" class="text" name="new-poll-a[]" id="new-poll-a" />
			</p>
			<p>
				<label for="new-poll-q">6. atbilde</label><br />
				<input type="text" class="text" name="new-poll-a[]" id="new-poll-a" />
			</p>
			<p>
				<label for="new-poll-q">7. atbilde</label><br />
				<input type="text" class="text" name="new-poll-a[]" id="new-poll-a" />
			</p>
			<p>
				<label for="new-poll-q">8. atbilde</label><br />
				<input type="text" class="text" name="new-poll-a[]" id="new-poll-a" />
			</p>
                        <p>
                                <label for="new-poll-q">9. atbilde</label><br />
                                <input type="text" class="text" name="new-poll-a[]" id="new-poll-a" />
                        </p>
                        <p>
                                <label for="new-poll-q">10. atbilde</label><br />
                                <input type="text" class="text" name="new-poll-a[]" id="new-poll-a" />
                        </p>
                        <p>
                                <label for="new-poll-q">11. atbilde</label><br />
                                <input type="text" class="text" name="new-poll-a[]" id="new-poll-a" />
                        </p>
                        <p>
                                <label for="new-poll-q">12. atbilde</label><br />
                                <input type="text" class="text" name="new-poll-a[]" id="new-poll-a" />
                        </p>

			<p>
				<input class="button" type="submit" value="Nosūtīt" />
			</p>
		</fieldset>
	</form>
	<!-- END BLOCK : polls_admin-add-->

	<!-- START BLOCK : polls_admin-list-->
	<table id="pm-table">
		<tr>
			<th class="title">Jautājums</th>
			<th>Tēma</th>
        </tr>
		<!-- START BLOCK : polls_admin-list-node-->
		<tr>
			<td>{question}</td>
			<td>{topic}</td>
		</tr>
		<!-- END BLOCK : polls_admin-list-node-->
	</table>
	<!-- END BLOCK : polls_admin-list-->

</div>
<!-- END BLOCK : polls_admin-body-->
