<?php

$polls = $db->get_results("SELECT * FROM poll WHERE `group` = '0' AND `lang` = '$lang' ORDER BY id DESC LIMIT 45");

foreach ($polls as $poll) {

	$title = $poll->name;
	$total = $db->get_var("SELECT count(*) FROM  `responses`, `questions` WHERE `responses`.`qid`=`questions`.`id` AND pid='" . $poll->id . "'");
    
	$tpl->newBlock('polls-box');
	$tpl->assign('polls-title', $title);
    
	$questions = $db->get_results("SELECT * FROM `questions` WHERE `pid`='" . $poll->id . "' ORDER BY `question`");
    
	if (!empty($questions)) {
    
		$tpl->newBlock('polls-answers');
        
		foreach ($questions as $question) {
			$responses = $db->get_row("SELECT count(*) as `total` FROM `responses` WHERE `qid` = '" . $question->id . "'");
            $calc = ($total == 0) ? round($responses->total * 100) : round(($responses->total / $total) * 100);
			$tpl->newBlock('polls-answers-node');
			$tpl->assign(array(
				'polls-answer-question' => $question->question,
				'polls-answer-percentage' => $calc
			));
		}
        
        $tpl->gotoBlock('polls-answers');
        
        // runescape apakšprojektā aptaujas ir miniblogos
        if ($lang == 9) {
            $mb_text = $db->get_row("SELECT `id`, `text` FROM `miniblog` WHERE `id` = '".(int)$poll->topic."' LIMIT 1");
            if ($mb_text) {
                $tpl->assign(array(
                    'polls-totalvotes' => $total,
                    'url' => '/say/' . $rsbot_id . '/' . $poll->topic . '-' . mb_get_strid($mb_text->text, $mb_text->id)
                ));
            }
        // citur - rakstos
        } else {
            $tpl->assign(array(
                'polls-totalvotes' => $total,
                'url' => '/read/' . get_page_strid($poll->topic)
            ));
        }
        
	}
}
