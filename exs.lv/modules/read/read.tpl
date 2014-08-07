<!-- START BLOCK : page-options-->
<ul id="page-options">
	<li class="option-edit"><a href="?mode=edit">labot</a></li>
	<li class="option-history"><a href="?mode=history">vēsture</a></li>
</ul>
<!-- END BLOCK : page-options-->

<!-- START BLOCK : page-history-->
<h1>Saglabātās versijas</h1>
<!-- START BLOCK : page-history-list-->
<ol>
	<!-- START BLOCK : page-history-node-->
	<li>
		<a href="/?c=252&amp;page={id}" target="_blank"><strong>{title}</strong></a> ({symbols} simboli) @ {time} pēdējās izmaiņas no {user}
	</li>
	<!-- END BLOCK : page-history-node-->
</ol>
<!-- END BLOCK : page-history-list-->

<!-- END BLOCK : page-history-->
<!-- START BLOCK : adm-edit-comment-->
<h1>Komentāra labošana</h1>
<form action="{page-url}" id="comment-edit" class="form" method="post" enctype="multipart/form-data">
	<fieldset>
		<legend>Labot komentāru</legend>
		<label for="edit-comment-text">Teksts:</label><br />
		<textarea name="edit-comment-text" id="edit-comment-text" cols="94" rows="40" style="width: 100%; height: 300px;">{comment-text}</textarea>
		<p>
			<input type="hidden" name="edit-comment-id" value="{comment-id}" /><input type="submit" name="submit" value="Saglabāt izmaiņas" class="button primary" />
		</p>
	</fieldset>
</form>
<!-- END BLOCK : adm-edit-comment-->
<!-- START BLOCK : edit-article-->
<h1>Labot rakstu &quot;{article-showtitle}&quot;</h1>

<!-- START BLOCK : edit-movie-->
<form action="{page-url}" class="form" method="post" enctype="multipart/form-data">
	<fieldset>
		<legend>Filmas avatars</legend>
		<!-- START BLOCK : edit-movie-avatar-->
		<img src="//img.exs.lv{url}" />
		<div class="c"></div>
		<!-- END BLOCK : edit-movie-avatar-->

		<p><input type="submit" name="search-avatar" value="Meklēt automātiski" class="button primary" /></p>
		<!-- START BLOCK : edit-movie-image-->
		<a href="{url}" class="imgselect"><img src="{url}" /></a>
		<!-- END BLOCK : edit-movie-image-->
		<div class="c"></div>

		<p><label for="avatar-url">Saite uz bildi:</label><br /><input type="text" name="avatar-url" id="avatar-url" class="text" value="" /></p>

		<p><input type="hidden" name="edit-topic-id" value="{article-id}" /><input type="submit" name="submit" value="Pievienot avataru" class="button primary" /></p>
	</fieldset>
</form>

<!-- END BLOCK : edit-movie-->


<form action="{page-url}" class="form" method="post" enctype="multipart/form-data">
	<fieldset>
		<legend>Labot rakstu</legend>

		<!-- START BLOCK : goto-wide-page -->
		<p style="margin-left:20px"><a href="{wide-page-url}&amp;wide=1">Atvērt platā skata režīmu (notiks lapas pārlāde!)</a></p>
		<!-- END BLOCK : goto-wide-page -->

		<!-- START BLOCK : goto-narrow-page -->
		<p style="margin-left:20px"><a href="{wide-page-url}&amp;narrow=1">Atvērt šaurā skata režīmu (notiks lapas pārlāde!)</a></p>
		<!-- END BLOCK : goto-narrow-page -->

		<p><label for="edit-topic-title">Nosaukums:</label><br /><input type="text" name="edit-topic-title" id="edit-topic-title" class="title" value="{article-title}" maxlength="64" /></p>

		<!-- START BLOCK : edit-movie-data-->

		<p>
			<label for="movie-titlelv">Nosaukums latviski:</label><br />
			<input type="text" name="movie-titlelv" id="movie-titlelv" class="text" value="{title_lv}" maxlength="64" />
		</p>
		<p>
			<label for="movie-year">Gads:</label><br />
			<input type="text" name="movie-year" id="movie-year" class="text" style="width:50px;" value="{year}" maxlength="4" />
		</p>
		<p>
			<!--<label for="movie-imdb">IMDB id:</label><br />
			<input type="text" name="movie-imdb" id="movie-imdb" class="text" style="width:100px;" value="{imdb_id}" maxlength="40" />-->
			<label><input type="checkbox" class="checkbox" name="imdb-getdata" />&nbsp;iegūt IMDB datus</label>
		</p>
		<p>
			<label for="movie-type">Filmas veids:</label><br />
			<select name="movie-type">
				<option value="movie"{sel-movie}>Filma</option>
				<option value="series"{sel-series}>Seriāls</option>
				<option value="animation"{sel-animation}>Animācijas filma</option>
				<option value="documentary"{sel-documentary}>TV raidījums</option>
			</select>
		</p>

		<!-- END BLOCK : edit-movie-data-->

		<label for="edit-topic-body">Teksts:</label><br />
		<textarea name="edit-topic-body" id="edit-topic-body" cols="94" rows="40" style="width: 100%; height: 500px;">{article-text}</textarea>
		<!-- START BLOCK : edit-article-av-->
		<p><a class="thb-image"><img src="//exs.lv/{img}" alt="Avatars" /></a></p>
		<!-- END BLOCK : edit-article-av-->
		<p><label for="edit-avatar">Raksta avatars:</label><br /><input type="file" class="long" name="edit-avatar" id="edit-avatar" /></p>
		<!-- START BLOCK : edit-article-category -->
		<p><label for="edit-category">Lapas sadaļa:</label><br />
			<select name="edit-category">
				<!-- START BLOCK : catgroup-->
				<optgroup label="{title}">
					<!-- START BLOCK : catitem-->
					<option value="{id}"{sel}>{title}</option>
					<!-- END BLOCK : catitem-->
				</optgroup>
				<!-- END BLOCK : catgroup-->
			</select></p>
		<!-- END BLOCK : edit-article-category -->
		<p><input type="hidden" name="edit-topic-id" value="{article-id}" /><input type="submit" name="submit" value="Saglabāt izmaiņas" class="button primary" /></p>
	</fieldset>
</form>
<!-- END BLOCK : edit-article-->

<!-- START BLOCK : read-article-->

<h1>{article-title}
	<!-- START BLOCK : title-lv-->
	<span class="slash">/</span> <small>{title}</small>
	<!-- END BLOCK : title-lv-->
</h1>

<!-- START BLOCK : page-ad-google-->
{ad-468}
<!-- END BLOCK : page-ad-google-->

<!-- START BLOCK : page-ad-dateks-->
<div class="content-block">
	<script type="text/javascript" src="http://affiliate.dateks.lv/scripts/banner.php?a_aid=view&amp;a_bid=8872f56e"></script>
</div>
<!-- END BLOCK : page-ad-dateks-->

<!-- START BLOCK : article-avatar-box-->
<img class="av" src="//exs.lv/{article-avatar-image}" alt="{article-avatar-alt}" title="{article-avatar-alt}" />
<!-- END BLOCK : article-avatar-box-->

<!-- START BLOCK : movie-avatar-->
<a href="//img.exs.lv{image}" class="lightbox" title="{title}"><img class="av" id="post-avatar" src="//img.exs.lv{thb}" alt="{title}" /></a>
<!-- END BLOCK : movie-avatar-->

<div id="full-story">
	<!-- START BLOCK : movie-info-->
	<div id="movie-info">

		<!-- START BLOCK : movie-info-type-->
		<p class="title"><strong>{type} &quot;{title}&quot;</strong></p>
		<!-- END BLOCK : movie-info-type-->

		<!-- START BLOCK : movie-info-year-->
		<p><strong>Gads:</strong> {year}</p>
		<!-- END BLOCK : movie-info-year-->

		<!-- START BLOCK : movie-info-genres-->
		<p><strong>Žanrs:</strong> {genres}</p>
		<!-- END BLOCK : movie-info-genres-->

		<!-- START BLOCK : movie-info-rating-->
		<p><strong>IMDB vērtējums:</strong> {rating}</p>
		<!-- END BLOCK : movie-info-rating-->

		<!-- START BLOCK : movie-info-runtime-->
		<p><strong>Garums:</strong> {runtime} minūtes</p>
		<!-- END BLOCK : movie-info-runtime-->

		<!-- START BLOCK : movie-like-->
		{like}
		<!-- END BLOCK : movie-like-->

		<!-- START BLOCK : movie-likes-->
		<p>
			<strong>Iesaka:</strong><br />
			<!-- START BLOCK : movie-likes-user-->
			<img src="{avatar}" class="icon" alt="{nick}" title="{nick}" />
			<!-- END BLOCK : movie-likes-user-->
			<br style="clear:both"/>{rest}
		</p>
		<!-- END BLOCK : movie-likes-->

	</div>
	<div class="c"></div>
	<p>&nbsp;</p>
	<!-- END BLOCK : movie-info-->
	{article-text}

	<!-- START BLOCK : page-ad-google-bottom-->
	<div class="c"></div>
	{ad-468}
	<!-- END BLOCK : page-ad-google-bottom-->

</div>
<!-- START BLOCK : post-stags-->
<div class="c"></div>
<div id="related-topics">
	<div class="mbox">
		<h4>Saistītie raksti:</h4>
		<ul>
			<!-- START BLOCK : post-stags-node-->
			<li><a href="{url}">{title}</a></li>
			<!-- END BLOCK : post-stags-node-->
		</ul>
	</div>
</div>
<!-- END BLOCK : post-stags-->
<div class="c"></div>
<div class="mbox" style="margin: 5px 0" id="like-rate-box">
	<div id="post-rating">Lasītāju vērtējums: <span class="current-rating">{rating}</span> ({rating_count} balsis)</div>
	<div id="star"></div>

	<div style="padding: 5px;">
		<script type="text/javascript" src="//www.draugiem.lv/api/api.js"></script>
		<div style="float: left; margin: 0 12px 0 0" id="draugiemLike"></div>
		<script type="text/javascript">
			var p = {
				titlePrefix: "{page-domain}",
				name: "{page-domain}"
			};
			new DApi.Like(p).append('draugiemLike');
		</script>

		<div style="width: 90px;float: left; margin: 0"><a href="https://twitter.com/share" class="twitter-share-button">Tweet</a>
			<script>!function(d, s, id) {
					var js, fjs = d.getElementsByTagName(s)[0];
					if (!d.getElementById(id)) {
						js = d.createElement(s);
						js.id = id;
						js.src = "//platform.twitter.com/widgets.js";
						fjs.parentNode.insertBefore(js, fjs);
					}
				}(document, "script", "twitter-wjs");</script></div>

		<div id="fb-root"></div>
		<script>(function(d, s, id) {
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id))
					return;
				js = d.createElement(s);
				js.id = id;
				js.src = "//connect.facebook.net/lv_LV/all.js#xfbml=1";
				fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));</script>

		<div class="fb-like" data-send="false" data-layout="button_count" data-width="100" data-show-faces="false"></div>

		<div class="c"></div>
	</div>

</div>
<div class="c"></div>
<ul class="article-info">
	<li class="date">{article-date}</li>
	<li class="profile user-level-{level} user-gender-{gender}">{author}</li>
	<li class="comments">{article-posts}x</li>
	<li class="views">{article-views}x</li>
	<!-- START BLOCK : add-bookmark-->
	<li class="attach">[<a title="Pievienot savai rakstu izlasei" href="?mode=bookmark">+</a>]{article-status}</li>
	<!-- END BLOCK : add-bookmark-->
</ul>
<div class="c"></div>
<!-- START BLOCK : post-tags-->
<div id="post-tags-wrapper">
	<!-- START BLOCK : post-tags-ul-->
	<ul id="article-tags" class="list-tags">
		<!-- START BLOCK : post-tags-node-->
		<li><a href="/tag/{slug}" rel="tag">{tag-title}</a><!--{tag-remove}--></li>
		<!-- END BLOCK : post-tags-node-->
	</ul>
	<!-- END BLOCK : post-tags-ul-->
</div>
<!-- START BLOCK : post-newtags-->
<div class="c"></div>
<form action="{page-url}" id="new-tags" class="form" method="post" style="padding: 12px 0 4px">
	<fieldset>
		<legend>Tagi, atdalīti ar komatu</legend>
		<input style="width:340px" type="text" class="text" name="newtags" id="post-tags-input" /> <input class="button" type="submit" value="Pielikt" />
	</fieldset>
</form>
<!-- END BLOCK : post-newtags-->
<div class="c"></div>
<!-- END BLOCK : post-tags-->
<!-- START BLOCK : post-tools-->
<form class="simple-form" action="" method="post">
	<fieldset>
		<label><input type="checkbox" name="close" class="ajax-checkbox"{edit-page-closed}{edit-page-disable-closing} />Slēgt komentārus</label>
		<input type="hidden" name="close-do" value="1" />
	</fieldset>
</form>
<!-- START BLOCK : post-disableclose-->
<form class="simple-form" action="" method="post">
	<fieldset>
		<label><input type="checkbox" name="disable-close" class="ajax-checkbox"{edit-page-disabled} />Neļaut autoram atslēgt/aizslēgt rakstu</label>
		<input type="hidden" name="disable-close-do" value="1" />
	</fieldset>
</form>
<!-- END BLOCK : post-disableclose-->
<!-- START BLOCK : post-attach-->
<form class="simple-form" action="" method="post">
	<fieldset>
		<label><input type="checkbox" name="attach" class="ajax-checkbox"{edit-page-attached} />Piespraust rakstu</label>
		<input type="hidden" name="attach-do" value="1" />
	</fieldset>
</form>
<!-- END BLOCK : post-attach-->
<!-- END BLOCK : post-tools-->

<!-- END BLOCK : read-article-->

<!-- START BLOCK : comments-block-->
<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
<dl id="comments">
	<!-- START BLOCK : comments-node-->
	<!-- START BLOCK : comments-node-user-->
	<dt>
	<a class="username" id="c{comment-id}" href="{aurl}">{comment-author}</a>
	<a href="{aurl}">
		<img class="comments-avatar" src="{avatar}" alt="{title}" />
	</a>
	<span class="custom-title">{custom_title}</span>
	<span class="author-info">Karma: {karma}</span>
	</dt>
	<dd>
		<p class="inf">
			<!-- START BLOCK : comments-vote-->
			<span class="c-rate">
				<span class="r-val {comment-vclass}">{comment-vote_value}</span>{comment-plus}{comment-minus}
			</span>
			<!-- END BLOCK : comments-vote-->
			<strong>
				<a href="#c{comment-id}" title="Saite uz komentāru">#{comment-number}</a>
			</strong>
			<span>{comment-date}</span>
			<!-- START BLOCK : report-comment -->
			<span class="report-button">
				<a class="report-user" href="/report/article-comment/{comment-id}" title="Ziņot par pārkāpumu">Ziņot</a>
			</span>
			<!-- END BLOCK : report-comment -->
			<!-- START BLOCK : comments-adm-->
			| [<a href="{delete}" class="confirm red" title="Dzēst">x</a>]
			| <a href="{edit}">labot</a>
			<!-- END BLOCK : comments-adm-->
			<!-- START BLOCK : comments-reply-->
			| <a class="rpl" href="/reply/{page-id}/{comment-id}">atbildēt</a>
			<!-- END BLOCK : comments-reply-->
			<!-- START BLOCK : comments-own-->
			| <a href="{edit}">labot</a>
			<!-- END BLOCK : comments-own-->
		</p>
		<div class="comment-text">{comment-text}{comment-editedby}
			<!-- START BLOCK : com-replies-->
			<ul class="rpl-list mbox">
				<!-- START BLOCK : com-reply-->
				<li>
					<!-- START BLOCK : reply-vote-->
					<span class="c-rate"><span class="r-val {comment-vclass}">{comment-vote_value}</span>{comment-plus}{comment-minus}</span>
					<!-- END BLOCK : reply-vote-->
					<a name="c{rpl-id}" href="{rpl-aurl}"><img class="rpl-avatar av" src="{rpl-avatar}" alt="" /></a>
					<div class="response-content">
						<p class="comment-author"><a href="{rpl-aurl}">{rpl-author}</a> @ {rpl-date} atbildēja:
							<!-- START BLOCK : report-reply -->
							<span class="report-button">
								<a class="report-user" href="/report/article-comment/{comment-id}" title="Ziņot par pārkāpumu">Ziņot</a>
							</span>
							<!-- END BLOCK : report-reply -->
							<!-- START BLOCK : reply-adm-->
							[<a href="{delete}" class="confirm red" title="Dzēst">x</a>] |
							<a href="{edit}">labot</a>
							<!-- END BLOCK : reply-adm-->

						</p>{rpl-text}</div></li>
				<!-- END BLOCK : com-reply-->
			</ul>
			<!-- END BLOCK : com-replies-->
		</div>
		{signature}

		<!-- START BLOCK : comment-tools-->
		<p class="comment-tools">
			<a href="/user/{id}">profils</a>
			<!-- START BLOCK : comments-pm-->
			<a href="/pm/write/?to={id}">vēstule</a>
			<!-- END BLOCK : comments-pm-->
			<a href="/gallery/{id}">galerija</a>
		</p>
		<!-- END BLOCK : comment-tools-->

		<div class="c"></div></dd>
	<!-- END BLOCK : comments-node-user-->
	<!-- START BLOCK : comments-node-anon-->
	<dt>
	<span class="username">{comment-anon_nick}</span>
	<img class="comments-avatar" src="{comment-avatar}" alt="{comment-anon_nick}" />
	<br />
	<span class="author-info"><span class="title">Grupa:</span> <span>Viesi</span></span>
	</dt>
	<dd>
		<p class="inf">
			<strong><a href="#c{comment-id}" id="c{comment-id}" title="Saite uz komentāru">#{comment-number}</a> </strong>{comment-date}
			<!-- START BLOCK : comments-anon-adm-->
			| <a href="?delanon={comment-id}" class="confirm red">dzēst</a> |
			<a href="?blockanon={comment-ip}">bloķēt ({comment-ip})</a> |
			<a href="?editcom={comment-id}">labot</a>
			<!-- END BLOCK : comments-anon-adm-->
		</p>
		<div class="comment-text">{comment-text}{comment-editedby}</div>
	</dd>
	<!-- END BLOCK : comments-node-anon-->
	<!-- END BLOCK : comments-node-->
</dl>
<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
<!-- END BLOCK : comments-block-->

<!-- START BLOCK : article-closed-->
<p class="closed">Komentāri ir slēgti.</p>
<!-- END BLOCK : article-closed-->

<!-- START BLOCK : add-comment-->
<form action="" class="form" method="post" id="add-comment-form">
	<fieldset>
		<legend>Pievienot komentāru</legend>
		<input type="hidden" name="comment-pid" value="{comment-pid}" />
		<input type="hidden" name="checksrc" value="{comment-pid-check}" />
		<!-- START BLOCK : resp-tools-->
		<label><input type="checkbox" name="no-bump" value="1" /><small>&nbsp;nebumpot</small></label><br />
		<!-- END BLOCK : resp-tools-->
		<textarea style="height:150px;width:100%" cols="45" rows="5" name="commenttext" ></textarea>
		<p>
			<input type="submit" class="button primary" value="Pievienot" />
		</p>
	</fieldset>
</form>
<!-- END BLOCK : add-comment-->

<!-- START BLOCK : login-to-comment-->
<div class="form">
	<p class="notice">Ielogojies vai <a href="/register">izveido profilu</a>, lai komentētu!</p>
</div>
<!-- END BLOCK : login-to-comment-->
