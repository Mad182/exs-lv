<!-- START BLOCK : pm-menu-->
<h1>{pm-top-title}</h1>

<ul class="tabs">
	<li class="{inbox-active}"><a title="Saņemtās vēstules" href="/pm" class="{inbox-active}"><span class="inbox">Saņemtās</span></a></li>
	<li class="{outbox-active}"><a title="Sūtītās vēstules" href="/pm/sent" class="{outbox-active}"><span class="outbox">Sūtītās</span></a></li>
	<li class="{compose-active}"><a title="Rakstīt vēstuli" href="/pm/write" class="ajax-module-mobile {compose-active}"><span class="compose">Rakstīt</span></a></li>
	<li class="{search-active}"><a title="Meklēt vēstuli" href="/pm/search" class="{search-active}"><span class="mail-search">Meklēt</span></a></li>
</ul>

<div class="c"></div>

<div class="tabMain">

	<!-- START BLOCK : pm-read-inbox-->
	<!-- START BLOCK : pm-read-from-->
	<a class="friend friend-right" href="/user/{pm-from-id}" title="{pm-from-title}">
		<img src="{avatar}" alt="" />
		{pm-from-nick}
	</a>
	<!-- END BLOCK : pm-read-from-->
	<h3>{pm-title}</h3>
	<div class="post-content">
		{pm-text}
	</div>

	<div class="c"></div>
	<p id="pm-read-footer">
		<a class="button primary ajax-module-mobile" href="/pm/write/?replyto={pm-id}" title="Rakstīt vēstuli">Atbildēt</a>
		<a id="reply-history" class="button" href="/pm/?history=true&amp;msg_id={pm-id}">Sarunas vēsture</a>
	</p>
	<div class="c"></div>
	<div id="pm-history-container"></div>
	<!-- END BLOCK : pm-read-inbox-->

	<!-- START BLOCK : pm-read-outbox-->
	<a class="friend friend-right" href="/user/{pm-to-id}" title="{pm-to-title}">
		<img src="{avatar}" alt="" />
		{pm-to-nick}
	</a>
	<h3>{pm-title}</h3>
	<div class="post-content">
		{pm-text}
	</div>

	<div class="c"></div>
	<p id="pm-read-footer">
		<a id="reply-history" class="button" href="/pm/?history=true&amp;msg_id={pm-id}">Sarunas vēsture</a>
	</p>
	<div class="c"></div>
	<div id="pm-history-container"></div>
	<!-- END BLOCK : pm-read-outbox-->

	<!-- START BLOCK : pm-read-error-->
	<p>Pieprasītā vēstule netika atrasta!</p>
	<!-- END BLOCK : pm-read-error-->

	<!-- START BLOCK : pm-list-inbox-->

	<table id="pm-table">
		<tr>
			<th class="type">&nbsp;</th>
			<th class="title">Temats</th>
			<th class="user">No kā?</th>
			<th class="pmdate">Datums</th>
		</tr>
		<!-- START BLOCK : pm-list-inbox-node-->
		<tr class="read-{pm-read}">
			<td class="{type}">&nbsp;</td>
			<td class="title"><a href="/pm/inbox/{pm-id}" title="{pm-title} - lasīt vēstuli">{pm-title}</a></td>
			<td>{from}</td>
			<td class="pmdate"><a href="/pm/inbox/{pm-id}" title="{pm-title} - lasīt vēstuli">{pm-date}</a></td>
		</tr>
		<!-- START BLOCK : pm-list-inbox-node-->

		<!-- START BLOCK : pm-list-inbox-empty-->
		<tr class="message">
			<td colspan="4">Tev nav nevienas saņemtās vēstules!</td>
		</tr>
		<!-- START BLOCK : pm-list-inbox-empty-->
	</table>
	<p class="core-pager">
		{pager-next}
		{pager-numeric}
		{pager-prev}
	</p>
	<!-- END BLOCK : pm-list-inbox-->

	<!-- START BLOCK : pm-list-outbox-->

	<table id="pm-table">
		<tr>
			<th class="type">&nbsp;</th>
			<th class="title">Temats</th>
			<th class="user">Kam?</th>
			<th class="pmdate">Datums</th>
		</tr>

		<!-- START BLOCK : pm-list-outbox-node-->
		<tr class="read-{pm-read}">
			<td class="{type}">&nbsp;</td>
			<td class="title"><a href="/pm/sent/{pm-id}" title="{pm-title} - lasīt vēstuli">{pm-title}</a></td>
			<td>{to}</td>
			<td class="pmdate"><a href="/pm/sent/{pm-id}" title="{pm-title} - lasīt vēstuli">{pm-date}</a></td>
		</tr>
		<!-- START BLOCK : pm-list-outbox-node-->

		<!-- START BLOCK : pm-list-outbox-empty-->
		<tr class="message">
			<td colspan="4">Tu pagaidām neesi nosūtījis nevienu vēstuli!</td>
		</tr>
		<!-- START BLOCK : pm-list-outbox-empty-->
	</table>
	<p class="core-pager">
		{pager-next}
		{pager-numeric}
		{pager-prev}
	</p>
	<!-- END BLOCK : pm-list-outbox-->

	<!-- START BLOCK : pm-compose-->
	<form id="compose-message" class="form" action="" method="post">
		<fieldset>
			<legend>Rakstīt jaunu vēstuli</legend>
			<input type="hidden" name="die-motherfcker-wannabes" value="{pm-check}" />
			<p id="compose-to-local">
				<label for="compose-to">Kam:</label><br />
				<select name="compose-to">
					<option value="0">&nbsp;</option>
					<!-- START BLOCK : pm-compose-option-->
					<option value="{friend-id}"{friend-sel}>{friend-nick}</option>
					<!-- END BLOCK : pm-compose-option-->
				</select>
			</p>
			<p>
				<label for="compose-title">Tēma:</label><br />
				<input type="text" name="compose-title" id="compose-title" class="text" value="{compose-title}" maxlength="64" />
			</p>
			<textarea cols="80" rows="3" style="width:100%;height:300px" name="compose-body"></textarea>
			<p>
				<input type="submit" name="submit" class="button primary" value="Nosūtīt!" />
			</p>
		</fieldset>
	</form>
	<!-- END BLOCK : pm-compose-->

	<!-- START BLOCK : pm-search-->
	<form class="form" action="" method="GET">
		<fieldset>
			<legend>Vēstuļu meklētājs</legend>
			<p>
				<input type="text" name="q" id="search-q" class="text" value="{qstr}" />
				<input type="submit" value="Meklēt" class="button primary" />
			</p>
		</fieldset>
	</form>
	<!-- START BLOCK : res-search-->
	<ol>
		<!-- START BLOCK : res-search-node-->
		<li style="border-bottom: 1px solid #ddd;padding:8px 2px">
			<p style="padding:0"><a href="{link}">{title}</a></p>
			<p style="padding:0;font-size:90%">{text}</p>
		</li>
		<!-- END BLOCK : res-search-node-->
	</ol>
	<!-- END BLOCK : res-search-->
	<!-- END BLOCK : pm-search-->

</div>
<!-- END BLOCK : pm-menu-->
