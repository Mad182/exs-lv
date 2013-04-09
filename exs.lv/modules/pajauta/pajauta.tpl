<!-- START BLOCK : pajauta-add-->
<h1>Pievienot jautājumu</h1>
<form class="form" action="" method="post">
<fieldset>
  <p>
    <label for="question">Jautājums</label><br />
    <input name="question" id="question" type="text" class="title" />
  </p>
  <p>
    <label for="answ0">Atbilde #1</label><br />
    <input name="answ0" id="answ0" type="text" class="text" />
  </p>
  <p>
    <label for="answ1">Atbilde #2</label><br />
    <input name="answ1" id="answ1" type="text" class="text" />
  </p>
  <p>
    <input type="submit" value="Saglabāt" />
  </p>
</fieldset>
</form>
<!-- END BLOCK : pajauta-add-->
<!-- START BLOCK : pajauta-q-->
<h1 style="text-align: center;">{nick}: {question}</h1>
<div id="answer-ask">
	<p style="float: left; width: 40%;padding: 10px 20px;text-align: center;font-size:20px;">
		<a href="#" onclick="return postanswer(0);">{answ0}</a>
	</p>
	<p style="float: right; width: 40%;padding: 10px 20px;text-align: center;font-size:20px;">
		<a href="#" onclick="return postanswer(1);">{answ1}</a>
	</p>
	<div class="c"></div>
</div>
<div id="answer-results"></div>
<p style="text-align: right;">
<a href="/Pajauta/pievienot">Pievienot savu jautājumu</a><br />
<a href="/Pajauta/arhivs">Arhīvs</a>
</p>
{comments}
<!-- END BLOCK : pajauta-q-->
<!-- START BLOCK : pajauta-a-->
<h1 style="text-align: center;">{nick}: {question}</h1>
<div id="answer-results">
	<p>{answ0}: <strong>{count0}x</strong><br />
		<span style="display:block;width:100%;height:10px;font-size:1px;line-height:0;">
		  <span style="-webkit-border-radius:3px;-moz-border-radius:3px;border-radius:3px;display:block;width:{percent0}%;background:#a85;height:10px;font-size:1px;line-height:0;">
		</span>
	</p>
	<p>{answ1}: <strong>{count1}x</strong><br />
		<span style="display:block;width:100%;height:10px;font-size:1px;line-height:0;">
		  <span style="-webkit-border-radius:3px;-moz-border-radius:3px;border-radius:3px;display:block;width:{percent1}%;background:#58a;height:10px;font-size:1px;line-height:0;">
		</span>
	</p>
<p>
<strong><a href="/Pajauta">Citu jautājumu &raquo;</a></strong>
</p>
</div>
<p style="text-align: right;">
<a href="/Pajauta/pievienot">Pievienot savu jautājumu</a><br />
<a href="/Pajauta/arhivs">Arhīvs</a>
</p>
{comments}
<!-- END BLOCK : pajauta-a-->
<!-- START BLOCK : pajauta-list-->
<h1>Jautājumi</h1>
<ul>
<!-- START BLOCK : pajauta-list-node-->
<li><a href="/Pajauta/{slug}">{question}</a></li>
<!-- END BLOCK : pajauta-list-node-->
</ul>
<!-- END BLOCK : pajauta-list-->
<!-- START BLOCK : pajauta-noq-->
<h1>Oooops!</h1>
<p>Mums beidzās jautājumi. Varbūt gribi kādu <strong><a href="/Pajauta/pievienot">pievienot</a></strong>?</p>
<p style="text-align: right;">
<a href="/Pajauta/pievienot">Pievienot savu jautājumu</a><br />
<a href="/Pajauta/arhivs">Arhīvs</a>
</p>
<!-- END BLOCK : pajauta-noq-->