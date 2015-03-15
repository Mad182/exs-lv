<!-- START BLOCK : listsubcats-->
<table id="forum">
	<tr>
		<th class="first" colspan="4">
			Apakšsadaļas
		</th>
	</tr>
	<!-- START BLOCK : listsubcats-node-->
	<tr><td class="forum-avatar"><a href="/{textid}"><img width="48" height="48" src="/{icon}" alt="" /></a></td><td><h3><a href="/{textid}">{title}</a></h3><p>{content}{addlink}{editlink}{uplink}{downlink}</p>
			<!-- START BLOCK : subcats-->
			<ul class="subcat-list">
				<!-- START BLOCK : subcats-node-->
				<li><a href="/{textid}">{title}</a></li>
				<!-- END BLOCK : subcats-node-->
			</ul>
			<!-- END BLOCK : subcats-->

		</td><td class="stat">{topics}&nbsp;{txt-topics}<br />{posts}&nbsp;{txt-posts}</td><td class="last">{topic}<br />{date}<br />no: {author}</td></tr>
	<!-- END BLOCK : listsubcats-node-->
</table>
<!-- END BLOCK : listsubcats-->

<!-- START BLOCK : list-articles-->
<h1>{title}</h1>

<ul id="mainlist">
	<!-- START BLOCK : list-->
	<li>
		<h2><a href="{node-url}">{title}</a></h2>
		<ul class="article-info">
			<li class="date">{date}</li>
			<li class="comments"><a href="{node-url}#comments">{posts} komentāri</a></li>
			<li class="profile user-level-{level} user-gender-{gender}">{author}</li>
		</ul>
		<div class="c"></div>
		<!-- START BLOCK : list-avatar-->
		<img class="av" src="{img-server}{node-avatar-image}" alt="{node-avatar-alt}" />
		<!-- END BLOCK : list-avatar-->
		<p>{intro} <a href="{node-url}" class="read-more">Lasīt&nbsp;tālāk&nbsp;&raquo;</a></p>
		<div class="c"></div>
	</li>
	<!-- END BLOCK : list-->
</ul>
<div class="c"></div>
<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
<!-- END BLOCK : list-articles-->
<!-- START BLOCK : list-articles-short-->
<h1>{title}</h1>
<ul>
	<!-- START BLOCK : list-articles-short-node-->
	<li><a href="{node-url}">{title}</a> no {author}</li>
	<!-- END BLOCK : list-articles-short-node-->
</ul>
<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
<!-- END BLOCK : list-articles-short-->
<!-- START BLOCK : list-forum-->

	<!-- START BLOCK : forum-new-->
	<a class="add-topic button primary" href="/{strid}/?cat={catid}#new">+ izveidot tēmu</a>
	<!-- END BLOCK : forum-new-->

<h1>{title}</h1>

<table id="forum">
	<tr>
		<th colspan="2" class="first">Tēmas</th>
		<th>Atbildes</th>
		<th class="last">Datums</th>
	</tr>
	<!-- START BLOCK : list-forum-node-->
	<tr>
		<td><img width="19" height="18" src="//img.exs.lv/bildes/{timg}" alt="" /></td>
		<td><h3><a href="{node-url}">{title}</a></h3></td>
		<td class="center">{posts}</td>
		<td class="last">{date}<br />no:&nbsp;{author}</td>
	</tr>
	<!-- END BLOCK : list-forum-node-->
</table>
<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
<!-- END BLOCK : list-forum-->
<!-- START BLOCK : error-catempty-->
<h1>{title}</h1>
<div id="full-story">
	<p>Hmmm... izskatās, ka šajā lapas sadaļā pagaidām nav neviena raksta :(</p>
</div>
<!-- END BLOCK : error-catempty-->

