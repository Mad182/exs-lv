<div class="groups-header-bar">
	<div class="groups-title-group">
		<h1>Domubiedru grupas</h1>
		<a href="/group-create" class="groups-create-btn"><strong>+ Izveidot grupu</strong></a>
	</div>

	<div class="groups-sort-options">
		<span class="sort-label">Kārtot pēc:</span>
		<a href="/grupas" class="sort-link {sort_members_active}">Biedri</a>
		<a href="/grupas/?order=posts" class="sort-link {sort_posts_active}">Posti</a>
		<a href="/grupas/?order=abc" class="sort-link {sort_abc_active}">Nosaukums</a>
	</div>
</div>

<!-- START BLOCK : groups-cat-->
<div class="groups-category-section">
	<h2 class="groups-cat-title">{title}</h2>
	<ul class="exs-groups">
	<!-- START BLOCK : list-groups-node-->
		<li class="{member_class}">
			<a href="{link}" class="group-avatar-link">
				<img class="av group-avatar" src="{avatar}" alt="{title}" />
			</a>
			<div class="group-details">
				<h3 class="group-title">
					<a href="{link}">{title}</a>
					{add}
				</h3>
				<div class="group-meta">
					<span class="meta-item"><strong>Biedri:</strong> {members}</span>
					<span class="meta-item"><strong>Posti:</strong> {posts}</span>
					<span class="meta-item"><strong>Admins:</strong> {admin}</span>
				</div>
			</div>
		</li>
	<!-- END BLOCK : list-groups-node-->
	</ul>
	<div class="c"></div>
</div>
<!-- END BLOCK : groups-cat-->

