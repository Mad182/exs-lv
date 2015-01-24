<?php

require_once(LIB_PATH . '/htmlpurifier/library/HTMLPurifier.includes.php');

$userid = (int) $_GET['var1'];
$user = $db->get_row("SELECT * FROM users WHERE id = '" . $userid . "'");
if ($user->yt_name) {

	$inprofile = $user;
	$page_title = 'Jaunākais ' . $user->nick . ' YouTube profilā';

	$tpl->assignGlobal(array(
		'user-id' => $user->id,
		'user-nick' => h($user->nick)
	));

	if ($user->yt_updated < time() - 7200) {
		$rssurl = 'http://gdata.youtube.com/feeds/base/users/' . $user->yt_name . '/uploads?client=ytapi-youtube-user&v=2';
		$loaded = simplexml_load_file($rssurl);
		foreach ($loaded as $load) {
			if ($load->title) {

				$link = sanitize($load->link['href']);
				$title = sanitize($load->title);
				$content = filterb4db($load->content);

				$config = HTMLPurifier_Config::createDefault();
				$config->set('Cache.SerializerPath', CORE_PATH . '/cache/htmlpurifier');
				$config->set('AutoFormat.Linkify', true);
				$config->set('AutoFormat.AutoParagraph', true);
				$config->set('AutoFormat.RemoveSpansWithoutAttributes', true);
				$config->set('AutoFormat.RemoveEmpty', true);
				$purifier = new HTMLPurifier($config);
				$content = $purifier->purify($content);
				
				$content = str_replace('border:1px solid #999999;margin:0px 10px 5px 0px;', '" class="av', $content);
				$content = str_replace('<table', '<table style="width:100%"', $content);
				$content = str_replace('width="256"', '', $content);
				$content = str_replace('width:555px;', '', $content);
				$content = str_replace('http:','https:',htmlpost2db($content));

				$time = strtotime($load->published);

				if (!$db->get_var("SELECT count(*) FROM `ytrss` WHERE `url` = '$link'")) {

					$db->query("INSERT INTO `ytrss` (`user_id`,`url`,`title`,`content`,`time`)
											VALUES ('$user->id','$link','$title','$content','$time')");
				}
			}
		}
		$db->query("UPDATE users SET yt_updated = ('" . time() . "') WHERE id = '$user->id'");
	}

	$items = $db->get_results("SELECT * FROM ytrss WHERE user_id = '$user->id' ORDER BY time DESC LIMIT 20");

	if ($items) {
		$tpl->newBlock('user-yt');
		foreach ($items as $item) {
			$tpl->newBlock('user-yt-node');
			$tpl->assign(array(
				'title' => $item->title,
				'content' => add_smile($item->content),
			));
		}
	}
} else {
	$tpl->newBlock('error-nouser');
	$page_title = 'Kļūda: profils nav atrasts!';
}
