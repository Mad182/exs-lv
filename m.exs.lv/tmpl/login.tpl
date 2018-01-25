<!DOCTYPE html>
<html lang="lv">
	<head>
		<meta charset="UTF-8">
		<title>Ielogoties - {server-name}</title>
		<meta name="googlebot" content="noindex">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="stylesheet" href="{static-server}/css/mobile.css?social" type="text/css" />
		<script type="text/javascript" src="{static-server}/js/jquery.min.js,mobile.js"></script>
	</head>

	<body>
		<div id="outer-wrapper">
			<div id="header">
				<a id="logo" href="/">{server-name}</a>
			</div>
			<div id="wrapper">
				<!-- START BLOCK : flash-message-->
				<div class="c"></div>
				<div class="mbox {class}" id="flash-message">
					<p><a id="close-flash-message" href="#"><img src="{img-server}/bildes/fugue-icons/cross-button.png" alt="Aizvērt" title="Aizvērt" width="16" height="16" /></a> {message}</p>
				</div>
				<div class="c"></div>
				<!-- END BLOCK : flash-message-->
				<form class="form" method="post" action="{page-loginurl}">
					<fieldset>
						<legend>Autorizācija</legend>
						<input type="hidden" name="xsrf_token" value="{xsrf}" />
						<p>
							<label for="niks">Niks:</label><br />
							<input class="text" name="niks" id="niks" type="text" />
						</p>
						<p>
							<label for="parole">Parole:</label><br />
							<input class="text" name="parole" id="parole" type="password" />
						</p>
						<p>
							<input class="button primary" name="submit" value="Ienākt" type="submit" />
						</p>

						<p id="ext-login">
							<a rel="nofollow" href="/fb-login" class="external-login external-fb" title="Log in with FaceBook"><span>FaceBook</span></a>
							<a rel="nofollow" href="/twitter-login" class="external-login external-twitter" title="Log in with twitter"><span>Twitter</span></a>
						</p>

					</fieldset>
				</form>
			</div>
			<div id="footer">&copy; {server-name}, {current-year}</div>
		</div>

		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		  ga('create', 'UA-4190387-2', 'auto');
		  ga('send', 'pageview');

		</script>

	</body>
</html>
