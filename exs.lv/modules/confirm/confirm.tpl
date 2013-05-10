<!-- START BLOCK : err-->
<div class="form">
	<p class="error">Kļūda - profils ir jau apstiprināts, vai ievadīta kļūdaina adrese!</p>
</div>
<!-- END BLOCK : err-->

<!-- START BLOCK : succ-->
<form class="form" id="login-form-inline" action="/" method="post">
	<p class="success">Viss kārtībā :)<br />tagad vari ielogoties...</p>
	<fieldset>
		<legend>Ielogoties</legend>
		<p>
			<label for="login-nick-inline">Niks:</label><br />
			<input id="login-nick-inline" class="text" name="niks" type="text" />
		</p>
		<p>
			<label for="login-pass-inline">Parole:</label><br />
			<input id="login-pass-inline" class="text" name="parole" type="password" />
		</p>
		<p>
			<input name="login-submit" id="login-submit-inline" value="Ienākt" type="submit" />
		</p>
	</fieldset>
</form>
<!-- END BLOCK : succ-->
