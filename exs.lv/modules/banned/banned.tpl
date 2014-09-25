<!-- START BLOCK : banned-public-->
{bmsg}
<!-- END BLOCK : banned-public-->

<!-- START BLOCK : banned-by-group -->
<h2>Bloķēto profilu grupas</h2>
<table class="table table-light table-banned" style="margin: 0 auto 50px;font-size:80%">
	<tr class="light-header">
		<th colspan="2" style="width:130px;text-align:left">Lietotājs</th>
		<th style="width:310px;text-align:left">Iemesls</th>
		<th style="width:30px"></th>
		<th style="width:15px"></th>
	</tr>
	<!-- START BLOCK : by-group-outer -->
		<!-- START BLOCK : by-group -->
		<tr>
			<td colspan="2" style="border-right:0">
				{nick}
				<!-- START BLOCK : remove-ip-main -->
				<br><span class="banned-ip">{banned-ip}
					<a title="Noņemt IP banu" class="confirm red" href="/?c={category-id}&amp;delete_ip={banned-id}"><img class="delete-ban" src="http://exs.lv/bildes/x.png" alt="x" /></a>
				</span>
				<!-- END BLOCK : remove-ip-main -->
				<img class="show-banned pointer" data-id="{group-id}" src="/bildes/fugue-icons/arrow-down.png">
			</td>
			<td>{banned-reason} (<a href="/user/{banned-author}">{anick}</a>)</td>
			<td style="text-align:right;width:126px;font-size:11px">Vieta:&nbsp;{where}<br />No:&nbsp;{banned-date}<br />Līdz:&nbsp;{banned-until}</td>
			<td>
				<!-- START BLOCK : rmban-main -->
				<a title="Noņemt liegumu" class="confirm red" href="/?c={category-id}&amp;delete={banned-id}">
					<img class="delete-ban" src="http://exs.lv/bildes/fugue-icons/cross-button.png" alt="x" />
				</a>
				<!-- END BLOCK : rmban-main -->
			</td>
		</tr>
		<!-- END BLOCK : by-group -->
		<!-- START BLOCK : by-group-child -->
		<tr class="is-hidden child-of-{group-id}">
			<td class="child-pattern" style="width:10px">&nbsp;</td>
			<td>{nick}
				<!-- START BLOCK : remove-ip-child -->
				<br />{banned-ip} 
				<a title="Noņemt IP banu" class="confirm red" href="/?c={category-id}&amp;delete_ip={banned-id}"><img class="delete-ban" src="http://exs.lv/bildes/x.png" alt="x" /></a>
				<!-- END BLOCK : remove-ip-child -->
			</td>
			<td>{banned-reason} (<a href="/user/{banned-author}">{anick}</a>)</td>
			<td style="text-align:right;width:126px;font-size:11px">Vieta:&nbsp;{where}<br />No:&nbsp;{banned-date}<br />Līdz:&nbsp;{banned-until}</td>
			<td>
				<!-- START BLOCK : rmban-child -->
				<a title="Noņemt liegumu" class="confirm red" href="/?c={category-id}&amp;delete={banned-id}">
					<img class="delete-ban" src="http://exs.lv/bildes/fugue-icons/cross-button.png" alt="x" />
				</a>
				<!-- END BLOCK : rmban-child -->
			</td>
		</tr>
		<!-- END BLOCK : by-group-child -->
	<!-- END BLOCK : by-group-outer -->
</table>
<!-- END BLOCK : banned-by-group -->

<!-- START BLOCK : banned-by-single -->
<h2>Atsevišķi bloķētie profili</h2>
<table class="table table-light" style="margin:0 auto;font-size:80%">
	<tr class="light-header">
		<th style="width:120px;text-align:left">Lietotājs</th>
		<th style="width:250px;text-align:left">Iemesls</th>
		<th style="width:20px"></th>
		<th style="width:15px"></th>
	</tr>
	<!-- START BLOCK : by-single -->
	<tr>
		<td>{nick}
			<!-- START BLOCK : remove-ip -->
			<br><span class="banned-ip">{banned-ip}
				<a title="Noņemt IP banu" class="confirm red" href="/?c={category-id}&amp;delete_ip={banned-id}"><img class="delete-ban" src="http://exs.lv/bildes/x.png" alt="x" /></a>
			</span>
			<!-- END BLOCK : remove-ip -->
		</td>
		<td>{banned-reason} (<a href="/user/{banned-author}">{anick}</a>)</td>
		<td style="text-align:right;width:126px;font-size:11px">
			Vieta:&nbsp;{where}<br />
			No:&nbsp;{banned-date}<br />
			Līdz:&nbsp;{banned-until}
		</td>
		<td>
			<!-- START BLOCK : rmban-single -->
			<a title="Noņemt liegumu" class="confirm red" href="/?c={category-id}&amp;delete={banned-id}">
				<img class="delete-ban" src="http://exs.lv/bildes/fugue-icons/cross-button.png" alt="x" />
			</a>
			<!-- END BLOCK : rmban-single -->
		</td>
	</tr>
	<!-- END BLOCK : by-single-->
</table>
<!-- END BLOCK : banned-by-single -->

<!-- START BLOCK : banned-by-global-->
	<h2>Bloķētie lietotāji</h2>
	<table class="table" style="margin: 0 auto;font-size:80%">
		<tr>
			<th>Lietotājs</th>
			<th>Iemesls</th>
			<th>Datums</th>
			<th>Del</th>
		</tr>
		<!-- START BLOCK : by-global -->
		<tr>
			<td>{nick}<br />{banned-ip}[<a title="Noņemt IP banu" class="red" href="/?c={category-id}&amp;delete_ip={banned-id}"><img src="http://exs.lv/bildes/x.png" alt="x" /></a>]</td>
			<td>{banned-reason} (<a href="/user/{banned-author}">{anick}</a>)</td>
			<td style="text-align:right;width:126px;font-size:11px">Vieta:&nbsp;{where}<br />No:&nbsp;{banned-date}<br />Līdz:&nbsp;{banned-until}</td>
			<td>
				<!-- START BLOCK : rmban-3 -->
				[<a title="Noņemt liegumu" class="red" href="/?c={category-id}&amp;delete={banned-id}"><img src="http://exs.lv/bildes/x.png" alt="x" /></a>]
				<!-- END BLOCK : rmban-3 -->
			</td>
		</tr>
		<!-- END BLOCK : by-global -->
	</table>
<!-- END BLOCK : banned-by-global -->
