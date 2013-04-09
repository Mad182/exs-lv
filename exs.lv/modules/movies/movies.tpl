<!-- START BLOCK : list-articles-->
<h1><a class="rss" title="Seko līdzi jaunumiem šajā lapas sadaļā, izmantojot RSS!" href="/rss/{strid}" rel="feed">rss</a> {title}</h1>

<ul id="mainlist">
	<!-- START BLOCK : list-->
	<li>
	<h2><a href="{node-url}">{title}{title-lv}</a></h2>
	<ul class="article-info">
		<li class="date">{date}</li>
		<li class="comments"><a href="{node-url}#comments">{posts} komentāri</a></li>
		<li class="profile user-level-{level} user-gender-{gender}"><a href="/user/{author-id}">{author}</a></li>
	</ul>
	<div class="c"></div>
	<!-- START BLOCK : list-avatar-->
	<img class="av" src="http://img.exs.lv{node-avatar-image}" alt="{node-avatar-alt}" />
	<!-- END BLOCK : list-avatar-->
	{year}
	{genres}
	<p>{intro} <a href="{node-url}" class="read-more">Lasīt&nbsp;tālāk&nbsp;&raquo;</a></p>
	<div class="c"></div>
	</li>
	<!-- END BLOCK : list-->
</ul>
<div class="c"></div>
<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
<!-- END BLOCK : list-articles-->


<!-- START BLOCK : list-search-->
<h1><a class="rss" title="Seko līdzi jaunumiem šajā lapas sadaļā, izmantojot RSS!" href="/rss/{strid}" rel="feed">rss</a> {title}</h1>

<ul id="movie-list" style="list-style:none;padding:10px 0;margin:0">
	<!-- START BLOCK : movie-->
	<li style="float:left;width:172px;height:256px;text-align:center;pading: 0;margin:0;background:transparent;">
		<div><a href="{node-url}"><img src="http://img.exs.lv{node-avatar-image}" alt="{node-avatar-alt}" /></a></div>
		<a href="{node-url}"><strong>{title}</strong></a>	
	</li>
	<!-- END BLOCK : movie-->
</ul>
<div class="c"></div>
<!-- END BLOCK : list-search-->

