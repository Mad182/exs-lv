<?php
/**
 *  Profilu sasaistes sadaļa.
 *
 *  Moderatoriem iespēja esošos viena lietotāja profilus sasaistīt kopā,
 *  lai vienkāršā veidā monitorētu fake profilu lietošanu un 
 *  vienlaicīgi varētu dāvāt liegumus veselām grupām profilu.
 */

$robotstag[] = 'noindex';
$add_css[] = 'grouped-profiles.css';

// šāda mainīgā eksistenci pārbaudīs failos, kas tiek iekļauti šajā failā
$sub_include = true;

if (!im_mod() || ($lang != 1 && $lang != 0)) {
	set_flash('Pieeja liegta!');
	redirect();
	exit;
}

/*
|--------------------------------------------------------------------------
|   jQuery AJAX: atgriezīs sadaļas pamatsaturu.
|--------------------------------------------------------------------------
|   Tiek izsaukts, nospiežot uz šīs sadaļas cilnes,
|   lai to ielādētu satura blokā zem ciļņu izvēlnes.
*/

if (isset($_GET['_']) && isset($_GET['load'])) {
    // saturs atsevišķā failā, jo tiek ielasīts vairākās vietās
    include_once(CORE_PATH.'/modules/grouped-profiles/main-content.php');
    echo json_encode(array(
        'content' => $new_tpl->getOutputContent()
    ));
    exit;
}


/*
|--------------------------------------------------------------------------
|   Lietotāja meklēšana no saraksta pēc tā profila ID.
|--------------------------------------------------------------------------  
*/
if (isset($_GET['var1']) && $_GET['var1'] == 'search' &&
    isset($_POST['user_id'])) {

	$user_id = (int)$_POST['user_id'];
	if ($user_id < 1) {
		set_flash('Norādīti kļūdaini dati!');
		redirect('/'.$_GET['viewcat']);
	}
	
	// nepieciešams norādītā lietotāja parent id, jo jāritina līdz tam
	$get_parent = $db->get_row("
		SELECT
			`users_groups`.`user_id`,
			IFNULL(`parent`.`user_id`, 0) AS `parent_id`
		FROM `users_groups`
			JOIN `users` ON (
				`users_groups`.`user_id` = `users`.`id` AND
				`users`.`deleted` = 0
			)
			LEFT JOIN `users_groups` AS `parent` ON (
				`users_groups`.`parent_id` = `parent`.`id`
			)
		WHERE 
			`users_groups`.`deleted_by` = 0 AND
			`users_groups`.`user_id` = ".$user_id."
		LIMIT 1
	");
	
	if (!$get_parent) {
		set_flash('Lietotājs netika atrasts!');
		redirect('/'.$_GET['viewcat']);
	}
	
	// norādītais lietotājs ir main profils
	if ($get_parent->parent_id == '0') {
		redirect('/'.$_GET['viewcat'].'?scroll='.$get_parent->user_id);
	} else { // norādītais lietotājs ir child profils
		redirect('/'.$_GET['viewcat'].'?scroll='.$get_parent->parent_id);
	}
}
	

/*
|--------------------------------------------------------------------------
|   "Main" profila ieraksta pievienošana sarakstam.
|--------------------------------------------------------------------------  
*/
else if (isset($_GET['var1']) && $_GET['var1'] == 'add-main') {

	if (!isset($_POST['userid']) || (int)$_POST['userid'] < 1) {
		set_flash('Norādīti kļūdaini dati!');
		redirect('/'.$_GET['viewcat']);
	}
	
	$user_id = (int)$_POST['userid'];
	
	$if_exists = get_user($user_id);
	if (!$if_exists) {
		set_flash('Norādītais lietotājs neeksistē!');
		redirect('/'.$_GET['viewcat']);
	} else if ($if_exists->level == 1 || $if_exists->level == 2) {
		set_flash('Norādītais lietotājs nevar tikt pievienots!');
		redirect('/'.$_GET['viewcat']);
	}
	
	// norādītais lietotājs vēl nedrīkst būt datubāzē, citādi 
	// vienā brīdī būs dublikāti
	$if_exists = $db->get_var("
		SELECT count(*) FROM `users_groups`
		JOIN `users` ON (
			`users_groups`.`user_id` = `users`.`id` AND
			`users`.`deleted` = 0
		)
		WHERE 
			`users_groups`.`deleted_by` = 0 AND
			`users_groups`.`user_id` = ".$user_id."
	");
	if ($if_exists > 0) {
		set_flash('Norādītais lietotājs jau atrodas kādā no grupām!');
		redirect('/'.$_GET['viewcat']);
	}
	
	$data = array(
		'user_id' => $user_id,
		'created_by' => $auth->id,
		'created_at' => time()
	);
	
	$insert = $db->insert('users_groups', $data);
	
	if ($insert !== false) {
		set_flash('Profils pievienots sarakstam!', 'success');
		redirect('/'.$_GET['viewcat'].'?scroll='.$user_id);
	} else {
		set_flash('Izveidot ierakstu neizdevās!');
		redirect('/'.$_GET['viewcat']);
	}
}


/*
|--------------------------------------------------------------------------
|   "Child" profilu piesaiste kādam "main" profilam.
|-------------------------------------------------------------------------- 
|   $_GET['var2'] - `users_groups`.`id` vērtība
*/
else if (isset($_GET['var1']) && $_GET['var1'] == 'add-child' &&
         isset($_GET['var2'])) {

	// lai piesaistītu child, datubāzē jau jābūt ierakstam par main profilu
	$parent_id = (int)$_GET['var2'];
	$parent_data = $db->get_row("
		SELECT
			`users`.`id`, 
			`users`.`nick`,
			`users`.`level`
		FROM `users_groups`
			JOIN `users` ON (
				`users_groups`.`user_id` = `users`.`id` AND
				`users`.`deleted` = 0
			)
		WHERE 
			`users_groups`.`id` = ".$parent_id." AND 
			`users_groups`.`deleted_by` = 0
	");
	if (!$parent_data) {
		if (isset($_GET['_'])) { // ajax pieprasījums
			echo 'Darbība neizdevās';
			exit;
		} else {
			set_flash('Darbība neizdevās!');
			redirect('/'.$_GET['viewcat']);
		}
	}
	
	// atgriezīs fancybox ar child pievienošanas formu
	if (isset($_GET['_'])) {
	
		$templ = fetch_tpl();
		if ($templ === false) {
			echo 'Neizdevās atlasīt datus.';
		} else {
			$templ->newBlock('new-child-form');            
			$templ->assign(array(
				'category-url' => $category->textid,
				'main-id' => $parent_id,
				'main-profile' => usercolor($parent_data->nick, $parent_data->level, false)
			));
			
			echo $templ->getOutputContent();
		}
		
		exit;
	
	// pievienos ierakstu datubāzei
	} else if (isset($_POST['child_ids'])) {

		$childs_not_added = 0;
		$childs_added = 0;

		// ievades laukā var būt norādīti vairāki profilu id, 
		// atdalīti ar komatiem
		$child_ids = explode(',', $_POST['child_ids']);
		foreach ($child_ids as $child_id) {
		
			$child_id = trim($child_id);
			$child_id = (int)$child_id;

			if ($child_id > 0) {
			
				$if_exists = get_user($child_id);        
				if (!$if_exists) {
					$childs_not_added++;
				// pievienot modus/adminus nebūs ļauts
				} else if ($if_exists->level == 1 || $if_exists->level == 2) {
					$childs_not_added++;
				} else {
					
					// child vēl nedrīkst būt datubāzē, citādi 
					// vienā brīdī būs dublikāti
					$if_exists = $db->get_var("
						SELECT count(*) FROM `users_groups`
						JOIN `users` ON (
							`users_groups`.`user_id` = `users`.`id` AND
							`users`.`deleted` = 0
						)
						WHERE 
							`users_groups`.`deleted_by` = 0 AND
							`users_groups`.`user_id` = ".$child_id."
					");
					if ($if_exists > 0) {
						$childs_not_added++;
					} else {
					
						$data = array(
							'user_id' => $child_id,
							'parent_id' => $parent_id,
							'created_by' => $auth->id,
							'created_at' => time()
						);
						
						$insert = $db->insert('users_groups', $data);
						
						if ($insert === false) {
							$childs_not_added++;
						} else {
							$childs_added++;
						}
					}
				}
			} else {
				$childs_not_added++;
			}
		}
		
		if ($childs_not_added > 0) {
			set_flash('Piesaistīti profili: '.$childs_added.'. Nepiesaistīti profili: '.$childs_not_added.'.');
		} else {            
			set_flash('Piesaistīti '.$childs_added.' profili.', 'success');
		}
		$auth->log('Piesaistīja '.$childs_added.' profilus', 'users', $parent_data->id);
		
		redirect('/'.$_GET['viewcat'].'?scroll='.$parent_data->id);

	} else {
		set_flash('Kļūdaini norādīta adrese!');
		redirect('/'.$_GET['viewcat']);
	}
}


/*
|--------------------------------------------------------------------------
|   Sasaistīto profilu grupas apraksta rediģēšana.
|-------------------------------------------------------------------------- 
*/
else if (isset($_GET['var1']) && $_GET['var1'] == 'edit' &&
         isset($_GET['var2'])) {

	$group_id = (int)$_GET['var2'];
	
	$data = $db->get_row("
		SELECT 
			`users_groups`.`id`,
			`users_groups`.`user_id`,
			`users_groups`.`description`,
			`users`.`nick`,
			`users`.`level`
		FROM `users_groups`
			JOIN `users` ON (
				`users_groups`.`user_id` = `users`.`id` AND
				`users`.`deleted` = 0
			)
		WHERE
			`users_groups`.`deleted_by` = 0 AND
			`users_groups`.`id` = ".$group_id."
	");
	
	if (!$data) {
		if (isset($_GET['_'])) { // ajax pieprasījums
			echo 'Darbība neizdevās';
			exit;
		} else {
			set_flash('Darbība neizdevās!');
			redirect('/'.$_GET['viewcat']);
		}
	}
	
	// atgriezīs fancybox ar apraksta rediģēšanas formu
	if (isset($_GET['_'])) {
	
		$templ = fetch_tpl();
		if ($templ === false) {
			echo 'Neizdevās atlasīt datus.';
		} else {
			$templ->newBlock('edit-description');            
			$templ->assign(array(
				'category-url' => $category->textid,
				'main-id' => $group_id,
				'main-profile' => usercolor($data->nick, $data->level, false),
				'description' => $data->description
			));
			
			echo $templ->getOutputContent();
		}
		
		exit;
	
	// atjaunos aprakstu datubāzē
	} else if (isset($_POST['description'])) {

		$description = input2db($_POST['description'], 2000);
		
		$values = array('description' => $description);
		$criteria = array('id' => $data->id);        
		$update = $db->update('users_groups', $criteria, $values);
		
		if ($update !== false) {
			set_flash('Apraksts atjaunots!', 'success');
			redirect('/'.$_GET['viewcat'].'?scroll='.$data->user_id);
		} else {
			set_flash('Atjaunot aprakstu neizdevās!');
			redirect('/'.$_GET['viewcat']);
		}

	} else {
		set_flash('Kļūdaini norādīta adrese!');
		redirect('/'.$_GET['viewcat']);
	}
}


/*
|--------------------------------------------------------------------------
|   Profilu grupas dzēšana.
|--------------------------------------------------------------------------
|   $_GET['var2'] - `users_groups`.`id`
*/
else if (isset($_GET['var1']) && $_GET['var1'] == 'delete-group' &&
         isset($_GET['var2'])) {

	$group_id = (int)$_GET['var2'];
	if ($group_id < 1) {
		if (isset($_GET['_'])) { // ajax pieprasījums
			echo 'Darbības neizdevās!';
			exit;
		} else {
			set_flash('Darbība neizdevās!');
			redirect('/'.$_GET['viewcat']);
		}
	}
	
	// atgriezīs fancybox saturu ar dzēšanas apstiprinājumu
	if (isset($_GET['_'])) {
	
		$tmpl = fetch_tpl();
		if ($tmpl === false) {
			echo 'Darbība neizdevās!';
			exit;
		}
		
		// apstiprinājuma logā jāvar parādīt lietotāja niku tā krāsās
		$data = $db->get_row("
			SELECT
				`users`.`id`, `users`.`nick`, `users`.`level`
			FROM `users_groups`
				JOIN `users` ON (
					`users_groups`.`user_id` = `users`.`id` AND
					`users`.`deleted` = 0
				)
			WHERE 
				`users_groups`.`id` = ".$group_id." AND 
				`users_groups`.`deleted_by` = 0
		");
		if (!$data) {
			echo 'Darbība neizdevās';
			exit;
		}
		
		// child profilu skaits
		$profile_count = $db->get_var("
			SELECT count(*) FROM `users_groups`
			JOIN `users` ON (
				`users_groups`.`user_id` = `users`.`id` AND
				`users`.`deleted` = 0
			)
			WHERE 
				`users_groups`.`deleted_by` = 0 AND 
				`users_groups`.`parent_id` = ".$group_id."
		");
		
		$tmpl->newBlock('delete-confirmation');
		$tmpl->assign(array(
			'category-url' => $category->textid,
			'main-id' => $group_id,
			'main-profile' => usercolor($data->nick, $data->level, false),
			'profile-count' => $profile_count
		));
		
		echo $tmpl->getOutputContent();
		exit;
	
	// atzīmēs grupu kā dzēstu
	} else {
	
		$data = array(
			'deleted_by' => $auth->id,
			'deleted_at' => time()
		);
		$criteria = array(
			'id' => $group_id
		);
		$update = $db->update('users_groups', $criteria, $data);
		
		if ($update) {
			set_flash('Profilu grupa dzēsta!', 'success');
			$auth->log('Dzēsa profilu grupu', 'ug', $group_id);
		} else {
			set_flash('Profilu grupu dzēst neizdevās!');
		}
		redirect('/'.$category->textid);
	}
}
 

/*
|--------------------------------------------------------------------------
|   Kāda "child" profila atsaistīšana no "main" profila.
|--------------------------------------------------------------------------
|   $_GET['var2'] - `users_groups`.`id`
*/
else if (isset($_GET['var1']) && $_GET['var1'] == 'delete-child' &&
         isset($_GET['var2'])) {

	$child_id = (int)$_GET['var2'];
	if ($child_id < 1) {
		set_flash('Atsaistīšana neizdevās!');
		redirect('/'.$_GET['viewcat']);
	}
	
	$update = $db->query("
		UPDATE `users_groups` 
		SET 
			`deleted_by` = ".(int)$auth->id.", 
			`deleted_at` = ".sanitize(time())." 
		WHERE `id` = ".$child_id." 
		LIMIT 1
	");
	
	if ($update) {
		set_flash('Profils atsaistīts!', 'success');
		$auth->log('Atsaistīja profilu', 'ug', $child_id);
		if (isset($_GET['var3'])) {
			$var3 = (int)$_GET['var3'];
			redirect('/'.$_GET['viewcat'].'?scroll='.$var3);
		}
	} else {
		set_flash('Profilu atsaistīt neizdevās!');
	}
	
	redirect('/'.$_GET['viewcat']);
}


/*
|--------------------------------------------------------------------------
|   Apmainīs norādīto "child" profilu vietām ar "main".
|--------------------------------------------------------------------------
|   $_GET['var2'] - `users_groups`.`id`
*/
else if (isset($_GET['var1']) && $_GET['var1'] == 'change-main' &&
         isset($_GET['var2'])) {

	$child_id = (int)$_GET['var2'];
	if ($child_id < 1) {
		set_flash('Darbība neizdevās!');
		redirect('/'.$_GET['viewcat']);
	}

	$data = $db->get_row("
		SELECT
			`users_groups`.`id`,
			`users_groups`.`user_id`,
			`users_groups`.`parent_id`,
			`parent`.`description`
		FROM `users_groups`
			JOIN `users` ON (
				`users_groups`.`user_id` = `users`.`id` AND
				`users`.`deleted` = 0
			)
			JOIN `users_groups` AS `parent` ON (
				`users_groups`.`parent_id` = `parent`.`id` AND
				`parent`.`deleted_by` = 0
			)
		WHERE 
			`users_groups`.`id` = ".$child_id." AND 
			`users_groups`.`deleted_by` = 0 AND 
			`users_groups`.`parent_id` != 0
	");
	
	if (!$data) {
		set_flash('Darbība neizdevās!');
		redirect('/'.$_GET['viewcat']);
	}
	
	// child -> main
	$arr = array('parent_id' => 0, 'description' => sanitize($data->description));
	$criteria = array('id' => $data->id);
	$upd = $db->update('users_groups', $criteria, $arr);
	
	if (!$upd) {
		set_flash('Darbība neizdevās!');
		redirect('/'.$_GET['viewcat']);
	}
	
	// main -> child
	$arr = array('parent_id' => $data->id, 'description' => '');    
	$criteria = array('id' => $data->parent_id, 'deleted_by' => 0);
	$upd = $db->update('users_groups', $criteria, $arr);
	
	// children -> change parent
	$criteria = array('parent_id' => $data->parent_id, 'deleted_by' => 0);
	$upd = $db->update('users_groups', $criteria, $arr);
	
	redirect('/'.$_GET['viewcat'].'?scroll='.$data->user_id);
}


/*
|--------------------------------------------------------------------------
|   Ievietos lapā sadaļas pamatsaturu - sarakstu ar saistītajiem profiliem.
|--------------------------------------------------------------------------
*/
else {
    $tpl->newBlock('mcp-grouped-profiles-tabs');
    // šis "div" tags nepieciešams javascriptam
    $tpl->newBlock('mcp-grouped-outer-start');
    // saturs atsevišķā failā, jo tiek ielasīts vairākās vietās
    include_once(CORE_PATH.'/modules/grouped-profiles/main-content.php');
    // varam jauno HTML iekļaut esošajā template kā parastu mainīgo
    $tpl->gotoBlock('_ROOT');
    $tpl->assign('includable-content', $new_tpl->getOutputContent());
    $tpl->newBlock('mcp-grouped-outer-end');
}
