<?php

if (isset($_GET['tag'])) {
	$tagid = (int) $_GET['tag'];
	$tag = $db->get_row("SELECT * FROM `tags` WHERE `id` = '$tagid'");
	if ($tag) {
		redirect('/tag/' . $tag->slug, true);
	} else {
		redirect('/tag');
	}
}

if (isset($_GET['var1'])) {
	$tslug = mkslug($_GET['var1']);
	$tag = $db->get_row("SELECT * FROM `tags` WHERE `slug` = '$tslug'");

	if ($tag) {
		$url = '/tag/' . $tag->slug;
		if ($_SERVER['REQUEST_URI'] != $url) {
			redirect($url, true);
		}

		$tpl->newBlock('tags-current');
		$tpl->assign(array(
			'tag-name' => $tag->name,
			'tag-id' => $tag->id
		));
		if (!empty($tag->description)) {
			$tpl->newBlock('tags-description');
			$tpl->assign('description', $tag->description);
		}
		$page_title = $tag->name . ' | ' . $page_title;
		$findtaged = $db->get_results("SELECT * FROM `taged` WHERE `tag_id` = '$tag->id' AND `type` = 0 AND `lang` = '$lang' ORDER BY `id` DESC LIMIT 20");
		if ($findtaged) {
			$tpl->newBlock('tags-articles');
			foreach ($findtaged as $taged) {
				if ($taged->type == 0) {
					$article = $db->get_row("SELECT `id`,`title`,`strid`,`author`,`text` FROM `pages` WHERE `id` = '" . $taged->page_id . "' ORDER BY `date` DESC");
					//pr($article->text);
					if ($article) {
						$author = get_user($article->author);
						$tpl->newBlock('tags-articles-node');
						$tpl->assign(array(
							'url' => '/read/' . $article->strid,
							'text' => textlimit($article->text, 170),
							'title' => $article->title,
							'id' => $article->id,
							'author' => $author->nick
						));
					} else {
						//clean database from broken tags
						$db->query("DELETE FROM `taged` WHERE `id` = '$taged->id' AND `lang` = '$lang'");
					}
				}
			}
		}
		$findtaged = $db->get_results("SELECT * FROM `taged` WHERE `tag_id` = '$tag->id' AND `type` = 1 AND `lang` = '$lang' ORDER BY `id` DESC LIMIT 27");
		if ($findtaged) {
			$tpl->newBlock('tags-images');
			foreach ($findtaged as $taged) {
				if ($taged->type == 1) {
					$article = $db->get_row("SELECT `id`, `text`, `thb`, `uid` FROM images WHERE `id` = " . $taged->page_id);
					if ($article) {
						$tpl->newBlock('node-img');
						$tpl->assign(array(
							'id' => $article->id,
							'thb' => $article->thb,
							'title' => ucfirst(h(substr(strip_tags($article->text), 0, 256))),
							'uid' => $article->uid
						));
					} else {
						//clean database from broken tags
						$db->query("DELETE FROM `taged` WHERE `id` = '$taged->id' AND `lang` = '$lang'");
					}
				}
			}
		}
		$findtaged = $db->get_results("SELECT * FROM `taged` WHERE `tag_id` = '$tag->id' AND `type` = 2 AND `lang` = '$lang' ORDER BY `id` DESC LIMIT 25");
		if ($findtaged) {
			$tpl->newBlock('tags-miniblogs');
			foreach ($findtaged as $taged) {
				if ($taged->type == 2) {
					$mb = $db->get_row("SELECT * FROM `miniblog` WHERE `id` = ('" . $taged->page_id . "') AND `removed` = '0'");

					if ($mb) {
						$mb->text = mb_get_title($mb->text);
						$url_title = mkslug(textlimit($mb->text, 36, ''));
						$text = textlimit(hide_spoilers($mb->text), 64, '');
						$tpl->newBlock('tags-articles-node-mb');
						$tpl->assign(array(
							'uid' => $mb->author,
							'id' => $mb->id,
							'url' => $url_title,
							'text' => $text
						));
					} else {
						//clean database from broken tags
						$db->query("DELETE FROM `taged` WHERE `id` = '$taged->id' AND `lang` = '$lang'");
					}
				}
			}
		}
		$findtaged = $db->get_results("SELECT * FROM `taged` WHERE `tag_id` = '$tag->id' AND `type` = 3 AND `lang` = '$lang' ORDER BY `id` DESC LIMIT 25");
		if ($findtaged) {
			$tpl->newBlock('tags-groups');
			foreach ($findtaged as $taged) {
				if ($taged->type == 3) {
					$group = $db->get_row("SELECT id,title FROM clans WHERE `id` = ('" . $taged->page_id . "')");

					if ($group) {
						$tpl->newBlock('tags-articles-node-group');
						$tpl->assign(array(
							'title' => $group->title,
							'id' => $group->id
						));
					} else {
						//clean database from broken tags
						$db->query("DELETE FROM `taged` WHERE `id` = '$taged->id' AND `lang` = '$lang'");
					}
				}
			}
		}
	} else {
		header("HTTP/1.1 410 Gone");
		redirect('/tag');
	}
}


$cloud = rand(0, 200);
$cache_created = @filemtime(CORE_PATH . '/cache/tags-large/' . $lang . '-' . $cloud . '.html');
if (!$cache_created || (time() - $cache_created) > 43200) {

	$tags = tags_random($db, $lang, 90);

	if ($tags) {
		$out = '';
		if ($lang == 1) {
			$multiplier = 2.6;
		} else {
			$multiplier = 3.6;
		}
		foreach ($tags as $tag) {
			$count = $db->get_var("SELECT count(*) FROM `taged` WHERE `tag_id` = '$tag->id' AND `lang` = '$lang'");
			$size = (7 + ceil(log($count + 1) * $multiplier));
			$out .= '<a style="font-size:' . $size . 'px" href="/tag/' . $tag->slug . '">' . h($tag->name) . '</a> ';
		}
	}
	$handle = fopen(CORE_PATH . '/cache/tags-large/' . $lang . '-' . $cloud . '.html', 'wb');
	fwrite($handle, $out);
	fclose($handle);
} else {
	$out = file_get_contents(CORE_PATH . '/cache/tags-large/' . $lang . '-' . $cloud . '.html');
}

$tpl->newBlock('tags-rand');
$tpl->assign('out', $out);

