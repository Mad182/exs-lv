<!-- START BLOCK : quests-intro -->
<h1 class="content-title no-margin">RuneScape kvesti un minikvesti</h1>
<div id="intro-quests" class="rs-intro">
	<img src="{intro-image}" title="" alt="">
	<p>Neizskaidrojami atgadījumi, nodevīgas idejas un postoši spēki ir tie, kas valda pār Gīlinoras iemītnieku šķietami mierīgo ikdienu. 
    Tomēr aiz mierpilnajām sejām un glaimojošajiem vārdiem briest daudz nopietnāki draudi, kas var ietekmēt ikvienas radības turpmāko likteni. 
    Pār zemi klīst drūmas domas, gaisu pāršķeļ stindzinoši kliedzieni, bet ēnās milst naids un atriebība.</p> 
	<p>Šur tur krodziņos uzklīst arī pa kādam izslāpušam dēkainim, kura mēle nakts gaitā atraisās, un tādās reizēs ciematu iemītnieki kāri ķer katru vārdu par nedienām un raižpilnām zīmēm citviet. 
    Kā rādās, pēc asinīm izslāpuši nezvēri, prasmīgi šamaņi un varaskāri dēmoni nav vienīgā nelaime. 
    Vēl jau eksistē tie nešķīsteņi, kas uzglūn domās un sapņos...</p>  
</div>
<ul id="quests-tabs">
  <li class="nohover">Rādīt:</li>  
  <li><a href="/kvestu-pamacibas">sērijas</a></li>
  <li><a href="/p2p-kvesti">P2P</a></li>
  <li><a href="/f2p-kvesti">F2P</a></li>
  <li><a href="/mini-kvesti">minikvestus</a></li>
</ul>
<div class="clearfix"></div>
<!-- END BLOCK : quests-intro -->

<!-- START BLOCK : quests-series -->
<h1 class="content-title">RuneScape kvestu sērijas</h1>
<div id="quests-series">
	<!-- START BLOCK : single-series -->
	<div class="series"{newline}>
		<div class="series-title">{series_title}</div>
		<img src="/bildes/runescape/series/{series_img}" title="{page_title}" alt="{page_title}">
		<!-- START BLOCK : series-quest -->
		{page_title}
		<!-- END BLOCK : series-quest-->
	</div>
	<!-- END BLOCK : single-series -->
</div>
<div class="clearfix"></div>
<!-- END BLOCK : quests-series -->

<!-- START BLOCK : quests-outro -->
<h1 class="content-title" style="border-bottom:none">RuneScape kvestu statistika</h1>
<div id="quests-outro">
	<div style="float:left;width:60%">

		<!-- START BLOCK : quests-stats -->
		<p class="facts-title"><b>Statistika</b></p>
		<div style="width:350px"> 
			<table class="stats-left">
				<tr><td class="left">P2P kvesti:</td><td>{p2p}</td></tr>
				<tr><td class="left">F2P kvesti:</td><td>{f2p}</td></tr>
				<tr class="space"><td class="left">2014. gadā:</td><td>{2014}</td></tr>
				<tr><td class="left">13. gadā:</td><td>{2013}</td></tr>
				<tr><td class="left">12. gadā:</td><td>{2012}</td></tr>
				<tr><td class="left">11. gadā:</td><td>{2011}</td></tr>
				<tr><td class="left">10. gadā:</td><td>{2010}</td></tr>
				<tr><td class="left">Senāk:</td><td>{older}</td></tr>  
			</table>
			<img id="balance" src="/bildes/runescape/balance.png" title="Balance Elemental">
			<table class="stats-right">
				<tr><td class="left">Īpašs (kā RFD):</td><td>{special}</td></tr>
				<tr><td class="left">Grandmaster:</td><td>{grandmaster}</td></tr>
				<tr><td class="left">Master:</td><td>{master}</td></tr>
				<tr><td class="left">Intermediate:</td><td>{intermediate}</td></tr>
				<tr><td class="left">Easy:</td><td>{easy}</td></tr>
				<tr><td class="left">Novice:</td><td>{novice}</td></tr>
				<tr class="space"><td class="left">Minikvesti:</td><td>{miniquests}</td></tr>
			</table>
		</div>
		<!-- END BLOCK : quests-stats -->

		<!-- START BLOCK : quests-facts -->
		<p style="margin-top:15px" class="facts-title"><b>Fakti par RuneScape kvestiem</b></p>
		<ul id="quests-facts">
			<li>Pirms EoC visu kvestu izpildīšanai bija nepieciešams vismaz 85. combat līmenis, bet, izejot tos, spēlētāja Combat pieauga līdz 105. līmenim.</li>
			<li>Spēlētāji, kuri ir izgājuši visus kvestus, no Wise Old Man par 99,000gp var nopirkt Quest Point Cape - vienu no Capes of Accomplishment.</li>
			<li>50. kvests - <a href="/read/legends-quest-2">Legends' Quest</a>, 100. kvests - <a href="/read/recipe-for-disaster-3">Recipe for Disaster</a>, 150. kvests - <a href="/read/chosen-commander-the">The Chosen Commander</a>.</li>
			<li><a href="/read/recipe-for-disaster-3">Recipe for Disaster</a> patiesībā sastāv no daudziem apakškvestiem.</li>
			<li>Laika gaitā daži kvesti tikuši mainīti. Piemēram, Unstable Foundations un Learning the Ropes no spēles ir izņemti, Romeo & Juliet aizstāts ar <a href="/read/gunnar-s-ground">Gunnar's Ground</a>, bet <a href="/read/sheep-shearer">Sheep Shearer</a> atstāts kā minikvests.</li>
			<li>Jagex apgalvo, ka <a href="/read/cook-s-assistant">Cook's Assistant</a> ir pats pirmais kvests, kuru viņi bija sākuši izstrādāt.</li>
		</ul>
		<!-- END BLOCK : quests-facts -->

	</div>
	<!-- START BLOCK : max-skills -->
	<div id="quests-skills">
		<table class="rslist skill-level">
			<tr class="listhead">
				<td class="right" style="width:100px">Prasme</td>
				<td style="width:20px"></td>
				<td style="width:150px;text-align:left">Kvests</td>
			</tr>
			<!-- START BLOCK : skill-requirement -->
			<tr>
				<td class="right">{skill}</td>
				<td class="center">{level}</td>
				<td class="left">{page_title}</td>
			</tr>
			<!-- END BLOCK : skill-requirement -->
		</table>
	</div>
	<!-- END BLOCK : max-skills -->
</div>
<!-- END BLOCK : quests-outro -->

<!-- START BLOCK : p2p-quests -->
<h1 class="content-title">RuneScape maksas kvesti</h1>
<table class="rslist questlist">
	<tr class="listhead">
		<td style="width:15px;">&nbsp;</td>
		<td style="width:500px;">&nbsp;</td>
		<td style="width:140px;" class="center">Autors</td>
		<td style="width:20px">&nbsp;</td>	
	</tr>
	<!-- START BLOCK : p2p-quest -->
	<tr{border}>
		<td class="letter">{letter}</td>  
		<td><a href="/read/{page_strid}">{page_title}</a></td>
		<td class="center">{page-author}</td>
		<td style="position:relative">{warning}</td>
	</tr>
	<!-- END BLOCK : p2p-quest -->
</table>
<!-- START BLOCK : questlist-placeholders -->
<h1 class="content-title">Vēl neuzrakstītās pamācības</h1>
<table class="rslist questlist">
	<tr class="listhead">
		<td style="width:15px;">&nbsp;</td>
		<td style="width:200px;">&nbsp;</td>
		<td style="width:440px;">&nbsp;</td>
	</tr>
	<!-- START BLOCK : quest-ph -->
	<tr>
		<td>&nbsp;</td>  
		<td><a href="#">{page_title}</a></td>
		<td>Šāda pamācība lapā iztrūkst. Lai tādu izveidotu, dodies uz <a href="/write">šo lapu</a>.</td>
	</tr>
	<!-- END BLOCK : quest-ph -->
</table>
<!-- END BLOCK : questlist-placeholders -->
<!-- END BLOCK : p2p-quests --> 

<!-- START BLOCK : other-quests -->
<h1 class="content-title">{extended-title}</h1>
<table class="other-quests">
	<!-- START BLOCK : other-quest -->
	<tr>
		<td class="quest-image">
			<a href="/read/{page_strid}">{page_image}</a>
		</td>
		<td style="position:relative">
			<p class="mq-title">
				<a href="/read/{page_strid}">{page_title}</a> @ {page_date} no {page_author} {warning}
			</p>
			<p style="font-size: 11px;">{rspage_description}</p>
		</td>
	</tr>
	<!-- END BLOCK : other-quest -->
</table>
<!-- START BLOCK : extended-placeholders -->
<h1 class="content-title">Lapā vēl iztrūkstošās {needed} pamācības</h1>
<table class="ext-quests">
	<!-- START BLOCK : extended-ph -->
	<tr>
		<td class="exists-not">
			<a href="/write"><img src="/bildes/runescape/empty.png" title="" alt=""></a>
		</td>
		<td>
			<p class="mq-title facts-title"><a href="#">{title}</a></p>
			<p>Oops! Šāda pamācība ir nomaldījusies lapas rakstu labirintos un nav atrodama. Vai palīdzēsi <a href="/write">papildināt</a> exs.lv datu krātuvi ar šo rakstu latviešu valodā? {link}</p>
		</td>
	</tr>
	<!-- END BLOCK : extended-ph -->
</table>
<!-- END BLOCK : extended-placeholders -->
<!-- END BLOCK : other-quests -->