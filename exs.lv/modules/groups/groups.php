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
	$where = ' WHERE `id` = 4 ';
}
$categories = $db->get_results("SELECT title,id FROM clans_categories $where ORDER BY importance DESC");

$all_groups = $db->get_results("SELECT c.id, c.category_id, c.title, c.avatar, c.posts, c.members, c.owner, c.strid, u.nick AS admin FROM clans c LEFT JOIN users u ON u.id = c.owner WHERE c.`lang` = '$lang' $order");

$groups_by_cat = [];
if ($all_groups) {
	foreach ($all_groups as $group) {
		$groups_by_cat[$group->category_id][] = $group;
	}
}

$user_memberships = [];
if ($auth->ok) {
	$memberships = $db->get_results("SELECT clan, seenposts FROM clans_members WHERE user = '$auth->id' AND approve = '1'");
	if ($memberships) {
		foreach ($memberships as $m) {
			$user_memberships[$m->clan] = $m;
		}
	}
}

if ($categories) {
	foreach ($categories as $group_category) {
		if (!empty($groups_by_cat[$group_category->id])) {
			$tpl->newBlock('groups-cat');
			$tpl->assign('title', $group_category->title);
			foreach ($groups_by_cat[$group_category->id] as $group) {
				$user = $user_memberships[$group->id] ?? null;
				if (($auth->ok && $user) || $group->owner == $auth->id) {
					$istyle = ' style="background:green;width:75px;height:75px;margin-top:15px;" ';
				} else {
					$istyle = ' style="width:75px;height:75px;margin-top:15px;" ';
				}
				if ($auth->ok && $user && ($group->posts - $user->seenposts) > 0) {
					$add = '&nbsp;(<a style="font-size: 16px;" href="/group/' . $group->id . '/forum/"><span class="red">' . ($group->posts - $user->seenposts) . '</span></a>)';
				} else {
					$add = '';
				}

				$group->av_alt = 1;
				$avatar = get_avatar($group, 'm');

				$tpl->newBlock('list-groups-node');

				if (!empty($group->strid)) {
					$group->link = '/' . $group->strid;
				} else {
					$group->link = '/group/' . $group->id;
				}

				$tpl->assign([
					'title' => $group->title,
					'link' => $group->link,
					'avatar' => $avatar,
					'posts' => $group->posts,
					'members' => $group->members + 1,
					'admin' => $group->admin ?? '',
					'style' => $istyle,
					'add' => $add,
				]);
			}
		}
	}
}

unset($pagepath);


