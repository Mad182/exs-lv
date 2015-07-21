<h1>Jaunākais portālā</h1>

<div style="width:468px;height:60px;margin:8px auto">
	<script type="text/javascript" id="position_2918">
	  var ads_positions = ads_positions || [];
	  ads_positions.push(["2918", "", "document"]);
	  (function() {
	    if (!document.getElementById("ads_loader")) {
	      var script = document.createElement("script"); script.type = "text/javascript"; script.id = "ads_loader"; script.async = true;
	      script.src = ("https:" == document.location.protocol ? "https://" : "http://") + "static.adclick.lv/ads_loader__min.js?rand=" + (new Date()).getTime();
	      (document.getElementsByTagName("head")[0] || document.getElementsByTagName("body")[0]).appendChild(script);
	    }
	  })();
	</script>
</div>

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
		<li><h3><a href="{node-url}">{title}</a></h3>
			<ul class="article-info"><li class="date">{date}</li><li class="comments"><a href="{node-url}#comments">{posts}x</a></li><li class="profile user-level-{level}"><a href="{aurl}">{author}</a></li></ul>
			<div class="c"></div>
			<a href="{node-url}"><img class="av index-av" width="75" height="75" src="//img.exs.lv/{avatar}" alt="{title}" /></a>
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

<!-- END BLOCK : news-->

<div class="c"></div>

</div>

<!-- INCLUDE BLOCK : share-block -->

