<h1>Optimize PNG images</h1>

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

<img src="http://img.exs.lv/tmp/{file}" id="target" alt="" /><br />

<form id="optimize" class="form coords" action="/optijpg">
	<input type="hidden" value="{file}" name="file">
	<input type="submit" class="button primary" value="Optimize it!" name="optimize" />

</form>

<!-- END BLOCK : optimize-->

<!-- START BLOCK : optimize-upl-->

<form class="form" action="/optijpg" method="post" enctype="multipart/form-data">
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
			(Only JPG images, up to 10MB accepted)
		</p>
		<p><input type="submit" class="button primary" value="Upload!" /></p>
	</fieldset>
</form>
<p class="small">You can also use: http://ezgif.com/optijpg?url=<span style="color:#393;">http://link/to/image.jpg</span></p>
<!-- END BLOCK : optimize-upl-->

<h3>Output:</h3>
<div id="crop-result"></div>

<p>
	Lossless JPEG image optimization by removing exif and other unnecessary data.
</p>
