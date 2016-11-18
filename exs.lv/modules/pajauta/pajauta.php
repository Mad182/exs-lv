<?php

require_once('includes/ajax_comments.php');

if ($auth->ok) {

	$tpl->assignInclude('module-head', 'modules/' . $category->module . '/head.tpl');
	$tpl->prepare();


	if (isset($_GET['var1'])) {

		/* if($_GET['var1'] == 'pievienot') {

		  if(isset($_POST['question'])
		  && !empty($_POST['question'])
		  && !empty($_POST['answ0'])
		  && !empty($_POST['answ1'])) {

		  if(in_array($auth->id,array(16431,7979,16724,5820,12621,19740,8070,18817,8427,5593,19225,2178,881,12024,5608,10595,2490,10646,1099,5343,16151,18571,6919,17531,15047,18482,4532,13247,9989,15422))) {
		  die('Tavām stulbībām te nav vietas :(');
		  }

		  $question = sanitize(h(substr(strip_tags(trim($_POST['question'])),0,240)));
		  $answ0 = 		sanitize(h(substr(strip_tags(trim($_POST['answ0'])),0,200)));
		  $answ1 = 		sanitize(h(substr(strip_tags(trim($_POST['answ1'])),0,200)));
		  $slug = mkslug($_POST['question']);

		  if(empty($slug)) {
		  die('Jautājumam jāsatur tekstu!');
		  }

		  $db->query("INSERT INTO qgame_questions (slug,user_id,question,answ0,answ1,time)
		  VALUES ('$slug',$auth->id,'$question','$answ0','$answ1',NOW())");
		  userlog($auth->id,'Uzdeva jautājumu exs lietotājiem: &quot;<a href="/Pajauta/'.$slug.'">'.$question.'</a>&quot;');
		  redirect('https://exs.lv/Pajauta/'.$slug);
		  } else {
		  $tpl->newBlock('pajauta-add');
		  }

		  } else */

		if ($_GET['var1'] == 'arhivs') {

			$questions = $db->get_results("SELECT * FROM qgame_questions");
			$tpl->newBlock('pajauta-list');
			foreach ($questions as $question) {
				$tpl->newBlock('pajauta-list-node');
				$tpl->assign([
					'slug' => $question->slug,
					'question' => $question->question
				]);
			}
		} else {

			$question = $db->get_row("SELECT * FROM qgame_questions WHERE slug = '" . sanitize($_GET['var1']) . "' LIMIT 1");

			if ($question) {

				$author = get_user($question->user_id);
				$answered = $db->get_var("SELECT count(*) FROM qgame_answers WHERE user_id = '$auth->id' AND question_id = '$question->id'");
				if (isset($_POST['answer'])) {
					if ($answered) {
						echo 'Tu jau nobalsoji!';
					} else {
						$answ = (bool) $_POST['answer'];
						$db->query("INSERT INTO qgame_answers (question_id,user_id,answer,time)
															VALUES ('$question->id','$auth->id','$answ',NOW())");
						$answ0 = (int) $db->get_var("SELECT count(*) FROM qgame_answers WHERE question_id = '$question->id' AND answer = '0'");
						$answ1 = (int) $db->get_var("SELECT count(*) FROM qgame_answers WHERE question_id = '$question->id' AND answer = '1'");

						$total = $answ0 + $answ1;

						echo '<p>' . $question->answ0 . ': <strong>' . $answ0 . 'x</strong><br />
							<span style="display:block;width:100%;height:10px;font-size:1px;line-height:0;">
							  <span style="-webkit-border-radius:3px;-moz-border-radius:3px;border-radius:3px;display:block;width:' . ceil(100 / $total * $answ0) . '%;background:#a85;height:10px;font-size:1px;line-height:0;">
							</span></p>';
						echo '<p>' . $question->answ1 . ': <strong>' . $answ1 . 'x</strong><br />
							<span style="display:block;width:100%;height:10px;font-size:1px;line-height:0;">
							  <span style="-webkit-border-radius:3px;-moz-border-radius:3px;border-radius:3px;display:block;width:' . ceil(100 / $total * $answ1) . '%;background:#58a;height:10px;font-size:1px;line-height:0;">
							</span></p>';
						echo '<p><a href="/pajauta/' . $question->slug . '">Komentāri &raquo;</a><br /><strong><a href="/pajauta">Citu jautājumu &raquo;</a></strong></p>';
					}
					exit;
				}

				if (!$answered) {
					$tpl->newBlock('pajauta-q');

					$tpl->assign([
						'question' => $question->question,
						'answ0' => $question->answ0,
						'answ1' => $question->answ1,
						'answ1' => $question->answ1,
						'nick' => h($author->nick),
						'comments' => comments_block('qg-' . $question->id)
					]);
				} else {
					$answ0 = (int) $db->get_var("SELECT count(*) FROM qgame_answers WHERE question_id = '$question->id' AND answer = '0'");
					$answ1 = (int) $db->get_var("SELECT count(*) FROM qgame_answers WHERE question_id = '$question->id' AND answer = '1'");

					$tpl->newBlock('pajauta-a');

					$total = $answ0 + $answ1;

					$tpl->assign([
						'question' => $question->question,
						'qurl' => $question->slug,
						'answ0' => $question->answ0,
						'answ1' => $question->answ1,
						'count0' => $answ0,
						'count1' => $answ1,
						'percent0' => ceil(100 / $total * $answ0),
						'percent1' => ceil(100 / $total * $answ1),
						'nick' => h($author->nick),
						'comments' => comments_block('qg-' . $question->id)
					]);
				}

				if (!$lastid = (int) $db->get_var("SELECT id FROM ajax_comments WHERE parent = 'qg-" . $question->id . "' ORDER BY id DESC LIMIT 1")) {
					$lastid = 1;
				}

				$tpl->assignGlobal([
					'qslug' => $question->slug,
					'lastid' => $lastid
				]);

				$page_title = ucfirst($question->question) . ' - ' . $question->answ0 . ' vai ' . $question->answ1;

				if (isset($_GET['ajax']) || isset($_POST['ajax'])) {
					echo comments_block('qg-' . $question->id, $_GET['ajax']);
					exit;
				}
			}
		}
	} else {

		$answered = $db->get_col("SELECT question_id FROM qgame_answers WHERE user_id = '$auth->id'");

		if (!empty($answered)) {
			$add = 'WHERE id NOT IN (' . implode(',', $answered) . ') ';
		} else {
			$add = '';
		}

		$slug = $db->get_var("SELECT slug FROM qgame_questions $add ORDER BY RAND() LIMIT 1");

		if ($slug) {
			redirect('https://exs.lv/pajauta/' . $slug);
		} else {
			$tpl->newBlock('pajauta-noq');
		}
	}
} else {

	$tpl->assignInclude('module-head', 'modules/' . $category->module . '/head-public.tpl');
	$tpl->prepare();

	if (isset($_GET['var1'])) {

		if ($_GET['var1'] == 'arhivs') {

			$questions = $db->get_results("SELECT * FROM qgame_questions");
			$tpl->newBlock('pajauta-list');
			foreach ($questions as $question) {
				$tpl->newBlock('pajauta-list-node');

				$tpl->assign([
					'slug' => $question->slug,
					'question' => $question->question
				]);
			}
		} else {

			$question = $db->get_row("SELECT * FROM qgame_questions WHERE slug = '" . sanitize($_GET['var1']) . "' LIMIT 1");

			if ($question) {

				$author = get_user($question->user_id);

				$answ0 = (int) $db->get_var("SELECT count(*) FROM qgame_answers WHERE question_id = '$question->id' AND answer = '0'");
				$answ1 = (int) $db->get_var("SELECT count(*) FROM qgame_answers WHERE question_id = '$question->id' AND answer = '1'");

				$total = $answ0 + $answ1;

				$tpl->newBlock('pajauta-a');

				$tpl->assign([
					'question' => $question->question,
					'answ0' => $question->answ0,
					'answ1' => $question->answ1,
					'count0' => $answ0,
					'count1' => $answ1,
					'percent0' => ceil(100 / $total * $answ0),
					'percent1' => ceil(100 / $total * $answ1),
					'nick' => h($author->nick),
					'comments' => comments_block('qg-' . $question->id)
				]);

				$page_title = ucfirst($question->question) . ' - ' . $question->answ0 . ' vai ' . $question->answ1;
			}
		}
	} else {

		$questions = $db->get_results("SELECT * FROM qgame_questions");
		$tpl->newBlock('pajauta-list');
		foreach ($questions as $question) {
			$tpl->newBlock('pajauta-list-node');

			$tpl->assign([
				'slug' => $question->slug,
				'question' => $question->question
			]);
		}
	}
}
