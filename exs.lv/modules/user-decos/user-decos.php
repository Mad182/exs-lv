<?php

/**
 * Lietotāju apbalvojuma ikonas uz avatariem (admin)
 */
if (!im_mod()) {
	redirect();
}

global $m;


if (isset($_POST['give_deco_all'])) {
    if ($m->get('give_deco_all') === false) {
        $decosAll = array();
        if ($handle = opendir('bildes/fugue-icons/')) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    $allDecos[] = "/bildes/fugue-icons/" . $file;
                }
            }
            closedir($handle);
        }
        $decoUsers = $db->get_results("SELECT id FROM users where decos='' AND lastseen BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW()");
        foreach ($decoUsers as $decoUser){
            $decos = array();
            $decos[] = [
                'title' => h(''),
                'icon' => h($allDecos[rand(0, sizeof($allDecos)-1)])
            ];
            $db->query("UPDATE `users` SET `decos` = '" . sanitize(serialize($decos)) . "' WHERE `id` = '$decoUser->id'");
        }

        $auth->log('Pievienoja visiem lietotājiem profila ikonu', 'users');
        echo 'a';
        $m->set('give_deco_all', 1, 600);
    }
    else {
        $auth->log('Neveiksmīgi mēģināja pievienot visiem lietotājiem profila ikonu', 'users');
    }

}

if (isset($_POST['remove_deco_all'])) {
    if ($m->get('remove_deco_all') === false) {
        $new = sanitize(serialize(array()));
        $db->query("UPDATE `users` SET `decos` = '$new'");
        $auth->log('Noņēma profila ikonas visiem', 'users');

        $m->set('remove_deco_all', 1, 600);
    }
    else {
        $auth->log('Neveiksmīgi mēģināja noņemt visiem lietotājiem profila ikonu', 'users');
    }

}



if (isset($_POST['userid']) && isset($_POST['title']) && isset($_POST['icon'])) {

	if(substr($_POST['icon'],0,8) !== 'https://') {
		set_flash('Attēlus var pievienot tikai no HTTPS linkiem!', 'error');
		redirect('/user_decos');
	}

	$userid = (int) $_POST['userid'];
	$user = $db->get_row("SELECT * FROM users WHERE id = '$userid'");

	if (!empty($user)) {
		if (!$user->decos == '') {
			$decos = [];
		} else {
			$decos = unserialize($user->decos);
		}

		$decos[] = [
			'title' => h($_POST['title']),
			'icon' => h($_POST['icon'])
		];

		$decos = serialize($decos);

		$db->query("UPDATE `users` SET `decos` = '" . sanitize($decos) . "' WHERE `id` = '$user->id'");
		$auth->log('Pievienoja profila ikonu (' . h($_POST['icon']) . ')', 'users', $user->id);

		header('Location: /' . $category->textid);
		exit;
	}
}

$listdecos = $db->get_results("SELECT * FROM `users` WHERE `decos` != '' LIMIT 100");

foreach ($listdecos as $decos) {
	$images = unserialize($decos->decos);
	if (!empty($images)) {
		$new = [];
		foreach ($images as $key => $deco) {
			if (isset($_GET['uid']) && $_GET['uid'] == $decos->id && isset($_GET['remove']) && $_GET['remove'] != $key) {
				$new[$key] = [
					'title' => $deco['title'],
					'icon' => $deco['icon'],
				];
			}
			$tpl->newBlock('existing-deco');
			$tpl->assign([
				'title' => $deco['title'],
				'icon' => $deco['icon'],
				'key' => $key,
				'userid' => $decos->id,
				'nick' => $decos->nick,
			]);
		}

		if (isset($_GET['uid']) && $_GET['uid'] == $decos->id && isset($_GET['remove'])) {
			$new = serialize($new);

			$db->query("UPDATE `users` SET `decos` = '" . sanitize($new) . "' WHERE `id` = '$decos->id'");
			$auth->log('Noņēma profila ikonu', 'users', $decos->id);

			header('Location: /' . $category->textid);
			exit;
		}
	} else {
		$db->query("UPDATE users SET decos = '' WHERE id = '$decos->id'");
	}
}

