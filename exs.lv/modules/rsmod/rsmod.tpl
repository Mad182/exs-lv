<h1 class="rs-content-title">{content-title}</h1>

<!-- START BLOCK : series-form -->
<form class="form" action="/series/update" method="post">
    <!-- START BLOCK : series-column -->
    <table class="rslist col-list">
    <tr class="listhead">
        <td style="width:20px"></td>
        <td style="width:250px">Nosaukums</td>
        <td style="width:70px">Secība</td>
    </tr>
    <!-- START BLOCK : single-series -->
    <tr>
        <td><img src="/bildes/fugue-icons/blue-folder-open-document-text.png" alt=""></td>
        <td><input class="transp" style="width:220px" name="title_{id}" value="{title}"></td>
        <td class="center">
            <select name="order_{id}">
                <!-- START BLOCK : selection-option -->
                <option value="{order}"{selected}>{order}</option>
                <!-- END BLOCK : selection-option -->
            </select>
        </td>
    </tr>
    <!-- END BLOCK : single-series -->
    </table>
    <!-- END BLOCK : series-column -->
  <input class="button primary" type="submit" name="submit" value="Mainīt secību">
<form>
<!-- END BLOCK : series-form -->

<!-- START BLOCK : rsmod-quests-order -->
<div class="qorder" style="{clearleft}">
<form class="form" action="/rsmod/order/{story}" method="POST">
<table class="rslist">
<tr class="listhead"><td style="text-align:left" colspan="2">{title}</td></tr>
<!-- START BLOCK : order-quest -->
<tr>
<td style="width:100px;"><a href="/read/{strid}">{quest-title}</a></td>
<td style="width:50px;">    
    <select name="{qid}_order">
    <!-- START BLOCK : order-nr -->
      <option value="{nr}"{selected}>{nr}</option>
    <!-- END BLOCK : order-nr -->  
    </select>   
</td>
</tr>
<!-- END BLOCK : order-quest -->
</table>
<input type="submit" name="submit" class="button danger" value="Veikt izmaiņas" />
</form>
</div>
<!-- END BLOCK : rsmod-quests-order -->

<!-- START BLOCK : rsmod-quests-skills -->
<h1 class="rs_content_title">Kvestiem nepieciešamo prasmju rediģēšana</h1>
<form class="form" action="/rsmod/qskills/" method="POST">
<!-- START BLOCK : skills-col -->
<table class="rslist col-list" style="margin-left:10px">
<tr class="listhead">
  <td></td>
  <td class="" style="width:90px">Prasme</td>
  <td class="center" style="width:70px">Līmenis</td>
  <td class="left" style="width:160px;">Quests</td>
</tr>
<!-- START BLOCK : level -->
<tr>
  <td><img src="/bildes/fugue-icons/arrow-step-out.png" /></td>
  <td class=" cab" style="width:90px">{skill}</td>
  <td class="center" style="width:50px"><input type="text" class="level" name="{id}_level" value="{level}" /></td>
  <td><input type="text" class="quest" name="{id}_quest" value="{quest}" /></td>
</tr>
<!-- END BLOCK : level -->
</table>
<!-- END BLOCK : skills-col -->
<br /><br />
<input type="submit" name="submit" class="button danger" value="Veikt izmaiņas" />
</form>
<!-- END BLOCK : rsmod-quests-skills -->

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

<!-- START BLOCK : rsmod-questedit -->
<div id="quest-form">
<form class="form" action="/rsmod/quest/{id}" method="POST">
<fieldset>
<legend><a href="/read/{strid}">{title}</a> pamācības informācijas rediģēšana</legend>                 
  <p>Sākumpunkta atrašanās vieta:</p>    
    <p><input type="text" style="width:400px;" name="location" value="{location}" /></p> 
  <p>Nepieciešamās prasmes:</p>
    <p class="info">(formāts: 55 Agility, 77 Runecrafting, 111 Mining)</p>
    <textarea class="text" name="skills">{skills}</textarea>
  <p>Nepieciešamie kvesti:</p>
    <p class="info">(formāts: Lost Tribe, While Guthix Sleeps, Monkey Madness)</p>
    <textarea class="text" name="quests">{quests}</textarea>
  <p>Citas prasības:</p>
    <textarea class="text" name="extra">{extra}</textarea>   
  <select name="difficulty">
    <option value="0">not set</option>
    <!-- START BLOCK : rsmod-guide-difficulty -->
    <option value="{nr}"{selected}>{level}</option>
    <!-- END BLOCK : rsmod-guide-difficulty -->
  </select>  
  <select name="length">
    <option value="0">not set</option>
    <!-- START BLOCK : rsmod-guide-length -->
    <option value="{nr}"{selected}>{length}</option>
    <!-- END BLOCK : rsmod-guide-length -->
  </select>  
  <select name="storyline">
    <option value="0">ārpus sērijas</option>
    <!-- START BLOCK : rsmod-guide-storyline -->
    <option value="{nr}"{selected}>{storyline}</option>
    <!-- END BLOCK : rsmod-guide-storyline -->
  </select>  
  <select name="old">
    <option value="0">kvalitāte OK</option>
    <!-- START BLOCK : rsmod-guide-age -->
    <option value="{nr}"{selected}>{old}</option>
    <!-- END BLOCK : rsmod-guide-age -->
  </select>
  <p>Pamācības apraksts:</p>
    <textarea class="text" name="description">{description}</textarea>
  <p>Datums, kad ieviests spēlē:</p>
    <p class="info">(formāts: dd/mm/yyyy)</p>
    <input type="text" name="date" value="{date}" />
<br /><br />   
<input class="button" type="submit" value="Apstiprināt" />
</fieldset>
</form>
</div>
<!-- END BLOCK : rsmod-questedit -->

<!-- START BLOCK : rsmod-questlist -->
<table class="rslist questlist" style="margin-left:20px">
<tr>
  <td colspan="9" class="cab left">{cat-title}</td>	
</tr>
<!-- START BLOCK : questlist-quest -->
<tr>
	<td class="center"><img src="/bildes/fugue-icons/blue-folder-open-document-text.png" /></td>
	<td style="width:222px"><a href="/read/{strid}">{title}</a></td>
	<td class="center">{auth}</td>
	<td class="center">{mq}</td>
	<td class="center">{level}</td>
	<td class="center" style="color:#336699">{storline}</td>
	<td>{page_id}</td>
	<td class="center"><a href="/rsmod/qedit/{page_id}"><img src="/bildes/rs/page_edit.png" title="Labot" alt="" /></a></td>
  <td class="center">
	  <!-- START BLOCK : quest-delete -->
    <a class="confirm" href="/rsmod/qedit/?delete={page_id}"><img src="/bildes/fugue-icons/cross-button.png" title="Dzēst" alt="" /></a>
    <!-- END BLOCK : quest-delete -->
  </td>
</tr>
<!-- END BLOCK : questlist-quest -->
</table>  
<!-- END BLOCK : rsmod-questlist -->

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
