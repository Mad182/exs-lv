<?php

	if(!empty($http_response_header)) {
	  foreach($http_response_header as $header) {
			if(substr($header,0,9) == 'Location:' || substr($header,0,9) == 'location:') {
			  if($category->textid == 'bans') {
					$header = str_replace('Location: ', 'Location: /bans/', $header);
				}
				header($header);
				exit;
			}
		}
	}

	$page_title = get_between($contents,'<title>','</title>') . ' - cs.exs.lv';
  $contents = get_between($contents,'<body>','</body>');
	$contents = str_replace(array(
		'/bans/images',
		"Not logged in [<a href='/bans/login.php'>login</a>]",
		'href="http://www.amxbans.net"',
	),
	array(
		'http://csadmin.exs.lv/bans/images',
		'',
		'rel="nofollow" href="http://www.amxbans.net"',
	),
	$contents);
	$tpl->assignInclude('module-head','modules/api/head/amxbans.tpl');
	$tpl->prepare();
	
	$pagepath = '';
?>