<?php
/**
 *  Android miniblogu apakšmodulis.
 *
 *  Apstrādā pieprasījumus saistībā ar darbībām miniblogos, tai skaitā
 *  jauna minibloga pievienošanu vai esoša komentēšanu, vērtēšanu u.c.
 */

require(API_PATH.'/shared/android.miniblogs.php');

/**
 *  Atgriezīs jaunāko miniblogu sarakstu.
 *  /miniblogs/getlist
 */
if ($var1 === 'getlist') {
	set_action('jaunākos miniblogus');
	api_fetch_miniblogs();

/**
 *  Jauna minibloga pievienošana vai esoša minibloga komentēšana.
 *  /miniblogs/{new|comment}
 */
} else if ($var1 === 'new' || $var1 === 'comment') {
	
	if (!isset($_POST['group_id']) || !isset($_POST['parent_id']) ||
		!isset($_POST['content']) || !isset($_POST['is_private'])) {
		api_error('Kļūdains pieprasījums.');
		if ($var1 === 'new') {
			api_log('Netika iesniegti minibloga ieraksta pievienošanas dati.');
		} else {
			api_log('Netika iesniegti minibloga komentēšanas dati.');
		}
	} else {
		api_add_miniblog(array(
			'group_id' => $_POST['group_id'],
			'parent_id' => $_POST['parent_id'],
			'is_private' => $_POST['is_private'],
			'content' => $_POST['content']
		));
	}

/**
 *  Minibloga vērtēšana ar plusu vai mīnusu.
 *  /miniblogs/{plus|minus}/{entry_id}
 */
} else if (!empty($var1) && !empty($var2) &&
		   in_array($var1, array('plus', 'minus'))) {
	api_rate_comment($var2, ($var1 === 'plus'));

/**
 *  Minibloga satura atgriešana ar visiem komentāriem.
 *  /miniblogs/getcontent/{miniblog_id}
 */
} else if ($var1 === 'getcontent' && !empty($var2)) {
	api_fetch_miniblog($var2);
    
/**
 *  Minibloga rediģējamā satura atgriešana.
 *  /miniblogs/geteditable/{miniblog_id}
 */
} else if ($var1 === 'geteditable' && !empty($var2)) {
	api_get_editable_mb($var2);
    
/**
 *  Minibloga rediģēšana.
 *  /miniblogs/edit/{miniblog_id}
 */
} else if ($var1 === 'edit' && !empty($var2)) {
	
	if (!isset($_POST['text'])) {
		api_error('Nav saņemts ieraksta jaunais teksts.');
        api_log('Nav saņemts minibloga ieraksta jaunais saturs.');
	} else {
		api_edit_miniblog((int)$var2, $_POST['text']);
	}

} else {
    api_log('Sasniegts miniblogu moduļa "else" bloks.');
    api_error('hellou... are thou lost?');
}
