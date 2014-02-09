<?php

/**
 * Aptaujas lapas malā
 */
 
// $_POST balsošana
if (isset($_POST['vote']) && isset($_POST['questions'])) {
	$voted = $db->get_var("SELECT
			count(*)
		FROM
			`responses`,
			`questions`
		WHERE
			`responses`.`qid` = `questions`.`id` AND
			`responses`.`user_id` = '" . $auth->id . "' AND
			`pid` = (SELECT `pid` FROM `questions` WHERE `id` = '" . intval($_POST['questions']) . "' LIMIT 1)
		");
	if (!$voted) {
		$db->query("INSERT INTO `responses` (`qid`, `user_id`) VALUES ('" . intval($_POST['questions']) . "', '" . $auth->id . "')");
		userlog($auth->id, 'Nobalsoja aptaujā', '/bildes/poll-icon.png');
		update_karma($auth->id, 1);
	} else {
		$error = 'Tu jau nobalsoji!';
	}
} else if (!isset($_POST['questions']) && isset($_POST['vote'])) {
	$error = 'Jāizvēlas atbilde!';
}


// atlasa informāciju par aktīvo aptauju
$poll = $db->get_row("SELECT * FROM `poll` WHERE `group` = '0' AND `lang` = '$lang' ORDER BY `id` DESC LIMIT 1");
$title = 'Nav aptaujas!';

if ($poll) {

	$title = $poll->name;

    // ja lietotājs ir neautorizējies vai jau ir nobalsojis...
	if (!$auth->ok || 
        $db->get_var("
            SELECT count(*) FROM  `responses`, `questions` 
            WHERE 
                `responses`.`qid` = `questions`.`id` AND
                `responses`.`user_id` = '" . $auth->id . "' AND
                pid = '" . $poll->id . "'
        ")
    ) {
    
		$total = $db->get_var("
            SELECT count(*) FROM `responses`, `questions` 
            WHERE 
                `responses`.`qid` = `questions`.`id` AND
                `pid` = '" . $poll->id . "'
        ");
        
		$tpl->newBlock('poll-box');
		$tpl->assign('poll-title', $title);
        
		$questions = $db->get_results("SELECT * FROM `questions` WHERE `pid` = '" . $poll->id . "' ORDER BY `id`");
        
		if (!empty($questions)) {
        
			$tpl->newBlock('poll-answers');

			foreach ($questions as $question) {
				$responses = $db->get_var("SELECT count(*) FROM `responses` WHERE `qid` = '" . $question->id . "'");
				$tpl->newBlock('poll-answers-node');
				$tpl->assign(array(
					'poll-answer-question' => $question->question,
					'poll-answer-percentage' => round(($responses / $total) * 100)
				));
			}

			$tpl->gotoBlock('poll-answers');
            // runescape apakšprojektā aptaujas ir miniblogos
            if ($lang == 9) {
                $poll_mb_text = $db->get_row("SELECT `id`, `text` FROM `miniblog` WHERE `id` = '".(int)$poll->topic."' LIMIT 1");
                if ($poll_mb_text) {
                    $tpl->assign(array(
                        'poll-totalvotes' => $total,
                        'ppage-id' => '/say/' . $rsbot_id . '/' . $poll->topic . '-' . mb_get_strid($poll_mb_text->text, $poll_mb_text->id)
                    ));
                }
            } else {
                $tpl->assign(array(
                    'poll-totalvotes' => $total,
                    'ppage-id' => '/read/' . get_page_strid($poll->topic)
                ));
            }
		}
        
    // lietotājs aptaujā vēl nav balsojis
	} else {
    
		$tpl->newBlock('poll-box');
		$tpl->assign('poll-title', $title);
        
		$questions = $db->get_results("SELECT * FROM `questions` WHERE `pid` = '" . $poll->id . "' ORDER BY `id`");
        
		if (!empty($questions)) {
        
			$tpl->newBlock('poll-questions');
			if (isset($error)) {
				$tpl->newBlock('poll-error');
				$tpl->assign('poll-error', $error);
			}
            
			$tpl->newBlock('poll-options');
            
			foreach ($questions as $question) {
				$tpl->newBlock('poll-options-node');
				$tpl->assign(array(
					'poll-options-question' => $question->question,
					'poll-options-id' => $question->id
				));
			}
		}
	}
}