<h1>Samazināt GIF animāciju</h1>

<!-- START BLOCK : resize-->

<script type="text/javascript">

	$(function($){

		$('#resize').live('submit', function() {
			$('#crop-result').fadeTo(250, 0.4);
			$('#crop-result').html('<img src="/bildes/ezgif/loading.gif" alt="Loading..." />');
			$.ajax({
				type: "POST",
				url: $(this).attr('action')+'?_=1',
				data: $(this).serialize(),
				success: function(data) {
					$('#crop-result').html(data);
					$('#crop-result').fadeTo(250, 1);
				}
			});
			return false;
		});

	});


</script>

<div>Citi rīki: {menu}</div>

<img src="http://img.exs.lv/tmp/{file}" id="target" alt="" /><br />

<form id="resize" class="form coords" action="/resize">
	<input type="hidden" value="{file}" name="file">
	<p>
		Esošais platums: {width}px<br />
		Esošais augstums: {height}px<br />
		<label>Jaunais platums: <input type="text" size="4" id="width" name="width" class="text tiny" />&nbsp;(0 - auto)</label><br />
		<label>Jaunais augstums: <input type="text" size="4" id="height" name="height" class="text tiny" />&nbsp;(0 - auto)</label>
	</p>

	<p>
	<label for="method">Samazināšanas metode*:</label><br />
	<select name="method" id="method">
		<option value="im">ImageMagick</option>
		<option value="gifsicle">Gifsicle</option>
		<option value="im-coalesce">ImageMagick + coalesce</option>
	</select>
	</p>

	<input type="submit" class="button primary" value="Mainīt izmēru!" name="crop" />

</form>

<!-- END BLOCK : resize-->

<!-- START BLOCK : resize-upl-->

<form class="form" action="/resize" method="post" enctype="multipart/form-data">
	<fieldset>
		<legend>Norādi attēlu</legend>
		<p>
			<label>Ielādēt attēlu no datora:</label><br />
			<input type="file" name="new-image" />
		</p>
		<p>
			<label>Vai iekopēt saiti uz attēlu:</label><br />
			<input type="text" class="text" name="new-image-url" />
		</p>
		<p>
			(Tikai gif attēliem, līdz 12 MB!)
		</p>
		<p><input type="submit" class="button primary" value="Ielādēt!" /></p>
	</fieldset>
</form>
<p class="small">Tu vari izmantot arī: http://lv.ezgif.com/resize?url=<span style="color:#393;">http://link/to/image.gif</span></p>
<!-- END BLOCK : resize-upl-->

<h3>Rezultāts:</h3>
<div id="crop-result"></div>

<p>
	* GIF animāciju samazināšana ir nedaudz sarežģīts process, un bieži vien atkarībā no vajadzībām ir jāizvēlas starp labāku kvalitāti vai mazāku faila izmēru.<br />
	Izmēģini citu samazināšanas metodi, ja neesi apmierināts ar rezultātu (piemēram, bojāts attēls vai slikta kvalitāte).<br />
	&quot;Gifsicle&quot; parasti strādā visātrāk un izveido vismazākos failu izmērus, bet tas ir paredzēts ātrumam, nevis kvalitātei.
</p>
