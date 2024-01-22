<!-- START BLOCK : registration-form-->
<h1>Jauna lietotāja reģistrācija</h1>

<script>
	$(document).ready(function() {
		/* reģistrāciajs formas javascript check */
		$('#reg-www').val("{botstring}");
	});
</script>

<form id="edit-profile" class="form" action="" method="post">
	<fieldset>
		<legend>Reģistrēties lapā</legend>

		<!-- START BLOCK : invalid-mail-->
		<p class="error">Nekorekti norādīta e-pasta adrese!</p>
		<!-- END BLOCK : invalid-mail-->

		<!-- START BLOCK : invalid-nick-len-->
		<p class="error">Nikam jābūt 3 līdz 16 simbolus garam!</p>
		<!-- END BLOCK : invalid-nick-len-->

		<!-- START BLOCK : invalid-nick-taken-->
		<p class="error">Šāds niks jau ir aizņemts!</p>
		<!-- END BLOCK : invalid-nick-taken-->

		<!-- START BLOCK : invalid-mail-taken-->
		<p class="error">Šāds e-pasts jau ir reģistrēts!</p>
		<!-- END BLOCK : invalid-mail-taken-->

		<!-- START BLOCK : invalid-pass-len-->
		<p class="error">Parolei jābūt vismaz 6 simbolus garai!</p>
		<!-- END BLOCK : invalid-pass-len-->

		<!-- START BLOCK : invalid-pass-mach-->
		<p class="error">Ievadītās paroles nesakrīt!</p>
		<!-- END BLOCK : invalid-pass-mach-->

		<!-- START BLOCK : invalid-bots-->
		<p class="error">Kaut kas nav kārtībā ar robotu pārbaudi!</p>
		<!-- END BLOCK : invalid-bots-->

		<!-- START BLOCK : invalid-agree-->
		<p class="error">Ja vēlies reģistrēties, Tev jāpiekrīt noteikumiem!</p>
		<!-- END BLOCK : invalid-agree-->


		<!-- START BLOCK : greetings-->
		<p class="success">Paldies, ka reģistrējies!<br>Pārbaudi savu e-pasta kastīti - tur jābūt saitei, kuru atverot Tavs profils tiks apstiprināts :)<br>Ja neredzi vēstuli, pārliecinies, kai tā nav nejauši iekritusi spama sadaļā :/</p>
		<!-- END BLOCK : greetings-->

		<!-- START BLOCK : form-fields-->
		<input type="hidden" name="reg_token" value="{reg_token}" />
		<p>
			<label for="{field_nick}">Iesauka:<br><span class="description">Vārds, ar kādu Tevi pazīs lapā</span></label>
			<input tabindex="1" type="text" class="text usercheck" name="{field_nick}" id="{field_nick}" value="{new-nick}" maxlength="14" /> <span class="usercheck-response" id="userexists"></span>
		</p>
		<p>
			<label for="{field_mail}">E-pasta adrese:<br>
				<span class="description">Jābūt reālai, jo uz to tiks nosūtīts reģistrācijas apstiprinājuma e-pasts.<br>
					E-pasta adrese tiek izmantota nozaudētas paroles gadījumā.<br>Uz to netiks sūtīti komerciāli paziņojumi.
				</span>
			</label>
			<input tabindex="2" type="text" class="text" name="{field_mail}" id="{field_mail}" value="{new-mail}" maxlength="64" />
		</p>
		<p>
			<label for="omnomnom">Parole:</label>
			<input tabindex="3" type="password" class="text" name="omnomnom" id="omnomnom" autocomplete="new-password" />
		</p>
		<p>
			<label for="url">Parole atkārtoti:</label>
			<input tabindex="4" type="password" class="text" name="url" id="url" autocomplete="new-password" />
		</p>
		<p id="required-registration-field">
			<label for="reg-www">Homepage:</label>
			<input type="text" class="text" name="www" id="reg-www" value="http://" />
		</p>

		<script src="https://www.google.com/recaptcha/api.js"></script>
		<div class="g-recaptcha" data-sitekey="6Lc4eR0TAAAAADtKKHnukW83hTpJYDoqR3BQeVdU"></div>
		<p>&nbsp;</p>

		<h2>Reģistrējoties tu piekrīti mājas lapas lietošanas noteikumiem:</h2>
		<div class="box">{rules}</div>
		<p>
			<label><input tabindex="6" type="checkbox" name="agree" /> ar noteikumiem iepazinos un piekrītu tos ievērot</label>
		</p>
		<p>
			<input tabindex="7" class="button primary" type="submit" name="submit" id="submit" value="Reģistrēties" />
		</p>
		<!-- END BLOCK : form-fields-->
	</fieldset>
</form>
<!-- END BLOCK : registration-form-->
