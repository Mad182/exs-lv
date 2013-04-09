<h1>Optimize animated gif image</h1>

<!-- START BLOCK : optimize-->

<script type="text/javascript">

	$(function($){

		$('#optimize').live('submit', function() {
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

<div>Other tools: {menu}</div>

<img src="http://img.exs.lv/tmp/{file}" id="target" alt="" /><br />
	<p>
		File size: {filesize}
	</p>

<form id="optimize" class="form coords" action="/optimize">
	<input type="hidden" value="{file}" name="file">

	<p>
	<label for="method">Optimization method:</label><br />
	<select name="method" id="method">
		<option value="im_color">Color Reduction</option>
		<option value="im_color_dither" selected="selected">Color Reduction + dither</option>
		<option value="gifsicle_1">Gifsicle level 1</option>
		<option value="gifsicle_2">Gifsicle level 2</option>
	</select>
	</p>
	<p>
		<label>Reduce colors to <input type="text" size="4" id="colors" name="colors" class="text tiny" value="{manual-colors}" />&nbsp;(1 - 256)</label><br />
		<small>0 = set of variations (200/128/90/64/32/16/8)</small>
	</p>

	<input type="submit" class="button primary" value="Optimize it!" name="optimize" />

</form>

<!-- END BLOCK : optimize-->

<!-- START BLOCK : optimize-upl-->

<form class="form" action="/optimize" method="post" enctype="multipart/form-data">
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
<p class="small">You can also use: http://ezgif.com/optimize?url=<span style="color:#393;">http://link/to/image.gif</span></p>
<!-- END BLOCK : optimize-upl-->

<h3>Output:</h3>
<div id="crop-result"></div>

<p>
	Gif optimizer is designed to shrink gif file size by reducing number of colors in each frame.<br />Each gif frame can use up to unique 256 colors, and by reducing this number, you can achieve smaller file size.<br />This tool makes multiple variations of input image, and you can choose the one that has best size/quality ratio for your needs.
</p>
