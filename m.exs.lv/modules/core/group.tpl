<!-- START BLOCK : group-options-->
<ul id="page-options"><li class="option-edit"><a href="/?group={group-id}&amp;act=edit">labot lapu</a></ul>
<!-- END BLOCK : group-options-->

<!-- START BLOCK : group-menu-->
<h2>{group-title}</h2>
<ul class="tabs">
	<li><a href="/group/{group-id}" class="{active-tab-info}"><span class="profile">Informācija</span></a></li>
	<li><a href="/group/{group-id}/members" class="{active-tab-members}"><span class="friends">Biedri</span></a></li>
	<li><a href="/group/{group-id}/forum" class="{active-tab-community}"><span class="comments">Sarunas</span></a></li>
</ul>
<!-- END BLOCK : group-menu-->

<!-- START BLOCK : group-info-->
<div class="tabMain">
	<div>
	{group-text}
	</div>
	<div>

		<h3>Rīki</h3>

<!-- START BLOCK : group-info-apply-->
		 <p><a class="l-gmember" href="/?group={group-id}&amp;act=apply">Pieteikties</a></p>
<!-- END BLOCK : group-info-apply-->
<!-- START BLOCK : group-info-cancel-->
		<p><a class="l-gmember" href="/?group={group-id}&amp;act=cancel">Dzēst pieteikumu</a></p>
<!-- END BLOCK : group-info-cancel-->
<!-- START BLOCK : group-info-quit-->
		<p><a class="l-gmember" href="/?group={group-id}&amp;act=cancel">Pamest grupu</a></p>
<!-- END BLOCK : group-info-quit--> 

		<h3>Grupas statistika</h3>
		<p style="font-size: 90%;">
			Biedri: {group-members}<br />
			Posti: {group-posts}<br />
			Admins: {group-admin}
		</p>
	</div>
	<div class="c"></div>
</div>
<!-- START BLOCK : group-info-->

<!-- START BLOCK : group-edit-->
<div class="tabMain">


<form action="{page-url}" class="form" method="post">
<fieldset>
<p>
	<label for="edit-group-title">Nosaukums:</label><br />
	<input type="text" name="edit-group-title" id="edit-group-title" class="text" value="{group-title}" maxlength="64" />
</p>
<p>
	<label for="edit-group-text">Teksts:</label><br />
	<textarea name="edit-group-text" id="edit-group-text" cols="94" rows="40" style="width: 60%; height: 500px;">{group-text}</textarea>
</p>
<!-- START BLOCK : edit-group-av-->	
<p>
	<a class="thb-image"><img src="http://exs.lv{img}" alt="Avatars" /></a>
</p>
<!-- END BLOCK : edit-group-av-->
<p>
	<label for="edit-avatar">Grupas avatars:</label><br />
	<input type="file" class="long" name="edit-avatar" id="edit-avatar" />
</p>
<p>
	<input class="button" type="submit" name="submit" value="Saglabāt izmaiņas" class="submit" />
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
<a class="image" href="/?u={pending-uid}"><img src="http://exs.lv/dati/bildes/useravatar/{pending-avatar}" alt="" /></a>
<h3><a href="/?u={pending-uid}">{pending-nick}</a> <span>{pending-date}</span></h3>
<a href="/?group={group-id}&amp;act=confirm&amp;confirm={pending-id}">Apstiprināt</a>
<div class="c"></div>
</li>
<!-- END BLOCK : pending-node-->
</ul>
<div class="c"></div>
<!-- END BLOCK : pending-->

<!-- START BLOCK : members-->
<strong>Visi biedri</strong>
<ul id="friend-list">
<!-- START BLOCK : members-node-->
<li class="{member-class}"><a class="profile-link" href="/?u={member-id}"><img src="http://exs.lv/dati/bildes/useravatar/{member-avatar}" /><br />{member-nick}</a>
<!-- START BLOCK : member-delete-->
<a class="delete" title="Dzēst dalībnieku no grupas" href="/?group={group-id}&amp;act=drop&amp;drop={member-id}" onclick="return confirm_delete();"><img src="http://exs.lv/bildes/x.png" alt="x" title="Dzēst dalībnieku no grupas" /></a>
<!-- END BLOCK : member-delete-->
<!-- START BLOCK : member-moderator-->
<a class="moderator" href="/?group={group-id}&amp;act=setmod&amp;uid={member-id}"><img src="http://exs.lv/bildes/icons/user_add.png" alt="mod" title="Uzlikt par moderatoru" /></a>
<!-- END BLOCK : member-moderator--> 
<!-- START BLOCK : member-unmoderator-->
<a class="unmoderator" href="/?group={group-id}&amp;act=unsetmod&amp;uid={member-id}"><img src="http://exs.lv/bildes/icons/user_delete.png" alt="unmod" title="Noņemt moderatora statusu" /></a>
<!-- END BLOCK : member-unmoderator-->
</li>
<!-- END BLOCK : members-node-->
</ul>
<div class="c"></div>
<!-- END BLOCK : members-->

</div>
<!-- END BLOCK : group-members-->


<!-- START BLOCK : group-community-->

<div class="tabMain">

<!-- START BLOCK : noguestacc-->
<div class="form"><p class="notice">Grupas sarunās var piedalīties tikai apstiprinātie biedri!</p></div>
<!-- END BLOCK : noguestacc-->

<!-- START BLOCK : user-miniblog-->
	<!-- INCLUDE BLOCK : conversation -->
<!-- END BLOCK : user-miniblog-->

</div>
<!-- END BLOCK : group-community-->
