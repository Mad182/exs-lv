<!-- INCLUDE BLOCK : sub-template -->


<!-- START BLOCK : no-pages-found -->
<p class="simple-note">
    Neizdevās atlasīt nevienu šīs sadaļas rakstu.
</p>
<!-- END BLOCK : no-pages-found -->


<!-- START BLOCK : rshelp-list -->
<h2 style="margin-top:-20px">{category-title}</h2>
    <table class="rslist">
        <tr class="listhead">
            <td style="width:20px">&nbsp;</td>
            <td style="width:370px">Raksts</td>
            <td style="width:200px;text-align:center">Raksta autors</td>
        </tr>
        <!-- START BLOCK : rshelp-listitem -->
        <tr>                 
            <td><img src="/bildes/runescape/li.png" style="vertical-align:middle" alt=""></td>
            <td><a href="/read/{strid}">{title}</a></td>
            <td class="center">{author}</td>
        </tr>
        <!-- END BLOCK : rshelp-listitem -->
    </table>
    <!-- START BLOCK : show-pager -->
    <div class="c"></div>
    <p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
    <!-- END BLOCK : show-pager -->
<!-- END BLOCK : rshelp-list -->


<!-- START BLOCK : rs-articles -->
<h1>{articles-title}</h1>
<ul id="mainlist">
	<!-- START BLOCK : rs-article -->
	<li>
		<h3><a href="{url}">{title}</a></h3>
		<ul class="article-info">
			<li class="date">{date}</li>
			<li class="comments"><a href="{node-url}#comments">{posts} komentāri</a></li>
			<li class="profile"><a href="{aurl}">{author}</a></li>
			<li class="views">skatīts {views}x</li>
		</ul>
		<div class="c"></div>
		<!-- START BLOCK : article-avatar -->
		<img class="av" style="max-height:70px" src="http://exs.lv/{image}">
		<!-- END BLOCK : article-avatar -->
		<div style="padding: 5px 0 10px">
            {intro} 
            <a href="{url}" class="read-more">Lasīt&nbsp;tālāk&nbsp;&raquo;</a>
        </div>
		<div class="c"></div>
	</li>
	<!-- END BLOCK : rs-article -->
</ul>
<div class="c"></div>
<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
<!-- END BLOCK : rs-articles -->
