<?php

/**
 * Lietotāju apbalvojuma ikonas uz avatariem (admin)
 */
if (!im_mod()) {
	redirect();
}


/*
 * Pievieno visiem lietotājiem nejauši izvēlētas ikonas
 */
if (isset($_POST['give_deco_all'])) {
    if ($m->get('give_deco_all') === false) {
        $decosAll = []; # Te glabājam visas ielasītās ikonas
        if ($handle = opendir('bildes/fugue-icons/')) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    $imageData = @getimagesize("bildes/fugue-icons/".$file);
                    if (!$imageData || $imageData[0] == 0 || $imageData[1] == 0) continue;
                    $ratio = $imageData[0] / $imageData[1];
                    /*
                     * Izrādās, ka ne visas ikonas direktorijā ir 16x16px, bet ņemot vērā to, ka
                     * tās pēc tam tiek resizotas uz 16x16 galvenais ir, lai tai būtu 1:1 ratio
                     */
                    if ($imageData[0] <= 32 && $imageData[1] <= 32 && $ratio == 1){
                        $decosAll[] = "/bildes/fugue-icons/" . $file;
                    }
                }
            }
            closedir($handle);
        }
        // Ievācam visus lietotājus, kas ir bijuši aktīvi pēdējās 30 dienās
        $decoUsers = $db->get_results("SELECT id FROM users where decos='' AND lastseen BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW()");

        //Katram lietotājam pievienojam nejauši izvēlētu ikonu
        $decoCount = 0;
        if (!empty($decoUsers) && !empty($decosAll)) {
            $maxIdx = count($decosAll) - 1;
            foreach ($decoUsers as $decoUser) {
                $decos = [[
                    'title' => h(''),
                    'icon' => h($decosAll[rand(0, $maxIdx)])
                ]];
                $db->query("UPDATE `users` SET `decos` = '" . sanitize(serialize($decos)) . "' WHERE `id` = '$decoUser->id'");
                $decoCount++;
            }
        }

        set_flash('Veiksmīgi pievienotas ikonas ' . $decoCount . ' lietototājiem');
        $auth->log('Pievienoja' . $decoCount . ' lietotājiem profila ikonu', 'users');
        $m->set('give_deco_all', 1, 600);
    }
    else {
        set_flash("Apbalvojumi netika pievienoti, jo šī darbība tiek pārāk bieži lietota");
        $auth->log('Neveiksmīgi mēģināja pievienot visiem lietotājiem profila ikonu', 'users');
    }

    redirect('/user_decos');

}

/*
 * Noņem visiem lietotājiem ikonas
 */

if (isset($_POST['remove_deco_all'])) {
    if ($m->get('remove_deco_all') === false) {
        $new = sanitize(serialize(array()));
        $db->query("UPDATE `users` SET `decos` = '$new'");
        $auth->log('Noņēma profila ikonas visiem', 'users');
        set_flash('Veiksmīgi noņemtas ikonas visiem lietototājiem');

        $m->set('remove_deco_all', 1, 1);
    }
    else {
        set_flash("Apbalvojumi netika noņemti, jo šī darbība tiek lietota pārāk bieži");
        $auth->log('Neveiksmīgi mēģināja noņemt visiem lietotājiem profila ikonu', 'users');
    }
    redirect('/user_decos');
}



if (isset($_POST['userid']) && isset($_POST['title']) && isset($_POST['icon'])) {

	if(substr($_POST['icon'],0,8) !== 'https://') {
		set_flash('Attēlus var pievienot tikai no HTTPS linkiem!', 'error');
		redirect('/user_decos');
	}

	$userid = (int) $_POST['userid'];
	$user = $db->get_row("SELECT * FROM users WHERE id = '$userid'");

	if (!empty($user)) {
		if (empty($user->decos)) {
			$decos = [];
		} else {
			$decos = @unserialize($user->decos, ['allowed_classes' => false]);
			if (!is_array($decos)) {
				$decos = [];
			}
		}

		//Ieliekam iepriekš uztaisītajā arrayā vēl vienu arrayu. What is this
		$decos[] = [
			'title' => h($_POST['title']),
			'icon' => h($_POST['icon'])
		];

		$decos = serialize($decos);

		$db->query("UPDATE `users` SET `decos` = '" . sanitize($decos) . "' WHERE `id` = '$user->id'");
		$auth->log('Pievienoja profila ikonu (' . h($_POST['icon']) . ')', 'users', $user->id);

        redirect('/user_decos');
	}
}

$listdecos = $db->get_results("SELECT * FROM `users` WHERE `decos` != '' LIMIT 100");

if (!empty($listdecos)) {
foreach ($listdecos as $decos) {
	$images = @unserialize($decos->decos, ['allowed_classes' => false]);
	if (!empty($images) && is_array($images)) {
		$new = [];
		$can_remove = isset($_GET['uid']) && $_GET['uid'] == $decos->id && isset($_GET['remove']) && check_token('remdeco-' . $decos->id . '-' . $_GET['remove'], $_GET['token'] ?? '');
		foreach ($images as $key => $deco) {
			if ($can_remove && $_GET['remove'] != $key) {
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
				'token' => make_token('remdeco-' . $decos->id . '-' . $key),
			]);
		}

		if ($can_remove) {
			$new = serialize($new);

			$db->query("UPDATE `users` SET `decos` = '" . sanitize($new) . "' WHERE `id` = '$decos->id'");
			$auth->log('Noņēma profila ikonu', 'users', $decos->id);

            redirect('/user_decos');
		}
	} else {
		$db->query("UPDATE users SET decos = '' WHERE id = '$decos->id'");
	}
}
}

