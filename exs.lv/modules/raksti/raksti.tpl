<!-- START BLOCK : list-articles-->
<h1>{articles-title}</h1>

<!-- START BLOCK : list-node-->

<article class="post">
	
	<h2 class="entry-title">
		<a href="{url}" title="{title}" rel="bookmark">{title}</a>
		<span class="entry-cat"><a href="/{cat-strid}" title="Skatīt visau kateogirju {cat}" rel="category tag">{cat}</a></span>
	</h2>
	
	<div class="entry-meta row-fluid">
		<ul class="clearfix">
			<li><img alt="" src="{avatar}" class="userav" />{author}</li>
			<li><img src="{img-server}/bildes/time.png" alt="">{date}</li>
			<li><img src="{img-server}/bildes/komen.png" alt=""><a href="{url}#comments" title="Komentāri">{posts} komentāri</a></li>
		</ul>
	</div>
	
	<div class="entry-content">
		<!-- START BLOCK : list-avatar-->
		<a href="{url}" title="Atvērt rakstu" rel="bookmark">
			<img class="av" src="{img-server}/{image}" alt="{alt}" />
		</a>
		<!-- END BLOCK : list-avatar-->
		<p>{intro}</p>
		<p class="moretag"><a href="{url}"> Lasīt tālāk</a></p>
		<div class="c"></div>
	</div>

</article>
<!-- END BLOCK : list-node-->

<p class="pagination core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
<!-- END BLOCK : list-articles-->

