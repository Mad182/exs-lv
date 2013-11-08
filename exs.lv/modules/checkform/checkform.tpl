<!-- START BLOCK : mod-cpanel -->
<h1>Profilu meklēšana un pārbaude</h1>
<div id="checkform">
	<form id="search-nick" method="post">
		<p><strong>Lietotājvārds:</strong></p>
		<p class="form-input-box">
			<input type="text" name="nick" value="{nick}"> 
			<input type="submit" name="submit" class="danger button" value="Meklēt">
		</p>
	</form>
	<form id="search-mail" method="post">
		<p><strong>E-pasts:</strong></p>
		<p class="form-input-box">
			<input type="text" name="mail" value="{mail}"> 
			<input type="submit" name="submit" class="danger button" value="Meklēt">
		</p>
	</form>
	<form id="search-ip" method="post">
		<p><strong>Pēdējā lietotā IP:</strong></p>
		<p class="form-input-box">
			<input type="text" name="ip" value="{ip}">		
			<input type="submit" name="submit" class="danger button" value="Meklēt">
		</p>
	</form>
	<form id="search-vip" method="post">
		<p><strong>Vispār lietota IP:</strong></p>
		<p class="form-input-box">
			<input type="text" name="vip" value="{vip}">		
			<input type="submit" name="submit" class="danger button" value="Meklēt">
		</p>
	</form>
	
	<!-- START BLOCK : search-results -->
	<p class="infop data-explanation"><strong>Nospiežot uz atrasta lietotājvārda, aplūkojama plašāka informācija!</strong></p>
	<table id="user-results">
		<tr>
			<th style="text-align:left;width:120px">Profils</th>
			<th style="width:120px">{ip-type}</th>
			<th style="width:90px">E-pasts</th>
			<th style="width:60px">Karma</th>
			<th style="width:50px">Dienas</th>
		</tr>
		<!-- START BLOCK : search-result -->
		<tr>
			<td class="get-user-info" data-id="{id}">
				<a href="/user/{id}">{nick}</a>
			</td>
			<td class="centered-result">{lastip}</td>
			<td class="centered-result">{mail}</td>
			<td class="centered-result">{karma}</td>
			<td class="centered-result">{date}</td>
		</tr>
		<tr class="hide-userdata">
			<td id="data-{id}" class="wider-row" colspan="5"></td>
		</tr>
		<!-- END BLOCK : search-result -->
	</table>
	<!-- END BLOCK : search-results -->
</div>
<!-- END BLOCK : mod-cpanel -->