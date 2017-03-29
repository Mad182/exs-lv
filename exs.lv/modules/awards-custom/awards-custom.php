<?php
/**
 *  Adminu sadaļa custom profila medaļu piešķiršanai lietotājiem.
 */

// admins-only
if (!$auth->ok || !in_array((int)$auth->id, [1, 115])) {
    set_flash('lost?');
    redirect('/');
}


/*
|--------------------------------------------------------------------------
|   Jauna apbalvojuma veida pievienošana ($_POST).
|--------------------------------------------------------------------------
*/
if (isset($_GET['var1']) && $_GET['var1'] === 'new_award' && isset($_POST['submit'])) {
    
    if (!check_token('new_award_type', $_GET['token'])) {
        set_flash('no hacking, pls');
        redirect('/'.$category->textid);
    }
    
    if (isset($_FILES['image'])) {        
        require_once(LIB_PATH . '/verot/src/class.upload.php');        
		$foo = new Upload($_FILES['image']);
        $dst_filename = strtolower(str_replace(' ', '_', $foo->file_src_name_body));
        $foo->file_new_name_body = $dst_filename;
		$foo->image_resize = true;
		$foo->image_x = 32;
		$foo->image_y = 32;
		$foo->allowed = ['image/*'];
		$foo->image_convert = 'png';
		$foo->file_auto_rename = false;
		$foo->file_overwrite = false;
		$foo->process(CORE_PATH . '/dati/bildes/awards/');
		if ($foo->processed) {
			if (!file_exists(CORE_PATH . '/dati/bildes/awards/'.$foo->file_dst_name_body.'.png')) {
                set_flash('Kļūda: ' . $foo->error, 'error');
                redirect('/'.$category->textid);                
			}
			$foo->clean();
		} else {
			set_flash('Kļūda: ' . $foo->error, 'error');
			redirect('/'.$category->textid);
		}
	}
    
    $award_title = '-';
    if (isset($_POST['aw_title'])) {
        $award_title = trim(strip_tags($_POST['aw_title'], '<a>'));
    }
    
    $db->insert('autoawards_custom', [
        'award_title' => sanitize($award_title),
        'img_title' => sanitize($foo->file_dst_name_body),
        'created_at' => 'NOW()',
        'created_by' => (int) $auth->id
    ]);

	set_flash('Apbalvojums pievienots!', 'success');
	redirect('/'.$category->textid);
}


/*
|--------------------------------------------------------------------------
|   Apbalvojuma piešķiršana lietotājam/-iem ($_POST).
|--------------------------------------------------------------------------
*/
if (isset($_GET['var1']) && $_GET['var1'] === 'award_user' && isset($_POST['submit'])) {
    
    if (!check_token('new_award', $_GET['token'])) {
        set_flash('no hacking, pls');
        redirect('/'.$category->textid);
    }
    
    $sel_award = (isset($_POST['sel_award'])) ? (int) $_POST['sel_award'] : 0;
    if ($sel_award < 1) {
        set_flash('Kļūda: izvēlētais apbalvojums neeksistē.', 'error');
        redirect('/'.$category->textid);
    }
    
    // atlasa info par izvēlēto apbalvojumu
    $aw = $db->get_row("
        SELECT * FROM `autoawards_custom` WHERE `id` = ".$sel_award
    );
    if (!$aw) {
        set_flash('Kļūda: izvēlētais apbalvojums neeksistē.', 'error');
        redirect('/'.$category->textid);
    }
    
    $user_ids = [];
    
    if (isset($_POST['user_ids'])) {
        $ids = explode(',', $_POST['user_ids']);
        foreach ($ids as $id) {
            $id = (int) trim($id);
            if ($id < 1) continue;
            if (!get_user($id)) continue;
            $user_ids[] = $id;
        }
    }
    
    // piešķir apbalvojumus
    if (!empty($user_ids)) {
        foreach ($user_ids as $usr) {
            
            $db->insert('autoawards', [
                'user_id' => $usr,
                'award' => sanitize($aw->img_title),
                'title' => sanitize($aw->award_title),
                'created' => 'NOW()'
            ]);
            
            $db->update('autoawards', $db->insert_id, ['importance' => $db->insert_id]);
            
            userlog($usr, 'Ieguva medaļu &quot;'.$aw->award_title.'&quot;', $img_server . '/dati/bildes/awards/'.$aw->img_title.'.png');
            notify($usr, 7);
            
            $m->delete('aw_' . $usr);
            $m->delete('android_awards_'.$usr.'-6');
        }
    }

	set_flash('Apbalvojumi piešķirti.', 'success');
	redirect('/'.$category->textid);
}


/*
|--------------------------------------------------------------------------
|   Formu un apbalvojumu saraksta izdrukāšana.
|--------------------------------------------------------------------------
*/

$tpl->assignAll([
    'new-award-type' => make_token('new_award_type'),
    'new-award' => make_token('new_award')
]);

$custom_awards = $db->get_results("
    SELECT * FROM `autoawards_custom` ORDER BY `id`
");

if ($custom_awards) {
    
    // apbalvojumu dropdown
    foreach ($custom_awards as $award) {    
        $tpl->newBlock('sel-custom-award');
        $tpl->assignAll($award);
    }
    
    // apbalvojumu saraksts
    $tpl->newBlock('award-list');    
    foreach ($custom_awards as $award) {    
        $tpl->newBlock('single-award');
        $tpl->assignAll($award);
    }
}

