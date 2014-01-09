<!-- START BLOCK : report-form -->
<div id="outer-form-block">
	<p id="report-title" style="clear:both">Pārkāpuma ziņojums</p>
	<div id="form-block">
		<p><strong class="report-item">Pārkāpējs:</strong> {offender}</p>
		<form id="report-form" method="post" action="{action}">
			<input type="hidden" name="anti-xsrf" value="{xsrf}">
			<p class="report-content"><strong class="report-item">Saturs:</strong><br>{entry-text}</p>

			<p class="report-item"><strong>Iemesls sūdzībai:</strong></p>
			<p><textarea id="report-txtarea" name="report-reason"></textarea></p>

			<p>
				<input class="danger button" type="submit" name="submit" value="Ziņot">
				<a class="fancy-close button primary" href="javascript:void(0)">Pārdomāju!</a></p>
		</form>
	</div>
	<div id="report-description">
		<p style="color:orangered"><strong>Uzmanību!</strong></p>
		<!-- START BLOCK : main-exs-report-info -->
		<p>
			Forma paredzēta, lai ziņotu par lietotāju, kurš tā vai citādi pārkāpis lapas noteikumus!<br><br>
			Pamatojot pārkāpuma iemeslu, jānorāda noteikums, kas pārkāpts!<br><br>
			Par neatbilstošu formas izmantošanu sūdzības iesniedzējs saņems brīdinājumu.
		</p>
		<!-- END BLOCK : main-exs-report-info -->
		<!-- START BLOCK : sub-exs-report-info -->
		<p>
			Forma paredzēta, lai ziņotu par lietotāju, kurš tā vai citādi pārkāpis lapas noteikumus!<br><br>
			Par neatbilstošu formas izmantošanu sūdzības iesniedzējs var tikt sodīts!
		</p>
		<!-- END BLOCK : sub-exs-report-info -->
		<p><a href="/read/lietosanas-noteikumi" target="_blank">Lapas noteikumi</a></p>
	</div>
</div>
<div class="clearfix"></div>
<div class="report-response"></div>
<!-- END BLOCK : report-form -->
<!-- START BLOCK : error-message -->
<p class="report-error">{error-message}</p>
<!-- END BLOCK : error-message -->