<!-- START BLOCK : csservlist-->
<a style="float: right;font-weight: bold;padding: 3px;margin:3px;" href="http://exs.lv/cs_servera_monitors/">Pievienot serveri&nbsp;&raquo;</a>
<h1>Latvijas Counter-Strike serveri</h1>
<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
<!-- START BLOCK : csserver-->
<div class="server" style="float:left;width:48%;padding:5px;height:90px">
	<a href="/servers/{id}"><img class="av" src="/bildes/cs/{mapimg}.jpg" alt="{map}" /></a>
	<h4 style="margin: 0;paddong:3px 0"><a href="/servers/{id}">{title}</a></h4>
	<div style="font-size:90%"><div style="color:#888">{address}:{port}</div><div>Spēlētāji: {players}/{maxplayers}</div><div>Karte: {map}</div></div>
	<div class="c"></div>
</div>
<!-- END BLOCK : csserver-->
<div class="c"></div>
<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>

<!-- END BLOCK : csservlist-->

<!-- START BLOCK : csview-->
<h1>Server: {title}</h1>

<div style="float:left;width:150px;padding:10px 0">
	<iframe src="http://exs.lv/server.php?s={uid}&amp;color=333333&amp;bgcolor=FFFFFF&amp;padding=0&amp;players=true" hspace="0" vspace="0" border="0" frameborder="0" scrolling="no" width="150" height="640"></iframe>
</div>

<div style="float:right;width:580px;padding:10px 0">

	<form class="form" action="" method="get">
		<fieldset>
			<legend>Online spēlētāji 24 stundās</legend>
			<p>
				<img width="530" height="180" src="/servers/{id}/players_online?_={time}" alt="Online spēlētāji {address}" title="Online spēlētāji {address} pēdējās 24 stundās">
			</p>
		</fieldset>
	</form>

	<form class="form" action="" method="get">
		<fieldset>
			<legend>Online spēlētāji nedēļas laikā</legend>
			<p>
				<img width="530" height="180" src="/servers/{id}/players_online_week?_={time}" alt="Online spēlētāji {address}" title="Online spēlētāji {address} pēdējās nedēļas laikā">
			</p>
		</fieldset>
	</form>

	<form class="form" action="" method="get">
		<fieldset>
			<legend>Populārākās {address} kartes</legend>
			<p style="text-align: center;">
				<!-- START BLOCK : maplist-->
				<span style="font-size:{size}px">{map}</span>
				<!-- END BLOCK : maplist-->
			</p>
		</fieldset>
	</form>

	<form class="form" action="" method="get">
		<fieldset>
			<legend>Kods monitora ievietošanai mājas lapā</legend>
			<textarea style="width:95%;height:70px;font-size:90%">{code}</textarea>
			<a href="/cs_servera_monitors/?adr={address}&amp;port={port}&amp;bgcolor=FFFFFF&amp;color=333333&amp;width=140&amp;height=180&amp;padding=4" style="float:right;">Pielāgot monitoru &raquo;</a>
			<div class="c"></div>
		</fieldset>
	</form>

</div>

<div class="c"></div>
<!-- END BLOCK : csview-->
