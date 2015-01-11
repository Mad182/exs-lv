<div id="gallery-content">

	<!-- START BLOCK : user-gallery-->
	<div class="tabMain">

		<!-- START BLOCK : add-image-max-->
		<div class="form">
			<p class="notice">Tu esi sasniedzis {max} galerijas attēlu limitu. Lai pievienotu jaunas bildes, izdzēs kādu no vecajām, vai pasūti <a href="/extend-gallery">papildus galerijas bilžu vietas</a>.</p>
		</div>
		<!-- START BLOCK : add-image-max-->

		<!-- START BLOCK : add-image-form-->
		<a id="addpic" href="#">Pievienot attēlu</a>
		<div class="c"></div>
		<form id="newpic" class="form" action="{page-url}" method="post" enctype="multipart/form-data" style="display:none">
			<fieldset>
				<legend>Pievienot jaunu attēlu</legend>
				<input type="hidden" name="unique-form-check" value="{gallery-check}" />
				<p>
					<label for="new-image">Attēls:</label><br />
					<input type="file" class="text" name="new-image" id="new-image" />
				</p>
				<!-- START BLOCK : new-image-interest -->
				<p>
					<label for="new-image-interest">Attēla kategorija:</label><br />
					<select name="new-image-interest">
						<option value="0">--</option>
						<!-- START BLOCK : select-new-interest-->
						<option value="{id}">{title}</option>
						<!-- END BLOCK : select-new-interest-->
					</select>
				</p>
				<!-- END BLOCK : new-image-interest -->
				<p>
					<label for="new-image-description">Komentārs:</label><br />
					<input type="text" class="text" name="new-image-description" id="new-image-description" />
				</p>
				<p>
					<input class="button primary" type="submit" name="submit" id="submit" value="Pievienot" />
				</p>
			</fieldset>
		</form>
		<!-- START BLOCK : add-image-form-->

		<!-- START BLOCK : image-list-->
		<div id="gallery-image-list">
			<a class="prev browse left gray">&laquo;</a>
			<div class="scrollable imgs" id="images">
				<div class="items">
					<!-- START BLOCK : image-list-node-->
					<a class="{image-list-sel}" href="/gallery/{user-id}/{image-list-id}#images"><img src="{img-server}/{image-list-thb}" alt="{image-list-linkid}" /><span>{image-list-posts}</span></a>
					<!-- START BLOCK : image-list-node-->
				</div>
			</div>
			<a class="next browse right gray">&raquo;</a>
			<div class="c"></div>
		</div>

		<script type="text/javascript">
			$().ready(function() {
				$("#images").scrollable();
				/* parvietojam augsejo attelu navigaciju uz lapu kur atrodas bilde */
				var api = $("#images").data("scrollable");
				api.move({current-img-page});
			});
		</script>


		<!-- START BLOCK : image-list-->

		<!-- START BLOCK : image-list-empty-->
		<p>Šis lietotājs pagaidām nav pievienojis nevienu attēlu :(</p>
		<!-- START BLOCK : image-list-empty-->

		<!-- START BLOCK : image-view-->
		<div id="gal-large" class="prew-next">
			<!-- START BLOCK : image-view-img-->
			<img src="{img-server}/{image-url}" alt="attēls" title="{image-title}" />
			{newer}
			{older}
			<!-- END BLOCK : image-view-img-->
			<!-- START BLOCK : image-view-video-->
			{video}
			<!-- END BLOCK : image-view-video-->
			<div class="clear"></div>
		</div>

		{image-text}

		<div class="mbox" style="margin: 5px 0">
			<div id="post-rating">Lietotāju vērtējums: <span class="current-rating">{rating}</span> ({rating_count} balsis)</div>
			<div id="star"></div>
			<div class="c"></div>
			<ul class="article-info"><li class="date">{image-date}</li><li class="comments">{image-posts} komentāri</li><li class="views">skatīts {image-views}x</li></ul><div class="c"></div>
		</div>

		<div class="c"></div>
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
			<label for="post-tags-input">tagi, atdalīti ar komatu:</label><br />
			<input style="width: 340px;" type="text" class="text" name="newtags" id="post-tags-input" /> <input class="button" type="submit" value="Pielikt" />
		</form>
		<!-- END BLOCK : post-newtags-->
		<div class="c"></div>
		<!-- START BLOCK : edit-image-form-->
		<form class="form" action="" method="post">
			<fieldset>
				<legend>Attēla rīki</legend>
				<input type="hidden" value="{edit-id}" name="edit-image-id" />
				<label for="edit-image-disablecomments">
					<input class="ajax-checkbox" type="checkbox" name="edit-image-disablecomments" id="edit-image-disablecomments"{edit-closed} />Slēgt komentārus
				</label>
				<!-- START BLOCK : edit-image-interest -->
				<p>
					<label for="image-interest">Attēla kategorija:</label>
					<select class="ajax-checkbox" name="image-interest">
						<option value="0">--</option>
						<!-- START BLOCK : select-interest-->
						<option value="{id}"{sel}>{title}</option>
						<!-- END BLOCK : select-interest-->
					</select>
				</p>
				<!-- END BLOCK : edit-image-interest -->
				[<a title="Dzēst attēlu un tā komentārus" href="/gallery/{user-id}/{edit-id}?mode=delete&token={token}" class="confirm"><span class="red">dzēst</span></a>]
			</fieldset>
		</form>
		<!-- START BLOCK : edit-image-form-->
		<!-- END BLOCK : image-view-->

		<!-- START BLOCK : adm-edit-comment-->
		<a name="virsraksts"></a>
		<h2>Labot komentāru</h2>
		<form action="{page-url}" id="comment-edit" class="form" method="post" enctype="multipart/form-data">
			<fieldset>
				<legend>Komentāra saturs</legend>
				<input type="hidden" name="edit-comment-id" value="{comment-id}" />
				<textarea name="edit-comment-text" id="edit-comment-text" cols="94" rows="40" style="width:100%;height:300px">{comment-text}</textarea>
				<p>
					<input type="submit" name="submit" value="Saglabāt izmaiņas" class="submit" />
				</p>
			</fieldset>
		</form>
		<!-- END BLOCK : adm-edit-comment-->

	</div>
	<!-- END BLOCK : user-gallery-->

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
				<span class="c-rate"><span class="r-val {comment-vclass}">{comment-vote_value}</span>{comment-plus}{comment-minus}</span>
				<!-- END BLOCK : comments-vote-->
				<strong><a href="#c{comment-id}" title="Saite uz komentāru">#{comment-number}</a> </strong>{comment-date}
				<!-- START BLOCK : report-user -->
				<a class="report-user" href="/report/gallery-comment/{comment-id}" title="Ziņot par pārkāpumu">Ziņot</a>
				<!-- END BLOCK : report-user -->
				<!-- START BLOCK : comments-adm-->
				| <a href="{delete}" class="confirm red">dzēst</a>
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
					<li><a name="c{rpl-id}" href="{rpl-aurl}"><img class="rpl-avatar" src="/dati/bildes/{rpl-path}/{rpl-author-avatar}" alt="" /></a><div class="response-content"><p class="comment-author"><a href="{rpl-aurl}">{rpl-author}</a> @ {rpl-date} atbildēja:</p>{rpl-text}</div></li>
					<!-- END BLOCK : com-reply-->
				</ul>
				<!-- END BLOCK : com-replies-->
			</div>
			<p class="comment-tools"><a href="{aurl}">profils</a>
				<!-- END BLOCK : comments-pm-->
				<a href="/pm/write/?to={comment-author-id}">vēstule</a>
				<!-- END BLOCK : comments-pm-->
				<a href="/gallery/{comment-author-id}">galerija</a></p><div class="c"></div></dd>
		<!-- END BLOCK : comments-node-user-->
		<!-- END BLOCK : comments-node-->
	</dl>
	<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
	<!-- END BLOCK : comments-block-->

	<!-- START BLOCK : article-closed-->
	<p class="closed">Komentāri ir slēgti.</p>
	<!-- END BLOCK : article-closed-->

	<!-- START BLOCK : add-comment-->
	<form action="" class="form" method="post">
		<fieldset>
			<legend>Pievienot komentāru</legend>
			<input type="hidden" name="comment-pid" value="{comment-pid}" />
			<input type="hidden" name="checksrc" value="{comment-pid-check}" />
			<textarea style="height:90px" cols="45" rows="5" name="commenttext" ></textarea><br />
			<input type="submit" class="button primary" value="Pievienot" />
		</fieldset>
	</form>
	<!-- END BLOCK : add-comment-->

	<!-- START BLOCK : login-to-comment-->
	<div class="form">
		<p class="notice">Ielogojies vai <a href="/register">izveido profilu</a>, lai komentētu!</p>
	</div>
	<!-- END BLOCK : login-to-comment-->

</div>
