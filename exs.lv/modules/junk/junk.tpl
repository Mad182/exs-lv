<ul class="tabs">
	<li><a href="/junk">Jaunākie</a></li>
	<li><a href="/junk/commented">Pēdējie komentētie</a></li>
	<li><a href="/junk/add">Pievienot savu</a></li>
</ul>

<!-- START BLOCK : junk-->

<a class="junk-button" href="/junk/random" title="random"><img style="float: right;margin: 5px 10px;" src="//img.exs.lv/bildes/junk/random.png" alt="Random" /></a>


<!-- INCLUDE BLOCK : share-block -->

<div class="c"></div>
<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
	<!-- START BLOCK : junk-item-date-->
	<div class="c"></div>
	<h3>{date}</h3>
	<div class="c"></div>
	<ul id="junk-list" style="margin:0;padding:10px 0">
		<!-- START BLOCK : junk-item-->
		<li style="float:left;background:transparent;padding:0;margin:0"><a href="/junk/{id}"><img src="//img.exs.lv{thb}" alt="" style="float:left;margin:0 5px 10px" class="av" /></a></li>
		<!-- END BLOCK : junk-item-->
	</ul>
	<!-- END BLOCK : junk-item-date-->
<div class="c"></div>
<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
<!-- END BLOCK : junk-->


	<!-- START BLOCK : junk-view-->
	<ul id="miniblog-list">
		<li>
			<div class="c"></div>
			<div id="junk-vote-wrap">{voter}</div>

			<!-- START BLOCK : mb-reply-main-->
			<a href="#" class="mb-reply-main mb-icon">Atbildēt</a>
			<!-- END BLOCK : mb-reply-main-->

			<p style="text-align: center;margin: 10px auto;width: 300px;"">

				<!-- START BLOCK : junk-next-->
				<a class="junk-button" href="/junk/{id}#content" title="Jaunāka"><img style="float: left;margin: 0 10px;" src="//img.exs.lv/bildes/junk/left.png" alt="Jaunāka" /></a>
				<!-- END BLOCK : junk-next-->

				<!-- START BLOCK : junk-prev-->

				<a class="junk-button" href="/junk/{id}#content" title="Vecāka"><img style="float: right;margin: 0 10px;" src="//img.exs.lv/bildes/junk/right.png" alt="Vecāka" /></a>
				<!-- END BLOCK : junk-prev-->

				<a class="junk-button" href="/junk/random" title="random"><img style="margin: 0 10px;" src="//img.exs.lv/bildes/junk/random.png" alt="Random" /></a>

			</p>

			<div class="c"></div>

			<p style="text-align:center">{title}</p>
			{image}

			<div class="c"></div>

			<!-- INCLUDE BLOCK : share-block -->

			<!-- START BLOCK : junk-view-author-->
			<p>Pievienoja: <a href="/user/{id}">{nick}</a></p>
			<!-- END BLOCK : junk-view-author-->

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
	<a class="junk-button" href="/junk/random" title="random"><img style="float: right;margin: 5px 10px;" src="//img.exs.lv/bildes/junk/random.png" alt="Random" /></a>
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
