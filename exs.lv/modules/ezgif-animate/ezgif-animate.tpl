<h1>Gif animator</h1>

<!-- START BLOCK : animate-->

<script type="text/javascript">

	$(function($){

		$('#animate').live('submit', function() {
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

		$("#animation-frames").disableSelection();
		$("#animation-frames").sortable();

	});


</script>

<form id="animate" class="form" action="/animate" method="post">
	<input type="hidden" value="{file}" name="file">

(drag and drop frames to change order)
<ul id="animation-frames">
<!-- START BLOCK : animate-frame-->
<li class="frame">
	<img src="http://img.exs.lv/tmp/{folder}/{file}" alt="" />
	<input type="hidden" value="{file}" name="files[]">
</li>

<!-- END BLOCK : animate-frame-->
</ul>
<div class="clear"></div

	<label>Delay: <input type="text" size="4" id="delay" name="delay" class="text tiny" value="12" />&nbsp;(delay controls animation speed. Longer delay = slower animation)</label><br />
	<p><input type="submit" class="button primary" value="Animate it!" name="crop" /></p>
</form>


<!-- END BLOCK : animate-->
coming soon
<!-- START BLOCK : animate-upl-->


<!-- END BLOCK : animate-upl-->

<h3>Output:</h3>
<div id="crop-result"></div>


