<!-- START BLOCK : fb-signup-->
<h1>Ienākt ar facebook.lv</h1>

<p>Izskatās, ka Tu pirmo reizi esi ienācis izmantojot facebook. Ja Tev jau ir savs profils, Tu to vari savienot ar facebook pasi. Ja ne, izvēlies niku un nāc iekšā tā pat :)</p>


<div>
	<div class="half-left">
		<h3>Izveidot profilu</h3>
		<form id="edit-profile" class="form" action="" method="post">
			<fieldset style="border: 0;padding: 0;margin: 10px 10px 10px 0">
				<!-- START BLOCK : invalid-nick-len-->
				<p class="error">Nikam jābūt 3 līdz 16 simbolus garam!</p>
				<!-- END BLOCK : invalid-nick-len-->
				<!-- START BLOCK : invalid-nick-taken-->
				<p class="error">Šāds niks jau ir aizņemts!</p>
				<!-- END BLOCK : invalid-nick-taken-->
				<p>
					<img src="{avatar}" class="av" style="float:none;" alt="" />
				</p>
				<p>
					<label for="nick">Niks:<br /><span class="description">Vārds, ar kādu Tevi pazīs lapā</span></label><br />
					<input style="width: 120px;" type="text" class="text usercheck" name="nick" id="nick" value="{nick}" maxlength="20" /> <span class="usercheck-response" id="userexists"></span>
				</p>
				<p>
					<input class="button primary" type="submit" name="submit" id="submit" value="Taisam jaunu" />
				</p>
			</fieldset>
		</form>
	</div>

	<div class="half-right">
		<h3>Jau esmu reģistējies</h3>

		<form id="login-profile" class="form" action="" method="post">
			<fieldset style="border: 0;padding: 0;margin: 10px 10px 10px 0">
				<!-- START BLOCK : invalid-->
				<p class="error">Nepareizs lietotājvārds un/vai parole!</p>
				<!-- END BLOCK : invalid-->
				<p>
					<label for="existing-nick">Niks:</label><br />
					<input style="width: 180px;" type="text" class="text" name="existing-nick" id="existing-nick" maxlength="14" />
				</p>
				<p>
					<label for="existing-password">Parole:</label><br />
					<input style="width: 180px;" type="password" class="text" name="existing-password" id="existing-password" />
				</p>
				<p>
					<input class="button primary" type="submit" name="submit" id="submit" value="Ienākt" />
				</p>
			</fieldset>
		</form>
	</div>
	<div class="c"></div>
</div>

<!-- END BLOCK : fb-signup-->

<!-- START BLOCK : fb-login-->
<h2>Ienākt ar facebook.com</h2>
<a href="{link}"><img src="http://exs.lv/bildes/fb-login.png" alt="login with facebook" /></a>
<!-- END BLOCK : fb-login-->
