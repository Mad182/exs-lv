<!DOCTYPE html>
<html lang="lv">
<head>
<meta charset="UTF-8">
<title>Ielogoties - {server-name}</title>
<meta http-equiv="content-language" content="lv">
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
		<p><a id="close-flash-message" href="#"><img src="http://img.exs.lv/bildes/fugue-icons/cross-button.png" alt="Aizvērt" title="Aizvērt" width="16" height="16" /></a> {message}</p>
	</div>
	<div class="c"></div>
	<!-- END BLOCK : flash-message-->
	<form class="form" method="post" action="{page-loginurl}">
	<fieldset>
	<legend>Ielogoties</legend>
	<input type="hidden" name="xsrf_token" value="{xsrf}" />
	<p>
		Niks:<br />
		<input class="text" name="niks" type="text" />
	</p>
	<p>
		Parole:<br />
		<input class="text" name="parole" type="password" />
	</p>
	<p>
		<input class="button" name="submit" value="Log in" type="submit" />
	</p>
	</fieldset>
	</form>
	</div>
	<div id="footer">&copy; {server-name}, 2013</div>
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
