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

<!-- START BLOCK : rsmod-activities -->
<table class="rslist questlist" style="margin-left:20px">
    <tr>
        <td colspan="6" class="cab left">{cat-title}</td>	
    </tr>
    <!-- START BLOCK : activity -->
    <tr>
        <td class="center"><img src="/bildes/fugue-icons/blue-folder-open-document-text.png" /></td>
        <td style="width:300px"><a href="/read/{strid}">{title}</a></td>
        <td class="center">{auth}</td>
        <td>{page_id}</td>
        <td class="center"><a href="/rsmod/aedit/{page_id}"><img src="/bildes/rs/page_edit.png" title="Labot" alt="" /></a></td>
        <td class="center">
            <!-- START BLOCK : activity-delete -->
            <a class="confirm" href="/rsmod/aedit/?delete={page_id}"><img src="/bildes/fugue-icons/cross-button.png" title="Dzēst" alt="" /></a>
            <!-- END BLOCK : activity-delete -->
        </td>
    </tr>
    <!-- END BLOCK : activity -->
</table>  
<!-- END BLOCK : rsmod-activities -->

<!-- START BLOCK : rsmod-activities-edit -->
<div id="quest-form">
    <form class="form" action="/rsmod/aedit/?update={id}" method="POST">
        <fieldset>
            <legend><a href="/read/{strid}">{title}</a></legend>                 
            <p>Sākumpunkta atrašanās vieta:</p>    
            <p><input type="text" style="width:400px;" name="location" value="{location}" /></p>
            <p>Raksta stāvoklis:</p>   
            <select name="old">
                <option value="0">kvalitāte OK</option>
                <!-- START BLOCK : activity-age -->
                <option value="{nr}"{selected}>{old}</option>
                <!-- END BLOCK : activity-age -->
            </select>
            <select name="members">
                <option value="0">free</option>
                <option value="1"{selected-members}>members only</option>  
            </select>
            <p>Pamācības apraksts:</p>
            <textarea class="text" name="description">{description}</textarea>
            <br /><br />   
            <input class="button" type="submit" value="Apstiprināt" />
        </fieldset>
    </form>
</div>
<!-- END BLOCK : rsmod-activities-edit -->

<!-- START BLOCK : rsmod-pagelist -->
<table class="rslist" style="clear:both">
    <tr class="listhead">
        <td style="width:20px"></td>
        <td style="width:330px">guide</td>
        <td style="width:200px">auth</td>
        <td style="width:100px;">cat</td>
        <td></td>
    </tr>
    <!-- START BLOCK : pagelist-listitem -->
    <tr{border}>
        <td><img src="/bildes/rs/page.png" style="vertical-align:middle;" title="" alt="" /></td>
        <td><a href="/read/{strid}">{title}</a></td>
        <td class="center">{author}</td>
        <td>{category}</td>
        <td>{quest_chapter}</td>
    </tr>
    <!-- END BLOCK : pagelist-listitem -->
</table>
<!-- END BLOCK : rsmod-pagelist -->

<!-- START BLOCK : rsmod-cities-edit -->
<div style="clear:both;display:block">
    <form class="form" action="/rsmod/places/{id}" method="POST">
        <fieldset>
            <legend><a href="/read/{strid}">{title}</a> ceļveža sadaļa</legend>
            <b>Sadaļa:</b> 
            <select name="cat">
                <option value="0">Nekategorizēts</option>
                <!-- START BLOCK : rsmod-cities-cat -->
                <option value="{nr}"{selected}>{cat}</option>
                <!-- END BLOCK : rsmod-cities-cat -->
            </select>                    
            <input class="button" name="submit" type="submit" value="Apstiprināt" />
        </fieldset>
    </form>
</div>
<!-- END BLOCK : rsmod-cities-edit -->

<!-- START BLOCK : rsmod-cities -->
<h1 class="rs_content_title">Ceļveži</h1>
<!-- START BLOCK : cities-cat -->
<b>{title}</b><br />
<!-- START BLOCK : cat-listitem -->
<a href="/read/{strid}">{title}</a><br />
<!-- END BLOCK : cat-listitem -->
<!-- END BLOCK : cities-cat -->
<h1 class="rs_content_title">Ceļvežu pārvaldīšana</h1>
<table class="rslist">
    <tr class="listhead">
        <td style="width:16px;"></td>	
        <td style="width:200px;">nosaukums</td>
        <td style="width:90px">sadaļa</td>
        <td style="width:30px;">pageid</td>
        <td style="width:120px;">autors</td>
        <td style="width:16px;"></td>
        <td style="width:16px;"></td>
    </tr>
    <!-- START BLOCK : city -->
    <tr>
        <td class="center"><span style="color:green;">{ready}</span></td>
        <td><a href="/read/{strid}">{title}</a></td>
        <td class="center"><span style="color:lightcoral"><b>{c-title}</b></span></td>
        <td>{id}</td>
        <td class="center">{author}</td>
        <td class="center"><a href="/rsmod/places/?edit={id}"><img src="/bildes/rs/page_edit.png" title="Labot" alt="" /></a></td>
        <td class="center">
            <!-- START BLOCK : city-delete -->
            <a class="confirm" href="/rsmod/places/?delete={id}"><img src="/bildes/rs/delete.png" title="Dzēst" alt="" /></a>
            <!-- END BLOCK : city-delete -->
        </td>
    </tr>
    <!-- END BLOCK : city -->
</table>
<!-- END BLOCK : rsmod-cities -->