<!-- START BLOCK : generator-->
<h2>Uztaisīt savējo</h2>
<script type="text/javascript">
	$(document).ready(function() {
		$('.generator-text').live('change', function() {
			console.log('action');
			var _url = 'http://meme.exs.lv/img.php?image={image}&top=' + encodeURIComponent($('#g-top').val()) + '&bottom=' + encodeURIComponent($('#g-bottom').val()) + '&center=' + encodeURIComponent($('#g-center').val());
			$('#generator-result').fadeTo(200, 0.9);
			_im = $("<img>");
			_im.hide();
			_im.bind("load", function() {
				$(this).fadeIn();
			});
			$('#generator-result').html(_im);
			$('#generator-result').fadeTo(100, 1);
			_im.attr('src', _url);
			_im.css('width', '400px');
			_im.css('display', 'inline');
			return false;
		});
	});
</script>

<form class="form" action="" style="float: left;" method="get">
	<fieldset>
		<p><input class="text generator-text" type="text" name="top" id="g-top" /></p>
		<p><input class="text generator-text" type="text" name="center" id="g-center" /></p>
		<p><input class="text generator-text" type="text" name="bottom" id="g-bottom" /></p>
	</fieldset>
</form>

<div id="generator-result" style="float:left;width:400px"><img style="width: 400px;" src="http://meme.exs.lv/img.php?image={image}" alt="" /></div>

<!-- END BLOCK : generator-->

<div class="clear"></div>

<h2>Galerija</h2>

<!-- START BLOCK : img-->
<a href="/meme/{file}"><img style="width:100px;height: 100px;" src="http://meme.exs.lv/images/{file}" /></a>
<!-- END BLOCK : img-->
