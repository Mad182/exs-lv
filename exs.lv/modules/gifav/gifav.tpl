<h1>Animētie avatari</h1>

<!-- START BLOCK : import-->
<form class="form" action="" method="post">
	<fieldset>
		<legend>Importēt avataru no <a href="http://gif-avatars.com/" target="_blank">gif-avatars.com</a></legend>
		<p>
			<label>Avatara ID:</label><br />
			<input type="text" name="gif_avatars_id" class="text number" style="width: 50px;" />
			<input type="submit" name="gif_avatars_import" class="button primary" value="Importēt" />
		</p>
	</fieldset>
</form>
<!-- END BLOCK : import-->

<form class="form" action="" method="post">
	<fieldset>
		<ul style="padding:0;margin:0">
			<!-- START BLOCK : av-node-->
			<li style="background:transparent;float:left;width:90px;padding:4px;margin:3px;height:110px;text-align:center;{owned}">
				<label for="av-{id}">
					<img src="/dati/bildes/useravatar/{image}" alt="{id}" style="width: 90px;height: 90px;" />
				</label>
				<br />
				<input id="av-{id}" type="radio" name="avatarid" value="{id}" />
			</li>
			<!-- END BLOCK : av-node-->
		</ul>
		<div class="c"></div>
		<p>Bez avatariem kas redzami šeit, vari izvēlēties arī jebkuru avataru no <a href="http://gif-avatars.com/">http://gif-avatars.com/</a>, tur ir visi vajadzīgie izmēri. Tādā gadījumā sūti PM ar linku uz avataru kādam modiņam vai adminam :)</p>

		<p>Animētais avatars maksā 5 exs.lv kredīta punktus. Par to tu iegūsti vienu avataru, ko izvēlies. Citiem šis avatars vairs nebūs pieejams.</p>

		<!-- START BLOCK : av-buy-->
		<input type="submit" class="button primary" value="Nosūtīt" />
		<!-- END BLOCK : av-buy-->

		<!-- START BLOCK : av-credit-->

		<h4>Kā iegādāties 5 kredīta punktus?</h4>
		<div class="box">
			<ul class="tabs">
				<li><a href="/payment-info" class="active ajax" id="default-payment-link"><img src="//img.exs.lv/bildes/flags/lv.png" alt="" />&nbsp;Latvijā</a></li>
				<li><a href="/payment-info/uk" class="ajax"><img src="//img.exs.lv/bildes/flags/gb.png" alt="" />&nbsp;Lielbritānijā</a></li>
				<li><a href="/payment-info/ie" class="ajax"><img src="//img.exs.lv/bildes/flags/ie.png" alt="" />&nbsp;Īrijā</a></li>
			</ul>
			<div id="pay" class="ajaxbox">
			</div>
		</div>

		<script type="text/javascript">
			$(document).ready(function() {
				$('#default-payment-link').click();
			});
		</script>

		<!-- END BLOCK : av-credit-->

	</fieldset>
</form>

