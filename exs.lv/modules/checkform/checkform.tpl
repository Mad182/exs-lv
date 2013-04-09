<!-- START BLOCK : mod-cpanel -->
<h1>Lapas lietotāju kontroles forma</h1>
<div id="checkform">
	<form id="search-nick" action="" method="post">
		<p><strong>Lietotājvārds:</strong></p>
		<p class="form-input-box">
			<input type="text" name="nick" value="{niks}" /> 
			<input type="submit" name="submit" class="danger button" value="Meklēt" />
		</p>
	</form>
	<form id="search-mail" action="" method="post">
		<p><strong>E-pasts:</strong></p>
		<p class="form-input-box">
			<input type="text" name="mail" value="{mails}" /> 
			<input type="submit" name="submit" class="danger button" value="Meklēt" />
		</p>
	</form>
	<form id="search-skype" action="" method="post">
		<p><strong>Pēdējā lietotā IP:</strong></p>
		<p class="form-input-box">
			<input type="text" name="ip" value="{aipii}" />		
			<input type="submit" name="submit" class="danger button" value="Meklēt" />
		</p>
	</form>
	<!-- START BLOCK : search-results -->
	<!-- START BLOCK : search-ignored -->
		<p><strong class="res-info">Rezultāti:</strong> {res-count} :: <strong class="res-info">Parādīti:</strong> {res-count-2}</p>
	<!-- END BLOCK : search-ignored -->
	<table id="user-results">
		<tr>
			<th>Niks</th>
			<th>IP</th>
			<th>E-pasts</th>
			<th>Karma</th>
			<th>Dienas</th>
		</tr>
		<!-- START BLOCK : search-result -->
		<tr>
			<td style="width:120px;" class="get-user-info" data-id="{id}"><a href="/user/{id}">{nick}</a></td>
			<td style="width:120px;">{lastip}</td>
			<td style="width:90px;text-align:center">{mail}</td>
			<td style="width:80px;text-align:center">{karma}</td>
			<td style="width:50px;text-align:center">{date}</td>
		</tr>
		<tr class="hide-userdata"><td id="data-{id}" class="wider-row" colspan="5"></td></tr>
		<!-- END BLOCK : search-result -->
	</table>
	<!-- END BLOCK : search-results -->
</div>
<!-- END BLOCK : mod-cpanel -->