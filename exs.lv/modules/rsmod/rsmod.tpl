<!-- INCLUDE BLOCK : sub-template -->


<!-- START BLOCK : all-series-block -->

    <!-- START BLOCK : no-series-found -->
    <p class="simple-note">Neizdevās atlasīt informāciju par kvestu sērijām.</p>
    <!-- END BLOCK : no-series-found -->

    <!-- START BLOCK : series-notes -->
    <p class="simple-note">
        Nospiežot uz bultiņas pie katras sērijas, var aplūkot sarakstu ar sērijai piesaistītajiem kvestiem, kā arī sarakstu rediģēt.
    </p>
    <!-- END BLOCK : series-notes -->

    <!-- START BLOCK : series-form -->
    <form class="form" action="/{category-url}/update" method="post">

        <!-- START BLOCK : series-column -->
        <table class="rslist series-list">
        <tr class="listhead">
            <td style="width:5px"></td>
            <td style="width:230px">Nosaukums</td>
            <td class="is-centered" style="width:70px">Secība</td>
            <td>&nbsp;</td>
        </tr>
        <!-- START BLOCK : series-row -->
        <tr>
            <td>&nbsp;</td>
            <td><input class="series-input" name="title_{id}" value="{title}"></td>
            <td class="is-centered">
                <select name="order_{id}">
                    <!-- START BLOCK : selection-option -->
                    <option value="{ordered_by}"{selected}>{ordered_by}</option>
                    <!-- END BLOCK : selection-option -->
                </select>
            </td>
            <td>
                <a class="related-quests" href="/{category-url}/getlist/{id}">
                    <img class="is-pointer" src="/bildes/fugue-icons/navigation-270-button.png" title="Skatīt sērijas kvestus" alt="">
                </a>
            </td>
        </tr>
        <!-- END BLOCK : series-row -->
        </table>
        <!-- END BLOCK : series-column -->
        
        <div class="clearfix"></div>
        <input class="button primary" type="submit" name="submit" value="Apstiprināt izmaiņas">
    </form>
    <!-- END BLOCK : series-form -->
<!-- END BLOCK : all-series-block -->


<!-- START BLOCK : series-quests-block -->
<div style="width:500px">
    <p class="simple-note">
        <strong>Sērijas kvestu secība</strong>
        <a class="change-list" style="float:right" href="/{category-url}/list/{series-id}">Veikt izmaiņas sarakstā</a>
    </p>
    <div style="padding:0 10px 10px">
        <form id="quest-order" action="/{category-url}/order/{series-id}" method="post">
            <input type="hidden" name="json_check" value="1">
            
            <!-- START BLOCK : quest-list -->
            <ul class="series-quests">
                <!-- START BLOCK : series-quest -->
                <li>
                    <a href="/read/{strid}">{title}</a>
                    <select name="order-{id}">
                    <!-- START BLOCK : option-param -->
                        <option value="{value}"{selected}>{value}</option>
                    <!-- END BLOCK : option-param -->
                    </select>
                </li>
                <!-- END BLOCK : series-quest -->        
            </ul>
            <!-- END BLOCK : quest-list -->
            
            <!-- START BLOCK : no-series-quests -->
            <p class="no-quests" style="margin-left:20px">Sērijā nav neviena kvesta.</p>
            <!-- END BLOCK : no-series-quests -->
            
            <!-- START BLOCK : submit-button -->
            <input type="submit" class="button primary" name="submit" value="Atjaunot secību">
            <p class="response"></p>
            <!-- END BLOCK : submit-button -->
        </form>
    </div>
</div>
<!-- END BLOCK : series-quests-block -->


<!-- START BLOCK : all-quests-block -->

    <!-- START BLOCK : wrong-params -->
    <p class="simple-note">Nepareizi norādīta sērija</p>
    <!-- END BLOCK : wrong-params -->
    
    <!-- START BLOCK : series-not-found -->
    <p class="simple-note">Neizdevās atlasīt sarakstu ar kvestiem</p>
    <!-- END BLOCK : series-not-found -->
    
    <!-- START BLOCK : all-quests-list -->
    <div class="fancy-list">
        <p class="simple-note">Pievienojamie RuneScape kvesti</p>
        <p style="color:#4A84B1;margin-left:12px">Sarakstā redzami arī placeholders.</p>
        <ul>
            <!-- START BLOCK : list-single-quest -->
            <li class="{marker}">
                <a class="set-quest" href="/series/{type}/{series-id}/{page-id}">{title}</a>
            </li>
            <!-- END BLOCK : list-single-quest -->
        </ul>
    </div>
    <!-- END BLOCK : all-quests-list -->

<!-- END BLOCK : all-quests-block -->


<!-- START BLOCK : set-quest-block -->
<p class="simple-note">SUCCESS story</p>
<!-- END BLOCK : set-quest-block -->


<!-- START BLOCK : skill-requirements -->

    <!-- START BLOCK : no-skills-added -->
    <p class="simple-note">Neizdevās atlasīt prasmju sarakstu.</p>
    <!-- END BLOCK : no-skills-added -->

    <!-- START BLOCK : skills-notes -->
    <p class="simple-note">
        Saraksts paredzēts, lai katrai prasmei varētu norādīt augstāko līmeni, kāds nepieciešams kādam no kvestiem.<br><br>
        - Ja lapā eksistē raksts par kvestu, laukā norādāms raksta adreses nosaukums (<a href="/all-quests">atrodams pamācību sarakstos</a>), savukārt, ja raksta nav, laukā vienkārši jāievada kvesta nosaukums.<br>
        - Ievades lauki ar <span style="color:#fb7d89">sarkanu rāmi</span> apzīmē to, ka raksts ar tādu adresi nav atrasts.
    </p>
    <!-- END BLOCK : skills-notes -->

    <!-- START BLOCK : skills-form -->
    <form class="form" action="/{category-url}" method="post">
        <!-- START BLOCK : skills-column -->
        <table class="rslist skill-reqs">
            <tr class="listhead">
                <td>&nbsp;</td>
                <td style="width:70px">Prasme</td>
                <td class="is-centered" style="width:70px">Līmenis</td>
                <td class="is-left" style="width:160px">Kvests</td>
            </tr>
            <!-- START BLOCK : single-skill -->
            <tr{special}>
                <td><img src="/bildes/fugue-icons/control-stop-square-small.png" title="{title} prasība" alt=""></td>
                <td style="width:70px">{title}</td>
                <td class="is-centered" style="width:50px">
                    <input class="level-input" type="text" name="level-{id}" value="{level}">
                </td>
                <td>
                    <input class="quest-input" type="text" name="quest-{id}" value="{page_title}">
                </td>
            </tr>
            <!-- END BLOCK : single-skill -->
        </table>
        <!-- END BLOCK : skills-column -->
        <div class="clearfix"></div>
        <input type="submit" name="submit" class="button" value="Veikt izmaiņas">
    </form>
    <!-- END BLOCK : skills-form -->
<!-- END BLOCK : skill-requirements -->


<!-- START BLOCK : rsmod-placeholders -->
<h1 class="rs_content_title">Iztrūkstošās pamācības</h1>
<!-- START BLOCK : rsmod-ph-addnew -->
<div style="display:block;margin:20px 0">
    <form class="form" action="/rsmod/ph" method="POST">
        <fieldset>
            <legend>pievienot jaunu</legend>
            <p style="clear:left;"><span class="cab">Nosaukums:</span> <input type="text" style="width:300px" name="title" /><br />
                <span class="cab">Sadaļa:</span>
                <select name="cat">
                    <option value="99">F2P kvesti</option>
                    <option value="100">P2P kvesti</option>
                    <option value="193">mini-kvesti</option>
                    <option value="160">minispēles</option>
                    <option value="792">Distractions & Diversions</option>
                    <option value="96">Ceļveži: pilsētas</option>
                    <option value="97">Ceļveži: salas</option>
                    <option value="101" selected="selected">Ceļveži: pazemes</option>
                    <option value="95">Ceļveži: Wilderness</option>
                    <option value="80">Ceļveži: citas vietas</option>
                </select><br />
                <span class="cab">Links 1:</span> <input style="width:300px;" type="text" name="url" /><br />
                <span class="cab">Links 2:</span> <input style="width:300px;" type="text" name="url2" />
                <input type="submit" name="submit" class="button primary" value="add" />
            </p>
        </fieldset>
    </form>
</div>
<div id="rs_info_block"><img src="/bildes/fugue-icons/exclamation.png" />Lai aplūkotu linkus, spied uz ikonām kreisajā sānā.</div>
<div id="rs_info_block"><img src="/bildes/fugue-icons/exclamation.png" />Lai nomainītu adresi vai nosaukumu, izdzēs un pievieno pa jaunam.</div>
<!-- END BLOCK : rsmod-ph-addnew --> 
<!-- START BLOCK : rsmod-phtable -->
<table class="rslist">
    <tr class="cab">
        <td colspan="3">{cat-title}</td>
        <td class="center">link 1</td>
        <td class="center">link 2</td>
        <td></td>
    </tr>
    <!-- START BLOCK : rsmod-ph-listitem -->
    <tr> 
        <td style="width:20px"><a href="javascript:void(0);"><img class="show-ph-links" src="/bildes/fugue-icons/blue-folder-open-document-text.png" /></a></td>
        <td class="ph-title" style="width:250px">{title}</td>
        <td style="width:100px"></td>
        <td class="center" style="width:80px"><img style="vertical-align:middle" src="/bildes/rs/{link1}.png" title="{url}" /></td>
        <td class="center" style="width:80px"><img style="vertical-align:middle" src="/bildes/rs/{link2}.png" title="{url2}" /></td>
        <td style="width:50px">
            <a class="button danger confirm" style="font-size: 90%;line-height: 1.1;padding: 3px 10px;" title="Dzēst" href="/rsmod/ph/?delete={id}">dzēst</a>
        </td>
    </tr>
    <tr class="ph-links" style="display:none">
        <td></td>
        <td colspan="4">
            <a href="{url}">{url}</a><br /><a href="{url2}">{url2}</a>
        </td>
        <td></td>
    </tr>
    <!-- END BLOCK : rsmod-ph-listitem -->
</table>
<!-- END BLOCK : rsmod-phtable -->
<!-- END BLOCK : rsmod-placeholders -->
