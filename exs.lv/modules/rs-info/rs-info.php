<?php

/**
 * 	Pamācībām pievienojamā papildinformācija.
 */
// ne-moderatorus sūtām prom
if (!im_mod()) {
	set_flash('Error 403: Permission denied!');
	redirect();
}
$sub_include = true;


$data = $db->get_results("SELECT `rs_pages_backup`.`img`,`rs_pages_backup`.`location`,`rs_pages_backup`.`members_only`,`rs_pages_backup`.`description`,`rs_pages_backup`.`page_id`,`rs_pages_backup`.`large_img` FROM `rs_pages_backup` LEFT JOIN `rs_classes` ON `rs_pages_backup`.`class_id` = `rs_classes`.`id` WHERE is_placeholder = 0 AND deleted_by = 0 AND `rs_pages_backup`.`category_id` = 792");
foreach ($data as $d) {
    $db->query("UPDATE `rs_pages` SET `members_only` = '".$d->members_only."', `location` = '".sanitize($d->location)."', `description` = '".sanitize($d->description)."', `img` = '".$d->img."', `large_img` = '".$d->large_img."' WHERE `page_id` = '".$d->page_id."' ");
}
exit;

// array_keys ir lapas textid
$submodules = array(
	'info-quests'    => 'quests.php',
	'info-distractions'    => 'distractions.php'
);


// iekļauj lapā pareizo apakšmoduli
if (isset($submodules[$category->textid])) {

	if (file_exists(CORE_PATH . '/modules/rs-info/submodules/' . $submodules[$category->textid])) {
    
        $tpl->assignInclude('sub-template', CORE_PATH . '/modules/rs-info/submodules/' . str_replace('php', 'tpl', $submodules[$category->textid]));
		$tpl->prepare();
        
		include(CORE_PATH . '/modules/rs-info/submodules/' . $submodules[$category->textid]);
        
	} else {
		set_flash('Kļūdaini norādīta adrese!');
		redirect();
	}
} else {
	set_flash('Kļūdaini norādīta adrese!');
	redirect();
}