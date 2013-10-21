<!-- START BLOCK : login-form-->
<form class="form" method="post" action="{page-url}">
<fieldset>
<legend>Ielogoties</legend>
<input type="hidden" name="xsrf_token" value="{xsrf}" />
<span{cat-sel-106}><a href="/register">Reģistrēties</a></span>
<!-- START BLOCK : login-form-error1-->
<a class="red" href="/forgot-password">Aizmirsi paroli?</a>
<!-- END BLOCK : login-form-error1-->
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
<!-- END BLOCK : login-form-->

