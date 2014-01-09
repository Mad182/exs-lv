<?php

/** 	
 * 	Atgriež fancybox saturu, kādu lietotāji redz, nospiežot nosūdzēšanas podziņu,
 * 	un apstrādā iesniegtās sūdzības.
 *
 * 	Moduļa adrese: 		exs.lv/report
 *
 *
 * 	0 - miniblogs (pats mb, mb komentārs, junk komentārs, ieraksti grupā)
 * 	1 - raksta komentārs (arī komentāru atbildes; /read sadaļā)
 * 	2 - galerijas attēla komentārs
 */
//	pieļaujamie ieraksti, kādus ļauts nosūdzēt
$allowed_report_types = array('miniblog', 'article-comment', 'gallery-comment');


// pieļaujamie projekti/apakšprojekti, kuros iespējotas sūdzības
//	1 - exs.lv; 
//	7 - lol.exs.lv
//  9 - runescape.exs.lv
$allowed_sites = array(1, 7, 9);


//	pēc idejas sadaļu skatīt var tikai caur jquery pieprasījumu;
if (!isset($_GET['_'])) {
	set_flash('Pieeja liegta!');
	redirect();
	exit;
}


//	lai varētu ziņot par pārkāpumu, lietotājam jābūt autorizētam projektā;
//	no telefoniem šāda iespēja arī nav ļauta
if (!$auth->ok || $auth->mobile || !in_array($lang, $allowed_sites)) {
	set_flash('Darbība liegta!');
	redirect();
	exit;
}

/**
 * 	Vairākās vietās izmantota funkcija kļūdas paziņojuma atgriešanai.
 *
 * 	@param - norāda kļūdas numuru, lai noteiktu, 
 * 			 kurš koda bloks to atgrieza.
 */
function send_error($e = 0) {
	global $template;

	$template->newBlock('error-message');
	$template->assign('error-message', 'Notikusi kļūda (#' . $e . ')! Pārlādē lapu un mēģini vēlreiz!');

	echo json_encode(
			array('state' => 'error', 'content' => $template->getOutputContent())
	);
	exit;
}

//	saturs uz fancybox tiks atgriezts no jauna template objekta
$template = new TemplatePower(CORE_PATH . '/modules/report/report.tpl');
$template->prepare();

//	katram sadaļas pieprasījumam jābūt formā /report/{entry-type}/{entry-id}
if (!isset($_GET['var1']) || !in_array($_GET['var1'], $allowed_report_types) ||
		!isset($_GET['var2']) || !is_numeric($_GET['var2'])) {
	send_error(1);
}

//	atkarībā no ieraksta veida izvēlas datubāzes tabulu
//	un citu saistošu informāciju
$entry_id = (int) $_GET['var2'];

switch ($_GET['var1']) {
	case 'article-comment':
		$entry_type = 'article-comment';
		$entry_type_id = 1;
		$entry_table = 'comments';
		break;
	case 'gallery-comment':
		$entry_type = 'gallery-comment';
		$entry_type_id = 2;
		$entry_table = 'galcom';
		break;
	// miniblogs
	default:
		$entry_type = 'miniblog';
		$entry_type_id = 0;
		$entry_table = 'miniblog';
		break;
};



/**
 * 	Apstrādā saņemtos $_POST datus,
 * 	kas sūdzības veidā tiek ierakstīti datubāzē.
 */
if (isset($_POST['report-reason'])) {

	//	anti-xsrf pārbaude
	if (!isset($_POST['anti-xsrf']) || $_POST['anti-xsrf'] != $auth->xsrf) {
		echo json_encode(array('state' => 'error', 'content' => 'Kļūdaini iesniegti dati!'));
		exit;
	}

	//	satura laukam jāsastāv vismaz no 10 simboliem
	if (mb_strlen($_POST['report-reason']) < 10) {
		echo json_encode(array('state' => 'error', 'content' => 'Pārkāpuma pamatojums par īsu!'));
		exit;
	}

	//	iesniegt sūdzību ļauts reizi 20 sekundēs
	if (isset($_SESSION['timeout_reports']) && ($_SESSION['timeout_reports'] + 20) > time()) {
		echo json_encode(
				array('state' => 'error', 'content' => 'Tik bieži iesniegt sūdzību nav ļauts! Lūdzu, nedaudz uzgaidi!')
		);
		exit;
	} else {
		$_SESSION['timeout_reports'] = time();
	}

	//	šī $_POST bloka atgrieztās kļūdas tiks parādītas jau iepriekš
	//	atgrieztā paragrāfā, tāpēc izsaukt send_error() nav vajadzīgs

	$report_reason = post2db($_POST['report-reason']);
	$report_content = '';

	//	pārbauda, vai ieraksts ar tādu ID eksistē, un atgriež saturu;
	//	netiek fiksēts, vai lietotājs vispār šādu ierakstu spēj fiziski skatīt,
	//	bet tam nav nozīmes, jo neapskatāma ieraksta iesūdzēšana neko ļaunu nedara
	$query_check = $db->get_row("SELECT `text` FROM `$entry_table` WHERE `id` = '$entry_id' AND `removed` = '0' ");
	if (!$query_check) {
		echo json_encode(array('state' => 'error', 'content' => 'Kļūdaini iesniegti dati!'));
		exit;
	} else {
		// nav jāsanitizo, jo vienreiz jau apstrādāts
		$report_content = $query_check->text;
	}

	$query_insert = $db->query("
		INSERT INTO `reports`
			(type, entry_id, comment, reported_content, created_by, created_at, site_id)
		VALUES
			('$entry_type_id', '$entry_id', '$report_reason', '$report_content', '" . $auth->id . "', '" . time() . "', $lang)
	");
	if ($query_insert) {
		$state = 'success';
		$content = 'Informācija par pārkāpumu veiksmīgi iesniegta!';
	} else {
		$state = 'error';
		$content = 'Kļūda! Sūdzību iesniegt neizdevās!';
	}
	echo json_encode(array('state' => $state, 'content' => $content));
	exit;
}






// lietotājs vēlas nosūdzēt mb, mb komentāru, junk komentāru vai ierakstu grupā
if ($entry_type == 'miniblog') {

	// pārbauda, vai norādītais mb ID datubāzē skatītajā apakšprojektā eksistē
	$query_data = $db->get_row("
		SELECT 
			`miniblog`.`id`, 
			`miniblog`.`author`,
			`miniblog`.`groupid`,
			`miniblog`.`text`,
			
			`users`.`id` AS `userid`,
			`users`.`nick`,
			`users`.`level`
			
		FROM `miniblog`
			JOIN `users` ON `miniblog`.`author` = `users`.`id`
		WHERE 
			`miniblog`.`id` 		= '" . (int) $_GET['var2'] . "' 	AND
			`miniblog`.`removed` 	= '0'						AND
			`miniblog`.`lang`		= $lang
	");

	if (!$query_data) {
		send_error(4);
	}

	// liedz nosūdzēt komentāru, ja tas atradies grupā,
	// kurai lietotājam nav piekļuves
	if (!empty($query_data->groupid)) {

		$group = $db->get_row("SELECT * FROM `clans` WHERE `id` = '$query_data->groupid' ");

		if (!$group->public && $group->owner !== $auth->id) {

			$is_member = $db->get_var("SELECT count(*) FROM `clans_members` WHERE `clan` = '$query_data->groupid' AND `user` = '$auth->id' AND `approve` = 1");

			if (!$is_member) {
				die('Nav pieejas!');
			}
		}
	}
}
// lietotājs vēlas nosūdzēt kāda raksta komentāru
else if ($entry_type == 'article-comment') {

	// pārbauda, vai norādītais komentāra ID datubāzē skatītajā apakšprojektā eksistē
	$query_data = $db->get_row("
		SELECT 
			`comments`.`id`, 
			`comments`.`author`,
			`comments`.`text`,
			
			`users`.`id` AS `userid`,
			`users`.`nick`,
			`users`.`level`
			
		FROM `comments`
			JOIN `users` ON `comments`.`author` = `users`.`id`
		WHERE 
			`comments`.`id` 		= '" . (int) $_GET['var2'] . "' AND
			`comments`.`removed` 	= '0'
	");

	if (!$query_data) {
		send_error(2);
	}
}
// lietotājs vēlas nosūdzēt kādu galerijas komentāru
else if ($entry_type == 'gallery-comment') {

	// pārbauda, vai norādītais galerijas komentāra ID datubāzē skatītajā apakšprojektā eksistē
	$query_data = $db->get_row("
		SELECT 
			`galcom`.`id`, 
			`galcom`.`author`,
			`galcom`.`text`,
			
			`users`.`id` AS `userid`,
			`users`.`nick`,
			`users`.`level`
			
		FROM `galcom`
			JOIN `users` ON `galcom`.`author` = `users`.`id`
		WHERE 
			`galcom`.`id` 		= '" . (int) $_GET['var2'] . "' AND
			`galcom`.`removed` 	= '0'
	");

	if (!$query_data) {
		send_error(5);
	}
} else {
	send_error(3);
}

$entry_text = add_smile(textlimit($query_data->text, 300));
$offender = usercolor($query_data->nick, $query_data->level);
$offender = '<a href="' . mkurl('user', $query_data->userid, $query_data->nick) . '">' . $offender . '</a>';

// atgriež HTML formu, kurā ļauts ievadīt pārkāpuma iemeslu
$template->newBlock('report-form');
$template->assign(array(
	'offender' => $offender,
	'action' => '/report/' . $entry_type . '/' . $entry_id,
	'entry-text' => $entry_text,
	'xsrf' => $auth->xsrf
));

if ($lang == 1) {
	$template->newBlock('main-exs-report-info');
} else {
	$template->newBlock('sub-exs-report-info');
}

echo json_encode(array('state' => 'success', 'content' => $template->getOutputContent()));
exit;

