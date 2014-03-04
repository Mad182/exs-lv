<!-- START BLOCK : quest-list -->
<table class="rslist" style="margin-left:10px;margin-top:25px">
    <tr class="listhead">
		<td colspan="2">{cat-title}</td>
		<td style="width:100px;text-align:center">Autors</td>
		<td style="width:100px">&nbsp;</td>	
		<td style="width:150px">&nbsp;</td>	
		<td style="width:50px">&nbsp;</td>	
		<td style="width:30px">&nbsp;</td>	
	</tr>
    <!-- START BLOCK : list-guide -->
    <tr>
        <td style="width:20px"><img src="/bildes/fugue-icons/blue-folder-open-document-text.png" alt=""></td>
        <td style="width:200px"><a href="/read/{page_strid}">{page_title}</a></td>
        <td style="text-align:center">{user_nick}</td>
        <td style="text-align:center">{rspage_difficulty}</td>
        <td style="color:#336699;text-align:center">{rsclasses_title}</td>
        <td>{page_id}</td>
        <td>
            <a href="/{category-url}/edit/{page_id}"><img src="/bildes/fugue-icons/pencil.png" title="Labot" alt=""></a>
        </td>
    </tr>
    <!-- END BLOCK : list-guide -->
</table>  
<!-- END BLOCK : quest-list -->

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