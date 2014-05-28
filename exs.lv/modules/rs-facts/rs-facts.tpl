<ul class="tabs">
	<li><a href="/{category-url}"><span class="pages">RuneScape fakti</span></a></li>
</ul>

<!-- START BLOCK : block-edit -->
<h2 style="font-weight:bold">Labot faktu</h2>

<form id="edit-fact" class="form" method="post">
	<fieldset>
        <input type="hidden" name="anti-xsrf" value="{xsrf}">
		<p>
			<label for="edit-fact">Fakts:</label><br>
			<textarea id="edit-fact" class="text" name="edit-fact" style="width: 500px; height: 100px;" cols="10" rows="4">
                {text}
            </textarea>
		</p>
		<p>
			<input type="submit" id="submit" class="button primary" name="submit" value="Saglabāt">
		</p>
	</fieldset>
</form>
<!-- START BLOCK : block-edit -->

<!-- START BLOCK : block-add-->
<h2 style="font-weight:bold">Jauns fakts</h2>

<form id="new-fact" class="form" method="post">
	<fieldset>
        <input type="hidden" name="anti-xsrf" value="{xsrf}">
		<p>
			<textarea id="new-fact" class="text" name="new-fact" style="width: 500px; height: 100px;" cols="10" rows="4">
            </textarea>
		</p>
		<p>
			<input type="submit" id="submit" class="button primary" name="submit" value="Pievienot">
		</p>
	</fieldset>
</form>
<!-- START BLOCK : block-add-->

<!-- START BLOCK : block-list -->
<table class="table facts-table">
	<tr>
        <th style="padding:0"></th>
		<th>Teksts</th>
		<th>Labot</th>
		<th>Dzēst</th>
	</tr>
	<!-- START BLOCK : single-fact -->
	<tr>
        <td class="row-counter">{counter}</td>
		<td>{text}</td>
		<td class="row-center">
            <a href="/{category-url}/edit/{id}">
                <img src="/bildes/fugue-icons/receipt--pencil.png" title="Labot" alt="">
            </a>
        </td>
		<td class="row-center">
            <a class="confirm" href="/{category-url}/delete/{id}?val={xsrf}">
                <img src="/bildes/fugue-icons/cross.png" title="Dzēst" alt="">
            </a>
        </td>
	</tr>
	<!-- END BLOCK : single-fact -->
</table>
<!-- START BLOCK : block-list -->
