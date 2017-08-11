<!-- START BLOCK : statistics-body-->
<h1>Lapas statistika</h1>

<ul class="tabs">
	<li><a href="/statistika" class="active"><span class="friends">Statistika</span></a></li>
	<li><a href="/top"><span class="pages">Populārākie raksti</span></a></li>
	<li><a href="/blogstats"><span class="pages">Blogu statistika</span></a></li>
	<li><a href="/medalas"><span class="awards">Medaļas</span></a></li>
</ul>

<div class="tabMain">

	<div class="half-left">
		<h3>Visādi cipari</h3>
		<br />
		<p>
			Kopā lietotāji: {statistics-users}<br />
			Raksti un foruma tēmas: {statistics-pages}<br />
			Bildes galerijās: {statistics-images}<br />
			Komentāri kopā: {statistics-comments}<br />
			Privātās vēstules: {statistics-pms}<br />
			Ieraksti miniblogos: {statistics-miniblog}
		</p>
	</div>

	<div class="half-right">
		<!-- START BLOCK : usertop-->
		<h3>Šodien aktīvākie</h3>
		<p style="font-size: 90%">
			<!-- START BLOCK : usertop-node-->
			<a href="{url}">{user}</a>&nbsp;({today}) <br />
			<!-- END BLOCK : usertop-node-->  
			<!-- START BLOCK : usertop-self-->
			<span style="color: #aaa;border-top: 1px solid #ccc">*{user}&nbsp;({today})</span>
			<!-- END BLOCK : usertop-self-->
		</p>
		<!-- END BLOCK : usertop-->
	</div>
	<div class="c"></div>

	<div class="half-left">

		<h3>Lielāko muldētāju TOP 100</h3>
		<ol>
			<!-- START BLOCK : spamerlist-node-->
			<li><a href="{url}">{spamer-nick}</a> - {spamer-posts}</li>
			<!-- END BLOCK : spamerlist-node-->
		</ol>

	</div>

	<div class="half-right">

		<h3>Labās karmas top 100</h3>
		<ol>
			<!-- START BLOCK : karma-node-->
			<li><a href="{url}">{spamer-nick}</a> - {karma}</li>
			<!-- END BLOCK : karma-node-->
		</ol>

	</div>
	<div class="c"></div>

</div>

<!-- END BLOCK : statistics-body-->
