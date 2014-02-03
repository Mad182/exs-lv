<h1 style="position:relative;top:-15px">Lietotāju iesniegtās sūdzības{is_archive}</h1>
<ul id="report-tabs" class="tabs">
	<li><a href="/reports/miniblogs"{tab-miniblogs}>Miniblogi{count-mblogs}</a></li>
	<li><a href="/reports/articles"{tab-articles}>Raksti{count-articles}</a></li>
	<li><a href="/reports/gallery-comments"{tab-gallery-comments}>Bildes{count-gcomments}</a></li>
</ul>

<!-- START BLOCK : report-list-container -->

<!-- START BLOCK : list-reports -->
<h2>{report-title}</h2>
<table class="mod-list-table" style="width:95%;margin-bottom:40px">
	<tr class="">
		<th style="width:25%">Iesaistītie</th>
		<th style="width:48%">Pārkāpuma pamatojums</th>
		<th style="width:20%;padding-left:2px;text-align:left">Iesniegšanas laiks</th>
        <!-- START BLOCK : archive-button-header -->
		<th style="width:7%">&nbsp;</th>
        <!-- END BLOCK : archive-button-header -->
	</tr>
	<!-- START BLOCK : single-report -->
	<tr>
		<td style="padding:7px 10px 7px 2px">
			<ul class="list-table">
				<li><span class="stronger">Iesniedzis:</span>{reporter_nick}</li>
				<li><span class="stronger">Pārkāpējs:</span>{rule_breaker_nick}</li>
				<!-- START BLOCK : archived-by -->
				<li><span class="stronger" style="padding-left:9px">Arhivēja:</span>{archivator_nick}</li>
				<!-- END BLOCK : archived-by -->
			</ul>
		</td>
		<td style="padding:7px 10px 7px 2px">
			{report-comment}
			<!-- START BLOCK : show-full-content -->
			<br><a href="#" class="report-full">Sūdzība ir saīsināta! Skatīt sūdzību pilnā apjomā.</a>
			<!-- END BLOCK : show-full-content -->
			<span class="report-addr">
				{report-place} 			
				<!-- START BLOCK : display-original-content -->
				(<a class="get-report-content" href="/reports/show_content/{report_id}" title="Apskatīt ieraksta saturu">#</a>)
				<!-- END BLOCK : display-original-content -->
			</span>
			<div class="report-full-content" style="display:none">{full-content}</div>
		</td>
		<td style="padding:7px 0 2px">
			<ul class="list-table">
				<li>{report_created_at}</li>
				<li class="view_more"><a href="/warns/{rule_breaker_id}">Skatīt brīdinājumus ({warn_count})</a></li>
			</ul>
		</td>
        <!-- START BLOCK : archive-button -->
		<td style="padding:7px 0">			
			<a href="/reports/remove/{report_id}" class="button primary report-archive">Arhivēt</a>
		</td>
        <!-- END BLOCK : archive-button -->
	</tr>
	<!-- END BLOCK : single-report -->
</table>	
<!-- END BLOCK : list-reports -->

<!-- START BLOCK : no-reports-found -->
<p class="no-reports"><strong>Nav nevienas {report-type}!</strong></p>
<!-- END BLOCK : no-reports-found -->

<!-- END BLOCK : report-list-container -->