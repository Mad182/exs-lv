<?php

if(isset($_GET['mbpage'])) {
  $skip = 5*intval($_GET['mbpage']);
}  else {
  $skip = 0;
}

$tpl = new TemplatePower('modules/mb_mobile/mb_mobile.tpl');
$tpl->prepare();

$db->cache_dir = 'cache/miniblog/';
$db->use_disk_cache = true;
$db->cache_queries = true;

if($auth->id == 1) {
  $groupquery = '1 = 1';
} else {
  $usergroups = array("`miniblog`.`groupid` = '0'");
  if($auth->ok) {
  	$g_owners = $db->get_col("SELECT id FROM clans WHERE owner = '$auth->id'");
  	if($g_owners) {
      foreach($g_owners as $g_owner) {
        $usergroups[] = "`miniblog`.`groupid` = '".$g_owner."'";
      }
    }
  	$g_members = $db->get_col("SELECT clan FROM clans_members WHERE user = '$auth->id' AND approve = '1'");
  	if($g_members) {
      foreach($g_members as $g_member) {
        $usergroups[] = "`miniblog`.`groupid` = '".$g_member."'";
      }
    }
  }
  $groupquery = implode(' OR ',$usergroups);
}

$mbs = $db->get_results("SELECT
		`miniblog`.`id` AS `id`,
		`miniblog`.`text` AS `text`,
		`miniblog`.`date` AS `date`,
		`miniblog`.`author` AS `author`,
		`miniblog`.`posts` AS `posts`,
		`miniblog`.`groupid` AS `groupid`,
		`users`.`avatar` AS `avatar`,
		`users`.`av_alt` AS `av_alt`,
		`users`.`nick` AS `nick`
	FROM
		`miniblog`,
		`users`
	WHERE
		`miniblog`.`removed` = '0' AND
		`miniblog`.`parent` = '0' AND
		(".$groupquery.") AND
		`users`.`id` = `miniblog`.`author`
	ORDER BY
		`miniblog`.`bump`
	DESC LIMIT $skip,5");

if($mbs) {
	foreach ($mbs as $mb) {
	  $tpl->newBlock('friendssay-box-node');
	  $spec = '';
	  if($mb->avatar == '') {$mb->avatar = 'none.png';}
		if($mb->av_alt) {
	    $u_small_path = 'u_small';
	  } else {
	    $u_small_path = 'useravatar';
	  }
		$mb->text = preg_replace("#(^|[\n ]|<a(.*?)>)http://(www\.)?youtube\.com/watch\?v=([a-zA-Z0-9\-_]+)((.*?)</a>)?#ime", 'get_youtube_title("\\4") ',strip_tags(str_replace(array('<br/>','<br>','<br />','<p>','</p>','&nbsp;',"\n","\r"),' ',$mb->text)));
    $url_title = mkslug(textlimit($mb->text,36,''));

	  if($mb->groupid != 0) {
      $spec = ' class="group"';
      $group = $db->get_row("SELECT * FROM clans WHERE id = '$mb->groupid'");
      if($group->avatar) {
        $mb->avatar = $group->avatar;
        $mb->av_alt = 1;
      }
      $url = '/?group='.$mb->groupid.'&amp;act=community&amp;single='.$mb->id;
	  } else {
      $url = '/say/'.$mb->author.'/'.$mb->id.'-'.$url_title;
    }
 		$mb->text = wordwrap($mb->text, 32, "\n", 1);
	  if($mb->groupid != 0) {
      $mb->text = '<em><span>@' . $group->title . '</span></em>'.textlimit($mb->text,115,'...');
    } else {
      $mb->text = textlimit($mb->text,140,'...');
    }
		$time = time_ago(strtotime($mb->date));
		$tpl->assign(array(
		  'url' => $url,
		  'spec' => $spec,
		  'id' => $mb->id,
		  'author' => $mb->author,
		  'text' => $mb->text,
		  'nick' => htmlspecialchars($mb->nick),
		  'time' => $time,
		  'avatar' => $mb->avatar,
		  'u_small_path' => $u_small_path,
		  'resp' => $mb->posts
		));
	}
}

$tpl->assignGlobal('sel-'.$skip,' class="selected"');

$tpl->printToScreen();
exit;

?>