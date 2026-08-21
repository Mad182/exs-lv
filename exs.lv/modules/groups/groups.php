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
		foreach ($memberships as $membership) {
			$user_memberships[$membership->clan] = $membership;
		}
	}
}

$sort_members_active = (!isset($_GET['order']) || $_GET['order'] == 'members') ? 'active' : '';
$sort_posts_active = (isset($_GET['order']) && $_GET['order'] == 'posts') ? 'active' : '';
$sort_abc_active = (isset($_GET['order']) && $_GET['order'] == 'abc') ? 'active' : '';

$tpl->assignGlobal([
	'sort_members_active' => $sort_members_active,
	'sort_posts_active' => $sort_posts_active,
	'sort_abc_active' => $sort_abc_active,
]);

if ($categories) {
	foreach ($categories as $group_category) {
		if (!empty($groups_by_cat[$group_category->id])) {
			$tpl->newBlock('groups-cat');
			$tpl->assign('title', $group_category->title);
			foreach ($groups_by_cat[$group_category->id] as $group) {
				$user = $user_memberships[$group->id] ?? null;
				$is_member = ($auth->ok && $user) || $group->owner == $auth->id;

				if ($auth->ok && $user && ($group->posts - $user->seenposts) > 0) {
					$unread_count = $group->posts - $user->seenposts;
					$add = '<a class="group-unread-badge" href="/group/' . $group->id . '/forum/" title="' . $unread_count . ' unread posts">+' . $unread_count . '</a>';
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
					'posts' => number_format($group->posts, 0, '', ' '),
					'members' => number_format($group->members + 1, 0, '', ' '),
					'admin' => $group->admin ?? '',
					'member_class' => $is_member ? 'is-member' : '',
					'add' => $add,
				]);
			}
		}
	}
}

unset($pagepath);


