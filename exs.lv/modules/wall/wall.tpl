
<ul class="tabs">
	<li><a href="/index/news"{newsactive}><span class="news">Jaunumi</a></span></li>
	<li><a href="/index/wall"{wallactive}><span class="wall">Siena</a></span></li>
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

<div class="left span6">
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
					<li><img alt="" src="{avatar}" class="userav" />{author}</li>
					<li><img src="/responsive/images/time.png" alt="">{date}</li>
					<li><img src="/responsive/images/komen.png" alt=""><a href="{url}#comments" title="Comment on Lectus non rutrum pulvinar urna leo dignissim lorem">{posts}</a></li>
				</ul>
			</div>
	
			<div class="entry-content">
				<!-- START BLOCK : news-image-->
				<a class="topic-image image_thumb_zoom" href="{url}" title="{title}" rel="bookmark">
					<img width="75" height="75" src="{img-server}/topics/frontpage/{image}" alt="{title}" />
				</a>
				<!-- END BLOCK : news-image-->
				<!-- START BLOCK : news-av-->
				<a href="{url}" title="{title}" rel="bookmark">
					<img class="av index-av" width="75" height="75" src="{img-server}/{image}" alt="{title}" />
				</a>
				<!-- END BLOCK : news-av-->
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

<div class="right span6" id="home-middle">

	<h2 class="title"><a href="/kasnotiek"><span>Pēdējās aktivitātes</span></a></h2>
	<div id="last-action-list">
		{index-log}
	</div>
	
	<!-- START BLOCK : poll-box-->
	<h2 class="title"><a href="/aptaujas"><span>Aptauja</span></a></h2>
	<h3 class="poll-q">{poll-title}</h3>
	<!-- START BLOCK : poll-answers-->
	<ol class="poll-answers">
		<!-- START BLOCK : poll-answers-node-->
		<li>{poll-answer-question}<div><span>{poll-answer-percentage}%</span><div style="width:{poll-answer-percentage}%"></div></div></li>
		<!-- END BLOCK : poll-answers-node-->
	</ol>
	Balsojuši: {poll-totalvotes}<br />
	<a href="{ppage-id}">Komentāri</a>
	<!-- END BLOCK : poll-answers-->
	<!-- START BLOCK : poll-questions-->
	<form name="poll" method="post" action="">
		<fieldset>
			<!-- START BLOCK : poll-error-->
			<p>{poll-error}</p>
			<!-- END BLOCK : poll-error-->
			<!-- START BLOCK : poll-options-->
			<ol id="poll-questions">
				<!-- START BLOCK : poll-options-node-->
				<li><label><input type="radio" name="questions" value="{poll-options-id}" /> {poll-options-question}</label></li>
				<!-- END BLOCK : poll-options-node-->
			</ol>
			<input type="submit" name="vote" value="Balsot!" class="button primary" />
			<!-- END BLOCK : poll-options-->
		</fieldset>
	</form>
	<!-- END BLOCK : poll-questions-->
	<!-- END BLOCK : poll-box-->

	<!-- START BLOCK : cindex-right-->

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

	<h2 class="title"><a href="/speles"><span>Spēļu apskati</span></a></h2>
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
	<!-- END BLOCK : cindex-right-->

</div>

<!-- END BLOCK : news-->

<div class="c"></div>

</div>

<!-- INCLUDE BLOCK : share-block -->

