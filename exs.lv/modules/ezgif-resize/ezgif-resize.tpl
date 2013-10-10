<h1>Animated gif resizer</h1>

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

<div>Other tools: {menu}</div>

<img src="http://img.exs.lv/tmp/{file}" id="target" alt="" /><br />

<form id="resize" class="form coords" action="/resize">
	<input type="hidden" value="{file}" name="file">
	<p>
		Current width: {width}px<br />
		Current height: {height}px<br />
		<label>New width: <input type="text" size="4" id="width" name="width" class="text tiny" />&nbsp;(0 - auto)</label><br />
		<label>New height: <input type="text" size="4" id="height" name="height" class="text tiny" />&nbsp;(0 - auto)</label>
	</p>

	<p>
	<label for="method">Resize method*:</label><br />
	<select name="method" id="method">
		<option value="im">ImageMagick</option>
		<option value="gifsicle">Gifsicle</option>
		<option value="im-coalesce">ImageMagick + coalesce</option>
	</select>
	</p>

	<input type="submit" class="button primary" value="Resize it!" name="crop" />

</form>

<!-- END BLOCK : resize-->

<!-- START BLOCK : resize-upl-->

<form class="form" action="/resize" method="post" enctype="multipart/form-data">
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
<p class="small">You can also use: http://ezgif.com/resize?url=<span style="color:#393;">http://link/to/image.gif</span></p>
<!-- END BLOCK : resize-upl-->

<h3>Output:</h3>
<p>(Please be patient, resizing may take some time, depending on file size and resize method)</p>
<div id="crop-result"></div>

<p>
	* GIF animation resizing is usually tricky, and you will probably have to choose between smaller file size or better quality. Some gifs may require coalesce option, for best results.<br />
	Try different methods if you are not satisfied with the result.<br />
	Gifsicle is usually the fastest tool and produces smaller files, but is designed for speed, not quality.
</p>
