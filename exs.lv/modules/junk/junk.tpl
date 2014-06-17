<ul class="tabs nav nav-tabs">
	<li><a href="/junk">Jaunākie</a></li>
	<li><a href="/junk/commented">Pēdējie komentētie</a></li>
	<li><a href="/junk/add">Pievienot savu</a></li>
</ul>

<!-- START BLOCK : junk-->

<a class="junk-button" href="/junk/random" title="random"><img style="float: right;margin: 5px 10px;" src="http://exs.lv/bildes/junk/random.png" alt="Random" /></a>

<script type="text/javascript" src="//www.draugiem.lv/api/api.js"></script>
<div style="float: right;margin: 20px 20px 0 0;" id="draugiemLike"></div>
<script type="text/javascript">
	new DApi.Like().append('draugiemLike');
</script>

<div class="c"></div>
<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
<!-- START BLOCK : junk-item-date-->
<ul id="junk-list" style="margin:0;padding:10px 0">
	<div class="c"></div>
	<h3>{date}</h3>
	<div class="c"></div>
	<ul id="junk-list" style="margin:0;padding:10px 0">
		<!-- START BLOCK : junk-item-->
		<li style="float:left;background:transparent;padding:0;margin:0"><a href="/junk/{id}"><img src="http://img.exs.lv{thb}" alt="" style="float:left;margin:0 5px 10px" class="av" /></a></li>
		<!-- END BLOCK : junk-item-->
	</ul>
	<!-- END BLOCK : junk-item-date-->
	<div class="c"></div>
	<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
	<!-- END BLOCK : junk-->


	<!-- START BLOCK : junk-view-->
	<ul id="miniblog-list">
		<li>
			<div class="mbox">
				<div class="c"></div>
				<div id="junk-vote-wrap">{voter}</div>

				<!-- START BLOCK : mb-reply-main-->
				<a href="#" class="mb-reply-main mb-icon">Atbildēt</a>
				<!-- END BLOCK : mb-reply-main-->

				<p style="text-align: center;margin: 10px auto;width: 300px;"">

					<!-- START BLOCK : junk-next-->
					<a class="junk-button" href="/junk/{id}#content" title="Jaunāka"><img style="float: left;margin: 0 10px;" src="http://exs.lv/bildes/junk/left.png" alt="Jaunāka" /></a>
					<!-- END BLOCK : junk-next-->

					<!-- START BLOCK : junk-prev-->

					<a class="junk-button" href="/junk/{id}#content" title="Vecāka"><img style="float: right;margin: 0 10px;" src="http://exs.lv/bildes/junk/right.png" alt="Vecāka" /></a>
					<!-- END BLOCK : junk-prev-->

					<a class="junk-button" href="/junk/random" title="random"><img style="margin: 0 10px;" src="http://exs.lv/bildes/junk/random.png" alt="Random" /></a>

				</p>

				<div class="c"></div>

				<div class="content-block" style="width:728px;">
					<script type="text/javascript"><!--
					google_ad_client = "ca-pub-9907860161851752";
						/* junk 728x90 */
						google_ad_slot = "4194683639";
						google_ad_width = 728;
						google_ad_height = 90;
						//-->
					</script>
					<script type="text/javascript"
							src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
					</script>
				</div>


				<p style="text-align: center;">{title}</p>
				<p style="text-align: center;"><img src="http://img.exs.lv{image}" class="av" style="height:auto;width:auto;max-width:720px;float: none;" alt="" /></p>

				<div class="c"></div>
				<div style="padding: 5px 50px">

					<script type="text/javascript" src="//www.draugiem.lv/api/api.js"></script>
					<div style="float: left; margin: 1px 12px 0 0;z-index: 3" id="draugiemLike"></div>
					<script type="text/javascript">
						var p = {
							title: "{title-html}",
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
				<div class="c"></div>
				<!-- START BLOCK : junk-view-author-->
				<p>Pievienoja: <a href="/user/{id}">{nick}</a></p>
				<!-- END BLOCK : junk-view-author-->
			</div>

			<!-- START BLOCK : miniblog-posts-->
			{mbout}
			<!-- END BLOCK : miniblog-posts-->

			<!-- START BLOCK : miniblog-no-->
			<ul class="responses-0"><li style="display:none">Nav atbilžu</li></ul>
			<!-- END BLOCK : miniblog-no-->

			<!-- START BLOCK : user-miniblog-resp-->
			<div class="reply-ph-default reply-ph-current">
				<div id="response-{id}" class="miniblog-response">
					<form id="addresponse" class="form" action="{page-url}" method="post">
						<fieldset>
							<legend>Atbilde</legend>
							<input type="hidden" name="response-to" id="response-to" value="{id}" />
							<input type="hidden" name="token" id="token" value="{token}" />
							<textarea class="mb-textarea" tabindex="1" rows="5" cols="42" name="responseminiblog" id="responseminiblog"></textarea>
							<p>
								<input id="mbresponse-submit" tabindex="2" class="button primary" type="submit" name="submit" value="Pievienot" />
								<input id="mbresponse-waiting" class="button disabled" type="submit" style="display:none" value="Pievienot" disabled="disabled" />
							</p>
						</fieldset>
					</form>
				</div>
			</div>
			<!-- END BLOCK : user-miniblog-resp-->
			<!-- START BLOCK : user-miniblog-closed-->
			<div class="miniblog-response"><p class="closed">Šis minibloga ieraksts ir slēgts.{by}</p>{reason}</div>
			<!-- END BLOCK : user-miniblog-closed-->
			<!-- START BLOCK : user-miniblog-login-->
			<div class="miniblog-response"><p class="login">Ielogojies vai <a href="/register">izveido profilu</a>, lai komentētu!</p></div>
			<!-- END BLOCK : user-miniblog-login-->

		</li>
	</ul>
	<div class="c"></div>
	<!-- END BLOCK : junk-view-->

	<!-- END BLOCK : junk-add-->
	<a class="junk-button" href="/junk/random" title="random"><img style="float: right;margin: 5px 10px;" src="http://exs.lv/bildes/junk/random.png" alt="Random" /></a>
	<div class="c"></div>
	<form class="form" action="{page-url}" method="post" enctype="multipart/form-data">
		<fieldset>
			<legend>Pievienot attēlu</legend>
			<p>
				<label for="new-image">Attēls:</label><br />
				<input type="file" name="new-image" id="new-image" />
			</p>
			<p>
				<label for="title">Nosaukums/komentārs</label><br />
				<input type="text" class="text" value="" name="title" id="title" />
			</p>
			<p>
				<input type="submit" class="button primary" value="Upload!" />
			</p>
		</fieldset>
	</form>
	<!-- END BLOCK : junk-add-->
