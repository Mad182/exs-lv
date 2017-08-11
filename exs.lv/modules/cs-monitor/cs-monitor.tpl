<!-- START BLOCK : csmon-->
<h1>CS/CSS servera statusa monitors</h1>
<script type="text/javascript" src="/modules/cs-monitor/jscolor/jscolor.js"></script>
<form class="form" action="/cs_servera_monitors/" method="get">
	<fieldset>
		<legend>Izveidot monitoru</legend>
		<p style="float: right">
			<a href='http://host-tracker.com/' onMouseOver='this.href="http://host-tracker.com/website-monitoring-stats/5597722/ff/";'>
				<img width='80' height='15' border='0' alt='speedtest' src="http://ext.host-tracker.com/uptime-img/?s=15&amp;t=5597722&amp;m=0.59&amp;p=Total&amp;src=ff" />
			</a>
		</p>
		<p>
			<label>Adrese (IP vai domēns) : ports</label><br />
			<input style="width: 160px;" type="text" class="text" name="adr" value="{adr}" /><input style="width: 50px;margin-left: 3px;" type="text" class="text" name="port" value="{port}" />
		</p>
		<table>
			<tr>
				<td style="width: 140px;">
					<p>
						<label>Fona krāsa</label><br />#<input style="width: 60px;" type="text" class="text color" name="bgcolor" value="{bgcolor}" />
					</p>
				</td>
				<td style="width: 140px;">
					<p>
						<label>Teksta krāsa</label><br />#<input style="width: 60px;" type="text" class="text color" name="color" value="{color}" />
					</p>
				</td>
			</tr>
		</table>
		<table>
			<tr>
				<td style="width: 140px;">
					<p>
						<label>Platums</label><br /><input style="width: 35px;" type="text" class="text" name="width" value="{width}" />&nbsp;px
					</p>
				</td>
				<td style="width: 140px;">
					<p>
						<label>Augstums</label><br /><input style="width: 35px;" type="text" class="text" name="height" value="{height}" />&nbsp;px
					</p>
				</td>
				<td style="width: 140px;">
					<p>
						<label>Atkāpe no malām</label><br /><input style="width: 35px;" type="text" class="text" name="padding" value="{padding}" />&nbsp;px
					</p>
				</td>
			</tr>
		</table>
		<p>
			<label>{notitle}Nerādīt servera nosaukumu</label>
		</p>
		<p>
			{players}
			<input type="submit" value="Izveidot monitora kodu" />
		</p>
	</fieldset>
</form>
<!-- START BLOCK : csmon-->

<!-- START BLOCK : csinc-->
<form class="form">
	<fieldset>
		<legend>Kā tas izskatīsies</legend>
		{iframe}
	</fieldset>
</form>
<form class="form" action="" method="get">
	<fieldset>
		<legend>HTML kods ievietošanai mājas lapā</legend>
		<textarea style="width: 95%; height: 100px;font-size: 90%;">{code}</textarea>
	</fieldset>
	<fieldset>
		<legend>PHP kods (ar spēlētāju sarakstu)</legend>
		<textarea style="width: 95%; height: 80px;font-size: 90%;">{code-php}</textarea>
	</fieldset>
</form>
<!-- START BLOCK : csinc-->
<form class="form" id="kastas">
	<fieldset>
		<legend>Kas tad tas?</legend>
		<p>Monitoru atļauts brīvi izmantot jebkurā mājas lapā, bet būtu labi, ja Tu apmaiņā pret to kaut kur savā lapā ieliktu saiti uz exs.lv vai mūsu bannerīti:</p>
		<p><a href="http://exs.lv/" title="exs.lv spēles"><img src="http://exs.lv/bildes/banner_88x31.png" alt="exs.lv" width="88" height="31" /></a></p>
		<textarea style="width: 95%; height: 40px;font-size: 90%;">&lt;a href="http://exs.lv/" title="exs.lv spēles"&gt;&lt;img src="http://exs.lv/bildes/banner_88x31.png" alt="exs.lv" width="88" height="31" /&gt;&lt;/a&gt;</textarea>
		<p>Lai nodrošinātu ātru darbību un lieki nenoslogotu serverus, informācija monitorā tiek atjaunota ne biežāk, kā reizi 30 sekundēs.</p>
		<p>Trūkst attēla kādai kartei? <a href="http://exs.lv/?c=104&amp;act=compose&amp;to=1">Pasaki man!</a></p>
	</fieldset>
</form>

<form class="form">
	<fieldset>
		<legend>Nejauši izvelēti serveri</legend>
		<!-- START BLOCK : cslatest-->
		<iframe src="{src}&color=333333&bgcolor=FFFFFF&padding=4" hspace="0" vspace="0" border="0" frameborder="0" scrolling="no" width="170" height="200"></iframe>
		<!-- START BLOCK : cslatest-->
	</fieldset>
</form>
