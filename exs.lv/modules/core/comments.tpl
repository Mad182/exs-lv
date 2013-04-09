<!-- START BLOCK : comments-block-->
<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
<dl id="comments">
	<!-- START BLOCK : comments-node-->
	<!-- START BLOCK : comments-node-user-->
	<dt>
		<a class="username" id="c{comment-id}" href="{aurl}">{comment-author}</a>
		<a href="{aurl}">
			<img class="comments-avatar" src="/dati/bildes/useravatar/{comment-author-avatar}" alt="{comment-author-title}" />
		</a>
		<span class="custom-title">{comment-author-custom_title}</span>
		<span class="author-info">
			<span class="title">Grupa:</span> <span>{comment-author-class}</span><br />
			<span class="title">Karma:</span> <span>{comment-author-karma}</span>
		</span>
	</dt>
	<dd>
		<p class="inf">
			<!-- START BLOCK : comments-vote-->
			<span class="c-rate"><span class="r-val {comment-vclass}">{comment-vote_value}</span>{comment-plus}{comment-minus}</span>
			<!-- END BLOCK : comments-vote-->
			<strong><a href="#c{comment-id}" title="Saite uz komentāru">#{comment-number}</a> </strong>{comment-date}
			<!-- START BLOCK : comments-adm-->
			| <a href="{delete}" class="confirm red">dzēst</a>
			| <a href="{edit}">labot</a>
			<!-- END BLOCK : comments-adm-->
			<!-- START BLOCK : comments-reply-->
			| <a class="rpl" href="/Reply/{page-id}/{comment-id}">atbildēt</a>
			<!-- END BLOCK : comments-reply-->
			<!-- START BLOCK : comments-own-->
			| <a href="{edit}">labot</a>
			<!-- END BLOCK : comments-own-->
			<!-- START BLOCK : comments-quote-->
			| <a href="#" onclick="tinyMCE.execCommand('mceInsertContent',false,' [quote=&quot;{author}&quot;]{text}[/quote]\n\r');return false">citēt</a>
			<!-- END BLOCK : comments-quote-->
		</p>
		<div class="comment-text">{comment-text}{comment-editedby}
			<!-- START BLOCK : com-replies-->
			<ul class="rpl-list mbox">
				<!-- START BLOCK : com-reply-->
				<li><a name="c{rpl-id}" href="{rpl-aurl}"><img class="rpl-avatar" src="/dati/bildes/{rpl-path}/{rpl-author-avatar}" alt="" /></a><div class="response-content"><p class="comment-author"><a href="{rpl-aurl}">{rpl-author}</a> @ {rpl-date} atbildēja:</p>{rpl-text}</div></li>
				<!-- END BLOCK : com-reply-->
			</ul>
			<!-- END BLOCK : com-replies-->
		</div>{comment-author-signature}<p class="comment-tools"><a href="{aurl}">profils</a>
			<!-- END BLOCK : comments-pm-->
			<a href="/?c=104&amp;act=compose&amp;to={comment-author-id}">vēstule</a>
			<!-- END BLOCK : comments-pm-->
			<a href="/?g={comment-author-id}">galerija</a></p><div class="c"></div></dd>
	<!-- END BLOCK : comments-node-user-->
	<!-- START BLOCK : comments-node-anon-->
	<dt>
		<span class="username">{comment-anon_nick}</span>
		<img class="comments-avatar" src="{comment-avatar}" alt="{comment-anon_nick}" /><br />
		<span class="author-info"><span class="title">Grupa:</span> <span>Viesi</span></span>
	</dt>
	<dd>
		<p class="inf"><strong><a href="#c{comment-id}" id="c{comment-id}" title="Saite uz komentāru">#{comment-number}</a> </strong>{comment-date}
			<!-- START BLOCK : comments-anon-adm-->
			| <a href="/?p={page-id}&amp;delanon={comment-id}" class="confirm red">dzēst</a> |
			<a href="/?p={page-id}&amp;blockanon={comment-ip}">bloķēt ({comment-ip})</a> |
			<a href="/?p={page-id}&amp;editcom={comment-id}">labot</a>
			<!-- END BLOCK : comments-anon-adm-->
		</p>
		<div class="comment-text">{comment-text}{comment-editedby}</div>
	</dd>
	<!-- END BLOCK : comments-node-anon-->
	<!-- END BLOCK : comments-node-->
</dl>
<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
<!-- END BLOCK : comments-block-->

<!-- START BLOCK : no-comments-block-->
<p>Pagaidām nav komentāru.</p>
<!-- END BLOCK : no-comments-block-->
<!-- START BLOCK : login-to-comment-->
<p>Jāielogojas, lai pievienotu komentāru.</p>
<!-- END BLOCK : login-to-comment-->
<!-- START BLOCK : comment-added-->
<p>Paldies, tavs komentārs pievienots!</p>
<!-- END BLOCK : comment-added-->
<!-- START BLOCK : no-flood-->
<p>Tu jau komentēji pēdējais!</p>
<!-- END BLOCK : no-flood-->
<!-- START BLOCK : article-closed-->
<p class="closed">Komentāri ir slēgti.</p>
<!-- END BLOCK : article-closed-->

<!-- START BLOCK : add-comment-->
<form action="" class="form" method="post">
	<fieldset>
		<legend>Pievienot komentāru</legend>
		<input type="hidden" name="comment-pid" value="{comment-pid}" />
		<input type="hidden" name="checksrc" value="{comment-pid-check}" />
		<!-- START BLOCK : add-comment-sm-->
		<div class="smileys">
			<a href="#" onclick="tinyMCE.execCommand('mceInsertContent',false,' :D');return false"><img src="/bildes/fugue-icons/smiley-grin.png" alt="" /></a>
			<a href="#" onclick="tinyMCE.execCommand('mceInsertContent',false,' :)');return false"><img src="/bildes/fugue-icons/smiley.png" alt="" /></a>
			<a href="#" onclick="tinyMCE.execCommand('mceInsertContent',false,' :(');return false"><img src="/bildes/fugue-icons/smiley-sad.png" alt="" /></a>
			<a href="#" onclick="tinyMCE.execCommand('mceInsertContent',false,' ;)');return false"><img src="/bildes/fugue-icons/smiley-wink.png" alt="" /></a>
			<a href="#" onclick="tinyMCE.execCommand('mceInsertContent',false,' ;(');return false"><img src="/bildes/fugue-icons/smiley-cry.png" alt="" /></a>
			<a href="#" onclick="tinyMCE.execCommand('mceInsertContent',false,' :lol:');return false"><img src="/bildes/fugue-icons/smiley-lol.png" alt="" /></a>
			<a href="#" onclick="tinyMCE.execCommand('mceInsertContent',false,' :mrgreen:');return false"><img src="/bildes/fugue-icons/smiley-mr-green.png" alt="" /></a>
			<a href="#" onclick="tinyMCE.execCommand('mceInsertContent',false,' :eek:');return false"><img src="/bildes/fugue-icons/smiley-eek.png" alt="" /></a>
			<a href="#" onclick="tinyMCE.execCommand('mceInsertContent',false,' :roll:');return false"><img src="/bildes/fugue-icons/smiley-roll.png" alt="" /></a>
			<a href="#" onclick="tinyMCE.execCommand('mceInsertContent',false,' :sleep:');return false"><img src="/bildes/fugue-icons/smiley-sleep.png" alt="" /></a>
			<a href="#" onclick="tinyMCE.execCommand('mceInsertContent',false,' :evil:');return false"><img src="/bildes/fugue-icons/smiley-evil.png" alt="" /></a>
			<a href="#" onclick="tinyMCE.execCommand('mceInsertContent',false,' :blush:');return false"><img src="/bildes/fugue-icons/smiley-red.png" alt="" /></a>
			<a href="#" onclick="tinyMCE.execCommand('mceInsertContent',false,' :cool:');return false"><img src="/bildes/fugue-icons/smiley-cool.png" alt="" /></a>
			<a href="#" onclick="tinyMCE.execCommand('mceInsertContent',false,' :P');return false"><img src="/bildes/fugue-icons/smiley-razz.png" alt="" /></a>
			<a href="#" onclick="tinyMCE.execCommand('mceInsertContent',false,' :o:');return false"><img src="/bildes/fugue-icons/smiley-surprise.png" alt="" /></a>
			<a href="#" onclick="tinyMCE.execCommand('mceInsertContent',false,' :|');return false"><img src="/bildes/fugue-icons/smiley-neutral.png" alt="" /></a>
		</div>
		<!-- END BLOCK : add-comment-sm-->
		<textarea style="height:90px" cols="45" rows="5" name="commenttext" ></textarea><br />
		<input type="submit" class="button" value="Pievienot" />
	</fieldset>
</form>
<!-- END BLOCK : add-comment-->

<!-- START BLOCK : add-comment-anon-->
<form action="" class="form" method="post">
	<fieldset>
		<legend>Pievienot komentāru</legend>
		<p>
			<small>Varbūt tomēr vēlies ielogoties vai <a href="/register">reģistrēties</a>? Tas ir pavisam ātri ;)<br />Pievienojot komentāru, Tu piekrīti <a href="/read/lietosanas-noteikumi">lietošanas noteikumiem</a>.</small>
		</p>
		<p>
			<label>Niks<span class="red">*</span></label><br />
			<input type="text" class="text" name="comment-anon-nick" value="{comment-anon-nick}" id="comment-anon-nick" maxlength="14" /> <span id="userexists"></span>
		</p>
		<p>
			<label>E-pasts (netiks parādīts)</label><br />
			<input type="text" class="text" name="comment-anon-mail" value="{comment-anon-mail}" id="comment-anon-mail" />
		</p>
		<p>
			<label>Teksts<span class="red">*</span></label><br />
			<textarea style="height:90px" cols="45" rows="5" name="comment-anon-text" id="comment-anon-text" ></textarea><br />
		</p>
		<p>
			<input type="hidden" name="comment-pid" value="{comment-pid}" />
			<input type="hidden" name="checksrc" value="{comment-pid-check}" />
			<input type="submit" class="button" value="Pievienot" />
		</p>
	</fieldset>
</form>
<!-- END BLOCK : add-comment-anon-->