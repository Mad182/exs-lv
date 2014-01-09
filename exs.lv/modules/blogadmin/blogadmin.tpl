<!-- START BLOCK : blogadmin-setup-->
<h1>Tavs blogs</h1>
<p>Tev šobrīd nav exs.lv bloga.</p>
<h4>Kā iegūt blogu?</h4>
<ol>
	<li>Esi aktīvs lapā un gaidi - blogs tiek piešķirts automātiski katram, kurš sasniedz 200. karmas līmeni.</li>
	<li>Blogu var "nopirkt" par 5 exs kredītiem.</li>
</ol>
<p>Tev šobrīd ir <strong>{credit}</strong> exs punkti.</p>
{pay}

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

<!-- END BLOCK : blogadmin-setup-->
<!-- START BLOCK : blogadmin-body-->

<h1>Tavs blogs</h1>

<ul class="tabs">
	<li><a href="/{category-url}" class="{new-active}">Jauns raksts</a></li>
	<li><a href="/{category-url}/?act=edit" class="{edit-active}"><span class="pages">Labot</span></a></li>
	<li><a href="/{category-url}/?act=links" class="{links-active}"><span class="bookmarks">Saites</span></a></li>
</ul>

<div class="tabMain">
	<!-- START BLOCK : user-usertopics-list-->
	<ul>
		<!-- START BLOCK : user-usertopics-node-->
		<li><a href="/read/{strid}">{title}</a> [<a href="/read/{strid}?mode=edit">labot</a>]</li>
		<!-- END BLOCK : user-usertopics-node-->
	</ul>
	<!-- START BLOCK : user-usertopics-list-->
	<!-- START BLOCK : blogadmin-new-->
	<form action="{page-url}" id="blog-edit" class="form" method="post" enctype="multipart/form-data">
		<fieldset>
			<legend>Jauns raksts</legend>
			<input type="hidden" name="token" value="{blog-check}" />
			<p>
				<label for="new-topic-title">Nosaukums:</label><br />
				<input type="text" name="new-topic-title" id="new-topic-title" class="new-title text" maxlength="64" />
			</p>
			<label for="new-topic-body">Teksts:</label><br />
			<textarea name="new-topic-body" id="new-topic-body" cols="94" rows="40" style="width: 100%; height: 400px;"></textarea>
			<p>
				<label for="new-avatar">Raksta avatars:</label><br />
				<input type="file" class="long" name="new-avatar" id="new-avatar" />
			</p>
			<p>
				<input type="submit" name="submit" value="Pievienot" class="button primary" />
			</p>
		</fieldset>
	</form>
	<!-- END BLOCK : blogadmin-new-->
	<!-- START BLOCK : blogadmin-links-->
	<!-- START BLOCK : user-sidelinks-list-->
	<table class="main-table">
		<tr>
			<th>Nosaukums</th>
			<th>Saite</th>
			<th>Labot</th>
			<th>Dzēst</th>
		</tr>
		<!-- START BLOCK : user-sidelinks-node-->
		<tr>
			<td>{sidelink-title}</td>
			<td>{sidelink-url}</td>
			<td>[<a href="/{category-url}/?act=links&amp;edit={sidelink-id}">labot</a>]</td>
			<td>[<a class="sarkans" href="/{category-url}/?act=links&amp;delete={sidelink-id}">dzēst</a>]</td>
		</tr>
		<!-- END BLOCK : user-sidelinks-node-->
	</table>
	<!-- START BLOCK : user-sidelinks-list-->
	<!-- START BLOCK : sidelinks-new-->
	<form action="{page-url}" id="sidelinks-new" class="form" method="post" enctype="multipart/form-data">
		<fieldset>
			<legend>Pievienot jaunu saiti</legend>
			<!-- START BLOCK : error-nolink-->
			<p class="error">Saiti jānorāda oblogāti!</p>
			<!-- END BLOCK : error-nolink-->
			<p>
				<label for="new-sidelink-title">Nosaukums:</label><br />
				<input type="text" name="new-sidelink-title" id="new-sidelink-title" class="text" maxlength="64" />
			</p>
			<p>
				<label for="new-sidelink-url">Saite:</label><br />
				<input type="text" value="http://" name="new-sidelink-url" id="new-sidelink-url" class="text" maxlength="256" />
			</p>
			<p>
				<input type="submit" name="submit" value="Pievienot" class="submit" />
			</p>
		</fieldset>
	</form>
	<!-- END BLOCK : sidelinks-new-->
	<!-- START BLOCK : sidelinks-edit-->
	<form action="{page-url}" id="sidelinks-new" class="form" method="post" enctype="multipart/form-data">
		<fieldset>
			<legend>Labot saiti</legend>
			<!-- START BLOCK : error-nolink2-->
			<p class="error">Saiti jānorāda oblogāti!</p>
			<!-- END BLOCK : error-nolink2-->
			<p>
				<label for="edit-sidelink-title">Nosaukums:</label><br />
				<input type="text" name="edit-sidelink-title" id="edit-sidelink-title" value="{title}" class="text" maxlength="64" />
			</p>
			<p>
				<label for="edit-sidelink-url">Saite:</label><br />
				<input type="text" value="{url}" name="edit-sidelink-url" id="edit-sidelink-url" class="text" maxlength="256" />
			</p>
			<p>
				<input type="submit" name="submit" value="Pievienot" class="submit" />
			</p>
			<p>
				<a href="/{category-url}/?act=links">Atgriezties sarakstā</a>
			</p>
		</fieldset>
	</form>
	<!-- END BLOCK : sidelinks-edit-->
	<!-- START BLOCK : error-noedit-->
	<p class="error">Norādīts nepariezs link_id</p>
	<!-- END BLOCK : error-noedit-->
	<!-- END BLOCK : blogadmin-links-->
</div>
<!-- END BLOCK : blogadmin-body-->
