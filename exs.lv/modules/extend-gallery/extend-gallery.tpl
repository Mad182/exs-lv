<!-- START BLOCK : buygal-->
<h2>Paplašināt galeriju</h2>
<p>Tavs galerijas limits ir {maximg} attēli.</p>
<p>Galerijas palielināšana par 100 attēliem maksā <strong>3</strong> expunktus.</p>
{pay}
<p>Tev ir <strong>{credit}</strong> expunkti.</p>

<h4>Kā iegādāties 5 kredīta punktus?</h4>
<div class="box">
	<ul class="tabs">
		<li><a href="/payment-info" class="active ajax" id="default-payment-link"><img src="{static-server}/bildes/flags/lv.png" alt="" />&nbsp;Latvijā</a></li>
	</ul>
	<div id="pay" class="ajaxbox">
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('#default-payment-link').click();
	});
</script>

<!-- END BLOCK : buygal-->
