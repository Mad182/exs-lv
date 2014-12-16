<!-- START BLOCK : banned-public-->
{bmsg}
<!-- END BLOCK : banned-public-->

<!-- START BLOCK : table-of-banned -->
	<h2>Bloķētie lietotāji</h2>
	<table class="table table-light" style="margin:0 auto;font-size:80%">
		<tr class="light-header" style="text-align:center">
			<th style="width:120px">Lietotājs</th>
			<th>Iemesls</th>
			<th style="width:150px;text-align:right">Datums</th>
			<th style="width:20px">Del</th>
		</tr>
		<!-- START BLOCK : banned-row -->
		<tr>
			<td>{nick}<br />{banned-ip}&nbsp;<a title="Noņemt IP banu" class="red" href="/?c={category-id}&amp;delete_ip={banned-id}&amp;token={token}"><img src="http://exs.lv/bildes/x.png" alt="x" /></a></td>
			<td>{banned-reason} (<a href="/user/{banned-author}">{anick}</a>)</td>
			<td style="text-align:right;width:126px;font-size:11px">Vieta:&nbsp;{where}<br />No:&nbsp;{banned-date}<br />Līdz:&nbsp;{banned-until}</td>
			<td style="text-align:center">
				<!-- START BLOCK : remove-ban -->
				<a title="Noņemt liegumu" class="confirm red" href="/?c={category-id}&amp;delete={banned-id}&amp;token={token}">
					<img class="delete-ban" src="http://exs.lv/bildes/fugue-icons/cross-button.png" alt="x" />
				</a>
				<!-- END BLOCK : remove-ban -->
			</td>
		</tr>
		<!-- END BLOCK : banned-row -->
	</table>
<!-- END BLOCK : table-of-banned -->

