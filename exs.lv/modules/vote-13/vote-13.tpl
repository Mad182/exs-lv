<!-- START BLOCK : vote-content -->
<form class="form" action="/{cat}/voted" method="post">
<fieldset>
	<legend>eXs party '13 aptauja</legend>
	<div class="vote-question">
		<p{dark-skin}><label for="user-name">Vārds:</label></p>
		<input type="text" id="user-name" class="text" name="user-name" value="" /><br />
		<span id="user-name-error" class="input-error"></span>
	</div>
	<div class="vote-question">
		<p{dark-skin}><label for="user-age">Vecums:</label></p>
		<input type="text" id="user-age" class="text" name="user-age" value="" /><br />
		<span id="user-age-error" class="input-error"></span>
	</div>
	<hr style="width:90%;margin:10px auto 20px" />
	<!--<div class="vote-question">
		<p>Vai veidot divu dienu pasākumu (sākums piektdienas vakarā, bet beigas - svētdienas rītā)?</p>
		<input id="days-yes" type="radio" name="days" value="1" checked="checked" /> <label for="days-yes">Jā</label><br />
		<input id="days-no" type="radio" name="days" value="0" /> <label for="days-no">Nē</label>
	</div>//-->
	<div class="vote-question">
		<p{dark-skin}>Kuri datumi Tev būtu vispieņemamākie?</p>
		<input id="2607" type="checkbox" name="date[]" value="0" /> <label for="2607">26.07 - 27.07</label><br />
		<input id="0208" type="checkbox" name="date[]" value="1" /> <label for="0208">02.08 - 03.08</label><br />
		<input id="0908" type="checkbox" name="date[]" value="2" /> <label for="0908">09.08 - 10.08</label><br />
		<input id="1608" type="checkbox" name="date[]" value="3" /> <label for="1608">16.08 - 17.08</label><br />
		<input id="2308" type="checkbox" name="date[]" value="4" /> <label for="2308">23.08 - 24.08</label>
	</div>
	<div class="vote-question">
		<p{dark-skin}>Lielākā summa, kādu esi gatavs maksāt par viesu mājas īri?</p>
		<input id="5" type="radio" name="cost" value="0" /> <label for="5">Ls 5</label><br />
		<input id="6" type="radio" name="cost" value="1" /> <label for="6">Ls 6</label><br />
		<input id="7" type="radio" name="cost" value="2" /> <label for="7">Ls 7</label><br />
		<input id="8" type="radio" name="cost" value="3" checked="checked" /> <label for="8">Ls 8</label><br />
		<input id="9" type="radio" name="cost" value="4" /> <label for="9">Ls 9</label><br />
		<input id="10" type="radio" name="cost" value="5" /> <label for="10">Ls 10</label>
	</div>
	<div class="vote-question">
		<p{dark-skin}>Vai Tu būtu gatavs veikt maksājumu ar pārskaitījumu jau pirms pasākuma norises? (Maksāšana uz vietas būtu nedaudz dārgāka.)</p>
		<input id="payment-yes" type="radio" name="payment" value="0" checked="checked" /> <label for="payment-yes">Jā</label><br />
		<input id="payment-no" type="radio" name="payment" value="1" /> <label for="payment-no">Nē</label><br />
		<input id="payment-other" type="radio" name="payment" value="2" /> <label for="payment-other">Maksātu uz vietas skaidrā naudā.</label>
	</div>
	<div class="vote-question">
		<p{dark-skin}>Lielākais attālums līdz viesu mājai no Rīgas, kāds tev šķiet pieņemams?</p>
		<input id="25km" type="radio" name="distance" value="0" /> <label for="25km">25 km</label><br />
		<input id="50km" type="radio" name="distance" value="1" /> <label for="50km">50 km</label><br />
		<input id="80km" type="radio" name="distance" value="2" /> <label for="80km">80 km</label><br />
		<input id="150km" type="radio" name="distance" value="3" /> <label for="150km">150 km</label><br />
		<input id="distance" type="radio" name="distance" value="4" checked="checked" /> <label for="distance">Attālums nav svarīgs</label> 
	</div>
	<p><input type="submit" class="button danger" name="submit" value="Iesniegt" /></p>
</fieldset>
</form>
<!-- END BLOCK : vote-content -->
<!-- START BLOCK : already-voted -->
<p style="text-align:center;font-size:15px;font-weight:bold;color:#fa3232;padding:50px;">Paldies par izteikto viedokli! Tiekamies jau ballītē!<br />(Rezultāti būs pieejami nedaudz vēlāk!)</p>
<!-- END BLOCK : already-voted -->
<!-- START BLOCK : vote-results -->
<h1>Aptaujas rezultāti</h1>
<p style="margin:0;padding:3px 10px;"><strong>Forma aizpildīta {data-count}.</strong></p>
	<!-- START BLOCK : vote-data -->
		<div class="vote-question"><p{dark-skin}>{question}</p></div>
		<table class="vote-table">
			<tr class="header"><td style="text-align:center">Vērtība</td><td>Balsu skaits</td></tr>
		<!-- START BLOCK : vote-data-field -->
			<tr>
				<td class="left-td" style="width:150px;">{field}</td>
				<td style="width:250px">
					<div class="vote-bar-outer"><div class="vote-bar" style="width:{bar-width}px"></div></div>&nbsp;&nbsp;&nbsp;{count} ({percents}%)
				</td>
			</tr>
		<!-- END BLOCK : vote-data-field -->
		</table>
	<!-- END BLOCK : vote-data -->
<!-- END BLOCK : vote-results -->