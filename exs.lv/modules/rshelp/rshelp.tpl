<!-- INCLUDE BLOCK : sub-template --> 

<!-- START BLOCK : tasks -->
<h1 class="content-title">RuneScape Tasks</h1>
<div class="rs-intro">
  <img id="trollis" src="/bildes/runescape/intro/troll.png" title="" alt="">
  <p>Tasks, agrāk zināmi kā 'Achievement Diaries', sastāv no izaicinājumu un uzdevumu sērijām, kuras spēlētājam liek veikt spēles NPC, 
		lai pārbaudītu tā zināšanas un iemaņas attiecīgajā teritorijā. 
		Katras teritorijas uzdevumi tiek iedalīti 4 sarežģītības līmeņos: vieglie, vidēji grūtie, sarežģītie un ļoti sarežģītie (elite). 
		Jo tālāk uzdevumu sarakstā spēlētājs tiek, jo grūtāki paliek veicamie uzdevumi, turklāt tie prasa aizvien augstākas prasmes, 
		sarežģītākus kvestus un plašākas zināšanas.</p>
  <p>Centīgākie pildītāji nepaliek bez atalgojuma!</p>
</div>
<div class="rs_info">
  <img src="/bildes/runescape/tasks.png" title="" alt="">
  Sadaļā esošās pamācības ir ļoti novecojušas un īsti neatbilst pašreizējai situācijai spēlē! Palīdzi atjaunot tās! 
</div>
<div class="clearfix"></div>

<div style="margin-top:20px">
	<!-- START BLOCK : tasks-block -->
	<div class="tasks-block{newline}">
		<div class="tasks-title">{class_title}</div>
		<img src="/bildes/runescape/tasks/{class_img}" title="{class_title}" alt="">
		<ul>
			<!-- START BLOCK : task -->
			<li><a class="page" href="/read/{page_strid}">{page_title}</a></li>
			<!-- END BLOCK : task-->
		</ul>
	</div>
	<!-- END BLOCK : tasks-block -->
	<div class="clearfix"></div>

	<h1 class="content-title">Lapā iztrūkstošās teritorijas</h1>
	<!-- START BLOCK : tasks-not -->
	<div class="tasks-block{newline}">
		<div class="tasks-title">{class_title}</div>
		<img src="/bildes/runescape/tasks/{class_img}" title="{class_title}" alt="">
	</div>
	<!-- END BLOCK : tasks-not -->
</div>
<!-- END BLOCK : tasks -->

<!-- START BLOCK : guilds -->
<h1 class="content-title">RuneScape Ģildes</h1>
<div class="rs-intro" style="margin-bottom:20px">
	<img id="wise-old-man" src="/bildes/runescape/intro/wise-old-man.png" title="Wise Old Man" alt="">
	<p>Ģildes ir īpaši iekārtotas ēkas, kurās var iekļūt tikai attiecīgajā prasmē sevišķi iepratušies spēlētāji, 
		ja tie sasnieguši noteiktu prasmes līmeni, izpildījuši grūtu kvestu vai sakrājuši noteiktu Quest Points apjomu.</p> 
	<p style="margin-bottom:7px">Katrai ģildei ir savas unikālās iespējas, ieskaitot vieglāku piekļuvi rīkiem un resursiem, kas veltīti attiecīgajai prasmei, 
		un veikaliem, kuros pārdod citur nenopērkamus priekšmetus.</p>
</div> 
<!-- START BLOCK : guild -->
<div class="tasks-block{newline}">
	<div class="tasks-title">{page_title}{rspage_members_only}{rspage_is_old}</div>
	<a href="/read/{page_strid}"><img src="/bildes/runescape/guilds/{rspage_img}" title="{page_title}" alt=""></a>
	<p><span class="guild-strong">Koordinātas:</span> {rspage_location}</p>
	<p style="padding-bottom:5px"><span class="guild-strong">Prasības:</span> {rspage_extra}</p>
</div>
<!-- END BLOCK : guild -->
<!-- START BLOCK : guilds-not -->
<div class="tasks-block">
	<div class="tasks-title">
		<img class="guild-icon" src="/bildes/runescape/other.png" alt=""> Citi sadaļas raksti</div>
	<img src="/bildes/runescape/guilds/other.png" title="Citi ģilžu raksti" alt="">
	<ul>
		<!-- START BLOCK : guild-page -->
		<li><a class="page" href="/read/{page_strid}">{page_title}</a></li>
		<!-- END BLOCK : guild-page -->
	</ul>
</div>
<!-- END BLOCK : guilds-not -->
<!-- END BLOCK : guilds -->                                                            

<!-- START BLOCK : rshelp-list -->
<h1 class="rs_content_title title-margin">{category-title}</h1>
<table class="rslist">
	<tr class="listhead">
		<td style="width:20px">&nbsp;</td>
		<td style="width:370px">Raksts</td>
		<td style="width:200px;text-align:center">Raksta autors</td>
	</tr>
	<!-- START BLOCK : rshelp-listitem -->
	<tr>                 
		<td><img src="/bildes/runescape/page.png" style="vertical-align:middle" alt=""></td>
		<td><a href="/read/{strid}">{title}</a></td>
		<td class="center">{author}</td>
	</tr>
	<!-- END BLOCK : rshelp-listitem -->
</table>
<!-- END BLOCK : rshelp-list -->


<!-- START BLOCK : runescape-mainpage -->
<div id="rswelcome">
  <div id="left-col">
    <a href="/kvestu-pamacibas">
      <div id="quests-banner" class="sm_banner upper-image">
        <p class="bannertitle">Kvesti / Minikvesti</p>
      </div>
    </a>
    <a href="/minispeles">
      <div id="minigames-banner" class="sm_banner">
        <p class="bannertitle">Minispēles</p>
      </div>
    </a>
  </div>
  <div id="center-col">
    <img src="/bildes/rs/intro/main-large.png" />
  </div>
  <div id="right-col">    
    <a href="/prasmes">
      <div id="skills-banner" class="sm_banner upper-image">
        <p class="bannertitle">Prasmes</p>
      </div>
    </a>
    <a href="/celvezi">
      <div id="areas-banner" class="sm_banner">
        <p class="bannertitle">Ceļveži</p>
      </div>
    </a>
  </div>
</div>
<!-- START BLOCK : rs-banner-menu-->
<div style="clear:both;width:100%">
	<ul id="rsmenu">
		<li id="left-border"></li>
		<li>Minikvesti</li>
		<li>Medīšana</li>
		<li>D&D</li>
		<li>padomi</li>
		<li id="right-border"></li>
	</ul>
</div>
<!-- END BLOCK : rs-banner-menu-->

<div style="clear:both;"></div>

<!-- START BLOCK : rsarticles-->
<h1 class="rs_content_title">Jaunākie RuneScape raksti</h1>
<ul id="rsarticles">
	<!-- START BLOCK : rsarticle-->
	<li>
		<h1 class="atc-title"><a href="/read/{strid}">{title}</a></h1>
		<div class="atc-info">
			<span class="atc-right">
				komentāri: <span class="atc-number">{posts}</span> 
				| skatījumi: <span class="atc-number">{views}</span>
			</span>
			<span class="atc-left"><a href="{aurl}">{author}</a> @ {date}</span>
		</div>
		<!-- START BLOCK : rsarticle-avatar-->
		<div class="atc-avatar-outer">
			<img class="atc-avatar" src="/{image}" alt="{alt}" />
		</div>
		<!-- END BLOCK : rsarticle-avatar-->
		<div class="atc-intro">
			{intro} <a href="/read/{strid}" class="read-more">Lasīt&nbsp;tālāk&nbsp;&raquo;</a>
		</div>
	</li>
	<!-- END BLOCK : rsarticle-->
</ul>
<ul class="rspages">
	<!-- START BLOCK : all-rs-pages -->
	{all-pages}
	<!-- END BLOCK : all-rs-pages -->
</ul>
<!-- END BLOCK : rsarticles--> 

<!-- END BLOCK : runescape-mainpage -->


<!-- START BLOCK : rs-list-articles-->
<h2>{articles-title}</h2>
<ul id="mainlist">
	<!-- START BLOCK : rs-list-articles-node-->
	<li><h3>{cat}: <a href="{node-url}">{articles-node-title}</a></h3>
		<ul class="article-info">
			<li class="date">{articles-node-date}</li>
			<li class="comments"><a href="{node-url}#comments">{articles-node-posts} komentāri</a></li>
			<li class="profile"><a href="{aurl}">{articles-node-author}</a></li>
			<li class="views">skatīts {articles-node-views}x</li></ul>
		<div class="c"></div>
		<!-- START BLOCK : rs-list-articles-node-avatar-->
		<img class="av" src="/{node-avatar-image}" alt="{node-avatar-alt}" />
		<!-- END BLOCK : rs-list-articles-node-avatar-->
		<div style="padding: 5px 0">{articles-node-intro} <a href="{node-url}" class="read-more">Lasīt&nbsp;tālāk&nbsp;&raquo;</a></div>
		<div class="c"></div></li>
	<!-- END BLOCK : rs-list-articles-node-->
</ul>
<div class="c"></div>
<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
<!-- END BLOCK : rs-list-articles-->