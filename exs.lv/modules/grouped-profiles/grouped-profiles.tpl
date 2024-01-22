<!-- START BLOCK : mcp-grouped-profiles-tabs -->
<ul id="prof_mgmt_tabs" class="tabs">
	<li><a href="/findby">Profilu meklētājs</a></li>
	<li><a href="/grouped-profiles" class="active">Profilu sasaiste</a></li>
</ul>
<!-- END BLOCK : mcp-grouped-profiles-tabs -->

<!-- START BLOCK : mcp-grouped-outer-start -->
<div id="prof_mgmt">
<!-- END BLOCK : mcp-grouped-outer-start -->

<!-- START BLOCK : content-info -->
<p class="note note-top">Šī sadaļa paredzēta, lai kopā saistītu tos profilus, kurus izmanto viens lapas lietotājs. Sasaistot profilus kopā un bloķējot kādu no tiem, būs iespējams norādīt, lai uzreiz bloķēti tiktu arī visi pārējie piesaistītie profili vai daļa no tiem.</p>
<!-- END BLOCK : content-info -->

<!-- START BLOCK : new-profile-form -->
<div class="prof-mgmt-forms">
	<div>
		<form method="post" action="/{category-url}/search">
			<p>Sameklē sarakstā esošu profilu:</p>
			<p>
				<input type="text" name="user_id" placeholder="Profila ID">
				<input type="submit" name="submit" class="button primary" value="Meklēt">
			</p>
		</form>
	</div>
	<div>
		<form method="post" action="/{category-url}/add-main">
			<p>Pievieno jaunu galveno profilu:</p>
			<p>
				<input type="text" name="userid" placeholder="Profila ID">
				<input type="submit" name="submit" class="button primary" value="Pievienot">
			</p>
		</form>
	</div>
	<div class="clearfix"></div>
</div>
<!-- END BLOCK : new-profile-form -->

<!-- START BLOCK : new-child-form -->
<div class="fancy-container">
	<p class="fancy-title">Profilu piesaiste</p>
	<p class="fancy-info" style="width:50%">
		Lai iegūtu profila ID, atver šī lietotāja profilu un nokopē to no adreses.<br><br>
		Piemēram, ja adrese ir <i>exs.lv/user/115</i>, tad ID ir <i>115</i>.<br><br>Laukā iespējams norādīt vairākus ID, atdalītus ar komatiem.
	</p>
	<p style="margin-top:25px">Galvenais profils:&nbsp;{main-profile}</p>
	<form method="post" action="/{category-url}/add-child/{main-id}">
		<label for="child_id">Piesaistāmo profilu ID:</label>
		<input id="child_id" style="position:relative;top:10px" type="text" name="child_ids" value="" placeholder="id1, id2, id3" autofocus>
		<input class="button primary" style="position:relative;top:20px" type="submit" name="submit" value="Pievienot">
	</form>
</div>
<!-- END BLOCK : new-child-form -->

<!-- START BLOCK : edit-description -->
<div class="fancy-container">
	<p class="fancy-title">Profilu grupas apraksts</p>
	<p style="margin-bottom:5px">Galvenais profils:&nbsp;{main-profile}</p>
	<form method="post" action="/{category-url}/edit/{main-id}">
		<label for="description">Apraksts:</label>
		<textarea id="description" class="profiles-description" name="description">{description}</textarea>
		<input class="button" type="submit" onClick="javascript:$.fancybox.close();return false;" name="submit" value="Atcelt">
		<input class="button primary" type="submit" name="submit" value="Atjaunot">
	</form>
</div>
<!-- END BLOCK : edit-description -->

<!-- START BLOCK : delete-confirmation -->
<div class="fancy-container">
	<p class="fancy-title">Profilu grupas dzēšana</p>
	<p class="fancy-info" style="width:50%">
		Šai grupai ir piesaistīti <span style="color:red">{profile-count}</span> profili.
	</p>
	<p style="margin-bottom:5px;margin-top:10px">Galvenais profils:&nbsp;{main-profile}</p>
	<p>Vai tiešām vēlies šo grupu dzēst?</p>
	<form method="post" action="/{category-url}/delete-group/{main-id}">
		<input class="button primary" type="submit" onClick="javascript:$.fancybox.close();return false;" name="submit" value="Atcelt">
		<input class="button danger" type="submit" name="submit" value="Dzēst">
	</form>
</div>
<!-- END BLOCK : delete-confirmation -->

<!-- START BLOCK : no-profiles -->
<p class="note note-empty">Nav neviena pievienota profila.</p>
<!-- END BLOCK : no-profiles -->

<!-- START BLOCK : scroll-to -->
<script>
	$(document).ready(function() {
		var aTag = $({main-id});
		aTag.next().removeClass('is-hidden'); 
		$('html, body').animate({scrollTop: aTag.offset().top}, 'slow');
	});
</script>
<!-- END BLOCK : scroll-to -->

<!-- START BLOCK : profile-list -->
<table id="profile-list" class="mod-list-table">
	<tr>
		<td>Nr.</td>
		<td>Main profils</td>
		<td>Profilu skaits</td>
		<td>Iespējas</td>
	</tr>
	<!-- START BLOCK : a-profile -->
	<tr id="profile-{user_id}" class="main-profile">
		<td>{counter}</td>
		<td>
			<a href="/user/{user_id}">{user_nick}</a>
			<a class="show-children pointer" href="javascript:void(0);">
				<img src="/bildes/fugue-icons/arrow-down.png" title="Skatīt piesaistītos profilus">
			</a>
		</td>
		<td>{profile_count}</td>
		<td style="position:relative;">
			<a class="connect-profile" href="/{category-url}/add-child/{ug_id}">
				<img src="/bildes/fugue-icons/sql-join-left.png" title="Piesaistīt profilu">
			</a>
			<a href="/user/{user_id}/block">
				<img src="/bildes/fugue-icons/auction-hammer.png" title="Skatīt bloķēšanas sadaļu">
			</a>
			<a class="edit-description" href="/{category-url}/edit/{ug_id}">
				<img src="/bildes/fugue-icons/script--plus.png" title="Labot grupas komentāru">
			</a>
			<a class="delete-group" href="/{category-url}/delete-group/{ug_id}">
				<img src="/bildes/fugue-icons/bin-full.png" title="Dzēst grupu">
			</a>
		</td>
	</tr>
	<tr class="is-hidden">
		<td colspan="4">
			<!-- START BLOCK : all-children -->
			<div class="child-block">
				<p style="float:right">Redzēts: {user_seen}, pēdējā IP: <a href="/findby?ip={user_lastip}">{user_lastip}</a></p>
				{description}
				
				<!-- START BLOCK : no-children -->
				<p style="text-align:left;">Šim profilam citu piesaistītu profilu nav.</p>
				<!-- END BLOCK : no-children -->

				<table class="child-table clearfix">
					<!-- START BLOCK : child-table-header -->
					<tr>
						<td>ID</td>
						<td>Lietotājvārds</td>
						<td>Redzēts</td>
						<td>Pēdējā IP</td>
						<td>&nbsp;</td>
					</tr>
					<!-- END BLOCK : child-table-header -->
					<!-- START BLOCK : a-child -->
					<tr>
						<td>{child_id}</td>
						<td><a href="/user/{child_id}">{child_nick}</a></td>
						<td>{child_seen}</td>
						<td><a href="/findby?ip={child_lastip}">{child_lastip}</a></td>
						<td>
							<a class="confirm" href="/{category-url}/change-main/{child_parent}">
								<img src="/bildes/fugue-icons/arrow-135-medium.png" title="Mainīt vietām ar galveno profilu">
							</a>
							<a href="/user/{child_id}/block">
								<img src="/bildes/fugue-icons/auction-hammer.png" title="Skatīt bloķēšanas sadaļu">
							</a>
							<a class="confirm" href="/{category-url}/delete-child/{child_parent}/{user_id}">
								<img src="/bildes/fugue-icons/bin-full.png" title="Atsaistīt profilu">
							</a>
						</td>
					</tr>
					<!-- END BLOCK : a-child -->
				</table>
			</div>
			<!-- END BLOCK : all-children -->
		</td>
	</tr>
	<!-- END BLOCK : a-profile -->
</table>
<!-- END BLOCK : profile-list -->

{includable-content}

<!-- START BLOCK : mcp-grouped-outer-end -->
</div>
<!-- END BLOCK : mcp-grouped-outer-end -->
