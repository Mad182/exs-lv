<?php

/**
 * 	RuneScape pamācību sadaļas
 */

// dev tools
if ($auth->id == 115 && isset($_GET['force'])) {
	if ($_GET['force'] == 'true') {
		update_rspages(true, true); // updeito, drukā
	} else {
		update_rspages(false, true);  // neupdeito, drukā
	}
	exit;
} elseif ($auth->id == 115 && isset($_GET['refresh'])) {
	// atjauno iekešotās daļas
	$stats = get_quests_stats(true);
	echo '<pre>' . var_dump($stats, false) . '</pre>';
	exit;
}



$tpl_options = 'no-right';  // attieksies uzreiz uz visiem apakšmoduļiem
$sub_include = true;        // submoduļos ir pārbaude, vai šāds mainīgais definēts


// submoduļu indeksi ir kategoriju strid no datubāzes
$submodules = array(
	'kvestu-pamacibas'          => array('quests.php', 'quests.tpl'),
	'f2p-kvesti'                => array('quests.php', 'quests.tpl'),
	'p2p-kvesti'                => array('quests.php', 'quests.tpl'),
	'mini-kvesti'               => array('quests.php', 'quests.tpl'),
	'minispeles'                => array('minigames.php', 'minigames.tpl'),
	'distractions-diversions'   => array('minigames.php', 'minigames.tpl'),
	'prasmes'                   => array('skills.php', 'skills.tpl'),
	//'tasks'                     => 'tasks.php',
	'gildes'                    => 'guilds.php'
);



// iekļauj lapā pareizo apakšmoduli
if (isset($submodules[$category->textid])) {

	if (is_array($submodules[$category->textid])) {
		$cat = $submodules[$category->textid][0];
		$sub_tpl = $submodules[$category->textid][1];
	} else {
		$cat = $submodules[$category->textid];
		$sub_tpl = false;
	}

	// sub-template
	if ($sub_tpl !== false) {
		$tpl->assignInclude('sub-template', CORE_PATH . '/modules/rshelp/submodules/' . $sub_tpl);
		$tpl->prepare();
	}

	// sub-file
	if (file_exists(CORE_PATH . '/modules/rshelp/submodules/' . $cat)) {
		include(CORE_PATH . '/modules/rshelp/submodules/' . $cat);
	} else {
		set_flash('Kļūdaini norādīta adrese!');
		redirect();
	}
}



// pārējās RuneScape pamācību sadaļās raksti tiks izdrukāti parastā tabulas formā
else {

	// redzamas būs visas trīs lapas kolonnas
	$tpl_options = '';
    
    $order_by = 'ORDER BY `title` ASC ';
    if ($category->id == 599) { // rs jaunumu raksti
        $order_by = 'ORDER BY `date` DESC ';
    }

	$all_items = $db->get_results("
        SELECT `strid`,`title`,`author` 
        FROM `pages` 
        WHERE `category` = '" . $category->id . "' 
        ORDER BY `title` ASC 
        LIMIT 0, 150
    ");
    
	if ($all_items) {
    
		$tpl->newBlock('rshelp-list');
		$tpl->assign('category-title', $category->title);
        
		foreach ($all_items as $item => $data) {
        
			if ($user = get_user($data->author)) {
				$data->author  = '<a style="font-size:11px;"';
                $data->author .= ' href="'.mkurl('user', $user->id, $user->nick).'">';
                $data->author .= usercolor($user->nick, $user->level) . '</a>';
			}
            
			$tpl->newBlock('rshelp-listitem');
			$tpl->assignAll($data);
		}
	} else {
		set_flash('Kļūdaini norādīta adrese!');
		redirect();
	}
}