<!-- START BLOCK : gmail-signup -->
<h1>Reģistrācijas pabeigšana ar Google</h1>

<!-- START BLOCK : invalid-nick-len -->
<div class="alert alert-error">
	<strong>Kļūda:</strong> Izvēlētais lietotājvārds ir par īsu vai par garu (jābūt no 3 līdz 24 zīmēm)!
</div>
<!-- END BLOCK : invalid-nick-len -->

<!-- START BLOCK : invalid-nick-taken -->
<div class="alert alert-error">
	<strong>Kļūda:</strong> Šāds lietotājvārds jau ir aizņemts! Lūdzu izvēlies citu.
</div>
<!-- END BLOCK : invalid-nick-taken -->

<div class="mbox">
	<p>Esi veiksmīgi autorizējies ar savu Google/Gmail kontu (<strong>{email}</strong>).</p>
	<p>Lai pabeigtu reģistrāciju eksā, norādi vēlamo lietotājvārdu:</p>

	<form action="" method="post" class="form-horizontal" style="margin-top: 20px;">
		<div class="control-group">
			<label class="control-label" for="nick">Lietotājvārds:</label>
			<div class="controls">
				<input type="text" id="nick" name="nick" value="{nick}" maxlength="24" required>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<input type="submit" class="button primary" value="Pabeigt reģistrāciju">
			</div>
		</div>
	</form>
</div>
<!-- END BLOCK : gmail-signup -->
