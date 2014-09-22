<?php
/**
 *  Profilu sasaiste
 *
 *  Moderatoriem paredzēta sadaļa lietotāju profilu sagrupēšanai.
 */
/**
    TODO:
    
        - stils uz dark skin
        - grupā pārmainot main profilu, būs jāpārbauda bloķētā grupa liegumu tabulā
        - apakšprojekti...
        - pie bloķēšanas - pābaudot profila eksistenci kādā grupā, jāpārbauda arī, vai main profils nav dzēsts
*/

if (!im_mod()) {
    set_flash('Pieeja liegta!');
    redirect();
    exit;
} 


/**
 *  Profila meklēšana pēc main profila
 *
 *  Pēc norādītā ID atrod šī lietotāja parent profila ID, tad
 *  ar javascriptu aizritina lapu līdz attiecīgajai tabulas rindai.
 */
if (isset($_GET['var1']) && $_GET['var1'] == 'search' && isset($_POST['user_id'])) {

    $user_id = (int)$_POST['user_id'];
    if ($user_id < 1) {
        set_flash('Norādīti kļūdaini dati!');
        redirect('/'.$_GET['viewcat']);
    }
    
    // nepieciešams norādītā lietotāja parent id
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
    

/**
 *  Main profila ieraksta pievienošana datubāzei
 */
} else if (isset($_GET['var1']) && $_GET['var1'] == 'add-main') {

    if (!isset($_POST['userid']) || (int)$_POST['userid'] < 1) {
        set_flash('Norādīti kļūdaini dati!');
        redirect('/'.$_GET['viewcat']);
    }
    
    $user_id = (int)$_POST['userid'];
    
    $if_exists = get_user($user_id);
    if (!$if_exists) {
        set_flash('Norādītais lietotājs neeksistē!');
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
    
    
/**
 *  Child profila piesaiste kādam main profilam
 *
 *  $_GET['var2'] - `users_groups`.`id` vērtība
 */
} else if (isset($_GET['var1']) && $_GET['var1'] == 'add-child' && isset($_GET['var2'])) {

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
    } else if (isset($_POST['child_id'])) {

        $child_id = (int)$_POST['child_id'];        
        $if_exists = get_user($child_id);        
        if (!$if_exists) {
            set_flash('Sasaistīt profilus neizdevās!');
            redirect('/'.$_GET['viewcat']);
        }
        
        // norādītais child lietotājs vēl nedrīkst būt datubāzē, citādi 
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
            set_flash('Norādītais lietotājs jau atrodas kādā no grupām!');
            redirect('/'.$_GET['viewcat']);
        }
        
        $data = array(
            'user_id' => $child_id,
            'parent_id' => $parent_id,
            'created_by' => $auth->id,
            'created_at' => time()
        );
        
        $insert = $db->insert('users_groups', $data);
        
        if ($insert !== false) {
            set_flash('Profils piesaistīts!', 'success');
            redirect('/'.$_GET['viewcat'].'?scroll='.$parent_data->id);
        } else {
            set_flash('Piesaistīt profilu neizdevās!');
            redirect('/'.$_GET['viewcat']);
        }

    } else {
        set_flash('Kļūdaini norādīta adrese!');
        redirect('/'.$_GET['viewcat']);
    }

    
/**
 *  Sasaistīto profilu grupas apraksta rediģēšana
 */
} else if (isset($_GET['var1']) && $_GET['var1'] == 'edit' && isset($_GET['var2'])) {

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


/**
 *  Profilu grupas dzēšana
 *
 *  $_GET['var2'] - `users_groups`.`id`
 */
} else if (isset($_GET['var1']) && $_GET['var1'] == 'delete-group' && isset($_GET['var2'])) {

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
        } else {
            set_flash('Profilu grupu dzēst neizdevās!');
        }
        redirect('/'.$category->textid);
    }
 

/**
 *  Kāda child profila atsaistīšana no main profila
 *
 *  $_GET['var2'] - `users_groups`.`id`
 */ 
} else if (isset($_GET['var1']) && $_GET['var1'] == 'delete-child' && isset($_GET['var2'])) {

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
        if (isset($_GET['var3'])) {
            $var3 = (int)$_GET['var3'];
            redirect('/'.$_GET['viewcat'].'?scroll='.$var3);
        }
    } else {
        set_flash('Profilu atsaistīt neizdevās!');
    }
    
    redirect('/'.$_GET['viewcat']);
 

/**
 *  Apmaina norādīto child vietām ar main
 *
 *  $_GET['var2'] - `users_groups`.`id`
 */ 
} else if (isset($_GET['var1']) && $_GET['var1'] == 'change-main' && isset($_GET['var2'])) {

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
 
    
/**
 *  Izdrukā sarakstu ar pievienotajiem profiliem
 */
} else {
    
    $tpl->newBlock('content-info');
    $tpl->newBlock('new-profile-form');
    
    $profiles = $db->get_results("
        SELECT
            `users_groups`.`id` AS `ug_id`,
            `users_groups`.`description`,
            `users`.`id`        AS `user_id`,
            `users`.`nick`      AS `user_nick`,
            `users`.`level`     AS `user_level`,
            `users`.`lastip`    AS `user_lastip`,
            `users`.`lastseen`  AS `user_seen`,
            IFNULL(`child`.`id`, 0) AS `child_id`,
            `children`.`id`     AS `child_parent`,
            `child`.`nick`      AS `child_nick`,
            `child`.`level`     AS `child_level`,
            `child`.`lastip`    AS `child_lastip`,
            `child`.`lastseen`  AS `child_seen`
        FROM `users_groups`
            JOIN `users` ON (
                `users_groups`.`user_id` = `users`.`id` AND
                `users`.`deleted` = 0
            )
            LEFT JOIN `users_groups` AS `children` ON (
                `users_groups`.`id` = `children`.`parent_id` AND 
                `children`.`deleted_by` = 0
            )
            LEFT JOIN `users` AS `child` ON (
                `children`.`user_id` = `child`.`id` AND
                `child`.`deleted` = 0
            )
        WHERE 
            `users_groups`.`parent_id` = 0 AND
            `users_groups`.`deleted_by` = 0
        ORDER BY 
            `users`.`nick` ASC,
            `child`.`nick` ASC
    ");

    if ($profiles === false) {
        $tpl->newBlock('no-profiles');
    } else {
    
        $tmp_main_id = 0;
        $main_cnt = 0;          // main profilu skaits
        $children_cnt = 0;      // child profilu skaits ciklā ejošajam main
    
        $tpl->newBlock('profile-list');
        
        foreach ($profiles as $profile) {
    
            // mainās main profils
            if ($profile->user_id != $tmp_main_id) {

                $tmp_main_id = $profile->user_id;
                
                // pirmajā cikla reizē children skaits vēl nav zināms, jo
                // tas tiek skaitīts, ejot pa ciklu, un ir jāpieraksta beigās
                if ($main_cnt > 0) {                    
                    if ($children_cnt == 0) {
                        $tpl->newBlock('no-children');
                    } else {
                        $tpl->newBlock('child-table-header');
                    }
                    $tpl->gotoBlock('a-profile');
                    $tpl->assign('profile_count', $children_cnt);
                }                
                $children_cnt = 0;
                
                $main_cnt++;
                
                // izveido jaunu rindu galvenajam profilam
                $profile->user_nick = usercolor($profile->user_nick, $profile->user_level, false, $profile->user_id);
                $profile->user_seen = date('d.m.y, H:i', strtotime($profile->user_seen));

                $tpl->newBlock('a-profile');
                $tpl->assignAll($profile);
                $tpl->assign('counter', $main_cnt);
                
                // tabulas rinda visiem turpmākajiem child profiliem
                $tpl->newBlock('all-children');
                $tpl->assign(array(
                    'user_seen' => $profile->user_seen,
                    'user_lastip' => $profile->user_lastip
                ));
                if (!empty($profile->description)) {
                    $tpl->assign('description', '<p><strong>Komentārs:</strong></p><p>'.$profile->description.'</p>');
                }
            }
            
            // main profilam var nebūt neviena child profila
            if ($profile->child_id != '0') {

                $profile->child_nick = usercolor($profile->child_nick, $profile->child_level, false, $profile->child_id);
                $profile->child_seen = date('d.m.y, H:i', strtotime($profile->child_seen));
            
                $tpl->newBlock('a-child');
                $tpl->assignAll($profile);
                $tpl->assign('main-profile-id', $profile->user_id);
                
                $children_cnt++;
            }
        }
        
        // ciklā pēdējam ierakstam child skaits vēl netika norādīts
        if ($children_cnt == 0) {
            $tpl->newBlock('no-children');
        } else {
            $tpl->newBlock('child-table-header');
        }
        $tpl->gotoBlock('a-profile');
        $tpl->assign('profile_count', $children_cnt);
        
        // javascripts meklēs main profilu ar šādu id un 
        // aizritinās lapu līdz attiecīgajai rindai
        if (isset($_GET['scroll'])) {
            $scroll_to = (int)$_GET['scroll'];
            if ($scroll_to > 0) {
                $tpl->newBlock('scroll-to');
                $tpl->assign('main-id', '\'#profile-'.$scroll_to.'\'');
            }
        }
    }
}
