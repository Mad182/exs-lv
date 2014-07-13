<!-- START BLOCK : minigames -->

<!-- START BLOCK : mg-intro-text -->
<div class="rs-intro">
    <span class="vc-ghost-item"></span>
	<img id="logo-minigames" class="vc-item" src="/bildes/runescape/intro/banner-imp.png">
    <p class="vc-item" style="max-width:85%">   
		<strong>Minispēles</strong> (savulaik sauktas par <strong>Aktivitātēm</strong>) ir spēles, kurās viens vai vairāki spēlētāji kopīgiem spēkiem cenšas sasniegt kādu mērķi. Dažreiz šajās minispēlēs spēlētājs var saņemt atalgojumu par paveikto - vai nu XP punktus, vai arī kādu priekšmetu.<br>&nbsp;&nbsp;&nbsp;Tās RuneScape kartē ir atzīmētas ar sarkanu zvaigzni, kas ievilkta aplī. Atšķirībā no kvestiem un minikvestiem, šīs spēles var atkārtot daudz reižu.
	</p>
</div>
<!-- END BLOCK : mg-intro-text -->

<!-- START BLOCK : dd-intro-text -->
<div class="rs-intro">
    <span class="vc-ghost-item"></span>
	<img id="logo-distractions" class="vc-item" src="/bildes/runescape/intro/phoenix.png">
    <span class="vc-item" style="max-width:80%">
        <p>Šīs aktivitātes, dažkārt apzīmētas ar <strong>D&amp;D</strong>, ir nelielu notikumu sērija, kas no spēlē ieviestajām minispēlēm atšķiras ar to, ka tās vai nu pārvietojas apkārt pa Gīlinoru, vai tām ir ierobežots laiks, kad un cik ilgi tās var pildīt, vai arī tās vieno abi faktori.</p>
        <p style="margin-bottom:-8px">Meteorīta ietriekšanās zemē bankas apmeklējuma laikā, spiegojoši pingvīni, kas noslēpušies ziedošajos krūmos, vai apņēmīgas koka saknes, kas saķer nejaušu garāmgājēju aiz pēdām un nelaiž vaļā, ir tieši tas, kas ietilpst tajās!</p>
    </span>
</div>
<!-- END BLOCK : dd-intro-text -->

<!-- START BLOCK : minigames-statistics -->
<ul class="stats-block">
    <li><img src="/bildes/runescape/star-f2p.png" title="Bezmaksas versijas minispēle">&nbsp;{f2p-only}</li>
    <li><img src="/bildes/runescape/star-p2p.png" title="Maksas versijas minispēle">&nbsp;{p2p-only}</li>
    <li><img src="/bildes/runescape/safe.png" title="Droša minispēle">&nbsp;{safe}</li>
    <li><img src="/bildes/runescape/unsafe.png" title="Bīstama minispēle">&nbsp;{unsafe}</li>
</ul>
<!-- END BLOCK : minigames-statistics -->

<!-- START BLOCK : no-guides-found -->
<p class="simple-note">
    Neizdevās atrast nevienu šīs sadaļas rakstu.
</p>
<!-- END BLOCK : no-guides-found -->

<!-- START BLOCK : minigames-list -->
<table class="rslist minigame-list">
	<tr class="listhead">
		<td style="width:100px">&nbsp;</td>
		<td style="width:600px">
            Aktivitātes apraksts
            <span style="float:right;margin-right:7px">Vieta</span>
        </td>
	</tr>
	<!-- START BLOCK : minigame -->
	<tr>
		<td{cluetip}>{avatar}</td>
		<td>
			<p>
                {title}
                <!-- START BLOCK : p2p-only -->
                <img src="/bildes/runescape/star-p2p-small.png" title="Pieejama tikai maksas spēlētājiem" alt="">
                <!-- END BLOCK : p2p-only -->
                <!-- START BLOCK : unsafe-minigame -->
                <img src="/bildes/runescape/unsafe-small.png" title="Bīstama minispēle ar iespēju mirt" alt="">
                <!-- END BLOCK : unsafe-minigame -->
                <span>{starting_point}</span>
            </p>
			<p>{description}</p>
        </td>	
	</tr>
	<!-- END BLOCK : minigame -->
</table>
<!-- END BLOCK : minigames-list -->

<!-- START BLOCK : minigames-placeholders -->
<h1 class="content-title">Lapā vēl iztrūkstošās {type} pamācības</h1>
<table class="rslist">
	<tr class="listhead">
		<td style="width:80px"></td>
		<td style="width:250px"></td>
		<td style="width:150px"></td>
	</tr>
	<!-- START BLOCK : minigame-placeholder -->
	<tr>
		<td class="mg-avatar">
			<img src="/bildes/runescape/empty-2.png" title="Pamācība iztrūkst" alt="">
		</td>
		<td style="width:70%">
			<p class="mg-title"><a href="/write">{title}</a></p>
			<p>Oops! Šāda pamācība ir nomaldījusies lapas rakstu labirintos un nav atrodama. Vai palīdzēsi <a href="/write">papildināt</a> exs.lv datu krātuvi ar šo rakstu latviešu valodā? {link}</p>
		</td>	
		<td style="width:18%"></td>	
	</tr>
	<!-- END BLOCK : minigame-placeholder -->
</table>
<!-- END BLOCK : minigames-placeholders -->

<!-- END BLOCK : minigames -->
