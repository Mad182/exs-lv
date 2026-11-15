
<ul class="tabs">
	<li><a href="/index/news"{newsactive}><span class="news">Jaunumi</span></a></li>
	<li><a href="/index/wall"{wallactive}><span class="wall">Siena</span></a></li>
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

<div class="left span7">
	<!-- START BLOCK : cindex-list-->
	<h1>Jaunumi</h1>
	<div class="index-list main">
		<!-- START BLOCK : index-news-node-->
		<article class="post">
	
			<h2 class="entry-title">
				<a href="{url}" title="{title}" rel="bookmark">{title}</a>
			</h2>
	
			<div class="entry-meta row-fluid">
				<ul class="clearfix">
					<li><img alt="" src="{avatar}" class="userav" width="16" height="16" loading="lazy" />{author}</li>
					<li><img src="{img-server}/bildes/time.png" alt="" width="17" height="17" loading="lazy">{date}</li>
					<li><img src="{img-server}/bildes/komen.png" alt="" width="17" height="15" loading="lazy"><a href="{url}#comments" title="Komentāri">{posts}</a></li>
				</ul>
			</div>
	
			<div class="entry-content">
				<!-- START BLOCK : news-image-->
				<a class="topic-image image_thumb_zoom" href="{url}" title="{title}" rel="bookmark">
					<img src="{img-server}/topics/frontpage/{image}" alt="{title}" width="600" height="240" loading="lazy" />
				</a>
				<!-- END BLOCK : news-image-->
				<!-- START BLOCK : news-av-->
				<a href="{url}" title="{title}" rel="bookmark">
					<img class="av index-av" src="{img-server}/{image}" alt="{title}" loading="lazy" />
				</a>
				<!-- END BLOCK : news-av-->
				<p>{intro}</p>
				<p class="moretag"><a href="{url}"> Lasīt tālāk</a></p>
				<div class="c"></div>
			</div>

		</article>
		<!-- END BLOCK : index-news-node-->
	</div>
	<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
	<!-- END BLOCK : cindex-list-->
</div>

<div class="right span5" id="home-middle">

	<h2 class="title"><a href="/kasnotiek"><span>Pēdējās aktivitātes</span></a></h2>
	<div id="last-action-list" class="mbox">
		{index-log}
	</div>

	<!-- START BLOCK : cindex-right-->

	<div class="fp-latest" id="latest-in-blogs">
		<h2 class="title"><a href="/blogs"><span>Jaunākais blogos</span></a></h2>
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
	</div>

	<div class="fp-latest" id="latest-games">
		<h2 class="title"><a href="/spelu-apskati"><span>Spēļu apskati</span></a></h2>
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
	</div>

	<div class="fp-latest" id="latest-movies">
		<h2 class="title"><a href="/filmas"><span>Filmu apskati</span></a></h2>
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
	</div>

	<div class="fp-latest" id="latest-music">
		<h2 class="title"><a href="/muzika"><span>Mūzikas apskati</span></a></h2>
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
	</div>

	<div class="fp-latest" id="latest-browser-games">
		<h2 class="title"><a href="/speles"><span>Tiešsaistes spēles</span></a></h2>
		<div class="mbox">
			<p style="margin:0 0 6px;">Spēlē bezmaksas pārlūka spēles un cīnies par rekordiem EXS.LV topos:</p>
			<p style="margin:0;">
				<a href="/tetris"><strong>Tetris</strong></a> &bull;
				<a href="/wordle"><strong>Wordle</strong></a> &bull;
				<a href="/snake"><strong>Čūska</strong></a> &bull;
				<a href="/2048-spele"><strong>2048</strong></a> &bull;
				<a href="/arkanoid"><strong>Arkanoid</strong></a> &bull;
				<a href="/minu-mekletajs"><strong>Mīnu meklētājs</strong></a> &bull;
				<a href="/speles">Visas spēles &raquo;</a>
			</p>
		</div>
	</div>
	<!-- END BLOCK : cindex-right-->

</div>

<!-- END BLOCK : news-->

<div class="c"></div>
</div>

