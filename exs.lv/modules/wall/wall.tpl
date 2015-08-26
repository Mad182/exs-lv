<h1>Jaunākais portālā</h1>

<ul class="tabs">
	<li><a href="/index/news"{newsactive}>Jaunumi</a></li>
	<li><a href="/index/wall"{wallactive}>Siena</a></li>
</ul>

<div class="tabMain">

<!-- START BLOCK : wall-->

<div id="wall">
	<ul id="wall-posts">
		<!-- START BLOCK : wall-node-->
		<li class="mbox">
			<a href="{url}">
				<span class="time-ago">{time}</span>
				<img class="av" src="{avatar}" alt="" />
				<div class="post-wrapper">
					<div class="post-info">
						<span class="author">{author} {where}</span>
					</div>
					<div class="post-content">{title}&nbsp;[{posts}]</div>
					<!-- START BLOCK : wall-lastpost-->
					<div class="last-post">
						<img src="{av}" alt="" class="av" />
						<div class="post-info"><span class="lastpost-author">{user}</span></div>
						<div class="lastpost-text">{txt}</div>
					</div>
					<!-- END BLOCK : wall-lastpost-->
				</div>
			</a>
			<div class="c"></div>
		</li>
		<!-- END BLOCK : wall-node-->
	</ul>
</div>

<!-- END BLOCK : wall-->

<!-- START BLOCK : news-->

<div class="half-left" style="width: 52%;text-align:left;padding:0 3px;">
	<!-- START BLOCK : cindex-list-->
	<h1>Jaunumi</h1>
	<ul class="index-list main">
		<!-- START BLOCK : index-news-node-->
		<article class="post">
	
			<h2 class="entry-title">
				<a href="{url}" title="{title}" rel="bookmark">{title}</a>
			</h2>
	
			<div class="entry-meta row-fluid">
				<ul class="clearfix">
					<li><img alt="" src="{avatar}" style="width:16px;height:16px" /><a href="/user/{author-id}" title="Apskatīt profilu" rel="author">{author}</a></li>
					<li><img src="/responsive/images/time.png" alt="">{date}</li>
					<li><img src="/responsive/images/komen.png" alt=""><a href="{node-url}#comments" title="Comment on Lectus non rutrum pulvinar urna leo dignissim lorem">{posts}</a></li>
				</ul>
			</div>
	
			<div class="entry-content">
				<a href="{url}" title="Permalink to Lectus non rutrum pulvinar urna leo dignissim lorem" rel="bookmark">
					<img class="av index-av" width="75" height="75" src="{img-server}/{avatar}" alt="{title}" />
				</a>
				<p>{intro}</p>
				<p class="moretag"><a href="{url}"> Lasīt tālāk</a></p>
				<div class="c"></div>
			</div>

		</article>
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

<!-- END BLOCK : news-->

<div class="c"></div>

</div>

<!-- INCLUDE BLOCK : share-block -->

