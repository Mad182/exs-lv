<h1>Add effects to animated gifs</h1>

<!-- START BLOCK : effects-->

<script type="text/javascript">

	$(function($){

		$('#effects').live('submit', function() {
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

	<form id="effects" class="form coords" action="/effects">
	<input type="hidden" value="{file}" name="file">

	<p>
		<label><input type="checkbox" name="sepia" /> sepia</label><br />
		
		<label><input type="checkbox" name="monochrome" /> monochrome</label><br />
		
		<label><input type="checkbox" name="grayscale" /> grayscale</label><br />
		
		<label><input type="checkbox" name="flip" /> flip (vertical)</label><br />
		
		<label><input type="checkbox" name="flop" /> flop (horizontal)</label><br />
		
		<label><input type="checkbox" name="reverse" /> reverse</label><br />
		
		
	</p>
	<input type="submit" class="button primary" value="effects it!" name="effects" />

</form>

<!-- END BLOCK : effects-->

<!-- START BLOCK : effects-upl-->

<form class="form" action="/effects" method="post" enctype="multipart/form-data">
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
<p class="small">You can also use: http://ezgif.com/effects?url=<span style="color:#393;">http://link/to/image.gif</span></p>
<!-- END BLOCK : effects-upl-->

<h3>Output:</h3>
<div id="crop-result"></div>

<p>
	Add effects to your animated gif.<br />
	This tool can convert GIF image to grayscale, sepia, monochrome and flip/reverse it.
</p>
