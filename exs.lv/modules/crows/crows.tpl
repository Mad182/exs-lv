<h1 style="position:relative;top:-15px">Pēdējās brīvībā palaistās vārnas...</h1>
<!-- START BLOCK : warns-list -->
<table class="mod-list-table">
	<tr class="">
		<th style="width:3%">&nbsp;</td>
		<th style="width:18%">Iesaistītie</th>
		<th style="width:63%">Iemesls</th>
		<th style="width:16%">Laiks</th>
	</tr>
	<!-- START BLOCK : single-warn -->
	<tr{removed-warn}>
		<td class="counter">{row_counter}</td>
		<td>
			<ul class="list-table">
				<li><span class="stronger" style="margin-left:11px">No:</span>{creator_nick}</li>
				<li><span class="stronger">Kam:</span>{offender_nick}</li>
                <!-- START BLOCK : warn-site -->
				<li><span class="stronger">Vieta:</span>{site}</li>
                <!-- END BLOCK : warn-site -->
			</ul>
		</td>
		<td>{warn_reason} {removal-reason}</td>
		<td style="padding-left:0;padding-right:0">
			<ul class="list-table">
				<li>{warn_created_at}</li>
				<li class="view_more"><a href="/warns/{offender_id}">Skatīt vairāk</a></li>
			</ul>
		</td>
	</tr>
	<!-- END BLOCK : single-warn -->
</table>
<!-- END BLOCK : warns-list -->

<!-- START BLOCK : no-warns-found -->
<p>Nav neviena izteikta brīdinājuma! Vai tā var būt?</p>
<!-- END BLOCK : no-warns-found -->
