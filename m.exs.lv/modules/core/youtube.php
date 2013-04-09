<?php

$userid = (int)$_GET['y'];
$user = $db->get_row("SELECT * FROM users WHERE id = ('" . $userid  ."')");
if($user->yt_name) {
	//for profile sidebox
	$inprofile = $user;

	$tpl->assignInclude('module-currrent','modules/core/youtube.tpl');
	$tpl->prepare();
	$tpl->newBlock('profile-menu');

	if($user->yt_name) {
		$tpl->newBlock('yt-tab');
	}

	$page_title = 'Jaunākais ' . $user->nick . ' YouTube profilā';
	if($skip) {$page_title =	$page_title . ' - lapa ' . ($skip/$end+1);}
	$tpl->assignGlobal(array(
	  'user-id' => $user->id,
		'user-nick' => htmlspecialchars($user->nick),
		'active-tab-yt' => ' activeTab'
	));
	
	if($user->yt_updated < time()-120) {
		$rssurl = 'http://gdata.youtube.com/feeds/base/users/'.$user->yt_name.'/uploads?client=ytapi-youtube-user&v=2';
	  $loaded = simplexml_load_file($rssurl);
	  foreach($loaded as $load) {
	    if($load->title) {

	      $link = sanitize($load->link['href']);
	      $title = sanitize($load->title);
	      $content = sanitize($load->content);
	      $time = strtotime($load->published);

	      if(!$db->get_var("SELECT count(*) FROM ytrss WHERE url = '$link'")) {

					$db->query("INSERT INTO ytrss (user_id,url,title,content,time)
											VALUES ('$user->id','$link','$title','$content','$time')");
				}
			}
		}
		$db->query("UPDATE users SET yt_updated = ('".time()."') WHERE id = '$user->id'");
	}
	
	$items = $db->get_results("SELECT * FROM ytrss WHERE user_id = '$user->id' ORDER BY time DESC LIMIT 20");
	
	if($items) {
	  $tpl->newBlock('user-yt');
		foreach($items as $item) {
	  	$tpl->newBlock('user-yt-node');
			$tpl->assign(array(
			  'title' => $item->title,
			  'content' => $item->content,
			));
		}
	}


} else {
	$tpl->newBlock('error-nouser');
	$page_title = 'Kļūda: profils nav atrasts!';
}

?>