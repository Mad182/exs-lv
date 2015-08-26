<!-- START BLOCK : list-articles-->
<h1>{articles-title}</h1>

<!-- START BLOCK : list-node-->

<article class="post">
	
	<h2 class="entry-title">
		<a href="{url}" title="{title}" rel="bookmark">{title}</a>
		<span class="entry-cat"><a href="/movies" title="Skatīt visau kateogirju {cat}" rel="category tag">{cat}</a></span>
	</h2>
	
	<div class="entry-meta row-fluid">
		<ul class="clearfix">
			<li><img alt="" src="{avatar}" style="width:16px;height:16px" /><a href="/user/{author-id}" title="Apskatīt profilu" rel="author">{author}</a></li>
			<li><img src="/responsive/images/time.png" alt="">{date}</li>
			<li><img src="/responsive/images/view-bg.png" alt="">{views}</li>
			<li><img src="/responsive/images/komen.png" alt=""><a href="{node-url}#comments" title="Comment on Lectus non rutrum pulvinar urna leo dignissim lorem">{posts} komentāri</a></li>
			<li class="tagz"><img src="/responsive/images/tags-icon.png" alt=""><a href="#" rel="tag">Grid</a><br /></li>
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

