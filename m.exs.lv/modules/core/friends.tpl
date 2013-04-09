<!-- START BLOCK : user-friends-->
<div class="tabMain">
<!-- START BLOCK : user-friend-pending-->
<strong>Draudzības uzaicinājumi</strong>
<ul id="friends-pending">
<!-- START BLOCK : user-friend-pending-node-->
<li>
<a class="image" href="/?u={friend-id}" title="{friend-title}"><img src="/dati/bildes/useravatar/{friend-avatar}" alt="" /></a>
<h3><a href="/?u={friend-id}" title="{friend-title}">{friend-nick}</a> <span>{friend-date}</span></h3>
<a href="/?f={user-id}&amp;confirm={friend-id}">Apstiprināt</a> / <a href="/?f={user-id}&amp;deny={friendship-id}">Noraidīt</a>
<div class="c"></div>
</li>
<!-- END BLOCK : user-friend-pending-node-->
</ul>
<div class="c"></div>
<!-- END BLOCK : user-friend-pending-->

<strong>Visi draugi</strong>
<ul id="friend-list">
<!-- START BLOCK : user-friend-node-->
<li><a class="profile-link" href="?u={friend-id}"><img src="/dati/bildes/useravatar/{friend-avatar}" alt="{friend-title}" /><br />{friend-nick}</a>
<!-- START BLOCK : user-friend-delete-->
<a class="delete" title="Pārtraukt draudzību" href="/?f={user-id}&amp;deny={friendship-id}" onclick="return confirm_delete();"><img src="/bildes/x.png" alt="x" title="Pārtraukt draudzību" /></a>
<!-- END BLOCK : user-friend-delete-->
</li>
<!-- END BLOCK : user-friend-node-->
</ul>
<div class="c"></div>
</div>
<!-- END BLOCK : user-friends-->