<!DOCTYPE html>
<html lang="lv">
<head>
	<meta charset="UTF-8">
	<title>{page-title}</title>
	<link rel="stylesheet" href={static-server}/css/core.css" type="text/css" media="screen,projection" />
	<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
	<script type="text/javascript" src="{static-server}/js/jquery.min.js"></script>
	<!-- INCLUDE BLOCK : module-head -->
	<style type="text/css">
		body, html {
			background: #f4f6f9;
		}
		#wrapper-simple {
			background: #fff;
		}
		img {
			border: 5px solid #fafafa;
		}
	</style>
</head>

<body>

	<div id="wrapper-simple" style="width:800px;">

		<!-- INCLUDE BLOCK : module-currrent -->
		<!-- INCLUDE BLOCK : module-core-error -->

		<div class="c"></div>
		<div style="border-top: 1px solid #ccc;text-align: right;padding: 2px 0 0;margin: 20px 0 0; color: #999;font-size: 11px;">
			Spēcināts ar <a href="http://exs.lv/">exs.lv</a>
		</div>

	</div>

	<script type="text/javascript">

		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-4190387-2']);
		_gaq.push(['_setDomainName', 'exs.lv']);
		_gaq.push(['_trackPageview']);

		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();

	</script>

</body>

</html>
