<h1>Logotās darbības</h1>

<form style="margin:0 0 0 50px" method="post">
	<label for="criteria" style="font-weight:bold">Meklēt pēc:</label>
	<select name="criteria" id="criteria">
		<option value="0"{selected-0}>lietotāja ID</option>
		<option value="1"{selected-1}>vietas ID</option>
		<option value="2"{selected-2}>darbības</option>
		<option value="3"{selected-3}>IP</option>
	</select>
	<label for="value" style="font-weight:bold">Vērtība:</label>
	<input type="text" name="value" value="{field-value}">
	<input type="submit" name="submit" value="Meklēt">
</form>
<p style="margin-left:20px">Meklējot pēc IP adreses, var norādīt tās sākuma daļu, beigas aizstājot ar %, piemēram "46.109%".</p>

<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>

<table class="table table-light">
	<tr class="light-header">
		<th>Lietotājs</th>
		<th>Vieta</th>
		<th>Darbība</th>
		<th>Laiks</th>
		<th>IP</th>
	</tr>
	<!-- START BLOCK : logs-list-node-->
	<tr>
		<td>{log-who}</td>
		<td>{log-place}</td>
		<td>{log-action}</td>
		<td>{log-time}</td>
		<td>{log-ip}</td>
	</tr>
	<!-- END BLOCK : logs-list-node-->
</table>

<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
