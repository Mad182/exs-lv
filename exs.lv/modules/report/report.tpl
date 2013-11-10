<!-- START BLOCK : report-form -->
<div id="outer-form-block">
	<p id="report-title">Pārkāpuma ziņojums</p>
	<div id="form-block">
		<p><strong class="report-item">Pārkāpējs:</strong> {offender}</p>
		<form id="report-form" method="post" action="{action}">
		
			<p class="report-content"><strong class="report-item">Saturs:</strong><br>{entry-text}</p>
			
			<p class="report-item"><strong>Iemesls sūdzībai:</strong></p>
			<p><textarea id="report-txtarea" name="report-reason"></textarea></p>
			
			<p>
				<input class="danger button" type="submit" name="submit" value="Ziņot">
				<a class="fancy-close button primary" href="javascript:void(0)">Pārdomāju!</a></p>
		</form>
	</div>
</div>
<div class="report-response"></div>
<!-- END BLOCK : report-form -->
<!-- START BLOCK : error-message -->
<p class="report-error">{error-message}</p>
<!-- END BLOCK : error-message -->