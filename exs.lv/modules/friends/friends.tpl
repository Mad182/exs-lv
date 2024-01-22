<!-- START BLOCK : user-friends-->
<div class="tabMain">
	<!-- START BLOCK : user-friend-pending-->
	<strong>Draudzības uzaicinājumi</strong>
	<ul id="friends-pending">
		<!-- START BLOCK : user-friend-pending-node-->
		<li>
			<a class="image" href="/user/{friend-id}" title="{friend-title}"><img src="{friend-avatar}" alt="" /></a>
			<h3><a href="/user/{friend-id}" title="{friend-title}">{friend-nick}</a> <span>{friend-date}</span></h3>
			<a href="?confirm={friend-id}&amp;token={token}">Apstiprināt</a> / <a href="?deny={friendship-id}&amp;token={token}">Noraidīt</a>
			<div class="c"></div>
		</li>
		<!-- END BLOCK : user-friend-pending-node-->
	</ul>
	<div class="c"></div>
	<!-- END BLOCK : user-friend-pending-->

	<!-- START BLOCK : user-friend-list-->
	<ul id="friend-list">
		<!-- START BLOCK : user-friend-node-->
		<li><a class="profile-link" href="/user/{friend-id}"><img src="{friend-avatar}" alt="{friend-title}" /><br>{friend-nick}</a>
			<!-- START BLOCK : user-friend-delete-->
			<a class="delete confirm" title="Pārtraukt draudzību" href="?deny={friendship-id}&amp;token={token}"><img src="{static-server}/bildes/x.png" alt="x" title="Pārtraukt draudzību" /></a>
			<!-- END BLOCK : user-friend-delete-->
		</li>
		<!-- END BLOCK : user-friend-node-->
	</ul>
	<div class="c"></div>
	<!-- END BLOCK : user-friend-list-->
</div>
<!-- END BLOCK : user-friends-->

