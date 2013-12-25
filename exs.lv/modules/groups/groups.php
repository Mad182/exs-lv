<?php

$order = 'ORDER BY members DESC, posts DESC';
if (isset($_GET['order']) && $_GET['order'] == 'posts') {
	$order = 'ORDER BY posts DESC, members DESC';
} elseif (isset($_GET['order']) && $_GET['order'] == 'abc') {
	$order = 'ORDER BY title ASC';
}

//  runescape apakšprojektā redzamas būs tikai rs grupas;
//  šādi ar pārbaudēm neies cauri visām pārējām kategorijām
$where = '';
if ($lang == 9) {
    $where = ' WHERE `category_id` == 9 ';
}
$categories = $db->get_results("SELECT title,id FROM clans_categories $where ORDER BY importance DESC");

foreach ($categories as $group_category) {

	$groups = $db->get_results("SELECT id,title,avatar,posts,members,owner,strid FROM clans WHERE `lang` = '$lang' AND category_id = '$group_category->id' $order");
	if ($groups) {
		$tpl->newBlock('groups-cat');
		$tpl->assign('title', $group_category->title);
		foreach ($groups as $group) {

			$user = $db->get_row("SELECT id,seenposts FROM clans_members WHERE clan = '$group->id' AND user = '$auth->id' AND approve = '1'");
			if ($auth->ok && $user or $group->owner == $auth->id) {
				$istyle = ' style="background:green;width:72px;height:72px;" ';
			} else {
				$istyle = ' style="width:72px;height:72px;" ';
			}
			if ($auth->ok && $user && ($group->posts - $user->seenposts) > 0) {
				$add = '&nbsp;(<a style="font-size: 16px;" href="/group/' . $group->id . '/forum/"><span class="red">' . ($group->posts - $user->seenposts) . '</span></a>)';
			} else {
				$add = '';
			}
			if ($group->avatar == '') {
				$group->avatar = 'none.png';
			}
			$tpl->newBlock('list-groups-node');
			
			if(!empty($group->strid)) {
				$group->link = '/'.$group->strid;
			} else {
				$group->link = '/group/'.$group->id;
			}
			
			$tpl->assign(array(
				'title' => $group->title,
				'link' => $group->link,
				'avatar' => $group->avatar,
				'posts' => $group->posts,
				'members' => $group->members + 1,
				'admin' => $db->get_var("SELECT nick FROM users WHERE id = '$group->owner'"),
				'style' => $istyle,
				'add' => $add,
			));
		}
	}
}
