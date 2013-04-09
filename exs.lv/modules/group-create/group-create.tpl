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
	<ul id="paytabs" class="shadetabs">
		<li><a href="/?c=313" class="selected"><img src="/bildes/flags/lv.png" alt="" />&nbsp;Latvijā</a></li>
		<li><a href="/?c=313&lang=uk"><img src="/bildes/flags/gb.png" alt="" />&nbsp;Lielbritānijā</a></li>
		<li><a href="/?c=313&lang=ie"><img src="/bildes/flags/ie.png" alt="" />&nbsp;Īrijā</a></li>
	</ul>
	<div id="pay" class="ajaxbox">
		<p>Sūti īsziņu ar tekstu: <strong>TXT EXS {user-id}</strong> uz numuru 1897</p>
		<p>Maksa (0,99 LVL) ir pievienota telefona rēķinam vai atrēķināta no priekšapmaksas kartes.<br />
		Atbalsts: +37128690182 | info@openidea.lv<br />
		Piedāvā fortumo.lv</p>
	</div>
</div>

</fieldset>
</form>

<!-- END BLOCK : group-create-->