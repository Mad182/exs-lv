<!DOCTYPE html>
<html lang="lv">
	<head>
		<meta charset="UTF-8">
		<title>Ielogoties - {server-name}</title>
		<meta name="googlebot" content="noindex">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="stylesheet" href="{static-server}/css/mobile.css" type="text/css" />
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

					</fieldset>
				</form>
			</div>
			<div id="footer">&copy; {server-name}, {current-year}</div>
		</div>

	</body>
</html>

