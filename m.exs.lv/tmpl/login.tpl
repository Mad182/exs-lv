<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>Ielogoties</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="content-language" content="lv" />
<meta name="googlebot" content="noindex" />
<link rel="stylesheet" href="/m.css" type="text/css" />
</head>

<body>
<div id="outer-wrapper">
	<div id="header">
	<a id="logo" href="/">{server-name}</a>
	</div>
	<div id="wrapper">
	<form class="form" method="post" action="{page-url}">
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
