<!-- START BLOCK : distraction-list -->
<table class="rslist" style="margin-left:20px;margin-top:25px">
    <tr class="listhead">
		<td colspan="2">{cat-title}</td>
		<td style="width:140px;text-align:center">Autors</td>
		<td style="width:50px">&nbsp;</td>	
		<td style="width:30px">&nbsp;</td>	
	</tr>
    <!-- START BLOCK : list-guide -->
    <tr>
        <td><img src="/bildes/fugue-icons/blue-folder-open-document-text.png" alt=""></td>
        <td style="width:222px"><a href="/read/{page_strid}">{page_title}</a></td>
        <td style="text-align:center">{user_nick}</td>
        <td style="text-align:center">{page_id}</td>
        <td>
            <a href="/{category-url}/edit/{page_id}"><img src="/bildes/fugue-icons/pencil.png" title="Labot" alt=""></a>
        </td>
    </tr>
    <!-- END BLOCK : list-guide -->
</table>  
<!-- END BLOCK : distraction-list -->

<!-- START BLOCK : distraction-edit -->
<p style="text-align:right">
    <a href="/{category-url}" class="button rs-button" style="top:0">Uz sarakstu</a>
</p>
<form class="form info-form" action="/{category-url}/edit/{page_id}" method="post">

    <fieldset>
        <legend><a href="/read/{page_strid}">{page_title}</a></legend>                 
        <p><strong>Sākumpunkta atrašanās vieta:</strong></p>    
        <p><input type="text" style="width:400px;" name="location" value="{rspage_location}" /></p>
        <select name="members_only">
            <option value="0">Free</option>
            <option value="1"{selected-members}>Members only</option>  
        </select>
        <p><strong>Pamācības apraksts:</strong></p>
        <textarea class="text" name="description">{rspage_description}</textarea>
        <br><br>   
        <input class="button danger" type="submit" value="Apstiprināt">
        
    </fieldset>
</form>
<!-- END BLOCK : distraction-edit -->
