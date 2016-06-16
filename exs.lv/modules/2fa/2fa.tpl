<!-- START BLOCK : auth-2fa-->
<h1>Divu Faktoru Autentifikācija</h1>

<form id="2fa" class="form" action="{page-url}" method="post">
	<fieldset>
		<input type="hidden" name="xsrf_token" value="{xsrf}" />
		<p>
			<label for="code">Ievadi kodu no Google Authenticator:</label>
			<input type="text" class="text" name="code" id="code" value="" maxlength="16" />
		</p>
		<p>
			<label for="remember"><input type="checkbox" name="remember" id="remember" /> atcerēties šo ierīci</label>
		</p>
		<p>
			<input type="submit" name="submit" class="button primary" value="Ienākt" />
		</p>
	</fieldset>
</form>

<!-- END BLOCK : auth-2fa-->

