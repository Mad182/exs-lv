<?php
/**
 *  RuneScape faktu pārvaldība.
 *
 *  Iespēja apskatīt esošos un pievienot jaunus faktus.
 *
 * 	@addr   exs.lv/rsfacts
 */
if ($lang != 9) {
    set_flash('Pieeja sadaļai liegta!');
	redirect();
}
// 3217 - Mahjarrat
if (!im_mod() && !in_array($auth->id, array(3217))) {
    set_flash('Pieeja sadaļai liegta!');
	redirect();
}



/**
 *  Atgriež RuneScape faktu jquery pieprasījumam parādīšanai sākumlapā
 */
if (isset($_GET['_'])) {

	$facts_count = $db->get_var("SELECT count(*) FROM `rs_facts` WHERE `deleted_by` = 0 ");
    
	if ($facts_count > 0) {

		$rand = rand(0, $facts_count - 1);
		$single_fact = $db->get_row("SELECT `text` FROM `rs_facts` WHERE `deleted_by` = 0 LIMIT $rand, 1");

		if ($single_fact) {
			echo json_encode(array('state' => 'success', 'content' => $single_fact->text));
            exit;
		}
	}
    echo json_encode(array('state' => 'error', 'content' => 'Piedod, neatradu nevienu RuneScape faktu! ;('));
    exit;
}



// fakta dzēšana
else if (isset($_GET['var1']) && $_GET['var1'] == 'delete' && 
         isset($_GET['var2']) && is_numeric($_GET['var2'])) {
         
    if (!isset($_GET['val']) || $_GET['val'] != $auth->xsrf) {
        set_flash('Pls, no!');
        redirect('/' . $category->textid);
    } 
    
	$delete = (int)$_GET['var2'];
	if ($db->query("
        UPDATE `rs_facts` 
        SET 
            `deleted_by` = '".$auth->id."', 
            `deleted_at` = '".time()."' 
        WHERE `id` = $delete 
        LIMIT 1"
    )) {
        set_flash('Fakts veiksmīgi dzēsts!');
    } else {
        set_flash('Faktu dzēst neizdevās!');
    }
	redirect('/' . $category->textid);
}



// fakta rediģēšana
else if (isset($_GET['var1']) && $_GET['var1'] == 'edit' && 
         isset($_GET['var2']) && is_numeric($_GET['var2'])) {

	$fact_id    = (int)$_GET['var2'];
	$fact       = $db->get_row("SELECT * FROM `rs_facts` WHERE `deleted_by` = 0 AND `id` = $fact_id ");

	// tukšu lapu nav vērts rādīt, tāpēc pārvirzām uz faktu sarakstu
	if (!$fact) {
        set_flash('Kļūdaini norādīts fakta ID!');
		redirect('/' . $category->textid);
	}

	// fakta informācijas atjaunošana datubāzē
	if (isset($_POST['edit-fact'])) {
    
        if (!isset($_POST['anti-xsrf']) || $_POST['anti-xsrf'] != $auth->xsrf) {
            set_flash('Pls, no!');
            redirect('/' . $category->textid);
        }

		$fact_text = sanitize(trim($_POST['edit-fact']));

		if ($db->query("
            UPDATE `rs_facts` 
            SET 
                `text`          = '$fact_text',
                `updated_by`    = '".(int)$auth->id."',
                `updated_at`    = '".time()."'
            WHERE `id` = $fact_id 
        ")) {
            set_flash('Fakts veiksmīgi labots!');
		} else {
            set_flash('Faktu izlabot neizdevās!');
        }
		redirect('/' . $category->textid);
	}

	// rediģēšanas forma
    $fact->text = stripslashes($fact->text);
	$tpl->newBlock('block-edit');
	$tpl->assignAll($fact);
    $tpl->assign('xsrf', $auth->xsrf);
}



// fakta ierakstīšana datubāzē
else if (isset($_POST['new-fact'])) {

    if (!isset($_POST['anti-xsrf']) || $_POST['anti-xsrf'] != $auth->xsrf) {
        set_flash('Pls, no!');
        redirect('/' . $category->textid);
    }
    
	$newfact = sanitize(trim($_POST['new-fact']));

	if ($db->query("
        INSERT INTO `rs_facts` 
            (text, created_by, created_at) 
        VALUES 
            ('$newfact', '".(int)$auth->id."', '".time()."')
    ")) {
		set_flash('Fakts veiksmīgi pievienots!');
	} else {
        set_flash('Faktu pievienot neizdevās!');
    }
    redirect('/' . $category->textid);
}



// pievienošanas forma
if (!isset($_GET['var1'])) {
    $tpl->newBlock('block-add');
    $tpl->assign('xsrf', $auth->xsrf);
}



// no datubāzes atlasa visus pievienotos konkrētā veida faktus un
// izvada tos saraksta veidā
$facts = $db->get_results("SELECT * FROM `rs_facts` WHERE `deleted_by` = 0 ORDER BY `id` DESC");

if ($facts) {

    $counter = 1;

	$tpl->newBlock('block-list');

	foreach ($facts as $fact) {
    
        $fact->text = stripslashes($fact->text);

		$tpl->newBlock('single-fact');
        $tpl->assignAll($fact);
        $tpl->assign(array(
            'xsrf' => $auth->xsrf,
            'counter' => $counter
        ));
        
        $counter++;
	}
}