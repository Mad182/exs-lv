<!-- START BLOCK : user-profile-->
<div class="tabMain">
	{edit}
	<p>
		<!-- START BLOCK : user-profile-pm-->
		<a href="/pm/write/?to={user-id}" class="button primary" title="Nosūtīt vēstuli">Nosūtīt PM</a>
		<a href="/user/{user-id}/give" class="button primary" title="Dāvināt exs kredītus">Dāvināt expts</a>
		<!-- END BLOCK : user-profile-pm-->
		<!-- START BLOCK : user-profile-ban-->
		<a href="/user/{user-id}/block" class="button danger">Bloķēt lietotāju</a>
		<!-- END BLOCK : user-profile-ban-->
		<!-- START BLOCK : user-profile-lol-->
		<a href="/lol-user/{user-id}" class="button primary">LoL profili</a>
		<!-- END BLOCK : user-profile-lol-->
		{friend-link}
	</p>
	<!-- START BLOCK : user-profile-last_action-->
	<p>Šobrīd skatās {user-last_action}</p>
	<!-- END BLOCK : user-profile-last_action-->
	<p>Reģistrējās pirms {user-days} {user-days-text}, pēdējo reizi bija online pirms {user-lastseen}</p>
	<dl id="profile-info" class="list-attr">

		<!-- START BLOCK : info-node-->
		<dt>{title}</dt>
		<dd>{value}</dd>
		<!-- END BLOCK : info-node-->

		<dt>Karma</dt><dd>{user-karma}</dd>
		<dt>Posti</dt><dd>{user-posts} (vidēji {user-postsday} dienā)</dd>
		<dt>Raksti</dt><dd><a href="/topics/{user-id}">{user-pages}</a></dd>
		<dt>Vērtējums</dt><dd>{user-votes}</dd>
		<dt>Vērtēja citus</dt><dd>{user-vote_total}x ({user-vote_others})</dd>
		<!-- START BLOCK : user-modinfo-->
		<dt><i>E-pasts</i></dt><dd><i>{mail}</i></dd>
		<dt><i>Pēdējā IP</i></dt><dd><i><a href="/checkform?ip={lastip}">{lastip}</a></i></dd>
		<dt><i>UserAgent</i></dt><dd><i>{user_agent}</i></dd>
		<!-- END BLOCK : user-modinfo-->
	</dl>
	<!-- START BLOCK : user-profile-awards-->
	<h3>Apbalvojumi:</h3>
	<ul id="listsub-list">
		<!-- START BLOCK : user-profile-awards-node-->
		<li style="background: url('/bildes/icons/{award-icon}') no-repeat 0 50%;"><a href="{award-link}">{award-title}</a></li>
		<!-- END BLOCK : user-profile-awards-node-->
	</ul>
	<!-- END BLOCK : user-profile-awards-->
	<!-- START BLOCK : user-profile-about-->
	<h3>Par sevi</h3>
	{user-about}
	<!-- END BLOCK : user-profile-about-->

	<div class="c"></div>
	<p>&nbsp;</p>
	<div style="float:left;width:68%">
		<!-- START BLOCK : user-actions-->
		<h3>Pēdējās aktivitātes lapā</h3>
		<div class="box">
			{out}
		</div>
		<!-- END BLOCK : user-actions-->
	</div>

	<div style="float:right;width:30%">
		<!-- START BLOCK : user-profile-views-->
		<h3>Profilu apskatījuši</h3>
		<div class="box">
			<ul class="small-userlist">
				<!-- START BLOCK : user-profile-views-node-->
				<li><a href="/user/{id}" title="{nick} apskatījās {date}"><img src="{avatar}" alt="{nick}" width="45" height="45" /></a></li>
				<!-- END BLOCK : user-profile-views-node-->
			</ul>
		</div>

		<!-- END BLOCK : user-profile-views-->
	</div>
	<div class="c"></div>

	<!-- START BLOCK : grouplist-->
	<h3>Manas grupas</h3>
	<!-- START BLOCK : g-admin-->
	<a href="/group/{group-id}" class="l-gadmin">{group-title}</a>,
	<!-- END BLOCK : g-admin-->
	<!-- START BLOCK : g-member-->
	<a href="/group/{group-id}" class="{group-class}">{group-title}</a>,
	<!-- END BLOCK : g-member-->
	<!-- END BLOCK : grouplist-->

	<div class="c"></div>

	<!-- START BLOCK : user-profile-lastpage-->
	<div class="half-left">
		<h3>Jaunākās {user-nick} tēmas</h3>
		<div class="box">
			<ul class="bloglist">
				<!-- START BLOCK : user-profile-lastpage-node-->
				<li><a href="{node-url}">{lastpage-title}</a></li>
				<!-- END BLOCK : user-profile-lastpage-node-->
			</ul>
		</div>
	</div>
	<!-- END BLOCK : user-profile-lastpage-->

	<!-- START BLOCK : user-profile-lastbookmark-->
	<div class="half-right">
		<h3>Rakstu izlasei pievienots</h3>
		<div class="box">
			<ul class="bloglist">
				<!-- START BLOCK : user-profile-lastbookmark-node-->
				<li><a href="{node-url}">{bookmark-title}</a></li>
				<!-- END BLOCK : user-profile-lastbookmark-node-->
			</ul>
		</div>
	</div>
	<!-- END BLOCK : user-profile-lastbookmark-->

	<div class="c"></div>

	<!-- START BLOCK : user-profile-lastcom-->
	<div class="half-left">
		<h3>Pēdējie komentāri rakstos</h3>
		<div class="box">
			<ul class="bloglist">
				<!-- START BLOCK : user-profile-lastcom-node-->
				<li><a href="{url}">{comments-text}</a></li>
				<!-- END BLOCK : user-profile-lastcom-node-->
			</ul>
		</div>
	</div>
	<!-- END BLOCK : user-profile-lastcom-->

	<!-- START BLOCK : user-profile-lastgcom-->
	<div class="half-right">
		<h3>Bilžu komentāri</h3>
		<div class="box">
			<ul class="bloglist">
				<!-- START BLOCK : user-profile-lastgcom-node-->
				<li><a href="/gallery/{comments-uid}/{comments-image}#c{comments-id}">{comments-text}</a></li>
				<!-- END BLOCK : user-profile-lastgcom-node-->
			</ul>
		</div>
	</div>
	<!-- END BLOCK : user-profile-lastgcom-->

	<div class="c"></div>
</div>

<!-- END BLOCK : user-profile-->

<!-- START BLOCK : user-profile-changenick-->
<div class="tabMain">
	<form id="edit-profile" class="form" action="{page-url}" method="post">
		<script type="text/javascript">
			function UserExists() {
				nick = document.getElementById('new-nick').value;
				load("/?c=250&user=" + nick, 'userexists');
			}
		</script>
		<fieldset>
			<legend>Exs.lv nika maiņa</legend>
			<p>
				<label for="new-nick">Jaunais niks:</label><br />
				<input type="text" class="text" name="new-nick" id="new-nick" value="" maxlength="14" onblur="UserExists();" onkeyup="UserExists();" /> <span id="userexists"></span>
			</p>
			<p>
				<input type="submit" name="submit" id="submit" class="button primary" value="Saglabāt" />
			</p>
			<p>Nika maiņa ir maksas pakalpojums. Katra nika mainīšanas reize maksā <strong>5</strong> exs.lv kredīta punktus. Tev šobrīd ir <strong>{user-credit}</strong> kredīta punkti. Apdomā labi, un raksti uzmainīgi, jo par 5 punktiem niku varēsi mainīt tikai vienu reizi. Pēc nika maiņas būs jāielogojas atkārtoti. Ja rodas jautājumi vai problēmas, vispirms sazinies ar lietotāju <a href="/user/1-Minka"><span class="admins">Minka</span></a>.</p>

			<h4>Kā iegādāties 5 kredīta punktus?</h4>
			<div class="box">
				<ul class="tabs">
					<li><a href="/payment-info" class="active ajax" id="default-payment-link"><img src="http://img.exs.lv/bildes/flags/lv.png" alt="" />&nbsp;Latvijā</a></li>
					<li><a href="/payment-info/uk" class="ajax"><img src="http://img.exs.lv/bildes/flags/gb.png" alt="" />&nbsp;Lielbritānijā</a></li>
					<li><a href="/payment-info/ie" class="ajax"><img src="http://img.exs.lv/bildes/flags/ie.png" alt="" />&nbsp;Īrijā</a></li>
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

</div>
<!-- END BLOCK : user-profile-changenick-->

<!-- START BLOCK : user-profile-buytitle-->
<div class="tabMain">
	{pay}
	<p>Lietotāja nosaukums parādās zem profila attēliem. Brīvi izvēlētu nosaukumu var iegūt vai nu sasniedzot 500. karmas līmeni, vai nopērkot iespēju to mainīt par <strong>3</strong> exs.lv kredīta punktiem. Tev šobrīd ir <strong>{user-credit}</strong> kredīta punkti.<br />Ja rodas jautājumi vai problēmas, vispirms sazinies ar lietotāju <a href="/user/1-Minka"><span class="admins">Minka</span></a>.</p>

	<h4>Kā iegādāties 5 kredīta punktus?</h4>
	<div class="box">
		<ul class="tabs">
			<li><a href="/payment-info" class="active ajax" id="default-payment-link"><img src="http://img.exs.lv/bildes/flags/lv.png" alt="" />&nbsp;Latvijā</a></li>
			<li><a href="/payment-info/uk" class="ajax"><img src="http://img.exs.lv/bildes/flags/gb.png" alt="" />&nbsp;Lielbritānijā</a></li>
			<li><a href="/payment-info/ie" class="ajax"><img src="http://img.exs.lv/bildes/flags/ie.png" alt="" />&nbsp;Īrijā</a></li>
		</ul>
		<div id="pay" class="ajaxbox">
		</div>
	</div>

	<script type="text/javascript">
		$(document).ready(function() {
			$('#default-payment-link').click();
		});
	</script>

</div>
<!-- END BLOCK : user-profile-buytitle-->

<!-- START BLOCK : user-profile-edit-->
<div class="tabMain">
	<form id="edit-profile" class="form" action="{page-url}" method="post">
		<fieldset>
			<legend>Publiskā profila informācija</legend>

			<!-- START BLOCK : custom_title-->
			<p>
				<label for="edit-custom_title">Lietotāja nosaukums:</label><br />
				<input type="text" class="text" name="edit-custom_title" id="edit-custom_title" value="{user-custom_title}" maxlength="18" />
			</p>
			<!-- END BLOCK : custom_title-->

			<!-- START BLOCK : custom_title_buy-->
			<p><a href="/user/buytitle"><strong>Vēlies nomainīt lietotāja nosaukumu?</strong></a></p>
			<!-- END BLOCK : custom_title_buy-->

			<p>
				<label for="edit-skype">Skype niks:</label><br />
				<input type="text" class="text" name="edit-skype" id="edit-skype" value="{user-skype}" maxlength="32" />
			</p>
			<p>
				<label for="edit-yt_name">YouTube:</label><br />
				<input type="text" class="text" name="edit-yt_name" id="edit-yt_name" value="{user-yt_name}" maxlength="32" />
			</p>
			<p>
				<label for="edit-twitter">Twitter:</label><br />
				<input type="text" class="text" name="edit-twitter" id="edit-twitter" value="{user-twitter}" maxlength="64" />
			</p>
			<p>
				<label for="edit-web">Mājaslapa:</label><br />
				<input type="text" class="text" name="edit-web" id="edit-web" value="{user-web}" maxlength="128" />
			</p>
			<p>
				<label for="edit-city">Pilsēta:</label><br />
				<select class="text" name="edit-city" id="edit-city">
					<option value="0">Neteikšu</option>
					<!-- START BLOCK : user-profile-edit-city-->
					<option value="{city-id}"{city-sel}>{city-title}</option>
					<!-- END BLOCK : user-profile-edit-city-->
				</select>
			</p>

			<!-- START BLOCK : sig-about-edit-->
			<label for="edit-signature">Paraksts:<br /><span class="description">(parādās zem komentāriem)</span></label><br />
			<textarea rows="4" cols="20" name="edit-signature" id="edit-signature">{user-signature}</textarea>

			<br />

			<label for="edit-signature">Par mani<br /><span class="description">(redzams citiem atverot Tavu profilu)</span></label><br />
			<textarea rows="4" cols="20" style="width:98%;height:300px" name="edit-about" id="edit-about">{user-about}</textarea>

			<br />
			<!-- END BLOCK : sig-about-edit-->

			<!-- START BLOCK : sig-about-disabled-->
			<p class="notice">
				Tavam profilam ir atslēgta foruma paraksta un profila informācijas iespēja.
			</p>
			<!-- END BLOCK : sig-about-disabled-->

			<p>
				<input type="submit" name="submit" class="button primary" value="Saglabāt" />
			</p>
		</fieldset>
	</form>
</div>
<!-- END BLOCK : user-profile-edit-->

<!-- START BLOCK : user-profile-security-->
<div class="tabMain">
	<form id="edit-profile" class="form" action="{page-url}" method="post">
		<fieldset>
			<legend>Profila drošības iestatījumi</legend>
			<p>
				<label for="edit-mail">E-pasta adrese:</label><br />
				<input type="text" class="text" name="edit-mail" id="edit-mail" value="{user-mail}" maxlength="64" />
			</p>

			<h4>Paroles maiņa:</h4>
			<p>
				<label for="password-old">Vecā parole:</label><br />
				<input type="password" class="text" name="password-old" id="password-old" value="" />
			</p>
			<p>
				<label for="password-1">Jaunā parole:</label><br />
				<input type="password" class="text" name="password-1" id="password-1" value="" />
			</p>
			<p>
				<label for="password-2">Atkārto jauno paroli:</label><br />
				<input type="password" class="text" name="password-2" id="password-2" value="" />
			</p>
			<p>
				<input type="submit" name="submit" class="button primary" value="Saglabāt" />
			</p>
		</fieldset>
	</form>
</div>
<!-- END BLOCK : user-profile-security-->

<!-- START BLOCK : user-profile-avatar-->
<div class="tabMain">
	<form id="edit-profile" class="form" action="{page-url}" method="post" enctype="multipart/form-data">
		<fieldset>
			<legend>Avatara maiņa</legend>
			<p>
				<label for="edit-avatar">Profila attēls: (<a href="/animacijas">Vēlies kustīgu?</a>)</label><br />
				<input type="file" class="text" name="edit-avatar" id="edit-avatar" />
			</p>
			<p>
				<input type="submit" name="submit" class="button primary" value="Saglabāt" />
			</p>
		</fieldset>
	</form>
</div>
<!-- END BLOCK : user-profile-avatar-->


<!-- START BLOCK : user-profile-settings-->
<div class="tabMain">
	<form id="edit-profile" class="form" action="{page-url}" method="post">
		<fieldset>
			<legend>Tavi lapas iestatījumi</legend>

			<h3>Sekot līdzi jaunākajām tēmām</h3>
			<p>
				<label for="edit-show_code"><input type="checkbox" name="edit-show_code" id="edit-show_code"{edit-show_code-mark} />coding.lv</label>
			</p>
			<p>
				<label for="edit-show_lol"><input type="checkbox" name="edit-show_lol" id="edit-show_lol"{edit-show_lol-mark} />lol.exs.lv</label>
			</p>
			<p>
				<label for="edit-show_rp"><input type="checkbox" name="edit-show_rp" id="edit-show_rp"{edit-show_rp-mark} />rp.exs.lv</label>
			</p>
			<p>
				<label for="edit-show_rs"><input type="checkbox" name="edit-show_rs" id="edit-show_rs"{edit-show_rs-mark} />runescape.exs.lv</label>
			</p>

			<h3>Lapas izskats</h3>
			<p>
				<label for="edit-enablesig"><input type="checkbox" name="edit-enablesig" id="edit-enablesig"{edit-enablesig-mark} />rādīt lietotāju parakstus pie komentāriem</label>
			</p>
			<p>
				<label for="edit-skin">Tēma:</label>
				<select name="edit-skin" id="edit-skin">
					<option value="0"{user-skin-0}>Gaiša</option>
					<option value="1"{user-skin-1}>Tumša</option>
				</select>
				<br />
				<a href="/augsa" target="_blank">Pielāgot augšu</a>
			</p>
			<p>
				<input type="submit" name="submit" class="button primary" value="Saglabāt" />
			</p>
		</fieldset>
	</form>
</div>
<!-- END BLOCK : user-profile-settings-->



<!-- START BLOCK : user-profile-block-->
<div class="tabMain">
	<form id="edit-profile" class="form" action="/user/{user-id}/block" method="post" enctype="multipart/form-data">
		<fieldset>
			<legend>Bloķēt pieeju lapai</legend>
			<p>
				<label for="block-reason">Iemesls:</label><br />
				<input type="text" class="text" name="block-reason" id="block-reason" value="" maxlength="256" />
			</p>
			<p>
				<label for="block-length">Termiņš:</label><br />
				<select name="block-length" id="block-length">
					<option value="21600">6 stundas</option>
					<option value="86400" selected="selected">1 diena</option>
					<option value="259200">3 dienas</option>
					<option value="604800">1 nedēļa</option>
					<option value="1209600">2 nedēļas</option>
					<option value="2629743">1 mēnesis</option>
					<option value="5184000">2 mēneši</option>
					<option value="7889231">3 mēneši</option>
					<option value="15552000">6 mēneši</option>
					<option value="31556926">1 gads</option>
				</select>
			</p>
			<!-- START BLOCK : block-domain -->
			<p>
				<label for="block-domain">Vieta:</label><br />
				<select name="block-domain" id="block-domain">
					<option value="0" selected="selected">Visos domēnos</option>
					<!-- START BLOCK : block-domain-node -->
					<option value="{id}">{domain}</option>
					<!-- START BLOCK : block-domain-node -->
				</select>
			</p>
			<!-- END BLOCK : block-domain -->
			<p>
				<!-- START BLOCK : warn-removal -->
				<label for="warn-removal">Cik senākos brīdinājumus noņemt?</label><br />
				<select name="warn-removal" id="warn-removal">
					<option value="0">Nevienu</option>
					<!-- START BLOCK : warn-removal-option -->
					<option value="{x}">{x}</option>
					<!-- END BLOCK : warn-removal-option -->
				</select>
			</p>
			<p>
				<label for="warn-reason">Brīdinājumu noņemšanas iemesls:</label><br />
				<input type="text" class="text" name="warn-removal-reason" id="warn-reason" value="" maxlength="256" />
				<!-- END BLOCK : warn-removal -->
				<!-- START BLOCK : no-active-warns -->
				Šim lietotājam šobrīd nav aktīvu, noņemamu brīdinājumu.
				<!-- END BLOCK : no-active-warns -->
			</p>
			<p>
				<input class="button primary" type="submit" name="submit" value="OK" />
			</p>
		</fieldset>
	</form>
</div>
<!-- END BLOCK : user-profile-block-->
<!-- START BLOCK : user-profile-give-->
<div class="tabMain">
	<form id="give-profile" class="form" action="" method="post">
		<fieldset>
			<legend>Dāvināt exs kredītu</legend>
			<p>
				<select name="exs-amount">
					<!-- START BLOCK : give-am-->
					<option value="{value}">{value}</option>
					<!-- END BLOCK : give-am-->
				</select>
			</p>
			<p><input class="button danger confirm" type="submit" name="submit" id="submit" value="OK" /></p>
		</fieldset>
	</form>
</div>
<!-- END BLOCK : user-profile-give-->
