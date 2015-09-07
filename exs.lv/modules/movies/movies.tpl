<!-- START BLOCK : list-articles-->
<h1>{title}</h1>

<!-- START BLOCK : list-->
<article class="post">
	
	<h2 class="entry-title">
		<a href="{node-url}" title="{title}" rel="bookmark">{title-prefix}{title}{title-lv}</a>
		<span class="entry-cat"><a href="/movies" title="Skatīt visas filmas" rel="category tag">Filmas</a></span>
	</h2>
	
	<div class="entry-meta row-fluid">
		<ul class="clearfix">
			<li><img alt="" src="{avatar}" class="userav" /><a href="/user/{author-id}" title="Apskatīt profilu" rel="author">{author}</a></li>
			<li><img src="//exs.lv/responsive/images/time.png" alt="">{date}</li>
			<li><img src="//exs.lv/responsive/images/komen.png" alt=""><a href="{node-url}#comments" title="Komentāri">{posts} komentāri</a></li>
		</ul>
	</div>
	
	<div class="entry-content">
		<!-- START BLOCK : list-avatar-->
		<a href="#" title="Permalink to Lectus non rutrum pulvinar urna leo dignissim lorem" rel="bookmark">
			<img class="av" src="{img-server}{image}" alt="{alt}" />
		</a>
		<!-- END BLOCK : list-avatar-->
		<p style="font-size:90%;padding: 12px 20px;margin:0;overflow: hidden">
			{year}
			{genres}
			{runtime}
		</p>
		<p>{intro}</p>
		<p class="moretag"><a href="{node-url}"> Lasīt tālāk</a></p>
	</div>

</article>
<!-- END BLOCK : list-->

<p class="pagination core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
<!-- END BLOCK : list-articles-->


<!-- START BLOCK : list-search-->
<h1>{title}</h1>

<ul id="movie-list">
	<!-- START BLOCK : movie-->
	<li>
		<div><a href="{node-url}"><img src="//img.exs.lv{image}" alt="{alt}" /></a></div>
		<a href="{node-url}"><strong>{title}</strong></a>
	</li>
	<!-- END BLOCK : movie-->
</ul>
<div class="c"></div>
<!-- END BLOCK : list-search-->
