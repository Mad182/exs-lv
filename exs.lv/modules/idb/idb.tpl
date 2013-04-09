<!-- START BLOCK : idb-navig -->
<div id="idb-navig">
	<a class="modbutton" href="/db"><img src="/modules/idb/images/magnifying_glass.png" title="Sākumlapa" alt="" /></a>
	<a class="modbutton" href="/db/unlisted"><img src="/modules/idb/images/pencil.png" title="lot of work :(" alt="" /></a>
    <a class="modbutton" href="/db/listed"><img src="/modules/idb/images/correct.png" title="Iztulkotie šķirkļi" alt="" /></a>	
	<!-- START BLOCK : navig-mods -->
	<a class="modbutton" style="position:relative" href="/db/stats"><img src="/modules/idb/images/3d_bar_chart.png" title="Lietotāju statistika" alt="" /></a>
	<a class="modbutton" href="/db/queue"><img src="/modules/idb/images/down.png" title="Pārskatāmi garadarbi" alt="" /></a>
	<!-- END BLOCK : navig-mods -->
	<!-- START BLOCK : navig-app -->
	<a class="modbutton" href="/db/myitems"><img src="/modules/idb/images/pen.png" title="Mani priekšmeti" alt="" /></a>
	<!-- END BLOCK : navig-app -->
	<a class="modbutton" href="/db/help"><img src="/modules/idb/images/Help.png" title="Tulkošanas vārdnīca" alt="" /></a>
	<div id="idb-amount"><span class="highlight">Paveiktais:</span> {amount} / 10308 ({percents}%)</div>
</div>
<!-- END BLOCK : idb-navig -->
<!-- START BLOCK : item-search-->
<div id="search_line">
	<form class="form jqsearch" action="/db/search/" method="get">	
		<img class="search_icon" src="/modules/idb/images/magnifier.png" title="Meklētājs" alt="" />
		<input class="search" name="q" value="{qstr}" tabindex="1" /><br />
		<input class="search_input button" type="submit" value="Meklēt" tabindex="2" /><br />
		<input id="search_ch" type="checkbox" name="checkbox" style="vertical-align:middle" {checked} /> 
		<label for="search_ch" class="search_lv">meklēt starp neiztulkotajiem šķirkļiem</label>
	</form>
</div>
<hr /><br />
<div id="idb-homepage" style="padding-left:20px">
<!-- START BLOCK : search-list -->
<ul class="item_list_main">
	<li class="list_main_title">{main_title}</li>
    <!-- START BLOCK : search-items -->
	<li><span class="list_nr">{counter}</span> <a href="/db/{strid}" title="{item}">{item}</a></li>
    <!-- END BLOCK : search-items -->
	<!-- START BLOCK : search-page-sm -->
	<li class="{block_class} pagerow">{page-nr}</li>
	<!-- END BLOCK : search-page-sm -->
</ul>
<!-- END BLOCK : search-list -->
</div>
<div id="searchload">
	<p>Lūdzu, pacietību! Priekšmeti tiek meklēti!</p>
</div>
<div id="searchpage"></div>
<!-- END BLOCK : item-search-->
<!-- START BLOCK : idb-content -->
<!-- START BLOCK : idb-help -->
<p id="idb-help-title" class="idb-red">Padomi priekšmetu šķirkļu tulkošanai, lai mēs pieturētos pie viena stila</p>
<a href="/page/46000-Izplatitakas_latviesu_valodas_lietojuma_kludas"># Izplatītākās latviešu valodas lietojuma kļūdas</a><br /><br />
<p style="font-size:11px;">
	Ieteicams izvairīties no 'tu' vai 'jūs' formas lietošanas. Piedomāt vairāk pie teikuma skanējuma.
	Tulkojot apbruņojumu, noteikti jāpieraksta klāt nepieciešamie līmeņi, lai to izmantotu.
	<br />Pagatavojams - attiecināms tikai uz kulinārijas lietām; ar priekšmetiem utt. jālieto <i>izgatavojams.</i><br /><br />
<table class="idb-table" style="font-size:11px;">
	<tr class="rowbg">
		<td style="width:200px;">Ne pārāk labs variants</td>
		<td style="width:200px;">Kā pareizāk lietot</td>
	</tr>
	<tr><td>Priekš skaistuma</td><td>Skaistumam</td></tr>
	<tr><td>iekš Lumbridge</td><td>Lumbridge pilsētā</td></tr>
	<tr><td>Nepieciešams 30 Attack</td><td>Nepieciešams vismaz 30. Attack līmenis</td></tr>
	<tr><td>Familiar</td><td>Sekotājs</td></tr>
	<tr><td>Savādāks</td><td>Citāds</td></tr>
	<tr><td>Savādāk</td><td>Citādi</td></tr>
	<tr><td>Level</td><td>Līmenis</td></tr>
	<tr><td>Boost</td><td>Palielinājums</td></tr>
	<tr><td>Ranged</td><td>Tālcīņa, ranged</td></tr>
	<tr><td>Melee</td><td>Tuvcīņa, melee</td></tr>
	<tr><td>Logs</td><td>Pagales</td></tr>
	<tr><td>Furnace</td><td>Krāsns, kausētava</td></tr>
	<tr><td>Smithing anvil</td><td>Kalēja lakta, lakta</td></tr>
	<tr><td>Skill</td><td>Prasme</td></tr>
	<tr><td>Enchant</td><td>Apburt, noburt</td></tr>
	<tr><td>Scroll</td><td>Tīstoklis</td></tr>
	<tr><td>Quest</td><td>Kvests</td></tr>
	<tr><td>Ore</td><td>Rūda</td></tr>
	<tr><td>Bar</td><td>Stienis, bar</td></tr>
	<tr><td>Pickaxe</td><td>Cērte, pickaxe</td></tr>
	<tr><td>LP, life points</td><td>dzīvības, dzīvības punkti, life points</td></tr>
	<tr><td>XP, expierence</td><td>Pieredzes punkti, pieredze, xp</td></tr>
	<tr><td>P2P, members</td><td>Maksājošie spēlētāji</td></tr>
	<tr><td>Special attack</td><td>Īpašais uzbrukums</td></tr>
	<tr><td>Ieroči tiek...</td><td>...nēsāti, lietoti</td></tr>
	<tr><td>Bruņas, drēbes...</td><td>...valkā, nēsā</td></tr>
</table>
<br /><br />
<!-- END BLOCK : idb-help -->
<!-- START BLOCK : list -->
<div id="load-list">
<div class="idb-title idb-red">{list-title}
	<!-- START BLOCK : list-pages -->
	<ul id="search-pages">{pagerow}</ul>
	<!-- END BLOCK : list-pages -->
</div>
<ul id="result-list">
	<!-- START BLOCK : single-item -->
	<li>
        <img src="/dati/idb/{img}" title="{item}" alt="" />
        <a href="/db/{strid}">{item}</a>
		<!-- START BLOCK : single-item-modedit -->
        <a style="float:right;" href="/db/{strid}/edit"><span class="idb-red">[ labot ]</span></a>
		<!-- END BLOCK : single-item-modedit -->
		<!-- START BLOCK : single-item-asg -->
        <a style="float:right;" href="#"><span class="idb-blue">[ iesniegts ]</span></a>
		<!-- END BLOCK : single-item-asg -->
	</li>
	<!-- END BLOCK : single-item -->
</ul>
<br />
<div id="idb_pages">
	<!-- START BLOCK : item-page -->
	<a class="list-page" href="/db/{type}/{page-link}">{page-nr}</a>&nbsp;&nbsp;
	<!-- END BLOCK : item-page -->
</div>
</div>
<!-- END BLOCK : list -->
<!-- START BLOCK : search-form -->
<div id="ssearch-block" style="clear:both;">
	<form id="ssearch-form" action="/db/ssearch" method="get">
		<input type="text" id="ssearch-input" style="width:250px" name="q" value="{qstr}" />
		<input type="submit" class="button" value="ok" />
	</form>
	<a class="close-results"><img class="close-form" src="/modules/idb/images/close.png" title="Aizvērt" alt="close" /></a>
	<div id="ssearch-results"></div>
	<div id="nextpage"></div>
	<div id="prevpage"></div>
</div>
<!-- END BLOCK : search-form -->
<!-- START BLOCK : itemview -->
<div class="borderline" style="clear:both;"></div>
<div id="item_info">
<table id="item_table">
	<tr class="item-header rowbg">
		<td colspan="4">
			<img src="/modules/idb/images/item.png" title="{item} | RuneScape priekšmetu datubāze" alt="" />
			<p>{item}</p>
			<!-- START BLOCK : reset-item -->
			<a class="reset-item confirm" href="/db/reset/{strid}"><img src="/modules/idb/images/x.png" title="reset item" /></a>
			<!-- END BLOCK : reset-item -->
		</td>
	</tr>
	<tr>
		<td class="item_picture" rowspan="2">
			<img src="{img}" title="{item}" alt="" />
			<span class="weight">{weight}</span>
		</td>
		<td style="height:20px;">
			<p class="idb_button{members}">P2P</p>
			<p class="idb_button{trade}">Maināms</p>
			<p class="idb_button{equips}">Ekipējams</p>
			<p class="idb_button{stacks}">Krājas kaudzē</p>
			<p class="idb_button{quest}">Kvests</p>
		</td>
	</tr>
	<tr><td><span class="item-field">Examine:</span> {examine}</td></tr>
	<tr><td colspan="2"><span class="item-field">Ieguve</span> {location}</td></tr>
	<!-- START BLOCK : itemview-uses -->
	<tr><td><span class="item-field">Izmantojums</span></td><td>{uses}</td></tr>
	<!-- END BLOCK : itemview-uses -->
	<!-- START BLOCK : itemview-notes -->
	<tr><td colspan="2"><span class="item-field">Piezīmes</span> {notes}</td></tr>
	<!-- END BLOCK : itemview-notes -->
	<!-- START BLOCK : itemview-monsters -->
	<tr><td><span class="item-field">Briesmoņi</span></td><td>{droppedby}</td></tr>
	<!-- END BLOCK : itemview-monsters -->		
	<tr><td><span class="item-field">Autors{views}</span></td><td>{auser}</td></tr>
	<!-- START BLOCK : itemview-appby -->
	<tr>
		<td><span class="item-field">Apstiprināja</span></td>
		<td>{appuser}</td>
	</tr>
	<!-- END BLOCK : itemview-appby -->
</table>
<!-- START BLOCK : itemview-options -->
<div id="option-line">
	<a class="button danger" href="/db/{strid}/edit" title="">Rediģēt informāciju</a>
</div>
<!-- END BLOCK : itemview-options -->
<!-- START BLOCK : item-view-accept-->
<div style="float:left;clear:both;margin-top:20px;font-size:13px;">
	<a href="/db/{strid}/approve2"><span class="itemdb-green">[Apstiprināt]</span></a> |
	<a class="confirm" href="/db/{strid}/disapprove2"><span class="itemdb-red">[Noraidīt]</span></a>
</div>
<!-- END BLOCK : item-view-accept-->
<div id="bonuses">
<table class="bonuses">
	<tr><td class="rowbg idb_center" colspan="4"><strong>Priekšmetu spēks</strong></td></tr>
	<tr>
		<td style="width:50px;" class="idb_hlight">Armour</td>
		<td style="width:25px;">{armour}</td>
		<td style="width:60px;" class="idb_hlight">Life bonus</td>
		<td style="width:25px;">{lifeb}</td>
	</tr>
	<tr>
		<td class="idb_hlight">Damage</td><td>{dmg}</td>
		<td class="idb_hlight">Hit bonus</td><td>{bonuses}</td>
	</tr>
	<tr>
		<td class="idb_hlight">Level</td><td>{level}</td>
		<td class="idb_hlight">Prayer bonus</td><td>{prayb}</td>
	</tr>
	<tr>
		<td class="idb_hlight idb_center" colspan="2">Accuracy</td>
		<td colspan="2">{accuracy}</td>
	</tr>
	<tr><td class="rowbg idb_center" colspan="4"><strong>Cita info</strong></td></tr>
	<tr><td colspan="2" class="idb_hlight">Critical melee</td><td colspan="2">{cmelee}</td></tr>
	<tr><td colspan="2" class="idb_hlight">Critical magic</td><td colspan="2">{cmage}</td></tr>
	<tr><td colspan="2" class="idb_hlight">Critical ranged</td><td colspan="2">{crange}</td></tr>	
	<tr><td colspan="2" class="idb_hlight">Ekipējuma lauks</td><td colspan="2">{slot}</td></tr>
	<tr><td colspan="2" class="idb_hlight">Uzbrukuma veids</td><td colspan="2">{type}</td></tr>
	<tr><td colspan="2" class="idb_hlight">Uzbrukuma stils</td><td colspan="2">{style}</td></tr>
	<tr><td colspan="2" class="idb_hlight">Ātrums</td><td colspan="2">{speed}</td></tr>
	<tr><td colspan="2" class="idb_hlight">Munīcija</td><td colspan="2">{ammo}</td></tr>	
</table>
</div>
<!-- START BLOCK : bonuses-old -->
<div id="bonuses">
	<table class="bonuses">
	<tr class="table-center"><td class="itemsdb-rowbg" colspan="4"><strong>Bonusi</strong></td></tr>
	<tr class="table-center"><td colspan="2"><strong>Attack</strong></td><td colspan="2"><strong>Defence</strong></td></tr>
	<tr><td style="width:40px;" class="idb_hlight">Stab</td><td style="width:40px;">{attack_stab}</td><td style="width:40px;" class="idb_hlight">Stab</td><td style="width:40px;">{defence_stab}</td></tr>
	<tr><td class="idb_hlight">Slash</td><td>{attack_slash}</td><td class="idb_hlight">Slash</td><td>{defence_slash}</td></tr>
	<tr><td class="idb_hlight">Crush</td><td>{attack_crush}</td><td class="idb_hlight">Crush</td><td>{defence_crush}</td>	</tr>
	<tr><td class="idb_hlight">Magic</td><td>{attack_mage}</td><td class="idb_hlight">Magic</td><td>{defence_mage}</td></tr>
	<tr><td class="idb_hlight">Range</td><td>{attack_range}</td><td class="idb_hlight">Range</td><td>{defence_range}</td></tr>
	<tr class="header table-center"><td class="itemsdb-rowbg" colspan="4">Citi bonusi</td></tr>
	<tr><td class="idb_hlight" colspan="2">Prayer</td><td colspan="2">{prayer}</td></tr>
	<tr><td class="idb_hlight" colspan="2">Summoning</td><td colspan="2">{summoning}</td></tr>
	<tr><td class="idb_hlight" colspan="2">Strength</td><td colspan="2">{strength}</td></tr>
	<tr><td class="idb_hlight" colspan="2">Magic damage</td><td colspan="2">{magic_damage}</td></tr>
	<tr><td class="idb_hlight" colspan="2">Range strength</td><td colspan="2">{range_strength}</td></tr>
	<tr><td class="idb_hlight" colspan="2">Absorb melee</td><td colspan="2">{absorb_melee}</td></tr>
	<tr><td class="idb_hlight" colspan="2">Absorb magic</td><td colspan="2">{absorb_mage}</td></tr>
	<tr><td class="idb_hlight" colspan="2">Absorb range</td><td colspan="2">{absorb_range}</td></tr>
	<tr><td colspan="2"><strong>Ekipējuma lauks: </strong></td><td colspan="2">{slot}</td></tr>
	</table>
</div>
<!-- END BLOCK : bonuses-old -->
</div>
<!-- END BLOCK : itemview -->
<!-- START BLOCK : itemedit -->
<br /><br />
<div id="idb-info" style="clear:both;">
    <img src="/modules/idb/images/info.png" title="" alt="" />
	Teksta ievades ailēs iespējas izmantot tagus, kuri ap izvēlēto vārdu apliks adresi uz attiecīgo kvestu/priekšmetu/minispēli: <br />
	<i>[[Spring sq'irk fruit]]</i>, <i>[[Temple at Senntisten, the|Temple at Senntisten]]</i> vai <i>[[Rune bar|Rune bars]]</i>. <br />
	Pirmā daļa ir precīzais nosaukums, kāds nepieciešams adresei. Otrās daļas teksts tiks izvadīts. Strādā arī 1. variants.
</div>
<form action="" class="form" method="post">
<table class="idbtable" style="margin-left:30px">
	<tr>
		<td class="rowbg" style="height:15px" colspan="3">
			<a class="spec" href="http://www.tip.it/runescape/items/view/{itemid}">aplūkot priekšmetu kontekstā</a>
		</td>
	</tr>
	<tr>
		<td style="width:40px"><img src="{img}" title="{item}" alt="" /></td>
		<td>
			<span class="rowname">Nosaukums:</span>&nbsp;&nbsp;&nbsp;
			<input class="input-normal" type="text" name="item" value="{item}" />
		</td>
		<td style="width:200px">
			<span class="rowname">Lauks:</span>&nbsp;&nbsp;&nbsp;
			<!-- START BLOCK : itemedit-slots -->
			<select name="slot">
				<!-- START BLOCK : itemedit-slot -->
				<option value="{slot}"{selected}>{slot}</option>
				<!-- END BLOCK : itemedit-slot -->
			</select>
			<!-- END BLOCK : itemedit-slots -->
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<p class="idb_button{members}" data-id="members">P2P</p>
			<p class="idb_button{trade}" data-id="trade">Maināms</p>
			<p class="idb_button{equips}" data-id="equips">Ekipējams</p>
			<p class="idb_button{stacks}" data-id="stacks">Krājas kaudzē</p>
			<p class="idb_button{quest}" data-id="quest">Kvests</p>
		</td>
		<td>			
			<span class="rowname">Svars:</span>&nbsp;&nbsp;&nbsp; 
			<input class="input-small" type="text" name="weight" value="{weight}" />
		</td>
	</tr>
	<tr style="display:none">
		<td colspan="4">
			<input type="hidden" id="members" name="members" value="{members}" />
			<input type="hidden" id="trade" name="trade" value="{trade}" />
			<input type="hidden" id="equips" name="equips" value="{equips}" />
			<input type="hidden" id="stacks" name="stacks" value="{stacks}" />
			<input type="hidden" id="quest" name="quest" value="{quest}" />
		</td>
	</tr>
	<tr><td colspan="3" class="hl-row"><strong>Examine:</strong></td></tr>
	<tr><td colspan="3"><textarea class="idb-textarea" style="height:70px;" name="examine">{examine}</textarea></td></tr>
	<tr><td colspan="3" class="hl-row"><strong>Ieguves vieta:</strong> {location}</td></tr>
	<tr><td colspan="3"><textarea class="idb-textarea" style="height:70px;" name="lvlocation">{lvlocation}</textarea></td></tr>
	<tr><td colspan="3" class="hl-row"><strong>Izmantojums:</strong> {uses}</td></tr>
	<tr><td colspan="3"><textarea class="idb-textarea" style="height:70px;" name="lvuses">{lvuses}</textarea></td></tr>
	<tr><td colspan="3" class="hl-row"><strong>Piezīmes:</strong> {notes}</td></tr>
	<tr><td colspan="3"><textarea class="idb-textarea" style="height:70px;" name="lvnotes">{lvnotes}</textarea></td></tr>
	<tr><td colspan="3" class="hl-row"><strong>Briesmoņi, no kuriem šo lietu var iegūt:</strong></td></tr>
	<tr><td colspan="3"><textarea class="idb-textarea" style="height:70px;" name="droppedby">{droppedby}</textarea></td></tr>
</table>
<table class="bonuses" style="float:left;margin:10px 10px 0 35px">
	<tr><td class="rowbg idb_center" colspan="4"><strong>Priekšmetu spēks</strong></td></tr>
	<tr>
		<td style="width:50px;" class="idb_hlight">Armour</td>
		<td style="width:25px;"><input class="input-tiny" name="armour" value="{armour}" /></td>
		<td style="width:60px;" class="idb_hlight">Life bonus</td>
		<td style="width:25px;"><input class="input-tiny" name="lifeb" value="{lifeb}" /></td>
	</tr>
	<tr>
		<td class="idb_hlight">Damage</td><td><input class="input-tiny" name="dmg" value="{dmg}" /></td>
		<td class="idb_hlight">Hit bonus</td><td><input class="input-tiny" name="bonuses" value="{bonuses}" /></td>
	</tr>
	<tr>
		<td class="idb_hlight">Level</td><td><input class="input-tiny" name="level" value="{level}" /></td>
		<td class="idb_hlight">Prayer bonus</td><td><input class="input-tiny" name="prayb" value="{prayb}" /></td>
	</tr>
	<tr>
		<td class="idb_hlight idb_center" colspan="2">Accuracy</td>
		<td colspan="2"><input class="input-tiny" name="accuracy" value="{accuracy}" /></td>
	</tr>
</table>
<table class="bonuses" style="margin:10px 10px 10px 0;float:left">
	<tr><td class="rowbg idb_center" colspan="4"><strong>Cita info</strong></td></tr>
	<tr><td colspan="2" class="idb_hlight">Critical melee</td><td colspan="2">
		<input class="input-tiny" name="cmelee" value="{cmelee}" />
	</td></tr>
	<tr><td colspan="2" class="idb_hlight">Critical magic</td><td colspan="2">
		<input class="input-tiny" name="cmage" value="{cmage}" />
	</td></tr>
	<tr><td colspan="2" class="idb_hlight">Critical ranged</td><td colspan="2">
		<input class="input-tiny" name="crange" value="{crange}" />
	</td></tr>	
</table>
<table class="bonuses" style="margin:10px 10px 10px 0;">
	<tr><td class="rowbg idb_center" colspan="4"><strong>&nbsp;</strong></td></tr>
	<tr><td colspan="2" class="idb_hlight">Uzbrukuma veids</td><td colspan="2">
		<!-- START BLOCK : itemedit-types -->
		<select name="type">
			<!-- START BLOCK : itemedit-type -->
			<option value="{type}"{selected}>{type}</option>
			<!-- END BLOCK : itemedit-type -->
		</select>
		<!-- END BLOCK : itemedit-types -->
	</td></tr>
	<tr><td colspan="2" class="idb_hlight">Uzbrukuma stils</td><td colspan="2">
		<!-- START BLOCK : itemedit-styles -->
		<select name="style">
			<!-- START BLOCK : itemedit-style -->
			<option value="{style}"{selected}>{style}</option>
			<!-- END BLOCK : itemedit-style -->
		</select>
		<!-- END BLOCK : itemedit-styles -->
	</td></tr>
	<tr><td colspan="2" class="idb_hlight">Ātrums</td><td colspan="2">
		<!-- START BLOCK : itemedit-speeds -->
		<select name="speed">
			<!-- START BLOCK : itemedit-speed -->
			<option value="{speed}"{selected}>{speed}</option>
			<!-- END BLOCK : itemedit-speed -->
		</select>
		<!-- END BLOCK : itemedit-speeds -->
	</td></tr>
	<tr><td colspan="2" class="idb_hlight">Munīcija</td><td colspan="2">
		<!-- START BLOCK : itemedit-ammos -->
		<select name="ammo">
			<!-- START BLOCK : itemedit-ammo -->
			<option value="{ammo}"{selected}>{ammo}</option>
			<!-- END BLOCK : itemedit-ammo -->
		</select>
		<!-- END BLOCK : itemedit-ammos -->
	</td></tr>	
</table>
<p><input type="submit" name="submit" class="button danger" value="Saglabāt izmaiņas" /></p>
</form>
<!-- END BLOCK : itemedit -->
<!-- START BLOCK : itemedit-small -->
<br /><br />
<div id="idb-info" style="clear:both;">
    <img src="/modules/idb/images/info.png" title="" alt="" />
	Teksta ievades ailēs iespējas izmantot tagus, kuri ap izvēlēto vārdu apliks adresi uz attiecīgo kvestu/priekšmetu/minispēli: <br />
	<i>[[Spring sq'irk fruit]]</i>, <i>[[Temple at Senntisten, the|Temple at Senntisten]]</i> vai <i>[[Rune bar|Rune bars]]</i>. <br />
	Pirmā daļa ir precīzais nosaukums, kāds nepieciešams adresei. Otrās daļas teksts tiks izvadīts. Strādā arī 1. variants.
</div>
<form action="" class="form" method="post">
<table class="idbtable" style="margin-left:30px">
	<tr>
		<td class="rowbg" style="height:15px" colspan="2">
			<a class="spec" href="http://www.tip.it/runescape/items/view/{itemid}">aplūkot priekšmetu kontekstā</a>
		</td>
	</tr>
	<tr>
		<td style="width:40px"><img src="{img}" title="{item}" alt="" /></td>
		<td>
			<span class="rowname">Nosaukums:</span>&nbsp;&nbsp;&nbsp;
			<input class="input-normal" type="text" name="item" value="{item}" disabled="disabled" />
		</td>
	</tr>
	<tr><td colspan="2" class="hl-row"><strong>Ieguves vieta:</strong> {location}</td></tr>
	<tr><td colspan="2"><textarea class="idb-textarea" style="height:70px;" name="lvlocation">{lvlocation}</textarea></td></tr>
	<tr><td colspan="2" class="hl-row"><strong>Izmantojums:</strong> {uses}</td></tr>
	<tr><td colspan="2"><textarea class="idb-textarea" style="height:70px;" name="lvuses">{lvuses}</textarea></td></tr>
	<tr><td colspan="2" class="hl-row"><strong>Piezīmes:</strong> {notes}</td></tr>
	<tr><td colspan="2"><textarea class="idb-textarea" style="height:70px;" name="lvnotes">{lvnotes}</textarea></td></tr>
</table>
<p style="margin:20px 0 0 20px"><input type="submit" name="submit" class="button danger" value="Saglabāt izmaiņas" /></p>
</form>
<!-- END BLOCK : itemedit-small -->
<!-- START BLOCK : itemcontest -->
<p class="idb-title idb-red">Priekšmetu datubāzes statistika</p>
<table class="idb-table">
	<tr class="rowbg">
		<td style="width:120px;">Lietotājs</td>
		<td style="width:50px"></td>
		<td style="width:150px;text-align:center;">{prev_text} (<span class="idb-red">{prev_count}</span>)</td>		
		<td style="width:150px;text-align:center;">{this_text} (<span class="idb-red">{this_count}</span>)</td>
	</tr>
	<!-- START BLOCK : contest-node -->
    <tr>
		<td>{nick} ({items})</td>
		<td style="text-align:center;"><a href="/db/stats/{user}">Skatīt</a></td>
		<td style="text-align:center;">{lcount}</td>		
		<td style="text-align:center;">{tcount}</td>
    </tr>
	<!-- END BLOCK : contest-node -->
</table>
<!-- END BLOCK : itemcontest -->
<!-- START BLOCK : itemsuser -->
<p class="idb-title idb-red">Lietotāja tulkojumi</p>
<span class="idb-red"><strong>Skaits: </strong></span> {items_count} {show_items_count}<br />
<table class="idb-table">
	<tr class="idb-table-header rowbg">
		<td style="width:40px;"></td>
		<td style="width:200px;">Nosaukums</td>
		<td style="width:40px;"></td>
		<td style="width:200px;">Nosaukums</td>
	</tr>
	<!-- START BLOCK : useritem -->
    <tr class="idb-small">
		<!-- START BLOCK : useritem-data -->
		<td>{img}</td>
		<td><a href="/db/{strid}">{name}</a></td>
		<!-- END BLOCK : useritem-data -->
    </tr>
	<!-- END BLOCK : useritem -->
</table>
<div id="idb_pages" style="margin-top:10px">
<!-- START BLOCK : useritem-page -->
<a href="/db/stats/{user}/?page={page-link}">{page-nr}</a>&nbsp;&nbsp;
<!-- END BLOCK : useritem-page -->
</div>
<!-- END BLOCK : itemsuser -->
<!-- START BLOCK : error-no-items -->
<p class="idb-red simple-note">Šajā nedēļā šis lietotājs nav iztulkojis nevienu priekšmeta šķirkli!</p>
<!-- END BLOCK : error-no-items -->
<!-- START BLOCK : queue-noitems -->
<p class="idb-red simple-note">Visi iesniegtie priekšmetu šķirkļu tulkojumi pārbaudīti!</p>
<!-- END BLOCK : queue-noitems -->
<!-- START BLOCK : itemsqueue -->
<p class="idb-title idb-red">Vēl neapstiprināto priekšmetu saraksts <a href="/db/queue/{strid-spec}?viewall=1"><span style="float:right;" class="idb-blue">[Skatīt visus pēc kārtas atvērtā skatā]</span></a></p>

<table class="idb-tbl2 idb-small">
	<tr class="idb-table-header rowbg">
		<td style="width:40px;">Attēls</td>
		<td style="width:250px;">Nosaukums</td>
		<td style="width:120px;">Autors</td>
		<td style="width:40px;">Attēls</td>
		<td style="width:250px;">Nosaukums</td>
		<td style="width:120px;">Autors</td>
	</tr>
	<!-- START BLOCK : queue-item -->
	<tr>
		<!-- START BLOCK : queue-item-col -->
		<td><img src="/dati/idb/{img}" title="{item}" alt="" /></td>
		<td><a href="/db/queue/{strid}">{item}</a></td>
		<td>{nick}</td>
		<!-- END BLOCK : queue-item-col -->
	</tr>
	<!-- END BLOCK : queue-item -->
</table>
<!-- END BLOCK : itemsqueue -->
<!-- START BLOCK : queueview -->
<h2>{item}</h2>
<div class="idb-blocktext">
	Šis priekšmets atrodas neapstiprināto sarakstā. Lai to atzītu par derīgu, nospied uz 'Apstiprināt'. <br />Lai veiktu pretēju darbību, nospied 'Noraidīt'.
	Noraidīšana nozīmē, ka tas netiks pievienots iztulkotajiem. <br />
	<p style="text-align:center;font-size:13px;">
        <a href="/db/queue/{strid}/approve{viewall}"><span class="idb-green">Apstiprināt</span></a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <a href="/db/queue/{strid}/remove{viewall}"><span class="idb-red">Noraidīt</span></a>
	</p>
</div>
<form action="/db/queue/{strid}/approve{viewall}" method="post">
<table id="item_table" class="table-accept">
	<tr class="item-header rowbg">
		<td colspan="4">
			<img src="/bildes/fugue-icons/node-insert-previous.png" title="{item} | RuneScape priekšmetu datubāze" alt="" />
			<p>{item}</p>
		</td>
	</tr>
	<tr>
		<td class="item_picture" rowspan="2">
			<img src="/dati/idb/{img}" title="{item}" alt="" />
			<span class="weight">{weight}</span>
		</td>
		<td style="height:20px;">
			<p class="idb_button{members}" data-id="members">P2P</p>
			<p class="idb_button{trade}" data-id="trade">Maināms</p>
			<p class="idb_button{equips}" data-id="equips">Ekipējams</p>
			<p class="idb_button{stacks}" data-id="stacks">Krājas kaudzē</p>
			<p class="idb_button{quest}" data-id="quest">Kvests</p>
		</td>
	</tr>
	<tr><td><span class="item-field">Examine:</span> {examine}</td></tr>
	<tr><td colspan="2"><span class="item-field">Ieguve</span> {lvlocation}</td></tr>
	<tr><td><strong>Izmantojums:</strong></td><td>{lvuses}</td></tr>
	<!-- START BLOCK : queue-notes-->
	<tr><td colspan="2"><span class="item-field">Piezīmes:</span> {lvnotes}</td></tr>
	<!-- END BLOCK : queue-notes -->
	<tr><td><span class="item-field">Pievienoja:</span></td><td>{nick}</td>
	</tr>
	<!-- START BLOCK : queue-form-lv -->
	<tr><td style="height:30px;" colspan="2">
		<input type="hidden" id="members" name="members" value="{members}" />
		<input type="hidden" id="trade" name="trade" value="{trade}" />
		<input type="hidden" id="equips" name="equips" value="{equips}" />
		<input type="hidden" id="stacks" name="stacks" value="{stacks}" />
		<input type="hidden" id="quest" name="quest" value="{quest}" />
	</td></tr>
	<tr><td><strong>Ieguve:</strong></td>
		<td style="overflow:auto;"><textarea class="idb-txtarea" name="lvlocation">{location-data}</textarea></td>
	</tr>
	<tr>
		<td><strong>Izmantojums:</strong></td>

		<td style="overflow:auto;"><textarea class="idb-txtarea" name="lvuses">{uses-data}</textarea></td>
	</tr>
	<tr>
		<td><strong>Piezīmes:</strong></td>

		<td style="overflow:auto;"><textarea class="idb-txtarea" name="lvnotes">{notes-data}</textarea></td>
	</tr>
	<tr>
		<td></td><td><input type="submit" name="submit" class="button" value="Apstiprināt ar šādām izmaiņām" /></td>
	</tr>	
	<!-- END BLOCK : queue-form-lv -->
	<!-- START BLOCK : queue-form-eng -->
	<tr><td><strong>Ieguve:</strong></td><td>{location}</td></tr>
	<tr><td><strong>Izmantojums:</strong></td><td>{uses}</td></tr>
	<tr><td><strong>Piezīmes:</strong></td><td>{notes}</td></tr>
	<!-- END BLOCK : queue-form-eng -->
</table>
</form>
<!-- END BLOCK : queueview -->
<!-- START BLOCK : myitems-noitems -->
<p class="idb-red simple-note">Diemžēl nav tulkots vēl neviens priekšmeta šķirklis, kas būtu jāpārbauda!</p>
<!-- END BLOCK : myitems-noitems -->
<!-- START BLOCK : myitems -->
<p class="idb-title">Tulkotie priekšmeti un to statuss (pēdējie 50 šķirkļi)</p>
<ul id="result-list" style="margin-bottom:20px">
	<!-- START BLOCK :  myitem -->
	<li>
        <img src="/dati/idb/{img}" title="{item}" alt="" />
        <a href="/db/myitems/{id}">{item}</a>
        <a style="float:right;" href="#"><span class="idb-{color}">[ {text} ]</span></a>
	</li>
	<!-- END BLOCK :  myitem -->
</ul>
<!-- END BLOCK : myitems -->
<!-- START BLOCK : myitem-view -->
<br /><br />
<div id="idb-info" style="clear:both;">
    <img src="/modules/idb/images/info.png" title="" alt="" />
	Teksta ievades ailēs iespējas izmantot tagus, kuri ap izvēlēto vārdu apliks adresi uz attiecīgo kvestu/priekšmetu/minispēli: <br />
	<i>[[Spring sq'irk fruit]]</i>, <i>[[Temple at Senntisten, the|Temple at Senntisten]]</i> vai <i>[[Rune bar|Rune bars]]</i>. <br />
	Pirmā daļa ir precīzais nosaukums, kāds nepieciešams adresei. Otrās daļas teksts tiks izvadīts. Strādā arī 1. variants.
</div>
<form action="/db/myitems/{id}" class="form" method="post">
<table class="idbtable" style="margin-left:30px">
	<tr>
		<td class="rowbg" style="height:15px" colspan="2">
			<a class="spec" href="http://www.tip.it/runescape/items/view/{itemid}">aplūkot priekšmetu kontekstā</a>
		</td>
	</tr>
	<tr>
		<td style="width:40px"><img src="{img}" title="{item}" alt="" /></td>
		<td>
			<span class="rowname">Nosaukums:</span>&nbsp;&nbsp;&nbsp;
			<input class="input-normal" type="text" name="item" value="{item}" disabled="disabled" />
		</td>
	</tr>
	<tr><td colspan="2" class="hl-row"><strong>Ieguves vieta:</strong> {location}</td></tr>
	<tr><td colspan="2"><textarea class="idb-textarea" style="height:70px;" name="lvlocation">{lvlocation}</textarea></td></tr>
	<tr><td colspan="2" class="hl-row"><strong>Izmantojums:</strong> {uses}</td></tr>
	<tr><td colspan="2"><textarea class="idb-textarea" style="height:70px;" name="lvuses">{lvuses}</textarea></td></tr>
	<tr><td colspan="2" class="hl-row"><strong>Piezīmes:</strong> {notes}</td></tr>
	<tr><td colspan="2"><textarea class="idb-textarea" style="height:70px;" name="lvnotes">{lvnotes}</textarea></td></tr>
</table>
<!-- START BLOCK : myitem-submit -->
<p style="margin:20px 0 0 20px"><input type="submit" name="submit" class="button danger" value="Saglabāt izmaiņas" /></p>
<!-- END BLOCK : myitem-submit -->
<!-- START BLOCK : myitem-submit-disabled -->
<p class="simple-note idb-red" style="margin-bottom:30px"">Veikt izmaiņas vairs nav iespējams!</p>
<!-- END BLOCK : myitem-submit-disabled -->
</form>
<!-- END BLOCK : myitem-view -->
<!-- START BLOCK : idb-editor -->
<form action="/db/editor/{page}{user}" method="post">
	<!-- START BLOCK : editor-view -->
	<table id="item_table" class="item_editor" style="margin-bottom:50px;font-size:11px;">
		<tr class="item-header rowbg">
			<td colspan="2">
				<img src="/bildes/fugue-icons/node-insert-previous.png" title="{item} | RuneScape priekšmetu datubāze" alt="" />
				<p>{item}</p>
				<span>dzēst: <input type="checkbox" name="{id}-reset" /></span>
				<span>Pievienot mani kā autoru: <input type="checkbox" name="{id}-auser" /></span>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="hl-row"><strong>ENG: </strong>{location}</td>
		</tr>
		<tr>
			<td colspan="2" class="hl-row"><strong>LAT: </strong>{lvlocation}</td>
		</tr>
		<tr>
			<td><strong>Ieguve:</strong></td>
			<td><textarea class="idb-textarea-wide" style="font-size:11px" name="{id}-lvlocation">{lvlocation-area}</textarea></td>
		</tr>
		<tr>
			<td class="hl-row" colspan="2"><strong>ENG: </strong>{uses}</td>
		</tr>
		<tr>
			<td class="hl-row" colspan="2"><strong>LAT: </strong>{lvuses}</td>
		</tr>
		<tr>
			<td style="width:100px;"><strong>Izmantojums:</strong></td>
			<td><textarea class="idb-textarea-wide" style="font-size:11px" name="{id}-lvuses">{lvuses-area}</textarea></td>
		</tr>
		<tr>
			<td class="hl-row" colspan="2"><strong>ENG: </strong>{notes}</td>
		</tr>
		<tr>
			<td class="hl-row" colspan="2"><strong>LAT: </strong>{lvnotes}</td>
		</tr>
		<tr>
			<td><strong>Piezīmes: </strong></td>
			<td><textarea class="idb-textarea-wide" style="font-size:11px" name="{id}-lvnotes">{lvnotes-area}</textarea></td>
		</tr>
		<tr>
			<td><strong>Pievienoja:</strong></td>
			<td>{auser}</td>
		</tr>
	</table>
	<!-- END BLOCK : editor-view -->
	<input class="button danger" type="submit" name="submit" value="Saglabāt" />
</form>
<!-- END BLOCK : idb-editor -->
<!-- END BLOCK : idb-content -->