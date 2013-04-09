<!-- START BLOCK : notepad-->
<h1>Pierakstu blociņš <a href="javascript:void();"><abbr title="Piezīmes ir vieta, kur pierakstīt linkus, dziesmu nosaukumus vai pilnīgi jebko citu. Šīs piezīmes nekad netiek dzēstas un citiem lietotājiem nav redzamas. Tās var izmantot arī kā vietu, kur veidot rakstu melnrakstus.">(?)</abbr></a></h1>

<ul class="tabs">
	<!-- START BLOCK : np-menu-node-->
	<li><a href="/piezimes/read/{id}" class="{sel}">{title}</a></li>
	<!-- END BLOCK : np-menu-node-->
	<li><a href="/piezimes/new" class="{active-tab-new}"><span class="tools">+</span></a></li>
</ul>

<div class="tabMain">
	<!-- START BLOCK : notepad-view-->
	<a href="/piezimes/edit/{id}" class="button">Labot lapu</a> [<a href="/piezimes/delete/{id}" class="red confirm">dzēst</a>]
	<div class="c"></div>
	{content}
	<!-- END BLOCK : notepad-view-->
	<!-- START BLOCK : notepad-edit-->
	<form class="form" action="" method="post">
		<div id="autosave-status"></div>
		<!-- START BLOCK : notepad-title-->
		<p>
		  <label>Nosaukums:</label><br />
		  <input type="text" name="title" value="" class="text" />
		</p>
		<!-- END BLOCK : notepad-title-->
		<p>
			<textarea name="note-text" id="note-text" cols="94" rows="40" style="width: 100%;height:700px">{content}</textarea>
		</p>
		<p>
		  <input type="submit" name="submit" value="Saglabāt" class="button" />
		</p>
	</form>
	<!-- END BLOCK : notepad-edit-->
</div>
<!-- END BLOCK : notepad-->
