<!DOCTYPE html>
<html lang="lv">
<head>
	<meta charset="UTF-8">
	<title>Vai šodien ir piektdiena?</title>
	<style>
		body, html {
			font-family: tahoma, arial, verdana, sans-serif;
			padding: 0;
			margin: 0;
			width: 100%;
			height: 100%;
			overflow: hidden;
			background: #333;
			color: #eee;
			text-shadow: 0 1px 1px #222;
			font-size: 14px;
		}
		h1 {
			font-size: 36px;
			padding: 10px 0;
			margin: 0;
			text-shadow: 0 2px 2px #222;
		}
		h1.q {
			font-size: 38px;
			color: #69c;
			display: block;
			padding: 0 60px;
			width: 650px;
			margin: 20px 0;
		}
		h1.fail {
			color: red;
			font-size: 46px;
		}
		h1.win {
			color: green;
			font-size: 52px;
		}
		h1.q:before {
			color: #69c;
			display: block;
			font-size: 200%;
			width: 50px;
			content: '\201C';
			height: 0;
			margin-left: -0.55em;
		}
		h1.q:after {
			color: #69c;
			display: block;
			font-size: 200%;
			width: 50px;
			content: '\201D';
			height: 50px;
			margin-top: -70px;
			margin-left: 460px;
		}
		#wrap {
			width: 600px;
			padding: 30px 10px;
			margin: 40px auto;
			background: #444;
			border-radius: 5px;
			box-shadow: 3px 3px 5px #222
		}
	</style>
</head>
<body>
	<div id="wrap">
		<h1 class="q">Vai šodien ir piektdiena?</h1>
		{out}
		<p>&nbsp;</p>
	</div>
	<script>
		var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
		document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
	</script>
	<script>
		try {
		var pageTracker = _gat._getTracker("UA-4190387-2");
						pageTracker._trackPageview();
		} catch (err) {}
	</script>
</body>
</html>
