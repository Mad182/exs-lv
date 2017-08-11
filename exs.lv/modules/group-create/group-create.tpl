<!-- START BLOCK : group-create-->
<form id="edit-profile" class="form" action="{page-url}" method="post">
	<fieldset>
		<legend>Jaunas grupas izveide</legend>
		<p>
			<label for="new-title">Nosaukums:<br /><span class="description">Grupas nosaukums vēlāk <strong>nav maināms!</strong></span></label><br />
			<input type="text" class="text" name="new-title" id="new-title" value="" maxlength="64" />
		</p>
		<p>
			<input class="button primary" type="submit" name="submit" id="submit" value="Izveidot grupu" />
		</p>
		<p>Grupas izveide Tev maksās <strong>3</strong> exs.lv kredīta punktus. Tev šobrīd ir <strong>{user-credit}</strong> kredīta punkti.</p>

		<h4>Kā iegādāties 5 kredīta punktus?</h4>
		<div class="box">
			<ul class="tabs">
				<li><a href="/payment-info" class="active ajax" id="default-payment-link"><img src="{static-server}/bildes/flags/lv.png" alt="" />&nbsp;Latvijā</a></li>
			</ul>
			<div id="pay" class="ajaxbox">
			</div>
		</div>

		<script type="text/javascript">
			$(document).ready(function() {
				$('#default-payment-link').click();
			});
		</script>

	</fieldset>
</form>

<!-- END BLOCK : group-create-->

