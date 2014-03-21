{ad-468}

<div class="half-left" style="width: 52%;text-align:left;padding:0 3px;">
	<!-- START BLOCK : cindex-list-->
	<h1>Jaunumi</h1>
	<ul class="index-list main">
		<!-- START BLOCK : index-news-node-->
		<li><h3><a href="{node-url}">{title}</a></h3>
			<ul class="article-info"><li class="date">{date}</li><li class="comments"><a href="{node-url}#comments">{posts}x</a></li><li class="profile user-level-{level}"><a href="{aurl}">{author}</a></li></ul>
			<div class="c"></div>
			<a href="{node-url}"><img class="av index-av" width="75" height="75" src="http://img.exs.lv/{avatar}" alt="{title}" /></a>
			<p>{intro}</p>
			<div class="c"></div>
		</li>
		<!-- END BLOCK : index-news-node-->
	</ul>
	<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
	<!-- END BLOCK : cindex-list-->

</div>

<div class="half-right" style="width: 41%;text-align:left;padding:0 3px;">

	<h2>Pēdējās aktivitātes</h2>
	<div id="last-action-list">
		{index-log}
	</div>

	<!-- START BLOCK : cindex-right-->
	<h2>Jaunākais blogos</h2>
	<ul class="index-list secondary">
		<!-- START BLOCK : index-blogs-node-->
		<li>
			{av}
			<h3><a href="{node-url}">{title}</a></h3>
			<p>{intro}</p>
			<div class="c"></div>
		</li>
		<!-- END BLOCK : index-blogs-node-->
	</ul>
	<h2>Spēļu apskati</h2>
	<ul class="index-list secondary">
		<!-- START BLOCK : index-games-node-->
		<li>
			{av}
			<h3><a href="{node-url}">{title}</a></h3>
			<p>{intro}</p>
			<div class="c"></div>
		</li>
		<!-- END BLOCK : index-games-node-->
	</ul>
	<h2>Filmu apskati</h2>
	<ul class="index-list secondary">
		<!-- START BLOCK : index-movies-node-->
		<li>
			{av}
			<h3><a href="{node-url}">{title}</a></h3>
			<p>{intro}</p>
			<div class="c"></div>
		</li>
		<!-- END BLOCK : index-movies-node-->
	</ul>
	<h2>Mūzikas apskati</h2>
	<ul class="index-list secondary">
		<!-- START BLOCK : index-music-node-->
		<li>
			{av}
			<h3><a href="{node-url}">{title}</a></h3>
			<p>{intro}</p>
			<div class="c"></div>
		</li>
		<!-- END BLOCK : index-music-node-->
	</ul>
	<!-- END BLOCK : cindex-right-->

</div>
