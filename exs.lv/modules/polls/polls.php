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
			$tpl->newBlock('polls-answers-node');
			$tpl->assign(array(
				'polls-answer-question' => $question->question,
				'polls-answer-percentage' => round(($responses->total / $total) * 100)
			));
		}
		$tpl->gotoBlock('polls-answers');
		$tpl->assign(array(
			'polls-totalvotes' => $total,
			'url' => '/read/' . get_page_strid($poll->topic)
		));
	}
}
