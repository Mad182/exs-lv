<?php
/**
 *  iOS miniblogu apakšmodulis.
 *
 *  Apstrādā pieprasījumus saistībā ar darbībām miniblogos, tai skaitā
 *  jauna minibloga pievienošanu vai esoša komentēšanu, vērtēšanu u.c.
 *
 *  Adrese: ios.exs.lv/miniblogs/
 */

require(API_PATH.'/shared/ios.miniblogs.php');


/**
 *  Atgriezīs jaunāko miniblogu sarakstu.
 *  /miniblogs/getlatestlist
 */
if ($var1 === 'getlatest') {
	set_action('jaunākos miniblogus');
	api_fetch_miniblogs();

/**
 *  Jauna minibloga pievienošana vai esoša minibloga komentēšana.
 *  /miniblogs/{new|comment}
 */
} else if ($var1 === 'new' || $var1 === 'comment') {
	
	if (!isset($_POST['parent_id']) || !isset($_POST['mb_text']) ||
        ($var1 === 'new' && !isset($_POST['is_private']))) {
		api_error('Kļūdains pieprasījums.');
		if ($var1 === 'new') {
			api_log('Netika iesniegti minibloga ieraksta pievienošanas dati.');
		} else {
			api_log('Netika iesniegti minibloga komentēšanas dati.');
		}
	} else {
		api_add_miniblog(array(
			'group_id' => 0,
			'parent_id' => $_POST['parent_id'],
			'is_private' => ($var1 === 'new' ? $_POST['is_private'] : 0),
			'mb_text' => $_POST['mb_text']
		));
	}

/**
 *  Minibloga vērtēšana ar plusu vai mīnusu.
 *  /miniblogs/{plus|minus}/{miniblog_id}
 */
} else if (!empty($var1) && !empty($var2) &&
		   in_array($var1, array('plus', 'minus'))) {

	api_rate_comment($var2, ($var1 === 'plus'));

/**
 *  Minibloga satura atgriešana ar visiem komentāriem.
 *  /miniblogs/getminiblog/{miniblog_id}
 */
} else if ($var1 === 'getminiblog' && !empty($var2)) {
	api_fetch_miniblog($var2);

/**
 *  Citi gadījumi.
 */
} else {
    api_log('Sasniegts miniblogu moduļa "else" bloks.');
    api_error('hellou... are thou lost?');
}
