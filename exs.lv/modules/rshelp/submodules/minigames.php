<?php

/**
 * 	Publiskā RuneScape minispēļu un D&D sadaļa
 */
!isset($sub_include) and die('No hacking, pls.');

$cat_id = ($category->textid == 'minispeles') ? 160 : 792;
$title_1 = ($cat_id == 160) ? 'minispēles' : 'Distractions & Diversions';
$title_2 = ($cat_id == 160) ? 'minispēļu' : 'Distractions & Diversions';

$tpl->newBlock('minigames');
$tpl->assign('top-content-title', 'RuneScape ' . $title_1);

// augšējais sadaļas intro teksts
if ($cat_id == 160) {
	$tpl->newBlock('minigames-intro');
} else {
	$tpl->newBlock('diversions-intro');
}

// no datubāzes atlasa sadaļas saistītos rakstus un izvada tabulas veidā
$minigames = $db->get_results("
    SELECT 
        `pages`.`id`,
        `pages`.`strid`     AS `page_strid`, 
        `pages`.`title`     AS `page_title`,
        `pages`.`author`    AS `page_author`, 
        `pages`.`date`      AS `page_date`,
        `pages`.`avatar`,
        IFNULL(`rs_pages`.`is_old`, 0)  AS `rspage_old`,
        `rs_pages`.`description`        AS `rspage_description`,
        `rs_pages`.`location`           AS `rspage_location`,
        `rs_pages`.`members_only`       AS `rspage_p2p_only`
    FROM `pages`
        LEFT JOIN `rs_pages` ON (
            `pages`.`id`                = `rs_pages`.`page_id` AND
            `rs_pages`.`deleted_by`     = 0 AND
            `rs_pages`.`is_placeholder` = 0
        )
    WHERE 
        `pages`.`category` = $cat_id
    ORDER BY 
        `pages`.`title` ASC
");

if ($minigames) {

	foreach ($minigames as $game) {

		// mainīgo raksturiezīmju pārveidošana
		if ($user = get_user($game->page_author)) {
			$game->page_author = '<a href="' . mkurl('user', $user->id, $user->nick) . '">' . usercolor($user->nick, $user->level) . '</a>';
		}

		$game->avatar = ($game->avatar != '') ?
				'<a href="/read/' . $game->page_strid . '">
                <img class="mg-av" src="' . $img_server . '/' . $game->avatar . '" title="' . $game->page_title . '" alt="">
            </a>' : '';

		$game->page_date = date('d.m.Y', strtotime($game->page_date));
		$game->page_title = str_replace('[D&amp;D] ', '', $game->page_title);

		// ja izdevies atlasīt papildinfo par rakstu no `rs_pages` tabulas...
		if ($game->rspage_old != '0') {

			$game->rspage_p2p_only = ($game->rspage_p2p_only == 1) ? 'Jā' : 'Nē';

			$title = ($game->rspage_old == 1) ?
					'Pamācībai nepieciešamas jaunākas, labākas kvalitātes bildes!' :
					'Pamācību nepieciešams atjaunināt!';

			$picture = ($game->rspage_old == 1) ? 'info_yellow_sm.png' : 'info_red_sm.png';
			$picture = '<img class="mg-old" src="/bildes/runescape/' . $picture . '" title="' . $title . '" alt="">';

			$tpl->assign('warning', $picture);
		} else {
			//$game->rspage_p2p_only = 'Nē';
		}

		$tpl->newBlock('minigame');
		$tpl->assignAll($game);
	}
}

// placeholderi neuzrakstītajām pamācībām...
/*$get_ph = $db->get_results("SELECT * FROM `rs_placeholders` WHERE `cat` = '$catid' ORDER BY `title` ASC");
if ($get_ph) {
    $tpl->newBlock('minigames-placeholders');
    $tpl->assign('type', $title_2);
    foreach ($get_ph as $ph) {
        $tpl->newBlock('minigame-ph');
        //$ph->img = ($ph->img != '') ? '<a href="/write"><img class="mg-av" src="/bildes/rs/temp/'.$ph->img.'" title="Pamācība vēl nav uzrakstīta!" alt="" /></a>' : '';
        $tpl->assignAll($ph);
        $link2 = ($ph->url2 == '') ? '' : ' un <a href="' . $ph->url2 . '">šis raksts</a>';
        $link1 = ($link2 == '') ? '<a href="' . $ph->url . '">šis raksts</a>' : '<a href="' . $ph->url . '">šis</a>';
        if ($ph->url != '' || $ph->url2 != '') {
            $tpl->assign('link', '<p>Pamācības veidošanas procesā Tev var noderēt ' . $link1 . $link2 . '.</p>');
        }
    }
}*/