<h1 style="position:relative;top:-15px">Lietotāju iesniegtās sūdzības{is_archive}</h1>
<ul id="report-tabs" class="tabs">
	<li><a href="/reports/miniblogs"{tab-miniblogs}>Miniblogi{count-mblogs}</a></li>
	<li><a href="/reports/articles"{tab-articles}>Raksti{count-articles}</a></li>
	<li><a href="/reports/gallery-comments"{tab-gallery-comments}>Bildes{count-gcomments}</a></li>
</ul>
<p id="archived_reports"><a href="{archive-addr}" title="Arhivētie ziņojumi">Sadaļas arhivētie ziņojumi</a></p>
<!-- START BLOCK : list-reports -->
<table id="mod_list_table" style="width:95%">
<tr class="">
	<th style="width:18%">Iesaistītie</th>
	<th style="width:49%">Pārkāpuma pamatojums</th>
	<th style="width:23%">Iesniegšanas laiks</th>
	<th style="width:10%">&nbsp;</th>
</tr>
<!-- START BLOCK : single-report -->
<tr>
	<td>
		<ul class="mod-table-list">
			<li><span class="stronger">Iesniedzis:</span>{reporter_nick}</li>
			<li><span class="stronger">Pārkāpējs:</span>{rule_breaker_nick}</li>
		</ul>
	</td>
	<td>
		{report_comment}
		<span class="report-addr">{report-place}</span>
		<!-- START BLOCK : archived-by -->
		<span class="report-addr"><strong>Arhivējis:</strong> {archived-by}</span>
		<!-- END BLOCK : archived-by -->
	</td>
	<td>
		<ul class="mod-table-list">
			<li>{report_created_at}</li>
			<li class="view_more"><a href="/warns/{rule_breaker_id}">Skatīt brīdinājumus ({warn_count})</a></li>
		</ul>
	</td>
	<td>
		<!-- START BLOCK : archive-button -->
		<a href="/reports/remove/{report_id}?url={addr}" class="button primary">Arhivēt</a>
		<!-- END BLOCK : archive-button -->
		<!-- START BLOCK : activation-button -->
		<a href="/reports/activate/{report_id}?url={addr}" class="button danger">Aktualizēt</a>
		<!-- END BLOCK : activation-button -->
	</td>
</tr>
<!-- END BLOCK : single-report -->
</table>	
<!-- END BLOCK : list-reports -->
<!-- START BLOCK : no-reports-found -->
<p class="no-reports"><strong>Nav nevienas iesniegtas sūdzības!</strong></p>
<!-- END BLOCK : no-reports-found -->