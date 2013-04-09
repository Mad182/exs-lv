<h1>Crop animated gif image</h1>

<!-- START BLOCK : crop-->

<script type="text/javascript">

	$(document).ready(function() {

		var jcrop_api;


		$('#target').Jcrop({
			onChange:   showCoords,
			onSelect:   showCoords,
			onRelease:  clearCoords
		},function(){
		    jcrop_api = this;
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

		$('#set-to').click(function(e) {

			var r_x1 = Math.floor($('#x1').val());
			var r_y1 = Math.floor($('#y1').val());
			var r_x2 = r_x1+Math.floor($('#w').val());
			var r_y2 = r_y1+Math.floor($('#h').val());

			jcrop_api.setSelect([r_x1,r_y1,r_x2,r_y2]);

			return false;
		});

	});

	function showCoords(c) {
		$('#x1').val(Math.floor(c.x));
		$('#y1').val(Math.floor(c.y));
		$('#x2').val(Math.floor(c.x2));
		$('#y2').val(Math.floor(c.y2));
		$('#w').val(Math.floor(c.w));
		$('#h').val(Math.floor(c.h));
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
			<label>X1 <input type="text" size="4" id="x1" name="x1" class="text tiny animate-to" /></label>
			<label>Y1 <input type="text" size="4" id="y1" name="y1" class="text tiny animate-to" /></label>
			<label style="display:none;">X2 <input type="text" size="4" id="x2" name="x2" readonly="readonly" class="text tiny" /></label>
			<label style="display:none;">Y2 <input type="text" size="4" id="y2" name="y2" readonly="readonly" class="text tiny" /></label>
			<label>W <input type="text" size="4" id="w" name="w" class="text tiny animate-to" /></label>
			<label>H <input type="text" size="4" id="h" name="h" class="text tiny animate-to" /></label>
			<input type="submit" value="Set" id="set-to" class="button primary small" />
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
	* If there happens to be some problem with the output file (missing frames or strange artifacts), try the other option.<br />
	There may be difference, depending on compression used for the source image.
</p>
