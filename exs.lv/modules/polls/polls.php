<?php

if ($auth->ok) {
	$tpl->newBlock('polls-create');
}

$polls = $db->get_results("SELECT * FROM poll WHERE `group` = '0' AND `lang` = '$lang' AND `approved` = '1' ORDER BY id DESC LIMIT 45");

if (!empty($polls)) {
	$poll_ids = [];
	$topic_ids = [];
	foreach ($polls as $p) {
		$poll_ids[] = (int) $p->id;
		if (!empty($p->topic)) {
			$topic_ids[] = (int) $p->topic;
		}
	}

	// Batch-fetch all questions with their response counts in 1 query
	$questions_by_poll = [];
	$totals_by_poll = [];
	if (!empty($poll_ids)) {
		$pid_in = implode(',', array_unique($poll_ids));
		$all_questions = $db->get_results("
			SELECT q.*, COUNT(r.qid) AS `total_responses`
			FROM `questions` q
			LEFT JOIN `responses` r ON r.qid = q.id
			WHERE q.pid IN ($pid_in)
			GROUP BY q.id
			ORDER BY q.question
		");
		if (!empty($all_questions)) {
			foreach ($all_questions as $q) {
				$pid = (int) $q->pid;
				$questions_by_poll[$pid][] = $q;
				$totals_by_poll[$pid] = ($totals_by_poll[$pid] ?? 0) + (int) $q->total_responses;
			}
		}
	}

	// Batch-fetch discussion topics (miniblog for lang=9, pages otherwise)
	$mb_by_id = [];
	$strid_by_id = [];
	if (!empty($topic_ids)) {
		$tid_in = implode(',', array_unique($topic_ids));
		if ($lang == 9) {
			$mb_rows = $db->get_results("SELECT `id`, `text` FROM `miniblog` WHERE `id` IN ($tid_in)");
			if (!empty($mb_rows)) {
				foreach ($mb_rows as $mr) {
					$mb_by_id[(int) $mr->id] = $mr;
				}
			}
		} else {
			$pg_rows = $db->get_results("SELECT `id`, `strid` FROM `pages` WHERE `id` IN ($tid_in)");
			if (!empty($pg_rows)) {
				foreach ($pg_rows as $pr) {
					$strid_by_id[(int) $pr->id] = $pr->strid;
				}
			}
		}
	}

	foreach ($polls as $poll) {

		$title = $poll->name;
		$total = $totals_by_poll[(int) $poll->id] ?? 0;

		$tpl->newBlock('polls-box');
		$tpl->assign('polls-title', $title);

		$questions = $questions_by_poll[(int) $poll->id] ?? [];

		if (!empty($questions)) {

			$tpl->newBlock('polls-answers');

			foreach ($questions as $question) {
				$resp_total = (int) $question->total_responses;
				$calc = ($total == 0) ? round($resp_total * 100) : round(($resp_total / $total) * 100);
				$tpl->newBlock('polls-answers-node');
				$tpl->assign([
					'polls-answer-question' => $question->question,
					'polls-answer-percentage' => $calc
				]);
			}

			$tpl->gotoBlock('polls-answers');

			// runescape apakšprojektā aptaujas ir miniblogos
			if ($lang == 9) {
				$mb_text = $mb_by_id[(int) $poll->topic] ?? null;
				if ($mb_text) {
					$tpl->assign([
						'polls-totalvotes' => $total,
						'url' => '/say/' . $rsbot_id . '/' . $poll->topic . '-' . mb_get_strid($mb_text->text, $mb_text->id)
					]);
				}
			// citur - rakstos
			} else {
				$tpl->assign([
					'polls-totalvotes' => $total,
					'url' => '/read/' . ($strid_by_id[(int) $poll->topic] ?? '')
				]);
			}
		}
	}
}
