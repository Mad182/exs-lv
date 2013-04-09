<!-- START BLOCK : poll-box-->
<h3>Aptauja</h3>
<div class="box">
<p><strong>{poll-title}</strong></p>
<!-- START BLOCK : poll-answers-->
<ol class="poll-answers">
<!-- START BLOCK : poll-answers-node-->
<li>{poll-answer-question}<div><span>{poll-answer-percentage}%</span><div style="width: {poll-answer-percentage}%"></div></div></li>
<!-- END BLOCK : poll-answers-node-->
</ol>
Balsojuši: {poll-totalvotes}<br />
<a href="{ppage-id}">Komentāri</a> | <a href="/?c=156">Arhīvs</a>
<!-- END BLOCK : poll-answers-->
<!-- START BLOCK : poll-questions-->
<form name="poll" method="post" action="{page-url}">
<fieldset>
<!-- START BLOCK : poll-error-->
<p>{poll-error}</p>
<!-- END BLOCK : poll-error-->
<!-- START BLOCK : poll-options-->
<ol id="poll-questions">
<!-- START BLOCK : poll-options-node-->
<li><label><input type="radio" name="questions" value="{poll-options-id}" /> {poll-options-question}</label></li>
<!-- END BLOCK : poll-options-node-->
</ol>
<input type="submit" name="vote" value="Balsot!" />
<!-- END BLOCK : poll-options-->
</fieldset>
</form>
<!-- END BLOCK : poll-questions-->
</div>
<!-- END BLOCK : poll-box-->