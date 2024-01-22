<!-- START BLOCK : passreset-form-->
<h1>Aizmirsu paroli</h1>
<form id="reset-password" class="form" action="" method="post">
	<fieldset>
		<legend>Nozaudētas paroles atjaunošana</legend>
		<!-- START BLOCK : invalid-namemail-->
		<p class="error">Lietotājvārds vai e-pasts datubāzē nav atrasti, vai arī tie nesakrīt!</p>
		<!-- END BLOCK : invalid-namemail-->
		<!-- START BLOCK : greetings-->
		<p class="success">Dati pieņemti!<br>Pārbaudi savu e-pastu.</p>
		<!-- END BLOCK : greetings-->
		<p>
			<label for="pwd-nick">Lietotājvārds:</label><br>
			<input type="text" class="text" name="pwd-nick" id="pwd-nick" maxlength="18" />
		</p>
		<p>
			<label for="pwd-mail">E-pasta adrese:</label><br>
			<input type="text" class="text" name="pwd-mail" id="pwd-mail" maxlength="128" />
		</p>
		<p>
			<input type="submit" name="submit" id="submit" value="Pieprasīt paroles maiņu" class="button primary" />
		</p>
	</fieldset>
</form>
<!-- END BLOCK : passreset-form-->
