<?php

$tpl->assignInclude('module-head', CORE_PATH . '/modules/' . $category->module . '/vote-13-head.tpl');
$tpl->prepare();

if (!$auth->ok || $auth->karma < 100) {
	set_flash('Pieeja liegta! Lai aplūkotu izvēlēto sadaļu, nepieciešams autorizēties, kā arī karmai jābūt vismaz 100!');
	redirect();
}

// Vai veidot divu dienu pasākumu (sākums piektdienas vakarā, bet beigas - svētdienas rītā)?
$arr_days = array(
	0 => 'Nē',
	1 => 'Jā'
);
// Kuri datumi Tev būtu vispieņemamākie?
$arr_dates = array(
	0 => '26.07 - 27.07',
	1 => '02.08 - 03.08',
	2 => '09.08 - 10.08',
	3 => '16.08 - 17.08',
	4 => '23.08 - 24.08'
);
// Lielākā summa, kādu esi gatavs maksāt par viesu mājas īri?
$arr_cost = array(
	0 => '5 Ls',
	1 => '6 Ls',
	2 => '7 Ls',
	3 => '8 Ls',
	4 => '9 Ls',
	5 => '10 Ls',
);
// Vai Tu būtu gatavs veikt maksājumu ar pārskaitījumu jau pirms pasākuma norises?
$arr_payment = array(
	0 => 'Jā',
	1 => 'Nē',
	2 => 'Maksātu uz vietas skaidrā naudā'
);
// Lielākais attālums līdz viesu mājai no Rīgas, kāds tev šķiet pieņemams?
$arr_distance = array(
	0 => '25 km',
	1 => '50 km',
	2 => '80 km',
	3 => '150 km',
	4 => 'Attālums nav svarīgs',
);





$voted = false;
$check = $db->get_row("SELECT * FROM `votes13` WHERE `user` = '" . $auth->id . "' ORDER BY `date` DESC");
if ($check)
	$voted = true;

// POST vērtības
if (!$voted && isset($_POST['submit']) && isset($_GET['var1']) && $_GET['var1'] == 'voted') {

	$name = (isset($_POST['user-name'])) ? sanitize(substr($_POST['user-name'], 0, 49)) : 'not entered';
	$age = (int) $_POST['user-age'];
	//$days		= (isset($_POST['days']) && ($_POST['days'] == 0 || $_POST['days'] == 1)) ? (int)$_POST['days'] : 0;
	$cost = (isset($_POST['cost']) && ((int) $_POST['cost'] >= 0 && (int) $_POST['cost'] < 6)) ? (int) $_POST['cost'] : 0;
	$payment = (isset($_POST['payment']) && ((int) $_POST['payment'] >= 0 && (int) $_POST['payment'] < 3)) ? (int) $_POST['payment'] : 0;
	$distance = (isset($_POST['distance']) && ((int) $_POST['distance'] >= 0 && (int) $_POST['distance'] < 5)) ? (int) $_POST['distance'] : 4;

	// checkbokšu pārbaude pieņemamajiem datumiem
	if (!empty($_POST['date'])) {
		foreach ($_POST['date'] as $check) {
			if ((int) $check < 5 && (int) $check >= 0) {
				$db->query("INSERT INTO `votes13_dates` (user,date,choice) VALUES (
					'" . $auth->id . "',
					NOW(),
					'" . (int) $check . "'
				) ");
			}
		}
	}

	$insert = $db->query("INSERT INTO `votes13` (user,date,ip,name,age,maxcost,paybycard,distance) VALUES(
		'" . $auth->id . "',
		NOW(),
		'" . $auth->ip . "',
		'" . $name . "',
		'" . $age . "',
		'" . $cost . "',
		'" . $payment . "',
		'" . $distance . "'
	) ");

	set_flash('Paldies par viedokli! Uz tikšanos grandiozākajā šīs vasaras ballītē!');
	redirect('/' . $category->textid);
	exit;
}

// rezultāti
if ($voted/* && isset($_GET['var1']) && $_GET['var1'] == 'results' */) {

	// pieņemamākie datumi
	$dates = $db->get_results("SELECT `choice`, count(*) AS 'count' FROM `votes13_dates` GROUP BY `choice` ORDER BY `choice` ASC");
	$data_count = $db->get_var("SELECT count(*) AS 'count' FROM `votes13` ");
	if ($dates) {
		$tpl->newBlock('vote-results');
		$tpl->assign('data-count', $data_count . ' ' . lv_dsk($data_count, 'reizi', 'reizes'));
		$tpl->newBlock('vote-data');
		$tpl->assign('question', 'Kuri datumi Tev būtu vispieņemamākie?');
		if ($auth->skin == 1) {
			$tpl->assign('dark-skin', ' style="background:#636262"');
		}

		foreach ($dates as $date) {
			$tpl->newBlock('vote-data-field');
			$tpl->assign(array(
				'field' => $arr_dates[$date->choice],
				'count' => $date->count,
				'bar-width' => floor(150 * ($date->count / $data_count)),
				'percents' => round(($date->count / $data_count) * 100, 1)
			));
		}
	}

	// viss pārējais
	$values_arr = array(
		//0 => array('length','Vai veidot divu dienu pasākumu (sākums piektdienas vakarā, bet beigas - svētdienas rītā)?','arr_days'),
		0 => array('maxcost', 'Lielākā summa, kādu esi gatavs maksāt par viesu mājas īri?', 'arr_cost'),
		1 => array('paybycard', 'Vai Tu būtu gatavs veikt maksājumu ar pārskaitījumu jau pirms pasākuma norises? (Uz vietas nedaudz dārgāk!)', 'arr_payment'),
		2 => array('distance', 'Lielākais attālums līdz viesu mājai no Rīgas, kāds tev šķiet pieņemams?', 'arr_distance'),
	);



	foreach ($values_arr as $values) {
		$get_data = $db->get_results("SELECT `" . $values[0] . "`, count(*) AS 'count' FROM `votes13` GROUP BY `" . $values[0] . "` ORDER BY `" . $values[0] . "` ASC");

		if ($get_data && $data_count) {

			//$tpl->newBlock('vote-results');
			$tpl->newBlock('vote-data');
			$tpl->assign('question', $values[1]);
			if ($auth->skin == 1) {
				$tpl->assign('dark-skin', ' style="background:#636262"');
			}

			foreach ($get_data as $single_data) {
				$tpl->newBlock('vote-data-field');
				$tpl->assign(array(
					'field' => ${$values[2]}[$single_data->$values[0]],
					'count' => $single_data->count,
					'bar-width' => floor(150 * ($single_data->count / $data_count)), // max 150 px garums
					'percents' => round(($single_data->count / $data_count) * 100, 1)
				));
			}
		}
	}
} else {
	if ($voted) {
		$tpl->newBlock('already-voted');
	} else {
		$tpl->newBlock('vote-content');
		$tpl->assign('cat', $category->textid);
		if ($auth->skin == 1) {
			$tpl->assign('dark-skin', ' style="background:#636262"');
		}
	}
}
?>