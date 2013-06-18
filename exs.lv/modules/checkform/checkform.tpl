<!-- START BLOCK : mod-cpanel -->
<h1>Profilu meklēšana un pārbaude</h1>
<div id="checkform">
	<form id="search-nick" action="" method="post">
		<p><strong>Lietotājvārds:</strong></p>
		<p class="form-input-box">
			<input type="text" name="nick" value="{nick}" /> 
			<input type="submit" name="submit" class="danger button" value="Meklēt" />
		</p>
	</form>
	<form id="search-mail" action="" method="post">
		<p><strong>E-pasts:</strong></p>
		<p class="form-input-box">
			<input type="text" name="mail" value="{mail}" /> 
			<input type="submit" name="submit" class="danger button" value="Meklēt" />
		</p>
	</form>
	<form id="search-ip" action="" method="post">
		<p><strong>Pēdējā lietotā IP:</strong></p>
		<p class="form-input-box">
			<input type="text" name="ip" value="{ip}" />		
			<input type="submit" name="submit" class="danger button" value="Meklēt" />
		</p>
	</form>
	<form id="search-vip" action="" method="post">
		<p><strong>Vispār lietota IP:</strong></p>
		<p class="form-input-box">
			<input type="text" name="vip" value="{vip}" />		
			<input type="submit" name="submit" class="danger button" value="Meklēt" />
		</p>
	</form>
	<!-- START BLOCK : search-results -->
	<!-- START BLOCK : search-ignored -->
		<p><strong class="res-info">Rezultāti:</strong> {res-count} :: <strong class="res-info">Parādīti:</strong> {res-count-2}</p>
	<!-- END BLOCK : search-ignored -->
	<table id="user-results">
		<tr>
			<th style="text-align:left">Profils</th>
			<th>{ip-type}</th>
			<th>E-pasts</th>
			<th>Karma</th>
			<th>Dienas</th>
		</tr>
		<!-- START BLOCK : search-result -->
		<tr>
			<td style="width:120px;" class="get-user-info" data-id="{id}"><a href="/user/{id}">{nick}</a></td>
			<td class="centered-result" style="width:120px;">{lastip}</td>
			<td class="centered-result" style="width:90px;">{mail}</td>
			<td class="centered-result" style="width:60px;">{karma}</td>
			<td class="centered-result" style="width:50px;">{date}</td>
		</tr>
		<tr class="hide-userdata"><td id="data-{id}" class="wider-row" colspan="5"></td></tr>
		<!-- END BLOCK : search-result -->
	</table>
	<!-- END BLOCK : search-results -->
</div>
<!-- END BLOCK : mod-cpanel -->