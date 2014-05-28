<!-- START BLOCK : list-articles-->
<h1>{title}</h1>

<ul id="mainlist">
	<!-- START BLOCK : list-->
	<li>
		<h2><a href="{node-url}">{title-prefix}{title}{title-lv}</a></h2>
		<ul class="article-info">
			<li class="date">{date}</li>
			<li class="comments"><a href="{node-url}#comments">{posts} komentāri</a></li>
			<li class="profile user-level-{level} user-gender-{gender}"><a href="/user/{author-id}">{author}</a></li>
		</ul>
		<div class="c"></div>
		<!-- START BLOCK : list-avatar-->
		<img class="av" src="http://img.exs.lv{image}" alt="{alt}" />
		<!-- END BLOCK : list-avatar-->
		<p style="font-size:90%;padding: 12px 20px;margin:0;overflow: hidden;">
			{year}
			{genres}
			{runtime}
		</p>
		<p style="text-align:left;">{intro} <a href="{node-url}" class="read-more">Lasīt&nbsp;tālāk&nbsp;&raquo;</a></p>
		<div class="c"></div>
	</li>
	<!-- END BLOCK : list-->
</ul>
<div class="c"></div>
<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
<!-- END BLOCK : list-articles-->


<!-- START BLOCK : list-search-->
<h1>{title}</h1>

<ul id="movie-list">
	<!-- START BLOCK : movie-->
	<li>
		<div><a href="{node-url}"><img src="http://img.exs.lv{image}" alt="{alt}" /></a></div>
		<a href="{node-url}"><strong>{title}</strong></a>
	</li>
	<!-- END BLOCK : movie-->
</ul>
<div class="c"></div>
<!-- END BLOCK : list-search-->

