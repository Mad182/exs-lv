<!-- START BLOCK : group-options-->
<ul id="page-options"><li class="option-edit"><a href="/?group={group-id}&amp;act=edit">labot lapu</a></ul>
<!-- END BLOCK : group-options-->
<!-- START BLOCK : tab-options-->
<ul id="page-options"><li class="option-edit"><a href="/group/{group-id}/tab/{slug}/edit">labot lapu</a></ul>
<!-- END BLOCK : tab-options-->

<!-- START BLOCK : group-menu-->
<h1>{group-title}</h1>

{main-ad-include}

<ul class="tabs">
	<li><a href="/group/{group-id}" class="{active-tab-info}"><span class="group-profile">Sākums</span></a></li>
	<li><a href="/group/{group-id}/forum" class="{active-tab-community}"><span class="comments">Sarunas</span></a></li>
	<li><a href="/group/{group-id}/members" class="{active-tab-members}"><span class="users">Biedri</span></a></li>
	<!-- START BLOCK : group-menu-add-->
	<li><a href="/group/{group-id}/tab/{url}" class="{sel}">{title}</a></li>
	<!-- END BLOCK : group-menu-add-->
	<li><a href="/group/{group-id}/search" class="{active-tab-search}"><span class="search">Meklēt</span></a></li>
	<!-- START BLOCK : group-menu-options-->
	<li><a href="/group/{group-id}/options" class="{active-tab-options}"><span class="tools">Rīki</span></a></li>
	<!-- END BLOCK : group-menu-options-->
</ul>
<!-- END BLOCK : group-menu-->

<!-- START BLOCK : group-pay-->
<div class="tabMain">
	<div style="float: left;width: 69%;overflow: hidden">

		<p>Iestāšanās šajā grupā maksā 3 exs kredītpunktus. Tev šobrīd ir <strong>{user-credit}</strong> kredīta punkti.</p>
		{pay}
		<h4>Kā iegādāties 5 kredīta punktus?</h4>

		<div class="box">
			<ul id="paytabs" class="shadetabs">
				<li><a href="/?c=313" rel="pay"><img src="/bildes/flags/lv.png" alt="" />&nbsp;Latvijā</a></li>
				<li><a href="/?c=313&lang=uk" rel="pay"><img src="/bildes/flags/gb.png" alt="" />&nbsp;Lielbritānijā</a></li>
				<li><a href="/?c=313&lang=ie" rel="pay"><img src="/bildes/flags/ie.png" alt="" />&nbsp;Īrijā</a></li>
			</ul>

			<div id="pay" class="ajaxbox"><noscript><p>Sūti īsziņu ar tekstu: <strong>TXT EXS {user-id}</strong> uz numuru 1897</p>
					<p>
					<p><small>Maksa (0,99 LVL) ir pievienota telefona rēķinam vai atrēķināta no priekšapmaksas kartes.<br />
							Atbalsts: +37128690182 | info@openidea.lv<br />
							Piedāvā fortumo.lv</small></p></noscript></div>
			<script type="text/javascript">
				var pay=new ddajaxtabs("paytabs", "pay")
				pay.setpersist(true)
				pay.setselectedClassTarget("link")
				pay.init(9976000)
			</script>
		</div>
	</div>
	<div style="float: right;width: 29%">

		<!-- START BLOCK : nmembers-pay-->
		<h3>Jaunākie biedri</h3>
		<ul class="small-userlist">
			<!-- START BLOCK : nmembers-pay-node-->
			<li><a href="/user/{member-id}" title="{member-nick}"><img src="{avatar}" alt="" /></a></li>
			<!-- END BLOCK : nmembers-pay-node-->
		</ul>
		<div class="c"></div>
		<!-- END BLOCK : nmembers-pay-->
		<h3>Grupas statistika</h3>
		<p style="font-size: 90%;">
			Biedri: {group-members}<br />
			Posti: {group-posts}<br />
			Admins: {group-admin}
		</p>
	</div>
	<div class="c"></div>
</div>
<!-- END BLOCK : group-pay-->

<!-- START BLOCK : group-info-->
<div class="tabMain">
	<div style="float: left;width: 69%;overflow: hidden">
		{group-text}
	</div>
	<div style="float: right;width: 29%">
		<!-- START BLOCK : group-info-apply-->
		<p><a class="l-gmember" href="/?group={group-id}&amp;act=apply">Pieteikties</a></p>
		<!-- END BLOCK : group-info-apply-->

		<!-- START BLOCK : group-info-apply-paid-->
		<p><a class="l-gmember" href="/?group={group-id}&amp;act=pay">Pieteikties</a></p>
		<!-- END BLOCK : group-info-apply-paid-->

		<!-- START BLOCK : glatest-box-->
		<h3>Jaunākais</h3>
		<ul class="blockhref mb-col">
			<!-- START BLOCK : glatest-box-node-->
			<li style="font-size:10px;text-align:left"><a href="{url}"><img class="av" src="{avatar}" alt="{nick}" /> <span class="author">{nick}</span> <span>pirms {time}</span> {text}&nbsp;[{resp}]</a></li>
			<!-- END BLOCK : glatest-box-node-->
		</ul>
		<!-- END BLOCK : glatest-box-->

		<!-- START BLOCK : g-poll-box-->
		<h3>Aptauja</h3>
		<div class="box">
			<p><strong>{poll-title}</strong></p>
			<!-- START BLOCK : g-poll-answers-->
			<ol class="poll-answers">
				<!-- START BLOCK : g-poll-answers-node-->
				<li>{poll-answer-question}
					<div>
						<span>{poll-answer-percentage}%</span>
						<div style="width: {poll-answer-percentage}%;"></div>
					</div>
				</li>
				<!-- END BLOCK : g-poll-answers-node-->
			</ol>
			Balsojuši: {poll-totalvotes}<br />
			<!-- END BLOCK : g-poll-answers-->
			<!-- START BLOCK : g-poll-questions-->
			<form method="post" action="">
				<fieldset>
					<legend style="display:none;">Aptauja</legend>
					<!-- START BLOCK : g-poll-error-->
					<p>{poll-error}</p>
					<!-- END BLOCK : g-poll-error-->
					<!-- START BLOCK : g-poll-options-->
					<ol id="poll-questions">
						<!-- START BLOCK : g-poll-options-node-->
						<li><label><input type="radio" name="g-questions" value="{poll-options-id}" /> {poll-options-question}</label></li>
						<!-- END BLOCK : g-poll-options-node-->
					</ol>
					<input type="submit" name="g-vote" value="Balsot!" />
					<!-- END BLOCK : g-poll-options-->
				</fieldset>
			</form>
			<!-- END BLOCK : g-poll-questions-->
		</div>
		<!-- END BLOCK : g-poll-box-->

		<!-- START BLOCK : nmembers-->
		<h3>Biedri</h3>
		<ul class="small-userlist">
			<!-- START BLOCK : nmembers-node-->
			<li><a href="/user/{member-id}" title="{member-nick}"><img src="{avatar}" alt="" /></a></li>
			<!-- END BLOCK : nmembers-node-->
		</ul>
		<div class="c"></div>
		<!-- END BLOCK : nmembers-->

		<h3>Statistika</h3>
		<p style="font-size: 90%">
			Biedri: {group-members}<br />
			Posti: {group-posts}<br />
			Admins: {group-admin}
		</p>
		<!-- START BLOCK : group-info-cancel-->
		<p><a class="l-gmember" href="/?group={group-id}&amp;act=cancel&amp;hash={hash}">Dzēst pieteikumu</a></p>
		<!-- END BLOCK : group-info-cancel-->

		<!-- START BLOCK : group-info-quit-->
		<p><a class="l-gmember confirm" href="/?group={group-id}&amp;act=cancel&amp;hash={hash}">Pamest grupu</a></p>
		<!-- END BLOCK : group-info-quit-->
	</div>
	<div class="c"></div>
</div>
<!-- END BLOCK : group-info-->

<!-- START BLOCK : group-tab-->
<div class="tabMain">
	<div id="full-story">
		{tab-text}
		{tab-module}
	</div>
	<div class="c"></div>
</div>
<!-- END BLOCK : group-tab-->

<!-- START BLOCK : noguestacc-tab-->
<div class="tabMain">
	<div class="form">
		<p class="notice">Šo sadaļu apskatīt var tikai apstiprinātie biedri!</p>
	</div>
</div>
<!-- END BLOCK : noguestacc-tab-->

<!-- START BLOCK : group-tab-edit-->
<div class="tabMain">
	<form action="{page-url}" class="form" method="post">
		<fieldset>
			<legend>Labot saturu</legend>
			<p>
				<textarea name="tab-text" id="tab-text" cols="94" rows="40" style="width: 100%; height: 700px">{tab-text}</textarea>
			</p>
			<p>
				<input type="submit" name="submit" value="Saglabāt izmaiņas" class="button" />
			</p>
		</fieldset>
	</form>
	{tab-module}
</div>
<!-- END BLOCK : group-tab-edit-->

<!-- START BLOCK : group-edit-->
<div class="tabMain">


	<form action="{page-url}" class="form" method="post" enctype="multipart/form-data">
		<fieldset>
			<legend>Labot grupas informāciju</legend>
			<p>
				<label for="edit-group-title">Nosaukums:</label><br />
				<input type="text" name="edit-group-title" id="edit-group-title" class="text" value="{group-title}" maxlength="64" disabled="disabled" />
			</p>
			<p>
				<label for="edit-group-text">Teksts:</label><br />
				<textarea name="edit-group-text" id="edit-group-text" cols="94" rows="40" style="width: 60%; height: 700px">{group-text}</textarea>
			</p>
			<!-- START BLOCK : edit-group-av-->
			<p>
				<a class="thb-image"><img src="{img}" alt="Avatars" /></a>
			</p>
			<!-- END BLOCK : edit-group-av-->
			<p>
				<label for="edit-avatar">Grupas avatars:</label><br />
				<input type="file" class="long" name="edit-avatar" id="edit-avatar" />
			</p>
			<!-- START BLOCK : group-edit-category-->
			<p>
				<label for="edit-category_id">Kategorija:</label><br />
				<select name="edit-category_id" id="edit-category_id">
					<!-- START BLOCK : select-category-->
					<option value="{id}"{sel}>{title}</option>
					<!-- END BLOCK : select-category-->
				</select>
			</p>
			<!-- END BLOCK : group-edit-category-->
			<!-- START BLOCK : group-edit-interest-->
			<p>
				<label for="edit-interest_id">Interešu grupa:</label><br />
				<select name="edit-interest_id" id="edit-interest_id">
					<option value="0">--</option>
					<!-- START BLOCK : select-interest-->
					<option value="{id}"{sel}>{title}</option>
					<!-- END BLOCK : select-interest-->
				</select>
			</p>
			<!-- END BLOCK : group-edit-interest-->
			<p>
				<input type="submit" name="submit" value="Saglabāt izmaiņas" class="submit" />
			</p>
		</fieldset>
	</form>

</div>
<!-- START BLOCK : group-edit-->

<!-- START BLOCK : group-members-->
<div class="tabMain">

	<!-- START BLOCK : pending-->
	<strong>Pieteikumi dalībai grupā</strong>
	<ul id="friends-pending">
		<!-- START BLOCK : pending-node-->
		<li>
			<a class="image" href="/user/{pending-uid}"><img src="{avatar}" alt="" /></a>
			<h3><a href="/user/{pending-uid}">{pending-nick}</a> <span>{pending-date}</span></h3>
			<a href="/?group={group-id}&amp;act=confirm&amp;confirm={pending-id}">Apstiprināt</a>
			<div class="c"></div>
		</li>
		<!-- END BLOCK : pending-node-->
	</ul>
	<div class="c"></div>
	<!-- END BLOCK : pending-->

	<!-- START BLOCK : members-->
	<strong>Visi biedri</strong>
	<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
	<ul id="friend-list">
		<!-- START BLOCK : members-node-->
		<li class="{member-class}"><a class="profile-link" href="/user/{member-id}"><img src="{avatar}" alt="" /><br />{member-nick}</a>
			<!-- START BLOCK : member-delete-->
			<a class="delete confirm" title="Dzēst dalībnieku no grupas" href="/?group={group-id}&amp;act=drop&amp;drop={member-id}"><img src="/bildes/x.png" alt="x" title="Dzēst dalībnieku no grupas" /></a>
			<!-- END BLOCK : member-delete-->
			<!-- START BLOCK : member-moderator-->
			<a class="moderator confirm" href="/?group={group-id}&amp;act=setmod&amp;uid={member-id}"><img src="/bildes/icons/user_add.png" alt="mod" title="Uzlikt par moderatoru" /></a>
			<!-- END BLOCK : member-moderator-->
			<!-- START BLOCK : member-unmoderator-->
			<a class="unmoderator confirm" href="/?group={group-id}&amp;act=unsetmod&amp;uid={member-id}"><img src="/bildes/icons/user_delete.png" alt="unmod" title="Noņemt moderatora statusu" /></a>
			<!-- END BLOCK : member-unmoderator-->
		</li>
		<!-- END BLOCK : members-node-->
	</ul>
	<div class="c"></div>
	<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
	<!-- END BLOCK : members-->

</div>
<!-- END BLOCK : group-members-->


<!-- START BLOCK : group-community-->

<div class="tabMain">

	<!-- START BLOCK : noguestacc-->
	<div class="form">
		<p class="notice">Grupas sarunās var piedalīties tikai apstiprinātie biedri!</p>
	</div>
	<!-- START BLOCK : noguestacc-login-->
	<form id="login-form-inline" class="form" action="" method="post">
		<fieldset>
			<legend>Ielogoties</legend>
			<input type="hidden" name="xsrf_token" value="{xsrf}" />
			<p>
				<label for="login-nick-inline">Niks:</label><br />
				<input id="login-nick-inline" class="text" name="niks" type="text" />
			</p>
			<p>
				<label for="login-pass-inline">Parole:</label><br />
				<input id="login-pass-inline" class="text" name="parole" type="password" />
			</p>
			<p>
				<input name="login-submit" id="login-submit-inline" value="Ienākt" type="submit" />
			</p>
		</fieldset>
	</form>
	<!-- END BLOCK : noguestacc-login-->
	<!-- END BLOCK : noguestacc-->

	<!-- START BLOCK : user-miniblog-->
	<!-- START BLOCK : archived-->
	<div class="form">
		<p class="notice">Grupa ir arhivēta. Tajā vairs nevar pievienot jaunas tēmas.</p>
	</div>
	<!-- END BLOCK : archived-->
	<!-- INCLUDE BLOCK : conversation -->
	<!-- END BLOCK : user-miniblog-->
</div>

<!-- END BLOCK : group-community-->

<!-- START BLOCK : group-search-->
<div class="tabMain">

	<!-- START BLOCK : noguestacc-search-->
	<div class="form">
		<p class="notice">Meklētāju var izmantot tikai apstiprinātie grupas biedri!</p>
	</div>
	<!-- END BLOCK : noguestacc-search-->

	<!-- START BLOCK : form-search-->
	<form class="form" action="" method="GET">
		<fieldset>
			<legend>Meklētājs</legend>
			<p>
				<label for="search-q">Meklējamais vārds vai frāze</label><br />
				<input type="text" name="q" id="search-q" class="text" value="{qstr}" />
			</p>
			<p>
				<input type="submit" value="Meklēt" class="button" />
			</p>
		</fieldset>
	</form>
	<!-- END BLOCK : form-search-->

	<!-- START BLOCK : res-search-->
	<ol>
		<!-- START BLOCK : res-search-node-->
		<li style="border-bottom: 1px solid #ddd">
			<p style="padding: 0;margin: 0;font-size: 90%">{text}</p>
			<p style="padding: 2px 0 10px;margin:0"><a href="http://exs.lv/group/{group-id}/forum/{link}">http://exs.lv/group/{group-id}/forum/{link}</a></p>
		</li>
		<!-- END BLOCK : res-search-node-->
	</ol>
	<!-- END BLOCK : res-search-->
</div>
<!-- END BLOCK : group-search-->


<!-- START BLOCK : group-gallery-->
<div class="tabMain">
	<!-- INCLUDE BLOCK : gallery -->
</div>
<!-- END BLOCK : group-gallery-->

<!-- START BLOCK : group-settings-->
<div class="tabMain">
	<form class="form" action="" method="post">
		<fieldset>
			<legend>Tabi</legend>
			<table>
				<!-- START BLOCK : group-settings-tab-->
				<tr>
					<td>
						[<a href="?deltab={id}" class="red confirm">dzēst</a>]
					</td>
					<td style="padding: 0 4px;">
						{title}
					</td>
				</tr>
				<!-- END BLOCK : group-settings-tab-->
			</table>
			<!--<p>
			<input class="button" type="submit" name="submit-save" value="Saglabāt" />
			</p>-->
		</fieldset>
	</form>

	<!-- START BLOCK : group-settings-newtab-->
	<form class="form" action="" method="post">
		<fieldset>
			<legend>Izveidot jaunu tabu</legend>
			<p>
				<label for="tab-title">Nosaukums</label><br />
				<input class="text" type="text" id="tab-title" name="tab-title" maxlength="12" />
			</p>
			<p>
				<label class="checkbox"><input type="checkbox" name="public" value="1" checked="checked" /> sadaļa būs pieejama arī tiem, kuri nav grupas biedri</label>
			</p>
			<p>
				<input class="button" type="submit" name="submit-new" value="Izveidot" />
			</p>
		</fieldset>
	</form>
	<!-- END BLOCK : group-settings-newtab-->

	<!-- START BLOCK : group-settings-main-->
	<form class="form" action="" method="post">
		<fieldset>
			<legend>Grupas opcijas</legend>
			<p>
				<label class="checkbox"><input type="checkbox" name="main-public" value="1"{public-sel} /> sarunas var lasīt arī nereģistrētie lietotāji</label>
			</p>
			<p>
				<label class="checkbox"><input type="checkbox" name="main-auto_approve" value="1"{auto_approve-sel} /> grupā var iestāties bez apstiprinājuma</label>
			</p>
			<p>
				<input class="button" type="submit" name="submit-main" value="Saglabāt" />
			</p>
		</fieldset>
	</form>
	<!-- END BLOCK : group-settings-main-->

	<!-- START BLOCK : polls_admin-body-->

	<!-- START BLOCK : polls_admin-success-->
	<div class="form">
		<p class="success">
			Jautājums izveidots...<br />
			Atbilžu varianti izveidoti...
		</p>
	</div>
	<!-- END BLOCK : polls_admin-success-->

	<!-- START BLOCK : polls_admin-add-->
	<form class="form" action="" method="post">
		<fieldset>
			<legend>Grupas aptauja</legend>
			<p>
				<label for="new-poll-q">Aptaujas jautājums</label><br />
				<input type="text" class="title" name="new-poll-q" id="new-poll-q" />
			</p>
			<p>
				<label for="new-poll-q">1. atbilde</label><br />
				<input type="text" class="text" name="new-poll-a[]" id="new-poll-a" />
			</p>
			<p>
				<label for="new-poll-q">2. atbilde</label><br />
				<input type="text" class="text" name="new-poll-a[]" id="new-poll-a" />
			</p>
			<p>
				<label for="new-poll-q">3. atbilde</label><br />
				<input type="text" class="text" name="new-poll-a[]" id="new-poll-a" />
			</p>
			<p>
				<label for="new-poll-q">4. atbilde</label><br />
				<input type="text" class="text" name="new-poll-a[]" id="new-poll-a" />
			</p>
			<p>
				<label for="new-poll-q">5. atbilde</label><br />
				<input type="text" class="text" name="new-poll-a[]" id="new-poll-a" />
			</p>
			<p>
				<label for="new-poll-q">6. atbilde</label><br />
				<input type="text" class="text" name="new-poll-a[]" id="new-poll-a" />
			</p>
			<p>
				<label for="new-poll-q">7. atbilde</label><br />
				<input type="text" class="text" name="new-poll-a[]" id="new-poll-a" />
			</p>
			<p>
				<label for="new-poll-q">8. atbilde</label><br />
				<input type="text" class="text" name="new-poll-a[]" id="new-poll-a" />
			</p>
			<p>
				<label for="new-poll-q">9. atbilde</label><br />
				<input type="text" class="text" name="new-poll-a[]" id="new-poll-a" />
			</p>
			<p>
				<label for="new-poll-q">10. atbilde</label><br />
				<input type="text" class="text" name="new-poll-a[]" id="new-poll-a" />
			</p>
			<p>
				<input class="button" type="submit" value="Izveidot" />
			</p>
		</fieldset>
	</form>
	<!-- END BLOCK : polls_admin-add-->

	<!-- START BLOCK : polls_admin-list-->
	<table class="main-table">
		<tr>
			<th class="title">Iepriekšējie jautājumi</th>
		</tr>
		<!-- START BLOCK : polls_admin-list-node-->
		<tr>
			<td>{question}</td>
		</tr>
		<!-- END BLOCK : polls_admin-list-node-->
	</table>
	<!-- END BLOCK : polls_admin-list-->

	<!-- END BLOCK : polls_admin-body-->

</div>
<!-- END BLOCK : group-settings-->
