<!-- START BLOCK : report-form -->
<div id="outer-form-block">
	<p id="report-title" style="clear:both">Pārkāpuma ziņojums</p>
	<div id="form-block" style="padding-bottom:0">
		<p><strong class="report-item">Pārkāpējs:</strong> {offender}</p>
		<form method="post" action="{action}" style="margin-bottom:5px">
			<input type="hidden" name="anti-xsrf" value="{xsrf}">
			<p class="report-content"><strong class="report-item">Saturs:</strong><br>{entry-text}</p>

			<p class="report-item"><strong>Iemesls sūdzībai:</strong></p>
			<p><textarea id="report-txtarea" name="report-reason"></textarea></p>

			<p style="text-align:right">
				<input class="primary button" type="submit" name="submit" value="ZIŅOT">
				<a class="fancy-close button" href="javascript:void(0)">pārdomāju</a>
            </p>
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

<script type="text/javascript">
    $(document).ready(function($) {
        /**
         *  Nospiežot uz "submit" pogas, iesniegts sūdzību.
         */
        $('#outer-form-block').on('submit', 'form', function() {
            $form = $(this);
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: $form.attr('action') + '?_=1',
                data: $form.serialize(),
                success: function(response) {
                    $('.report-response').html('');
                    if (response.state == 'success') {
                        $('#outer-form-block').toggle('slow');
                        $('.report-response').attr('class', 'report-response-good')
                                             .html(response.content);
                    } else {
                        $('.report-response').html(response.content);
                    }
                }
            });
            return false;
        });
        /**
         *  Nospiežot uz "Pārdomāju" pogas, aizvērs atvērto fancybox logu.
         */
        /* aizver atvērto fancybox, nospiežot uz "Pārdomāju" podziņas */
        $('#outer-form-block').on('click', '.fancy-close', function() {
            $.fancybox.close();
            return false;
        });
    });
</script>
<!-- END BLOCK : report-form -->
