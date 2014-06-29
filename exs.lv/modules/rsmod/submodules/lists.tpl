<!-- START BLOCK : list-tabs -->
<ul class="tabs">
    <li><a class="{tab-quests}" href="/all-quests">Kvesti</a></li>
    <li><a class="{tab-miniquests}" href="/all-miniquests">Minikvesti</a></li>
    <li><a class="{tab-minigames}" href="/all-minigames">Minispēles</a></li> 
    <li><a class="{tab-distractions}" href="/all-distractions">Distractions &amp; Diversions</a></li> 
    <li><a class="{tab-guilds}" href="/all-guilds">Ģildes</a></li> 
    <li><a class="{tab-unlisted}" href="/all-unlisted">Nepiesaistītie raksti</a></li> 
</ul>
<!-- END BLOCK : list-tabs -->


<!-- START BLOCK : list-intro-text -->
<p class="list-intro">Vairākās RuneScape pamācību sadaļās <em>(skatīt cilnes)</em> tiek parādīti tikai tie raksti, par kuriem šajā sadaļā ir izveidots ieraksts. Tas veidots ar mērķi, lai varētu pievienot arī lapā neesošu rakstu <em>placeholders</em>, pievienot rakstiem papildinformāciju (p2p only, sarežģītība u.tml.), kā arī ērti rakstu no pamācību sadaļas paslēpt, to nepārvietojot pa sadaļām. Visas šīs darbības veicamas šeit.<br><br>No tā izriet, ka raksts, kas tiek apstiprināts kādā no augstāk minētajām sadaļām, uzreiz pamācību sadaļā nebūs redzams, ja vien šeit nebūs ieraksta, kam raksts piesaistīts.</p>
<!-- END BLOCK : list-intro-text -->


<!-- START BLOCK : list-intro-unlisted -->
<p class="list-text">Šajā sarakstā aplūkojami visi tie raksti, kas pievienoti kādai no galvenajām RuneScape pamācību sadaļām, bet nav piesaistīti nevienam ierakstam.</p>
<!-- END BLOCK : list-intro-unlisted -->


<!-- START BLOCK : list-button-new -->
<p style="text-align:right">
    <a href="/{category-url}/new" class="button" title="Pievienot jaunu ierakstu">Pievienot ierakstu</a>
</p>
<!-- START BLOCK : list-button-new -->


<!-- START BLOCK : list-no-pages -->
<p class="list-no-pages">
    Nav neviena pievienota ieraksta.
</p>
<!-- END BLOCK : list-no-pages -->


<!-- START BLOCK : list-all-unlisted -->
<table class="rslist list-table">
    <tr class="listhead">
        <td style="width:20px">&nbsp;</td>
        <td style="width:270px">Nosaukums</td>        
        <td style="width:270px">Raksta adrese</td>
        <td class="is-centered" style="width:90px">Raksta ID</td>
    </tr>
    <!-- START BLOCK : unlisted-page -->
    <tr{splitted-row-style}{faded-row}>
        <td class="is-centered">{splitted-by}</td>
        <td><a href="/read/{strid}">{title}</a></td>
        <td><a href="/read/{strid}">{strid}</a></td>
        <td class="is-centered">{page_id}</td>
    </tr>
    <!-- END BLOCK : unlisted-page -->
</table>  
<!-- END BLOCK : list-all-unlisted -->


<!-- START BLOCK : list-all-pages -->
<table class="rslist list-table">
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
        <td><a href="/{category-url}/edit/{rspage_id}">
            <img class="is-faded" src="/bildes/fugue-icons/notebook--pencil.png" title="Labot informāciju" alt="">
        </a></td>        
        <td><a class="hide-page" href="/{category-url}/hide/{rspage_id}">
            <img class="is-faded" src="/bildes/fugue-icons/bin.png" title="Paslēpt/parādīt pamācību sadaļās" alt="">
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
        <td><a href="/{category-url}/edit/{rspage_id}">
            <img class="is-faded" src="/bildes/fugue-icons/notebook--pencil.png" title="Labot informāciju" alt="">
        </a></td>
        <td><a class="hide-page" href="/{category-url}/hide/{rspage_id}">
            <img class="is-faded" src="/bildes/fugue-icons/bin.png" title="Paslēpt/parādīt pamācību sadaļās" alt="">
        </a></td>
        <td><a class="del-page" href="/{category-url}/delete/{rspage_id}">
            <img class="is-faded" src="/bildes/fugue-icons/cross-button.png" title="Dzēst ierakstu" alt="">
        </a></td>
    </tr>
    <!-- END BLOCK : list-page-empty -->
    <!-- END BLOCK : list-row -->
</table>  
<!-- END BLOCK : list-all-pages -->


<!-- START BLOCK : quest-form -->
<p class="list-text">
    - Vairāki no aizpildāmajiem laukiem šobrīd netiek izmantoti, bet var noderēt nākotnē, tāpēc ieteicams aizpildīt visu.<br>
    - Ja ierakstam nebūs norādīta eksistējoša raksta adrese, tas tiks uztverts kā <em>placeholder</em>.<br>
    <span class="required">*</span> - obligāti aizpildāmie lauki. Pārējais var tikt aizpildīts arī vēlāk.
</p>
<form class="form list-form" method="post">
    <fieldset>
        
        <p class="field-title">
            <strong><span class="required">*</span>&nbsp;Nosaukums:</strong>
        </p>    
        <p><input type="text" style="width:400px" name="title" value="{title}"></p>
        
        <p class="field-title">
            <strong>Eksistējoša raksta adreses nosaukums:</strong>
        </p>
        <p class="field-example info">(piemērs: <em>death-to-the-dorgeshuun</em>)</p>
        <p><input type="text" style="width:400px" name="strid" value="{strid}"></p> 
               
        <p class="field-title">
            <strong>Sākumpunkta atrašanās vieta:</strong>
        </p>    
        <p><input type="text" style="width:400px" name="starting_point" value="{starting_point}"></p> 
        
        <p class="field-title">
            <strong>Nepieciešamās prasmes:</strong>
        </p>
        <p class="field-example">(formāts: 55 Agility, 77 Runecrafting, 111 Mining)</p>
        <textarea name="skills">{skills}</textarea>
        
        <p class="field-title">
            <strong>Nepieciešamie kvesti:</strong>
        </p>
        <p class="field-example info">(formāts: Lost Tribe, While Guthix Sleeps, Monkey Madness)</p>
        <textarea name="quests">{quests}</textarea>
        
        <p class="field-title">
            <strong>Citas prasības:</strong>
        </p>
        <p class="field-example">(formāts: tekstveida apraksts)</p>
        <textarea name="extra">{extra}</textarea>   
        
        <p class="field-title">
            <strong>Izvēlnes:</strong>
        </p>        
        <select name="difficulty">
            <!-- START BLOCK : add-difficulty -->
            <option value="{level-id}"{selected}>{level-title}</option>
            <!-- END BLOCK : add-difficulty -->
        </select> 
        
        <select name="length">
            <!-- START BLOCK : add-length -->
            <option value="{length-id}"{selected}>{length-title}</option>
            <!-- END BLOCK : add-length -->
        </select> 
        
        <select name="members_only">
            <option value="0">F2P</option>
            <option value="1"{sel-members}>P2P</option>
        </select>
        
        <select name="age">
            <option value="0">Fifth age</option>
            <option value="1"{sel-sixth}>Sixth age</option>
        </select>
        
        <select name="voice_acted">
            <option value="0">Neierunāti dialogi</option>
            <option value="1"{sel-voiced}>Ierunāti dialogi</option>
        </select>
        
        <p class="field-title">
            <strong>Apraksts:</strong> (f2p kvestiem/mini-kvestiem)
        </p>
        <textarea name="description">{description}</textarea>
        
        <p class="field-title">
            <strong>Datums, kad ieviests spēlē:</strong>
        </p>
        <p class="field-example">(formāts: dd/mm/gggg)</p>
        <input type="text" name="date" value="{date}"><br><br>   

        <input class="button" type="submit" name="submit" value="Pievienot">
        
    </fieldset>
</form>
<!-- END BLOCK : quest-form -->


<!-- START BLOCK : minigame-form -->
<p class="list-text">
    - Vairāki no aizpildāmajiem laukiem šobrīd netiek izmantoti, bet var noderēt nākotnē, tāpēc ieteicams aizpildīt visu.<br>
    - Ja ierakstam nebūs norādīta eksistējoša raksta adrese, tas tiks uztverts kā <em>placeholder</em>.<br>
    <span class="required">*</span> - obligāti aizpildāmie lauki. Pārējais var tikt aizpildīts arī vēlāk.
</p>
<form class="form list-form" method="post">
    <fieldset>
        
        <p class="field-title">
            <strong><span class="required">*</span>&nbsp;Nosaukums:</strong>
        </p>    
        <p><input type="text" style="width:400px" name="title" value="{title}"></p>
        
        <p class="field-title">
            <strong>Eksistējoša raksta adreses nosaukums:</strong>
        </p>
        <p class="field-example info">(piemērs: <em>lava-flow-mine</em>)</p>
        <p><input type="text" style="width:400px" name="strid" value="{strid}"></p> 
               
        <p class="field-title">
            <strong>Sākumpunkta atrašanās vieta:</strong>
        </p>    
        <p><input type="text" style="width:400px" name="starting_point" value="{starting_point}"></p>
        
        <p class="field-title">
            <strong>Prasības:</strong>
        </p>
        <p class="field-example">(formāts: tekstveida apraksts)</p>
        <textarea name="extra">{extra}</textarea>
        
        <select style="clear:both;display:block" name="members_only">
            <option value="0">F2P</option>
            <option value="1"{sel-members}>P2P</option>
        </select>
        
        <p class="field-title">
            <strong>Īss apraksts:</strong>
        </p>
        <textarea name="description">{description}</textarea><br>

        <input class="button" type="submit" name="submit" value="Pievienot">
        
    </fieldset>
</form>
<!-- END BLOCK : minigame-form -->


<!-- START BLOCK : guild-form -->
<p class="list-text">
    Ja ierakstam nebūs norādīta eksistējoša raksta adrese, tas tiks uztverts kā <em>placeholder</em>.<br>
    <span class="required">*</span> - obligāti aizpildāmie lauki. Pārējais var tikt aizpildīts arī vēlāk.
</p>
<form class="form list-form" method="post">
    <fieldset>
        
        <p class="field-title">
            <strong><span class="required">*</span>&nbsp;Nosaukums:</strong>
        </p>    
        <p><input type="text" style="width:400px" name="title" value="{title}"></p>
        
        <p class="field-title">
            <strong>Eksistējoša raksta adreses nosaukums:</strong>
        </p>
        <p class="field-example info">(piemērs: <em>champion-s-guild</em>)</p>
        <p><input type="text" style="width:400px" name="strid" value="{strid}"></p> 
               
        <p class="field-title">
            <strong>Sākumpunkta atrašanās vieta:</strong>
        </p>    
        <p><input type="text" style="width:400px" name="starting_point" value="{starting_point}"></p>
        
        <p class="field-title">
            <strong>Prasības:</strong>
        </p>
        <p class="field-example">(formāts: tekstveida apraksts)</p>
        <textarea name="extra">{extra}</textarea>
        
        <select style="clear:both; display:block" name="members_only">
            <option value="0">F2P</option>
            <option value="1"{sel-members}>P2P</option>
        </select><br>

        <input class="button" type="submit" name="submit" value="Pievienot">
        
    </fieldset>
</form>
<!-- END BLOCK : guild-form -->
