<!-- START BLOCK : list-articles-->
<h2>{articles-title}</h2>
<!-- START BLOCK : list-articles-node-->
<p>
	<!-- START BLOCK : list-articles-node-avatar-->
	<img class="av" src="http://exs.lv/{node-avatar-image}" alt="" />
	<!-- END BLOCK : list-articles-node-avatar-->
  <b><a href="{node-url}">{articles-node-title}</a></b>
	<br />{articles-node-posts} komentāri | <a href="{aurl}">{articles-node-author}</a><br />
	{articles-node-intro}
	<div class="c"></div>
</p>
<!-- END BLOCK : list-articles-node-->
<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
<!-- END BLOCK : list-articles-->

<!-- START BLOCK : list-articles-short-->
<h2>{articles-title}</h2>
<ul>
<!-- START BLOCK : list-articles-short-node-->
<li><a href="{node-url}">{articles-node-title}</a> no <a href="{aurl}">{articles-node-author}</a></li>
<!-- END BLOCK : list-articles-short-node-->
</ul>
<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
<!-- END BLOCK : list-articles-short-->

<!-- START BLOCK : list-cheats-->
<h2>{articles-title}</h2>
<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
<ul>
<!-- START BLOCK : list-cheats-node-->
<li><a href="{node-url}">{articles-node-title}</a></li>
<!-- END BLOCK : list-cheats-node-->
</ul>
<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
<!-- END BLOCK : list-cheats-->


<!-- START BLOCK : list-cheats-->
<h2>{articles-title}</h2>
<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
<ul>
<!-- START BLOCK : list-cheats-node-->
<li><a href="{node-url}">{articles-node-title}</a></li>
<!-- END BLOCK : list-cheats-node-->
</ul>
<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
<!-- END BLOCK : list-cheats-->



<!-- START BLOCK : list-forum-->
<h2>
	<a class="add-topic" href="/?c={parentid}&amp;cat={articles-catid}#new">Izveidot jaunu tēmu</a>
	{articles-title}
</h2>
<table id="forum">
<tr>
  <th colspan="2" class="first">Tēmas</th>
  <th>Autors</th>
  <th>Atbildes</th>
  <th class="last">Datums</th>
</tr>
<!-- START BLOCK : list-forum-node-->
<tr>
<td></td>
<td><h3><a href="{node-url}">{articles-node-title}</a></h3></td>
<td><a href="{aurl}">{articles-node-author}</a></td>
<td class="center">{articles-node-posts}</td>
<td class="last">{articles-node-date}</td>
</tr>
<!-- END BLOCK : list-forum-node-->
</table>
<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
<!-- END BLOCK : list-forum-->