<?php

	$page_title = get_between($contents,'<title>','</title>');
  $contents = get_between($contents,'<body>','</body>');
	$contents = str_replace(array(
	  'href="',
	  'action="',
	  'src="'
	),
	array(
		'href="/unreal/',
		'action="/unreal/',
	  'src="http://utstats.exs.lv/ut_stats/'
	),
	$contents);
	$tpl->assignInclude('module-head','modules/api/head/unreal.tpl');
	$tpl->prepare();

	
	$pagepath = '';
?>