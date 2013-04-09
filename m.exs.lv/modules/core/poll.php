<?php

if(isset($_POST['vote']) && isset($_POST['questions'])){
	$query = mysql_query("SELECT `questions`.`pid` FROM  `responses`, `questions` WHERE `responses`.`qid`=`questions`.`id` AND `responses`.`user_id`='".$auth->id."' AND pid=(SELECT pid FROM `questions` WHERE id='".$_POST['questions']."' LIMIT 1)");
	if(mysql_num_rows($query) == 0){
		$query = mysql_query("INSERT INTO `responses` (`qid`, `user_id`) VALUES ('".$_POST['questions']."', '".$auth->id."')");
	} else {
		$error = 'Tu jau nobalsoji!';
	}
} else if(!isset($_POST['questions']) && isset($_POST['vote'])){
	$error = 'Jāizvēlas atbilde!';
}
$query = mysql_query("SELECT * FROM `poll` ORDER BY `id` DESC LIMIT 1");
$rows = mysql_num_rows($query);
$title = 'Nav aptaujas!';
if($rows > 0){
	$poll = mysql_fetch_array($query);
	$title = $poll['name'];
}
$query = mysql_query("SELECT `questions`.`pid` FROM  `responses`, `questions` WHERE `responses`.`qid`=`questions`.`id` AND `responses`.`user_id`='".$auth->id."' AND pid='".$poll['id']."'");
if(mysql_num_rows($query) > 0 or !$auth->ok){
	$total = mysql_query("SELECT `questions`.`pid` FROM  `responses`, `questions` WHERE `responses`.`qid`=`questions`.`id` AND pid='".$poll['id']."'");
	$total = mysql_num_rows($total);
	$tpl->newBlock('poll-box');
	$tpl->assign('poll-title',$title);
	$query = mysql_query("SELECT * FROM `questions` WHERE `pid`='".$poll['id']."' ORDER BY `question`");
	$questions = mysql_num_rows($query);
	if($questions > 0){
		$tpl->newBlock('poll-answers');
		while($question = mysql_fetch_array($query)){
			$responses = $db->get_row("SELECT count(id) as total FROM `responses` WHERE qid='".$question['id']."'");
			$tpl->newBlock('poll-answers-node');
			$tpl->assign(array(
			  'poll-answer-question' => $question['question'],
			  'poll-answer-percentage' => round(($responses->total/$total)*100),
			));
		}
		$tpl->gotoBlock('poll-answers');
		$ppid = $poll['topic'];
		$pptitle = $db->get_var("SELECT title FROM pages WHERE id = '$ppid'");
		$tpl->assign(array(
			'poll-totalvotes' => $total,
			'ppage-id' => mkurl('page',$ppid,$pptitle)
		));
	}
} else {
	$tpl->newBlock('poll-box');
	$tpl->assign('poll-title',$title);
	$query = mysql_query("SELECT * FROM `questions` WHERE `pid`='".$poll['id']."' ORDER BY `question`");
	$questions = mysql_num_rows($query);
	if($questions > 0){
		$tpl->newBlock('poll-questions');
		if(isset($error)){
			$tpl->newBlock('poll-error');
			$tpl->assign('poll-error',$error);
		}
		$tpl->newBlock('poll-options');
		while($question = mysql_fetch_array($query)){
			$tpl->newBlock('poll-options-node');
			$tpl->assign(array(
			  'poll-options-question' => $question['question'],
			  'poll-options-id' => $question['id']
			));
		}
	}
}
?>