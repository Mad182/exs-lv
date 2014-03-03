<!-- START BLOCK : quest-list -->
<table class="rslist" style="margin-left:20px">
    <tr>
        <td colspan="8" style="font-weight:bold">{cat-title}</td>	
    </tr>
    <!-- START BLOCK : list-guide -->
    <tr>
        <td><img src="/bildes/fugue-icons/blue-folder-open-document-text.png" alt=""></td>
        <td style="width:222px"><a href="/read/{page_strid}">{page_title}</a></td>
        <td>{user_nick}</td>
        <td>{mq}</td>
        <td>{rspage_level}</td>
        <td style="color:#336699">{storyline}</td>
        <td>{page_id}</td>
        <td>
            <a href="/{category-url}/edit/{page_id}"><img src="/bildes/runescape/page.png" title="Labot" alt=""></a>
        </td>
    </tr>
    <!-- END BLOCK : list-guide -->
</table>  
<!-- END BLOCK : quest-list -->

<!-- START BLOCK : quest-edit -->
<p style="text-align:right">
    <a href="/{category-url}">Uz sarakstu</a>
</p>
<form class="form info-form" action="/{category-url}/edit/{page_id}" method="post">
    <fieldset>
    
        <legend><a href="/read/{page_strid}">{page_title}</a> informācija</legend>                 
        <p><strong>Sākumpunkta atrašanās vieta:</strong></p>    
        <p><input type="text" style="width:400px" name="location" value="{rspage_location}"></p> 
        
        <p><strong>Nepieciešamās prasmes:</strong></p>
        <p>(formāts: 55 Agility, 77 Runecrafting, 111 Mining)</p>
        <textarea name="skills">{rspage_skills}</textarea>
        
        <p><strong>Nepieciešamie kvesti:</strong></p>
        <p class="info">(formāts: Lost Tribe, While Guthix Sleeps, Monkey Madness)</p>
        <textarea name="quests">{rspage_quests}</textarea>
        
        <p><strong>Citas prasības:</strong></p>
        <textarea name="extra">{rspage_extra}</textarea>   
        
        <br><br>
        
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
        
        <p><strong>Pamācības apraksts:</strong></p>
        <textarea name="description">{rspage_description}</textarea>
        
        <p><strong>Datums, kad ieviests spēlē:</strong></p>
        <p>(formāts: dd/mm/yyyy)</p>
        <input type="text" name="date" value="{rspage_date}"><br><br>   
        
        <input class="button" type="submit" value="Apstiprināt">
        
    </fieldset>
</form>
<!-- END BLOCK : quest-edit -->