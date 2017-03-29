<h3 style="margin-top:40px">Piešķirt lietotājiem apbalvojumu</h3>
<div class="ca-form" style="margin-top:10px">
    <form method="post" action="/{category-url}/award_user?token={new-award}">
        <label for="user_ids">Saraksts ar lietotāju ID (atdalītiem ar komatiem):</label>
        <input id="user_ids" type="text" name="user_ids" placeholder="1, 115, 21018">
        <label for="sel_award">Izvēlies apbalvojumu:</label>
        <select name="sel_award" id="sel_award">
            <!-- START BLOCK : sel-custom-award -->
            <option value="{id}">{award_title}</option>
            <!-- END BLOCK : sel-custom-award -->
        </select>
        <input type="submit" name="submit" class="button primary" value="Pievienot">
    </form>
</div>

<h3 style="margin-top:40px">Jauns apbalvojuma veids</h3>
<div class="ca-form" style="margin-top:10px">
    <form method="post" enctype="multipart/form-data" action="/{category-url}/new_award?token={new-award-type}">
        <label for="aw_title">Apbalvojuma nosaukums:</label>
        <input id="aw_title" type="text" name="aw_title">
        <label for="aw_image">Attēls (32x32px):</label>
        <input id="aw_image" type="file" class="text" name="image">
        <input type="submit" name="submit" class="button primary" value="Pievienot">
    </form>
</div>

<h3 style="margin-top:40px">Apbalvojumu saraksts</h3>
<div class="tabMain">
    <!-- START BLOCK : award-list -->
	<ul class="ca-list">
		<!-- START BLOCK : single-award -->
		<li>
            <div class="ca-image-block"><img src="{img_server}/dati/bildes/awards/{img_title}.png" title="{img_title}" alt="{img_title}"></div>
            <div class="ca-title-block">{award_title} (/dati/bildes/awards/{img_title}.png)</div>
        </li>
		<!-- END BLOCK : single-award -->
	</ul>
    <!-- END BLOCK : award-list -->
</div>
