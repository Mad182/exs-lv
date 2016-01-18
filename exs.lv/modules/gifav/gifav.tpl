<h1>Animētie GIF avatari</h1>

<!-- START BLOCK : import-->
<form class="form" action="" method="post">
	<fieldset>
		<legend>Importēt avataru no <a href="https://gif-avatars.com/" target="_blank">gif-avatars.com</a></legend>
		<p>
			<label>Avatara ID:</label><br />
			<input type="text" name="gif_avatars_id" class="text number" />
			<input type="submit" name="gif_avatars_import" class="button primary" value="Importēt" />
		</p>
	</fieldset>
</form>
<!-- END BLOCK : import-->

<form class="form" action="" method="post">
	<fieldset>
		<ul style="padding:0;margin:0;list-style:none">
			<!-- START BLOCK : av-node-->
			<li style="background:transparent;float:left;width:146px;padding:6px 0 1px;margin:3px 1px;height:146px;text-align:center;{owned}">
				<label for="av-{id}" style="font-size:90%;font-weight:normal">
					<img src="/dati/bildes/useravatar/{image}" alt="{id}" style="width: 90px;height: 90px;" /><br />
					{title}
				</label>
				<input id="av-{id}" type="radio" name="avatarid" value="{id}" />
			</li>
			<!-- END BLOCK : av-node-->
		</ul>
		<div class="c"></div>
		<p>
			Papildus avatariem, kas redzami šeit, Tu vari izvēlēties arī jebkuru avataru no mājas lapas 
			<a href="https://gif-avatars.com/" target="_blank">https://gif-avatars.com/</a>,
			tur ir visi vajadzīgie izmēri.
			Tādā gadījumā sūti PM ar linku uz avataru kādam modiņam vai adminam :)
		</p>

		<p>
			Animētais avatars &quot;maksā&quot; 5 exs.lv kredīta punktus.<br />
			Par to tu iegūsti vienu avataru, ko izvēlies. Citiem šis avatars vairs nebūs pieejams, un Tu to jebkurā brīdī varēsi uzlikt atpakaļ, ja būsi nomainijis.
		</p>

		<!-- START BLOCK : av-buy-->
		<input type="submit" class="button primary" value="Nosūtīt" />
		<!-- END BLOCK : av-buy-->

		<!-- START BLOCK : av-credit-->

		<h4>Kā iegādāties 5 kredīta punktus?</h4>
		<div class="box">
			<ul class="tabs">
				<li><a href="/payment-info" class="active ajax" id="default-payment-link"><img src="//img.exs.lv/bildes/flags/lv.png" alt="" />&nbsp;Latvijā</a></li>
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

