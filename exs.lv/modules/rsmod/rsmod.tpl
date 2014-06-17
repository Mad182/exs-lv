<!-- INCLUDE BLOCK : sub-template -->

<!-- START BLOCK : series-form -->
<h1 class="rs-content-title">Quest storylines' management</h1>

<form class="form" action="/series/update" method="post">
	<!-- START BLOCK : series-column -->
	<table class="rslist col-list" style="background:#fff">
    <tr class="listhead">
			<td style="width:20px"></td>
			<td style="width:230px">Nosaukums</td>
			<td style="width:70px;text-align:center">Secība</td>
            <td>&nbsp;</td>
    </tr>
    <!-- START BLOCK : single-series -->
    <tr>
			<td><img src="/bildes/fugue-icons/layer-mask.png" alt=""></td>
			<td><input class="transp-input" style="width:220px" name="title_{class_id}" value="{class_title}"></td>
			<td class="center">
				<select name="order_{class_id}">
					<!-- START BLOCK : selection-option -->
					<option value="{ordered}"{selected}>{ordered}</option>
					<!-- END BLOCK : selection-option -->
				</select>
			</td>
            <td><img class="show-related-quests" src="/bildes/fugue-icons/arrow-curve-180-double.png" alt="related quests"></td>
    </tr>
    <tr id="series-{class_id}" class="series-hidden">
        <td colspan="4">
        
            <form action="/series/change-order/{class_id}" method="post">
            <ul class="related-quests">
            <!-- START BLOCK : related-quests -->
                <!-- START BLOCK : related-quest -->
                <li>
                    <select name="related-{rspages_id}">
                    <!-- START BLOCK : option-param -->
                        <option value="{value}"{selected}>{value}</option>
                    <!-- END BLOCK : option-param -->
                    </select>
                    <a id="quest-{rspages_id}" href="/read/{pages_strid}">{pages_title}</a>
                </li>
                <!-- END BLOCK : related-quest -->
            <!-- END BLOCK : related-quests -->            
            </ul>
                <input type="submit" class="button primary" name="submit" value="Atjaunināt secību">
            </form>
            
            <!-- START BLOCK : no-related-quests -->
            <p style="margin-left:20px">Nav piesaistītu pamācību!</p>
            <!-- END BLOCK : no-related-quests -->
            
            <a class="open-quest-list button danger" href="/series/list-quests/{class_id}">Rediģēt sarakstu</a>
        </td>
    </tr>
    <!-- END BLOCK : single-series -->
	</table>
	<!-- END BLOCK : series-column -->
    <div class="clearfix"></div>
  <input class="button primary" type="submit" name="submit" value="Mainīt secību">
</form>
<!-- END BLOCK : series-form -->

<div class="clearfix"></div>

<!-- START BLOCK : quests-skills -->
<h1 class="rs-content-title" style="margin-top:30px">Skills needed to complete all quests</h1>

<form class="form" action="/series/skills" method="post">
    <!-- START BLOCK : skills-column -->
    <table class="quests-skill-table rslist">
        <tr class="listhead">
            <td>&nbsp;</td>
            <td style="width:70px">Prasme</td>
            <td style="width:70px;text-align:center">Līmenis</td>
            <td style="width:160px;text-align:left">Kvests</td>
        </tr>
        <!-- START BLOCK : single-skill -->
        <tr>
            <td><img src="/bildes/fugue-icons/layer-mask.png" alt=""></td>
            <td style="width:70px">{skill}</td>
            <td style="width:50px;text-align:center">
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
    <input type="submit" name="submit" class="button primary" value="Veikt izmaiņas">
</form>
<!-- END BLOCK : quests-skills -->


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
