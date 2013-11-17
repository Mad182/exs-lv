<?php
/**	
 *	Fancybox saturs, kādu lietotāji redz, nospiežot nosūdzēšanas podziņu.
 *
 *	Moduļa adrese: 		exs.lv/report
 *	Pēdējās izmaiņas: 	10.10.2013 ( Edgars )
 */
 
/*
	<span class="report-button">
		<a class="report-user" href="/report/article-comment/{comment-id}">
			<img src="/bildes/fugue-icons/blue-document-horizontal-text.png" title="Ziņot par pārkāpumu!" alt="">
		</a>
	</span>
*/

/**
 *	0 - miniblogs (gan komentārs, gan pats mb; arī grupā)
 *	1 - raksta komentārs (/read sadaļā)
 *	2 - galerijas attēla komentārs
 *
 *	--- zemāk esošie vēl nav ieviesti
 *	3 - galerijas attēls
 *	4 - raksts kā tāds
 *	5 - junk bilde
 */
 
//	pieļaujamie ieraksti, kādus ļauts nosūdzēt
$allowed_report_types = array('miniblog', 'article-comment', 'gallery-comment');

//	pēc idejas sadaļu skatīt var tikai caur jquery pieprasījumu
if ( !isset($_GET['_']) ) {
	set_flash('Pieeja liegta!');
	redirect();
	exit;
}

//	lai varētu ziņot par pārkāpumu, lietotājam jābūt autorizētam exs.lv;
//	no telefoniem šāda iespēja arī nav ļauta
if ( !$auth->ok || $auth->mobile || $lang != 1 ) {
	echo json_encode( array('state' => 'error', 'content' => 'Darbība liegta!' ) );
	exit;
}
 
/**
 *	Vairākās vietās izmantota funkcija kļūdas paziņojuma atgriešanai.
 *
 *	@param - norāda kļūdas numuru, lai noteiktu, 
 *			 kurš koda bloks to atgrieza.
 */
function send_error($e = 0) {
	global $template;
	
	$template->newBlock('error-message');
	$template->assign('error-message', 'Notikusi kļūda (#'.$e.')! Pārlādē lapu un mēģini vēlreiz!');

	echo json_encode(
		array('state' => 'error', 'content' => $template->getOutputContent() )
	);
	exit;
}


 
//	saturs uz fancybox tiks atgriezts no jauna template objekta
$template = new TemplatePower(CORE_PATH . '/modules/report/report.tpl');
$template->prepare();

//	katram sadaļas pieprasījumam jābūt formā /report/{entry-type}/{entry-id}
if ( !isset($_GET['var1']) || !in_array($_GET['var1'], $allowed_report_types) || 
	!isset($_GET['var2']) || !is_numeric($_GET['var2']) ) {
	send_error(1);
}

//	atkarībā no ieraksta veida izvēlas datubāzes tabulu
//	un citu saistošu informāciju
$entry_id = (int)$_GET['var2'];

switch ($_GET['var1']) {
	case 'article-comment':
		$entry_type 		= 'article-comment';
		$entry_type_id		= 1;
		$entry_table		= 'comments';
		break;
	case 'gallery-comment':
		$entry_type 		= 'gallery-comment';
		$entry_type_id		= 2;
		$entry_table		= 'galcom';
		break;
	// miniblogs
	default:
		$entry_type 		= 'miniblog';
		$entry_type_id		= 0;
		$entry_table		= 'miniblog';
		break;
};



/**
 *	Apstrādā saņemtos $_POST datus,
 *	kas sūdzības veidā tiek ierakstīti datubāzē.
 */
if ( isset($_POST['report-reason']) ) {

	//	satura laukam jāsastāv vismaz no 10 simboliem
	if ( strlen($_POST['report-reason']) < 10 ) {
		echo json_encode( array('state' => 'error', 'content' => 'Pārkāpuma pamatojums par īsu!') );
		exit;
	}

	//	iesniegt sūdzību ļauts reizi 20 sekundēs
	if ( isset($_SESSION['timeout_reports']) && ($_SESSION['timeout_reports'] + 20) > time() ) {
		echo json_encode( 
			array('state' => 'error', 'content' => 'Tik bieži iesniegt sūdzību nav ļauts! Lūdzu, nedaudz uzgaidi!') 
		);
		exit;
	} else {
		$_SESSION['timeout_reports'] = time();
	}

	//	šī $_POST bloka atgrieztās kļūdas tiks parādītas jau iepriekš
	//	atgrieztā paragrāfā, tāpēc izsaukt send_error() nav vajadzīgs

	$report_reason	= post2db($_POST['report-reason']);
	
	//	pārbauda, vai ieraksts ar tādu ID eksistē
	$query_check = $db->get_var("SELECT count(*) FROM `$entry_table` WHERE `id` = '$entry_id' AND `removed` = '0' ");
	if ( $query_check == 0 ) {
		echo json_encode( array('state' => 'error', 'content' => 'Kļūdaini iesniegti dati!') );
		exit;
	}
		
	$query_insert = $db->query("
		INSERT INTO `reports`
			(type, entry_id, comment, created_by, created_at)
		VALUES
			('$entry_type_id', '$entry_id', '$report_reason', '".$auth->id."', '".time()."')
	");
	if ( $query_insert ) {
		$state 		= 'success';
		$content 	= 'Informācija par pārkāpumu veiksmīgi iesniegta!';
	}
	else {
		$state 		= 'error';
		$content 	= 'Kļūda! Sūdzību iesniegt neizdevās!';
	}
	echo json_encode( array('state' => $state, 'content' => $content) );
	exit;
}






// nosūdzēts tiek minibloga ieraksts
if ( $entry_type == 'miniblog' ) {
	
	// pārbauda, vai norādītais minibloga ID datubāzē eksistē
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
			`miniblog`.`id` 		= '".(int)$_GET['var2']."' AND
			`miniblog`.`removed` 	= '0'
	");

	if(!empty($query_data->groupid)) {
		$group = $db->get_row("SELECT * FROM `clans` WHERE `id` = '$query_data->groupid'");

		if(!$group->public && $group->owner !== $auth->id) {
			$is_member = $db->get_var("SELECT count(*) FROM `clans_members` WHERE `clan` = '$query_data->groupid' AND `user` = '$auth->id' AND `approve` = 1");

			if(!$is_member) {
				die('Nav pieejas!');
			}
		}
	}
	
	if ( !$query_data ) {
		send_error(4);
	}
	
	$entry_text = textlimit($query_data->text, 300);
	
	$offender 	= usercolor($query_data->nick, $query_data->level);
	$offender 	= '<a href="'.mkurl('user', $query_data->userid, $query_data->nick).'">'.$offender.'</a>';
	
}
//	nosūdzēts tiek kāda raksta komentārs
else if ( $entry_type == 'article-comment' ) {

	// pārbauda, vai norādītais komentāra ID datubāzē eksistē
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
			`comments`.`id` 		= '".(int)$_GET['var2']."' AND
			`comments`.`removed` 	= '0'
	");
	
	if ( !$query_data ) {
		send_error(2);
	}
	
	$entry_text = textlimit($query_data->text, 300);
	
	$offender 	= usercolor($query_data->nick, $query_data->level);
	$offender 	= '<a href="'.mkurl('user', $query_data->userid, $query_data->nick).'">'.$offender.'</a>';
}
//	nosūdzēts tiek galerijas komentārs
else if ( $entry_type == 'gallery-comment' ) {
	
	// pārbauda, vai norādītā galerijas komentāra ID datubāzē eksistē
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
			`galcom`.`id` 		= '".(int)$_GET['var2']."' AND
			`galcom`.`removed` 	= '0'
	");
	
	if ( !$query_data ) {
		send_error(5);
	}
	
	$entry_text = textlimit($query_data->text, 300);
	
	$offender 	= usercolor($query_data->nick, $query_data->level);
	$offender 	= '<a href="'.mkurl('user', $query_data->userid, $query_data->nick).'">'.$offender.'</a>';
	
}
else {
	send_error(3);
}


// atgriež HTML formu, kurā ļauts ievadīt pārkāpuma iemeslu
$template->newBlock('report-form');
$template->assign(array(
	'offender' 		=> $offender,
	'action'		=> '/report/'.$entry_type.'/'.$entry_id,
	'entry-text'	=> $entry_text
));

echo json_encode( array('state' => 'success', 'content' => $template->getOutputContent() ) );
exit;

