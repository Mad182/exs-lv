<h1>Pēdējās aktivitātes lapā</h1>

<style>
.user-actions-kasnotiek li {
	min-height: 70px;
	padding: 7px 4px;
	line-height: 16px;
}
.user-actions-kasnotiek .av {
	width: 64px !important;
	height: 64px !important;
	max-width: 64px !important;
	max-height: 64px !important;
	object-fit: cover;
	border-radius: 4px;
	margin: 2px 12px 6px 0 !important;
	float: left;
	display: block;
}
.user-actions-kasnotiek .event-content {
	margin: 0 0 0 78px !important;
	min-height: 64px;
	line-height: 18px;
	font-size: 13px;
}
.user-actions-kasnotiek .event-meta {
	font-size: 11px;
	color: #888;
	margin-bottom: 2px;
	display: inline-block;
}
</style>

<div class="box">
	<!-- START BLOCK : user-actions-->
	<ul class="user-actions user-actions-kasnotiek">
		<!-- START BLOCK : user-actions-node-->
		<li>
			<img class="av" src="{action-avatar}" width="64" height="64" alt="" />
			<div class="event-content">
				<span class="event-meta">{usrnick} pirms {action-date}</span><br>
				{action}
			</div>
			<div class="clear"></div>
		</li>
		<!-- END BLOCK : user-actions-node-->
	</ul>
	<!-- END BLOCK : user-actions-->
</div>

