<?php

if (isset($_GET['skip'])) { $skip = (int)$_GET['skip']; } else { $skip = 0; }

$end = 60;
if($category->intro) {$end = 10;}
if($category->showall) {$end = 400;}
if($category->alphabetical) {$sortby = "`pages`.`title` ASC";} else {$sortby = "`pages`.`attach` DESC, `pages`.`date` DESC";}
if($category->isforum) {
$end = 45;
$sortby = "`pages`.`attach` DESC, `pages`.`bump` DESC";
}

if($category->parent) {
$category2 = $db->get_row("SELECT id,title FROM `cat` WHERE `id` = '$category->parent'");
if($category2) {
	$pagepath = '<a href="/?c=' . $category2->id . '">' . $category2->title . '</a> / ' . $pagepath;
}
}

if(!$category->mods_only or ($auth->ok && ($auth->level == 1 or $auth->level == 2))) {

if($category->intro) {
  $articles = $db->get_results("
		SELECT
			`pages`.`id` AS `id`,
			`pages`.`title` AS `title`,
			`pages`.`date` AS `date`,
			`pages`.`author` AS `author`,
			`pages`.`posts` AS `posts`,
			`pages`.`text` AS `text`,
			`pages`.`sm_avatar` AS `avatar`,
			`pages`.`readby` AS `readby`,
			`pages`.`views` AS `views`,
			`pages`.`attach` AS `attach`,
			`pages`.`intro` AS `intro`,
			`users`.`nick` AS `nick`,
			`users`.`level` AS `level`
		FROM
			`pages`,
			`users`
		WHERE
			`pages`.`category` = ('" . $category->id . "') AND
			`users`.`id` = `pages`.`author`
		ORDER BY
			" . $sortby . "
		LIMIT
			$skip,$end");
} else {
  $articles = $db->get_results("
		SELECT
			`pages`.`id` AS `id`,
			`pages`.`title` AS `title`,
			`pages`.`date` AS `date`,
			`pages`.`author` AS `author`,
			`pages`.`closed` AS `closed`,
			`pages`.`attach` AS `attach`,
			`pages`.`views` AS `views`,
			`pages`.`readby` AS `readby`,
			`pages`.`posts` AS `posts`,
			`users`.`nick` AS `nick`,
			`users`.`level` AS `level`
		FROM
			`pages`,
			`users`
		WHERE
			`pages`.`category` = ('" . $category->id . "') AND
			`users`.`id` = `pages`.`author`
		ORDER BY
			" . $sortby . "
		LIMIT
			$skip,$end");
}
if($articles) {
	$tpl->assignInclude('module-currrent','modules/core/list.tpl');
	$tpl->prepare();
	$total = $db->get_var("SELECT count(*) FROM pages WHERE category = ('" . $category->id . "')");

	if($skip) {$page_title =	$page_title . ' - lapa ' . ($skip/$end+1);}
	if($category->isforum) {$page_title = $page_title . ' | Forums';}

	if($category->isforum) {
		$tpl->newBlock('list-forum');
		$tpl->assign(array(
		  'articles-title' => $category->title,
		  'articles-catid' => $category->id,
			'parentid' => $category->parent
		));


		foreach ($articles as $article) {
		  if(!$article->nick) {
				$article->nick = 'Nezināms';
				$article->level = 0;
			}
			$tpl->newBlock('list-forum-node');
			
			$date = date('Y-m-d',strtotime($article->date));
			
			$title_clear = $article->title;
			
			if($article->attach) {
				$article->title = '<strong><img src="http://exs.lv/bildes/attach-small.gif" alt="Piesprausts:" title="Piesprausts" /> ' . $article->title . '</strong>';
			} else {
        $article->title = htmlspecialchars($article->title);
			}

			if($article->closed) {
				if($auth->ok) {
					if(!in_array($auth->id,unserialize($article->readby))) {
					  if($article->attach) {
							$timg = 'sticky_unread_locked.gif';
						} else {
							$timg = 'topic_unread_locked.gif';
						}
					} else {
					  if($article->attach) {
							$timg = 'sticky_read_locked.gif';
						} else {
							$timg = 'topic_read_locked.gif';
						}
					}
				} else {
				  if($article->attach) {
						$timg = 'sticky_read_locked.gif';
					} else {
						$timg = 'topic_read_locked.gif';
					}
				}
			} else {
				if($auth->ok) {
					if(!in_array($auth->id,unserialize($article->readby))) {
					  if($article->attach) {
							$timg = 'sticky_unread.gif';
						} else {
							$timg = 'topic_unread.gif';
						}
					} else {
					  if($article->attach) {
							$timg = 'sticky_read.gif';
						} else {
							$timg = 'topic_read.gif';
						}
					}
				} else {
				  if($article->attach) {
						$timg = 'sticky_read.gif';
					} else {
						$timg = 'topic_read.gif';
					}
				}
			}

			$tpl->assign(array(
			  'articles-node-id' => $article->id,
				'node-url' => mkurl('page',$article->id,$title_clear),
				'aurl' => mkurl('user',$article->author,$article->nick),
			  'articles-node-title' => $article->title,
			  'articles-node-views' => $article->views,
			  'articles-node-timg' => $timg,
			  'articles-node-date' => $date,
			  'articles-node-author' => usercolor($article->nick,$article->level),
			  'articles-node-posts' => $article->posts,
			));
		}

	//list for categories with intro text
	} elseif($category->intro) {
		$tpl->newBlock('list-articles');
		$tpl->assign(array(
		  'articles-title' => $category->title,
		  'articles-catid' => $category->id
		));
		
		$shown = 0;
		foreach ($articles as $article) {
		  if(!$article->nick) {
				$article->nick = 'Nezināms';
				$article->level = 0;
			}
			$tpl->newBlock('list-articles-node');

			$date = display_time(strtotime($article->date));
			
			if($article->attach) {
				$article->title = '<strong><img src="/bildes/attach-small.gif" alt="Piesprausts:" title="Piesprausts" /> ' . $article->title . '</strong>';
			} else {
        $article->title = htmlspecialchars($article->title);
			}
			
			if(!empty($article->intro)) {
        $article->text = $article->intro;
			} else {
				$article->text = textlimit(strip_tags(trim(str_replace('<li>',' • ',str_replace(array('&nbsp;','<br />'),' ',$article->text)))),$config['article_intro_len']);
				$article->text = preg_replace("#(^|[\n ]|<a(.*?)>)http://(www\.)?youtube\.com/watch\?v=([a-zA-Z0-9\-_]+)(</a>)?#im",'<div class="video-thb"><a href="/?p='.$article->id.'"><img src="http://img.youtube.com/vi/$4/2.jpg" alt="" /></a><p>Youtube video</p><div class="c"></div></div>', $article->text,1);
				$videoid = get_between($article->text,'img.youtube.com/vi/','/2.jpg"');
				if($videoid) {
					$contents = file_get_contents('http://gdata.youtube.com/feeds/api/videos/' . $videoid);
					$v_description = '<strong><a href="/?p='.$article->id.'">'.get_between($contents,"<media:title type='plain'>",'</media:title>').'</a></strong><br />';
					$v_description .= textlimit(get_between($contents,"<media:description type='plain'>",'</media:description>'),270);
					$article->text = str_replace('<p>Youtube video</p>','<p>'.$v_description.'</p>',$article->text);
				}
				$article->intro = sanitize($article->text);
				$db->query("UPDATE pages SET intro = '$article->intro' WHERE id = '$article->id' LIMIT 1");
			}

		
			$tpl->assign(array(
			  'articles-node-id' => $article->id,
				'node-url' => mkurl('page',$article->id,$article->title),
				'aurl' => mkurl('user',$article->author,$article->nick),
			  'articles-node-title' => $article->title,
			  'articles-node-views' => $article->views,
			  'articles-node-date' => $date,
			  'articles-node-author' => usercolor($article->nick,$article->level),
			  'articles-node-posts' => $article->posts,
			  'articles-node-intro' => textlimit(strip_tags($article->text),140)
			));
			if($article->avatar) {
				$tpl->newBlock('list-articles-node-avatar');
				$tpl->assign(array(
				  'node-avatar-image' => trim($article->avatar),
			  	'node-avatar-alt' => trim(htmlspecialchars($article->title))
				));
			}
			
			if($category->id == 1 && $shown == 0 && !$topad) {
					$tpl->newBlock('list-ads');
			}
			$shown++;
		}

	} else {
		//list for categories withOUT intro text
		$tpl->newBlock('list-articles-short');
		$tpl->assign(array(
		  'articles-title' => $category->title,
		  'articles-catid' => $category->id
		));

		foreach ($articles as $article) {

			$tpl->newBlock('list-articles-short-node');
			
			if($article->attach) {
				$article->title = '<strong><img src="/bildes/attach-small.gif" alt="Piesprausts:" title="Piesprausts" /> ' . htmlspecialchars($article->title) . '</strong>';
			} else {
        $article->title = htmlspecialchars($article->title);
			}
			
			$tpl->assign(array(
			  'articles-node-id' => $article->id,
				'node-url' => mkurl('page',$article->id,$article->title),
				'aurl' => mkurl('user',$article->author,$article->nick),
			  'articles-node-title' => $article->title,
			  'articles-node-date' => $article->date,
			  'articles-node-author' => usercolor($article->nick,$article->level),
			));
		}
		
	}

	//pager
	if ($skip > 0) {
		if ($skip > $end) { $iepriekseja = $skip-$end; } else { $iepriekseja = 0; }
		$pager_next = '<a class="pager-next" title="Iepriekšējā lapa" href="/?c=' . $category->id . '&amp;skip=' . $iepriekseja . '">&laquo;</a> ';
	} else {
    $pager_next = '';
	}
  $pager_prev = '';
	if ($total > $skip+$end) {$pager_prev = ' <a class="pager-prev" title="Nākamā lapa" href="/?c=' . $category->id . '&amp;skip=' . ($skip+$end) . '">&raquo;</a>';}
	$startnext = 0;
	$page_number = 0;
	$pager_numeric = '';
	while($total-$startnext > 0) {
		$page_number++;
		$class = '';
		if($skip == $startnext) {$class = ' class="selected"';}

		if($total/$end < 10 or $page_number < 4 or $page_number > $total/$end-2 or $startnext == $skip or $startnext == $skip+$end  or $startnext == $skip-$end) {
			if($page_number != 1) {$pager_numeric .=' ';}
			$pager_numeric .= '<a href="/?c=' . $category->id . '&amp;skip=' . $startnext . '"' . $class . '>' . $page_number . '</a> ';
		} elseif ($startnext == $skip+$end*2 or $startnext == $skip-$end*2) {
			if($page_number != 1) {$pager_numeric .=' ';}
			$pager_numeric .= ' ... ';
		} elseif ($page_number == 4 && $skip/$end < 5) {
			if($page_number != 1) {$pager_numeric .=' ';}
			$pager_numeric .= ' ... ';
		}
		
		$startnext = $startnext+$end;
	}
	$tpl->assignGlobal(array(
		'pager-next' => $pager_next,
		'pager-prev' => $pager_prev,
		'pager-numeric' => $pager_numeric
	));
	
	
	
}

} else {
header('Location: /');
exit;
}

?>