<!-- START BLOCK : img-upload-->
<h1>Attēlu augšupielāde</h1>
<!-- START BLOCK : img-upload-success-->
<h4>BBCODE</h4>
<div class="box">
	<code>[img]https://img.exs.lv/{path}/{file}[/img]</code>
</div>
<h4>BBCODE (sīkbilde ar linku)</h4>
<div class="box">
	<code>[url=&quot;https://img.exs.lv/{path}/{file}&quot;][img]https://img.exs.lv/{path}/small/{file}[/img][/url]</code>
</div>
<h4>HTML</h4>
<div class="box">
	<code>&lt;img src=&quot;https://img.exs.lv/{path}/{file}&quot; alt=&quot;{file}&quot; /&gt;</code>
</div>
<h4>HTML (sīkbilde ar linku)</h4>
<div class="box">
	<code>&lt;a href=&quot;https://img.exs.lv/{path}/{file}&quot; class=&quot;lightbox post-url thb-image&quot;&gt;
		&lt;img src=&quot;https://img.exs.lv/{path}/small/{file}&quot; alt=&quot;{file}&quot; /&gt;
		&lt;/a&gt;</code>
</div>
<h4>Tiešais links</h4>
<div class="box">
	<code>https://img.exs.lv/{path}/{file}</code>
</div>
<h4>Attēls</h4>
<div class="box">
	<img src="https://img.exs.lv/{path}/{file}" alt="" />
</div>
<!-- END BLOCK : img-upload-success-->
<form class="form" action="{page-url}" method="post" enctype="multipart/form-data">
	<fieldset>
		<p>
			<label>Attēls:</label><br />
			<input type="file" name="new-image" />
		</p>
		<p>
			<label><input type="checkbox" name="resize" /> samazināt, lai liktu rakstā (540px)</label>
		</p>
        <!-- START BLOCK : rs-watermark-checkbox -->
        <p>
			<label><input type="checkbox" name="add-watermark" /> pievienot runescape.exs.lv ūdenszīmi</label>
		</p>
        <p>
			<label><input type="checkbox" name="position-left" /> novietot ūdenszīmi kreisajā apakšējā stūrī (pēc noklusējuma - apakšējais labais stūris)</label>
		</p>
        <!-- END BLOCK : rs-watermark-checkbox -->
		<p>
			<input type="submit" class="button primary" value="Upload!" />
		</p>
	</fieldset>
</form>
<!-- START BLOCK : img-upload-item-->
<div style="padding:5px">
	<a class="lightbox" href="https://img.exs.lv/{path}/{file}"><img class="av" src="https://img.exs.lv/{path}/small/{file}" alt="" width="100" /></a>

	<div style="font-size:80%">

		<pre style="margin-left:120px">https://img.exs.lv/{path}/{file}</pre>

		<pre style="margin-left:120px">[img]https://img.exs.lv/{path}/{file}[/img]</pre>

		<pre style="margin-left:120px">[url=&quot;https://img.exs.lv/{path}/{file}&quot;][img]https://img.exs.lv/{path}/small/{file}[/img][/url]</pre>

		<pre>&lt;img src=&quot;https://img.exs.lv/{path}/{file}&quot; alt=&quot;{file}&quot; /&gt;</pre>

		<pre>&lt;a href=&quot;https://img.exs.lv/{path}/{file}&quot; class=&quot;lightbox thb-image&quot;&gt;&lt;img src=&quot;https://img.exs.lv/{path}/small/{file}&quot; alt=&quot;{file}&quot; /&gt;&lt;/a&gt;</pre>

	</div>

	<div class="c"></div>

</div>
<div class="c"></div>
<!-- END BLOCK : img-upload-item-->
<p class="core-pager">{pager-next} {pager-numeric} {pager-prev} </p>
<!-- END BLOCK : img-upload-->
