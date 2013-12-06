<?php
/**	
 *	Atver fancybox ar saturu, kāds bija redzams pirms minibloga dzēšanas.
 *	Pieejams tikai lapas moderatoriem.
 *
 *	Moduļa adrese: 		exs.lv/mbview
 *	Pēdējās izmaiņas: 	06.12.2013 ( Edgars )
 */
if ( !$auth->ok || !im_mod() || $auth->mobile || !in_array($lang, array(1)) ) {
	die('<div class="deleted-mb-content"><strong>Error 403: Permission denied!</strong></div>');
	exit;
}

$content = 'Error!';

if ( !isset($_GET['var1']) || !is_numeric($_GET['var1']) ) {
	$content = '<div class="deleted-mb-content"><strong>Kļūdaini norādīta adrese!</strong></div>';
}
else {
	$entry_id = (int)$_GET['var1'];
	
	$data = $db->get_row("
		SELECT `text`, `groupid` FROM `miniblog`
		WHERE 
			`miniblog`.`id` 		= '$entry_id' 	AND 
			`miniblog`.`removed` 	= 1 			AND
			`miniblog`.`lang` 		= $lang
	");	
	if ( !$data ) {
		$content = '<div class="deleted-mb-content"><strong>Nepareizi norādīts ieraksta ID!</strong></div>';
	}
	else {
	
		// liedz skatīt komentāru, kurš atrodas grupā, kurai lietotājam nav piekļuves
		if( !empty($data->groupid) ) {
		
			$group = $db->get_row("SELECT `public`, `owner` FROM `clans` WHERE `id` = '$data->groupid' ");

			if( !$group->public && $group->owner !== $auth->id ) {
			
				$is_member = $db->get_var("SELECT count(*) FROM `clans_members` WHERE `clan` = '$data->groupid' AND `user` = '$auth->id' AND `approve` = 1");

				if( !$is_member ) {
					die('<div class="deleted-mb-content"><strong>Pieeja liegta!</strong></div>');
				}
			}
		}	
	
		$content = '<div class="deleted-mb-content"><strong>Dzēstais ieraksta saturs:</strong> '.add_smile($data->text).'</div>';
	}
}

echo $content;
exit;
