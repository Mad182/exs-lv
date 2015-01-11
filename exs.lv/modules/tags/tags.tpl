<!-- START BLOCK : tags-current-->
<h1>Tags &quot;{tag-name}&quot;</h1>
<!-- START BLOCK : tags-description-->
<p>{description}</p>
<!-- END BLOCK : tags-description-->

<!-- START BLOCK : tags-articles-->
<h2>Raksti</h2>
<ul class="taged blockhref mb-col" id="taged-articles">
	<!-- START BLOCK : tags-articles-node-->
	<li style="text-align:left;"><a href="{url}"><img src="//exs.lv/dati/bildes/topic-av/{id}.jpg" class="av" alt="" /><strong>{title}</strong><span>no {author}</span><span style="color:#444">{text}</span></a></li>
	<!-- END BLOCK : tags-articles-node-->
</ul>
<!-- END BLOCK : tags-articles-->

<!-- START BLOCK : tags-images-->
<h2>Attēli</h2>
<p class="taged imgs" style="padding:10px">
	<!-- START BLOCK : node-img-->
	<a href="/gallery/{uid}/{id}"><img src="{static-server}/{thb}" alt="Attēls {title}" /></a>
	<!-- END BLOCK : node-img-->
</p>
<div class="c"></div>
<!-- END BLOCK : tags-images-->

<!-- START BLOCK : tags-miniblogs-->
<h2>Miniblogi</h2>
<ul class="taged">
	<!-- START BLOCK : tags-articles-node-mb-->
	<li><a href="/say/{uid}/{id}-{url}">{text}</a></li>
	<!-- END BLOCK : tags-articles-node-mb-->
</ul>
<!-- END BLOCK : tags-miniblogs-->

<!-- START BLOCK : tags-groups-->
<h2>Grupas</h2>
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

