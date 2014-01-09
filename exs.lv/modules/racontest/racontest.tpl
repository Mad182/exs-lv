<!-- START BLOCK : ra-contest -->
<h1 style="font-size:13px;font-weight:bold;"><a class="rss" title="Seko līdzi jaunumiem šajā lapas sadaļā, izmantojot RSS!" href="/rss.php?view={articles-catid}" rel="feed">rss</a> {articles-title}</h1>
<form class="form" action="/racontest/" method="POST">
	<fieldset>
		<legend>Atlase</legend>
		<span style="font-size:90%;">Formāts: GGGG-MM-DD hh:mm:ss</span>
		<p>
			Sākums: <input style="width:150px;" type="text" name="start" value="{start}" /> 
			Beigas: <input style="width:150px;" type="text" name="end" value="{end}" />
		</p>
		<input class="button" type="submit" value="Skatīt" />
	</fieldset>
</form>
<div style="margin:20px 0 20px 50px;">
	<!-- START BLOCK : contest-cat -->
  <p style="margin:13px 0 0 0;padding:0;"><b>{contest-ctitle}</b></p>
  <table style="font-size:12px">
		<!-- START BLOCK : contest-topic -->     
		<tr>
			<td style="width:90px;">{addedby}</td>
			<td><a href="/read/{strid}">{title}</a></td>
		</tr>         
		<!-- END BLOCK : contest-topic -->
  </table>
	<!-- END BLOCK : contest-cat -->
</div>
<!-- END BLOCK : ra-contest -->