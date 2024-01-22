<!-- START BLOCK : twitter-signup-->
<h1>Autorizēties ar twitter.com</h1>

<p>Izskatās, ka Tu pirmo reizi esi ienācis izmantojot twitter. Ja Tev jau ir savs profils, Tu to vari savienot ar twitter login. Ja ne, droši izvēlies niku un nāc iekšā tā pat :)</p>

<div>
	<div class="half-left">
		<h3>Vēlos izveidot jaunu profilu</h3>
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
					<label for="nick">Niks:<br><span class="description">Vārds, ar kādu Tevi pazīs lapā</span></label><br>
					<input style="width: 120px;" type="text" class="text usercheck" name="nick" id="nick" value="{nick}" maxlength="20" /> <span class="usercheck-response" id="userexists"></span>
				</p>
				<label style="font-weight: normal;"><input type="checkbox" name="follow" checked="checked" /> Sekot @exs_lv oficiālajam twitter kontam</label>
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
					<label for="existing-nick">Niks:</label><br>
					<input style="width: 180px;" type="text" class="text" name="existing-nick" id="existing-nick" maxlength="14" />
				</p>
				<p>
					<label for="existing-password">Parole:</label><br>
					<input style="width: 180px;" type="password" class="text" name="existing-password" id="existing-password" />
				</p>
				<label style="font-weight: normal;"><input type="checkbox" name="follow" checked="checked" /> Sekot @exs_lv oficiālajam twitter kontam</label>
				<p>
					<input class="button primary" type="submit" name="submit" id="submit" value="Ienākt" />
				</p>
			</fieldset>
		</form>
	</div>
	<div class="c"></div>
</div>

<div>
<h3>Reģistrējoties, Tu piekrīti lietošanas noteikumiem:</h3>
{rules}
</div>

<!-- END BLOCK : twitter-signup-->

