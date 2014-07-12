<!-- START BLOCK : list-tabs -->
<ul class="tabs">
    <li><a class="{tab-series}" href="/kvestu-pamacibas">Sērijas</a></li>
    <li><a class="{tab-p2p}" href="/p2p-kvesti">P2P</a></li>
    <li><a class="{tab-f2p}" href="/f2p-kvesti">F2P</a></li>
    <li><a class="{tab-miniquests}" href="/mini-kvesti">Minikvesti</a></li>
    <li><a class="{tab-stats}" href="/kvestu-pamacibas/stats">Statistika</a></li>
    <li><a class="{tab-facts}" href="/kvestu-pamacibas/facts">Fakti</a></li>
    <li><a class="{tab-reqs}" href="/kvestu-pamacibas/skill-reqs">Prasmju prasības</a></li>
</ul>
<!-- END BLOCK : list-tabs -->


<!-- START BLOCK : series-intro-text -->
<div class="rs-intro">
    <span class="vc-ghost-item"></span>
	<img id="khazard" class="vc-item" src="{intro-image}" alt="">
	<p class="vc-item" style="max-width:85%">Neizskaidrojamas parādības un postoši spēki ir tie, kas satricina Gīlinoras iemītnieku mierīgo ikdienu. Pār zemi klīst drūmas domas, gaisu pāršķeļ stindzinoši kliedzieni, bet ēnās milst naids un atriebība.<br><span style="position:relative;top:4px">&nbsp;&nbsp;&nbsp;Šur tur krodziņos uzklīst arī pa kādam izslāpušam dēkainim, kura mēle nakts gaitā atraisās, un tādās reizēs ciematu iemītnieki kāri ķer katru vārdu par nedienām citviet. Kā rādās, pēc asinīm izslāpuši nezvēri, prasmīgi šamaņi un varaskāri dēmoni nav vienīgā nelaime. Vēl jau eksistē arī tie nešķīsteņi, kas uzglūn domās un sapņos...</span></p>  
</div>
<!-- END BLOCK : series-intro-text -->

<!-- START BLOCK : no-series-found -->
<p class="simple-note">Nav izveidota neviena kvestu sērija.</p>
<!-- END BLOCK : no-series-found -->

<!-- START BLOCK : series-block -->
<p class="simple-note" style="margin-bottom:20px;text-indent:7px">Šis sadalījums pa sērijām ir neoficiāls, arī liela daļa kvestu iederas vairākās sērijās, savukārt pašas sērijas nereti viena ar otru pārklājas vai papildina kādu citu, tāpēc atspoguļotais kādam var nešķist pilnīgi pareizs.</p>
<div id="series">
	<!-- START BLOCK : single-series -->
	<div class="series"{newline}>
		<h3>{series_title}</h3>
		<img src="/bildes/runescape/series/{img}" title="{series_title}" alt="{series_title}">
		<!-- START BLOCK : series-quest -->
		{quest}
		<!-- END BLOCK : series-quest-->
	</div>
	<!-- END BLOCK : single-series -->
</div>
<div class="clearfix"></div>
<!-- END BLOCK : series-block -->


<!-- START BLOCK : p2p-quests-block -->
<p class="simple-note">Šajā sarakstā uzskaitīti tie kvesti, kas pieejami tikai spēles maksas versijā (pay-to-play).<br>Atsevišķas iztrūkstošās pamācības var meklēt <a href="/padomi">arhīvā</a>.</p>
<!-- START BLOCK : no-p2p-quests -->
<p class="simple-note">Neizdevās atlasīt sadaļas rakstus. Tādu, iespējams, nemaz nav!</p>
<!-- END BLOCK : no-p2p-quests -->
<!-- START BLOCK : p2p-quests -->
<table class="rslist">
	<tr class="listhead">
		<td style="width:15px">&nbsp;</td>
		<td style="width:500px">&nbsp;</td>
		<td style="width:140px" class="center">Autors</td>
	</tr>
	<!-- START BLOCK : p2p-quest -->
	<tr{border}>
		<td class="letter">{letter}</td>  
		<td><a{extra} href="{strid}">{title}</a></td>
		<td class="center">{author}</td>
	</tr>
	<!-- END BLOCK : p2p-quest -->
</table>
<!-- END BLOCK : p2p-quests -->
<!-- END BLOCK : p2p-quests-block --> 


<!-- START BLOCK : common-quests -->
<!-- START BLOCK : no-quests-found -->
<p class="simple-note">Neizdevās atlasīt sadaļas rakstus. Tādu, iespējams, nemaz nav!</p>
<!-- END BLOCK : no-quests-found -->
<!-- START BLOCK : quests-found -->

    <table class="simple-quests">
        <!-- START BLOCK : common-quest -->
        <tr>
            <td><a href="{strid}">{image}</a></td>
            <td>
                <p><a{style} href="{strid}"{clue}>{title}</a> {date} {author}</p>
                {description}
            </td>
        </tr>
        <!-- END BLOCK : common-quest -->
    </table>
<!-- END BLOCK : quests-found -->
<!-- END BLOCK : common-quests -->


<!-- START BLOCK : stats-block -->
<p class="simple-note">Šajā sadaļā aplūkojama ar kvestiem saistīta statistika</p>
<!-- START BLOCK : no-stats-found -->
<p class="simple-note">
    Statistika nav pieejama.
</p>
<!-- END BLOCK : no-stats-found -->
<!-- START BLOCK : stats-found -->
<div class="quest-stats-block">
    <span class="vc-ghost-item"></span>
    <table class="stats-left vc-item">
        <tr><td class="left">P2P kvesti:</td><td>{p2p}</td></tr>
        <tr><td class="left">F2P kvesti:</td><td>{f2p}</td></tr>
        <tr class="space"><td class="left">2014. gadā:</td><td>{14}</td></tr>
        <tr><td class="left">13. gadā:</td><td>{13}</td></tr>
        <tr><td class="left">12. gadā:</td><td>{12}</td></tr>
        <tr><td class="left">11. gadā:</td><td>{11}</td></tr>
        <tr><td class="left">10. gadā:</td><td>{10}</td></tr>
        <tr><td class="left">Senāk:</td><td>{older}</td></tr>  
    </table>
    <img id="balance" class="vc-item" src="/bildes/runescape/intro/balance.png" title="Balance Elemental">
    <table class="stats-left vc-item">
        <tr><td class="left">Īpašs (kā RFD):</td><td>{special}</td></tr>
        <tr><td class="left">Grandmaster:</td><td>{grandmaster}</td></tr>
        <tr><td class="left">Master:</td><td>{master}</td></tr>
        <tr><td class="left">Experienced:</td><td>{experienced}</td></tr>
        <tr><td class="left">Intermediate:</td><td>{intermediate}</td></tr>
        <tr><td class="left">Novice:</td><td>{novice}</td></tr>
        <tr class="space"><td class="left">Minikvesti:</td><td>{miniquests}</td></tr>
    </table>
</div>
<!-- END BLOCK : stats-found -->
<div class="break"></div>
<!-- END BLOCK : stats-block -->


<!-- START BLOCK : facts-block -->
<p class="simple-note">
    Šajā sadaļā apkopoti dažādi ar kvestiem saistīti fakti
</p>
<div id="skills-facts" style="font-size:13px">
	<ul>
		<li>Pirms EoC visu kvestu izpildīšanai bija nepieciešams vismaz 85. combat līmenis, bet, izejot tos, spēlētāja Combat pieauga līdz 105. līmenim.</li>
        <li>Spēlētāji, kuri ir izgājuši visus kvestus, no Wise Old Man par 99,000gp var nopirkt Quest Point Cape - vienu no Capes of Accomplishment.</li>
        <li>50. kvests - <a href="/read/legends-quest-2">Legends' Quest</a>, 100. kvests - <a href="/read/recipe-for-disaster-3">Recipe for Disaster</a>, 150. kvests - <a href="/read/chosen-commander-the">The Chosen Commander</a>, 200. kvests - ?</li>
        <li><a href="/read/recipe-for-disaster-3">Recipe for Disaster</a> patiesībā sastāv no daudziem apakškvestiem.</li>
        <li>Laika gaitā daži kvesti tikuši mainīti. Piemēram, Unstable Foundations un Learning the Ropes no spēles ir izņemti, Romeo & Juliet aizstāts ar <a href="/read/gunnar-s-ground">Gunnar's Ground</a>, bet <a href="/read/sheep-shearer">Sheep Shearer</a> atstāts kā minikvests.</li> 
        <li>Jagex apgalvo, ka <a href="/read/cook-s-assistant">Cook's Assistant</a> ir pats pirmais kvests, kuru viņi bija sākuši izstrādāt.</li>
	</ul>
</div>
<div class="break"></div>
<!-- END BLOCK : facts-block -->


<!-- START BLOCK : skills-block -->
    <p class="simple-note">
        Šajā sadaļā parādīts augstākais līmenis katrā prasmē, kāds nepieciešams kādam no kvestiem. Sasniedzot šīs prasības, iespējams izpildīt visus kvestus.
    </p>
    <!-- START BLOCK : no-skills-found -->
    <p class="simple-note">
        Prasības nav pieejamas.
    </p>
    <!-- END BLOCK : no-skills-found -->
    <!-- START BLOCK : skills-found -->
    <table class="rslist skill-req">
        <tr class="listhead">
            <td class="right" style="width:130px">Prasme</td>
            <td style="width:80px"></td>
            <td style="width:220px;text-align:left">Kvests</td>
        </tr>
        <!-- START BLOCK : skill-requirement -->
        <tr>
            <td class="name" style="{style}">{title}</td>
            <td class="level">{level}</td>
            <td>{page_title}</td>
        </tr>
        <!-- END BLOCK : skill-requirement -->
    </table>
    <!-- END BLOCK : skills-found -->
    <div class="break"></div>
<!-- END BLOCK : skills-block -->
