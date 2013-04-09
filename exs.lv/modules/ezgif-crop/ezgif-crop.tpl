<h1>Crop animated gif image</h1>

<!-- START BLOCK : crop-->

<script type="text/javascript">

	$(function($){

		$('#target').Jcrop({
			onChange:   showCoords,
			onSelect:   showCoords,
			onRelease:  clearCoords
		});


		$('#coords').live('submit', function() {
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

	function showCoords(c) {
		$('#x1').val(c.x);
		$('#y1').val(c.y);
		$('#x2').val(c.x2);
		$('#y2').val(c.y2);
		$('#w').val(c.w);
		$('#h').val(c.h);
	};

	function clearCoords() {
		//$('#coords input').val('');
	};

</script>

<div class="jcExample">

	<div>Other tools: {menu}</div>

	<img src="http://img.exs.lv/tmp/{file}" id="target" alt="" /><br />

	<form id="coords" class="form coords" action="/crop">
		<input type="hidden" value="{file}" name="file">
		<p>
			<label>X1 <input type="text" size="4" id="x1" name="x1" readonly="readonly" class="text tiny" /></label>
			<label>Y1 <input type="text" size="4" id="y1" name="y1" readonly="readonly" class="text tiny" /></label>
			<label style="display:none;">X2 <input type="text" size="4" id="x2" name="x2" readonly="readonly" class="text tiny" /></label>
			<label style="display:none;">Y2 <input type="text" size="4" id="y2" name="y2" readonly="readonly" class="text tiny" /></label>
			<label>W <input type="text" size="4" id="w" name="w" readonly="readonly" class="text tiny" /></label>
			<label>H <input type="text" size="4" id="h" name="h" readonly="readonly" class="text tiny" /></label>
		</p>

		<p>
		<label for="method">Crop with*:</label><br />
		<select name="method" id="method">
			<option value="im">ImageMagick</option>
			<option value="gifsicle">Gifsicle</option>
		</select>
		</p>

		<input type="submit" class="button primary" value="Crop it!" name="crop" />

	</form>

</div>

<!-- END BLOCK : crop-->

<!-- START BLOCK : crop-upl-->

<form class="form" action="/crop" method="post" enctype="multipart/form-data">
	<fieldset>
		<legend>Select image</legend>
		<p>
			<label>Upload image from your computer:</label><br />
			<input type="file" name="new-image" />
		</p>
		<p>
			<label>OR paste image url:</label><br />
			<input type="text" class="text" name="new-image-url" />
		</p>
		<p>
			(Only gif images, up to 12MB accepted)
		</p>
		<p><input type="submit" class="button primary" value="Upload!" /></p>
	</fieldset>
</form>
<p class="small">You can also use: http://ezgif.com/crop?url=<span style="color:#393;">http://link/to/image.gif</span></p>
<!-- END BLOCK : crop-upl-->

<h3>Output:</h3>
<div id="crop-result"></div>

<p>
	* If there happens to be some problem with the output file (missing frames or strange artifacts), try the other option.
</p>
