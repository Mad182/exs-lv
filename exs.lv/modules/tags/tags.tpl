<!-- START BLOCK : tags-current-->
<h1>Tags &quot;{tag-name}&quot;</h1>
<!-- START BLOCK : tags-description-->
<p>{description}</p>
<!-- END BLOCK : tags-description-->

{ad-468}

<!-- START BLOCK : tags-articles-->
<h3>Raksti</h3>
<ul class="taged" id="taged-articles">
	<!-- START BLOCK : tags-articles-node-->
	<li><strong><a href="{url}">{title}</a></strong> <small>no <a href="{aurl}">{author}</a></small><p style="font-size:90%;padding: 0 0 6px;margin:0">{text}</p></li>
	<!-- END BLOCK : tags-articles-node-->
</ul>
<!-- END BLOCK : tags-articles-->


<!-- START BLOCK : tags-images-->
<h3>Attēli</h3>
<p class="taged imgs">
	<!-- START BLOCK : node-img-->
	<a href="/gallery/{uid}/{id}"><img src="http://img.exs.lv/{thb}" alt="Attēls {title}" /></a>
	<!-- END BLOCK : node-img-->
</p>
<div class="c"></div>
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
