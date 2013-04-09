{clist}

<!-- START BLOCK : games-single-->
<h1>{title}</h1>

<div id="game-wrapper">
	<div id="game" style="width:{width}px;height:{height}px;min-height:{height}px"><div id="swf-content"></div></div>
	<div id="game-instructions">{instructions}</div>
	<div id="game-description">{description}</div>
	<div style="text-align: right;">
		<div id="game-rating">Spēlētāju vērtējums: {rating} ({rating_count} balsis)</div>
		<!-- START BLOCK : game-rate-->
		<form id="game-rating-form" method="post" action="" onsubmit="return postrating();">
			<label><input type="radio" name="vote" value="1" /> 1</label>
			<label><input type="radio" name="vote" value="2" /> 2</label>
			<label><input type="radio" name="vote" value="3" checked="checked" /> 3</label>
			<label><input type="radio" name="vote" value="4" /> 4</label>
			<label><input type="radio" name="vote" value="5" /> 5</label>
			<input type="submit" value="Balsot" />
		</form>
		<!-- END BLOCK : game-rate-->
	</div>
</div>
<script type="text/javascript">
	var flashvars = {};
	var attributes = {};
	swfobject.embedSWF("{flash_file}", "swf-content", "{width}", "{height}", "9", flashvars, attributes);
</script>

<div id="game-recommend">
	<iframe style="border: 0;margin:0;" height="20" width="84" frameborder="0" src="http://www.draugiem.lv/say/ext/like.php?title={title-encoded}&amp;url=http://exs.lv/{category-url}/{slug}&amp;titlePrefix=Flash"></iframe>
	<a href="http://twitter.com/share" class="twitter-share-button" data-count="none">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
	<iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fexs.lv%2F{category-url}%2F{slug}&amp;layout=button_count&amp;show_faces=false&amp;width=100&amp;action=like&amp;font&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="overflow:hidden; width:100px; height:20px; border: 0;margin:0;" allowTransparency="true"></iframe>
</div>

{main-ad-include}

<!-- START BLOCK : games-edit-->
<h1>Labot spēli</h1>
<form class="form" action="" method="post">
	<fieldset>
		<p>
			<label for="description">Apraksts</label><br />
			<textarea style="height:60px" rows="2" cols="20" name="description" id="description">{description}</textarea>
		</p>
		<p>
			<label for="instructions">Pamācība</label><br />
			<textarea style="height:60px" rows="2" cols="20" name="instructions" id="instructions">{instructions}</textarea>
		</p>
		<p>
			<input type="submit" value="Saglabāt" />
		</p>
	</fieldset>
</form>
<!-- END BLOCK : games-edit-->

{comments}

<div class="c"></div>

<!-- START BLOCK : games-list-other-->
<h2>Populārākās {category} spēles</h2>
<ul id="games-list" class="related">
	<!-- START BLOCK : games-node-other-->
	<li><a title="Spēlēt {alt} ({category})" href="/{category-url}/{slug}"><img src="{thumbnail}" width="93" height="74" alt="{alt}" /> {title} <span>Reitings: {rating}</span></a></li>
	<!-- END BLOCK : games-node-other-->
</ul>
<div class="c"></div>
<p class="related">
	<a href="/{category-url}/{category_slug}">Visas &quot;{category}&quot; flash spēles&nbsp;&raquo;</a>
</p>
<!-- END BLOCK : games-list-other-->

<!-- END BLOCK : games-single-->
