<!-- START BLOCK : banned-public-->
{bmsg}
<!-- END BLOCK : banned-public-->


<!-- START BLOCK : banned-admin-->
<h2>Bloķētie lietotāji</h2>
<table class="main-table" style="margin: 0 auto;font-size:80%">
	<tr>
		<th>Lietotājs</th>
		<th>Iemesls</th>
		<th>Vieta</th>
		<th>Datums</th>
		<th>Del</th>
	</tr>
	<!-- START BLOCK : banned-admin-node-->
	<tr>
		<td>{nick}<br />{banned-ip}[<a title="Noņemt IP banu" class="red" href="/?c={category-id}&amp;delete_ip={banned-id}"><img src="http://exs.lv/bildes/x.png" alt="x" /></a>]</td>
		<td>{banned-reason} (<a href="/user/{banned-author}">{anick}</a>)</td>
		<td>{where}</td>
		<td style="text-align: right;width:126px">No:&nbsp;{banned-date}<br />Līdz:&nbsp;{banned-until}</td>
		<td>
<!-- START BLOCK : rmban-->
		[<a title="Noņemt liegumu" class="red" href="/?c={category-id}&amp;delete={banned-id}"><img src="http://exs.lv/bildes/x.png" alt="x" /></a>]
<!-- END BLOCK : rmban-->
		</td>
	</tr>
	<!-- END BLOCK : banned-admin-node-->
</table>
<!-- END BLOCK : banned-admin-->
