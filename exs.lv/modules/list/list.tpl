<!-- START BLOCK : listsubcats-->
{forum-subcats-html}
<!-- END BLOCK : listsubcats-->

<!-- START BLOCK : list-articles-->
<h1>{title}</h1>


<!-- START BLOCK : list-->

<article class="post">

	<h2 class="entry-title">
		<a href="{url}" title="{title}" rel="bookmark">{title}</a>
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
			<img class="av" src="{img-server}{image}" alt="{alt}" />
		</a>
		<!-- END BLOCK : list-avatar-->
		<p>{intro}</p>
		<p class="moretag"><a href="{url}"> Lasīt tālāk</a></p>
		<div class="c"></div>
	</div>

</article>
<!-- END BLOCK : list-->

<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
<!-- END BLOCK : list-articles-->
<!-- START BLOCK : list-articles-short-->
<h1>{title}</h1>
<ul>
	<!-- START BLOCK : list-articles-short-node-->
	<li><a href="{url}">{title}</a> no {author}</li>
	<!-- END BLOCK : list-articles-short-node-->
</ul>
<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
<!-- END BLOCK : list-articles-short-->
<!-- START BLOCK : list-forum-->

	<!-- START BLOCK : forum-new-->
	<a class="add-topic button primary" href="/{strid}/?cat={catid}#new">+ izveidot tēmu</a>
	<!-- END BLOCK : forum-new-->

<h1>{title}</h1>

{forum-topics-html}
<!-- END BLOCK : list-forum-->
<!-- START BLOCK : error-catempty-->
<h1>{title}</h1>
<div id="full-story">
	<p>Hmmm... izskatās, ka šajā lapas sadaļā pagaidām nav neviena raksta :(</p>
</div>
<!-- END BLOCK : error-catempty-->

