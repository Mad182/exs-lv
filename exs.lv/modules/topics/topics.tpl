<!-- START BLOCK : user-usertopics-private-->
<div class="tabMain">
	<p class="note">Šis profils ir privāts. Ielogojies lai apskatītu.</p>
</div>
<!-- END BLOCK : user-usertopics-private-->
<!-- START BLOCK : user-usertopics-->
<div class="tabMain">
	<!-- START BLOCK : empty-usertopics-->
	<p class="note">Šis lietotājs vēl nav izveidojis nevienu rakstu.</p>
	<!-- END BLOCK : empty-usertopics-->
	<!-- START BLOCK : user-usertopics-list-->
	<!-- START BLOCK : user-usertopics-pager-top-->
	<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
	<!-- END BLOCK : user-usertopics-pager-top-->

	<ul class="user-topics-list">
		<!-- START BLOCK : user-usertopics-node-->
		<li class="user-topic-item">
			<div class="user-topic-main">
				<div class="user-topic-title-wrap">
					<!-- START BLOCK : user-usertopics-closed-->
					<span class="user-topic-badge badge-closed" title="Slēgts">
						<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
						Slēgts
					</span>
					<!-- END BLOCK : user-usertopics-closed-->
					<a class="user-topic-title" href="{node-url}">{articles-node-title}</a>
				</div>
				<div class="user-topic-meta">
					<!-- START BLOCK : user-usertopics-cat-->
					<span class="user-topic-cat">{category-title}</span>
					<span class="user-topic-dot">&bull;</span>
					<!-- END BLOCK : user-usertopics-cat-->
					<span class="user-topic-date" title="{articles-node-datetime}">
						<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
						{articles-node-date}
					</span>
				</div>
			</div>
			<div class="user-topic-stats">
				<!-- START BLOCK : user-usertopics-views-->
				<span class="user-topic-stat stat-views" title="Skatījumi">
					<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
					{articles-node-views}
				</span>
				<!-- END BLOCK : user-usertopics-views-->
				<span class="user-topic-stat stat-replies {replies-class}" title="Atbildes">
					<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
					{articles-node-posts}
				</span>
			</div>
		</li>
		<!-- END BLOCK : user-usertopics-node-->
	</ul>

	<!-- START BLOCK : user-usertopics-pager-bottom-->
	<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
	<!-- END BLOCK : user-usertopics-pager-bottom-->
	<!-- END BLOCK : user-usertopics-list-->
</div>
<!-- END BLOCK : user-usertopics-->
