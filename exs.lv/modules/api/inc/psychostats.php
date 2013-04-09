<?php

	$page_title = str_replace('PsychoStats','Statistika',get_between($contents,'<title>','</title>') . ' - cs.exs.lv');
  $contents = get_between($contents,'<body>','</body>');
	$contents = str_replace(array(
	  'href="',
		'/stats/',
		'http://csadmin.exs.lv/stats/index.php',
		'player.php',
		'href="/psychostats/http',
		'<script type="text/javascript" src="includes/ofc/js/swfobject.js"></script>',
		'http://csadmin.exs.lv/stats//psychostats/player',
		'http://csadmin.exs.lv/stats/awards.php',
		'&amp;p=',
		'src="imgsess.php',
		'/psychostats//psychostats/',
		'"includes/ofc/',
		'/psychostats/player.php%3Fofc'
	),
	array(
		'href="/psychostats/',
		'http://csadmin.exs.lv/stats/',
		'/psychostats/index.php',
		'/psychostats/player.php',
		'href="http',
    '',
    'http://exs.lv/psychostats/player',
    'http://exs.lv/psychostats/awards.php',
    '&amp;_p=',
		'src="http://csadmin.exs.lv/stats/imgsess.php',
		'/psychostats/',
		'"http://csadmin.exs.lv/stats/includes/ofc/',
		'http://csadmin.exs.lv/stats/player.php%3Fofc'
	),
	$contents);
	$tpl->assignInclude('module-head','modules/api/head/psychostats.tpl');
	$tpl->prepare();
	
	if($file == 'player.php' || $file == 'awards.php' || $file == 'map.php' || $file == 'weapon.php' || $file == 'plrhist.php') {
		$tpl->newBlock('psycho2col');
	}
	
	$pagepath = '';
?>