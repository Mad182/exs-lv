<!-- START BLOCK : tags-current-->
<h1>Tags &quot;{tag-name}&quot;</h1>
<!-- START BLOCK : tags-description-->
<p>{description}</p>
<!-- END BLOCK : tags-description-->

<div class="content-ads">
	<script type="text/javascript"><!--
		google_ad_client = "pub-9907860161851752";
		/* exs_saturs 468x60, created 3/28/11 */
		google_ad_slot = "4703722392";
		google_ad_width = 468;
		google_ad_height = 60;
		//-->
	</script>
	<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
	</script>
</div>

<!-- START BLOCK : tags-articles-->
<h3>Raksti</h3>
<ul class="taged" id="taged-articles">
	<!-- START BLOCK : tags-articles-node-->
	<li><strong><a href="{url}">{title}</a></strong> <small>no <a href="{aurl}">{author}</a></small><p style="font-size:90%;padding: 0 0 6px;margin: 0;">{text}</p></li>
	<!-- END BLOCK : tags-articles-node-->
</ul>
<!-- END BLOCK : tags-articles-->


<!-- START BLOCK : tags-images-->
<h3>Attēli</h3>
<ul class="taged">
	<!-- START BLOCK : tags-articles-node-img-->
	<li><a href="/gallery/{articles-node-author-id}/{articles-node-id}">{articles-node-title}</a> no <a href="/?u={articles-node-author-id}">{articles-node-author}</a></li>
	<!-- END BLOCK : tags-articles-node-img-->
</ul>
<!-- END BLOCK : tags-images-->

<!-- START BLOCK : tags-miniblogs-->
<h3>Miniblogi</h3>
<ul class="taged">
	<!-- START BLOCK : tags-articles-node-mb-->
	<li><a href="/say/{uid}/{id}-{url}">{text}</a></li>
	<!-- END BLOCK : tags-articles-node-mb-->
</ul>
<!-- END BLOCK : tags-miniblogs-->

<!-- START BLOCK : tags-groups-->
<h3>Grupas</h3>
<ul class="taged">
	<!-- START BLOCK : tags-articles-node-group-->
	<li><a href="/group/{id}">{title}</a></li>
	<!-- END BLOCK : tags-articles-node-group-->
</ul>
<!-- END BLOCK : tags-groups-->


<!-- END BLOCK : tags-current-->

<!-- START BLOCK : tags-rand-->
<h2>Nejauši tagi</h2>
{out}
<!-- END BLOCK : tags-rand-->