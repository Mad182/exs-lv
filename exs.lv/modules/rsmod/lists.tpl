<!-- START BLOCK : list-tabs -->
<ul class="tabs">
    <li><a class="{tab-quests}" href="/all-quests">Kvesti</a></li>
    <li><a class="{tab-miniquests}" href="/all-miniquests">Minikvesti</a></li>
    <li><a class="{tab-minigames}" href="/all-minigames">Minispēles</a></li> 
</ul>
<!-- END BLOCK : list-tabs -->


<!-- START BLOCK : list-intro-text -->
<p class="list-intro">Vairākās RuneScape pamācību sadaļās <em>(skatīt cilnes)</em> tiek parādīti tikai tie raksti, par kuriem šajā sadaļā ir izveidots ieraksts. Tas veidots ar mērķi, lai varētu pievienot arī lapā neesošu rakstu <em>placeholders</em>, pievienot rakstiem papildinformāciju (p2p only, sarežģītība u.tml.), kā arī ērti rakstu no pamācību sadaļas paslēpt, to nepārvietojot pa sadaļām. Visas šīs darbības veicamas šeit.<br><br>No tā izriet, ka raksts, kas tiek apstiprināts kādā no augstāk minētajām sadaļām, uzreiz pamācību sadaļā nebūs redzams, ja vien šeit nebūs ieraksta, kam raksts piesaistīts.</p>
<!-- END BLOCK : list-intro-text -->


<!-- START BLOCK : list-button-new -->
<p style="text-align:right">
    <a href="/{category-url}/new" class="button" title="Pievienot jaunu ierakstu">Pievienot ierakstu</a>
</p>
<!-- START BLOCK : list-button-new -->


<!-- START BLOCK : list-all-pages -->
<table class="rslist guide-list-table">
    <tr class="listhead">
		<td style="width:20px">&nbsp;</td>
		<td style="width:200px">Nosaukums</td>        
		<td style="width:200px">Raksta adrese</td>
		<td class="is-centered" style="width:90px">Raksta ID</td>        
		<td style="width:20px">&nbsp;</td>	
		<td style="width:20px">&nbsp;</td>	
		<td style="width:20px">&nbsp;</td>	
		<td style="width:20px">&nbsp;</td>	
	</tr>
    <!-- START BLOCK : list-row -->
    <!-- START BLOCK : list-page -->
    <tr{splitted-row-style}{faded-row}>
        <td class="is-centered">{splitted-by}</td>
        <td><a href="/read/{strid}">{title}</a></td>
        <td><a href="/read/{strid}">{strid}</a></td>
        <td class="is-centered">{page_id}</td>
        <td>&nbsp;</td>        
        <td><a class="hide-page" href="/{category-url}/hide/{rspage_id}">
            <img class="is-faded" src="/bildes/fugue-icons/bin.png" title="Paslēpt/parādīt pamācību sadaļās" alt="">
        </a></td>
        <td><a href="#">
            <img class="is-faded" src="/bildes/fugue-icons/notebook--pencil.png" title="Labot informāciju" alt="">
        </a></td>
        <td><a class="del-page" href="/{category-url}/delete/{rspage_id}">
            <img class="is-faded" src="/bildes/fugue-icons/cross-button.png" title="Dzēst ierakstu" alt="">
        </a></td>
    </tr>
    <!-- END BLOCK : list-page -->
    <!-- START BLOCK : list-page-empty -->
    <tr{splitted-row-style}{faded-row}>
        <td class="is-centered">{splitted-by}</td>
        <td>{title}</td>
        <td class="is-centered" colspan="2"><em>Ierakstam nav piesaistīta raksta</em></td>
        <td>&nbsp;</td>
        <td><a class="hide-page" href="/{category-url}/hide/{rspage_id}">
            <img class="is-faded" src="/bildes/fugue-icons/bin.png" title="Paslēpt/parādīt pamācību sadaļās" alt="">
        </a></td>
        <td><a href="#">
            <img class="is-faded" src="/bildes/fugue-icons/notebook--pencil.png" title="Labot informāciju" alt="">
        </a></td>
        <td><a class="del-page" href="/{category-url}/delete/{rspage_id}">
            <img class="is-faded" src="/bildes/fugue-icons/cross-button.png" title="Dzēst ierakstu" alt="">
        </a></td>
    </tr>
    <!-- END BLOCK : list-page-empty -->
    <!-- END BLOCK : list-row -->
</table>  
<!-- END BLOCK : list-all-pages -->


<!-- START BLOCK : new-page-form -->
<p>Lorems Ipsums lives here<br>Parametrus varēs labot arī vēlāk</p>

<p style="text-align:right">
    <a href="/{category-url}" class="button rs-button" style="top:0">Uz sarakstu</a>
</p>
<form class="form info-form" method="post">
    <fieldset>
    
        <legend>Jauns ieraksts</legend>
        
        <p><strong>Nosaukums:</strong></p>    
        <p><input type="text" style="width:400px" name="title" value=""></p>
        
        <p><strong>Eksistējoša raksta adreses nosaukums:</strong></p>    
        <p><input type="text" style="width:400px" name="strid" value=""></p> 
        
        <input class="button" type="submit" value="Apstiprināt">
        
    </fieldset>
</form>
<!-- START BLOCK : new-page-form -->


<!-- START BLOCK : quest-edit -->
<p style="text-align:right">
    <a href="/{category-url}" class="button rs-button" style="top:0">Uz sarakstu</a>
</p>
<form class="form info-form" action="/{category-url}/edit/{page_id}" method="post">
    <fieldset>
    
        <legend><a href="/read/{page_strid}">{page_title}</a> informācija</legend>                 
        <p><strong>Sākumpunkta atrašanās vieta:</strong> (var izmantot nākotnē)</p>    
        <p><input type="text" style="width:400px" name="location" value="{rspage_location}"></p> 
        
        <p><strong>Nepieciešamās prasmes:</strong> (var izmantot nākotnē)</p>
        <p>(formāts: 55 Agility, 77 Runecrafting, 111 Mining)</p>
        <textarea name="skills">{rspage_skills}</textarea>
        
        <p><strong>Nepieciešamie kvesti:</strong> (var izmantot nākotnē)</p>
        <p class="info">(formāts: Lost Tribe, While Guthix Sleeps, Monkey Madness)</p>
        <textarea name="quests">{rspage_quests}</textarea>
        
        <p><strong>Citas prasības:</strong> (var izmantot nākotnē)</p>
        <textarea name="extra">{rspage_extra}</textarea>   
        
        <p><strong>Izvēlnes:</strong></p>        
        <select name="difficulty">
            <option value="0">Nav atzīmēts</option>
            <!-- START BLOCK : edit-difficulty -->
            <option value="{level-id}"{selected}>{level-title}</option>
            <!-- END BLOCK : edit-difficulty -->
        </select> 
        
        <select name="length">
            <option value="0">Nav atzīmēts</option>
            <!-- START BLOCK : edit-length -->
            <option value="{length-id}"{selected}>{length-title}</option>
            <!-- END BLOCK : edit-length -->
        </select>  
        
        <select name="storyline">
            <option value="0">Ārpus sērijas</option>
            <!-- START BLOCK : edit-storyline -->
            <option value="{story-id}"{selected}>{story-title}</option>
            <!-- END BLOCK : edit-storyline -->
        </select>  
        
        <select name="members_only">
            <option value="0"{selected-free}>Free</option>
            <option value="1"{selected-members}>Members only</option>
        </select>
        
        <p><strong>Apraksts:</strong> (minikvestiem/f2p kvestiem)</p>
        <textarea name="description">{rspage_description}</textarea>
        
        <p style="display:none"><strong>Datums, kad ieviests spēlē:</strong></p>
        <p style="display:none">(formāts: dd/mm/yyyy)</p>
        <input style="display:none" type="text" name="date" value="{rspage_date}"><br><br>   
        
        <input class="button" type="submit" value="Apstiprināt">
        
    </fieldset>
</form>
<!-- END BLOCK : quest-edit -->
