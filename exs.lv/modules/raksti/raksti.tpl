<!-- START BLOCK : list-articles-->
<h1>{articles-title}</h1>
<ul id="mainlist">
	<!-- START BLOCK : list-articles-node-->
	<li>
		<h3>{cat}: <a href="{node-url}">{articles-node-title}</a></h3>
		<ul class="article-info">
			<li class="date">{articles-node-date}</li>
			<li class="comments"><a href="{node-url}#comments">{articles-node-posts} komentāri</a></li>
			<li class="profile"><a href="{aurl}">{articles-node-author}</a></li>
			<li class="views">skatīts {articles-node-views}x</li>
		</ul>
		<div class="c"></div>
		<!-- START BLOCK : list-articles-node-avatar-->
		<img class="av" src="//img.exs.lv/{node-avatar-image}" alt="{node-avatar-alt}" />
		<!-- END BLOCK : list-articles-node-avatar-->
		<div style="padding: 5px 0 10px">{articles-node-intro} <a href="{node-url}" class="read-more">Lasīt&nbsp;tālāk&nbsp;&raquo;</a></div>
		<div class="c"></div>
	</li>
	<!-- END BLOCK : list-articles-node-->
</ul>
<div class="c"></div>
<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
<!-- END BLOCK : list-articles-->

