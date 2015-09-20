<?php
/**
 *  Atgriezīs fancybox saturu, kāds lietotājiem redzams, nospiežot uz
 *  noziņošanas podziņas pie dažādiem ierakstiem lapā, kā arī
 *  apstrādās iesniegto formu datus.
 *
 * 	Moduļa adrese: exs.lv/report
 *  ---------------------------------------------------------------------------
 *
 *  Ziņojumu veidu ID, kas tiks ierakstīti datubāzē:
 *
 *    0 - miniblogs (pats mb, mb komentārs, junk komentārs, ieraksti grupā)
 * 	  1 - raksta komentārs (arī komentāru atbildes; /read sadaļā)
 * 	  2 - galerijas attēla komentārs
 */

 
// katram pieprasījumam uz šo sadaļu jābūt formātā
//   /report/{entry-type}/{entry-id}
// kur {entry-type} ir kāda no šīm vērtībām...
$allowed_types = array(
    'miniblog',
    'article-comment',
    'gallery-comment'
);

// pieļaujamie projekti/apakšprojekti, kuros iespējotas sūdzības:
// exs.lv, lol.exs.lv, runescape.exs.lv
$allowed_sites = array(1, 7, 9);

// sadaļa ielādējama tikai caur ajax pieprasījumu
if (!isset($_GET['_'])) {
	set_flash('Nepareizi veikts pieprasījums!');
	redirect();
	exit;
// lai varētu ziņot par pārkāpumu, lietotājam jābūt autorizētam projektā;
// no telefoniem šāda iespēja arī nav ļauta
} else if (!$auth->ok || $auth->mobile || !in_array($lang, $allowed_sites)) {
	set_flash('Darbība liegta!');
	redirect();
	exit;
}


/*
|--------------------------------------------------------------------------
|   Modulim specifiskas funkcijas.
|--------------------------------------------------------------------------
*/

/**
 *  Atgriezīs paziņojumu par sūdzības kļūdu.
 */
function send_error($e = 0) {
    $content = $e;
    if (is_int($e)) {
        $content = '<p class="report-error">Notikusi kļūda (#'.$e.')! '.
                   'Pārlādē lapu un mēģini vēlreiz.</p>';
    }
	echo json_encode(array(
        'state' => 'error',
        'content' => $content
    )); exit;
}


/*
|--------------------------------------------------------------------------
|   Pieprasījuma parametru noteikšana.
|--------------------------------------------------------------------------
*/

// katram sadaļas pieprasījumam jābūt formā /report/{entry-type}/{entry-id}
if (!isset($_GET['var1']) || !in_array($_GET['var1'], $allowed_types) ||
    !isset($_GET['var2']) || !is_numeric($_GET['var2'])) {
	send_error(1);
}

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
	default:
		$entry_type = 'miniblog';
		$entry_type_id = 0;
		$entry_table = 'miniblog';
		break;
};


/*
|--------------------------------------------------------------------------
|   Iesniegtas formas apstrāde un ziņojuma ievietošana datubāzē.
|--------------------------------------------------------------------------
*/

if (isset($_POST['report-reason'])) {

    // dažādas pārbaudes
	if (!isset($_POST['anti-xsrf']) || $_POST['anti-xsrf'] !== $auth->xsrf) {
        send_error('hacking around?<br>Tava IP ir piefiksēta, un par darbību noziņots Drošības policijai.');
	} else if (mb_strlen($_POST['report-reason']) < 10) {
        send_error('Lūdzu, apraksti pārkāpumu plašāk!');
	} else if (isset($_SESSION['timeout_reports']) &&
        ($_SESSION['timeout_reports'] + 20) > time()) {
        send_error('Tik bieži iesniegt sūdzību nav ļauts! Lūdzu, brīdi uzgaidi!');
	}
	$_SESSION['timeout_reports'] = time();

    // caur formu iesniegtais saturs
	$report_reason = post2db($_POST['report-reason']);
	$report_content = '';

    // pārbaudīs, vai noziņojamais ieraksts maz eksistē;
	// neskatīs, vai lietotājs ierakstam vispār tiek klāt, bet tas nav svarīgi,
	// jo neapskatāma ieraksta iesūdzēšana neko ļaunu nenodarīs
	$query_check = $db->get_row("
        SELECT `text` FROM `$entry_table`
        WHERE `id` = $entry_id AND `removed` = 0
    ");
	if (!$query_check) send_error('hacking around?<br>Tava IP ir piefiksēta, un par darbību noziņots Drošības policijai.');

    // nav jāsanitizo, jo vienreiz jau apstrādāts
    $report_content = $query_check->text;

    $insert_data = array(
        'type' => $entry_type_id,
        'entry_id' => $entry_id,
        'comment' => $report_reason,
        'reported_content' => $report_content,
        'created_by' => $auth->id,
        'created_at' => time()
    );
    $sql = $db->insert('reports', $insert_data);

	if ($sql) {
		$state = 'success';
		$content = 'Informācija par pārkāpumu iesniegta.';
	} else {
		$state = 'error';
		$content = 'Sūdzību iesniegt neizdevās. :(';
	}
    
	echo json_encode(array(
        'state' => $state,
        'content' => $content
    )); exit;
}


/*
|--------------------------------------------------------------------------
|   Visam pārējam atgriezīs formu sūdzības iesniegšanai.
|--------------------------------------------------------------------------
*/

// pārbaudīs, vai ieraksts attiecīgajā db tabulā eksistē un vai
// lietotājam tam vispār ir piekļuve

// mb, mb komentārs, junk komentārs vai ieraksts grupā
if ($entry_type == 'miniblog') {

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
			`miniblog`.`id` = ".(int)$_GET['var2']." AND
			`miniblog`.`removed` = 0 AND
			`miniblog`.`lang` = $lang
	");
	if (!$query_data) send_error(4);

    // liegs nosūdzēt ierakstu, ja lietotājam tam nemaz nav pieejas
	if (!empty($query_data->groupid)) {
		$group = $db->get_row("
            SELECT * FROM `clans` WHERE `id` = '$query_data->groupid'
        ");
		if (!$group->public && $group->owner !== $auth->id) {
			$is_member = $db->get_var("
                SELECT count(*) FROM `clans_members`
                WHERE `clan` = '$query_data->groupid' AND
                      `user` = '$auth->id' AND `approve` = 1
            ");
			if (!$is_member) die('Nav pieejas!');
		}
	}

// raksta komentārs
} else if ($entry_type == 'article-comment') {

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
			`comments`.`id` = ".(int)$_GET['var2']." AND
			`comments`.`removed` = 0
	");
	if (!$query_data) send_error(2);

// galerijas komentārs
} else if ($entry_type == 'gallery-comment') {

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
			`galcom`.`id` = ".(int)$_GET['var2']." AND
			`galcom`.`removed` = 0
	");
	if (!$query_data) send_error(5);

} else {
	send_error(3);
}


// saformatēs atgriežamo HTML saturu
$entry_text = add_smile(textlimit($query_data->text, 300));
$offender = usercolor($query_data->nick, $query_data->level);
$offender = '<a href="/user/' . $query_data->userid . '">' . $offender . '</a>';

$new_tpl = fetch_tpl();
$new_tpl->newBlock('report-form');
$new_tpl->assign(array(
	'offender' => $offender,
	'action' => '/report/' . $entry_type . '/' . $entry_id,
	'entry-text' => $entry_text,
	'xsrf' => $auth->xsrf
));

// dažādiem projektiem ir dažāds rādāmais saturs
if ($lang == 1) {
	$new_tpl->newBlock('main-exs-report-info');
} else {
	$new_tpl->newBlock('sub-exs-report-info');
}

// visbeidzotsaturs tiks atgriezts pieprasījumam
echo json_encode(array(
    'state' => 'success',
    'content' => $new_tpl->getOutputContent()
)); exit;
