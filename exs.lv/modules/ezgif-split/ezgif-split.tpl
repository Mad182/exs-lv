<h1>Gif frame extractor (splitter)</h1>

<!-- START BLOCK : split-->

<script type="text/javascript">

	$(function($){

		$('#split').live('submit', function() {
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

<form id="split" class="form" action="/split" method="post">
	<input type="hidden" value="{file}" name="file">

	<p><input type="submit" class="button primary" value="Split it!" name="crop" /></p>
</form>


<!-- END BLOCK : split-->

<!-- START BLOCK : split-upl-->

<form class="form" action="/split" method="post" enctype="multipart/form-data">
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
<p class="small">You can also use: http://ezgif.com/split?url=<span style="color:#393;">http://link/to/image.gif</span></p>
<!-- END BLOCK : split-upl-->

<h3>Output:</h3>
<div id="crop-result"></div>

<p>
	This tool is designed to convert animated gif image into individual frames for editing or viewing them separately.<br />After decompressing gif file, you can download frames one by one or as a single zip file.
</p>
