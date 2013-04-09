<h1>Brīdinājumi{total_warns}</h1>

<!-- START BLOCK : warns-mod-->
<div class="form">
	<p class="notice">Moderatoriem un administratoriem nevar izteikt brīdinājumus!</p>
</div>
<!-- END BLOCK : warns-mod-->

<!-- START BLOCK : warns-noaccess-->
<div class="form">
	<p class="notice">Tev nav pieejas šai lapai!</p>
</div>
<!-- END BLOCK : warns-noaccess-->

<!-- START BLOCK : warns-nowarns-->
<div class="form">
	<p class="success">{msg}</p>
</div>
<!-- END BLOCK : warns-nowarns-->

<!-- START BLOCK : warns-edit-->
<form action="" method="post" class="form">
	<fieldset>
	  <legend>Pievienot brīdinājumu</legend>
	  <p>
			<textarea cols="20" rows="3" name="reason" style="height: 80px;">{reason}</textarea>
		</p>
		<p>
		  <input class="button" type="submit" name="submit_warn" value="Saglabāt" />
		</p>
	</fieldset>
</form>
<!-- END BLOCK : warns-edit-->

<!-- START BLOCK : warns-remove-->
<form action="" method="post" class="form">
	<fieldset>
	  <legend>Noņemt brīdinājumu</legend>
	  <p class="notice">{reason}</p>
	  <p>
	    <label>Iemesls noņemšanai</label><br />
			<textarea cols="20" rows="3" name="remove_reason" style="height: 80px;"></textarea>
		</p>
		<p>
		  <input class="button" type="submit" name="remove_warn" value="Noņemt" />
		</p>
	</fieldset>
</form>
<!-- END BLOCK : warns-remove-->

<!-- START BLOCK : warns-list-->
<div class="form">
	<h3>Aktīvie brīdinājumi</h3>
	<!-- START BLOCK : warns-active-->
	<div class="error">
		<span style="font-size:90%">
			<a href="{aurl}">{author}</a> {date} {edit} {remove}
		</span><br />
		<strong>Iemesls:</strong> {reason}    
		<div class="c"></div>
	</div>
	<!-- END BLOCK : warns-active-->
	<h3>Brīdinājumu arhīvs</h3>
	<!-- START BLOCK : warns-inactive-->
	<div class="notice">
		<span style="font-size:90%">
			<a href="{aurl}">{author}</a> {date}
		</span><br />
		<strong>Iemesls:</strong> {reason}
		<strong>Noņemšanas iemesls:</strong> {remove_reason}
		<div class="c"></div>
	</div>
	<!-- END BLOCK : warns-inactive-->
</div>
<!-- END BLOCK : warns-list-->

<h3>Banu vēsture</h3>
<div class="form">

<!-- START BLOCK : bans-active-->
	<p class="error">
		<span style="font-size:90%">
			<a href="{aurl}">{author}</a> {date}
		</span><br />
		<strong>Ilgums:</strong> {length}<br />
		<strong>Iemesls:</strong> {reason}
	</p>
<!-- END BLOCK : bans-active-->

<!-- START BLOCK : bans-inactive-->
	<p class="notice">
		<span style="font-size:90%">
			<a href="{aurl}">{author}</a> {date}
		</span><br />
		<strong>Ilgums:</strong> {length}<br />
		<strong>Iemesls:</strong> {reason}
	</p>
<!-- END BLOCK : bans-inactive-->

<!-- START BLOCK : bans-no-->
	<p class="success">Nav saglabātu banu :)</p>
<!-- END BLOCK : bans-no-->

</div>