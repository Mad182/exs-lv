<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="lv" lang="lv">

<head>
	<title>{page-title}</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta http-equiv="content-language" content="lv" />
	<meta http-equiv="X-UA-Compatible" content="IE=7" />
	<meta name="verify-v1" content="qig3ZD30iF02dLnudPOO854VRl0csJ+AIPTMuS2E9HI=" />
	<link rel="stylesheet" href="/css/0_style.css" type="text/css" media="screen,projection" />
	<script type="text/javascript" src="/js/jquery-1.2.3.pack.js"></script>
	<script type="text/javascript" src="/js/javascript.js"></script>
	<script>
	 $(document).ready(function() {
	 	 $("#responsecontainer").load("response.php");
	   var refreshId = setInterval(function() {
	      $("#left").load('/catche/miniblog_bump.html?randval='+ Math.random());
	   }, 9000);
	});
	</script>
</head>

<body>

<div id="conteiner">
<div id="left">

</div>
<div id="right">

			<!-- START BLOCK : user-miniblog-->
		    <div class="tabMain">
		    	<p id="miniblog-rss">
		      	<a class="miniblog-rss" href="/rss.php?m={user-id}">RSS</a>
		      </p>

				<!-- START BLOCK : user-miniblog-form-->
					<form id="addminiblog" name="addminiblog" class="fieldbox" action="{page-url}" method="post" enctype="multipart/form-data">
					  <fieldset>
							<legend>Pievienot jaunu ierakstu</legend>
					    <label for="newminiblog">Teksts:</label>
					    <textarea rows="5" cols="42" name="newminiblog" id="newminiblog"
							onkeydown="textCounter(document.addminiblog.newminiblog,document.addminiblog.remLen0,170)"
							onkeyup="textCounter(document.addminiblog.newminiblog,document.addminiblog.remLen0,170)"></textarea>
							<br />
							<input class="remlen" readonly="readonly" type="text" name="remLen0" size="3" maxlength="3" value="170" />
					    <br />
							<label for="new-image">Pievienotais attēls:</label>
							<input type="file" name="new-image" id="new-image" /><br />

					    <input type="submit" name="submit" id="submit" value="Pievienot" />
					  </fieldset>
					</form>
				<!-- END BLOCK : user-miniblog-form-->

    		<!-- START BLOCK : user-miniblog-list-->
	      		<ul id="miniblog-list">
  			  	<!-- START BLOCK : user-miniblog-list-node-->
  			    	<li>
							  <div class="mbox">
	  			    		<a name="m{miniblog-id}" href="/?u={miniblog-author-id}"><img class="miniblog-avatar" src="/dati/bildes/useravatar/{miniblog-author-avatar}" alt="{miniblog-author-nick}" title="{miniblog-author-nick}" /></a>
									<p class="date"><a class="draugiem" href="#" onclick="DraugiemSay('Raksts', 'http://exs.lv/?m={miniblog-author-id}&single={miniblog-id}', 'exs.lv'); return false;">iesaki draugiem</a> <a href="/?m={miniblog-author-id}&amp;single={miniblog-id}" class="permalink" rel="self bookmark">saite</a><a href="/?u={miniblog-author-id}">{miniblog-author}</a> @ {miniblog-date} teica:</p>
									{miniblog-text}
	  			  		<!-- START BLOCK : user-miniblog-list-node-delete-->
									[<a class="delete" title="Dzēst ierakstu" href="/?m={user-id}&amp;delete={miniblog-id}" onclick="return confirm_delete();"><img src="/bildes/x.png" alt="x" title="Dzēst ierakstu" /></a>]
	  			  		<!-- END BLOCK : user-miniblog-list-node-delete-->
	  			  		<!-- START BLOCK : user-miniblog-list-node-response-->
	  			  			[<a href="/?m={user-id}&amp;single={miniblog-id}" title="Pievienot atbildi"><img src="/bildes/response.gif" alt="a" title="Pievienot atbildi" /></a>]
	  			  		<!-- END BLOCK : user-miniblog-list-node-response-->
	  			  			<div class="c"></div>
								</div>
			    		<!-- START BLOCK : user-miniblog-list-responses-->
				      		<ul class="responses">
			  			  	<!-- START BLOCK : user-miniblog-list-responses-node-->
			  			    	<li>
			  			    		<a name="m{miniblog-id}" href="/?u={miniblog-author-id}"><img class="miniblog-avatar" src="/dati/bildes/useravatar/{miniblog-author-avatar}" alt="{miniblog-author-nick}" title="{miniblog-author-nick}" /></a>
			  			    	  <div class="response-content">
												<p class="date"><a href="/?u={miniblog-author-id}">{miniblog-author}</a> @ {miniblog-date} atbildēja:</p>
												{miniblog-text}
		  			  		<!-- START BLOCK : user-miniblog-list-responses-node-delete-->
										[<a class="delete" title="Dzēst ierakstu" href="/?m={user-id}&amp;delete={miniblog-id}" onclick="return confirm_delete();"><img src="/bildes/x.png" alt="x" title="Dzēst ierakstu" /></a>]
		  			  		<!-- END BLOCK : user-miniblog-list-responses-node-delete-->
		  			  		    </div>
										</li>
			            <!-- END BLOCK : user-miniblog-list-responses-node-->



			  			  	<!-- START BLOCK : user-miniblog-more-->
			  			    	<li class="more">
			  			    		<a href="/?m={resp-mid}&amp;single={resp-sid}">Apskatīt vēl {resp-more} komentārus...</a>
										</li>
			            <!-- END BLOCK : user-miniblog-more-->



			  				  </ul>
			    		<!-- END BLOCK : user-miniblog-list-responses-->

			  			<!-- START BLOCK : user-miniblog-resp-->
  			  			<div id="response-{miniblog-id}" class="miniblog-response">
									<form id="addresponse" name="addresponse{miniblog-id}" class="fieldbox" action="{page-url}" method="post" enctype="multipart/form-data">
									  <fieldset>
											<legend>Pievienot atbildi</legend>
											<input type="hidden" name="response-to" value="{miniblog-id}" />
											<input type="hidden" name="die-motherfcker-wannabes" value="{miniblog-check}" />
									    <label for="responseminiblog">Teksts:</label>
									    <textarea rows="5" cols="42" name="responseminiblog" id="responseminiblog"
											onkeydown="textCounter(document.addresponse{miniblog-id}.responseminiblog,document.addresponse{miniblog-id}.remLen{miniblog-id},170)"
											onkeyup="textCounter(document.addresponse{miniblog-id}.responseminiblog,document.addresponse{miniblog-id}.remLen{miniblog-id},170)"></textarea>
									    <br />
											<input class="remlen" readonly="readonly" type="text" name="remLen{miniblog-id}" size="3" maxlength="3" value="170" />
									    <br />
											<label for="new-image">Pievienotais attēls:</label>
											<input type="file" name="new-image" id="new-image" /><br />

									    <input type="submit" name="submit" id="submit" value="Pievienot" />
									  </fieldset>
									</form>
  			  			</div>
			  			<!-- END BLOCK : user-miniblog-resp-->

							</li>
            <!-- END BLOCK : user-miniblog-list-node-->
  				  </ul>

					<div class="c"></div>
					<p class="core-pager">
					  {pager-next}
					  {pager-numeric}
					  {pager-prev}
					</p>
    		<!-- END BLOCK : user-miniblog-list-->

		    </div>
			<!-- END BLOCK : user-miniblog-->
</div>
</div>
<script src="/js/behavior.js" type="text/javascript"></script>
<script src="/js/rating.js" type="text/javascript"></script>
<script src="/js/load.js?1234" type="text/javascript"></script>
<script type="text/javascript">
	var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
	document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
	try {
	var pageTracker = _gat._getTracker("UA-4190387-2");
	pageTracker._trackPageview();
	} catch(err) {}
</script>
</body>

</html>