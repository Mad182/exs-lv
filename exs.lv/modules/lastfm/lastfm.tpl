<!-- START BLOCK : lastfm-auth-->
<h1>Last.fm integrācija</h1>
<p>
	Lai citi redzētu, ko tu klausies LastFM, tev jāatļauj exs.lv piekļuvi savam pēdējo klausīto dziesmu sarakstam!
</p>
<p>
	<a class="button primary" href="http://www.last.fm/api/auth/?api_key={key}">Atļaut piekļuvi last.fm</a>
</p>
<!-- END BLOCK : lastfm-auth-->

<!-- START BLOCK : lastfm-success-->
<h1>Last.fm integrācija</h1>
<p>
	Tavs profils ir veiksmīgi savienots ar last.fm!
</p>
<form class="form" action="" method="post">
	<fieldset>
		<legend>Last.fm iestatījumi</legend>
		<label><input type="checkbox" name="friends" class="ajax-checkbox"{friendsmark} />Rādīt tikai draugu dziesmas</label>
		<input type="hidden" name="friends-do" value="1" />
	</fieldset>
</form>
<!-- END BLOCK : lastfm-success-->

