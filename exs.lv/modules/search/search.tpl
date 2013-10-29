<!-- START BLOCK : search-results-->
<h1>Meklēt ar google</h1>

<style type="text/css">
	@import url(http://www.google.com/cse/api/branding.css);
	
	/* fluid layout fix */
	.cse-branding-bottom:after, .cse-branding-right:after {
		clear: none;
	}
</style>

<div class="cse-branding-right" style="background-color:#FFFFFF;color:#000000">
	<div class="cse-branding-form">
		<form class="form" action="/search/" id="cse-search-box">
			<div>
				<input type="hidden" name="cx" value="{cx}" />
				<input type="hidden" name="cof" value="FORID:11" />
				<input type="hidden" name="ie" value="UTF-8" />
				<input type="text" name="q" size="31" class="text" />
				<input type="submit" name="sa" value="Meklēt" class="button primary" />
			</div>
		</form>
	</div>
</div>

<div id="cse-search-results"></div>
<script type="text/javascript">
	var googleSearchIframeName = "cse-search-results";
	var googleSearchFormName = "cse-search-box";
	var googleSearchFrameWidth = 560;
	var googleSearchDomain = "www.google.com";
	var googleSearchPath = "/cse";
</script>
<script type="text/javascript" src="http://www.google.com/afsonline/show_afs_search.js"></script>

<!-- END BLOCK : search-results-->

